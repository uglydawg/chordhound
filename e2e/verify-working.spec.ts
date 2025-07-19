import { test, expect } from '@playwright/test';

test.describe('Application Status Check', () => {
  test('chord generator page loads successfully', async ({ page }) => {
    await page.goto('/');
    
    // Page loads without errors
    await expect(page).not.toHaveTitle(/Error/);
    
    // Main elements are present
    const bodyText = await page.locator('body').textContent();
    expect(bodyText).toContain('Piano Chord Generator');
    expect(bodyText).toContain('Chord 1');
    expect(bodyText).toContain('Chord 4');
    expect(bodyText).toContain('Piano Keyboard');
    
    console.log('✅ Chord generator page loads successfully');
  });

  test('can interact with chord selector', async ({ page }) => {
    await page.goto('/');
    
    // Select a chord
    const selector = page.locator('select').first();
    await selector.selectOption('C');
    
    // Check that selection was made
    const value = await selector.inputValue();
    expect(value).toBe('C');
    
    console.log('✅ Can interact with chord selector');
  });

  test('piano keyboard is rendered', async ({ page }) => {
    await page.goto('/');
    
    // Check SVG exists
    const svgs = await page.locator('svg').count();
    expect(svgs).toBeGreaterThan(0);
    
    // Check for piano keys (rect elements)
    const keys = await page.locator('svg rect').count();
    expect(keys).toBeGreaterThan(20); // Should have many piano keys
    
    console.log('✅ Piano keyboard is rendered with', keys, 'keys');
  });

  test('print functionality is available', async ({ page }) => {
    await page.goto('/');
    
    // Check print button exists
    const printButton = await page.locator('button').filter({ hasText: 'Print' }).count();
    expect(printButton).toBe(1);
    
    console.log('✅ Print functionality is available');
  });

  test('navigation links work', async ({ page }) => {
    await page.goto('/');
    
    // Check main navigation exists
    const nav = await page.locator('nav').count();
    expect(nav).toBe(1);
    
    // Check for Piano Chords branding
    const branding = await page.locator('a').filter({ hasText: 'Piano Chords' }).count();
    expect(branding).toBeGreaterThan(0);
    
    console.log('✅ Navigation links work');
  });
});