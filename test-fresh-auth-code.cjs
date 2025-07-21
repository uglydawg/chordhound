const { chromium } = require('playwright');

async function testFreshAuthCode() {
  console.log('Testing with fresh auth code: 589709');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    // Fill email in main form
    const testEmail = 'test.final@example.com';
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
    
    // Send auth code
    console.log('\n=== Sending Auth Code ===');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(3000); // Wait for email to be sent
    
    // Fill the fresh auth code
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    await codeInput.fill('589709');
    console.log('Filled fresh auth code: 589709');
    
    // Verify the code
    console.log('\n=== Verifying Auth Code ===');
    await authCodeModal.locator('button:has-text("Verify Code")').click();
    await page.waitForTimeout(3000); // Wait for verification
    
    // Check for specific error messages
    const errorElements = await page.locator('.text-red-600, .text-red-500, [class*="error"]').all();
    
    console.log('\n=== Checking for Errors ===');
    if (errorElements.length > 0) {
      console.log(`Found ${errorElements.length} potential error elements:`);
      for (let i = 0; i < errorElements.length; i++) {
        const text = await errorElements[i].textContent();
        if (text && text.trim()) {
          console.log(`Error ${i + 1}: "${text.trim()}"`);
        }
      }
    } else {
      console.log('No error elements found');
    }
    
    // Check current URL
    const currentUrl = page.url();
    console.log(`\nCurrent URL after verification: ${currentUrl}`);
    
    if (currentUrl.includes('dashboard')) {
      console.log('✅ SUCCESS! Logged in and redirected to dashboard!');
    } else if (currentUrl.includes('phone-verification')) {
      console.log('✅ SUCCESS! Redirected to phone verification (expected flow)!');
    } else {
      console.log('❌ Still on login page');
      
      // Check if modal is still open
      const modalVisible = await authCodeModal.isVisible();
      console.log(`Modal still visible: ${modalVisible}`);
      
      // Check code input value
      const codeValue = await codeInput.inputValue();
      console.log(`Code input value: "${codeValue}"`);
      
      // Check if there are validation errors on the form
      const validationErrors = await authCodeModal.locator('[wire\\:dirty], [class*="invalid"], .border-red').all();
      console.log(`Found ${validationErrors.length} validation-related elements`);
    }
    
    console.log('\nBrowser will stay open for 15 seconds for manual inspection...');
    await page.waitForTimeout(15000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testFreshAuthCode().catch(console.error);