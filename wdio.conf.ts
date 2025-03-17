/// <reference types="@wdio/types" />

// Core Modules
import path from 'path';
import { fileURLToPath } from 'url';

// NPM Modules
import dotenv from 'dotenv';

// Define `__dirname` manually for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Supported Builders
const BUILDERS = [
    'BEAVER',
    'BREAKDANCE',
    'BRICKS',
    'BRIZY',
    'CUSTOM_THEME',
    'DIVI',
    'ELEMENTOR',
    'GUTENBERG',
    'NONE',
    'OXYGEN_CLASSIC',
    'YOOTHEME'
];

// Convert Host to ENV File Naming Convention
const envMap: { [key: string]: string } = {
    localwp: 'localwp',
    wpengine: 'wpengine',
    flywheel: 'flywheel'
};

// Ensure `NODE_ENV` is respected when loading `.env`
const NODE_ENV = process.env.NODE_ENV || 'test';
const TEST_HOST = (process.env.TEST_HOST || 'localwp').toLowerCase();

// Validate if the provided host is supported
if (!Object.keys(envMap).includes(TEST_HOST)) {
    throw new Error(`Invalid TEST_HOST: ${TEST_HOST}. Must be one of: ${Object.keys(envMap).join(', ')}`);
}

// Load the correct `.env` file based on NODE_ENV
const envPath = path.resolve(__dirname, `.env.${NODE_ENV}`);
dotenv.config({ path: envPath });

// Dynamically Build the `siteData` Array
const siteData = BUILDERS.map((builder) => ({
    host: TEST_HOST,
    builder,
    link: process.env[`${envMap[TEST_HOST].toUpperCase()}_${builder}_LINK`] || '',
    username: process.env[`${envMap[TEST_HOST].toUpperCase()}_${builder}_USERNAME`] || '',
    password: process.env[`${envMap[TEST_HOST].toUpperCase()}_${builder}_PASSWORD`] || ''
}));

export const config: WebdriverIO.Config = {
    bail: 0,

    // Inject `siteData` into global scope for easy access in tests
    beforeSession: function () {
        global.siteData = siteData;
    },

    capabilities: [
        {
            browserName: 'chrome'
        }
    ],
    connectionRetryCount: 3,
    connectionRetryTimeout: 120000,
    exclude: [],
    framework: 'mocha',
    logLevels: {
        webdriver: 'info',
        webdriverio: 'info',
        '@wdio/mocha-framework': 'info'
    },
    // **Generate & Open Allure Report After Tests**
    onComplete: async function () {
        const { execSync } = await import('child_process');
        const reportDir = 'test-reports/data';
        const outputDir = 'test-reports/report';

        console.log('📊 Generating Allure Report...');
        execSync(`allure generate ${reportDir} --clean -o ${outputDir}`, { stdio: 'inherit' });

        console.log('🚀 Opening Allure Report...');
        execSync(`allure open ${outputDir}`, { stdio: 'inherit' });
    },
    maxInstances: 10,
    mochaOpts: {
        ui: 'bdd',
        timeout: 60000
    },
    reporters: [['allure', { outputDir: 'test-reports/data' }]],
    runner: 'local',
    tsConfigPath: './test/tsconfig.json',
    specs: ['./test/specs/**/*.ts'],
    waitforTimeout: 10000
};
