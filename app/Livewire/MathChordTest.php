<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\MathematicalChordService;
use Livewire\Component;

class MathChordTest extends Component
{
    public string $root = 'C';
    public string $type = 'major';
    public string $startPosition = 'C4';
    public int $inversion = 0;
    
    public array $calculatedNotes = [];
    public string $error = '';
    
    // Key and progression
    public string $selectedKey = 'C';
    public string $selectedProgression = 'I-IV-V-I';
    public bool $isPlaying = false;
    public int $currentChordIndex = 0;
    public int $bpm = 120; // beats per minute
    public int $startingInversion = 0; // Starting inversion for progressions
    
    // Voice leading calculations
    public array $voiceLeadingAnalysis = [];
    
    // Rhythm settings
    public string $selectedRhythm = 'block';
    public string $timeSignature = '4/4';
    
    // Event listeners
    protected $listeners = [
        'tempo-changed' => 'handleTempoChange',
    ];
    
    // Available options
    public array $roots = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
    public array $types = ['major', 'minor', 'diminished', 'augmented'];
    public array $inversions = [
        0 => 'Root Position',
        1 => 'First Inversion',
        2 => 'Second Inversion'
    ];
    
    // Common progressions
    public array $progressions = [
        'I-IV-V-I' => ['I', 'IV', 'V', 'I'],
        'I-V-vi-IV' => ['I', 'V', 'vi', 'IV'],
        'I-vi-IV-V' => ['I', 'vi', 'IV', 'V'],
        'ii-V-I' => ['ii', 'V', 'I'],
        'I-IV-I-V' => ['I', 'IV', 'I', 'V'],
        'vi-IV-I-V' => ['vi', 'IV', 'I', 'V'],
        'I-iv-I-II' => ['I', 'iv', 'I', 'II'], // Your requested progression
    ];
    
    // Piano rhythm patterns
    public array $rhythmPatterns = [
        'block' => 'Block Chords',
        'alberti' => 'Alberti Bass',
        'waltz' => 'Waltz Pattern',
        'broken' => 'Broken Chords',
        'arpeggio' => 'Arpeggiated',
        'march' => 'March',
        'ballad' => 'Ballad Style',
        'ragtime' => 'Ragtime'
    ];
    
    // Time signatures
    public array $timeSignatures = [
        '4/4' => '4/4 (Common)',
        '3/4' => '3/4 (Waltz)',
        '6/8' => '6/8 (Compound)',
        '2/4' => '2/4 (March)',
        '5/4' => '5/4 (Irregular)',
        '7/8' => '7/8 (Complex)'
    ];
    
    protected MathematicalChordService $chordService;
    
    public function boot()
    {
        $this->chordService = new MathematicalChordService();
    }
    
    public function mount()
    {
        // Restore saved preferences from session
        $this->selectedKey = session('math_chord_key', 'C');
        $this->selectedProgression = session('math_chord_progression', 'I-IV-V-I');
        $this->bpm = session('math_chord_bpm', 120);
        $this->startingInversion = session('math_chord_starting_inversion', 0);
        $this->selectedRhythm = session('math_chord_rhythm', 'block');
        $this->timeSignature = session('math_chord_time_signature', '4/4');
        
        // Set initial root to match the selected key
        $this->root = $this->selectedKey;
        
        $this->calculateChord();
        $this->calculateVoiceLeadingAnalysis();
    }
    
    public function calculateChord()
    {
        $this->error = '';
        
        try {
            $this->calculatedNotes = $this->chordService->calculateChord(
                $this->root,
                $this->type,
                $this->startPosition,
                $this->inversion
            );
            
            // Dispatch event to play the chord
            $this->dispatch('play-math-chord', notes: $this->calculatedNotes);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->calculatedNotes = [];
        }
    }
    
    public function playChord()
    {
        if (!empty($this->calculatedNotes)) {
            $this->dispatch('play-math-chord', notes: $this->calculatedNotes);
        }
    }
    
    public function compareWithHardcoded()
    {
        // Calculate voice leading distance if comparing two chords
        if ($this->root === 'C' && $this->type === 'major') {
            $chord1 = $this->calculatedNotes;
            $chord2 = $this->chordService->calculateChord('F', 'major', $this->startPosition, $this->inversion);
            $distance = $this->chordService->calculateVoiceLeadingDistance($chord1, $chord2);
            
            session()->flash('voiceLeading', "Voice leading distance from C to F: $distance semitones");
        }
    }
    
