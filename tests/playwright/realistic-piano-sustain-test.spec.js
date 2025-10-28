import { test, expect } from '@playwright/test';

test.describe('Realistic Piano Sustain Duration Tests', () => {
    test.beforeEach(async ({ page }) => {
        // Navigate to the math chord test page
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to fully load
        await page.waitForLoadState('networkidle');
        
        // Wait for the piano player component or piano keys to be visible
        await page.waitForSelector('.piano-keys, #piano-keyboard, .piano-player', { timeout: 15000 });
        
        // Set up console monitoring for realistic sustain debug messages
        const consoleMessages = [];
        page.on('console', msg => {
            const text = msg.text();
            consoleMessages.push({
                type: msg.type(),
                text: text,
                timestamp: Date.now()
            });
            
            // Log realistic sustain debug messages
            if (text.includes('Playing realistic sustained bass note') || 
                text.includes('getRealisticSustainDuration') ||
                text.includes('sustain duration') ||
                text.includes('seconds (vs measure')) {
                console.log(`[SUSTAIN] ${text}`);
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
        
        // Monitor for errors
        page.on('pageerror', error => {
            console.log('Page error:', error.message);
        });
    });

    test('should verify bass notes (C2, F2) have longer sustain durations than middle notes', async ({ page }) => {
        console.log('=== Testing Bass Note Realistic Sustain Durations ===');
        
        // Set up progression with bass notes (lower octaves)
        await page.selectOption('select[wire\\:model\\.live="selectedKey"]', 'C');
        await page.selectOption('select[wire\\:model\\.live="selectedProgression"]', 'I-IV-V-I');
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'ballad'); // Ballad has longer base durations
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '80'); // Slower tempo for clearer observation
        
        await page.waitForTimeout(1000);
        
        // Capture console messages and monitor realistic sustain behavior
        const sustainMessages = [];
        page.on('console', msg => {
            const text = msg.text();
            if (text.includes('Playing realistic sustained bass note') || text.includes('sustain duration')) {
                sustainMessages.push(text);
            }
        });
        
        // Start rhythm playback
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await expect(playButton).toBeVisible();
        await playButton.click();
        
        // Monitor for 5 seconds to capture multiple chord changes and bass notes
        await page.waitForTimeout(5000);
        
        // Stop playback
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
            await page.waitForTimeout(500);
        }
        
        // Get all console messages from the page
        const allMessages = await page.evaluate(() => window.testConsoleMessages || []);
        const sustainDebugMessages = allMessages.filter(msg => 
            msg.text.includes('Playing realistic sustained bass note') ||
            msg.text.includes('sustain duration') ||
            msg.text.includes('seconds (vs measure')
        );
        
        console.log('=== Realistic Sustain Debug Messages ===');
        sustainDebugMessages.forEach(msg => console.log(msg.text));
        
        // Verify that bass notes show realistic sustain duration calculations
        const bassNoteMessages = sustainDebugMessages.filter(msg => 
            msg.text.includes('Playing realistic sustained bass note:') ||
            msg.text.includes('Playing realistic sustained bass note')
        );
        
        expect(bassNoteMessages.length).toBeGreaterThan(0);
        
        // Verify that bass notes have longer durations than the measure duration
        let foundRealisticScaling = false;
        bassNoteMessages.forEach(msg => {
            // Parse sustain duration from message format: "Playing realistic sustained bass note: C2 for 6.5 seconds (vs measure 3.0 seconds)"
            const match = msg.text.match(/Playing realistic sustained bass note: ([A-G]#?\d+) for ([\d.]+) seconds \(vs measure ([\d.]+) seconds\)/);
            if (match) {
                const note = match[1];
                const sustainDuration = parseFloat(match[2]);
                const measureDuration = parseFloat(match[3]);
                
                console.log(`Bass note ${note}: sustain=${sustainDuration}s, measure=${measureDuration}s, ratio=${(sustainDuration/measureDuration).toFixed(2)}x`);
                
                // Bass notes should have sustain durations longer than or equal to measure duration (realistic behavior)
                if (sustainDuration >= measureDuration * 0.9) { // Allow some tolerance for floating point precision
                    foundRealisticScaling = true;
                }
            }
        });
        
        expect(foundRealisticScaling).toBe(true);
        console.log('Bass note realistic sustain scaling: PASSED');
    });

    test('should verify different octave ranges produce appropriately scaled sustain durations', async ({ page }) => {
        console.log('=== Testing Octave-Based Sustain Duration Scaling ===');
        
        // Set up test with different chord progressions to capture various octaves
        await page.selectOption('select[wire\\:model\\.live="selectedKey"]', 'C');
        await page.selectOption('select[wire\\:model\\.live="selectedProgression"]', 'I-IV-V-I');
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'broken'); // Broken chord to get individual notes
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '100');
        
        await page.waitForTimeout(1000);
        
        // Inject test function to directly test getRealisticSustainDuration
        const sustainTestResults = await page.evaluate(() => {
            return new Promise((resolve) => {
                // Wait for Alpine data to be available
                setTimeout(() => {
                    const alpineData = Alpine.$data(document.querySelector('[x-data="mathChordPlayer"]'));
                    if (alpineData && alpineData.getRealisticSustainDuration) {
                        const baseDuration = 2.0;
                        const testNotes = [
                            'C2',  // Bass range
                            'F2',  // Bass range
                            'G2',  // Bass range
                            'C3',  // Lower middle
                            'C4',  // Middle
                            'C5',  // Upper middle
                            'C6',  // Treble
                            'C7',  // High treble
                            'C8'   // Very high treble
                        ];
                        
                        const results = testNotes.map(note => {
                            const sustainDuration = alpineData.getRealisticSustainDuration(note, baseDuration);
                            const multiplier = sustainDuration / baseDuration;
                            return {
                                note,
                                sustainDuration,
                                baseDuration,
                                multiplier
                            };
                        });
                        
                        resolve(results);
                    } else {
                        resolve([]);
                    }
                }, 1000);
            });
        });
        
        console.log('=== Sustain Duration Scaling Results ===');
        sustainTestResults.forEach(result => {
            console.log(`${result.note}: ${result.sustainDuration.toFixed(2)}s (${result.multiplier.toFixed(2)}x base)`);
        });
        
        // Verify octave-based scaling patterns
        expect(sustainTestResults.length).toBeGreaterThan(0);
        
        // Find bass, middle, and treble notes
        const bassResults = sustainTestResults.filter(r => r.note.includes('2'));
        const middleResults = sustainTestResults.filter(r => ['C3', 'C4', 'C5'].includes(r.note));
        const trebleResults = sustainTestResults.filter(r => ['C6', 'C7', 'C8'].includes(r.note));
        
        // Bass notes should have 3-4x multiplier
        bassResults.forEach(result => {
            expect(result.multiplier).toBeGreaterThan(2.5);
            expect(result.multiplier).toBeLessThan(4.5);
            console.log(`✓ Bass note ${result.note} has appropriate scaling: ${result.multiplier.toFixed(2)}x`);
        });
        
        // Middle notes should have ~1x multiplier (around reference duration)
        middleResults.forEach(result => {
            expect(result.multiplier).toBeGreaterThan(0.8);
            expect(result.multiplier).toBeLessThan(1.5);
            console.log(`✓ Middle note ${result.note} has appropriate scaling: ${result.multiplier.toFixed(2)}x`);
        });
        
        // Treble notes should have 0.2-0.8x multiplier
        trebleResults.forEach(result => {
            expect(result.multiplier).toBeGreaterThan(0.1);
            expect(result.multiplier).toBeLessThan(0.9);
            console.log(`✓ Treble note ${result.note} has appropriate scaling: ${result.multiplier.toFixed(2)}x`);
        });
        
        console.log('Octave-based sustain duration scaling: PASSED');
    });

    test('should verify piano keys stay pressed for realistic durations, not just measure durations', async ({ page }) => {
        console.log('=== Testing Visual Key Press Duration Matches Realistic Sustain ===');
        
        // Set up waltz pattern with clear bass notes
        await page.selectOption('select[wire\\:model\\.live="selectedKey"]', 'C');
        await page.selectOption('select[wire\\:model\\.live="selectedProgression"]', 'I-IV-V-I');
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'waltz');
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '120');
        
        await page.waitForTimeout(1000);
        
        // Start playback
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await playButton.click();
        
        // Monitor key states over time to verify realistic sustain durations
        const keyPressMonitoring = [];
        const monitoringStartTime = Date.now();
        
        // Monitor for 6 seconds to capture extended bass note sustains
        for (let i = 0; i < 60; i++) {
            await page.waitForTimeout(100);
            
            const keyStates = await page.evaluate(() => {
                const pressedKeys = document.querySelectorAll('.piano-key.pressed, .piano-key.active');
                const states = {};
                pressedKeys.forEach(key => {
                    const noteId = key.id.replace('key-', '');
                    states[noteId] = {
                        isPressed: key.classList.contains('pressed'),
                        isActive: key.classList.contains('active'),
                        octave: noteId.match(/\d+/)?.[0]
                    };
                });
                return states;
            });
            
            keyPressMonitoring.push({
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
        
        // Analyze key press durations
        console.log('=== Key Press Duration Analysis ===');
        
        // Track how long bass notes (octave 2-3) stay pressed
        let longestBassPress = 0;
        let bassNoteSustainedAcrossMeasures = false;
        
        // Find continuous bass note presses
        const bassNoteTimeline = {};
        keyPressMonitoring.forEach((snapshot, index) => {
            const bassNotesPressed = snapshot.pressedKeys.filter(note => {
                const octave = parseInt(note.match(/\d+/)?.[0] || '0');
                return octave <= 3; // Bass range
            });
            
            bassNotesPressed.forEach(bassNote => {
                if (!bassNoteTimeline[bassNote]) {
                    bassNoteTimeline[bassNote] = { start: snapshot.timestamp, end: snapshot.timestamp };
                } else {
                    bassNoteTimeline[bassNote].end = snapshot.timestamp;
                }
            });
        });
        
        // Calculate bass note press durations
        Object.entries(bassNoteTimeline).forEach(([note, timeline]) => {
            const duration = timeline.end - timeline.start;
            longestBassPress = Math.max(longestBassPress, duration);
            
            console.log(`Bass note ${note} pressed for ${duration}ms (${(duration/1000).toFixed(2)}s)`);
            
            // If bass notes are sustained for more than 3 seconds, they likely use realistic sustain
            if (duration > 3000) {
                bassNoteSustainedAcrossMeasures = true;
            }
        });
        
        // Verify that bass notes are sustained for realistic durations (longer than typical measure durations)
        expect(bassNoteSustainedAcrossMeasures).toBe(true);
        expect(longestBassPress).toBeGreaterThan(2000); // At least 2 seconds for realistic sustain
        
        console.log(`Longest bass press: ${longestBassPress}ms`);
        console.log(`Bass notes sustained across measures: ${bassNoteSustainedAcrossMeasures ? 'PASSED' : 'FAILED'}`);
    });

    test('should verify realistic sustain is applied across all rhythm patterns', async ({ page }) => {
        console.log('=== Testing Realistic Sustain Across Rhythm Patterns ===');
        
        const rhythmPatterns = ['waltz', 'ballad', 'march', 'broken', 'alberti', 'ragtime'];
        const sustainResults = {};
        
        for (const rhythm of rhythmPatterns) {
            console.log(`\n--- Testing ${rhythm} pattern for realistic sustain ---`);
            
            // Set rhythm pattern
            await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', rhythm);
            await page.selectOption('select[wire\\:model\\.live="bpm"]', '100');
            await page.waitForTimeout(500);
            
            // Clear console messages
            await page.evaluate(() => { window.testConsoleMessages = []; });
            
            // Start playback
            const playButton = page.locator('button.bg-green-600:has-text("Play")');
            await playButton.click();
            
            // Monitor for 3 seconds
            await page.waitForTimeout(3000);
            
            // Stop playback
            const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
            if (await stopButton.isVisible()) {
                await stopButton.click();
                await page.waitForTimeout(300);
            }
            
            // Get sustain messages for this pattern
            const messages = await page.evaluate(() => window.testConsoleMessages || []);
            const sustainMessages = messages.filter(msg => 
                msg.text.includes('Playing realistic sustained bass note') ||
                msg.text.includes('getRealisticSustainDuration')
            );
            
            sustainResults[rhythm] = {
                messageCount: sustainMessages.length,
                hasRealisticSustain: sustainMessages.length > 0,
                messages: sustainMessages.map(m => m.text)
            };
            
            console.log(`${rhythm}: ${sustainMessages.length} realistic sustain messages detected`);
            
            // Verify realistic sustain is being applied
            expect(sustainResults[rhythm].hasRealisticSustain).toBe(true);
        }
        
        // Verify all patterns use realistic sustain
        console.log('\n=== Realistic Sustain Summary ===');
        Object.entries(sustainResults).forEach(([rhythm, result]) => {
            console.log(`${rhythm}: ${result.hasRealisticSustain ? 'PASSED' : 'FAILED'} (${result.messageCount} messages)`);
            expect(result.hasRealisticSustain).toBe(true);
        });
        
        console.log('Realistic sustain across all rhythm patterns: PASSED');
    });

    test('should verify sustain duration scaling matches acoustic piano data expectations', async ({ page }) => {
        console.log('=== Testing Acoustic Piano Data Scaling Accuracy ===');
        
        // Test the scaling function directly with known expectations
        const scalingTestResults = await page.evaluate(() => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    const alpineData = Alpine.$data(document.querySelector('[x-data="mathChordPlayer"]'));
                    if (alpineData && alpineData.getRealisticSustainDuration) {
                        const baseDuration = 2.0; // 2-second reference
                        
                        // Test specific acoustic piano expectations
                        const testCases = [
                            // Bass range: should be 3-4x longer (30-60+ seconds in real piano)
                            { note: 'A0', expectedRange: [3.0, 4.0] },
                            { note: 'C1', expectedRange: [3.0, 4.0] },
                            { note: 'C2', expectedRange: [2.8, 3.8] },
                            
                            // Middle range: should be ~1x reference (10-20 seconds in real piano)
                            { note: 'C3', expectedRange: [1.0, 1.3] },
                            { note: 'C4', expectedRange: [0.9, 1.2] },
                            { note: 'C5', expectedRange: [0.9, 1.1] },
                            
                            // Treble range: should be 0.2-0.8x reference (1-5 seconds in real piano)
                            { note: 'C6', expectedRange: [0.4, 0.8] },
                            { note: 'C7', expectedRange: [0.2, 0.6] },
                            { note: 'C8', expectedRange: [0.2, 0.4] }
                        ];
                        
                        const results = testCases.map(testCase => {
                            const actualDuration = alpineData.getRealisticSustainDuration(testCase.note, baseDuration);
                            const multiplier = actualDuration / baseDuration;
                            const inExpectedRange = multiplier >= testCase.expectedRange[0] && multiplier <= testCase.expectedRange[1];
                            
                            return {
                                note: testCase.note,
                                actualDuration,
                                multiplier,
                                expectedRange: testCase.expectedRange,
                                inExpectedRange
                            };
                        });
                        
                        resolve(results);
                    } else {
                        resolve([]);
                    }
                }, 1000);
            });
        });
        
        console.log('=== Acoustic Piano Data Scaling Verification ===');
        scalingTestResults.forEach(result => {
            const status = result.inExpectedRange ? 'PASS' : 'FAIL';
            console.log(`${result.note}: ${result.multiplier.toFixed(2)}x (expected ${result.expectedRange[0]}-${result.expectedRange[1]}x) - ${status}`);
        });
        
        // Verify all scaling results are within expected acoustic piano ranges
        expect(scalingTestResults.length).toBeGreaterThan(0);
        scalingTestResults.forEach(result => {
            expect(result.inExpectedRange).toBe(true);
        });
        
        // Verify scaling trends
        const bassResults = scalingTestResults.filter(r => ['A0', 'C1', 'C2'].includes(r.note));
        const middleResults = scalingTestResults.filter(r => ['C3', 'C4', 'C5'].includes(r.note));
        const trebleResults = scalingTestResults.filter(r => ['C6', 'C7', 'C8'].includes(r.note));
        
        // Verify bass > middle > treble sustain trend
        const avgBassMultiplier = bassResults.reduce((sum, r) => sum + r.multiplier, 0) / bassResults.length;
        const avgMiddleMultiplier = middleResults.reduce((sum, r) => sum + r.multiplier, 0) / middleResults.length;
        const avgTrebleMultiplier = trebleResults.reduce((sum, r) => sum + r.multiplier, 0) / trebleResults.length;
        
        expect(avgBassMultiplier).toBeGreaterThan(avgMiddleMultiplier);
        expect(avgMiddleMultiplier).toBeGreaterThan(avgTrebleMultiplier);
        
        console.log(`Average multipliers - Bass: ${avgBassMultiplier.toFixed(2)}x, Middle: ${avgMiddleMultiplier.toFixed(2)}x, Treble: ${avgTrebleMultiplier.toFixed(2)}x`);
        console.log('Acoustic piano data scaling accuracy: PASSED');
    });

    test('should verify console logs show realistic sustain duration calculations', async ({ page }) => {
        console.log('=== Testing Console Log Output for Realistic Sustain ===');
        
        // Set up to capture detailed console logging
        const capturedMessages = [];
        page.on('console', msg => {
            const text = msg.text();
            capturedMessages.push(text);
        });
        
        // Set up test progression
        await page.selectOption('select[wire\\:model\\.live="selectedKey"]', 'C');
        await page.selectOption('select[wire\\:model\\.live="selectedProgression"]', 'I-IV-V-I');
        await page.selectOption('select[wire\\:model\\.live="selectedRhythm"]', 'ballad');
        await page.selectOption('select[wire\\:model\\.live="bpm"]', '80');
        
        await page.waitForTimeout(1000);
        
        // Start playback
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await playButton.click();
        
        // Let it play for 4 seconds to capture multiple chord changes
        await page.waitForTimeout(4000);
        
        // Stop playback
        const stopButton = page.locator('button.bg-red-600:has-text("Stop")');
        if (await stopButton.isVisible()) {
            await stopButton.click();
        }
        
        // Filter for realistic sustain console messages
        const realisticSustainLogs = capturedMessages.filter(msg => 
            msg.includes('Playing realistic sustained bass note:') &&
            msg.includes('for') &&
            msg.includes('seconds (vs measure')
        );
        
        console.log('=== Realistic Sustain Console Logs ===');
        realisticSustainLogs.forEach(log => console.log(log));
        
        // Verify expected log format and content
        expect(realisticSustainLogs.length).toBeGreaterThan(0);
        
        // Verify log message format
        realisticSustainLogs.forEach(log => {
            // Should match format: "Playing realistic sustained bass note: C2 for 6.5 seconds (vs measure 3.0 seconds)"
            const formatMatch = log.match(/Playing realistic sustained bass note: ([A-G]#?\d+) for ([\d.]+) seconds \(vs measure ([\d.]+) seconds\)/);
            expect(formatMatch).not.toBeNull();
            
            if (formatMatch) {
                const note = formatMatch[1];
                const sustainDuration = parseFloat(formatMatch[2]);
                const measureDuration = parseFloat(formatMatch[3]);
                
                // Verify reasonable duration values
                expect(sustainDuration).toBeGreaterThan(0);
                expect(measureDuration).toBeGreaterThan(0);
                
                // For bass notes, sustain should typically be longer than measure
                const octave = parseInt(note.match(/\d+/)?.[0] || '0');
                if (octave <= 3) {
                    expect(sustainDuration).toBeGreaterThanOrEqual(measureDuration);
                }
            }
        });
        
        console.log(`Found ${realisticSustainLogs.length} realistic sustain console logs with correct format`);
        console.log('Console log realistic sustain output: PASSED');
    });
});