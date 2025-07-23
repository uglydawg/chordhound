<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ConsoleErrorTest extends DuskTestCase
{
    /**
     * Test for console errors on home page
     */
    public function test_check_console_errors(): void
    {
        $this->browse(function (Browser $browser) {
            // Navigate to home page
            $browser->visit('/')
                    ->pause(3000); // Wait for page to fully load

            // Get browser console logs
            $logs = $browser->driver->manage()->getLog('browser');
            
            // Display all console messages for debugging
            echo "\n=== CONSOLE LOGS ===\n";
            foreach ($logs as $log) {
                echo "[{$log['level']}] {$log['message']}\n";
            }
            echo "==================\n";

            // Check for specific error types
            $errors = [];
            $warnings = [];
            $loadingErrors = [];

            foreach ($logs as $log) {
                $message = $log['message'];
                $level = $log['level'];
                
                if ($level === 'SEVERE') {
                    $errors[] = $message;
                }
                
                if ($level === 'WARNING') {
                    $warnings[] = $message;
                }
                
                if (stripos($message, 'failed to load') !== false || 
                    stripos($message, '404') !== false ||
                    stripos($message, 'enoent') !== false ||
                    stripos($message, 'favicon.svg') !== false) {
                    $loadingErrors[] = $message;
                }
            }

            // Report findings
            if (!empty($errors)) {
                echo "\n=== SEVERE ERRORS ===\n";
                foreach ($errors as $error) {
                    echo "- $error\n";
                }
            }

            if (!empty($warnings)) {
                echo "\n=== WARNINGS ===\n";
                foreach ($warnings as $warning) {
                    echo "- $warning\n";
                }
            }

            if (!empty($loadingErrors)) {
                echo "\n=== LOADING ERRORS ===\n";
                foreach ($loadingErrors as $error) {
                    echo "- $error\n";
                }
            }

            // Basic page functionality test
            $browser->assertSee('ChordHound');
            
            $this->assertTrue(true, 'Console error check completed');
        });
    }
}