    public function updated($property)
    {
        // When root changes, update startPosition to maintain the same octave
        if ($property === 'root') {
            $currentOctave = preg_match('/(\d)$/', $this->startPosition, $matches) ? $matches[1] : '4';
            $this->startPosition = $this->root . $currentOctave;
        }
        
        if (in_array($property, ['root', 'type', 'startPosition', 'inversion'])) {
            $this->calculateChord();
        }
        
        // When key changes, update the root note to the new key
        if ($property === 'selectedKey') {
            session(['math_chord_key' => $this->selectedKey]);
            $this->root = $this->selectedKey;
            $currentOctave = preg_match('/(\d)$/', $this->startPosition, $matches) ? $matches[1] : '4';
            $this->startPosition = $this->root . $currentOctave;
            $this->calculateChord();
        }
        
        // Save progression preference
        if ($property === 'selectedProgression') {
            session(['math_chord_progression' => $this->selectedProgression]);
        }
        
        // Save BPM preference
        if ($property === 'bpm') {
            session(['math_chord_bpm' => $this->bpm]);
            // Update PianoPlayer tempo when BPM changes
            $this->dispatch('tempo-changed', tempo: $this->bpm);
        }
        
        // Save starting inversion preference
        if ($property === 'startingInversion') {
            session(['math_chord_starting_inversion' => $this->startingInversion]);
        }
        
        // Save rhythm preference
        if ($property === 'selectedRhythm') {
            session(['math_chord_rhythm' => $this->selectedRhythm]);
        }
        
        // Save time signature preference
        if ($property === 'timeSignature') {
            session(['math_chord_time_signature' => $this->timeSignature]);
        }
        
        // Recalculate voice leading analysis when relevant properties change
        if (in_array($property, ['selectedKey', 'selectedProgression', 'startPosition', 'startingInversion', 'bpm', 'selectedRhythm', 'timeSignature'])) {
            $this->calculateVoiceLeadingAnalysis();
        }
    }
    
    // Convert Roman numeral to root note based on key
    private function romanToRoot(string $roman, string $key): array
    {
        $keyIndex = array_search($key, $this->roots);
        if ($keyIndex === false) return ['C', 'major'];
        
        // Define intervals for each roman numeral
        $romanIntervals = [
            'I' => [0, 'major'],
            'ii' => [2, 'minor'],
            'iii' => [4, 'minor'],
            'IV' => [5, 'major'],
            'V' => [7, 'major'],
            'vi' => [9, 'minor'],
            'vii°' => [11, 'diminished'],
            // Borrowed chords
            'iv' => [5, 'minor'], // Borrowed from parallel minor
            'II' => [2, 'major'], // Secondary dominant
        ];
        
        if (!isset($romanIntervals[$roman])) return ['C', 'major'];
        
        [$interval, $type] = $romanIntervals[$roman];
        $rootIndex = ($keyIndex + $interval) % 12;
        
        return [$this->roots[$rootIndex], $type];
    }
    
    public function playProgression()
    {
        // Use the new playRhythm() method which handles the entire progression
        $this->playRhythm();
    }
    
    public function stopProgression()
    {
        $this->isPlaying = false;
        $this->currentChordIndex = 0;
    }
    
    public function playNextChord()
    {
        if (!$this->isPlaying) return;
        
        $progression = $this->progressions[$this->selectedProgression];
        if ($this->currentChordIndex >= count($progression)) {
            $this->stopProgression();
            return;
        }
        
        // Get the current chord in the progression
        $roman = $progression[$this->currentChordIndex];
        [$chordRoot, $chordType] = $this->romanToRoot($roman, $this->selectedKey);
        
        // Calculate and play the chord
        // Extract octave from current startPosition and apply to new chord root
        $currentOctave = preg_match('/(\d)$/', $this->startPosition, $matches) ? $matches[1] : '4';
        $chordStartPosition = $chordRoot . $currentOctave;
        
        // Use starting inversion for first chord, then optimize
        $inversion = $this->currentChordIndex === 0 ? $this->startingInversion : 0;
        
        // Find optimal inversion based on voice leading
        if ($this->currentChordIndex > 0 && !empty($this->voiceLeadingAnalysis)) {
            // Find the analysis for our starting inversion
            foreach ($this->voiceLeadingAnalysis as $analysis) {
                if ($analysis['startingInversion'] === $this->startingInversion) {
                    $inversion = $analysis['sequence'][$this->currentChordIndex]['inversion'] ?? 0;
                    break;
                }
            }
        }
        
        $notes = $this->chordService->calculateChord($chordRoot, $chordType, $chordStartPosition, $inversion);
        $this->calculatedNotes = $notes;
        $this->root = $chordRoot;
        $this->type = $chordType;
        $this->inversion = $inversion;
        
        // Get rhythm pattern
        $rhythmInfo = $this->getRhythmPattern();
        
        // Dispatch events based on rhythm
        if ($this->selectedRhythm === 'block') {
            // Simple block chord
            $this->dispatch('play-math-chord', notes: $notes);
        } else {
            // Play rhythm pattern
            $this->dispatch('play-rhythm-pattern', 
                notes: $notes,
                pattern: $rhythmInfo,
                rhythm: $this->selectedRhythm,
                bpm: $this->bpm,
                timeSignature: $this->timeSignature,
                measureDuration: $this->getMeasureDuration()
            );
        }
        
        $this->dispatch('progression-chord-changed', 
            index: $this->currentChordIndex, 
            roman: $roman,
            root: $chordRoot,
            type: $chordType
        );
        
        // Schedule next chord based on time signature and rhythm
        $measureDuration = $this->getMeasureDuration();
        $this->currentChordIndex++;
        
        if ($this->currentChordIndex < count($progression)) {
            $this->dispatch('schedule-next-chord', delay: $measureDuration * 1000);
        } else {
            // Loop back to beginning after a delay
            $this->currentChordIndex = 0;
            if ($this->isPlaying) {
                $this->dispatch('schedule-next-chord', delay: $measureDuration * 1000);
            }
        }
    }
    
