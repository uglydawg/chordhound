<x-layouts.app :title="__('ChordHound - Free Piano Chord Generator & Music Learning Platform')">
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Hero Section -->
            <section class="text-center py-8">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    Master Piano Chords with Your Friendly
                    <span class="text-orange-600 dark:text-orange-400">Musical Companion</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-8 max-w-3xl mx-auto">
                    ChordHound makes learning piano chords fun and intuitive. Create chord progressions, 
                    visualize voice leading, and unlock your musical potential with our free interactive platform.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <flux:button size="base" variant="primary" :href="route('chords.index')" wire:navigate>
                        Learning About Chords
                    </flux:button>
                    @guest
                        <flux:button size="base" variant="outline" :href="route('register')" wire:navigate>
                            Sign Up Free
                        </flux:button>
                    @endguest
                    @auth
                        <flux:button size="base" variant="outline" :href="route('learning.index')" wire:navigate>
                            Visit Learning Hub
                        </flux:button>
                    @endauth
                </div>
                <div class="mt-8 flex items-center justify-center space-x-6 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center">
                        <flux:icon.check class="w-5 h-5 text-green-500 mr-2" />
                        No credit card required
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="py-8">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Everything You Need to Master Chords
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        Powerful features designed to accelerate your musical journey
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <flux:card class="text-center">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mx-auto mb-6">
                            <flux:icon.musical-note class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Interactive Chord Builder
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Build chords visually with our intuitive interface. Select tones, semitones, and inversions 
                            to create any chord progression imaginable.
                        </p>
                    </flux:card>

                    <!-- Feature 2 -->
                    <flux:card class="text-center">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mx-auto mb-6">
                            <flux:icon.sparkles class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Voice Leading Optimization
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Our intelligent algorithm suggests optimal inversions for smooth chord transitions, 
                            helping you create professional-sounding progressions.
                        </p>
                    </flux:card>

                    <!-- Feature 3 -->
                    <flux:card class="text-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mx-auto mb-6">
                            <flux:icon.star class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Blue Note Detection
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Automatically highlight tension notes and harmonic relationships within your progressions 
                            to deepen your understanding of music theory.
                        </p>
                    </flux:card>

                    <!-- Feature 4 -->
                    <flux:card class="text-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mx-auto mb-6">
                            <flux:icon.academic-cap class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Interactive Learning Hub
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Structured lessons, quizzes, and exercises guide you from chord basics to advanced 
                            progressions with gamified achievements.
                        </p>
                    </flux:card>

                    <!-- Feature 5 -->
                    <flux:card class="text-center">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mx-auto mb-6">
                            <flux:icon.folder class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Save & Share Progressions
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Save your favorite chord progressions, organize them into sets, and share them with 
                            fellow musicians or students.
                        </p>
                    </flux:card>

                    <!-- Feature 6 -->
                    <flux:card class="text-center">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mx-auto mb-6">
                            <flux:icon.printer class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Print-Ready Chord Sheets
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Generate beautiful, print-optimized chord sheets for practice sessions, teaching, 
                            or sharing with band members.
                        </p>
                    </flux:card>
                </div>
            </section>

            <!-- How It Works Section -->
            <section id="how-it-works" class="py-16 bg-gray-50 dark:bg-zinc-800 rounded-2xl px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Start Playing in Minutes
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        ChordHound makes learning piano chords simple and enjoyable
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                            1
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Choose Your Chords
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Select from major, minor, diminished, or augmented chords. Pick your root note and ChordHound 
                            instantly shows you the keys to play.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                            2
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Build Progressions
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Create up to 4-chord progressions with automatic voice leading suggestions. See how chords 
                            flow smoothly from one to the next.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                            3
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Learn & Practice
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Save your progressions, print chord sheets, and access our Learning Hub for structured 
                            lessons that build your skills step by step.
                        </p>
                    </div>
                </div>

                <div class="mt-16 text-center">
                    <flux:button size="base" variant="primary" :href="route('chords.index')" wire:navigate>
                        Try ChordHound Now - It's Free!
                    </flux:button>
                </div>
            </section>

            <!-- Testimonials Section -->
            <section id="testimonials" class="py-16">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Loved by Musicians Worldwide
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        Join thousands of musicians who've transformed their chord knowledge
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <flux:card>
                        <div class="flex mb-4">
                            @for ($i = 0; $i < 5; $i++)
                                <flux:icon.star class="w-5 h-5 text-yellow-400 fill-current" />
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            "ChordHound transformed how I teach piano. The visual chord builder helps my students 
                            understand inversions instantly. The dog theme keeps younger students engaged!"
                        </p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-orange-700 font-semibold">ST</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Sarah Thompson</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Piano Teacher</p>
                            </div>
                        </div>
                    </flux:card>

                    <!-- Testimonial 2 -->
                    <flux:card>
                        <div class="flex mb-4">
                            @for ($i = 0; $i < 5; $i++)
                                <flux:icon.star class="w-5 h-5 text-yellow-400 fill-current" />
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            "As a self-taught musician, ChordHound's voice leading feature was a game-changer. 
                            I finally understand why certain progressions sound so smooth!"
                        </p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-purple-700 font-semibold">MR</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Marcus Rodriguez</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Songwriter</p>
                            </div>
                        </div>
                    </flux:card>

                    <!-- Testimonial 3 -->
                    <flux:card>
                        <div class="flex mb-4">
                            @for ($i = 0; $i < 5; $i++)
                                <flux:icon.star class="w-5 h-5 text-yellow-400 fill-current" />
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            "The Learning Hub is fantastic! I completed the fundamentals module in a week and 
                            now I'm creating my own progressions. Love the achievement badges!"
                        </p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-blue-700 font-semibold">EK</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Emma Kim</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Music Student</p>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="py-20 px-8 bg-gradient-to-r from-orange-600 to-purple-600 rounded-2xl text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-6">
                    Ready to Unleash Your Musical Potential?
                </h2>
                <p class="text-xl text-orange-100 mb-8">
                    Join ChordHound today and start your journey to chord mastery. 
                    No credit card required.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <flux:button size="base" variant="outline" :href="route('register')" wire:navigate>
                        Get Started Free
                    </flux:button>
                    <flux:button size="base" variant="ghost" :href="route('chords.index')" wire:navigate>
                        Try Without Account
                    </flux:button>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>