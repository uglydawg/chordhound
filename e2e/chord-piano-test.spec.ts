import { test, expect } from '@playwright/test';

test.describe('Individual Chord Piano Display', () => {
  test('displays piano visualization under each chord when selected', async ({ page }) => {
    await page.goto('/');
    
    // Select C major for first chord
    const firstChordSelect = page.locator('select').first();
    await firstChordSelect.selectOption('C');
    
    // Wait for Livewire to update and the chord piano to render
    await page.waitForTimeout(2000);
    
    // Check that a mini piano appeared under the first chord
    // Look for the chord card which has the border and rounded-lg classes
    const firstChordCard = page.locator('.bg-white.border.rounded-lg').first();
    const svgInFirstCard = await firstChordCard.locator('svg').count();
    
    // Should have at least one SVG (the chord piano)
    expect(svgInFirstCard).toBeGreaterThan(0);
    
    // Check that the SVG has piano keys (rect elements)
    const pianoKeys = await firstChordCard.locator('svg rect').count();
    expect(pianoKeys).toBeGreaterThan(10); // Should have white and black keys
    
    console.log('✅ Individual chord piano is displayed with', pianoKeys, 'keys');
  });

  test('chord pianos show active notes in green', async ({ page }) => {
    await page.goto('/');
    
    // Select C major
    await page.locator('select').first().selectOption('C');
    await page.waitForTimeout(2000);
    
    // Check for green-filled rectangles (active notes)
    const greenKeys = await page.locator('.bg-white.border.rounded-lg').first().locator('svg rect[fill="#10B981"]').count();
    expect(greenKeys).toBeGreaterThan(0); // C major should have active notes
    
    console.log('✅ Active notes are highlighted in chord piano');
  });

  test('chord pianos update when chord type changes', async ({ page }) => {
    await page.goto('/');
    
    // Select C major first
    const firstCard = page.locator('.bg-white.border.rounded-lg').first();
    await firstCard.locator('select').first().selectOption('C');
    await page.waitForTimeout(2000);
    
    // Change semitone to minor
    const semitoneSelect = firstCard.locator('select').nth(1);
    await semitoneSelect.selectOption('minor');
    await page.waitForTimeout(2000);
    
    // Piano should still be visible
    const svgCount = await firstCard.locator('svg').count();
    expect(svgCount).toBeGreaterThan(0);
    
    console.log('✅ Chord piano updates when chord type changes');
  });

  test('displays four chord selectors per row on large screens', async ({ page }) => {
    // Set viewport to large screen
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto('/');
    
    // Check grid layout class
    const grid = page.locator('.grid');
    const gridClasses = await grid.getAttribute('class');
    expect(gridClasses).toContain('lg:grid-cols-4');
    
    console.log('✅ Grid displays 4 columns on large screens');
  });
});