<?php

declare(strict_types=1);

use Laravel\Dusk\Browser;

test('debug home page loading', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->screenshot('home-page-debug')
                ->pause(1000);
        
        // Get the page source to see what's actually rendered
        $pageSource = $browser->driver->getPageSource();
        
        // Check if we're getting an error page
        if (str_contains($pageSource, 'Whoops') || str_contains($pageSource, 'Exception')) {
            echo "Error detected in page source\n";
        } elseif (str_contains($pageSource, 'Master Piano Chords')) {
            echo "Found expected content in page source\n";
        } else {
            echo "Did not find expected content\n";
        }
        
        // Get the actual title
        $actualTitle = $browser->driver->getTitle();
        echo "Actual page title: " . $actualTitle . "\n";
        
        // Check if we're on the right page
        echo "Current URL: " . $browser->driver->getCurrentURL() . "\n";
        
        // Check console logs for errors
        $logs = $browser->driver->manage()->getLog('browser');
        $errorCount = 0;
        foreach ($logs as $log) {
            if ($log['level'] === 'SEVERE') {
                echo "Console error: " . json_encode($log) . "\n";
                $errorCount++;
            }
        }
        if ($errorCount === 0) {
            echo "No console errors found\n";
        }
        
        // Try to get some text content
        try {
            $bodyText = $browser->text('body');
            echo "Body text (first 500 chars): " . substr($bodyText, 0, 500) . "\n";
            
            // Check if it's the sidebar layout
            if (str_contains($bodyText, 'Piano Chords') && str_contains($bodyText, 'Dashboard')) {
                echo "Page appears to be using sidebar layout\n";
            }
        } catch (\Exception $e) {
            echo "Could not get body text: " . $e->getMessage() . "\n";
        }
        
        // Save the page source for inspection
        file_put_contents(storage_path('logs/home-page-source.html'), $pageSource);
        echo "Page source saved to storage/logs/home-page-source.html\n";
    });
});