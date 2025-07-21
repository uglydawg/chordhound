const { chromium } = require('playwright');

async function testMyChordSetsSidebar() {
  console.log('Testing My Chord Sets page sidebar...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // Login first
    console.log('Logging in...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    const testEmail = 'test.mysets@example.com';
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
    
    console.log('✅ Logged in, navigating to My Chord Sets page...');
    
    // Navigate to My Chord Sets page
    await page.goto('http://localhost:8000/my-chord-sets', { waitUntil: 'networkidle' });
    
    const currentUrl = page.url();
    console.log(`Current URL: ${currentUrl}`);
    
    // Check page title
    const title = await page.title();
    console.log(`Page title: ${title}`);
    
    // Look for sidebar elements
    const sidebarElements = await page.locator('[class*="bg-zinc-50"], [class*="sidebar"]').all();
    console.log(`Found ${sidebarElements.length} potential sidebar elements`);
    
    let sidebarFound = false;
    
    if (sidebarElements.length > 0) {
      for (let i = 0; i < sidebarElements.length; i++) {
        const visible = await sidebarElements[i].isVisible();
        const classes = await sidebarElements[i].getAttribute('class');
        
        if (visible && classes && classes.includes('bg-zinc-50')) {
          console.log(`✅ Sidebar element ${i + 1}: visible: ${visible}`);
          sidebarFound = true;
          break;
        }
      }
    }
    
    if (sidebarFound) {
      console.log('✅ SUCCESS! Sidebar is visible on My Chord Sets page');
      
      // Check if My Chord Sets link is highlighted
      const myChordSetsLink = page.locator('text=My Chord Sets');
      const linkExists = await myChordSetsLink.count() > 0;
      console.log(`My Chord Sets link in sidebar: ${linkExists}`);
      
      // Check if all navigation links are present
      const dashboardLink = page.locator('text=Dashboard');
      const pianoChordLink = page.locator('text=Piano Chords');
      
      const dashboardExists = await dashboardLink.count() > 0;
      const pianoChordExists = await pianoChordLink.count() > 0;
      
      console.log(`Dashboard link in sidebar: ${dashboardExists}`);
      console.log(`Piano Chords link in sidebar: ${pianoChordExists}`);
      
      // Test navigation to Piano Chords
      if (pianoChordExists) {
        console.log('\n=== Testing navigation to Piano Chords ===');
        await pianoChordLink.click();
        await page.waitForTimeout(3000);
        
        const newUrl = page.url();
        console.log(`After clicking Piano Chords: ${newUrl}`);
        
        if (newUrl.includes('chords')) {
          console.log('✅ Navigation to Piano Chords works');
          
          // Navigate back to My Chord Sets using sidebar
          console.log('\n=== Testing navigation back to My Chord Sets ===');
          const myChordSetsLinkOnChords = page.locator('text=My Chord Sets');
          await myChordSetsLinkOnChords.click();
          await page.waitForTimeout(3000);
          
          const backToMySets = page.url();
          console.log(`After clicking My Chord Sets: ${backToMySets}`);
          
          if (backToMySets.includes('my-chord-sets')) {
            console.log('✅ Navigation back to My Chord Sets works');
          }
        }
      }
      
    } else {
      console.log('❌ Sidebar is not visible on My Chord Sets page');
      
      // Check for any errors
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
    }
    
    console.log('\nBrowser will stay open for 15 seconds for manual inspection...');
    await page.waitForTimeout(15000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testMyChordSetsSidebar().catch(console.error);