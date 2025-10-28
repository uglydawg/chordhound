<?php

declare(strict_types=1);

beforeEach(function () {
    $this->client = static::createPantherClient([
        'browser' => static::CHROME,
        'external_base_uri' => 'http://127.0.0.1:8000',
    ]);
});

afterEach(function () {
    $this->client->quit();
});

test('block rhythm keys sync with audio playback', function () {
    $crawler = $this->client->request('GET', '/debug/math-chords');

    // Wait for page to load
    $this->client->waitFor('select[wire\\:model\\.live="selectedKey"]', 10);

    // Select C Major key
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').value = "C";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Select I-IV-V progression
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').value = "I-IV-V";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Select Block rhythm
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').value = "block";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Set BPM to 120
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="bpm"]\').value = "120";
        document.querySelector(\'select[wire\\\\:model\\\\.live="bpm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Start playback
    $this->client->executeScript('
        document.querySelector(\'button[wire\\\\:click="playRhythm"]\').click();
    ');
    $this->client->wait(500);

    // Monitor key states over time
    $keyEvents = [];
    $startTime = microtime(true);
    $beatDuration = 60000 / 120; // 500ms per beat at 120 BPM

    // Monitor for 2 seconds (4 beats)
    for ($i = 0; $i < 40; $i++) {
        $currentTime = (microtime(true) - $startTime) * 1000; // ms

        $activeKeys = $this->client->executeScript('
            const keys = document.querySelectorAll(".piano-key.active, .piano-key.pressed");
            return Array.from(keys).map(key => key.getAttribute("data-note")).filter(Boolean).sort().join(",");
        ');

        if (!empty($activeKeys)) {
            $keyEvents[] = [
                'time' => $currentTime,
                'keys' => $activeKeys,
            ];
        }

        usleep(50000); // 50ms
    }

    // Stop playback
    $this->client->waitFor('button[wire\\:click="stopProgression"]', 5);
    $this->client->executeScript('
        const stopBtn = document.querySelector(\'button[wire\\\\:click="stopProgression"]\');
        if (stopBtn) stopBtn.click();
    ');

    expect($keyEvents)->not->toBeEmpty('Should detect key activations during playback');
    expect(count($keyEvents))->toBeGreaterThan(3, 'Should have multiple key activation events');
});

test('arpeggio rhythm shows sequential keys', function () {
    $this->client->request('GET', '/debug/math-chords');
    $this->client->waitFor('select[wire\\:model\\.live="selectedKey"]', 10);

    // Select C Major key and I-IV-V progression
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').value = "C";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').value = "I-IV-V";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Select Arpeggiated rhythm
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').value = "arpeggio";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Set slow BPM for easier observation
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="bpm"]\').value = "60";
        document.querySelector(\'select[wire\\\\:model\\\\.live="bpm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Start playback
    $this->client->executeScript('
        document.querySelector(\'button[wire\\\\:click="playRhythm"]\').click();
    ');
    $this->client->wait(500);

    // Track sequential key activations
    $sequentialKeys = [];
    $previousKeys = '';

    for ($i = 0; $i < 40; $i++) {
        $currentKeys = $this->client->executeScript('
            const keys = document.querySelectorAll(".piano-key.active, .piano-key.pressed");
            return Array.from(keys).map(key => key.getAttribute("data-note")).filter(Boolean).sort().join(",");
        ');

        if ($currentKeys !== $previousKeys && !empty($currentKeys)) {
            $sequentialKeys[] = $currentKeys;
            $previousKeys = $currentKeys;
        }

        usleep(50000); // 50ms
    }

    // Stop playback
    $this->client->waitFor('button[wire\\:click="stopProgression"]', 5);
    $this->client->executeScript('
        const stopBtn = document.querySelector(\'button[wire\\\\:click="stopProgression"]\');
        if (stopBtn) stopBtn.click();
    ');

    expect(count($sequentialKeys))->toBeGreaterThan(2,
        'Arpeggio should show at least 3 different key states (bass + each chord note)');

    // Verify we're showing individual notes, not all at once
    $singleNoteStates = 0;
    foreach ($sequentialKeys as $keyState) {
        $noteCount = count(explode(',', $keyState));
        if ($noteCount <= 2) { // Bass + one chord note
            $singleNoteStates++;
        }
    }

    expect($singleNoteStates)->toBeGreaterThan(0,
        'Arpeggio should show individual notes lighting up, not all at once');
});

test('waltz rhythm syncs in 3/4 time', function () {
    $this->client->request('GET', '/debug/math-chords');
    $this->client->waitFor('select[wire\\:model\\.live="selectedKey"]', 10);

    // Select F Major key and I-IV-V progression
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').value = "F";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').value = "I-IV-V";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Select Waltz rhythm (should auto-set to 3/4)
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').value = "waltz";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Verify time signature auto-changed to 3/4
    $timeSignature = $this->client->executeScript('
        return document.querySelector(\'select[wire\\\\:model\\\\.live="timeSignature"]\').value;
    ');

    expect($timeSignature)->toBe('3/4', 'Waltz rhythm should auto-select 3/4 time');

    // Set BPM
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="bpm"]\').value = "120";
        document.querySelector(\'select[wire\\\\:model\\\\.live="bpm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Start playback
    $this->client->executeScript('
        document.querySelector(\'button[wire\\\\:click="playRhythm"]\').click();
    ');
    $this->client->wait(500);

    // Monitor for bass-only on beat 1, chord on beats 2 & 3
    $keyPatterns = [];

    for ($i = 0; $i < 30; $i++) {
        $activeNotes = $this->client->executeScript('
            const keys = document.querySelectorAll(".piano-key.active, .piano-key.pressed");
            return Array.from(keys).map(key => key.getAttribute("data-note")).filter(Boolean);
        ');

        $noteCount = is_array($activeNotes) ? count($activeNotes) : 0;

        if ($noteCount > 0) {
            $keyPatterns[] = $noteCount;
        }

        usleep(100000); // 100ms
    }

    // Stop playback
    $this->client->waitFor('button[wire\\:click="stopProgression"]', 5);
    $this->client->executeScript('
        const stopBtn = document.querySelector(\'button[wire\\\\:click="stopProgression"]\');
        if (stopBtn) stopBtn.click();
    ');

    // Should see variation: 1 note (bass only), then more notes (bass + chord)
    $hasVariation = count(array_unique($keyPatterns)) > 1;
    expect($hasVariation)->toBeTrue(
        'Waltz should show variation in active key count (bass-only vs bass+chord)');
});

test('rhythm change restarts from first chord', function () {
    $this->client->request('GET', '/debug/math-chords');
    $this->client->waitFor('select[wire\\:model\\.live="selectedKey"]', 10);

    // Select C Major key and I-IV-V-vi progression
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').value = "C";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').value = "I-V-vi-IV";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Start with block rhythm
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').value = "block";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Start playback
    $this->client->getCrawler()->filter('button[wire\\:click="playRhythm"]')->click();
    sleep(2); // Let it play for 2 seconds

    // Change rhythm
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').value = "waltz";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Check chord index reset to 0
    $chordIndexAfter = $this->client->executeScript('
        const livewireComponents = window.Livewire.all();
        if (livewireComponents && livewireComponents.length > 0) {
            return livewireComponents[0].get("currentChordIndex") || 0;
        }
        return 0;
    ');

    // Stop playback
    $this->client->waitFor('button[wire\\:click="stopProgression"]', 5);
    $this->client->executeScript('
        const stopBtn = document.querySelector(\'button[wire\\\\:click="stopProgression"]\');
        if (stopBtn) stopBtn.click();
    ');

    expect($chordIndexAfter)->toBe(0,
        'Changing rhythm should reset to first chord (index 0)');
});

test('broken chord shows sequential activation', function () {
    $this->client->request('GET', '/debug/math-chords');
    $this->client->waitFor('select[wire\\:model\\.live="selectedKey"]', 10);

    // Select G Major key and I-IV-V progression
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').value = "G";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedKey"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').value = "I-IV-V";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedProgression"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Select broken chord rhythm
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').value = "broken";
        document.querySelector(\'select[wire\\\\:model\\\\.live="selectedRhythm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Set moderate BPM
    $this->client->executeScript('
        document.querySelector(\'select[wire\\\\:model\\\\.live="bpm"]\').value = "80";
        document.querySelector(\'select[wire\\\\:model\\\\.live="bpm"]\').dispatchEvent(new Event("change", { bubbles: true }));
    ');
    $this->client->wait(500);

    // Start playback
    $this->client->executeScript('
        document.querySelector(\'button[wire\\\\:click="playRhythm"]\').click();
    ');
    $this->client->wait(500);

    // Track key activation changes
    $keyChanges = [];
    $previousKeys = '';

    for ($i = 0; $i < 40; $i++) {
        $currentKeys = $this->client->executeScript('
            const keys = document.querySelectorAll(".piano-key.active, .piano-key.pressed");
            const notes = Array.from(keys).map(key => key.getAttribute("data-note")).filter(Boolean).sort();
            return notes.join(",");
        ');

        if ($currentKeys !== $previousKeys) {
            $keyChanges[] = [
                'time' => $i * 50,
                'keys' => $currentKeys,
            ];
            $previousKeys = $currentKeys;
        }

        usleep(50000); // 50ms
    }

    // Stop playback
    $this->client->waitFor('button[wire\\:click="stopProgression"]', 5);
    $this->client->executeScript('
        const stopBtn = document.querySelector(\'button[wire\\\\:click="stopProgression"]\');
        if (stopBtn) stopBtn.click();
    ');

    expect(count($keyChanges))->toBeGreaterThan(2,
        'Broken chord should show multiple key state changes (sequential notes)');
});
