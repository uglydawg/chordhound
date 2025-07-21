// Visual test script for piano layout verification
// This can be run in the browser console to verify the piano appearance

function verifyPianoLayout() {
    const piano = document.querySelector('#piano-keyboard');
    
    if (!piano) {
        console.error('Piano keyboard not found!');
        return false;
    }
    
    // Check for octave markers
    const octaveMarkers = ['C1', 'C2', 'C3', 'C4', 'C5'];
    let foundOctaves = 0;
    
    octaveMarkers.forEach(marker => {
        if (piano.textContent.includes(marker)) {
            foundOctaves++;
            console.log(`✓ Found octave marker: ${marker}`);
        } else {
            console.log(`✗ Missing octave marker: ${marker}`);
        }
    });
    
    // Check for piano keys
    const whiteKeys = piano.querySelectorAll('rect[fill="#FFFFFF"]');
    const blackKeys = piano.querySelectorAll('rect[fill="#000000"]');
    
    console.log(`\nPiano key count:`);
    console.log(`- White keys: ${whiteKeys.length} (expected: 29)`);
    console.log(`- Black keys: ${blackKeys.length} (expected: 20)`);
    
    // Check for data-note attributes
    const keysWithNotes = piano.querySelectorAll('[data-note]');
    console.log(`- Keys with data-note attribute: ${keysWithNotes.length}`);
    
    // Check for labels
    const labels = piano.querySelectorAll('text');
    console.log(`- Text labels: ${labels.length}`);
    
    // Check visual styling
    const svgElement = piano.querySelector('svg');
    if (svgElement) {
        const viewBox = svgElement.getAttribute('viewBox');
        console.log(`\nSVG viewBox: ${viewBox}`);
        console.log(`SVG height: ${svgElement.style.height}`);
    }
    
    // Summary
    const isValid = foundOctaves === 5 && 
                   whiteKeys.length === 29 && 
                   blackKeys.length === 20 &&
                   keysWithNotes.length === 49;
    
    console.log(`\n${isValid ? '✓' : '✗'} Piano layout validation: ${isValid ? 'PASSED' : 'FAILED'}`);
    
    return isValid;
}

// Run the verification
verifyPianoLayout();