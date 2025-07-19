import { test, expect } from '@playwright/test';

test.describe('Chord Generator', () => {
  test('can access chord generator', async ({ page }) => {
    await page.goto('/');
    
    // Should see chord selector
    await expect(page.locator('text=Chord 1')).toBeVisible();
    await expect(page.locator('text=Chord 8')).toBeVisible();
    
    // Should see piano keyboard
    await expect(page.locator('text=Piano Keyboard')).toBeVisible();
  });

  test('can select chords', async ({ page }) => {
    await page.goto('/');
    
    // Select first chord
    const firstChordTone = page.locator('select[wire\\:model*="chords.1.tone"]');
    await firstChordTone.selectOption('C');
    
    // Should update chord display
    await expect(firstChordTone).toHaveValue('C');
    
    // Should show semitone and inversion options
    await expect(page.locator('select[wire\\:model*="chords.1.semitone"]')).toBeVisible();
    await expect(page.locator('select[wire\\:model*="chords.1.inversion"]')).toBeVisible();
  });

  test('can clear chord', async ({ page }) => {
    await page.goto('/');
    
    // Select a chord first
    const firstChordTone = page.locator('select[wire\\:model*="chords.1.tone"]');
    await firstChordTone.selectOption('C');
    
    // Clear the chord
    await page.click('button:has-text("Clear"):near(select[wire\\:model*="chords.1.tone"])');
    
    // Chord should be cleared
    await expect(firstChordTone).toHaveValue('');
  });

  test('print button is visible', async ({ page }) => {
    await page.goto('/');
    
    const printButton = page.locator('button:has-text("Print Chord Sheet")');
    await expect(printButton).toBeVisible();
  });

  test('navigation to chord page works', async ({ page }) => {
    await page.goto('/');
    
    // Click on Piano Chords in navigation
    await page.click('a:has-text("Piano Chords")');
    
    // Should stay on chord page
    await expect(page).toHaveURL(/\/chords|\/$/);
  });
});