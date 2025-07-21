const { chromium } = require('playwright');

async function testSidebarVisibility() {
  console.log('Testing sidebar visibility on Piano Chords page...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // First login with fixed code
    console.log('Logging in...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    const testEmail = 'test.sidebar@example.com';
    await page.fill('input[name="email"]', testEmail);
    
    // Open Auth Code modal
    await page.click('button:has-text("Continue with Auth Code")');
    await page.waitForTimeout(500);
    
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(2000);
    
    // Use fixed code
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    await codeInput.fill('555121');
    await authCodeModal.locator('button:has-text("Verify Code")').click();
    await page.waitForTimeout(3000);
    
    console.log('✅ Logged in successfully');
    
    // Navigate to Piano Chords page
    console.log('\n=== Testing Dashboard (should have sidebar) ===');
    await page.goto('http://localhost:8000/dashboard', { waitUntil: 'networkidle' });
    
    const dashboardSidebar = page.locator('flux\\:sidebar, [class*="sidebar"]');
    const dashboardSidebarVisible = await dashboardSidebar.isVisible();
    console.log(`Dashboard sidebar visible: ${dashboardSidebarVisible}`);
    
    if (dashboardSidebarVisible) {
      const pianoChordLink = page.locator('text=Piano Chords');
      const linkExists = await pianoChordLink.count() > 0;
      console.log(`Piano Chords link in sidebar: ${linkExists}`);
    }
    
    // Navigate to Piano Chords page
    console.log('\n=== Testing Piano Chords Page (should have sidebar) ===');
    await page.goto('http://localhost:8000/chords', { waitUntil: 'networkidle' });
    
    const chordsSidebar = page.locator('flux\\:sidebar, [class*="sidebar"]');
    const chordsSidebarVisible = await chordsSidebar.isVisible();
    console.log(`Piano Chords page sidebar visible: ${chordsSidebarVisible}`);
    
    if (chordsSidebarVisible) {
      console.log('✅ SUCCESS! Sidebar is now visible on Piano Chords page');
      
      // Check if navigation items are present
      const dashboardLink = page.locator('text=Dashboard');
      const myChordSetsLink = page.locator('text=My Chord Sets');
      
      const dashboardLinkExists = await dashboardLink.count() > 0;
      const myChordSetsLinkExists = await myChordSetsLink.count() > 0;
      
      console.log(`Dashboard link in sidebar: ${dashboardLinkExists}`);
      console.log(`My Chord Sets link in sidebar: ${myChordSetsLinkExists}`);
      
      // Test navigation back to dashboard
      if (dashboardLinkExists) {
        console.log('\n=== Testing sidebar navigation ===');
        await dashboardLink.click();
        await page.waitForTimeout(2000);
        
        const currentUrl = page.url();
        if (currentUrl.includes('dashboard')) {
          console.log('✅ Sidebar navigation to Dashboard works');
        }
      }
    } else {
      console.log('❌ FAILED! Sidebar is still not visible on Piano Chords page');
      
      // Check what layout elements are present
      const layoutElements = await page.locator('main, nav, header, aside, [class*="layout"]').all();
      console.log(`Found ${layoutElements.length} layout elements`);
      
      for (let i = 0; i < layoutElements.length; i++) {
        const tagName = await layoutElements[i].evaluate(el => el.tagName);
        const classes = await layoutElements[i].getAttribute('class');
        console.log(`Element ${i + 1}: ${tagName} - classes: ${classes}`);
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

testSidebarVisibility().catch(console.error);