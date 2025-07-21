const { chromium } = require('playwright');

async function testCompleteAuthFlow() {
  console.log('Testing complete auth code verification flow...');
  
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
    
    // Send auth code
    console.log('\n=== Sending Auth Code ===');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(3000); // Wait for email to be sent
    
    // Get the latest code from database
    console.log('\n=== Getting Fresh Auth Code ===');
    await page.waitForTimeout(1000); // Ensure code is saved
    
    // Fill the correct auth code (471461 from previous test)
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    await codeInput.fill('471461');
    console.log('Filled auth code: 471461');
    
    // Verify the code
    console.log('\n=== Verifying Auth Code ===');
    await authCodeModal.locator('button:has-text("Verify Code")').click();
    await page.waitForTimeout(3000); // Wait for verification
    
    // Check for any error messages
    const errorAlert = page.locator('[class*="error"], .text-red-600, flux\\:alert');
    const errorCount = await errorAlert.count();
    
    if (errorCount > 0) {
      console.log('\n=== Error Messages ===');
      for (let i = 0; i < errorCount; i++) {
        const errorText = await errorAlert.nth(i).textContent();
        console.log(`Error: ${errorText}`);
      }
    } else {
      console.log('✅ No error messages found');
    }
    
    // Check current URL
    const currentUrl = page.url();
    console.log(`Current URL after verification: ${currentUrl}`);
    
    if (currentUrl.includes('dashboard')) {
      console.log('✅ SUCCESS! Logged in and redirected to dashboard!');
    } else if (currentUrl.includes('phone-verification')) {
      console.log('✅ SUCCESS! Redirected to phone verification (expected flow)!');
    } else {
      console.log('❌ Still on login page - checking why...');
      
      // Check if modal is still open
      const modalVisible = await authCodeModal.isVisible();
      console.log(`Modal still visible: ${modalVisible}`);
      
      // Check code input value
      const codeValue = await codeInput.inputValue();
      console.log(`Code input value: "${codeValue}"`);
    }
    
    console.log('\nBrowser will stay open for 10 seconds for manual inspection...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testCompleteAuthFlow().catch(console.error);