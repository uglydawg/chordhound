<?php

declare(strict_types=1);

beforeEach(function () {
    $this->client = static::createPantherClient([
        'browser' => static::CHROME,
        'external_base_uri' => 'http://127.0.0.1:8000',
        'headless' => true, // Run without showing browser (for WSL)
    ]);
});

afterEach(function () {
    $this->client->quit();
});

test('can load math chords page and start playback', function () {
    // Load the page
    $this->client->request('GET', '/debug/math-chords');

    // Setup console log capture IMMEDIATELY after page load
    $this->client->executeScript('
        window.testConsoleLog = [];
        window.testErrors = [];
        window.livewireEvents = [];
        window.alpineReady = false;

        const originalLog = console.log;
        const originalError = console.error;

        console.log = function(...args) {
            const msg = args.map(a => typeof a === "object" ? JSON.stringify(a) : String(a)).join(" ");
            window.testConsoleLog.push(msg);
            originalLog.apply(console, args);
        };

        console.error = function(...args) {
            const msg = args.map(a => typeof a === "object" ? JSON.stringify(a) : String(a)).join(" ");
            window.testErrors.push(msg);
            originalError.apply(console, args);
        };

        console.log("=== TEST: Console capture initialized ===");

        // Check if Alpine.js is ready
        if (window.Alpine) {
            window.alpineReady = true;
            console.log("Alpine.js detected and ready");
        } else {
            console.log("Alpine.js not yet loaded, will check again");
        }

        // Check if Livewire is ready
        if (window.Livewire) {
            console.log("Livewire detected and ready");
        } else {
            console.log("Livewire not yet loaded");
        }
    ');

    // Wait for page to fully load
    $this->client->waitFor('select[wire\\:model\\.live="selectedKey"]', 10);
    $this->client->wait(5000); // Give Livewire, Alpine.js and samples time to initialize

    // Click the play button
    $this->client->executeScript('
        console.log("=== TEST: Clicking play button ===");
        const playBtn = document.querySelector(\'button[wire\\\\:click="playRhythm"]\');
        if (playBtn) {
            playBtn.click();
            console.log("Play button clicked");
        } else {
            console.error("Play button NOT found");
        }
    ');

    $this->client->wait(1000); // Wait 1 second for Livewire to dispatch

    // Check if we got logs from handleRhythmPattern
    $afterClickLogs = $this->client->executeScript('return window.testConsoleLog || [];');
    dump('=== LOGS AFTER BUTTON CLICK ===');
    foreach ($afterClickLogs as $log) {
        if (str_contains($log, 'handleRhythmPattern') || str_contains($log, 'data.chords') || str_contains($log, 'Data keys')) {
            dump($log);
        }
    }

    $this->client->wait(3000); // Wait 3 more seconds for playback

    // Get console logs, errors, and status
    $consoleLogs = $this->client->executeScript('return window.testConsoleLog || [];');
    $consoleErrors = $this->client->executeScript('return window.testErrors || [];');
    $livewireEvents = $this->client->executeScript('return window.livewireEvents || [];');
    $alpineReady = $this->client->executeScript('return window.alpineReady || false;');

    dump('=== ALPINE/LIVEWIRE STATUS ===');
    dump('Alpine Ready: ' . ($alpineReady ? 'YES' : 'NO'));

    dump('=== CONSOLE LOGS ===');
    foreach ($consoleLogs as $log) {
        dump($log);
    }

    dump('=== CONSOLE ERRORS ===');
    foreach ($consoleErrors as $error) {
        dump($error);
    }

    dump('=== LIVEWIRE EVENTS ===');
    dump($livewireEvents);

    // Count active keys
    $keyChecks = [];
    for ($i = 0; $i < 5; $i++) {
        $activeCount = $this->client->executeScript('
            return document.querySelectorAll(".piano-key.active, .piano-key.pressed").length;
        ');
        $keyChecks[] = $activeCount;
        $this->client->wait(200);
    }

    dump('=== KEY ACTIVATION COUNTS ===', $keyChecks);

    // Check if we ever had active keys
    $maxActiveKeys = max($keyChecks);

    expect($maxActiveKeys)->toBeGreaterThan(0,
        'Should have active piano keys during playback. Key counts: ' . implode(', ', $keyChecks));
});
