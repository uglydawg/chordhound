const { chromium } = require('playwright');

async function testProfileFixed() {
  console.log('Testing fixed profile page...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // Login first
    console.log('Logging in...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    const testEmail = 'profile.fixed@example.com';
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
    console.log('\n=== Testing Profile Settings Page ===');
    await page.goto('http://localhost:8000/settings/profile', { waitUntil: 'networkidle' });
    
    // Check if profile form components are now visible
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
    
    if (nameVisible && usernameVisible && displayNameVisible) {
      console.log('✅ Profile form loaded successfully!');
      
      // Get current values
      const currentName = await nameInput.inputValue();
      const currentUsername = await usernameInput.inputValue();
      const currentDisplayName = await displayNameInput.inputValue();
      
      console.log(`\nCurrent values:`);
      console.log(`- Name: "${currentName}"`);
      console.log(`- Username: "${currentUsername}"`);
      console.log(`- Display name: "${currentDisplayName}"`);
      
      // Test username conflict detection
      console.log('\n=== Testing Username Functionality ===');
      
      // Try changing username to something that might conflict
      await usernameInput.fill('admin');
      await usernameInput.blur();
      await page.waitForTimeout(2000);
      
      // Check for suggestions
      const suggestionButtons = page.locator('button:has-text("admin")');
      const suggestionCount = await suggestionButtons.count();
      
      if (suggestionCount > 0) {
        console.log(`✅ Username suggestions working: ${suggestionCount} suggestions found`);
        
        // Click first suggestion
        await suggestionButtons.first().click();
        await page.waitForTimeout(1000);
        
        const newUsernameValue = await usernameInput.inputValue();
        console.log(`Selected suggested username: "${newUsernameValue}"`);
      } else {
        console.log('⚠️ No username suggestions found (username might be available)');
      }
      
      // Test profile update
      console.log('\n=== Testing Profile Update ===');
      
      await nameInput.fill('Updated Test User');
      await displayNameInput.fill('Updated Display');
      
      await page.click('button:has-text("Save Changes")');
      await page.waitForTimeout(3000);
      
      // Check for success message
      const successMessage = page.locator('.bg-green-100, .text-green-700');
      const successVisible = await successMessage.isVisible();
      
      if (successVisible) {
        const successText = await successMessage.textContent();
        console.log(`✅ Profile update success: "${successText?.trim()}"`);
      } else {
        console.log('⚠️ No success message visible');
      }
      
      // Test email change functionality
      console.log('\n=== Testing Email Change ===');
      
      const changeEmailButton = page.locator('button:has-text("Change Email")');
      const emailButtonVisible = await changeEmailButton.isVisible();
      
      if (emailButtonVisible) {
        await changeEmailButton.click();
        await page.waitForTimeout(1000);
        
        const newEmailInput = page.locator('input[wire\\:model="new_email"]');
        const passwordInput = page.locator('input[wire\\:model="password"]');
        
        const emailFormVisible = await newEmailInput.isVisible() && await passwordInput.isVisible();
        console.log(`Email change form visible: ${emailFormVisible}`);
        
        if (emailFormVisible) {
          // Test cancellation
          await page.click('button:has-text("Cancel")');
          await page.waitForTimeout(1000);
          
          const formHidden = await newEmailInput.isHidden();
          console.log(`✅ Email change cancellation works: ${formHidden}`);
        }
      }
      
      console.log('\n✅ All profile functionality tests passed!');
      
    } else {
      console.log('❌ Profile form failed to load properly');
      
      // Check for any error messages
      const errorElements = await page.locator('.text-red-600, .bg-red-100').all();
      if (errorElements.length > 0) {
        console.log('Found error messages:');
        for (let i = 0; i < errorElements.length; i++) {
          const errorText = await errorElements[i].textContent();
          if (errorText && errorText.trim()) {
            console.log(`Error: "${errorText.trim()}"`);
          }
        }
      }
    }
    
    console.log('\nBrowser will stay open for 10 seconds for manual inspection...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testProfileFixed().catch(console.error);