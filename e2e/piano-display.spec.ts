import { test, expect } from '@playwright/test';

test.describe('Piano Display', () => {
  test('shows piano keyboard', async ({ page }) => {
    await page.goto('/');
    
    // Check for piano keyboard SVG
    const pianoSvg = page.locator('svg').first();
    await expect(pianoSvg).toBeVisible();
    
    // Check for key labels
    await expect(page.locator('text=C3')).toBeVisible();
    await expect(page.locator('text=Active Notes')).toBeVisible();
    await expect(page.locator('text=Blue Notes')).toBeVisible();
  });

  test('highlights keys when chord is selected', async ({ page }) => {
    await page.goto('/');
    
    // Select C major chord
    await page.locator('select[wire\\:model*="chords.1.tone"]').selectOption('C');
    
    // Wait for piano to update
    await page.waitForTimeout(500);
    
    // Check that some keys are highlighted (have fill color)
    const highlightedKeys = page.locator('rect[fill="#10B981"]'); // Green color for active notes
    await expect(highlightedKeys).toHaveCount(3); // C major has 3 notes
  });

  test('shows chord position numbers on active keys', async ({ page }) => {
    await page.goto('/');
    
    // Select multiple chords
    await page.locator('select[wire\\:model*="chords.1.tone"]').selectOption('C');
    await page.locator('select[wire\\:model*="chords.2.tone"]').selectOption('G');
    
    // Wait for piano to update
    await page.waitForTimeout(500);
    
    // Should see position numbers on keys
    await expect(page.locator('svg text:has-text("1")')).toBeVisible();
    await expect(page.locator('svg text:has-text("2")')).toBeVisible();
  });
});