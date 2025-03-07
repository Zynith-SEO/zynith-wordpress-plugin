// Core Modules
const fs = require('fs');
const https = require('https');
const path = require('path');

// NPM Modules
const axios = require('axios');
const gulp = require('gulp');
const iconv = require('gulp-iconv-lite');
const ADMZip = require('adm-zip');

// Variables
const pathPackageFile = path.resolve(__dirname, 'package.json');
const pathPluginFile = path.resolve(__dirname, 'src/php/wp-fedora-core.php');

// GitHub repo details
const wordpressRepoAPIURL = `https://api.github.com/repos/WordPress/WordPress/tags`;
const wordpressRepoDownloadURL = `https://github.com/WordPress/WordPress/archive/refs/tags`;

// Delay function (takes time in milliseconds)
function delayNextTask(time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

// Updated function to download the file with axios (follows redirects)
async function downloadFile(url, destination) {
  try {
    const response = await axios.get(url, {
      responseType: 'arraybuffer', // Ensure we get binary data for a zip file
      headers: { 'User-Agent': 'node.js' }
    });

    // Write data to the file
    fs.writeFileSync(destination, response.data);
    console.log(`Downloaded file to ${destination}`);
  } catch (error) {
    throw new Error(`Error downloading file: ${error.message}`);
  }
}

// Function to fetch the latest WordPress version tag
function fetchLatestWordPressVersion() {
  return new Promise((resolve, reject) => {
    https
      .get(wordpressRepoAPIURL, { headers: { 'User-Agent': 'node.js' } }, (res) => {
        let data = '';

        res.on('data', (chunk) => {
          data += chunk;
        });

        res.on('end', () => {
          const tags = JSON.parse(data);
          if (tags.length > 0) {
            const latestTag = tags[0].name;
            resolve(latestTag);
          } else {
            reject('No tags found for the repository');
          }
        });
      })
      .on('error', (err) => {
        reject(`Error fetching tags: ${err.message}`);
      });
  });
}

// Gulp task to convert all PHP files encoding from any encoding (like us-ascii) to utf-8
gulp.task('convert-php-encoding', function () {
  return gulp
    .src('./wp-fedora-core/**/*.php') // Include all PHP files inside the 'wp-fedora' directory and subdirectories
    .pipe(iconv({ from: 'us-ascii', to: 'utf-8' })) // Convert encoding to utf-8
    .pipe(gulp.dest('./wp-fedora-core')); // Save the converted files back to the same directory
});

// Copy /dist/assets/ folder to the plugin folder (retain `assets` folder)
gulp.task('copy-assets-from-dist-to-plugin-folder', function () {
  return gulp.src('dist/assets/**/*').pipe(gulp.dest('wp-fedora-core/wp-fedora/assets'));
});

// Copy /src/assets/img/ folder to the plugin folder inside /assets
gulp.task('copy-img-to-plugin-assets', function () {
  return gulp.src('src/assets/img/**/*').pipe(gulp.dest('wp-fedora-core/wp-fedora/assets/img'));
});

// Copy /src/php/ folder to the plugin folder
gulp.task('copy-php-to-plugin-folder', function () {
  return gulp.src('src/php/**/*').pipe(gulp.dest('wp-fedora-core/wp-fedora'));
});

// Gulp task to copy files to the mu-plugins folder
gulp.task('copy-plugin-to-mu-plugins', async function () {
  // Fetch the latest WordPress version to dynamically construct the path
  const latestVersion = await fetchLatestWordPressVersion();
  const muPluginsPath = path.resolve(__dirname, `WordPress-${latestVersion}/wp-content/mu-plugins`);

  // Source folder to copy files from
  const sourceFolder = path.resolve(__dirname, 'wp-fedora-core'); // Replace with your actual source folder

  return gulp
    .src(`${sourceFolder}/**/*`) // Select all files in the source folder
    .pipe(gulp.dest(muPluginsPath)) // Copy to mu-plugins path
    .on('end', () => {
      console.log(`Copied files from ${sourceFolder} to ${muPluginsPath}`);
    });
});

// Copy /src/assets/*.xsl files to the plugin folder inside /assets
gulp.task('copy-xsl-to-plugin-assets', function () {
  return gulp.src('src/assets/*.xsl').pipe(gulp.dest('wp-fedora-core/wp-fedora/assets'));
});

// Gulp task to create the mu-plugins folder
gulp.task('create-mu-plugins-folder', async function (done) {
  try {
    // Fetch the latest WordPress version to dynamically construct the path
    const latestVersion = await fetchLatestWordPressVersion();
    const wpContentPath = path.resolve(__dirname, `WordPress-${latestVersion}/wp-content`);

    const muPluginsPath = path.join(wpContentPath, 'mu-plugins');

    // Check if the mu-plugins folder exists, create it if it doesn't
    if (!fs.existsSync(muPluginsPath)) {
      fs.mkdirSync(muPluginsPath);
      console.log(`Created folder: ${muPluginsPath}`);
    } else {
      console.log(`Folder already exists: ${muPluginsPath}`);
    }
  } catch (error) {
    console.error(`Failed to create mu-plugins folder: ${error.message}`);
  }
  done();
});

// Ensure the package directories wp-fedora-core/wp-fedora exist
gulp.task('create-plugin-folders', function (done) {
  const coreDir = path.resolve(__dirname, 'wp-fedora-core');
  const pluginDir = path.join(coreDir, 'wp-fedora');

  // Check if wp-fedora-core exists, if not create it
  if (!fs.existsSync(coreDir)) {
    fs.mkdirSync(coreDir, { recursive: true });
    console.log(`Created directory: ${coreDir}`);
  }

  // Check if wp-fedora directory inside wp-fedora-core exists, if not create it
  if (!fs.existsSync(pluginDir)) {
    fs.mkdirSync(pluginDir, { recursive: true });
    console.log(`Created directory: ${pluginDir}`);
  }

  done();
});

// Gulp task to introduce a delay
gulp.task('delay-next-task', async function (done) {
  const delayTime = 1000; // Delay time in milliseconds (1 second)
  console.log(`Delaying for ${delayTime / 1000} seconds...`);
  await delayNextTask(delayTime);
  done();
});

// Recursively remove empty folders from the wp-fedora directory
gulp.task('delete-empty-folders', function (done) {
  const distDir = path.resolve(__dirname, 'wp-fedora');

  function removeEmptyDirs(dir) {
    // Check if the directory exists before proceeding
    if (!fs.existsSync(dir)) {
      console.log(`Directory ${dir} does not exist, skipping...`);
      return false;
    }

    const entries = fs.readdirSync(dir, { withFileTypes: true });
    let isEmpty = true;

    entries.forEach((entry) => {
      const entryPath = path.join(dir, entry.name);

      if (entry.isDirectory()) {
        // Recursively remove subdirectories
        if (removeEmptyDirs(entryPath)) {
          fs.rmdirSync(entryPath);
          console.log(`Removed empty folder: ${entryPath}`);
        } else {
          isEmpty = false;
        }
      } else {
        isEmpty = false;
      }
    });

    return isEmpty; // Return true if the directory is empty, otherwise false
  }

  removeEmptyDirs(distDir);
  done();
});

// Clean up directories, excluding zip files
gulp.task('delete-plugin-build-folders', function (done) {
  const distDir = path.resolve(__dirname, 'dist');
  const pluginDir = path.resolve(__dirname, 'wp-fedora-core');

  // Function to clean directories but exclude .zip and critical files like index.php
  function cleanDirectoryExcludingZip(directory) {
    if (fs.existsSync(directory)) {
      fs.readdirSync(directory).forEach((file) => {
        const filePath = path.join(directory, file);
        const stat = fs.statSync(filePath);

        if (stat.isDirectory()) {
          fs.rmSync(filePath, { recursive: true, force: true }); // Remove the directory itself
        } else if (!file.endsWith('.zip') && file !== 'index.php') {
          // Exclude index.php
          console.log(`Deleting file: ${filePath}`);
          fs.unlinkSync(filePath); // Remove the file
        } else {
          console.log(`Skipping: ${filePath}`);
        }
      });

      // After cleaning out all files, remove the root directory itself
      fs.rmSync(directory, { recursive: true, force: true }); // Updated to use fs.rmSync instead of fs.rmdirSync
      console.log(`Removed directory: ${directory}`);
    }
  }

  cleanDirectoryExcludingZip(pluginDir);
  cleanDirectoryExcludingZip(distDir);

  done();
});

// Gulp task to clean up the downloaded and unzipped WordPress files with error handling
gulp.task('delete-wordpress-build-folders', async function (done) {
  try {
    const latestVersion = await fetchLatestWordPressVersion();
    const zipFilePath = path.resolve(__dirname, `WordPress-${latestVersion}.zip`);
    const unzippedFolderPath = path.resolve(__dirname, `WordPress-${latestVersion}`);

    // Delete the zip file if it exists
    if (fs.existsSync(zipFilePath)) {
      fs.rmSync(zipFilePath, { force: true });
      console.log(`Deleted zip file: ${zipFilePath}`);
    } else {
      console.log(`Zip file not found: ${zipFilePath}`);
    }

    // Delete the unzipped folder if it exists
    if (fs.existsSync(unzippedFolderPath)) {
      fs.rmSync(unzippedFolderPath, { recursive: true, force: true });
      console.log(`Deleted unzipped folder: ${unzippedFolderPath}`);
    } else {
      console.log(`Unzipped folder not found: ${unzippedFolderPath}`);
    }
  } catch (error) {
    console.error(`Error during cleanup: ${error.message}`);
  }
  done();
});

// Download latest WP Version from GitHub with default naming
gulp.task('download-latest-wordpress-zip', async function (done) {
  try {
    const latestVersion = await fetchLatestWordPressVersion();
    const downloadUrl = `${wordpressRepoDownloadURL}/${latestVersion}.zip`;
    const downloadFileName = `WordPress-${latestVersion}.zip`; // Default naming format
    const downloadPath = path.resolve(__dirname, downloadFileName); // Path with default naming format

    console.log(`Downloading WordPress version ${latestVersion} from ${downloadUrl}`);
    await downloadFile(downloadUrl, downloadPath);

    console.log(`Downloaded WordPress version ${latestVersion} to ${downloadPath}`);
  } catch (error) {
    console.error(`Failed to download the latest version: ${error}`);
  }
  done();
});

// Move wp-fedora-core.php one level up
gulp.task('move-wp-fedora-core-php', function (done) {
  const sourcePath = 'wp-fedora-core/wp-fedora/wp-fedora-core.php';
  const targetPath = 'wp-fedora-core/wp-fedora-core.php';

  if (fs.existsSync(sourcePath)) {
    fs.renameSync(sourcePath, targetPath);
    console.log(`Moved wp-fedora-core.php to ${targetPath}`);
  } else {
    console.log('wp-fedora-core.php not found in the plugin folder.');
  }

  done();
});

// Gulp task to unzip the latest WordPress file in place
gulp.task('unzip-wordpress', async function (done) {
  try {
    const latestVersion = await fetchLatestWordPressVersion();
    const zipFileName = `WordPress-${latestVersion}.zip`; // Dynamic filename based on the version
    const zipFilePath = path.resolve(__dirname, zipFileName);

    console.log(`Unzipping ${zipFileName} in place`);

    // Initialize ADMZip with the zip file
    const zip = new ADMZip(zipFilePath);

    // Extract all files to the current directory
    zip.extractAllTo(/*target*/ __dirname, /*overwrite*/ true);

    console.log(`Extraction of ${zipFileName} complete at ${__dirname}`);
    done();
  } catch (error) {
    console.error(`Failed to unzip WordPress: ${error}`);
    done(error);
  }
});

// Gulp task to sync the version from package.json to the PHP file
gulp.task('update-plugin-version', function (done) {
  // Read package.json
  const packageJson = JSON.parse(fs.readFileSync(pathPackageFile, 'utf8'));
  const version = packageJson.version;

  // Read PHP file content
  const phpFileContent = fs.readFileSync(pathPluginFile, 'utf8');

  // Replace the version in the PHP file's comment header
  const updatedPhpFileContent = phpFileContent.replace(/Version:\s*\d+\.\d+\.\d+/, `Version:           ${version}`);

  // Write updated content back to the PHP file
  fs.writeFileSync(pathPluginFile, updatedPhpFileContent);

  console.log(`PHP file updated to version ${version}`);
  done();
});

// Gulp task to dynamically load and zip the entire wp-fedora-core folder
gulp.task('zip-plugin-core', async function () {
  const zip = (await import('gulp-zip')).default;
  const pluginDir = 'wp-fedora-core';
  const outputName = 'wp-fedora-core.zip';

  return gulp
    .src(`${pluginDir}/**/*`, { base: '.' }) // Include the folder itself and all its contents
    .pipe(zip(outputName)) // Create the zip
    .pipe(gulp.dest('./')); // Save it to the root directory
});

// Gulp task to zip the updated WordPress version
gulp.task('zip-wordpress-distro', async function () {
  const zip = (await import('gulp-zip')).default;

  // Fetch the latest WordPress version to dynamically construct the path and name
  const latestVersion = await fetchLatestWordPressVersion();
  const updatedWpFolder = path.resolve(__dirname, `WordPress-${latestVersion}`);
  const zipFileName = `WordPress-Fedora-${latestVersion}.zip`;

  return gulp
    .src(`${updatedWpFolder}/**/*`, { base: updatedWpFolder })
    .pipe(zip(zipFileName)) // Create the zip with the new name
    .pipe(gulp.dest(__dirname)) // Save to the current directory
    .on('end', () => {
      console.log(`Zipped updated WordPress version as ${zipFileName}`);
    });
});

// Build task (run all steps in sequence)
gulp.task(
  'build-plugin-core',
  gulp.series(
    'create-plugin-folders',
    'copy-php-to-plugin-folder',
    'copy-assets-from-dist-to-plugin-folder',
    'copy-img-to-plugin-assets',
    'copy-xsl-to-plugin-assets',
    'convert-php-encoding',
    'delete-empty-folders',
    'move-wp-fedora-core-php',
    'zip-plugin-core',
    function (done) {
      console.log('Plugin packaged successfully!');
      done();
    }
  )
);

gulp.task(
  'build-wordpress-distro',
  gulp.series(
    'download-latest-wordpress-zip',
    'unzip-wordpress',
    'create-mu-plugins-folder',
    'copy-plugin-to-mu-plugins',
    'zip-wordpress-distro',
    function (done) {
      console.log('WP Distro packaged successfully!');
      done();
    }
  )
);

// Project clean task - plugin version
gulp.task(
  'cleanup-plugin',
  gulp.series('delete-plugin-build-folders', function (done) {
    console.log('Project cleaned successfully!');
    done();
  })
);

// Project clean task - distro version
gulp.task(
  'cleanup-distro',
  gulp.series('delete-wordpress-build-folders', function (done) {
    console.log('Project cleaned successfully!');
    done();
  })
);
