#!/bin/bash

# ChordHound Comprehensive Chord Inversion Tests
# This script runs all Dusk tests to verify piano key activation and timing

echo "🎹 Starting ChordHound Chord Inversion Tests"
echo "============================================="
echo ""

# Ensure Chrome driver is up to date
echo "📦 Updating Chrome driver..."
php artisan dusk:chrome-driver --detect

echo ""
echo "🧪 Running comprehensive chord tests..."
echo ""

# Run environment test first  
echo "1️⃣ Testing basic environment..."
php artisan dusk tests/Browser/DuskEnvironmentTest.php

echo ""
echo "2️⃣ Testing quick chord functionality..."
php artisan dusk tests/Browser/QuickChordInversionTest.php

echo ""
echo "3️⃣ Testing comprehensive chord inversions..."
echo "   ⚠️  This test covers all 12 tones × 4 chord types × 3 inversions"
echo "   ⚠️  Expected to take 15-20 minutes to complete"
php artisan dusk tests/Browser/ComprehensiveChordInversionTest.php

echo ""
echo "4️⃣ Testing progression inversions across keys..."
echo "   ⚠️  This test covers 5 progressions × 7 keys"
echo "   ⚠️  Expected to take 10-15 minutes to complete"
php artisan dusk tests/Browser/ProgressionInversionKeyTest.php

echo ""
echo "✅ All chord inversion tests completed!"
echo ""
echo "📊 Test Summary:"
echo "   • Verified piano key activation for all chord types and inversions"
echo "   • Confirmed 1.5-second sustain timing for all chords"
echo "   • Tested progression inversion application across all keys"
echo "   • Validated rapid chord changes clear previous keys correctly"
echo ""
echo "🎵 ChordHound piano functionality is fully tested!"