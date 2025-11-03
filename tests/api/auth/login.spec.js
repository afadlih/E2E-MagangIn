/**
 * API Test: Authentication
 * Test untuk endpoint login
 * 
 * NOTE: Untuk Laravel dengan CSRF, kita test API response setelah form submission
 */

const { test, expect } = require('@playwright/test');
const { BASE_URL } = require('../helpers');

test.describe('API - Authentication', () => {

  test('POST /login - Success with valid credentials (Admin)', async ({ page }) => {
    // Load login page
    await page.goto(`${BASE_URL}/login`);

    // Listen untuk response dari login API
    const responsePromise = page.waitForResponse(response => 
      response.url().includes('/login') && response.request().method() === 'POST'
    );

    // Submit form
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', '12345');
    await page.click('button[type="submit"]');

    // Get response
    const response = await responsePromise;
    const body = await response.json();

    // Assert response
    expect(body).toHaveProperty('status');
    expect(body.status).toBe(true);
    expect(body).toHaveProperty('redirect');
    expect(body.redirect).toContain('dashboard');

    console.log('✓ Login admin berhasil - Status:', body.status, 'Redirect:', body.redirect);
  });

  test('POST /login - Fail with invalid credentials', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);

    const responsePromise = page.waitForResponse(response => 
      response.url().includes('/login') && response.request().method() === 'POST'
    );

    // Submit dengan password salah
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', 'wrong_password');
    await page.click('button[type="submit"]');

    const response = await responsePromise;
    const body = await response.json();

    // Assert login gagal
    expect(body).toHaveProperty('status');
    expect(body.status).toBe(false);
    expect(body).toHaveProperty('message');
    
    console.log('✓ Login dengan password salah ditolak - Message:', body.message);
  });
  
  test('POST /login - Fail with empty username (client-side validation)', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);

    // Submit dengan username kosong
    await page.fill('input[name="username"]', '');
    await page.fill('input[name="password"]', '12345');
    
    // HTML5 validation akan prevent submit, atau SweetAlert akan muncul
    // Kita cek apakah form tidak submit (masih di halaman login)
    await page.click('button[type="submit"]');
    
    // Wait sedikit untuk check
    await page.waitForTimeout(1000);
    
    // Verify masih di login page
    expect(page.url()).toContain('/login');
    
    console.log('✓ Validasi username kosong berhasil (client-side)');
  });
  
  test('POST /login - Success with valid credentials (Mahasiswa)', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);

    const responsePromise = page.waitForResponse(response => 
      response.url().includes('/login') && response.request().method() === 'POST'
    );

    await page.fill('input[name="username"]', 'mahasiswa');
    await page.fill('input[name="password"]', 'mhs');
    await page.click('button[type="submit"]');

    const response = await responsePromise;
    const body = await response.json();

    expect(body.status).toBe(true);
    expect(body.redirect).toContain('dashboard-mahasiswa');
    
    console.log('✓ Login mahasiswa berhasil - Redirect:', body.redirect);
  });
  
  test('POST /login - Success with valid credentials (Dosen)', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);

    const responsePromise = page.waitForResponse(response => 
      response.url().includes('/login') && response.request().method() === 'POST'
    );

    await page.fill('input[name="username"]', 'dosen');
    await page.fill('input[name="password"]', 'dsn');
    await page.click('button[type="submit"]');

    const response = await responsePromise;
    const body = await response.json();

    expect(body.status).toBe(true);
    expect(body.redirect).toContain('dashboard-dosen');
    
    console.log('✓ Login dosen berhasil - Redirect:', body.redirect);
  });

});
