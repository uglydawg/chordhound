const { chromium } = require('playwright');

async function testFixedCodeAnyEmail() {
  console.log('Testing fixed code 555121 works with ANY email...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    // Test with a completely random email
    const testEmail = 'random.user@anydomain.com';
    console.log(`Filling main email form with: ${testEmail}`);
    await page.fill('input[name="email"]', testEmail);
    
    // Open Auth Code modal
    console.log('\n=== Opening Auth Code Modal ===');
    await page.click('button:has-text("Continue with Auth Code")');
    await page.waitForTimeout(500);
    
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    
    // Verify email pre-filled
    const modalEmail = await authCodeModal.locator('input[type="email"]').inputValue();
    console.log(`Modal email pre-filled: "${modalEmail}"`);
    
    // Send code first to make input appear
    console.log('\n=== Sending Code First ===');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(2000);
    
    // Test fixed code
    console.log('\n=== Testing Fixed Code 555121 with Random Email ===');
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    await codeInput.fill('555121');
    console.log('Filled fixed code: 555121');
    
    // Verify the code
    console.log('\n=== Verifying Fixed Code ===');
    await authCodeModal.locator('button:has-text("Verify Code")').click();
    await page.waitForTimeout(3000); // Wait for verification
    
    // Check current URL
    const currentUrl = page.url();
    console.log(`Current URL after verification: ${currentUrl}`);
    
    if (currentUrl.includes('dashboard')) {
      console.log('✅ SUCCESS! Fixed code 555121 works with ANY email address!');
      console.log(`User created/logged in with email: ${testEmail}`);
    } else {
      console.log('❌ Fixed code failed');
    }
    
    console.log('\nBrowser will stay open for 10 seconds...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testFixedCodeAnyEmail().catch(console.error);