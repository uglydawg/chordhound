<!DOCTYPE html>
<html>
<head>
    <title>Piano Chords Test</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <h1>Piano Chords Application</h1>
    <p>If you can see this, the basic setup is working!</p>
    
    <div>
        <h2>Test Components:</h2>
        <ul>
            <li>ChordSelector: <livewire:chord-selector /></li>
            <li>ChordDisplay: <livewire:chord-display /></li>
        </ul>
    </div>
    
    @livewireScripts
    @fluxScripts
</body>
</html>