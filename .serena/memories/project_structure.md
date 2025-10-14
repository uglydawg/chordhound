# ChordHound Project Structure

## Directory Layout
```
├── app/                      # Application code
│   ├── Http/
│   │   ├── Controllers/     # Request controllers
│   │   │   ├── Auth/       # Authentication controllers
│   │   │   ├── ChordController.php
│   │   │   ├── QuizController.php
│   │   │   ├── LessonController.php
│   │   │   ├── LearningController.php
│   │   │   ├── SocialAuthController.php
│   │   │   └── TeacherDashboardController.php
│   │   └── Middleware/      # Custom middleware
│   │       └── EnsureUserIsTeacher.php
│   ├── Livewire/           # Livewire components
│   │   ├── Auth/           # Authentication components
│   │   ├── Settings/       # Settings components
│   │   ├── ChordGrid.php   # Main chord progression editor
│   │   ├── ChordDisplay.php # 2x2 grid chord display
│   │   ├── ChordSelector.php
│   │   ├── ChordPiano.php
│   │   ├── InteractivePiano.php
│   │   ├── VoiceLeadingAnimation.php
│   │   └── PrintChordSheet.php
│   ├── Models/             # Eloquent models
│   │   ├── User.php
│   │   ├── ChordSet.php
│   │   ├── ChordSetChord.php
│   │   ├── MagicLink.php
│   │   ├── AuthCode.php
│   │   ├── Lesson.php
│   │   ├── Quiz.php
│   │   └── Achievement.php
│   ├── Services/           # Business logic services
│   │   ├── ChordService.php          # Core chord calculations
│   │   ├── ChordVoicingService.php   # Voice leading logic
│   │   ├── AchievementService.php
│   │   ├── LearningPathService.php
│   │   └── QuizService.php
│   └── Providers/          # Service providers
├── bootstrap/              # Framework bootstrap files
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   ├── factories/          # Model factories
│   └── seeders/           # Database seeders
├── public/                 # Publicly accessible files
├── resources/
│   ├── views/             # Blade templates
│   │   ├── livewire/      # Livewire component views
│   │   ├── components/    # Blade components
│   │   │   └── flux/      # Flux UI component overrides
│   │   ├── layouts/       # Layout templates
│   │   └── auth/          # Authentication views
│   ├── css/               # Stylesheets
│   └── js/                # JavaScript files
├── routes/
│   └── web.php            # Web routes
├── storage/               # Storage directory
├── tests/                 # Test files
│   ├── Browser/           # Dusk browser tests
│   ├── Feature/           # Feature tests
│   └── Unit/              # Unit tests
├── .agent-os/             # Agent OS configuration
│   ├── product/           # Product documentation
│   │   ├── mission.md
│   │   ├── tech-stack.md
│   │   ├── roadmap.md
│   │   └── decisions.md
│   ├── specs/             # Feature specifications
│   └── instructions/      # Workflow instructions
└── vendor/                # Composer dependencies

## Key Files
- `artisan` - Laravel command-line interface
- `composer.json` - PHP dependencies
- `package.json` - JavaScript dependencies
- `.env` - Environment configuration
- `vite.config.js` - Vite bundler configuration
- `tailwind.config.js` - Tailwind CSS configuration
- `CLAUDE.md` - Project-specific Claude instructions
- `README.md` - Project documentation

## Important Components

### Core Services
- **ChordService**: Handles chord calculations, inversions, blue notes
- **ChordVoicingService**: Voice leading optimization algorithms

### Main UI Components
- **ChordGrid**: Central chord progression interface
- **ChordDisplay**: Visual representation of chords
- **ChordPiano**: Interactive piano keyboard
- **VoiceLeadingAnimation**: Smooth chord transitions

### Authentication Flow
- Multiple auth methods handled by Livewire components
- Social auth via SocialAuthController
- Magic links and auth codes for passwordless login