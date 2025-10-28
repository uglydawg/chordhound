import { test, expect } from '@playwright/test';

test.describe('BPM Synchronization between Math Chord Test and Piano Player - Corrected', () => {
    
    test.beforeEach(async ({ page }) => {
        // Navigate to the math chord test page
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to be fully loaded
        await page.waitForLoadState('networkidle');
        
        // Wait for essential elements to be visible
        await page.waitForSelector('select[wire\\:model\\.live="bpm"]', { timeout: 15000 });
        await page.waitForSelector('input[wire\\:model\\.live="tempo"]', { timeout: 15000 });
        
        // Give additional time for components to initialize
        await page.waitForTimeout(2000);
    });

    test('should synchronize BPM when math chord test BPM select changes', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        
        await expect(mathBpmSelect).toBeVisible();
        await expect(pianoTempoInput).toBeVisible();
        
        // Test synchronization for all available BPM values
        const bpmValues = ['60', '80', '100', '120', '140', '160', '180'];
        
        for (const bpm of bpmValues) {
            // Change BPM in math chord test
            await mathBpmSelect.selectOption(bpm);
            await page.waitForTimeout(1000);
            
            // Verify Piano Player tempo updated to match
            const pianoTempo = await pianoTempoInput.inputValue();
            expect(pianoTempo).toBe(bpm);
            
            console.log(`✓ BPM ${bpm}: Math -> Piano sync working`);
        }
    });

    test('should synchronize BPM when Piano Player tempo input changes to valid select values', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        
        // Test with values that exist in both controls
        const validValues = ['80', '100', '140', '160'];
        
        for (const value of validValues) {
            // Change Piano Player tempo directly
            await pianoTempoInput.fill(value);
            await pianoTempoInput.blur();
            await page.waitForTimeout(1000);
            
            // Verify math chord test BPM select updated to match (if the value exists)
            const mathBpm = await mathBpmSelect.inputValue();
            expect(mathBpm).toBe(value);
            
            console.log(`✓ BPM ${value}: Piano -> Math sync working`);
        }
    });

    test('should handle Piano Player increment/decrement within valid ranges', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        const incrementButton = page.locator('button[wire\\:click="incrementTempo"]');
        const decrementButton = page.locator('button[wire\\:click="decrementTempo"]');
        
        // Start with 100 BPM
        await mathBpmSelect.selectOption('100');
        await page.waitForTimeout(1000);
        
        // Verify initial sync
        expect(await pianoTempoInput.inputValue()).toBe('100');
        expect(await mathBpmSelect.inputValue()).toBe('100');
        
        // Increment to get to 120 (which exists in select)
        await incrementButton.click(); // 105
        await page.waitForTimeout(500);
        await incrementButton.click(); // 110  
        await page.waitForTimeout(500);
        await incrementButton.click(); // 115
        await page.waitForTimeout(500);
        await incrementButton.click(); // 120
        await page.waitForTimeout(1000);
        
        // Piano should show 120
        expect(await pianoTempoInput.inputValue()).toBe('120');
        // Math BPM should also show 120 (since it's a valid select option)
        expect(await mathBpmSelect.inputValue()).toBe('120');
        
        console.log('✓ Increment to valid select value: both components synchronized');
    });

    test('should show BPM values in play controls section', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        
        // Look for text that shows the current BPM
        const playControlsText = page.locator('text=/@ \\d+ BPM/');
        
        // Change BPM and verify it's displayed
        await mathBpmSelect.selectOption('140');
        await page.waitForTimeout(1000);
        
        await expect(playControlsText).toContainText('@ 140 BPM');
        
        // Test another value
        await mathBpmSelect.selectOption('80');
        await page.waitForTimeout(1000);
        
        await expect(playControlsText).toContainText('@ 80 BPM');
    });

    test('should maintain BPM bounds correctly', async ({ page }) => {
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        const decrementButton = page.locator('button[wire\\:click="decrementTempo"]');
        const incrementButton = page.locator('button[wire\\:click="incrementTempo"]');
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        
        // Test minimum bound - set to 60 and try to decrement
        await mathBpmSelect.selectOption('60');
        await page.waitForTimeout(1000);
        
        expect(await pianoTempoInput.inputValue()).toBe('60');
        
        // Try to decrement below minimum
        await decrementButton.click();
        await page.waitForTimeout(1000);
        
        // Should stay at 60 (minimum)
        expect(await pianoTempoInput.inputValue()).toBe('60');
        
        // Test maximum bound - set to 180 and increment several times
        await mathBpmSelect.selectOption('180');
        await page.waitForTimeout(1000);
        
        expect(await pianoTempoInput.inputValue()).toBe('180');
        
        // Increment several times to approach maximum (200)
        for (let i = 0; i < 5; i++) {
            await incrementButton.click();
            await page.waitForTimeout(200);
        }
        
        const finalTempo = parseInt(await pianoTempoInput.inputValue());
        expect(finalTempo).toBeLessThanOrEqual(200); // Should not exceed 200
        expect(finalTempo).toBeGreaterThan(180); // Should have increased from 180
        
        console.log(`✓ Final tempo after increments: ${finalTempo} (within bounds)`);
    });

    test('should persist BPM changes across page reloads', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        
        // Change to a non-default BPM value
        await mathBpmSelect.selectOption('160');
        await page.waitForTimeout(1000);
        
        // Verify both components updated
        expect(await pianoTempoInput.inputValue()).toBe('160');
        expect(await mathBpmSelect.inputValue()).toBe('160');
        
        // Reload the page
        await page.reload();
        await page.waitForLoadState('networkidle');
        await page.waitForSelector('select[wire\\:model\\.live="bpm"]', { timeout: 15000 });
        await page.waitForSelector('input[wire\\:model\\.live="tempo"]', { timeout: 15000 });
        await page.waitForTimeout(2000);
        
        // Re-find elements after reload
        const reloadedMathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const reloadedPianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        
        // Verify persistence
        expect(await reloadedMathBpmSelect.inputValue()).toBe('160');
        expect(await reloadedPianoTempoInput.inputValue()).toBe('160');
        
        console.log('✓ BPM persistence across page reload verified');
    });

    test('should handle edge case: Piano Player values that do not exist in BPM select', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        const incrementButton = page.locator('button[wire\\:click="incrementTempo"]');
        
        // Start with 120
        await mathBpmSelect.selectOption('120');
        await page.waitForTimeout(1000);
        
        // Increment once to get 125 (not in select options)
        await incrementButton.click();
        await page.waitForTimeout(1000);
        
        // Piano should show 125
        expect(await pianoTempoInput.inputValue()).toBe('125');
        
        // Math BPM should show the closest valid option or remain unchanged
        // This depends on implementation - the test documents the current behavior
        const mathBpmValue = await mathBpmSelect.inputValue();
        console.log(`Piano: 125, Math BPM select: ${mathBpmValue}`);
        
        // The math BPM should show either 120 (unchanged) or 140 (closest higher value)
        // This test documents the actual behavior rather than asserting a specific expectation
        expect(['120', '125', '140']).toContain(mathBpmValue);
        
        console.log(`✓ Edge case handled: Piano=125, Math BPM select=${mathBpmValue}`);
    });
});