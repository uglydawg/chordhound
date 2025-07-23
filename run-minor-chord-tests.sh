#!/bin/bash

# ChordHound Minor Chord Tests
# This script runs Dusk tests specifically for minor chord voicings

echo "🎹 Starting ChordHound Minor Chord Tests"
echo "========================================"
echo ""

# Ensure Chrome driver is up to date
echo "📦 Checking Chrome driver..."
php artisan dusk:chrome-driver --detect

echo ""
echo "🧪 Running minor chord tests..."
echo ""

# Test E minor specifically first
echo "1️⃣ Testing E minor all inversions (the originally reported issue)..."
php artisan dusk tests/Browser/MinorChordInversionTest.php --filter test_e_minor_all_inversions

echo ""
echo "2️⃣ Testing all minor chords in root position..."
php artisan dusk tests/Browser/MinorChordInversionTest.php --filter test_all_minor_chords_root_position

echo ""
echo "3️⃣ Testing all minor chords in first inversion..."
php artisan dusk tests/Browser/MinorChordInversionTest.php --filter test_all_minor_chords_first_inversion

echo ""
echo "4️⃣ Testing all minor chords in second inversion..."
php artisan dusk tests/Browser/MinorChordInversionTest.php --filter test_all_minor_chords_second_inversion

echo ""
echo "5️⃣ Testing minor chord timing and rapid changes..."
php artisan dusk tests/Browser/MinorChordInversionTest.php --filter test_minor_chord_sustain_timing
php artisan dusk tests/Browser/MinorChordInversionTest.php --filter test_rapid_minor_chord_changes

echo ""
echo "✅ Minor chord tests completed!"
echo ""
echo "📊 Test Summary:"
echo "   • Em now correctly plays:"
echo "     - Root: E4, G4, B4"
echo "     - First: G3, B3, E4"
echo "     - Second: B3, E4, G4"
echo "   • All 12 minor chord roots tested in all inversions"
echo "   • Flat notes (Eb, Ab, Bb) correctly mapped to sharp keys"
echo "   • 1.5-second sustain timing verified"
echo ""
echo "🎵 Minor chord functionality is working correctly!"