<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SimplePageLoadTest extends DuskTestCase
{
    /**
     * Test basic page load
     */
    public function test_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->pause(3000) // Wait for page to fully load
                ->screenshot('page-load');
                
            // Check what's actually on the page
            $pageText = $browser->text('body');
            echo "Page contains ChordHound: " . (str_contains($pageText, 'ChordHound') ? 'YES' : 'NO') . "\n";
            
            // Check for specific elements we expect
            if ($browser->element('div[class*="timeline"]')) {
                echo "Found timeline div\n";
            } else {
                echo "No timeline div found\n";
            }
            
            if ($browser->element('button[wire\\:click*="setKey"]')) {
                echo "Found key buttons\n";
            } else {
                echo "No key buttons found\n";
            }
            
            // Check page source
            $source = $browser->driver->getPageSource();
            echo "Page source length: " . strlen($source) . " bytes\n";
            echo "Contains Livewire: " . (str_contains($source, 'livewire') ? 'YES' : 'NO') . "\n";
        });
    }
}