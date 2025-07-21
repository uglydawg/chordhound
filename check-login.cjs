const { chromium } = require('playwright');

async function checkLoginPage() {
  console.log('Starting login page check...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    const response = await page.goto('http://localhost:8000/login', { 
      waitUntil: 'networkidle',
      timeout: 10000 
    });
    
    console.log(`Response status: ${response.status()}`);
    console.log(`Response URL: ${response.url()}`);
    
    // Take a screenshot for debugging
    await page.screenshot({ path: 'login-page-screenshot.png', fullPage: true });
    console.log('Screenshot saved as login-page-screenshot.png');
    
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
        const logoutElements = await page.$$('form[action*="logout"], button:has-text("Logout"), a:has-text("Logout"), [wire\\:click*="logout"]');
        if (logoutElements.length > 0) {
          console.log('Found logout element, clicking...');
          await logoutElements[0].click();
          await page.waitForTimeout(2000); // Wait for logout
          await page.goto('http://localhost:8000/login');
          console.log('Successfully navigated back to login page');
        }
      }
    }
    
    // Check for key elements on the login page
    console.log('\n=== Checking page content ===');
    
    // Check for login form elements
    const emailInputs = await page.$$('input[type="email"], input[name="email"]');
    const passwordInputs = await page.$$('input[type="password"], input[name="password"]');
    const loginButtons = await page.$$('button[type="submit"], button:has-text("Log in")');
    
    console.log(`Email input found: ${emailInputs.length > 0} (count: ${emailInputs.length})`);
    console.log(`Password input found: ${passwordInputs.length > 0} (count: ${passwordInputs.length})`);
    console.log(`Login button found: ${loginButtons.length > 0} (count: ${loginButtons.length})`);
    
    // Check for Google OAuth
    const googleButtons = await page.$$('a[href*="google"], button:has-text("Google")');
    console.log(`Google OAuth button found: ${googleButtons.length > 0} (count: ${googleButtons.length})`);
    
    // Check for Magic Link
    const magicLinkButtons = await page.$$('button:has-text("Magic Link")');
    console.log(`Magic Link button found: ${magicLinkButtons.length > 0} (count: ${magicLinkButtons.length})`);
    
    // Check for Auth Code
    const authCodeButtons = await page.$$('button:has-text("Auth Code")');
    console.log(`Auth Code button found: ${authCodeButtons.length > 0} (count: ${authCodeButtons.length})`);
    
    // Check for register link
    const registerLinks = await page.$$('a[href*="register"], a:has-text("Sign up")');
    console.log(`Register link found: ${registerLinks.length > 0} (count: ${registerLinks.length})`);
    
    // Get page content for debugging
    const bodyText = await page.textContent('body');
    console.log(`\nPage contains "login" text: ${bodyText.toLowerCase().includes('login')}`);
    console.log(`Page contains "email" text: ${bodyText.toLowerCase().includes('email')}`);
    console.log(`Page contains "password" text: ${bodyText.toLowerCase().includes('password')}`);
    
    // Check for any error messages
    const errorElements = await page.$$('.error, .alert, [class*="error"], [class*="alert"]');
    if (errorElements.length > 0) {
      console.log(`\n⚠️  Found ${errorElements.length} error element(s):`);
      for (let i = 0; i < errorElements.length; i++) {
        const errorText = await errorElements[i].textContent();
        console.log(`  - ${errorText}`);
      }
    }
    
    // Print first 500 characters of body content for debugging
    console.log(`\n=== Page content preview ===`);
    console.log(bodyText.substring(0, 500) + (bodyText.length > 500 ? '...' : ''));
    
    // Check HTML source
    const htmlContent = await page.content();
    console.log(`\n=== HTML source preview (first 1000 chars) ===`);
    console.log(htmlContent.substring(0, 1000) + (htmlContent.length > 1000 ? '...' : ''));
    
    console.log('\n=== Test completed ===');
    
    // Keep browser open for manual inspection
    console.log('Browser will stay open for 10 seconds for manual inspection...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

async function checkRegisterPage() {
  console.log('\n\nStarting register page check...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to register page...');
    const response = await page.goto('http://localhost:8000/register', { 
      waitUntil: 'networkidle',
      timeout: 10000 
    });
    
    console.log(`Register page response status: ${response.status()}`);
    console.log(`Register page URL: ${response.url()}`);
    
    // Take a screenshot
    await page.screenshot({ path: 'register-page-screenshot.png', fullPage: true });
    console.log('Screenshot saved as register-page-screenshot.png');
    
    const title = await page.title();
    console.log(`Register page title: ${title}`);
    
    // Check for registration form elements
    const nameInputs = await page.$$('input[name="name"], input[placeholder*="name"]');
    const emailInputs = await page.$$('input[type="email"], input[name="email"]');
    const passwordInputs = await page.$$('input[type="password"]');
    
    console.log(`Name input found: ${nameInputs.length > 0} (count: ${nameInputs.length})`);
    console.log(`Email input found: ${emailInputs.length > 0} (count: ${emailInputs.length})`);
    console.log(`Password input found: ${passwordInputs.length > 0} (count: ${passwordInputs.length})`);
    
    const bodyText = await page.textContent('body');
    console.log(`Register page contains "register" text: ${bodyText.toLowerCase().includes('register')}`);
    console.log(`Register page contains "account" text: ${bodyText.toLowerCase().includes('account')}`);
    
    // Print content preview
    console.log(`\n=== Register page content preview ===`);
    console.log(bodyText.substring(0, 500) + (bodyText.length > 500 ? '...' : ''));
    
    console.log('\nBrowser will stay open for 10 seconds for manual inspection...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during register test:', error);
  } finally {
    await browser.close();
  }
}

// Run both checks
checkLoginPage().then(() => {
  return checkRegisterPage();
}).catch(console.error);