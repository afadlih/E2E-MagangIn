/**
 * API Test Helpers
 * Utility functions untuk API testing
 */

const BASE_URL = 'http://localhost/E2E-MagangIn/public';

/**
 * Login dan dapatkan session cookies
 */
/**
 * Helper untuk login via API
 * NOTE: Laravel requires CSRF token, so we need to load login page first
 *
 * @param {import('@playwright/test').Page} page - Playwright page context
 * @param {string} username - Username untuk login
 * @param {string} password - Password untuk login
 * @returns {Promise<Object>} Response data dari login
 */
async function login(page, username, password) {
  // Load login page untuk get CSRF token
  await page.goto(`${BASE_URL}/login`);

  // Submit login via API request (akan include CSRF token)
  const response = await page.request.post(`${BASE_URL}/login`, {
    form: {
      username,
      password
    }
  });

  if (!response.ok()) {
    throw new Error(`Login failed: ${response.status()} ${response.statusText()}`);
  }

  const body = await response.json();
  return body;
}

/**
 * Generate random string untuk testing
 */
function generateRandomString(length = 10) {
  return Math.random().toString(36).substring(2, length + 2);
}

/**
 * Generate timestamp untuk unique data
 */
function getTimestamp() {
  return Date.now();
}

/**
 * Assert response status
 */
function assertStatus(response, expectedStatus) {
  if (response.status() !== expectedStatus) {
    throw new Error(`Expected status ${expectedStatus}, got ${response.status()}`);
  }
}

/**
 * Assert JSON response contains keys
 */
async function assertResponseHasKeys(response, keys) {
  const body = await response.json();

  for (const key of keys) {
    if (!(key in body)) {
      throw new Error(`Response missing key: ${key}`);
    }
  }

  return body;
}

/**
 * Extract CSRF token dari page
 */
async function getCsrfToken(page) {
  try {
    const token = await page.locator('input[name="_token"]').getAttribute('value');
    return token;
  } catch (e) {
    console.log('CSRF token not found');
    return null;
  }
}

module.exports = {
  BASE_URL,
  login,
  generateRandomString,
  getTimestamp,
  assertStatus,
  assertResponseHasKeys,
  getCsrfToken
};
