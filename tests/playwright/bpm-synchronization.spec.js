import { test, expect } from '@playwright/test';

test.describe('BPM Synchronization between Math Chord Test and Piano Player', () => {
    
    test.beforeEach(async ({ page }) => {
        // Navigate to the math chord test page
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to be fully loaded
        await page.waitForLoadState('networkidle');
        
        // Wait for essential elements to be visible with more flexible selectors
        await page.waitForSelector('select', { timeout: 15000 });
        await page.waitForSelector('input[type="number"]', { timeout: 15000 });
        
        // Give additional time for components to initialize
        await page.waitForTimeout(2000);
    });

    test('should synchronize BPM when math chord test BPM select changes', async ({ page }) => {
        // Find the BPM select dropdown using the Livewire model attribute
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        await expect(mathBpmSelect).toBeVisible();
        
        // Find the PianoPlayer tempo input using the Livewire model attribute
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        await expect(pianoTempoInput).toBeVisible();
        
        // Verify initial values match
        const initialMathBpm = await mathBpmSelect.inputValue();
        const initialPianoTempo = await pianoTempoInput.inputValue();
        expect(initialMathBpm).toBe(initialPianoTempo);
        
        // Change BPM in math chord test to 140
        await mathBpmSelect.selectOption('140');
        
        // Wait for the change to process
        await page.waitForTimeout(1500);
        
        // Verify Piano Player tempo updated to match
        const updatedPianoTempo = await pianoTempoInput.inputValue();
        expect(updatedPianoTempo).toBe('140');
        
        // Change BPM in math chord test to 100
        await mathBpmSelect.selectOption('100');
        await page.waitForTimeout(1500);
        
        // Verify Piano Player tempo updated again
        const newPianoTempo = await pianoTempoInput.inputValue();
        expect(newPianoTempo).toBe('100');
    });

    test('should synchronize BPM when Piano Player tempo input changes directly', async ({ page }) => {
        // Find the Piano Player tempo input
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        await expect(pianoTempoInput).toBeVisible();
        
        // Find the math chord test BPM select
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        await expect(mathBpmSelect).toBeVisible();
        
        // Change Piano Player tempo directly to 160
        await pianoTempoInput.fill('160');
        await pianoTempoInput.blur(); // Trigger the change event
        
        // Wait for Livewire to process the change
        await page.waitForTimeout(1000);
        
        // Verify math chord test BPM select updated to match
        const updatedMathBpm = await mathBpmSelect.inputValue();
        expect(updatedMathBpm).toBe('160');
        
        // Change Piano Player tempo to 80
        await pianoTempoInput.fill('80');
        await pianoTempoInput.blur();
        await page.waitForTimeout(1000);
        
        // Verify math chord test BPM select updated again
        const newMathBpm = await mathBpmSelect.inputValue();
        expect(newMathBpm).toBe('80');
    });

    test('should synchronize BPM when Piano Player increment/decrement buttons are used', async ({ page }) => {
        // Find the Piano Player increment and decrement buttons
        const incrementButton = page.locator('button[wire\\:click="incrementTempo"]');
        const decrementButton = page.locator('button[wire\\:click="decrementTempo"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        
        await expect(incrementButton).toBeVisible();
        await expect(decrementButton).toBeVisible();
        
        // Verify initial state (120 BPM)
        let currentPianoTempo = await pianoTempoInput.inputValue();
        let currentMathBpm = await mathBpmSelect.inputValue();
        expect(currentPianoTempo).toBe('120');
        expect(currentMathBpm).toBe('120');
        
        // Click increment button (should go to 125)
        await incrementButton.click();
        await page.waitForTimeout(1000);
        
        // Verify both components updated
        currentPianoTempo = await pianoTempoInput.inputValue();
        currentMathBpm = await mathBpmSelect.inputValue();
        expect(currentPianoTempo).toBe('125');
        expect(currentMathBpm).toBe('125');
        
        // Click increment button twice more (should go to 135)
        await incrementButton.click();
        await page.waitForTimeout(500);
        await incrementButton.click();
        await page.waitForTimeout(1000);
        
        currentPianoTempo = await pianoTempoInput.inputValue();
        currentMathBpm = await mathBpmSelect.inputValue();
        expect(currentPianoTempo).toBe('135');
        expect(currentMathBpm).toBe('135');
        
        // Click decrement button (should go to 130)
        await decrementButton.click();
        await page.waitForTimeout(1000);
        
        currentPianoTempo = await pianoTempoInput.inputValue();
        currentMathBpm = await mathBpmSelect.inputValue();
        expect(currentPianoTempo).toBe('130');
        expect(currentMathBpm).toBe('130');
    });

    test('should handle BPM boundary conditions correctly', async ({ page }) => {
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const decrementButton = page.locator('button[wire\\:click="decrementTempo"]');
        const incrementButton = page.locator('button[wire\\:click="incrementTempo"]');
        
        // Test minimum BPM (60)
        await mathBpmSelect.selectOption('60');
        await page.waitForTimeout(1000);
        
        let currentPianoTempo = await pianoTempoInput.inputValue();
        expect(currentPianoTempo).toBe('60');
        
        // Try to decrement below minimum - should stay at 60
        await decrementButton.click();
        await page.waitForTimeout(1000);
        
        currentPianoTempo = await pianoTempoInput.inputValue();
        expect(currentPianoTempo).toBe('60'); // Should not go below 60
        
        // Test maximum BPM (180)
        await mathBpmSelect.selectOption('180');
        await page.waitForTimeout(1000);
        
        currentPianoTempo = await pianoTempoInput.inputValue();
        expect(currentPianoTempo).toBe('180');
        
        // Try to increment above maximum - should stay at maximum allowed (200)
        for (let i = 0; i < 5; i++) {
            await incrementButton.click();
            await page.waitForTimeout(200);
        }
        
        currentPianoTempo = await pianoTempoInput.inputValue();
        const finalTempo = parseInt(currentPianoTempo);
        expect(finalTempo).toBeLessThanOrEqual(200); // Should not exceed 200
    });

    test('should display current BPM in the play controls section', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const playControlsText = page.locator('text=/Playing:.*@ \\d+ BPM/');
        
        // Change BPM and verify it's displayed in the play controls
        await mathBpmSelect.selectOption('140');
        await page.waitForTimeout(1000);
        
        // Check that the play controls section shows the updated BPM
        await expect(playControlsText).toContainText('@ 140 BPM');
        
        // Change to another BPM value
        await mathBpmSelect.selectOption('100');
        await page.waitForTimeout(1000);
        
        await expect(playControlsText).toContainText('@ 100 BPM');
    });

    test('should maintain BPM sync during rhythm pattern playback', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        const playButton = page.locator('button[wire\\:click="playRhythm"]');
        
        // Set a specific BPM
        await mathBpmSelect.selectOption('140');
        await page.waitForTimeout(1000);
        
        // Verify sync
        const pianoTempo = await pianoTempoInput.inputValue();
        expect(pianoTempo).toBe('140');
        
        // Start playing rhythm
        await playButton.click();
        await page.waitForTimeout(500);
        
        // Change BPM while playing
        await mathBpmSelect.selectOption('160');
        await page.waitForTimeout(1000);
        
        // Verify Piano Player tempo updated even during playback
        const updatedPianoTempo = await pianoTempoInput.inputValue();
        expect(updatedPianoTempo).toBe('160');
        
        // Stop playback
        const stopButton = page.locator('button[wire\\:click="stopProgression"]');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
    });

    test('should persist BPM changes between page reloads', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        
        // Change BPM to a non-default value
        await mathBpmSelect.selectOption('160');
        await page.waitForTimeout(1000);
        
        // Verify both components show the new BPM
        const pianoTempo = await pianoTempoInput.inputValue();
        expect(pianoTempo).toBe('160');
        
        // Reload the page
        await page.reload();
        await page.waitForLoadState('domcontentloaded');
        await page.waitForSelector('select[wire\\:model\\.live="bpm"]', { timeout: 15000 });
        await page.waitForSelector('input[wire\\:model\\.live="tempo"]', { timeout: 15000 });
        await page.waitForTimeout(1000);
        
        // Re-find elements after reload
        const reloadedMathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const reloadedPianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        
        // Verify BPM persisted
        const persistedMathBpm = await reloadedMathBpmSelect.inputValue();
        const persistedPianoTempo = await reloadedPianoTempoInput.inputValue();
        
        expect(persistedMathBpm).toBe('160');
        expect(persistedPianoTempo).toBe('160');
    });

    test('should handle rapid BPM changes without desynchronization', async ({ page }) => {
        const mathBpmSelect = page.locator('select[wire\\:model\\.live="bpm"]');
        const pianoTempoInput = page.locator('input[wire\\:model\\.live="tempo"]');
        const incrementButton = page.locator('button[wire\\:click="incrementTempo"]');
        
        // Rapidly change BPM values
        const bpmValues = ['100', '140', '80', '160', '120'];
        
        for (const bpm of bpmValues) {
            await mathBpmSelect.selectOption(bpm);
            await page.waitForTimeout(300); // Shorter wait for rapid changes
            
            const currentPianoTempo = await pianoTempoInput.inputValue();
            expect(currentPianoTempo).toBe(bpm);
        }
        
        // Test rapid increment button clicks
        let lastTempo = 120;
        for (let i = 0; i < 6; i++) {
            await incrementButton.click();
            await page.waitForTimeout(200);
            lastTempo += 5; // Increment by 5 each time
        }
        
        // Wait for final update
        await page.waitForTimeout(1000);
        
        const finalPianoTempo = await pianoTempoInput.inputValue();
        const finalMathBpm = await mathBpmSelect.inputValue();
        
        // Both should be synchronized
        expect(finalPianoTempo).toBe(finalMathBpm);
        expect(parseInt(finalPianoTempo)).toBe(lastTempo);
    });
});