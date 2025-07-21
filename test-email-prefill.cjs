const { chromium } = require('playwright');

async function testEmailPrefill() {
  console.log('Testing email pre-filling functionality...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    // Fill email in main form
    const testEmail = 'test@example.com';
    console.log(`Filling main email form with: ${testEmail}`);
    await page.fill('input[name="email"]', testEmail);
    
    // Test Magic Link email pre-fill
    console.log('\n=== Testing Magic Link Email Pre-fill ===');
    await page.click('button:has-text("Continue with Magic Link")');
    await page.waitForTimeout(500); // Wait for modal and pre-fill
    
    const magicLinkModal = page.locator('[data-modal="magic-link"]');
    const magicLinkEmailValue = await magicLinkModal.locator('input[type="email"]').inputValue();
    console.log(`Magic Link modal email value: "${magicLinkEmailValue}"`);
    console.log(`Pre-fill successful: ${magicLinkEmailValue === testEmail}`);
    
    // Close modal
    await page.keyboard.press('Escape');
    await page.waitForTimeout(300);
    
    // Test Auth Code email pre-fill
    console.log('\n=== Testing Auth Code Email Pre-fill ===');
    await page.click('button:has-text("Continue with Auth Code")');
    await page.waitForTimeout(500); // Wait for modal and pre-fill
    
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    const authCodeEmailValue = await authCodeModal.locator('input[type="email"]').inputValue();
    console.log(`Auth Code modal email value: "${authCodeEmailValue}"`);
    console.log(`Pre-fill successful: ${authCodeEmailValue === testEmail}`);
    
    // Test sending auth code with pre-filled email
    console.log('\n=== Testing Auth Code Send with Pre-filled Email ===');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(2000); // Wait for processing
    
    // Check if it transitioned to code input
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    const codeInputVisible = await codeInput.isVisible().catch(() => false);
    console.log(`Code input appeared: ${codeInputVisible}`);
    
    if (codeInputVisible) {
      console.log('✅ Auth code sent successfully with pre-filled email!');
      
      // Fill in the correct code from logs (533892)
      await codeInput.fill('533892');
      console.log('Filled auth code: 533892');
      
      // Try to verify
      await authCodeModal.locator('button:has-text("Verify Code")').click();
      await page.waitForTimeout(2000);
      
      // Check if redirected or still on modal
      const currentUrl = page.url();
      console.log(`Current URL after verification: ${currentUrl}`);
    }
    
    console.log('\n=== Test Results ===');
    console.log(`✅ Magic Link email pre-fill: ${magicLinkEmailValue === testEmail ? 'Working' : 'Not working'}`);
    console.log(`✅ Auth Code email pre-fill: ${authCodeEmailValue === testEmail ? 'Working' : 'Not working'}`);
    console.log(`✅ Auth Code send functionality: ${codeInputVisible ? 'Working' : 'Not working'}`);
    
    console.log('\nBrowser will stay open for 10 seconds for manual inspection...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testEmailPrefill().catch(console.error);