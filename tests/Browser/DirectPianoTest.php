<?php

declare(strict_types=1);

beforeEach(function () {
    $this->client = static::createPantherClient([
        'browser' => static::CHROME,
        'external_base_uri' => 'http://127.0.0.1:8000',
        'headless' => true,
    ]);
});

afterEach(function () {
    $this->client->quit();
});

test('direct piano test works without Livewire', function () {
    // Load the direct test page
    $this->client->request('GET', '/debug/piano-direct');

    // Wait for page to load
    $this->client->waitFor('button', 10);
    $this->client->wait(8000); // Wait longer for piano samples to fully load

    // Setup console capture
    $this->client->executeScript('
        window.testConsoleLog = [];
        const originalLog = console.log;
        console.log = function(...args) {
            const msg = args.map(a => typeof a === "object" ? JSON.stringify(a) : String(a)).join(" ");
            window.testConsoleLog.push(msg);
            originalLog.apply(console, args);
        };
    ');

    // Click the test button
    $this->client->executeScript('
        console.log("=== TEST: Clicking direct test button ===");
        const btn = document.querySelector("button");
        if (btn) {
            btn.click();
            console.log("Button clicked");
        } else {
            console.error("Button NOT found");
        }
    ');

    $this->client->wait(1000); // Wait for keys to activate

    // Get console logs
    $logs = $this->client->executeScript('return window.testConsoleLog || [];');
    dump('Console logs:', $logs);

    // Debug: Check what piano keys exist
    $allKeys = $this->client->executeScript('
        return document.querySelectorAll("[id^=key-]").length;
    ');
    $keyC4 = $this->client->executeScript('
        const key = document.getElementById("key-C4");
        return key ? {
            exists: true,
            classes: key.className,
            hasActive: key.classList.contains("active"),
            hasPressed: key.classList.contains("pressed")
        } : {exists: false};
    ');

    dump('Total piano keys found:', $allKeys);
    dump('key-C4 status:', $keyC4);

    // Count active keys
    $activeCount = $this->client->executeScript('
        return document.querySelectorAll(".piano-key.active, .piano-key.pressed").length;
    ');

    dump('Active keys:', $activeCount);

    expect($activeCount)->toBeGreaterThan(0,
        'Should have active piano keys after clicking button');
});
