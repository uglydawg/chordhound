import { test, expect } from '@playwright/test';

test('application loads successfully', async ({ page }) => {
  // Update the base URL to use port 8080
  await page.goto('http://localhost:8080/');
  
  // Check that the page loads without errors
  await expect(page).not.toHaveTitle(/Error/);
  
  // Should see some chord elements
  const content = await page.content();
  expect(content).toContain('Chord');
});