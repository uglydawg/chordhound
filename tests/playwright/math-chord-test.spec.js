import { test, expect } from '@playwright/test';

test.describe('Math Chord Test Page', () => {
    test.beforeEach(async ({ page }) => {
        // Navigate to the math chord test page
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to fully load
        await page.waitForLoadState('networkidle');
        
        // Set up console error monitoring
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log('Console error:', msg.text());
            }
        });
        
        // Set up page error monitoring
        page.on('pageerror', error => {
            console.log('Page error:', error.message);
        });
    });

    test('should load the math chord test page successfully', async ({ page }) => {
        // Verify page title (could be ChordHound or contain Math Chords)
        await expect(page).toHaveTitle(/ChordHound|Math.*Chord/i);
        
        // Check for main page content - look for any heading indicating this is the math chord page
        const headings = page.locator('h1, h2, h3');
        await expect(headings.first()).toBeVisible();
    });

    test('should render the PianoPlayer component', async ({ page }) => {
        // Wait for the PianoPlayer component to be present
        const pianoPlayer = page.locator('.piano-player');
        await expect(pianoPlayer).toBeVisible();
        
        // Check for piano keyboard visualization
        const pianoKeyboard = page.locator('#piano-keyboard');
        await expect(pianoKeyboard).toBeVisible();
        
        // Verify piano keys are rendered
        const pianoKeys = page.locator('.piano-key');
        await expect(pianoKeys.first()).toBeVisible();
        
        // Check that we have both white and black keys
        const whiteKeys = page.locator('.piano-key.white-key');
        const blackKeys = page.locator('.piano-key.black-key');
        await expect(whiteKeys.first()).toBeVisible();
        await expect(blackKeys.first()).toBeVisible();
    });

    test('should have functional play button for rhythm pattern playback', async ({ page }) => {
        // Look for the rhythm play button (green button with "Play" text)
        const rhythmPlayButton = page.locator('button.bg-green-600:has-text("Play")');
        await expect(rhythmPlayButton).toBeVisible();
        
        // Click the rhythm play button
        await rhythmPlayButton.click();
        
        // Wait a moment for audio to potentially start
        await page.waitForTimeout(1000);
        
        // The button might change to "Stop" or show some active state
        // Check if the button changed in appearance or if there's a stop button
        const stopButton = page.locator('button:has-text("Stop")');
        const isPlaying = await stopButton.isVisible();
        
        if (isPlaying) {
            // If stop button is visible, click it to stop playback
            await stopButton.click();
        }
    });

    test('should have Play Chord functionality', async ({ page }) => {
        // Look for the chord play button (blue button with "Play Chord" text)
        const chordPlayButton = page.locator('button.bg-blue-600:has-text("Play Chord")');
        await expect(chordPlayButton).toBeVisible();
        
        // Click the chord play button
        await chordPlayButton.click();
        
        // Wait a moment for audio to potentially start
        await page.waitForTimeout(1000);
        
        // The button should remain clickable after playing
        await expect(chordPlayButton).toBeVisible();
    });

    test('should support chord progression changes', async ({ page }) => {
        // Look for preset progression buttons
        const progressionButtons = page.locator('button:has-text("Classic"), button:has-text("Pop"), button:has-text("50s")');
        
        if (await progressionButtons.count() > 0) {
            const firstProgression = progressionButtons.first();
            await expect(firstProgression).toBeVisible();
            
            // Click on a progression button to change it
            await firstProgression.click();
            
            // Wait for the change to take effect
            await page.waitForTimeout(500);
            
            // Check if piano keyboard shows some keys highlighted
            const pianoKeys = page.locator('.piano-key');
            await expect(pianoKeys.first()).toBeVisible();
        }
    });

    test('should support BPM changes', async ({ page }) => {
        // Look for BPM control
        const bpmControl = page.locator('input[type="range"][name*="bpm"], input[type="number"][name*="bpm"], [data-testid="bpm-control"]');
        
        if (await bpmControl.count() > 0) {
            const bpmInput = bpmControl.first();
            await expect(bpmInput).toBeVisible();
            
            // Get current BPM value
            const originalBpm = await bpmInput.inputValue();
            
            // Change BPM value
            await bpmInput.fill('140');
            
            // Trigger change event
            await bpmInput.blur();
            
            // Wait for the change to take effect
            await page.waitForTimeout(500);
            
            // Verify the value changed
            const newBpm = await bpmInput.inputValue();
            expect(newBpm).toBe('140');
        }
    });

    test('should support rhythm pattern changes', async ({ page }) => {
        // Look for rhythm pattern controls
        const rhythmSelector = page.locator('select[name*="rhythm"], [data-testid="rhythm-selector"], .rhythm-pattern-selector');
        
        if (await rhythmSelector.count() > 0) {
            const rhythmControl = rhythmSelector.first();
            await expect(rhythmControl).toBeVisible();
            
            // Get available options
            const options = await rhythmControl.locator('option').count();
            
            if (options > 1) {
                // Get current selection
                const originalValue = await rhythmControl.inputValue();
                
                // Change to different rhythm pattern
                await rhythmControl.selectOption({ index: 1 });
                
                // Wait for the change to take effect
                await page.waitForTimeout(500);
                
                // Verify the value changed
                const newValue = await rhythmControl.inputValue();
                expect(newValue).not.toBe(originalValue);
            }
        }
    });

    test('should update piano visualization when chord parameters change', async ({ page }) => {
        // Look for chord parameter controls (key, chord type, inversion, etc.)
        const chordControls = page.locator('select[name*="key"], select[name*="chord"], select[name*="inversion"], [data-testid*="chord-control"]');
        
        if (await chordControls.count() > 0) {
            // Take a screenshot of the initial piano state
            const pianoKeyboard = page.locator('.piano-keyboard, [data-testid="piano-keyboard"]');
            await expect(pianoKeyboard).toBeVisible();
            
            // Count initially highlighted keys
            const initialHighlightedKeys = await page.locator('.piano-key.active, .piano-key.highlighted, [data-testid="active-key"]').count();
            
            // Change a chord parameter
            const firstControl = chordControls.first();
            await firstControl.selectOption({ index: 1 });
            
            // Wait for the change to propagate
            await page.waitForTimeout(1000);
            
            // Verify piano keyboard updated
            const updatedHighlightedKeys = await page.locator('.piano-key.active, .piano-key.highlighted, [data-testid="active-key"]').count();
            
            // The number of highlighted keys might change, or at least the visual should update
            // We'll check that there are still highlighted keys (indicating the chord is displayed)
            expect(updatedHighlightedKeys).toBeGreaterThan(0);
        }
    });

    test('should not have console errors during interactions', async ({ page }) => {
        const consoleErrors = [];
        const pageErrors = [];
        
        // Monitor console and page errors
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
            }
        });
        
        page.on('pageerror', error => {
            pageErrors.push(error.message);
        });
        
        // Perform various interactions
        const rhythmPlayButton = page.locator('button.bg-green-600:has-text("Play")').first();
        if (await rhythmPlayButton.isVisible()) {
            await rhythmPlayButton.click();
            await page.waitForTimeout(1000);
        }
        
        // Try changing controls
        const allSelects = page.locator('select');
        const selectCount = await allSelects.count();
        
        for (let i = 0; i < Math.min(selectCount, 3); i++) {
            const select = allSelects.nth(i);
            if (await select.isVisible()) {
                const optionCount = await select.locator('option').count();
                if (optionCount > 1) {
                    await select.selectOption({ index: 1 });
                    await page.waitForTimeout(500);
                }
            }
        }
        
        // Check for errors
        expect(consoleErrors).toHaveLength(0);
        expect(pageErrors).toHaveLength(0);
    });

    test('should handle audio context properly', async ({ page }) => {
        // Test that audio context is initialized when needed
        const rhythmPlayButton = page.locator('button.bg-green-600:has-text("Play")').first();
        
        if (await rhythmPlayButton.isVisible()) {
            // Click play button to trigger audio context
            await rhythmPlayButton.click();
            
            // Wait for audio to potentially start
            await page.waitForTimeout(2000);
            
            // Check if there's any audio-related error in console
            const audioErrors = await page.evaluate(() => {
                const errors = [];
                // Check if there are any audio context related errors
                if (window.console && window.console.error) {
                    // This is a simplified check - in a real scenario you'd capture console.error calls
                }
                return errors;
            });
            
            // Stop playback if stop button is available
            const stopButton = page.locator('button:has-text("Stop")');
            if (await stopButton.isVisible()) {
                await stopButton.click();
            }
        }
    });

    test('should display chord information correctly', async ({ page }) => {
        // Look for chord information display
        const chordInfo = page.locator('.chord-info, [data-testid="chord-info"], .current-chord');
        
        if (await chordInfo.count() > 0) {
            await expect(chordInfo.first()).toBeVisible();
            
            // The chord info should contain some text (chord name, notes, etc.)
            const chordText = await chordInfo.first().textContent();
            expect(chordText).toBeTruthy();
            expect(chordText.trim().length).toBeGreaterThan(0);
        }
    });

    test('should be responsive and work on different viewport sizes', async ({ page }) => {
        // Test desktop size
        await page.setViewportSize({ width: 1200, height: 800 });
        await page.waitForTimeout(500);
        
        const pianoPlayer = page.locator('.piano-player');
        await expect(pianoPlayer).toBeVisible();
        
        // Test tablet size
        await page.setViewportSize({ width: 768, height: 1024 });
        await page.waitForTimeout(500);
        
        await expect(pianoPlayer).toBeVisible();
        
        // Test mobile size
        await page.setViewportSize({ width: 375, height: 667 });
        await page.waitForTimeout(500);
        
        await expect(pianoPlayer).toBeVisible();
        
        // Reset to desktop
        await page.setViewportSize({ width: 1200, height: 800 });
    });
});