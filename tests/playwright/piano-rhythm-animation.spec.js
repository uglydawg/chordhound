import { test, expect } from '@playwright/test';

test.describe('Piano Key Animation During Rhythm Playback', () => {
    test.beforeEach(async ({ page }) => {
        // Navigate to the math chord test page (actual route is /debug/math-chords)
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to fully load
        await page.waitForLoadState('networkidle');
        
        // Wait for the PianoPlayer component to be visible
        await page.waitForSelector('.piano-player', { timeout: 10000 });
        
        // Set up console monitoring
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

    test('should verify PianoPlayer component is visible', async ({ page }) => {
        // Verify the PianoPlayer component is rendered
        const pianoPlayer = page.locator('.piano-player');
        await expect(pianoPlayer).toBeVisible();
        
        // Check for piano keyboard visualization
        const pianoKeyboard = page.locator('[data-testid="piano-keyboard"], .piano-keyboard, [id*="piano"]');
        await expect(pianoKeyboard.first()).toBeVisible();
        
        // Verify piano keys are rendered
        const pianoKeys = page.locator('.piano-key');
        await expect(pianoKeys.first()).toBeVisible();
        
        // Check that we have both white and black keys
        const whiteKeys = page.locator('.piano-key.white-key, .white-key');
        const blackKeys = page.locator('.piano-key.black-key, .black-key');
        
        const whiteKeyCount = await whiteKeys.count();
        const blackKeyCount = await blackKeys.count();
        
        expect(whiteKeyCount).toBeGreaterThan(0);
        expect(blackKeyCount).toBeGreaterThan(0);
        
        console.log(`Found ${whiteKeyCount} white keys and ${blackKeyCount} black keys`);
    });

    test('should animate piano keys during rhythm playback', async ({ page }) => {
        // Wait for initial chord calculation to complete
        await page.waitForTimeout(1000);
        
        // Find the rhythm play button (green Play button)
        const rhythmPlayButton = page.locator('button.bg-green-600:has-text("Play")');
        await expect(rhythmPlayButton).toBeVisible();
        
        // Get initial state of all piano keys to compare later
        const initialPianoKeys = await page.locator('.piano-key, [id^="key-"]').all();
        console.log(`Found ${initialPianoKeys.length} piano keys to monitor`);
        
        // Track key press animations
        let keyPressDetected = false;
        let transformDetected = false;
        
        // Set up monitoring for key state changes
        const monitorKeyStates = async () => {
            const keys = await page.locator('.piano-key, [id^="key-"]').all();
            
            for (const key of keys) {
                const hasPressed = await key.evaluate(el => el.classList.contains('pressed'));
                const hasActive = await key.evaluate(el => el.classList.contains('active'));
                const transform = await key.evaluate(el => el.style.transform);
                
                if (hasPressed || hasActive) {
                    keyPressDetected = true;
                    console.log('Key press detected on key:', await key.getAttribute('id'));
                }
                
                if (transform && transform.includes('translate')) {
                    transformDetected = true;
                    console.log('Transform detected:', transform);
                }
            }
        };
        
        // Start monitoring key states
        const monitorInterval = setInterval(monitorKeyStates, 50);
        
        // Click the rhythm play button to start playback
        await rhythmPlayButton.click();
        
        // Wait for potential key animations (rhythm patterns typically play within 2-4 seconds)
        await page.waitForTimeout(3000);
        
        // Clear monitoring
        clearInterval(monitorInterval);
        
        // Stop playback if still playing
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
        
        // Verify that key animations were detected
        expect(keyPressDetected).toBe(true);
        console.log('Piano key press animation verification:', keyPressDetected ? 'PASSED' : 'FAILED');
    });

    test('should test different rhythm patterns for key animations', async ({ page }) => {
        const rhythmPatterns = [
            'block',
            'alberti', 
            'waltz',
            'broken',
            'arpeggio',
            'march'
        ];
        
        for (const rhythm of rhythmPatterns) {
            console.log(`Testing rhythm pattern: ${rhythm}`);
            
            // Select the rhythm pattern
            const rhythmSelector = page.locator('select[wire\\:model\\.live="selectedRhythm"]');
            await rhythmSelector.selectOption(rhythm);
            
            // Wait for the change to take effect
            await page.waitForTimeout(500);
            
            // Click play button
            const playButton = page.locator('button.bg-green-600:has-text("Play")');
            await expect(playButton).toBeVisible();
            await playButton.click();
            
            // Monitor for key animations
            let keyAnimationDetected = false;
            let animationCount = 0;
            
            const checkForAnimations = async () => {
                const pressedKeys = await page.locator('.piano-key.pressed, .piano-key.active, [id^="key-"].pressed, [id^="key-"].active').count();
                const transformedKeys = await page.locator('.piano-key, [id^="key-"]').evaluateAll(keys => 
                    keys.filter(key => key.style.transform && key.style.transform.includes('translate')).length
                );
                
                if (pressedKeys > 0 || transformedKeys > 0) {
                    keyAnimationDetected = true;
                    animationCount++;
                    console.log(`Animation detected for ${rhythm}: pressed=${pressedKeys}, transformed=${transformedKeys}`);
                }
            };
            
            // Monitor for 2 seconds
            const monitoringInterval = setInterval(checkForAnimations, 100);
            await page.waitForTimeout(2000);
            clearInterval(monitoringInterval);
            
            // Stop playback
            const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
            if (await stopButton.isVisible()) {
                await stopButton.click();
                await page.waitForTimeout(500);
            }
            
            // Verify animation was detected for this rhythm
            expect(keyAnimationDetected).toBe(true);
            console.log(`${rhythm} pattern animation test:`, keyAnimationDetected ? 'PASSED' : 'FAILED', `(${animationCount} animations detected)`);
        }
    });

    test('should verify pressKey and releaseKey functions work correctly', async ({ page }) => {
        // Test manual chord play to verify key press/release functionality
        const chordPlayButton = page.locator('button.bg-blue-600:has-text("Play Chord")');
        await expect(chordPlayButton).toBeVisible();
        
        // Monitor key states before playing
        const getKeyStates = async () => {
            return await page.evaluate(() => {
                const keys = document.querySelectorAll('.piano-key, [id^="key-"]');
                const states = {};
                keys.forEach(key => {
                    states[key.id] = {
                        pressed: key.classList.contains('pressed'),
                        active: key.classList.contains('active'),
                        transform: key.style.transform
                    };
                });
                return states;
            });
        };
        
        const initialStates = await getKeyStates();
        
        // Click play chord button
        await chordPlayButton.click();
        
        // Wait a short time to allow key press animation
        await page.waitForTimeout(200);
        
        // Check for key press states during playback
        const duringPlayStates = await getKeyStates();
        
        // Wait for key release
        await page.waitForTimeout(2000);
        
        // Check final states
        const finalStates = await getKeyStates();
        
        // Verify that some keys were pressed during playback
        let keyWasPressed = false;
        let transformWasApplied = false;
        let keyWasReleased = false;
        
        for (const keyId in duringPlayStates) {
            const initial = initialStates[keyId];
            const during = duringPlayStates[keyId];
            const final = finalStates[keyId];
            
            if (during.pressed && !initial.pressed) {
                keyWasPressed = true;
                console.log(`Key ${keyId} was pressed during playback`);
            }
            
            if (during.transform && during.transform.includes('translate') && !initial.transform) {
                transformWasApplied = true;
                console.log(`Transform applied to key ${keyId}: ${during.transform}`);
            }
            
            if (during.pressed && !final.pressed) {
                keyWasReleased = true;
                console.log(`Key ${keyId} was released after playback`);
            }
        }
        
        expect(keyWasPressed).toBe(true);
        expect(transformWasApplied).toBe(true);
        expect(keyWasReleased).toBe(true);
        
        console.log('Manual chord play verification:');
        console.log('- Key press detected:', keyWasPressed);
        console.log('- Transform applied:', transformWasApplied);
        console.log('- Key release detected:', keyWasReleased);
    });

    test('should verify visual key depression animations', async ({ page }) => {
        // Test that visual transforms are applied correctly
        await page.waitForTimeout(1000);
        
        // Play a chord to trigger key animations
        const playButton = page.locator('button.bg-blue-600:has-text("Play Chord")');
        await playButton.click();
        
        // Wait for animation to start
        await page.waitForTimeout(100);
        
        // Check for visual depression effects
        const keyVisualStates = await page.evaluate(() => {
            const keys = document.querySelectorAll('.piano-key, [id^="key-"]');
            const visualStates = [];
            
            keys.forEach(key => {
                const computedStyle = window.getComputedStyle(key);
                const transform = key.style.transform || computedStyle.transform;
                const hasActiveClass = key.classList.contains('active') || key.classList.contains('pressed');
                
                if (hasActiveClass || (transform && transform.includes('translate'))) {
                    visualStates.push({
                        id: key.id,
                        transform: transform,
                        hasActiveClass: hasActiveClass,
                        isBlackKey: key.classList.contains('black-key'),
                        translateY: transform.includes('translateY') ? transform.match(/translateY\(([^)]+)\)/)?.[1] : null
                    });
                }
            });
            
            return visualStates;
        });
        
        expect(keyVisualStates.length).toBeGreaterThan(0);
        
        console.log('Visual key depression states detected:');
        keyVisualStates.forEach(state => {
            console.log(`- Key ${state.id}: active=${state.hasActiveClass}, transform=${state.transform}, translateY=${state.translateY}`);
        });
        
        // Verify that translateY is being applied for key depression
        const hasTranslateY = keyVisualStates.some(state => state.translateY);
        expect(hasTranslateY).toBe(true);
        
        // Wait for keys to be released
        await page.waitForTimeout(2000);
        
        // Verify keys are no longer active
        const finalActiveKeys = await page.locator('.piano-key.active, .piano-key.pressed').count();
        expect(finalActiveKeys).toBe(0);
    });

    test('should test BPM changes affect animation timing', async ({ page }) => {
        const bpmValues = ['60', '120', '180'];
        
        for (const bpm of bpmValues) {
            console.log(`Testing BPM: ${bpm}`);
            
            // Change BPM
            const bpmSelector = page.locator('select[wire\\:model\\.live="bpm"]');
            await bpmSelector.selectOption(bpm);
            await page.waitForTimeout(500);
            
            // Set rhythm pattern to something with clear timing
            const rhythmSelector = page.locator('select[wire\\:model\\.live="selectedRhythm"]');
            await rhythmSelector.selectOption('alberti');
            await page.waitForTimeout(500);
            
            // Start playback
            const playButton = page.locator('button.bg-green-600:has-text("Play")');
            await playButton.click();
            
            // Monitor animation frequency
            let animationEvents = [];
            const startTime = Date.now();
            
            const monitorAnimations = async () => {
                const activeKeys = await page.locator('.piano-key.active, .piano-key.pressed').count();
                if (activeKeys > 0) {
                    animationEvents.push(Date.now() - startTime);
                }
            };
            
            // Monitor for 2 seconds
            const interval = setInterval(monitorAnimations, 50);
            await page.waitForTimeout(2000);
            clearInterval(interval);
            
            // Stop playback
            const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
            if (await stopButton.isVisible()) {
                await stopButton.click();
                await page.waitForTimeout(500);
            }
            
            expect(animationEvents.length).toBeGreaterThan(0);
            console.log(`BPM ${bpm}: ${animationEvents.length} animation events detected`);
        }
    });

    test('should verify chord progression changes trigger different key animations', async ({ page }) => {
        const progressions = ['I-IV-V-I', 'I-V-vi-IV', 'ii-V-I'];
        
        for (const progression of progressions) {
            console.log(`Testing progression: ${progression}`);
            
            // Select progression
            const progressionSelector = page.locator('select[wire\\:model\\.live="selectedProgression"]');
            await progressionSelector.selectOption(progression);
            await page.waitForTimeout(500);
            
            // Use block rhythm for clearer chord changes
            const rhythmSelector = page.locator('select[wire\\:model\\.live="selectedRhythm"]');
            await rhythmSelector.selectOption('block');
            await page.waitForTimeout(500);
            
            // Start playback
            const playButton = page.locator('button.bg-green-600:has-text("Play")');
            await playButton.click();
            
            // Track unique key combinations being pressed
            const uniqueKeyPatterns = new Set();
            
            const trackKeyPatterns = async () => {
                const activeKeyIds = await page.evaluate(() => {
                    const activeKeys = document.querySelectorAll('.piano-key.active, .piano-key.pressed, [id^="key-"].active, [id^="key-"].pressed');
                    return Array.from(activeKeys).map(key => key.id).sort().join(',');
                });
                
                if (activeKeyIds) {
                    uniqueKeyPatterns.add(activeKeyIds);
                }
            };
            
            // Monitor for 4 seconds to catch chord changes
            const interval = setInterval(trackKeyPatterns, 100);
            await page.waitForTimeout(4000);
            clearInterval(interval);
            
            // Stop playback
            const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
            if (await stopButton.isVisible()) {
                await stopButton.click();
                await page.waitForTimeout(500);
            }
            
            // Verify we detected multiple different chord patterns
            expect(uniqueKeyPatterns.size).toBeGreaterThan(1);
            console.log(`Progression ${progression}: ${uniqueKeyPatterns.size} unique key patterns detected`);
        }
    });

    test('should verify no console errors during key animations', async ({ page }) => {
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
        
        // Test various rhythm patterns and playback
        const rhythmSelector = page.locator('select[wire\\:model\\.live="selectedRhythm"]');
        
        // Test alberti bass pattern (complex rhythm)
        await rhythmSelector.selectOption('alberti');
        await page.waitForTimeout(500);
        
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await playButton.click();
        
        // Let it play for a few seconds
        await page.waitForTimeout(3000);
        
        // Stop playback
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
        
        // Test manual chord play
        const chordPlayButton = page.locator('button.bg-blue-600:has-text("Play Chord")');
        await chordPlayButton.click();
        await page.waitForTimeout(1000);
        
        // Check for errors
        expect(consoleErrors).toHaveLength(0);
        expect(pageErrors).toHaveLength(0);
        
        if (consoleErrors.length > 0) {
            console.log('Console errors detected:', consoleErrors);
        }
        if (pageErrors.length > 0) {
            console.log('Page errors detected:', pageErrors);
        }
    });
});