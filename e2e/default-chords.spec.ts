import { test, expect } from '@playwright/test';

test.describe('Default Chords', () => {
  test('displays G Em C D as default chords', async ({ page }) => {
    await page.goto('/');
    
    // Wait for page to load
    await page.waitForTimeout(1000);
    
    // Check that only 4 chord cards are visible
    const chordCards = await page.locator('.bg-white.border.rounded-lg').count();
    expect(chordCards).toBe(5); // 4 chord cards + 1 piano display card
    
    // Check each chord card individually
    const cards = page.locator('.bg-white.border.rounded-lg');
    
    // Chord 1 should be G
    const chord1Select = cards.nth(0).locator('select').first();
    const chord1Value = await chord1Select.inputValue();
    expect(chord1Value).toBe('G');
    
    // Chord 2 should be E (Em)
    const chord2Select = cards.nth(1).locator('select').first();
    const chord2Value = await chord2Select.inputValue();
    expect(chord2Value).toBe('E');
    
    // Chord 3 should be C
    const chord3Select = cards.nth(2).locator('select').first();
    const chord3Value = await chord3Select.inputValue();
    expect(chord3Value).toBe('C');
    
    // Chord 4 should be D
    const chord4Select = cards.nth(3).locator('select').first();
    const chord4Value = await chord4Select.inputValue();
    expect(chord4Value).toBe('D');
    
    console.log('✅ Default chords G Em C D are set correctly');
  });

  test('G chord starts in first inversion', async ({ page }) => {
    await page.goto('/');
    
    // Wait for page to load
    await page.waitForTimeout(1000);
    
    // Get the first chord's inversion select
    const firstChordCard = page.locator('.bg-white.border.rounded-lg').first();
    const inversionSelect = firstChordCard.locator('select[wire\\:model\\.live*="inversion"]');
    
    // Check that G is in first inversion
    const inversionValue = await inversionSelect.inputValue();
    expect(inversionValue).toBe('first');
    
    console.log('✅ G chord is in first inversion');
  });

  test('Em chord is set to minor', async ({ page }) => {
    await page.goto('/');
    
    // Wait for page to load
    await page.waitForTimeout(1000);
    
    // Get the second chord's semitone select
    const secondChordCard = page.locator('.bg-white.border.rounded-lg').nth(1);
    const semitoneSelect = secondChordCard.locator('select[wire\\:model\\.live*="semitone"]');
    
    // Check that E is minor
    const semitoneValue = await semitoneSelect.inputValue();
    expect(semitoneValue).toBe('minor');
    
    console.log('✅ Em chord is set to minor');
  });
});