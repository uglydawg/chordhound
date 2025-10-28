// ChordHound Service Worker for caching assets
const CACHE_NAME = 'chordhound-v1';
const AUDIO_CACHE_NAME = 'chordhound-audio-v1';

// Assets to cache immediately on install
const STATIC_ASSETS = [
    '/js/multi-instrument-player.js',
    '/audio/chordchord-cinematic-piano.mp3',
    '/audio/chordchord-cinematic-piano-meta.json'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        Promise.all([
            caches.open(CACHE_NAME).then(cache => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            }),
            caches.open(AUDIO_CACHE_NAME).then(cache => {
                console.log('[SW] Audio cache created');
            })
        ])
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME && cacheName !== AUDIO_CACHE_NAME) {
                        console.log('[SW] Removing old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Fetch event - serve from cache when possible
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Special handling for audio files
    if (url.pathname.startsWith('/audio/')) {
        event.respondWith(
            caches.open(AUDIO_CACHE_NAME).then(cache => {
                return cache.match(request).then(response => {
                    if (response) {
                        console.log('[SW] Serving audio from cache:', url.pathname);
                        return response;
                    }
                    
                    // Not in cache, fetch and cache it
                    return fetch(request).then(response => {
                        if (response.status === 200) {
                            cache.put(request, response.clone());
                            console.log('[SW] Cached audio file:', url.pathname);
                        }
                        return response;
                    });
                });
            })
        );
        return;
    }
    
    // Special handling for Tone.js from app.js bundle
    if (url.pathname.includes('/build/assets/app') && url.pathname.endsWith('.js')) {
        event.respondWith(
            caches.open(CACHE_NAME).then(cache => {
                return cache.match(request).then(response => {
                    if (response) {
                        console.log('[SW] Serving app.js bundle from cache');
                        return response;
                    }
                    
                    return fetch(request).then(response => {
                        if (response.status === 200) {
                            cache.put(request, response.clone());
                            console.log('[SW] Cached app.js bundle');
                        }
                        return response;
                    });
                });
            })
        );
        return;
    }
    
    // Network-first strategy for other assets
    event.respondWith(
        fetch(request).catch(() => {
            return caches.match(request);
        })
    );
});