    public function setBpm($bpm)
    {
        $this->bpm = (int) $bpm;
        session(['math_chord_bpm' => $this->bpm]);
    }
    
    public function handleTempoChange($tempo)
    {
        // Sync BPM from PianoPlayer tempo changes
        $this->bpm = (int) $tempo;
        session(['math_chord_bpm' => $this->bpm]);
    }
    
    public function playRhythm()
    {
        \Log::debug('playRhythm() called', [
            'selectedKey' => $this->selectedKey,
            'selectedProgression' => $this->selectedProgression,
            'selectedRhythm' => $this->selectedRhythm,
            'bpm' => $this->bpm
        ]);

        // Build the chord progression array for the rhythm player
        $progression = $this->progressions[$this->selectedProgression];
        $chords = [];

        foreach ($progression as $index => $roman) {
            [$chordRoot, $chordType] = $this->romanToRoot($roman, $this->selectedKey);

            // Get octave from startPosition
            $octave = preg_match('/(\d)$/', $this->startPosition, $matches) ? $matches[1] : '4';
            $position = $chordRoot . $octave;

            // Use starting inversion for first chord
            $inversion = $index === 0 ? $this->startingInversion : 0;

            // Calculate optimal inversion based on voice leading
            if ($index > 0 && !empty($this->voiceLeadingAnalysis)) {
                foreach ($this->voiceLeadingAnalysis as $analysis) {
                    if ($analysis['startingInversion'] === $this->startingInversion) {
                        $inversion = $analysis['sequence'][$index]['inversion'] ?? 0;
                        break;
                    }
                }
            }

            // Calculate chord notes
            $notes = $this->chordService->calculateChord($chordRoot, $chordType, $position, $inversion);

            $chords[] = [
                'notes' => $notes,
                'root' => $chordRoot,
                'type' => $chordType,
                'roman' => $roman
            ];
        }

        // Get rhythm pattern
        $pattern = $this->getRhythmPattern();
        $measureDuration = $this->getMeasureDuration();

        // Ensure chords is a proper array (not associative)
        $chords = array_values($chords);

        \Log::info('About to dispatch play-rhythm-pattern event', [
            'chords_count' => count($chords),
            'chords_json' => json_encode($chords),
            'pattern' => $pattern,
            'rhythm' => $this->selectedRhythm
        ]);

        // Dispatch to Alpine.js rhythm player using named parameters
        $this->dispatch('play-rhythm-pattern',
            chords: $chords,
            pattern: $pattern,
            rhythm: $this->selectedRhythm,
            bpm: $this->bpm,
            timeSignature: $this->timeSignature,
            measureDuration: $measureDuration
        );

        \Log::info('Event dispatched successfully');
    }
    
    private function getMeasureDuration(): float
    {
        // Get beats per measure from time signature
        $parts = explode('/', $this->timeSignature);
        $beatsPerMeasure = (int)$parts[0];
        
        // Calculate measure duration based on BPM
        $beatDuration = 60 / $this->bpm; // Duration of one beat in seconds
        return $beatDuration * $beatsPerMeasure; // Total measure duration
    }
    
