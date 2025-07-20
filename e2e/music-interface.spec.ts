import { test, expect } from '@playwright/test';

test.describe('Music Production Interface', () => {
  test('displays new dark theme interface', async ({ page }) => {
    await page.goto('/');
    
    // Check for dark background
    const body = page.locator('body');
    const bgColor = await body.evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(bgColor).toBe('rgb(0, 0, 0)'); // black background
    
    // Check for Chord Studio branding
    await expect(page.locator('text=Chord Studio')).toBeVisible();
    
    // Check for MIDI player
    await expect(page.locator('.midi-player')).toBeVisible();
    await expect(page.locator('text=BPM')).toBeVisible();
    
    console.log('✅ Dark theme interface is displayed');
  });

  test('displays chord grid editor', async ({ page }) => {
    await page.goto('/');
    
    // Check for chord grid
    await expect(page.locator('.timeline-grid')).toBeVisible();
    await expect(page.locator('text=Chord Progression')).toBeVisible();
    
    // Check for 4 chord blocks
    const chordBlocks = await page.locator('.chord-block').count();
    expect(chordBlocks).toBe(4);
    
    // Check default chords are displayed in chord blocks
    await expect(page.locator('.chord-block').first().locator('.text-2xl')).toContainText('G');
    await expect(page.locator('.chord-block').nth(1).locator('.text-2xl')).toContainText('Em');
    await expect(page.locator('.chord-block').nth(2).locator('.text-2xl')).toContainText('C');
    await expect(page.locator('.chord-block').nth(3).locator('.text-2xl')).toContainText('D');
    
    console.log('✅ Chord grid editor is displayed with default chords');
  });

  test('can interact with chord blocks', async ({ page }) => {
    await page.goto('/');
    await page.waitForTimeout(1000);
    
    // Click on second chord block
    const secondBlock = page.locator('.chord-block').nth(1);
    await secondBlock.click();
    
    // Should have active class
    await expect(secondBlock).toHaveClass(/chord-block-active/);
    
    console.log('✅ Can select chord blocks');
  });

  test('displays chord palette', async ({ page }) => {
    await page.goto('/');
    
    // Check for chord palette
    await expect(page.locator('text=Chord Palette')).toBeVisible();
    
    // Check for note buttons in chord palette
    const chordPalette = page.locator('.bg-zinc-900').filter({ hasText: 'Chord Palette' });
    await expect(chordPalette.locator('button').filter({ hasText: 'C' }).first()).toBeVisible();
    await expect(chordPalette.locator('button').filter({ hasText: 'G' }).first()).toBeVisible();
    
    // Check for chord type buttons
    await expect(page.locator('button:has-text("Major")')).toBeVisible();
    await expect(page.locator('button:has-text("Minor")')).toBeVisible();
    
    console.log('✅ Chord palette is displayed');
  });

  test('can toggle AI suggestions', async ({ page }) => {
    await page.goto('/');
    
    // Click AI suggestions button
    await page.click('text=AI Suggestions');
    
    // Check suggestions appear
    await expect(page.locator('text=Common Progressions')).toBeVisible();
    await expect(page.locator('text=I-V-vi-IV')).toBeVisible();
    
    console.log('✅ AI suggestions can be toggled');
  });

  test('displays transport controls', async ({ page }) => {
    await page.goto('/');
    
    // Check for play button
    const playButton = page.locator('.transport-button').first();
    await expect(playButton).toBeVisible();
    
    // Check for tempo control
    await expect(page.locator('input[type="number"][min="60"][max="200"]')).toBeVisible();
    const tempoValue = await page.locator('input[type="number"]').inputValue();
    expect(tempoValue).toBe('120');
    
    console.log('✅ Transport controls are displayed');
  });
});