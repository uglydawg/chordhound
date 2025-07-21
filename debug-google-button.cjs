const { chromium } = require('playwright');

async function debugGoogleButton() {
  console.log('Debugging Google button visibility...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    // Search for all buttons and links containing "Google"
    const googleElements = await page.$$('*:has-text("Google")');
    console.log(`Found ${googleElements.length} elements containing "Google"`);
    
    for (let i = 0; i < googleElements.length; i++) {
      const element = googleElements[i];
      const tagName = await element.evaluate(el => el.tagName);
      const textContent = await element.evaluate(el => el.textContent.trim());
      const isVisible = await element.isVisible();
      const className = await element.evaluate(el => el.className);
      
      console.log(`Element ${i + 1}:`);
      console.log(`  Tag: ${tagName}`);
      console.log(`  Text: "${textContent}"`);
      console.log(`  Visible: ${isVisible}`);
      console.log(`  Class: "${className}"`);
      console.log('---');
    }
    
    // Also check for elements with route('auth.google')
    const allButtons = await page.$$('button, a');
    for (let i = 0; i < allButtons.length; i++) {
      const button = allButtons[i];
      const href = await button.getAttribute('href');
      const textContent = await button.evaluate(el => el.textContent.trim());
      
      if (href && href.includes('google')) {
        const isVisible = await button.isVisible();
        console.log(`Found Google link: "${textContent}" (visible: ${isVisible})`);
      }
    }
    
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error:', error);
  } finally {
    await browser.close();
  }
}

debugGoogleButton().catch(console.error);