const { chromium } = require('playwright');
const { exec } = require('child_process');
const { promisify } = require('util');

const execAsync = promisify(exec);

async function getLatestAuthCode(email) {
  const command = `php artisan tinker --execute="
use App\\Models\\AuthCode;
\\$code = AuthCode::where('email', '${email}')->orderBy('created_at', 'desc')->first();
echo \\$code ? \\$code->code : 'NO_CODE';
"`;
  
  const { stdout } = await execAsync(command);
  return stdout.trim();
}

async function testDynamicAuthCode() {
  console.log('Testing with dynamically retrieved auth code...');
  
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle' });
    
    // Fill email in main form
    const testEmail = 'test.dynamic@example.com';
    console.log(`Filling main email form with: ${testEmail}`);
    await page.fill('input[name="email"]', testEmail);
    
    // Open Auth Code modal
    console.log('\n=== Opening Auth Code Modal ===');
    await page.click('button:has-text("Continue with Auth Code")');
    await page.waitForTimeout(500);
    
    const authCodeModal = page.locator('[data-modal="auth-code"]');
    
    // Verify email pre-filled
    const modalEmail = await authCodeModal.locator('input[type="email"]').inputValue();
    console.log(`Modal email pre-filled: "${modalEmail}"`);
    
    // Send auth code
    console.log('\n=== Sending Auth Code ===');
    await authCodeModal.locator('button:has-text("Send Login Code")').click();
    await page.waitForTimeout(3000); // Wait for email to be sent
    
    // Get the latest auth code from database
    console.log('\n=== Getting Latest Auth Code ===');
    const latestCode = await getLatestAuthCode(testEmail);
    console.log(`Latest code from database: ${latestCode}`);
    
    if (latestCode === 'NO_CODE') {
      console.log('❌ No auth code found in database');
      return;
    }
    
    // Fill the latest auth code
    const codeInput = authCodeModal.locator('input[maxlength="6"]');
    await codeInput.fill(latestCode);
    console.log(`Filled auth code: ${latestCode}`);
    
    // Verify the code
    console.log('\n=== Verifying Auth Code ===');
    await authCodeModal.locator('button:has-text("Verify Code")').click();
    await page.waitForTimeout(3000); // Wait for verification
    
    // Check for error messages in the modal
    const modalErrors = await authCodeModal.locator('.text-red-600, .text-red-500, [class*="error"]').all();
    
    console.log('\n=== Checking Modal Errors ===');
    if (modalErrors.length > 0) {
      console.log(`Found ${modalErrors.length} error elements in modal:`);
      for (let i = 0; i < modalErrors.length; i++) {
        const text = await modalErrors[i].textContent();
        if (text && text.trim()) {
          console.log(`Error ${i + 1}: "${text.trim()}"`);
        }
      }
    } else {
      console.log('No error elements found in modal');
    }
    
    // Check current URL
    const currentUrl = page.url();
    console.log(`\nCurrent URL after verification: ${currentUrl}`);
    
    if (currentUrl.includes('dashboard')) {
      console.log('✅ SUCCESS! Logged in and redirected to dashboard!');
    } else if (currentUrl.includes('phone-verification')) {
      console.log('✅ SUCCESS! Redirected to phone verification (expected flow)!');
    } else {
      console.log('❌ Still on login page');
      
      // Check if modal is still open
      const modalVisible = await authCodeModal.isVisible();
      console.log(`Modal still visible: ${modalVisible}`);
      
      // Check code input value
      const codeValue = await codeInput.inputValue();
      console.log(`Code input value: "${codeValue}"`);
      
      // Check for Livewire errors
      const livewireErrors = await page.locator('[wire\\:target], [x-show*="error"]').all();
      console.log(`Found ${livewireErrors.length} Livewire-related elements`);
    }
    
    console.log('\nBrowser will stay open for 15 seconds for manual inspection...');
    await page.waitForTimeout(15000);
    
  } catch (error) {
    console.error('Error during test:', error);
  } finally {
    await browser.close();
  }
}

testDynamicAuthCode().catch(console.error);