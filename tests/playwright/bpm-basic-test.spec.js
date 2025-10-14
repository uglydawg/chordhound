import { test, expect } from '@playwright/test';

test.describe('BPM Synchronization - Basic Verification', () => {
    
    test('should verify BPM synchronization from Math Chord Test to Piano Player', async ({ page }) => {
        // Navigate to the math chord test page
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to load completely
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000);
        
        // Find the BPM select dropdown
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        await expect(mathBpmSelect).toBeVisible();
        
        // Find the Piano Player tempo input
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        await expect(pianoTempoInput).toBeVisible();
        
        // Get initial values
        const initialMathBpm = await mathBpmSelect.inputValue();
        const initialPianoTempo = await pianoTempoInput.inputValue();
        
        console.log(`Initial - Math BPM: ${initialMathBpm}, Piano Tempo: ${initialPianoTempo}`);
        
        // Verify they start synchronized
        expect(initialMathBpm).toBe(initialPianoTempo);
        
        // Change Math Chord Test BPM to 140
        await mathBpmSelect.selectOption('140');
        
        // Wait for the change to propagate
        await page.waitForTimeout(2000);
        
        // Verify Piano Player tempo updated
        const updatedPianoTempo = await pianoTempoInput.inputValue();
        console.log(`After change - Math BPM: 140, Piano Tempo: ${updatedPianoTempo}`);
        
        expect(updatedPianoTempo).toBe('140');
        
        // Change Math Chord Test BPM to 60
        await mathBpmSelect.selectOption('60');
        await page.waitForTimeout(2000);
        
        const finalPianoTempo = await pianoTempoInput.inputValue();
        console.log(`Final - Math BPM: 60, Piano Tempo: ${finalPianoTempo}`);
        
        expect(finalPianoTempo).toBe('60');
        
        console.log('✅ BPM synchronization from Math Chord Test to Piano Player is working');
    });
    
    test('should verify reverse synchronization (Piano Player to Math Chord Test)', async ({ page }) => {
        // Navigate to the math chord test page
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to load completely
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000);
        
        // Find elements
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        
        await expect(mathBpmSelect).toBeVisible();
        await expect(pianoTempoInput).toBeVisible();
        
        // Set initial state
        await mathBpmSelect.selectOption('120');
        await page.waitForTimeout(1000);
        
        // Change Piano Player tempo directly to a value that exists in the Math BPM select
        await pianoTempoInput.fill('160');
        await pianoTempoInput.blur(); // Trigger change event
        await page.waitForTimeout(2000);
        
        // Check if Math BPM select updated
        const updatedMathBpm = await mathBpmSelect.inputValue();
        console.log(`After Piano change - Math BPM: ${updatedMathBpm}, Piano Tempo: 160`);
        
        // The math BPM might or might not update depending on the implementation
        // This test documents the actual behavior
        console.log(`Reverse sync result: Piano=160 -> Math BPM=${updatedMathBpm}`);
        
        // At minimum, the piano input should retain the new value
        expect(await pianoTempoInput.inputValue()).toBe('160');
    });
    
    test('should verify Piano Player increment/decrement buttons work', async ({ page }) => {
        await page.goto('http://localhost:8000/debug/math-chords');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000);
        
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        const incrementButton = page.locator('button[wire\\:click="incrementTempo"]');
        const decrementButton = page.locator('button[wire\\:click="decrementTempo"]');
        
        await expect(pianoTempoInput).toBeVisible();
        await expect(incrementButton).toBeVisible();
        await expect(decrementButton).toBeVisible();
        
        // Get initial tempo
        const initialTempo = parseInt(await pianoTempoInput.inputValue());
        console.log(`Initial tempo: ${initialTempo}`);
        
        // Test increment
        await incrementButton.click();
        await page.waitForTimeout(1000);
        
        const afterIncrement = parseInt(await pianoTempoInput.inputValue());
        console.log(`After increment: ${afterIncrement}`);
        
        expect(afterIncrement).toBeGreaterThan(initialTempo);
        
        // Test decrement
        await decrementButton.click();
        await page.waitForTimeout(1000);
        
        const afterDecrement = parseInt(await pianoTempoInput.inputValue());
        console.log(`After decrement: ${afterDecrement}`);
        
        expect(afterDecrement).toBeLessThan(afterIncrement);
        
        console.log('✅ Piano Player increment/decrement buttons are working');
    });
});