import { test, expect } from '@playwright/test';

test.describe('Play Button Debug', () => {
    let consoleMessages = [];
    let errorMessages = [];
    let networkRequests = [];

    test.beforeEach(async ({ page }) => {
        // Clear message arrays
        consoleMessages = [];
        errorMessages = [];
        networkRequests = [];

        // Monitor console messages
        page.on('console', msg => {
            const message = {
                type: msg.type(),
                text: msg.text(),
                location: msg.location()
            };
            consoleMessages.push(message);
            console.log(`[CONSOLE ${msg.type().toUpperCase()}] ${msg.text()}`);
        });

        // Monitor page errors
        page.on('pageerror', error => {
            const errorInfo = {
                message: error.message,
                stack: error.stack,
                name: error.name
            };
            errorMessages.push(errorInfo);
            console.log(`[PAGE ERROR] ${error.message}`);
        });

        // Monitor network requests
        page.on('request', request => {
            networkRequests.push({
                url: request.url(),
                method: request.method(),
                resourceType: request.resourceType()
            });
        });

        // Navigate to the math chord test page
        await page.goto('http://localhost:8000/debug/math-chords');
        
        // Wait for the page to fully load
        await page.waitForLoadState('networkidle');
    });

    test('should debug Play button functionality step by step', async ({ page }) => {
        console.log('=== STARTING PLAY BUTTON DEBUG TEST ===');

        // Step 1: Verify page loaded correctly
        console.log('STEP 1: Verifying page load...');
        await expect(page).toHaveTitle(/ChordHound/);
        
        // Step 2: Find the Play button
        console.log('STEP 2: Looking for Play button...');
        const playButton = page.locator('button.bg-green-600:has-text("Play")');
        await expect(playButton).toBeVisible();
        console.log('✓ Play button found and visible');

        // Step 3: Check initial state
        console.log('STEP 3: Checking initial $isPlaying state...');
        const initialPlayText = await playButton.textContent();
        console.log(`Initial button text: "${initialPlayText}"`);
        
        const stopButton = page.locator('button:has-text("Stop")');
        const initialStopVisible = await stopButton.isVisible();
        console.log(`Initial Stop button visible: ${initialStopVisible}`);

        // Step 4: Check for Alpine.js/Livewire initialization
        console.log('STEP 4: Checking for Alpine.js and Livewire...');
        const alpineExists = await page.evaluate(() => {
            return typeof window.Alpine !== 'undefined';
        });
        console.log(`Alpine.js loaded: ${alpineExists}`);

        const livewireExists = await page.evaluate(() => {
            return typeof window.Livewire !== 'undefined';
        });
        console.log(`Livewire loaded: ${livewireExists}`);

        // Step 5: Check for MultiInstrumentPlayer
        console.log('STEP 5: Checking for MultiInstrumentPlayer...');
        const playerExists = await page.evaluate(() => {
            return typeof window.MultiInstrumentPlayer !== 'undefined';
        });
        console.log(`MultiInstrumentPlayer loaded: ${playerExists}`);

        // Step 6: Check for mathChordPlayer Alpine component
        console.log('STEP 6: Checking Alpine component initialization...');
        const alpineComponent = await page.evaluate(() => {
            const element = document.querySelector('[x-data="mathChordPlayer"]');
            return element ? {
                hasElement: true,
                xData: element.getAttribute('x-data'),
                hasAlpineData: element._x_dataStack && element._x_dataStack.length > 0
            } : { hasElement: false };
        });
        console.log('Alpine component info:', alpineComponent);

        // Step 7: Click the Play button and monitor everything
        console.log('STEP 7: Clicking Play button and monitoring events...');
        
        // Set up Livewire event monitoring
        const livewireEvents = [];
        await page.evaluate(() => {
            window.livewireEvents = [];
            if (window.Livewire) {
                window.Livewire.hook('message.sent', (message, component) => {
                    console.log('Livewire message sent:', message.method, message.params);
                    window.livewireEvents.push({
                        type: 'sent',
                        payload: message.message,
                        method: message.message.method,
                        params: message.message.params
                    });
                });
                
                window.Livewire.hook('message.received', (message, component) => {
                    console.log('Livewire message received:', message);
                    window.livewireEvents.push({
                        type: 'received',
                        payload: message.message
                    });
                });
            }
        });

        // Clear console messages before clicking
        consoleMessages = [];
        
        // Click the play button
        await playButton.click();
        console.log('✓ Play button clicked');

        // Wait a moment for events to process
        await page.waitForTimeout(2000);

        // Step 8: Check what happened after click
        console.log('STEP 8: Analyzing post-click state...');
        
        // Check if button text changed
        const postClickPlayVisible = await playButton.isVisible();
        const postClickStopVisible = await stopButton.isVisible();
        console.log(`After click - Play button visible: ${postClickPlayVisible}`);
        console.log(`After click - Stop button visible: ${postClickStopVisible}`);

        if (postClickStopVisible) {
            const stopButtonText = await stopButton.textContent();
            console.log(`Stop button text: "${stopButtonText}"`);
        }

        // Check Livewire events
        const livewireEventsResult = await page.evaluate(() => {
            return window.livewireEvents || [];
        });
        console.log('Livewire events captured:', livewireEventsResult);

        // Step 9: Check for specific PHP method calls
        console.log('STEP 9: Looking for Livewire method calls...');
        const methodCalls = livewireEventsResult.filter(event => 
            event.type === 'sent' && (
                event.method === 'playRhythm' || 
                event.method === 'playProgression' ||
                event.method === 'playNextChord'
            )
        );
        console.log('PHP method calls found:', methodCalls);

        // Step 10: Check for Alpine.js events
        console.log('STEP 10: Checking for Alpine.js custom events...');
        const customEvents = consoleMessages.filter(msg => 
            msg.text.includes('play-math-chord') ||
            msg.text.includes('play-rhythm-pattern') ||
            msg.text.includes('schedule-next-chord') ||
            msg.text.includes('progression-chord-changed')
        );
        console.log('Custom events found:', customEvents);

        // Step 11: Check component state
        console.log('STEP 11: Checking component state...');
        const componentState = await page.evaluate(() => {
            const element = document.querySelector('[x-data="mathChordPlayer"]');
            if (element && element._x_dataStack && element._x_dataStack[0]) {
                const data = element._x_dataStack[0];
                return {
                    hasPianoPlayer: !!data.pianoPlayer,
                    hasProgressionTimer: !!data.progressionTimer,
                    pianoPlayerType: typeof data.pianoPlayer
                };
            }
            return null;
        });
        console.log('Component state:', componentState);

        // Step 12: Test audio context
        console.log('STEP 12: Checking audio context...');
        const audioContextInfo = await page.evaluate(() => {
            if (window.AudioContext || window.webkitAudioContext) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    return {
                        exists: true,
                        state: ctx.state,
                        sampleRate: ctx.sampleRate
                    };
                } catch (e) {
                    return {
                        exists: true,
                        error: e.message
                    };
                }
            }
            return { exists: false };
        });
        console.log('Audio context info:', audioContextInfo);

        // Step 13: Summary of findings
        console.log('=== DEBUGGING SUMMARY ===');
        console.log(`Total console messages: ${consoleMessages.length}`);
        console.log(`Total page errors: ${errorMessages.length}`);
        console.log(`Total network requests: ${networkRequests.length}`);
        
        if (errorMessages.length > 0) {
            console.log('PAGE ERRORS:');
            errorMessages.forEach((error, i) => {
                console.log(`  ${i + 1}. ${error.message}`);
            });
        }

        const errorConsoleMessages = consoleMessages.filter(msg => msg.type === 'error');
        if (errorConsoleMessages.length > 0) {
            console.log('CONSOLE ERRORS:');
            errorConsoleMessages.forEach((msg, i) => {
                console.log(`  ${i + 1}. ${msg.text}`);
            });
        }

        const importantMessages = consoleMessages.filter(msg => 
            msg.text.includes('MultiInstrumentPlayer') ||
            msg.text.includes('play') ||
            msg.text.includes('chord') ||
            msg.text.includes('rhythm') ||
            msg.text.includes('Alpine') ||
            msg.text.includes('Livewire')
        );
        if (importantMessages.length > 0) {
            console.log('IMPORTANT MESSAGES:');
            importantMessages.forEach((msg, i) => {
                console.log(`  ${i + 1}. [${msg.type}] ${msg.text}`);
            });
        }

        // Final verification: check if playback actually started
        console.log('STEP 14: Final state verification...');
        const finalStopVisible = await stopButton.isVisible();
        console.log(`Final verification - Stop button visible: ${finalStopVisible}`);
        
        if (!finalStopVisible) {
            console.log('❌ ISSUE IDENTIFIED: Play button click did not result in Stop button appearing');
            console.log('This suggests the $isPlaying state did not change to true');
        } else {
            console.log('✅ SUCCESS: Play button click resulted in Stop button appearing');
        }

        console.log('=== END DEBUG TEST ===');

        // Let the test pass regardless of outcome - this is for debugging
        expect(true).toBe(true);
    });

    test('should test individual method calls directly', async ({ page }) => {
        console.log('=== TESTING INDIVIDUAL METHOD CALLS ===');

        // Test playRhythm method directly
        console.log('Testing playRhythm method...');
        const playRhythmResult = await page.evaluate(async () => {
            if (window.Livewire && window.Livewire.components) {
                const components = window.Livewire.components.componentsById;
                const componentId = Object.keys(components)[0];
                if (componentId) {
                    try {
                        await window.Livewire.emit('playRhythm');
                        return { success: true, method: 'playRhythm via emit' };
                    } catch (e) {
                        return { success: false, error: e.message, method: 'playRhythm via emit' };
                    }
                }
            }
            return { success: false, error: 'No Livewire components found' };
        });
        console.log('playRhythm result:', playRhythmResult);

        // Test playProgression method directly
        console.log('Testing playProgression method...');
        const playProgressionResult = await page.evaluate(async () => {
            if (window.Livewire && window.Livewire.components) {
                const components = window.Livewire.components.componentsById;
                const componentId = Object.keys(components)[0];
                if (componentId) {
                    try {
                        await window.Livewire.emit('playProgression');
                        return { success: true, method: 'playProgression via emit' };
                    } catch (e) {
                        return { success: false, error: e.message, method: 'playProgression via emit' };
                    }
                }
            }
            return { success: false, error: 'No Livewire components found' };
        });
        console.log('playProgression result:', playProgressionResult);

        await page.waitForTimeout(1000);
        expect(true).toBe(true);
    });

    test('should test audio and piano player initialization', async ({ page }) => {
        console.log('=== TESTING AUDIO AND PIANO PLAYER INITIALIZATION ===');

        // Check if MultiInstrumentPlayer is properly initialized
        const playerInit = await page.evaluate(() => {
            if (typeof window.MultiInstrumentPlayer === 'function') {
                try {
                    const player = new window.MultiInstrumentPlayer();
                    return {
                        success: true,
                        hasContext: !!player.context,
                        contextState: player.context ? player.context.state : 'no context',
                        methods: Object.getOwnPropertyNames(Object.getPrototypeOf(player))
                    };
                } catch (e) {
                    return {
                        success: false,
                        error: e.message
                    };
                }
            }
            return { success: false, error: 'MultiInstrumentPlayer not found' };
        });
        console.log('MultiInstrumentPlayer initialization:', playerInit);

        // Test audio context creation and resumption
        const audioTest = await page.evaluate(async () => {
            try {
                const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                if (AudioContextClass) {
                    const ctx = new AudioContextClass();
                    console.log('Audio context created, state:', ctx.state);
                    
                    if (ctx.state === 'suspended') {
                        await ctx.resume();
                        console.log('Audio context resumed, new state:', ctx.state);
                    }
                    
                    return {
                        success: true,
                        initialState: ctx.state,
                        sampleRate: ctx.sampleRate
                    };
                }
                return { success: false, error: 'AudioContext not available' };
            } catch (e) {
                return { success: false, error: e.message };
            }
        });
        console.log('Audio context test:', audioTest);

        expect(true).toBe(true);
    });
});