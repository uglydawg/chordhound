const { chromium } = require('playwright');

async function testUsernameFunctionality() {
  console.log('Testing username functionality...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // Test 1: Registration with auto-generated username
    console.log('\n=== Test 1: Registration with auto-generated username ===');
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle' });
    
    const testEmail = 'test.username@example.com';
    await page.fill('input[wire\\:model="email"]', testEmail);
    await page.click('button:has-text("Continue")');
    await page.waitForTimeout(2000);
    
    // Check if username is auto-generated from email
    const usernameValue = await page.inputValue('input[wire\\:model\\.blur="username"]');
    console.log(`Auto-generated username: "${usernameValue}"`);
    
    if (usernameValue) {
      console.log('✅ Username auto-generation works');
    } else {
      console.log('❌ Username auto-generation failed');
    }
    
    // Test 2: Login and check profile settings
    console.log('\n=== Test 2: Login with existing user and test profile settings ===');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    const existingEmail = 'profile.test@example.com';
    await page.fill('input[name="email"]', existingEmail);
    
    await page.click('button:has-text("Continue with Auth Code")');
    await page.waitForTimeout(500);
    
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(2000);
    
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    await codeInput.fill('555121');
    await authCodeModal.locator('button:has-text("Verify Code")').click();
    await page.waitForTimeout(3000);
    
    // Navigate to profile settings
    console.log('\n=== Test 3: Testing profile settings page ===');
    await page.goto('http://localhost:8000/settings/profile', { waitUntil: 'networkidle' });
    
    // Check if profile form loads
    const nameInput = page.locator('input[wire\\:model="name"]');
    const usernameInput = page.locator('input[wire\\:model\\.blur="username"]');
    const displayNameInput = page.locator('input[wire\\:model="display_name"]');
    
    const formLoaded = await nameInput.isVisible() && await usernameInput.isVisible();
    console.log(`Profile form loaded: ${formLoaded}`);
    
    if (formLoaded) {
      // Get current values
      const currentName = await nameInput.inputValue();
      const currentUsername = await usernameInput.inputValue();
      const currentDisplayName = await displayNameInput.inputValue();
      
      console.log(`Current name: "${currentName}"`);
      console.log(`Current username: "${currentUsername}"`);
      console.log(`Current display name: "${currentDisplayName}"`);
      
      // Test 4: Test username conflict detection
      console.log('\n=== Test 4: Testing username conflict detection ===');
      
      // Try to set username to a conflicting one
      await usernameInput.fill('test');
      await usernameInput.blur();
      await page.waitForTimeout(2000);
      
      // Check for suggestions
      const suggestionButtons = page.locator('button:has-text("test")');
      const suggestionCount = await suggestionButtons.count();
      
      if (suggestionCount > 0) {
        console.log(`✅ Username suggestions working: ${suggestionCount} suggestions found`);
        
        // Click first suggestion
        await suggestionButtons.first().click();
        await page.waitForTimeout(1000);
        
        const newUsernameValue = await usernameInput.inputValue();
        console.log(`Selected suggested username: "${newUsernameValue}"`);
      } else {
        console.log('⚠️ No username suggestions found (might be unique)');
      }
      
      // Test 5: Update profile
      console.log('\n=== Test 5: Testing profile update ===');
      
      await nameInput.fill('Test User Updated');
      await displayNameInput.fill('Test Display Name');
      
      await page.click('button:has-text("Save Changes")');
      await page.waitForTimeout(3000);
      
      // Check for success message
      const successAlert = page.locator('flux\\:alert, .alert-success, .text-green');
      const successVisible = await successAlert.isVisible();
      
      if (successVisible) {
        const successText = await successAlert.textContent();
        console.log(`✅ Profile update success: "${successText?.trim()}"`);
      } else {
        console.log('❌ No success message found');
      }
      
      // Test 6: Test email change form
      console.log('\n=== Test 6: Testing email change functionality ===');
      
      const changeEmailButton = page.locator('button:has-text("Change Email")');
      const emailChangeVisible = await changeEmailButton.isVisible();
      
      if (emailChangeVisible) {
        await changeEmailButton.click();
        await page.waitForTimeout(1000);
        
        const newEmailInput = page.locator('input[wire\\:model="new_email"]');
        const passwordInput = page.locator('input[wire\\:model="password"]');
        
        const emailFormVisible = await newEmailInput.isVisible() && await passwordInput.isVisible();
        console.log(`Email change form visible: ${emailFormVisible}`);
        
        if (emailFormVisible) {
          // Cancel to test cancellation
          await page.click('button:has-text("Cancel")');
          await page.waitForTimeout(1000);
          
          const formHidden = await newEmailInput.isHidden();
          console.log(`✅ Email change form cancellation works: ${formHidden}`);
        }
      }
    }
    
    console.log('\n=== Username functionality tests completed ===');
    console.log('Browser will stay open for 10 seconds for manual inspection...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testUsernameFunctionality().catch(console.error);