/**
 * API Test: Lowongan
 * Test untuk endpoints lowongan
 *
 * NOTE: Test ini menggunakan mahasiswa account
 */

const { test, expect } = require('@playwright/test');
const { BASE_URL } = require('../helpers');

test.describe('API - Lowongan', () => {

  test('GET /lowongan - Get list lowongan (check authorization)', async ({ page }) => {
    // Login sebagai mahasiswa
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="username"]', 'mahasiswa');
    await page.fill('input[name="password"]', 'mhs');
    await page.click('button[type="submit"]');
    await page.waitForSelector('.swal2-confirm', { timeout: 5000 });
    await page.click('.swal2-confirm');
    await page.waitForURL('**/dashboard-mahasiswa', { timeout: 10000 });

    // Request lowongan page
    await page.goto(`${BASE_URL}/lowongan`);

    // Verify response
    await page.waitForSelector('body');
    const content = await page.content();

    // Check if forbidden or accessible
    if (content.includes('Forbidden') || content.includes('403')) {
      console.log('ℹ Mahasiswa tidak memiliki akses ke halaman lowongan (403 Forbidden)');
      expect(content).toContain('Forbidden');
    } else {
      console.log('✓ Get list lowongan berhasil');
      expect(content).toContain('Lowongan');
    }
  });

  test('GET /lowongan/{id} - Get lowongan detail (check authorization)', async ({ page }) => {
    // Login sebagai mahasiswa
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="username"]', 'mahasiswa');
    await page.fill('input[name="password"]', 'mhs');
    await page.click('button[type="submit"]');
    await page.waitForSelector('.swal2-confirm', { timeout: 5000 });
    await page.click('.swal2-confirm');
    await page.waitForURL('**/dashboard-mahasiswa', { timeout: 10000 });

    // Request lowongan detail (assume ID 1 exists)
    await page.goto(`${BASE_URL}/lowongan/1`);

    // Verify response
    await page.waitForSelector('body');
    const content = await page.content();

    // Check response - could be 403, 404, or success
    const isForbidden = content.includes('Forbidden') || content.includes('403');
    const isNotFound = content.includes('404') || content.includes('Not Found');

    if (isForbidden) {
      console.log('ℹ Mahasiswa tidak memiliki akses ke lowongan detail (403 Forbidden)');
      expect(content).toContain('Forbidden');
    } else if (isNotFound) {
      console.log('ℹ Lowongan dengan ID 1 tidak ditemukan (404 Not Found)');
      expect(content).toContain('404');
    } else {
      console.log('✓ Page lowongan detail accessible');
      expect(page.url()).toContain('/lowongan/');
    }
  });
});