    private function getRhythmPattern(): array
    {
        // Returns timing and note pattern based on selected rhythm and time signature
        $beatDuration = 60 / $this->bpm; // Duration of one beat in seconds
        
        // Get note values based on time signature
        $parts = explode('/', $this->timeSignature);
        $noteValue = (int)$parts[1]; // Bottom number (4 = quarter note, 8 = eighth note)
        $baseDuration = 60 / $this->bpm; // Quarter note duration at current BPM
        
        // Adjust duration based on time signature's note value
        if ($noteValue == 8) {
            $baseDuration = $baseDuration / 2; // Eighth note base
        } elseif ($noteValue == 2) {
            $baseDuration = $baseDuration * 2; // Half note base
        }
        
        switch ($this->selectedRhythm) {
            case 'alberti':
                // Alberti bass: 16th note pattern
                return [
                    'pattern' => ['root', 'fifth', 'third', 'fifth'],
                    'duration' => $baseDuration / 4, // 16th notes
                    'repeat' => true
                ];
                
            case 'waltz':
                // Waltz: quarter note pattern in 3/4
                return [
                    'pattern' => ['root', 'chord', 'chord'],
                    'duration' => $baseDuration, // Quarter notes
                    'repeat' => true
                ];
                
            case 'broken':
                // Broken chord: eighth notes
                return [
                    'pattern' => ['sequential'],
                    'duration' => $baseDuration / 2, // Eighth notes
                    'repeat' => false
                ];
                
            case 'arpeggio':
                // Arpeggio: 16th notes
                return [
                    'pattern' => ['root', 'third', 'fifth', 'octave'],
                    'duration' => $baseDuration / 4, // 16th notes
                    'repeat' => true
                ];
                
            case 'march':
                // March: quarter note strong-weak
                return [
                    'pattern' => ['chord_strong', 'chord_weak'],
                    'duration' => $baseDuration, // Quarter notes
                    'repeat' => true
                ];
                
            case 'ballad':
                // Ballad: whole note sustain
                return [
                    'pattern' => ['chord_sustained'],
                    'duration' => $baseDuration * 4, // Whole note
                    'repeat' => false
                ];
                
            case 'ragtime':
                // Ragtime: syncopated eighth notes
                return [
                    'pattern' => ['bass', 'rest', 'chord', 'chord'],
                    'duration' => $baseDuration / 2, // Eighth notes
                    'repeat' => true
                ];
                
            default: // block
                // Block chords: quarter note duration
                return [
                    'pattern' => ['chord_full'],
                    'duration' => $baseDuration, // Quarter note
                    'repeat' => false
                ];
        }
    }
    
    public function calculateVoiceLeadingAnalysis()
    {
        $this->voiceLeadingAnalysis = [];
        $progression = $this->progressions[$this->selectedProgression];
        
        // Calculate for each starting inversion (0, 1, 2)
        for ($startInv = 0; $startInv <= 2; $startInv++) {
            $totalDistance = 0;
            $chordSequence = [];
            $previousChord = null;
            $currentInversion = $startInv;
            
            foreach ($progression as $index => $roman) {
                [$chordRoot, $chordType] = $this->romanToRoot($roman, $this->selectedKey);
                
                // Get octave from startPosition
                $octave = preg_match('/(\d)$/', $this->startPosition, $matches) ? $matches[1] : '4';
                $position = $chordRoot . $octave;
                
                // Calculate chord with current inversion
                $notes = $this->chordService->calculateChord($chordRoot, $chordType, $position, $currentInversion);
                
                $chordInfo = [
                    'roman' => $roman,
                    'chord' => $chordRoot . ($chordType === 'major' ? 'maj' : 'min'),
                    'inversion' => $currentInversion,
                    'notes' => implode(' ', $notes),
                    'distance' => 0
                ];
                
                // Calculate distance from previous chord
                if ($previousChord !== null) {
                    $distance = $this->chordService->calculateVoiceLeadingDistance($previousChord, $notes);
                    $chordInfo['distance'] = $distance;
                    $totalDistance += $distance;
                    
                    // Find optimal inversion for next chord (greedy approach)
                    if ($index < count($progression) - 1) {
                        $nextRoman = $progression[$index + 1];
                        [$nextRoot, $nextType] = $this->romanToRoot($nextRoman, $this->selectedKey);
                        $nextPosition = $nextRoot . $octave;
                        
                        $minDistance = PHP_INT_MAX;
                        $bestInversion = 0;
                        
                        // Try each inversion for the next chord
                        for ($inv = 0; $inv <= 2; $inv++) {
                            $testNotes = $this->chordService->calculateChord($nextRoot, $nextType, $nextPosition, $inv);
                            $testDistance = $this->chordService->calculateVoiceLeadingDistance($notes, $testNotes);
                            
                            if ($testDistance < $minDistance) {
                                $minDistance = $testDistance;
                                $bestInversion = $inv;
                            }
                        }
                        
                        $currentInversion = $bestInversion;
                    }
                }
                
                $chordSequence[] = $chordInfo;
                $previousChord = $notes;
            }
            
            $this->voiceLeadingAnalysis[] = [
                'startingInversion' => $startInv,
                'totalDistance' => $totalDistance,
                'sequence' => $chordSequence
            ];
        }
        
        // Sort by total distance to show best option first
        usort($this->voiceLeadingAnalysis, function($a, $b) {
            return $a['totalDistance'] <=> $b['totalDistance'];
        });
    }
    
    public function render()
    {
        return view('livewire.math-chord-test');
    }
}