const { chromium } = require('playwright');

async function testDashboardDirect() {
  console.log('Testing dashboard page directly...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // Login first
    console.log('Logging in...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    const testEmail = 'test.dashboard@example.com';
    await page.fill('input[name="email"]', testEmail);
    
    await page.click('button:has-text("Continue with Auth Code")');
    await page.waitForTimeout(500);
    
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(2000);
    
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    await codeInput.fill('555121');
    await authCodeModal.locator('button:has-text("Verify Code")').click();
    await page.waitForTimeout(3000);
    
    console.log('✅ Logged in, checking dashboard...');
    
    // Check current URL
    const currentUrl = page.url();
    console.log(`Current URL: ${currentUrl}`);
    
    if (currentUrl.includes('dashboard')) {
      console.log('✅ On dashboard page');
      
      // Check for any error messages on the page
      const errorElements = await page.locator('.error, [class*="error"], .text-red').all();
      if (errorElements.length > 0) {
        console.log('Found error messages:');
        for (let i = 0; i < errorElements.length; i++) {
          const text = await errorElements[i].textContent();
          if (text && text.trim()) {
            console.log(`Error: ${text.trim()}`);
          }
        }
      }
      
      // Check page title
      const title = await page.title();
      console.log(`Page title: ${title}`);
      
      // Check if Flux components are present
      const fluxElements = await page.locator('[class*="flux"]').all();
      console.log(`Found ${fluxElements.length} Flux-related elements`);
      
      // Look for sidebar specifically
      const sidebarElements = await page.locator('aside, [role="navigation"], nav, [class*="sidebar"]').all();
      console.log(`Found ${sidebarElements.length} potential sidebar elements`);
      
      if (sidebarElements.length > 0) {
        for (let i = 0; i < sidebarElements.length; i++) {
          const tagName = await sidebarElements[i].evaluate(el => el.tagName);
          const classes = await sidebarElements[i].getAttribute('class');
          const visible = await sidebarElements[i].isVisible();
          console.log(`Sidebar element ${i + 1}: ${tagName} - visible: ${visible} - classes: ${classes}`);
        }
      }
      
    } else {
      console.log('❌ Not on dashboard page');
    }
    
    console.log('\nBrowser will stay open for 20 seconds for manual inspection...');
    await page.waitForTimeout(20000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testDashboardDirect().catch(console.error);