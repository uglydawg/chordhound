import { test, expect } from '@playwright/test';

test.describe('BPM Synchronization - Simple Test', () => {
    
    test('should load math chord test page and verify BPM elements exist', async ({ page }) => {
        // Navigate to the math chord test page
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to load
        await page.waitForLoadState('networkidle');
        
        // Take a screenshot for debugging
        await page.screenshot({ path: 'test-results/bpm-page-loaded.png' });
        
        // Look for page heading to confirm we're on the right page
        const heading = page.locator('h1');
        await expect(heading).toBeVisible();
        const headingText = await heading.textContent();
        console.log('Page heading:', headingText);
        
        // Look for any select elements (including BPM)
        const selects = page.locator('select');
        const selectCount = await selects.count();
        console.log('Found', selectCount, 'select elements');
        
        // Look for any number inputs (including tempo)
        const numberInputs = page.locator('input[type="number"]');
        const inputCount = await numberInputs.count();
        console.log('Found', inputCount, 'number input elements');
        
        // Verify we have at least one select and one input
        expect(selectCount).toBeGreaterThan(0);
        expect(inputCount).toBeGreaterThan(0);
        
        // Look for BPM-related text
        const pageContent = await page.content();
        const hasBPM = pageContent.includes('BPM') || pageContent.includes('bpm');
        expect(hasBPM).toBe(true);
        
        // Look for any element with text containing "120" (default BPM)
        const elementsWith120 = page.locator('text=120');
        const count120 = await elementsWith120.count();
        console.log('Found', count120, 'elements containing "120"');
        
        // Try to find specific BPM and tempo elements with various selectors
        const bpmSelectors = [
            'select[wire\\:model\\.live="bpm"]',
            'select[name="bpm"]',
            'select[id*="bpm"]',
            'select:has(option[value="120"])'
        ];
        
        const tempoSelectors = [
            'input[wire\\:model\\.live="tempo"]',
            'input[name="tempo"]',
            'input[id*="tempo"]',
            'input[type="number"][value="120"]'
        ];
        
        for (const selector of bpmSelectors) {
            const elements = page.locator(selector);
            const count = await elements.count();
            console.log(`BPM selector "${selector}": ${count} elements`);
        }
        
        for (const selector of tempoSelectors) {
            const elements = page.locator(selector);
            const count = await elements.count();
            console.log(`Tempo selector "${selector}": ${count} elements`);
        }
    });
    
    test('should find and interact with BPM controls', async ({ page }) => {
        await page.goto('http://localhost:8000/debug/math-chords');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000); // Give extra time for Livewire to initialize
        
        // Try to find the BPM select by looking for option values
        const bpmSelect = page.locator('select').filter({ has: page.locator('option[value="120"]') }).first();
        if (await bpmSelect.count() > 0) {
            console.log('Found BPM select element');
            await expect(bpmSelect).toBeVisible();
            
            const initialValue = await bpmSelect.inputValue();
            console.log('Initial BPM value:', initialValue);
            
            // Try to change the value
            await bpmSelect.selectOption('140');
            await page.waitForTimeout(2000); // Wait for the change to propagate
            
            const newValue = await bpmSelect.inputValue();
            console.log('New BPM value:', newValue);
            expect(newValue).toBe('140');
        } else {
            console.log('Could not find BPM select element');
        }
        
        // Try to find tempo input
        const tempoInput = page.locator('input[type="number"]').first();
        if (await tempoInput.count() > 0) {
            console.log('Found tempo input element');
            await expect(tempoInput).toBeVisible();
            
            const tempoValue = await tempoInput.inputValue();
            console.log('Current tempo value:', tempoValue);
        } else {
            console.log('Could not find tempo input element');
        }
    });
});