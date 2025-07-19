import { test, expect } from '@playwright/test';

test('debug page content', async ({ page }) => {
  await page.goto('http://localhost:8080/');
  
  // Take screenshot for debugging
  await page.screenshot({ path: 'debug-homepage.png' });
  
  // Get page content
  const content = await page.content();
  console.log('Page title:', await page.title());
  
  // Check for any errors
  const bodyText = await page.locator('body').innerText();
  console.log('Body contains:', bodyText.substring(0, 200));
  
  // Check if Livewire is loaded
  const hasLivewire = await page.evaluate(() => {
    return typeof window.Livewire !== 'undefined';
  });
  console.log('Livewire loaded:', hasLivewire);
  
  // Basic assertion
  await expect(page).not.toHaveTitle(/Error/);
});