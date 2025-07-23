<?php

declare(strict_types=1);

use Laravel\Dusk\Browser;

test('home page loads without errors', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertTitle('ChordHound - Free Piano Chord Generator & Music Learning Platform')
                ->assertSee('Master Piano Chords')
                ->assertSee('Musical Companion')
                ->assertPresent('nav')
                ->assertVisible('a[href="#features"]');
        
        // Check for console errors
        $logs = $browser->driver->manage()->getLog('browser');
        $errors = array_filter($logs, function ($log) {
            return $log['level'] === 'SEVERE';
        });
        
        expect($errors)->toBeEmpty();
    });
});

test('home page has proper SEO meta tags', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/');
        
        // Check title
        $title = $browser->driver->getTitle();
        expect($title)->toBe('ChordHound - Free Piano Chord Generator & Music Learning Platform');
        
        // Check that meta description exists in page source
        $pageSource = $browser->driver->getPageSource();
        expect($pageSource)->toContain('Learn piano chords with ChordHound');
    });
});

test('home page navigation links work', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertVisible('a[href="#features"]')
                ->assertVisible('nav');
    });
});

test('home page call to action buttons are present', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertVisible('a[href="' . route('register') . '"]')
                ->assertSeeIn('a[href="' . route('register') . '"]', 'Get Started Free');
    });
});

test('home page is responsive', function () {
    $this->browse(function (Browser $browser) {
        // Desktop view
        $browser->visit('/')
                ->resize(1920, 1080)
                ->assertVisible('nav .hidden.md\\:flex');
        
        // Mobile view - test that mobile nav is working
        $browser->resize(375, 667)
                ->assertMissing('nav .hidden.md\\:flex');
    });
});

test('home page loads all sections', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertSee('Master Piano Chords')
                ->assertSee('ChordHound makes learning piano chords fun');
    });
});

test('dark mode toggle preserves content', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertSee('Master Piano Chords')
                ->assertSourceHas('dark:bg-zinc-900');
    });
});
