const { chromium } = require('playwright');

async function testAuthModals() {
  console.log('Testing authentication modals...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    // Test Magic Link button
    console.log('\n=== Testing Magic Link Modal ===');
    const magicLinkButton = page.locator('button:has-text("Continue with Magic Link")');
    await magicLinkButton.waitFor({ state: 'visible', timeout: 5000 });
    console.log('✅ Magic Link button found');
    
    await magicLinkButton.click();
    await page.waitForTimeout(1000); // Wait for modal to open
    
    // Check if modal opened
    const magicLinkModal = page.locator('[data-modal="magic-link"]');
    const isModalVisible = await magicLinkModal.isVisible();
    console.log(`Magic Link modal visible: ${isModalVisible}`);
    
    if (isModalVisible) {
      // Test email input in modal
      const emailInput = magicLinkModal.locator('input[type="email"]');
      await emailInput.fill('test@example.com');
      console.log('✅ Email input works in Magic Link modal');
      
      // Close modal by clicking outside or escape
      await page.keyboard.press('Escape');
      await page.waitForTimeout(500);
    }
    
    // Test Auth Code button
    console.log('\n=== Testing Auth Code Modal ===');
    const authCodeButton = page.locator('button:has-text("Continue with Auth Code")');
    await authCodeButton.waitFor({ state: 'visible', timeout: 5000 });
    console.log('✅ Auth Code button found');
    
    await authCodeButton.click();
    await page.waitForTimeout(1000); // Wait for modal to open
    
    // Check if modal opened
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    const isAuthCodeModalVisible = await authCodeModal.isVisible();
    console.log(`Auth Code modal visible: ${isAuthCodeModalVisible}`);
    
    if (isAuthCodeModalVisible) {
      // Test email input in modal
      const emailInput = authCodeModal.locator('input[type="email"]');
      await emailInput.fill('test@example.com');
      console.log('✅ Email input works in Auth Code modal');
      
      // Test send code button
      const sendCodeButton = authCodeModal.locator('button:has-text("Send Login Code")');
      await sendCodeButton.click();
      console.log('✅ Send Code button clicked');
      
      await page.waitForTimeout(2000); // Wait for response
      
      // Check if the form changed to code input
      const codeInput = authCodeModal.locator('input[maxlength="6"]');
      const codeInputVisible = await codeInput.isVisible().catch(() => false);
      console.log(`Code input visible after send: ${codeInputVisible}`);
    }
    
    // Check for Google OAuth button (should be hidden)
    console.log('\n=== Testing Google OAuth Button ===');
    const googleButton = page.locator('button:has-text("Continue with Google"), a:has-text("Continue with Google")');
    const googleButtonCount = await googleButton.count();
    console.log(`Google OAuth buttons found: ${googleButtonCount} (should be 0)`);
    if (googleButtonCount === 0) {
      console.log('✅ Google OAuth button is properly hidden');
    } else {
      console.log('❌ Google OAuth button is still visible');
    }
    
    console.log('\n=== Test Results ===');
    console.log('✅ Login page loads successfully');
    console.log(`✅ Magic Link modal: ${isModalVisible ? 'Working' : 'Not working'}`);
    console.log(`✅ Auth Code modal: ${isAuthCodeModalVisible ? 'Working' : 'Not working'}`);
    console.log(`✅ Google OAuth hidden: ${googleButtonCount === 0 ? 'Yes' : 'No'}`);
    
    console.log('\nBrowser will stay open for 10 seconds for manual inspection...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testAuthModals().catch(console.error);