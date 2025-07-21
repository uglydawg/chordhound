const { chromium } = require('playwright');

async function testChordsPageSidebar() {
  console.log('Testing Piano Chords page sidebar...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // Login first
    console.log('Logging in...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    const testEmail = 'test.chords@example.com';
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
    
    console.log('✅ Logged in, navigating to Piano Chords page...');
    
    // Navigate to Piano Chords page
    await page.goto('http://localhost:8000/chords', { waitUntil: 'networkidle' });
    
    const currentUrl = page.url();
    console.log(`Current URL: ${currentUrl}`);
    
    // Check page title
    const title = await page.title();
    console.log(`Page title: ${title}`);
    
    // Look for sidebar elements
    const sidebarElements = await page.locator('aside, [role="navigation"], nav, [class*="sidebar"]').all();
    console.log(`Found ${sidebarElements.length} potential sidebar elements`);
    
    let sidebarFound = false;
    
    if (sidebarElements.length > 0) {
      for (let i = 0; i < sidebarElements.length; i++) {
        const tagName = await sidebarElements[i].evaluate(el => el.tagName);
        const classes = await sidebarElements[i].getAttribute('class');
        const visible = await sidebarElements[i].isVisible();
        console.log(`Sidebar element ${i + 1}: ${tagName} - visible: ${visible} - classes: ${classes?.substring(0, 100)}...`);
        
        if (visible && classes && classes.includes('bg-zinc-50')) {
          sidebarFound = true;
        }
      }
    }
    
    if (sidebarFound) {
      console.log('✅ SUCCESS! Sidebar is now visible on Piano Chords page');
      
      // Check if Piano Chords link is highlighted
      const pianoChordLink = page.locator('text=Piano Chords');
      const linkExists = await pianoChordLink.count() > 0;
      console.log(`Piano Chords link in sidebar: ${linkExists}`);
      
      if (linkExists) {
        const linkClasses = await pianoChordLink.getAttribute('class');
        console.log(`Piano Chords link classes: ${linkClasses}`);
      }
      
      // Test navigation to Dashboard
      console.log('\n=== Testing navigation to Dashboard ===');
      const dashboardLink = page.locator('text=Dashboard');
      const dashboardLinkExists = await dashboardLink.count() > 0;
      
      if (dashboardLinkExists) {
        await dashboardLink.click();
        await page.waitForTimeout(3000);
        
        const newUrl = page.url();
        console.log(`After clicking Dashboard: ${newUrl}`);
        
        if (newUrl.includes('dashboard')) {
          console.log('✅ Navigation to Dashboard works');
          
          // Navigate back to Piano Chords
          console.log('\n=== Testing navigation back to Piano Chords ===');
          const pianoChordLinkOnDashboard = page.locator('text=Piano Chords');
          await pianoChordLinkOnDashboard.click();
          await page.waitForTimeout(3000);
          
          const backToChords = page.url();
          console.log(`After clicking Piano Chords: ${backToChords}`);
          
          if (backToChords.includes('chords')) {
            console.log('✅ Navigation back to Piano Chords works');
          }
        }
      }
      
    } else {
      console.log('❌ Sidebar is still not visible on Piano Chords page');
      
      // Check for any errors on the page
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
    
    console.log('\nBrowser will stay open for 20 seconds for manual inspection...');
    await page.waitForTimeout(20000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testChordsPageSidebar().catch(console.error);