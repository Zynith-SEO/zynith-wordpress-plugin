// Core Modules
import { exec } from 'child_process';
import path, { resolve } from 'path';
import { existsSync, readFileSync } from 'fs';

// NPM Modules
import { glob } from 'glob';
import { defineConfig } from 'vite';

// Use glob to find all valid JavaScript and SCSS files
const jsEntryPoints = glob.sync(resolve(__dirname, 'src/**/*.js'));
const scssEntryPoints = glob.sync(resolve(__dirname, 'src/**/*.scss')); // Glob for all SCSS files

// Create an input object where each JS file is treated as a separate entry
const inputFiles = jsEntryPoints.reduce(
  (entries, file) => {
    const name = file.replace(/.*[\\\/]/, '').replace(/\.js$/, ''); // Use filename without extension as entry name
    entries[name] = file;
    return entries;
  },
  {} as Record<string, string>
);

// Add all SCSS files as entries in inputFiles
scssEntryPoints.forEach((file) => {
  const name = file.replace(/.*[\\\/]/, '').replace(/\.scss$/, ''); // Use filename without extension as entry name
  inputFiles[name] = file;
});

// Function to check if a file is empty or trivial
function isOriginalFileEmptyOrTrivial(filePath: string): boolean {
  if (filePath.includes('?')) {
    return false;
  }

  if (!existsSync(filePath)) {
    return false;
  }

  const content = readFileSync(filePath, 'utf-8').trim();
  return !content || content === 'export {};' || content.length < 10;
}

export default defineConfig({
  plugins: [
    {
      name: 'watch-src-folder-and-run-script',
      handleHotUpdate({ file, server }) {
        const relativeFilePath = path.relative(server.config.root, file);

        // Only trigger when files in the /src folder are changed
        if (relativeFilePath.startsWith('src/')) {
          console.log(`File in /src changed: ${file}`);

          // Trigger your custom NPM script
          exec('npm run build:dev', (err, stdout, stderr) => {
            if (err) {
              console.error(`Error running script: ${err}`);
              return;
            }
            console.log(`Script output: ${stdout}`);
          });
        }
      }
    }
  ],
  build: {
    minify: true, // Enable minification for both JS and CSS
    outDir: 'dist',
    rollupOptions: {
      input: inputFiles, // Use the dynamically created input object (JS and SCSS)
      output: {
        entryFileNames: 'assets/js/[name].js', // JS files in assets/js
        chunkFileNames: 'assets/js/[name].js',
        assetFileNames: (assetInfo: any) => {
          if (assetInfo.name.endsWith('.css')) {
            // Put CSS files in the assets/css folder
            return 'assets/css/[name].css';
          }
          return 'assets/[name].[ext]'; // Default behavior for other assets
        },
        manualChunks(id) {
          // Exclude files that are minimal or trivial
          if (isOriginalFileEmptyOrTrivial(id)) {
            return undefined; // Do not create a chunk for empty files
          }
        }
      }
    }
  }
});
