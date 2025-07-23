<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomePageErrorCheckTest extends DuskTestCase
{
    /**
     * Test home page loads without console errors
     */
    public function test_home_page_loads_without_errors(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8000/')
                    ->waitFor('body', 10)
                    ->pause(2000); // Wait for all assets to load

            // Check for JavaScript console errors
            $logs = $browser->driver->manage()->getLog('browser');
            $errors = array_filter($logs, function ($log) {
                return $log['level'] === 'SEVERE';
            });

            if (!empty($errors)) {
                $this->fail('Console errors found: ' . json_encode($errors, JSON_PRETTY_PRINT));
            }

            // Check that key elements are present
            $browser->assertSee('ChordHound')
                    ->assertSee('Master Piano Chords')
                    ->assertSee('Learning About Chords');

            // Check that no error messages are displayed
            $browser->assertDontSee('Error')
                    ->assertDontSee('404')
                    ->assertDontSee('500')
                    ->assertDontSee('ENOENT');

            // Check that favicon loads without error
            $faviconResponse = $browser->visit('http://localhost:8000/favicon.ico');
            // If we reach here without exception, favicon loaded successfully
        });
    }

    /**
     * Test specific assets load correctly
     */
    public function test_assets_load_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8000/');

            // Check that CSS is loaded (look for styled elements)
            $browser->waitFor('body', 10);

            // Get network logs if available
            $logs = $browser->driver->manage()->getLog('browser');
            
            // Filter for any 404 or loading errors
            $loadingErrors = array_filter($logs, function ($log) {
                $message = strtolower($log['message']);
                return strpos($message, '404') !== false || 
                       strpos($message, 'failed to load') !== false ||
                       strpos($message, 'enoent') !== false ||
                       strpos($message, 'favicon.svg') !== false;
            });

            if (!empty($loadingErrors)) {
                echo "\nAsset loading errors found:\n";
                foreach ($loadingErrors as $error) {
                    echo "- " . $error['message'] . "\n";
                }
                $this->fail('Asset loading errors detected');
            }

            $this->assertTrue(true, 'No asset loading errors detected');
        });
    }

    /**
     * Test favicon and related icons load
     */
    public function test_favicon_resources_exist(): void
    {
        $this->browse(function (Browser $browser) {
            // Test various favicon files exist
            $faviconUrls = [
                'http://localhost:8000/favicon.ico',
                'http://localhost:8000/favicon-16x16.png',
                'http://localhost:8000/favicon-32x32.png',
                'http://localhost:8000/apple-touch-icon.png',
                'http://localhost:8000/site.webmanifest'
            ];

            foreach ($faviconUrls as $url) {
                try {
                    $response = file_get_contents($url);
                    $this->assertNotEmpty($response, "Failed to load: {$url}");
                } catch (\Exception $e) {
                    $this->fail("Error loading {$url}: " . $e->getMessage());
                }
            }
        });
    }
}