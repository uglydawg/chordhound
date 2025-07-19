import { test, expect } from '@playwright/test';

test.describe('Piano Chord Application', () => {
  test('homepage loads with chord selector', async ({ page }) => {
    await page.goto('/');
    
    // Check page title and header
    await expect(page).toHaveTitle(/Piano Chords|Laravel/);
    await expect(page.locator('h1').filter({ hasText: 'Piano Chord Generator' })).toBeVisible();
    
    // Check chord selectors are present
    await expect(page.locator('h3').filter({ hasText: 'Chord 1' })).toBeVisible();
    await expect(page.locator('h3').filter({ hasText: 'Chord 4' })).toBeVisible();
    
    // Check piano keyboard is present
    await expect(page.locator('h2').filter({ hasText: 'Piano Keyboard' })).toBeVisible();
    await expect(page.locator('svg')).toBeVisible();
  });

  test('can select chords', async ({ page }) => {
    await page.goto('/');
    
    // Select C major for first chord
    const firstChordSelect = page.locator('select[wire\\:model*="chords.1.tone"]');
    await firstChordSelect.selectOption('C');
    
    // Should show additional options
    await expect(page.locator('select[wire\\:model*="chords.1.semitone"]')).toBeVisible();
    await expect(page.locator('select[wire\\:model*="chords.1.inversion"]')).toBeVisible();
    
    // Clear button should be visible
    await expect(page.locator('button:has-text("Clear")').first()).toBeVisible();
  });

  test('navigation works', async ({ page }) => {
    await page.goto('/');
    
    // Check navigation links
    await expect(page.locator('a').filter({ hasText: 'Chords' })).toBeVisible();
    await expect(page.locator('a').filter({ hasText: 'Login' })).toBeVisible();
    
    // Click on Chords link
    await page.click('a[href*="/chords"]');
    await expect(page).toHaveURL(/\/chords/);
  });

  test('print button is present', async ({ page }) => {
    await page.goto('/');
    
    // Check print button
    const printButton = page.locator('button').filter({ hasText: 'Print Chord Sheet' });
    await expect(printButton).toBeVisible();
  });

  test('piano keyboard displays', async ({ page }) => {
    await page.goto('/');
    
    // Check SVG keyboard is rendered
    const svg = page.locator('svg').first();
    await expect(svg).toBeVisible();
    
    // Check for piano key elements
    const rects = page.locator('svg rect');
    const rectCount = await rects.count();
    expect(rectCount).toBeGreaterThan(20); // Should have many keys
  });

  test('login page is accessible', async ({ page }) => {
    await page.goto('/');
    
    // Click login link
    await page.click('a[href*="/login"]');
    
    // Should redirect to login page
    await expect(page).toHaveURL(/\/login/);
    
    // Check for login form elements
    await expect(page.locator('input[type="email"]')).toBeVisible();
    await expect(page.locator('input[type="password"]')).toBeVisible();
  });
});