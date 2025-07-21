const { chromium } = require('playwright');

async function testAuthCodeFlow() {
  console.log('Testing complete auth code flow...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    // Fill email in main form
    const testEmail = 'test.fresh@example.com';
    console.log(`Filling main email form with: ${testEmail}`);
    await page.fill('input[name="email"]', testEmail);
    
    // Open Auth Code modal
    console.log('\n=== Opening Auth Code Modal ===');
    await page.click('button:has-text("Continue with Auth Code")');
    await page.waitForTimeout(500);
    
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    const emailValue = await authCodeModal.locator('input[type="email"]').inputValue();
    console.log(`Modal email pre-filled: ${emailValue}`);
    
    // Send auth code
    console.log('\n=== Sending Auth Code ===');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(3000); // Wait for email to be sent
    
    // Check if transitioned to code input
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    const codeInputVisible = await codeInput.isVisible();
    console.log(`Code input appeared: ${codeInputVisible}`);
    
    if (codeInputVisible) {
      // The code should be logged to Laravel logs, let's check what's displayed
      const instructionText = await authCodeModal.locator('text=We\'ve sent a 6-digit code to').textContent();
      console.log(`Instruction text: ${instructionText}`);
      
      console.log('\n=== Please check Laravel logs for the auth code ===');
      console.log('You can run: tail -10 storage/logs/laravel.log | grep "login code"');
      
      // Wait for user to see the code in logs and enter it manually
      console.log('\nWaiting 15 seconds for manual code entry...');
      await page.waitForTimeout(15000);
      
      // Check if any error message appeared
      const errorAlert = authCodeModal.locator('flux\\:alert, [class*="error"], .text-red');
      const errorCount = await errorAlert.count();
      if (errorCount > 0) {
        console.log('\n=== Error Messages Found ===');
        for (let i = 0; i < errorCount; i++) {
          const errorText = await errorAlert.nth(i).textContent();
          console.log(`Error ${i + 1}: ${errorText}`);
        }
      }
      
      // Check current URL (might have redirected)
      const currentUrl = page.url();
      console.log(`Current URL: ${currentUrl}`);
      
      if (currentUrl.includes('dashboard')) {
        console.log('✅ Successfully logged in and redirected to dashboard!');
      } else {
        console.log('❌ Still on login page - verification may have failed');
        
        // Check the code input value
        const codeValue = await codeInput.inputValue();
        console.log(`Code input value: "${codeValue}"`);
      }
    }
    
    console.log('\nBrowser will stay open for 10 more seconds...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testAuthCodeFlow().catch(console.error);