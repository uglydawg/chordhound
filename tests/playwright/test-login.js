const { test, expect } = require('@playwright/test');

test('check login page accessibility and content', async ({ page }) => {
  console.log('Navigating to login page...');
  
  // Navigate to the login page
  const response = await page.goto('http://localhost:8000/login', { 
    waitUntil: 'networkidle',
    timeout: 10000 
  });
  
  console.log(`Response status: ${response.status()}`);
  console.log(`Response URL: ${response.url()}`);
  
  // Take a screenshot for debugging
  await page.screenshot({ path: 'login-page-screenshot.png', fullPage: true });
  
  // Get the page title
  const title = await page.title();
  console.log(`Page title: ${title}`);
  
  // Get the current URL (in case of redirects)
  const currentUrl = page.url();
  console.log(`Current URL: ${currentUrl}`);
  
  // Check if we're redirected (common if already authenticated)
  if (currentUrl !== 'http://localhost:8000/login') {
    console.log('⚠️  Redirected from login page');
    
    // If redirected to dashboard, let's logout first
    if (currentUrl.includes('dashboard')) {
      console.log('Currently on dashboard, attempting to logout...');
      
      // Look for logout button/link
      const logoutButton = page.locator('form[action*="logout"], button:has-text("Logout"), a:has-text("Logout"), [wire\\:click*="logout"]');
      if (await logoutButton.count() > 0) {
        await logoutButton.first().click();
        await page.waitForURL('**/login', { timeout: 5000 });
        console.log('Successfully logged out, now on login page');
      }
    }
  }
  
  // Check for key elements on the login page
  console.log('\n=== Checking page content ===');
  
  // Check for login form elements
  const emailInput = page.locator('input[type="email"], input[name="email"]');
  const passwordInput = page.locator('input[type="password"], input[name="password"]');
  const loginButton = page.locator('button[type="submit"], button:has-text("Log in")');
  
  console.log(`Email input found: ${await emailInput.count() > 0}`);
  console.log(`Password input found: ${await passwordInput.count() > 0}`);
  console.log(`Login button found: ${await loginButton.count() > 0}`);
  
  // Check for Google OAuth
  const googleButton = page.locator('a[href*="google"], button:has-text("Google")');
  console.log(`Google OAuth button found: ${await googleButton.count() > 0}`);
  
  // Check for Magic Link
  const magicLinkButton = page.locator('button:has-text("Magic Link")');
  console.log(`Magic Link button found: ${await magicLinkButton.count() > 0}`);
  
  // Check for Auth Code
  const authCodeButton = page.locator('button:has-text("Auth Code")');
  console.log(`Auth Code button found: ${await authCodeButton.count() > 0}`);
  
  // Check for register link
  const registerLink = page.locator('a[href*="register"], a:has-text("Sign up")');
  console.log(`Register link found: ${await registerLink.count() > 0}`);
  
  // Get page content for debugging
  const bodyText = await page.locator('body').textContent();
  console.log(`\nPage contains "login" text: ${bodyText.toLowerCase().includes('login')}`);
  console.log(`Page contains "email" text: ${bodyText.toLowerCase().includes('email')}`);
  console.log(`Page contains "password" text: ${bodyText.toLowerCase().includes('password')}`);
  
  // Check for any error messages
  const errorMessages = page.locator('.error, .alert, [class*="error"], [class*="alert"]');
  const errorCount = await errorMessages.count();
  if (errorCount > 0) {
    console.log(`\n⚠️  Found ${errorCount} error element(s):`);
    for (let i = 0; i < errorCount; i++) {
      const errorText = await errorMessages.nth(i).textContent();
      console.log(`  - ${errorText}`);
    }
  }
  
  // Print first 500 characters of body content for debugging
  console.log(`\n=== Page content preview ===`);
  console.log(bodyText.substring(0, 500) + (bodyText.length > 500 ? '...' : ''));
  
  console.log('\n=== Test completed ===');
});

test('check register page', async ({ page }) => {
  console.log('\nNavigating to register page...');
  
  const response = await page.goto('http://localhost:8000/register', { 
    waitUntil: 'networkidle',
    timeout: 10000 
  });
  
  console.log(`Register page response status: ${response.status()}`);
  console.log(`Register page URL: ${response.url()}`);
  
  // Take a screenshot
  await page.screenshot({ path: 'register-page-screenshot.png', fullPage: true });
  
  const title = await page.title();
  console.log(`Register page title: ${title}`);
  
  // Check for registration form elements
  const nameInput = page.locator('input[name="name"], input[placeholder*="name"]');
  const emailInput = page.locator('input[type="email"], input[name="email"]');
  const passwordInput = page.locator('input[type="password"]');
  
  console.log(`Name input found: ${await nameInput.count() > 0}`);
  console.log(`Email input found: ${await emailInput.count() > 0}`);
  console.log(`Password input found: ${await passwordInput.count() > 0}`);
  
  const bodyText = await page.locator('body').textContent();
  console.log(`Register page contains "register" text: ${bodyText.toLowerCase().includes('register')}`);
  console.log(`Register page contains "account" text: ${bodyText.toLowerCase().includes('account')}`);
});