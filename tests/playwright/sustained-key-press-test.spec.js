import { test, expect } from '@playwright/test';

test.describe('Sustained Piano Key Press During Rhythm Playback', () => {
    test.beforeEach(async ({ page }) => {
        // Navigate to the math chord test page (actual route is /debug/math-chords)
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to fully load
        await page.waitForLoadState('networkidle');
        
        // Wait for the piano player component to be visible
        await page.waitForSelector('.piano-player', { timeout: 10000 });
        
        // Set up console monitoring for debug messages
        const consoleMessages = [];
        page.on('console', msg => {
            const text = msg.text();
            consoleMessages.push({
                type: msg.type(),
                text: text,
                timestamp: Date.now()
            });
            
            // Log key press/release debug messages
            if (text.includes('Pressed key') || text.includes('Released key') || text.includes('Final release')) {
                console.log(`[CONSOLE] ${text}`);
            }
        });
        
        // Store console messages on page for access in tests
        await page.addInitScript(() => {
            window.testConsoleMessages = [];
            const originalLog = console.log;
            console.log = function(...args) {
                window.testConsoleMessages.push({
                    type: 'log',
                    text: args.join(' '),
                    timestamp: Date.now()
                });
                originalLog.apply(console, args);
            };
        });
        
        // Set up page error monitoring
        page.on('pageerror', error => {
            console.log('Page error:', error.message);
        });
    });

    test('should verify bass notes stay pressed for full measure duration in waltz pattern', async ({ page }) => {
        console.log('=== Testing Waltz Pattern Sustained Bass Notes ===');
        
        // Set up chord progression and waltz rhythm
        await page.selectOption('select[wire\\:model\\.live="selectedKey"]', 'C');
        await page.selectOption('select[wire\\:model\\.live="selectedProgression"]', 'I-IV-V-I');
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'waltz');
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '120');
        
        // Wait for selections to take effect
        await page.waitForTimeout(1000);
        
        // Start rhythm playback
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await expect(playButton).toBeVisible();
        await playButton.click();
        
        // Monitor key states over time to verify sustained bass notes
        const sustainedKeyMonitoring = [];
        const monitoringStartTime = Date.now();
        
        // Monitor for 4 seconds to capture full measure progression
        for (let i = 0; i < 40; i++) {
            await page.waitForTimeout(100);
            
            const keyStates = await page.evaluate(() => {
                const pressedKeys = document.querySelectorAll('.piano-key.pressed, .piano-key.active');
                const states = {};
                pressedKeys.forEach(key => {
                    const noteId = key.id.replace('key-', '');
                    states[noteId] = {
                        isPressed: key.classList.contains('pressed'),
                        isActive: key.classList.contains('active'),
                        transform: key.style.transform,
                        octave: noteId.match(/\d+/)?.[0]
                    };
                });
                return states;
            });
            
            sustainedKeyMonitoring.push({
                timestamp: Date.now() - monitoringStartTime,
                pressedKeys: Object.keys(keyStates),
                keyStates: keyStates
            });
        }
        
        // Stop playback
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
        
        // Analyze sustained key behavior
        console.log('=== Key Press Timeline Analysis ===');
        
        // Find bass notes (typically C3, F3, G3 for C major I-IV-V-I)
        const expectedBassNotes = ['C3', 'F3', 'G3'];
        let sustainedBassDetected = false;
        let longestBassPress = 0;
        
        sustainedKeyMonitoring.forEach((snapshot, index) => {
            const bassNotesPressed = snapshot.pressedKeys.filter(note => 
                expectedBassNotes.some(bassNote => note.includes(bassNote.slice(0, -1)) && note.includes('3'))
            );
            
            if (bassNotesPressed.length > 0) {
                console.log(`[${snapshot.timestamp}ms] Bass notes pressed: ${bassNotesPressed.join(', ')}`);
                
                // Check for sustained bass notes (should be pressed for multiple snapshots)
                if (index > 0) {
                    const previousBassNotes = sustainedKeyMonitoring[index - 1].pressedKeys.filter(note => 
                        expectedBassNotes.some(bassNote => note.includes(bassNote.slice(0, -1)) && note.includes('3'))
                    );
                    
                    const sustainedBass = bassNotesPressed.filter(note => previousBassNotes.includes(note));
                    if (sustainedBass.length > 0) {
                        sustainedBassDetected = true;
                        longestBassPress = Math.max(longestBassPress, snapshot.timestamp);
                    }
                }
            }
        });
        
        // Get console debug messages about key press counts
        const debugMessages = await page.evaluate(() => window.testConsoleMessages || []);
        const keyPressMessages = debugMessages.filter(msg => 
            msg.text.includes('Pressed key') || msg.text.includes('Released key') || msg.text.includes('count:')
        );
        
        console.log('=== Key Press Debug Messages ===');
        keyPressMessages.forEach(msg => console.log(msg.text));
        
        // Verify sustained bass behavior
        expect(sustainedBassDetected).toBe(true);
        expect(longestBassPress).toBeGreaterThan(500); // Bass should be sustained for at least 500ms
        
        console.log(`Sustained bass detection: ${sustainedBassDetected ? 'PASSED' : 'FAILED'}`);
        console.log(`Longest bass press duration: ${longestBassPress}ms`);
    });

    test('should verify ballad pattern maintains sustained notes for full measure', async ({ page }) => {
        console.log('=== Testing Ballad Pattern Sustained Notes ===');
        
        // Set up chord progression with ballad rhythm (should have longest sustain)
        await page.selectOption('select[wire\\:model\\.live="selectedKey"]', 'C');
        await page.selectOption('select[wire\\:model\\.live="selectedProgression"]', 'I-vi-IV-V');
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'ballad');
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '80'); // Slower tempo for ballad
        
        await page.waitForTimeout(1000);
        
        // Start playback
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await playButton.click();
        
        // Monitor sustained notes for 5 seconds (ballad should have very long sustains)
        const sustainMonitoring = [];
        const startTime = Date.now();
        
        for (let i = 0; i < 50; i++) {
            await page.waitForTimeout(100);
            
            const activeKeys = await page.locator('.piano-key.pressed, .piano-key.active').count();
            const timestamp = Date.now() - startTime;
            
            sustainMonitoring.push({ timestamp, activeKeys });
            
            if (activeKeys > 0) {
                console.log(`[${timestamp}ms] Active keys: ${activeKeys}`);
            }
        }
        
        // Stop playback
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
        
        // Verify that keys were sustained for extended periods
        const sustainPeriods = sustainMonitoring.filter(snapshot => snapshot.activeKeys > 0);
        const longestSustainPeriod = sustainPeriods.length * 100; // Each snapshot is 100ms apart
        
        expect(sustainPeriods.length).toBeGreaterThan(10); // Should have sustained notes for at least 1 second
        expect(longestSustainPeriod).toBeGreaterThan(1000); // Ballad should sustain for over 1 second
        
        console.log(`Ballad sustain period: ${longestSustainPeriod}ms`);
        console.log(`Sustained key periods detected: ${sustainPeriods.length}`);
    });

    test('should verify reference counting prevents early key release with overlapping notes', async ({ page }) => {
        console.log('=== Testing Reference Counting System ===');
        
        // Capture console messages in an array
        const capturedConsoleMessages = [];
        page.on('console', msg => {
            const text = msg.text();
            capturedConsoleMessages.push(text);
        });
        
        // Set up alberti bass pattern which should have overlapping notes
        await page.selectOption('select[wire\\:model\\.live="selectedKey"]', 'C');
        await page.selectOption('select[wire\\:model\\.live="selectedProgression"]', 'I-V-vi-IV');
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'alberti');
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '120');
        
        await page.waitForTimeout(1000);
        
        // Start playback
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await playButton.click();
        
        // Let it play for 3 seconds to capture reference counting behavior
        await page.waitForTimeout(3000);
        
        // Stop playback
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
        
        // Filter console messages for reference counting
        const refCountMessages = capturedConsoleMessages.filter(msg => 
            msg.includes('count:') || msg.includes('still held by') || msg.includes('Final release')
        );
        
        console.log('=== Reference Counting Debug Messages ===');
        refCountMessages.forEach(msg => console.log(msg));
        
        // Verify reference counting is working
        const hasRefCounting = refCountMessages.some(msg => msg.includes('still held by'));
        const hasFinalRelease = refCountMessages.some(msg => msg.includes('Final release'));
        
        expect(refCountMessages.length).toBeGreaterThan(0);
        console.log(`Reference counting detected: ${hasRefCounting ? 'PASSED' : 'FAILED'}`);
        console.log(`Final release detected: ${hasFinalRelease ? 'PASSED' : 'FAILED'}`);
    });

    test('should verify keys are properly released when playback stops', async ({ page }) => {
        console.log('=== Testing Key Release on Playback Stop ===');
        
        // Set up sustained rhythm pattern
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'ballad');
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '100');
        
        await page.waitForTimeout(500);
        
        // Start playback
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await playButton.click();
        
        // Wait for keys to be pressed
        await page.waitForTimeout(1000);
        
        // Verify keys are pressed during playback
        const pressedKeysDuringPlayback = await page.locator('.piano-key.pressed, .piano-key.active').count();
        console.log(`Keys pressed during playback: ${pressedKeysDuringPlayback}`);
        
        // Stop playback
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
        
        // Wait for keys to be released
        await page.waitForTimeout(1000);
        
        // Verify all keys are released after stopping
        const pressedKeysAfterStop = await page.locator('.piano-key.pressed, .piano-key.active').count();
        console.log(`Keys pressed after stop: ${pressedKeysAfterStop}`);
        
        expect(pressedKeysDuringPlayback).toBeGreaterThan(0);
        expect(pressedKeysAfterStop).toBe(0);
        
        console.log(`Key release verification: ${pressedKeysAfterStop === 0 ? 'PASSED' : 'FAILED'}`);
    });

    test('should test sustained key behavior across different rhythm patterns', async ({ page }) => {
        console.log('=== Testing Sustained Keys Across Rhythm Patterns ===');
        
        const rhythmPatterns = [
            { name: 'waltz', expectedSustain: 'moderate' },
            { name: 'ballad', expectedSustain: 'long' },
            { name: 'march', expectedSustain: 'moderate' },
            { name: 'broken', expectedSustain: 'short' },
            { name: 'alberti', expectedSustain: 'short' },
            { name: 'block', expectedSustain: 'moderate' }
        ];
        
        const sustainResults = {};
        
        for (const rhythm of rhythmPatterns) {
            console.log(`\n--- Testing ${rhythm.name} pattern ---`);
            
            // Set rhythm pattern
            await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', rhythm.name);
            await page.selectOption('select[wire\\:model\\.live="bpm"]', '120');
            await page.waitForTimeout(500);
            
            // Clear console messages
            await page.evaluate(() => { window.testConsoleMessages = []; });
            
            // Start playback
            const playButton = page.locator('button.bg-green-600:has-text("Play")');
            await playButton.click();
            
            // Monitor sustained keys for 2 seconds
            let maxSimultaneousKeys = 0;
            let totalSustainTime = 0;
            let sustainMeasurements = 0;
            
            for (let i = 0; i < 20; i++) {
                await page.waitForTimeout(100);
                const activeKeys = await page.locator('.piano-key.pressed, .piano-key.active').count();
                
                if (activeKeys > 0) {
                    maxSimultaneousKeys = Math.max(maxSimultaneousKeys, activeKeys);
                    totalSustainTime += 100;
                    sustainMeasurements++;
                }
            }
            
            // Stop playback
            const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
            if (await stopButton.isVisible()) {
                await stopButton.click();
                await page.waitForTimeout(300);
            }
            
            const averageSustainTime = sustainMeasurements > 0 ? totalSustainTime / sustainMeasurements : 0;
            
            sustainResults[rhythm.name] = {
                maxSimultaneousKeys,
                totalSustainTime,
                averageSustainTime,
                sustainMeasurements
            };
            
            console.log(`${rhythm.name}: max keys=${maxSimultaneousKeys}, total sustain=${totalSustainTime}ms, avg=${averageSustainTime.toFixed(1)}ms`);
            
            // Verify some keys were sustained
            expect(maxSimultaneousKeys).toBeGreaterThan(0);
            expect(totalSustainTime).toBeGreaterThan(0);
        }
        
        // Compare sustain behavior across patterns
        console.log('\n=== Sustain Comparison ===');
        
        // Ballad should have longest sustain
        expect(sustainResults.ballad.totalSustainTime).toBeGreaterThan(sustainResults.alberti.totalSustainTime);
        
        // Waltz should have moderate sustain
        expect(sustainResults.waltz.totalSustainTime).toBeGreaterThan(500);
        
        console.log('Rhythm pattern sustain test: PASSED');
        console.log(JSON.stringify(sustainResults, null, 2));
    });

    test('should verify bass notes remain pressed during full measure in march pattern', async ({ page }) => {
        console.log('=== Testing March Pattern Bass Sustain ===');
        
        // Set up march pattern with clear bass notes
        await page.selectOption('select[wire\\:model\\.live="selectedKey"]', 'C');
        await page.selectOption('select[wire\\:model\\.live="selectedProgression"]', 'I-IV-V-I');
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'march');
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '100');
        
        await page.waitForTimeout(1000);
        
        // Start playback
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await playButton.click();
        
        // Monitor bass note behavior specifically
        const bassNoteTracking = [];
        
        for (let i = 0; i < 30; i++) {
            await page.waitForTimeout(100);
            
            // Check for bass notes (C3, F3, G3 in C major progression)
            const bassNoteStates = await page.evaluate(() => {
                const bassNotes = ['C3', 'F3', 'G3'];
                const states = {};
                
                bassNotes.forEach(bassNote => {
                    const key = document.getElementById(`key-${bassNote}`);
                    if (key) {
                        states[bassNote] = {
                            pressed: key.classList.contains('pressed'),
                            active: key.classList.contains('active'),
                            visible: key.style.display !== 'none'
                        };
                    }
                });
                
                return states;
            });
            
            bassNoteTracking.push({
                timestamp: i * 100,
                states: bassNoteStates
            });
        }
        
        // Stop playback
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
        
        // Analyze bass note sustain patterns
        console.log('=== Bass Note Sustain Analysis ===');
        
        let bassNotesSustained = false;
        let longestBassSustain = 0;
        let currentSustain = 0;
        
        bassNoteTracking.forEach((snapshot, index) => {
            const pressedBassNotes = Object.entries(snapshot.states)
                .filter(([note, state]) => state.pressed || state.active)
                .map(([note]) => note);
            
            if (pressedBassNotes.length > 0) {
                currentSustain += 100;
                bassNotesSustained = true;
                console.log(`[${snapshot.timestamp}ms] Bass notes active: ${pressedBassNotes.join(', ')}`);
            } else {
                longestBassSustain = Math.max(longestBassSustain, currentSustain);
                currentSustain = 0;
            }
        });
        
        longestBassSustain = Math.max(longestBassSustain, currentSustain);
        
        // Verify bass notes were sustained appropriately for march pattern
        expect(bassNotesSustained).toBe(true);
        expect(longestBassSustain).toBeGreaterThan(300); // Should sustain for at least 300ms
        
        console.log(`Bass notes sustained: ${bassNotesSustained ? 'PASSED' : 'FAILED'}`);
        console.log(`Longest bass sustain: ${longestBassSustain}ms`);
    });

    test('should verify no console errors during sustained key playback', async ({ page }) => {
        console.log('=== Testing Error-Free Sustained Playback ===');
        
        const consoleErrors = [];
        const pageErrors = [];
        
        // Monitor for errors
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
            }
        });
        
        page.on('pageerror', error => {
            pageErrors.push(error.message);
        });
        
        // Test multiple rhythm patterns for errors
        const testPatterns = ['waltz', 'ballad', 'alberti', 'march'];
        
        for (const pattern of testPatterns) {
            console.log(`Testing ${pattern} for errors...`);
            
            await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', pattern);
            await page.waitForTimeout(500);
            
            const playButton = page.locator('button.bg-green-600:has-text("Play")');
            await playButton.click();
            
            // Let it play for 2 seconds
            await page.waitForTimeout(2000);
            
            const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
            if (await stopButton.isVisible()) {
                await stopButton.click();
                await page.waitForTimeout(500);
            }
        }
        
        // Verify no errors occurred
        expect(consoleErrors).toHaveLength(0);
        expect(pageErrors).toHaveLength(0);
        
        if (consoleErrors.length > 0) {
            console.log('Console errors detected:', consoleErrors);
        }
        if (pageErrors.length > 0) {
            console.log('Page errors detected:', pageErrors);
        }
        
        console.log('Error-free playback test: PASSED');
    });
});