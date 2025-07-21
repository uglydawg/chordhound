const { chromium } = require('playwright');

async function testFixedCode() {
  console.log('Testing fixed login code 555121...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    // Fill email in main form
    const testEmail = 'test.fixed@example.com';
    console.log(`Filling main email form with: ${testEmail}`);
    await page.fill('input[name="email"]', testEmail);
    
    // Open Auth Code modal
    console.log('\n=== Opening Auth Code Modal ===');
    await page.click('button:has-text("Continue with Auth Code")');
    await page.waitForTimeout(500);
    
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    
    // Send code first to make input appear
    console.log('\n=== Sending Code First ===');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(2000);
    
    // Now test fixed code
    console.log('\n=== Testing Fixed Code 555121 ===');
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
      console.log('✅ SUCCESS! Fixed code worked and redirected to dashboard!');
    } else {
      console.log('❌ Fixed code did not work (expected since feature is disabled by default)');
      
      // Check for error message
      const errorElements = await authCodeModal.locator('.text-red-600, .text-red-500, [class*="error"]').all();
      if (errorElements.length > 0) {
        for (let i = 0; i < errorElements.length; i++) {
          const text = await errorElements[i].textContent();
          if (text && text.trim()) {
            console.log(`Error: "${text.trim()}"`);
          }
        }
      }
    }
    
    console.log('\nBrowser will stay open for 10 seconds...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testFixedCode().catch(console.error);