import { test, expect } from '@playwright/test';

test.describe('Chord Sets (Authenticated)', () => {
  test.use({ storageState: 'auth.json' }); // Assume authenticated state
  
  test.skip('can save chord set', async ({ page }) => {
    // This test is skipped because it requires authentication
    // In a real scenario, you'd set up authentication state first
    
    await page.goto('/');
    
    // Select some chords
    await page.locator('select[wire\\:model*="chords.1.tone"]').selectOption('C');
    await page.locator('select[wire\\:model*="chords.2.tone"]').selectOption('G');
    
    // Click save button
    await page.click('button:has-text("Save Chord Set")');
    
    // Modal should open
    const modal = page.locator('[data-flux-modal="save-chord-set"]');
    await expect(modal).toBeVisible();
    
    // Fill in details
    await page.fill('#chord-set-name', 'My Test Chords');
    await page.fill('#chord-set-description', 'Test description');
    
    // Save
    await page.click('button[type="submit"]:has-text("Save")');
    
    // Should see success message
    await expect(page.locator('text=Chord set saved successfully')).toBeVisible();
  });

  test.skip('can view my chord sets', async ({ page }) => {
    // This test is skipped because it requires authentication
    
    await page.goto('/my-chord-sets');
    
    // Should see page title
    await expect(page.locator('text=My Chord Sets')).toBeVisible();
    
    // Should see new chord set button
    await expect(page.locator('a:has-text("New Chord Set")')).toBeVisible();
  });
});