// @ts-check
const { defineConfig, devices } = require('@playwright/test');

/**
 * Konfigurasi Playwright - Multi-Role Testing dengan StorageState
 * CATATAN PENTING: Jalankan setup-auth.js terlebih dahulu!
 * Command: node tests/setup-auth.js
 */
module.exports = defineConfig({
  testDir: './tests',

  timeout: 60000, // 60 detik per test

  fullyParallel: false, // Sequential

  retries: 0,

  workers: 1,

  reporter: [
    ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ['list'],
  ],

  use: {
    baseURL: 'http://localhost/E2E-MagangIn/public',

    trace: 'on',
    screenshot: 'on',
    video: 'on',

    headless: false, // Tampilkan browser

    viewport: { width: 1280, height: 720 },

    actionTimeout: 15000,
  },

  // ===== PROJECTS BERDASARKAN ROLE =====
  projects: [
    // 1. ADMIN PROJECT
    {
      name: 'admin',
      testDir: './tests/e2e/admin',
      use: {
        ...devices['Desktop Chrome'],
        baseURL: 'http://localhost/E2E-MagangIn/public', // Explicit baseURL for this project
        storageState: 'tests/auth/admin.json'
      },
    },

    // 2. MAHASISWA PROJECT
    {
      name: 'mahasiswa',
      testDir: './tests/e2e/mahasiswa',
      use: {
        ...devices['Desktop Chrome'],
        baseURL: 'http://localhost/E2E-MagangIn/public', // Explicit baseURL for this project
        storageState: 'tests/auth/mahasiswa.json'
      },
    },

    // 3. DOSEN PROJECT
    {
      name: 'dosen',
      testDir: './tests/e2e/dosen',
      use: {
        ...devices['Desktop Chrome'],
        baseURL: 'http://localhost/E2E-MagangIn/public', // Explicit baseURL for this project
        storageState: 'tests/auth/dosen.json'
      },
    },

    // 4. INDUSTRI PROJECT
    {
      name: 'industri',
      testDir: './tests/e2e/industri',
      use: {
        ...devices['Desktop Chrome'],
        baseURL: 'http://localhost/E2E-MagangIn/public', // Explicit baseURL for this project
        storageState: 'tests/auth/industri.json'
      },
    },

    // 5. MULTI-ROLE PROJECT (untuk test alur lintas role)
    {
      name: 'multi-role',
      testDir: './tests/e2e/multi-role',
      use: {
        ...devices['Desktop Chrome'],
        baseURL: 'http://localhost/E2E-MagangIn/public' // Explicit baseURL for this project
        // Multi-role akan switch storageState di dalam test
      },
    },

    // 6. API TESTS (tanpa storageState, menggunakan request context)
    {
      name: 'api',
      testDir: './tests/api',
      use: {
        baseURL: 'http://localhost/E2E-MagangIn/public',
        headless: true, // Run in headless for faster API tests
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
      },
    },
  ],
});

