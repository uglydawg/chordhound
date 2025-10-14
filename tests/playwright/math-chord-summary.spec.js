import { test, expect } from '@playwright/test';

test.describe('Math Chord Test Page - Error Summary Report', () => {
  test('comprehensive error analysis', async ({ page }) => {
    const consoleMessages = [];
    const consoleErrors = [];
    const consoleWarnings = [];
    const pageErrors = [];

    // Listen to all console events
    page.on('console', (msg) => {
      const text = msg.text();
      const type = msg.type();
      
      consoleMessages.push({ type, text, timestamp: Date.now() });
      
      if (type === 'error') {
        consoleErrors.push(text);
      } else if (type === 'warning') {
        consoleWarnings.push(text);
      }
    });

    // Listen to page errors (uncaught exceptions)
    page.on('pageerror', (error) => {
      const errorMsg = `Page Error: ${error.message}`;
      pageErrors.push(error.message);
      consoleErrors.push(errorMsg);
    });

    // Navigate to the debug page
    console.log('🔍 Starting comprehensive error analysis of /debug/math-chords');
    await page.goto('/debug/math-chords');
    await page.waitForLoadState('networkidle');
    
    // Wait for initial loading
    await page.waitForTimeout(3000);

    console.log('📊 INITIAL LOAD ANALYSIS:');
    console.log(`  - Total console messages: ${consoleMessages.length}`);
    console.log(`  - Console errors: ${consoleErrors.length}`);
    console.log(`  - Console warnings: ${consoleWarnings.length}`);
    console.log(`  - Page errors: ${pageErrors.length}`);

    // Check library loading status
    const toneLoaded = await page.evaluate(() => typeof window.Tone !== 'undefined');
    const pianoPlayerLoaded = await page.evaluate(() => typeof window.PianoPlayer !== 'undefined');
    const multiInstrumentPlayerLoaded = await page.evaluate(() => typeof window.MultiInstrumentPlayer !== 'undefined');

    console.log('📚 LIBRARY STATUS:');
    console.log(`  - Tone.js loaded: ${toneLoaded ? '✅' : '❌'}`);
    console.log(`  - PianoPlayer loaded: ${pianoPlayerLoaded ? '✅' : '❌'}`);
    console.log(`  - MultiInstrumentPlayer loaded: ${multiInstrumentPlayerLoaded ? '✅' : '❌'}`);

    // Interactive testing
    console.log('🎹 INTERACTIVE TESTING:');
    
    // Test piano keys
    const pianoKeys = page.locator('[data-key], .piano-key, .key, [class*="key"]');
    const keyCount = await pianoKeys.count();
    console.log(`  - Found ${keyCount} piano keys`);
    
    if (keyCount > 0) {
      console.log('  - Clicking first piano key...');
      await pianoKeys.first().click();
      await page.waitForTimeout(1000);
    }

    // Test play buttons
    const playButtons = page.locator('button', { hasText: /play/i });
    const playButtonCount = await playButtons.count();
    console.log(`  - Found ${playButtonCount} play buttons`);
    
    if (playButtonCount > 0) {
      console.log('  - Clicking first play button...');
      await playButtons.first().click();
      await page.waitForTimeout(2000);
    }

    // Test chord controls
    const chordControls = page.locator('button[class*="chord"], .chord-button, [data-chord], button[wire\\:click*="chord"]');
    const chordControlCount = await chordControls.count();
    console.log(`  - Found ${chordControlCount} chord controls`);
    
    if (chordControlCount > 0) {
      console.log('  - Clicking first chord control...');
      await chordControls.first().click();
      await page.waitForTimeout(1000);
    }

    // Wait for any delayed errors
    await page.waitForTimeout(3000);

    // Final report
    console.log('');
    console.log('📋 FINAL ERROR REPORT:');
    console.log('='.repeat(50));
    
    if (consoleErrors.length === 0) {
      console.log('✅ NO CONSOLE ERRORS FOUND!');
    } else {
      console.log(`❌ FOUND ${consoleErrors.length} CONSOLE ERRORS:`);
      consoleErrors.forEach((error, index) => {
        console.log(`  ${index + 1}. ${error}`);
      });
    }

    if (consoleWarnings.length > 0) {
      console.log('');
      console.log(`WARNING: FOUND ${consoleWarnings.length} CONSOLE WARNINGS:`);
      consoleWarnings.forEach((warning, index) => {
        console.log(`  ${index + 1}. ${warning}`);
      });
    }

    if (pageErrors.length > 0) {
      console.log('');
      console.log(`CRITICAL: FOUND ${pageErrors.length} PAGE ERRORS:`);
      pageErrors.forEach((error, index) => {
        console.log(`  ${index + 1}. ${error}`);
      });
    }

    // Categorize errors
    const audioErrors = consoleErrors.filter(error => 
      error.includes('AudioContext') || 
      error.includes('AudioParam') || 
      error.includes('setValueAtTime') ||
      error.includes('audio')
    );

    const loadingErrors = consoleErrors.filter(error => 
      error.includes('Failed to load') || 
      error.includes('404') ||
      error.includes('network')
    );

    const jsErrors = consoleErrors.filter(error => 
      !audioErrors.includes(error) && !loadingErrors.includes(error)
    );

    console.log('');
    console.log('ERROR CATEGORIZATION:');
    console.log(`  - Audio-related errors: ${audioErrors.length}`);
    console.log(`  - Loading/Network errors: ${loadingErrors.length}`);
    console.log(`  - JavaScript errors: ${jsErrors.length}`);

    if (audioErrors.length > 0) {
      console.log('');
      console.log('AUDIO ERRORS:');
      audioErrors.forEach((error, index) => {
        console.log(`  ${index + 1}. ${error}`);
      });
    }

    console.log('='.repeat(50));

    // The test passes regardless of errors found - this is for reporting only,
    // but we'll soft-fail if there are JavaScript errors (non-audio related)
    if (jsErrors.length > 0) {
      console.log(`ERROR: Test completed with ${jsErrors.length} critical JavaScript errors`);
    } else {
      console.log(`SUCCESS: Test completed - no critical JavaScript errors found`);
    }
  });
});