import { test, expect } from '@playwright/test';

test.describe('Blue Notes Detection', () => {
  test('detects blue notes in chord combinations', async ({ page }) => {
    await page.goto('/');
    
    // Select chords that would create blue notes
    // C major and C# major would create dissonance
    await page.locator('select[wire\\:model*="chords.1.tone"]').selectOption('C');
    await page.locator('select[wire\\:model*="chords.2.tone"]').selectOption('C#');
    
    // Wait for calculation
    await page.waitForTimeout(500);
    
    // Should show blue note badge
    const blueNoteBadge = page.locator('text=Blue Note');
    await expect(blueNoteBadge.first()).toBeVisible();
    
    // Piano should show blue highlighted keys
    const blueKeys = page.locator('rect[fill="#3B82F6"]'); // Blue color for blue notes
    const count = await blueKeys.count();
    expect(count).toBeGreaterThan(0);
  });

  test('shows blue note visual indicator on chord card', async ({ page }) => {
    await page.goto('/');
    
    // Select dissonant chords
    await page.locator('select[wire\\:model*="chords.1.tone"]').selectOption('C');
    await page.locator('select[wire\\:model*="chords.1.semitone"]').selectOption('major');
    
    await page.locator('select[wire\\:model*="chords.2.tone"]').selectOption('F#');
    await page.locator('select[wire\\:model*="chords.2.semitone"]').selectOption('diminished');
    
    // Wait for calculation
    await page.waitForTimeout(500);
    
    // Check for blue ring on chord card
    const blueRingCard = page.locator('.ring-blue-500');
    const count = await blueRingCard.count();
    expect(count).toBeGreaterThan(0);
  });
});