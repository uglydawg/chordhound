const { chromium } = require('playwright');

async function testProfilePageError() {
  console.log('Testing profile page for errors...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // Login first
    console.log('Logging in...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    const testEmail = 'profile.error@example.com';
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
    
    console.log('✅ Logged in successfully');
    
    // Navigate to profile settings
    console.log('Navigating to profile settings...');
    await page.goto('http://localhost:8000/settings/profile', { waitUntil: 'networkidle' });
    
    // Check for any console errors
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log(`❌ Console Error: ${msg.text()}`);
      }
    });
    
    // Check for any JavaScript errors
    page.on('pageerror', error => {
      console.log(`❌ Page Error: ${error.message}`);
    });
    
    // Check page content
    const pageTitle = await page.title();
    console.log(`Page title: ${pageTitle}`);
    
    // Check if there are any visible error messages on the page
    const errorElements = await page.locator('.error, [class*="error"], .text-red, flux\\:alert[variant="error"]').all();
    
    if (errorElements.length > 0) {
      console.log(`Found ${errorElements.length} error elements:`);
      for (let i = 0; i < errorElements.length; i++) {
        const errorText = await errorElements[i].textContent();
        if (errorText && errorText.trim()) {
          console.log(`Error ${i + 1}: "${errorText.trim()}"`);
        }
      }
    } else {
      console.log('No visible error messages found');
    }
    
    // Check if profile form components are present
    const nameInput = page.locator('input[wire\\:model="name"]');
    const usernameInput = page.locator('input[wire\\:model\\.blur="username"]');
    const displayNameInput = page.locator('input[wire\\:model="display_name"]');
    
    const nameVisible = await nameInput.isVisible();
    const usernameVisible = await usernameInput.isVisible();
    const displayNameVisible = await displayNameInput.isVisible();
    
    console.log(`Profile form elements visibility:`);
    console.log(`- Name input: ${nameVisible}`);
    console.log(`- Username input: ${usernameVisible}`);
    console.log(`- Display name input: ${displayNameVisible}`);
    
    // Check for any Flux components that might not be rendering
    const fluxCards = await page.locator('flux\\:card').count();
    const fluxInputs = await page.locator('flux\\:input').count();
    const fluxButtons = await page.locator('flux\\:button').count();
    
    console.log(`Flux components count:`);
    console.log(`- Cards: ${fluxCards}`);
    console.log(`- Inputs: ${fluxInputs}`);
    console.log(`- Buttons: ${fluxButtons}`);
    
    // Check the HTML source for any obvious issues
    const pageContent = await page.content();
    
    if (pageContent.includes('ErrorException') || pageContent.includes('FatalError') || pageContent.includes('ParseError')) {
      console.log('❌ PHP error detected in page source');
      
      // Extract error details
      const errorMatches = pageContent.match(/(ErrorException|FatalError|ParseError).*?<\/div>/s);
      if (errorMatches) {
        console.log(`Error details: ${errorMatches[0].substring(0, 500)}...`);
      }
    }
    
    if (pageContent.includes('Livewire component not found')) {
      console.log('❌ Livewire component error detected');
    }
    
    // Check network tab for failed requests
    const failedRequests = [];
    page.on('response', response => {
      if (!response.ok()) {
        failedRequests.push(`${response.status()} ${response.url()}`);
      }
    });
    
    // Wait a bit to catch any async requests
    await page.waitForTimeout(3000);
    
    if (failedRequests.length > 0) {
      console.log(`Failed HTTP requests:`);
      failedRequests.forEach(req => console.log(`- ${req}`));
    } else {
      console.log('No failed HTTP requests detected');
    }
    
    console.log('\nBrowser will stay open for 15 seconds for manual inspection...');
    await page.waitForTimeout(15000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testProfilePageError().catch(console.error);