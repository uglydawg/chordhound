import { test, expect } from '@playwright/test';

test('debug page access and element visibility', async ({ page }) => {
    console.log('Navigating to math-chord-test page...');
    
    try {
        await page.goto('http://localhost:8000/debug/math-chords');
        console.log('Successfully navigated to the page');
        
        // Wait for page to load
        await page.waitForLoadState('networkidle');
        console.log('Page loaded (networkidle)');
        
        // Check what's actually on the page
        const title = await page.title();
        console.log('Page title:', title);
        
        // Check for various selectors
        const selectors = [
            '.piano-player',
            '.piano-keys', 
            '#piano-keyboard',
            '.piano-container',
            'livewire\\:piano-player',
            '[x-data="mathChordPlayer"]'
        ];
        
        for (const selector of selectors) {
            try {
                const element = await page.locator(selector).first();
                const exists = await element.count() > 0;
                const visible = exists ? await element.isVisible() : false;
                console.log(`Selector '${selector}': exists=${exists}, visible=${visible}`);
            } catch (e) {
                console.log(`Selector '${selector}': error=${e.message}`);
            }
        }
        
        // Take a screenshot for debugging
        await page.screenshot({ path: 'debug-math-chord-test.png', fullPage: true });
        console.log('Screenshot saved as debug-math-chord-test.png');
        
        // Check if Alpine.js data is available
        const alpineAvailable = await page.evaluate(() => {
            return typeof Alpine !== 'undefined' && 
                   document.querySelector('[x-data="mathChordPlayer"]') !== null;
        });
        console.log('Alpine.js data available:', alpineAvailable);
        
        // Get some basic page content
        const bodyText = await page.locator('body').textContent();
        console.log('Page contains piano-related text:', bodyText.includes('piano') || bodyText.includes('Piano'));
        
    } catch (error) {
        console.error('Error during test:', error);
        await page.screenshot({ path: 'debug-error.png', fullPage: true });
        throw error;
    }
});