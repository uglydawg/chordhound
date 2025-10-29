/**
 * Piano Canvas 2D Renderer
 * High-performance canvas-based piano keyboard visualization
 */

export class PianoCanvas {
    constructor(canvasElement, options = {}) {
        this.canvas = canvasElement;
        this.ctx = this.canvas.getContext('2d');

        // Configuration
        this.options = {
            startNote: options.startNote || 'C3',
            endNote: options.endNote || 'B5',
            whiteKeyWidth: options.whiteKeyWidth || 40,
            whiteKeyHeight: options.whiteKeyHeight || 150,
            blackKeyWidth: options.blackKeyWidth || 24,
            blackKeyHeight: options.blackKeyHeight || 100,
            // Left hand colors (Bass - Blue)
            leftHandColor: options.leftHandColor || '#3B82F6',
            leftHandBlackColor: options.leftHandBlackColor || '#2563EB',
            // Right hand colors (Treble - Green)
            rightHandColor: options.rightHandColor || '#10B981',
            rightHandBlackColor: options.rightHandBlackColor || '#059669',
            // Both hands color (Purple)
            bothHandsColor: options.bothHandsColor || '#8B5CF6',
            bothHandsBlackColor: options.bothHandsBlackColor || '#7C3AED',
            // Default colors
            whiteKeyColor: options.whiteKeyColor || '#FFFFFF',
            blackKeyColor: options.blackKeyColor || '#1A1A1A',
            borderColor: options.borderColor || '#888888',
            textColor: options.textColor || '#333333',
            activeTextColor: options.activeTextColor || '#FFFFFF',
        };

        this.keys = this.generateKeys();

        // Separate tracking for left and right hand notes
        this.leftHandNotes = new Set();
        this.rightHandNotes = new Set();

        // Legacy support
        this.activeNotes = new Set();

        this.setupCanvas();
        this.setupInteraction();
        this.draw();

        // Handle window resize
        window.addEventListener('resize', () => this.handleResize());
    }

    setupCanvas() {
        // Handle high-DPI displays
        const dpr = window.devicePixelRatio || 1;
        const rect = this.canvas.getBoundingClientRect();

        this.canvas.width = rect.width * dpr;
        this.canvas.height = rect.height * dpr;
        this.ctx.scale(dpr, dpr);

        this.canvas.style.width = rect.width + 'px';
        this.canvas.style.height = rect.height + 'px';

        this.canvasWidth = rect.width;
        this.canvasHeight = rect.height;
    }

    handleResize() {
        this.setupCanvas();
        this.draw();
    }

    generateKeys() {
        const notePattern = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        const keys = [];

        // Parse start and end notes
        const startOctave = parseInt(this.options.startNote.slice(-1));
        const endOctave = parseInt(this.options.endNote.slice(-1));
        const startNoteIndex = notePattern.indexOf(this.options.startNote.slice(0, -1));
        const endNoteIndex = notePattern.indexOf(this.options.endNote.slice(0, -1));

        let whiteKeyX = 0;

        for (let octave = startOctave; octave <= endOctave; octave++) {
            const startIdx = (octave === startOctave) ? startNoteIndex : 0;
            const endIdx = (octave === endOctave) ? endNoteIndex : 11;

            for (let i = startIdx; i <= endIdx; i++) {
                const noteName = notePattern[i];
                const note = noteName + octave;
                const isBlack = noteName.includes('#');

                if (isBlack) {
                    // Position black key between white keys
                    keys.push({
                        note,
                        x: whiteKeyX - this.options.blackKeyWidth / 2,
                        y: 0,
                        width: this.options.blackKeyWidth,
                        height: this.options.blackKeyHeight,
                        isBlack: true
                    });
                } else {
                    keys.push({
                        note,
                        x: whiteKeyX,
                        y: 0,
                        width: this.options.whiteKeyWidth,
                        height: this.options.whiteKeyHeight,
                        isBlack: false
                    });
                    whiteKeyX += this.options.whiteKeyWidth;
                }
            }
        }

        return keys;
    }

    draw() {
        // Clear canvas
        this.ctx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);

        // Draw white keys first (background layer)
        this.keys.filter(k => !k.isBlack).forEach(key => {
            this.drawKey(key);
        });

        // Draw black keys on top (foreground layer)
        this.keys.filter(k => k.isBlack).forEach(key => {
            this.drawKey(key);
        });
    }

    drawKey(key) {
        // Determine which hand(s) are playing this note
        const isLeftHand = this.leftHandNotes.has(key.note);
        const isRightHand = this.rightHandNotes.has(key.note);
        const isLegacy = this.activeNotes.has(key.note);

        // Determine the state and color
        let handState = 'none';
        let activeColor, activeBlackColor;

        if (isLeftHand && isRightHand) {
            handState = 'both';
            activeColor = this.options.bothHandsColor;
            activeBlackColor = this.options.bothHandsBlackColor;
        } else if (isLeftHand) {
            handState = 'left';
            activeColor = this.options.leftHandColor;
            activeBlackColor = this.options.leftHandBlackColor;
        } else if (isRightHand) {
            handState = 'right';
            activeColor = this.options.rightHandColor;
            activeBlackColor = this.options.rightHandBlackColor;
        } else if (isLegacy) {
            // Legacy support for old API
            handState = 'legacy';
            activeColor = this.options.leftHandColor; // Default to left hand color
            activeBlackColor = this.options.leftHandBlackColor;
        }

        const isActive = handState !== 'none';

        // Save context for 3D effects
        this.ctx.save();

        if (key.isBlack) {
            this.drawBlackKey(key, isActive, activeBlackColor);
        } else {
            this.drawWhiteKey(key, isActive, activeColor);
        }

        this.ctx.restore();
    }

    drawBlackKey(key, isActive, activeColor) {
        const pressDepth = isActive ? 3 : 0;
        const x = key.x;
        const y = key.y + pressDepth;
        const w = key.width;
        const h = key.height - pressDepth;

        // Base color
        const baseColor = isActive ? (activeColor || this.options.leftHandBlackColor) : this.options.blackKeyColor;

        // Shadow cast by black key onto white keys (only when not pressed)
        if (!isActive) {
            this.ctx.fillStyle = 'rgba(0, 0, 0, 0.4)';
            this.ctx.fillRect(x + 2, y + h, w - 4, 4);
        }

        // Main key body with gradient for depth
        const bodyGradient = this.ctx.createLinearGradient(x, y, x + w, y);
        if (isActive) {
            bodyGradient.addColorStop(0, this.darkenColor(baseColor, 0.3));
            bodyGradient.addColorStop(0.5, baseColor);
            bodyGradient.addColorStop(1, this.darkenColor(baseColor, 0.2));
        } else {
            bodyGradient.addColorStop(0, this.darkenColor(baseColor, 0.4));
            bodyGradient.addColorStop(0.3, baseColor);
            bodyGradient.addColorStop(0.7, baseColor);
            bodyGradient.addColorStop(1, this.darkenColor(baseColor, 0.3));
        }
        this.ctx.fillStyle = bodyGradient;
        this.ctx.fillRect(x, y, w, h);

        // Front face highlight from light source (top 40% of key)
        const frontHighlight = this.ctx.createLinearGradient(x, y, x, y + h * 0.4);
        frontHighlight.addColorStop(0, 'rgba(255, 255, 255, 0.25)');
        frontHighlight.addColorStop(0.5, 'rgba(255, 255, 255, 0.12)');
        frontHighlight.addColorStop(1, 'rgba(255, 255, 255, 0)');
        this.ctx.fillStyle = frontHighlight;
        this.ctx.fillRect(x + 2, y, w - 4, h * 0.4);

        // Top edge highlight (glossy finish)
        const topHighlight = this.ctx.createLinearGradient(x, y, x, y + 8);
        topHighlight.addColorStop(0, 'rgba(255, 255, 255, 0.35)');
        topHighlight.addColorStop(1, 'rgba(255, 255, 255, 0)');
        this.ctx.fillStyle = topHighlight;
        this.ctx.fillRect(x + 3, y, w - 6, 8);

        // Left edge highlight (catching light from left-front)
        const leftHighlight = this.ctx.createLinearGradient(x, y, x + w * 0.2, y);
        leftHighlight.addColorStop(0, 'rgba(255, 255, 255, 0.2)');
        leftHighlight.addColorStop(1, 'rgba(255, 255, 255, 0)');
        this.ctx.fillStyle = leftHighlight;
        this.ctx.fillRect(x, y, w * 0.2, h);

        // Right edge shadow for depth
        const rightShadow = this.ctx.createLinearGradient(x + w * 0.8, y, x + w, y);
        rightShadow.addColorStop(0, 'rgba(0, 0, 0, 0)');
        rightShadow.addColorStop(1, 'rgba(0, 0, 0, 0.5)');
        this.ctx.fillStyle = rightShadow;
        this.ctx.fillRect(x + w * 0.8, y, w * 0.2, h);

        // Bottom shadow for depth (key getting darker at bottom)
        const bottomShadow = this.ctx.createLinearGradient(x, y + h * 0.6, x, y + h);
        bottomShadow.addColorStop(0, 'rgba(0, 0, 0, 0)');
        bottomShadow.addColorStop(1, 'rgba(0, 0, 0, 0.6)');
        this.ctx.fillStyle = bottomShadow;
        this.ctx.fillRect(x, y + h * 0.6, w, h * 0.4);

        // Subtle specular highlight (glossy piano finish)
        if (!isActive) {
            const specular = this.ctx.createRadialGradient(
                x + w * 0.4, y + h * 0.15, 0,
                x + w * 0.4, y + h * 0.15, w * 0.5
            );
            specular.addColorStop(0, 'rgba(255, 255, 255, 0.15)');
            specular.addColorStop(1, 'rgba(255, 255, 255, 0)');
            this.ctx.fillStyle = specular;
            this.ctx.fillRect(x, y, w, h * 0.4);
        }

        // Border for definition
        this.ctx.strokeStyle = 'rgba(0, 0, 0, 0.8)';
        this.ctx.lineWidth = 1;
        this.ctx.strokeRect(x + 0.5, y + 0.5, w - 1, h - 1);

        // Active state glow
        if (isActive) {
            this.ctx.shadowColor = 'rgba(74, 144, 226, 0.8)';
            this.ctx.shadowBlur = 15;
            this.ctx.shadowOffsetX = 0;
            this.ctx.shadowOffsetY = 0;
            this.ctx.strokeStyle = '#60A5FA';
            this.ctx.lineWidth = 2.5;
            this.ctx.strokeRect(x + 2, y + 2, w - 4, h - 4);
        }
    }

    drawWhiteKey(key, isActive, activeColor) {
        const pressDepth = isActive ? 2 : 0;
        const x = key.x;
        const y = key.y + pressDepth;
        const w = key.width;
        const h = key.height - pressDepth;

        // Base color
        const baseColor = isActive ? (activeColor || this.options.leftHandColor) : this.options.whiteKeyColor;

        // Main key body
        this.ctx.fillStyle = baseColor;
        this.ctx.fillRect(x, y, w, h);

        // Beveled top edge highlight (light from above-front)
        const topBevel = this.ctx.createLinearGradient(x, y, x, y + 12);
        if (isActive) {
            topBevel.addColorStop(0, this.lightenColor(baseColor, 0.3));
            topBevel.addColorStop(1, 'rgba(255, 255, 255, 0)');
        } else {
            topBevel.addColorStop(0, 'rgba(255, 255, 255, 1)');
            topBevel.addColorStop(0.6, 'rgba(255, 255, 255, 0.7)');
            topBevel.addColorStop(1, 'rgba(255, 255, 255, 0)');
        }
        this.ctx.fillStyle = topBevel;
        this.ctx.fillRect(x, y, w, 12);

        // Left edge bevel (catching light)
        const leftBevel = this.ctx.createLinearGradient(x, y, x + 6, y);
        if (isActive) {
            leftBevel.addColorStop(0, this.lightenColor(baseColor, 0.2));
            leftBevel.addColorStop(1, 'rgba(255, 255, 255, 0)');
        } else {
            leftBevel.addColorStop(0, 'rgba(255, 255, 255, 0.9)');
            leftBevel.addColorStop(1, 'rgba(255, 255, 255, 0)');
        }
        this.ctx.fillStyle = leftBevel;
        this.ctx.fillRect(x, y, 6, h);

        // Right edge shadow for depth
        const rightBevel = this.ctx.createLinearGradient(x + w - 6, y, x + w, y);
        rightBevel.addColorStop(0, 'rgba(0, 0, 0, 0)');
        if (isActive) {
            rightBevel.addColorStop(1, 'rgba(0, 0, 0, 0.25)');
        } else {
            rightBevel.addColorStop(1, 'rgba(0, 0, 0, 0.15)');
        }
        this.ctx.fillStyle = rightBevel;
        this.ctx.fillRect(x + w - 6, y, 6, h);

        // Bottom shadow for depth perception
        const bottomGradient = this.ctx.createLinearGradient(x, y + h - 40, x, y + h);
        bottomGradient.addColorStop(0, 'rgba(0, 0, 0, 0)');
        if (isActive) {
            bottomGradient.addColorStop(1, 'rgba(0, 0, 0, 0.25)');
        } else {
            bottomGradient.addColorStop(0.7, 'rgba(0, 0, 0, 0.05)');
            bottomGradient.addColorStop(1, 'rgba(0, 0, 0, 0.15)');
        }
        this.ctx.fillStyle = bottomGradient;
        this.ctx.fillRect(x, y + h - 40, w, 40);

        // Subtle center highlight (glossy finish catching light)
        if (!isActive) {
            const centerHighlight = this.ctx.createLinearGradient(x + w * 0.3, y + h * 0.2, x + w * 0.7, y + h * 0.4);
            centerHighlight.addColorStop(0, 'rgba(255, 255, 255, 0)');
            centerHighlight.addColorStop(0.5, 'rgba(255, 255, 255, 0.15)');
            centerHighlight.addColorStop(1, 'rgba(255, 255, 255, 0)');
            this.ctx.fillStyle = centerHighlight;
            this.ctx.fillRect(x + w * 0.2, y + h * 0.15, w * 0.6, h * 0.3);
        }

        // Specular highlight at top (simulating glossy piano finish)
        if (!isActive) {
            const specular = this.ctx.createRadialGradient(
                x + w * 0.5, y + h * 0.08, 0,
                x + w * 0.5, y + h * 0.08, w * 0.4
            );
            specular.addColorStop(0, 'rgba(255, 255, 255, 0.3)');
            specular.addColorStop(0.5, 'rgba(255, 255, 255, 0.1)');
            specular.addColorStop(1, 'rgba(255, 255, 255, 0)');
            this.ctx.fillStyle = specular;
            this.ctx.fillRect(x, y, w, h * 0.25);
        }

        // Inner border shadow (key depth illusion)
        this.ctx.strokeStyle = 'rgba(0, 0, 0, 0.1)';
        this.ctx.lineWidth = 1;
        this.ctx.strokeRect(x + 2.5, y + 2.5, w - 5, h - 5);

        // Outer border
        this.ctx.strokeStyle = this.options.borderColor;
        this.ctx.lineWidth = 1.5;
        this.ctx.strokeRect(x + 0.75, y + 0.75, w - 1.5, h - 1.5);

        // Draw key label
        const textColor = isActive ? this.options.activeTextColor : this.options.textColor;
        this.ctx.fillStyle = textColor;
        this.ctx.font = '11px -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'bottom';

        // Text shadow for better readability
        if (!isActive) {
            this.ctx.shadowColor = 'rgba(255, 255, 255, 0.8)';
            this.ctx.shadowBlur = 2;
            this.ctx.shadowOffsetX = 0;
            this.ctx.shadowOffsetY = 1;
        }

        this.ctx.fillText(
            key.note,
            x + w / 2,
            y + h - 8
        );

        // Reset shadow
        this.ctx.shadowColor = 'transparent';
        this.ctx.shadowBlur = 0;

        // Active state glow
        if (isActive) {
            this.ctx.shadowColor = 'rgba(59, 130, 246, 0.7)';
            this.ctx.shadowBlur = 12;
            this.ctx.shadowOffsetX = 0;
            this.ctx.shadowOffsetY = 0;
            this.ctx.strokeStyle = '#3B82F6';
            this.ctx.lineWidth = 2.5;
            this.ctx.strokeRect(x + 2, y + 2, w - 4, h - 4);
        }
    }

    // Helper function to darken a color
    darkenColor(color, amount) {
        const hex = color.replace('#', '');
        const r = Math.max(0, parseInt(hex.substring(0, 2), 16) * (1 - amount));
        const g = Math.max(0, parseInt(hex.substring(2, 4), 16) * (1 - amount));
        const b = Math.max(0, parseInt(hex.substring(4, 6), 16) * (1 - amount));
        return `rgb(${Math.round(r)}, ${Math.round(g)}, ${Math.round(b)})`;
    }

    // Helper function to lighten a color
    lightenColor(color, amount) {
        const hex = color.replace('#', '');
        const r = Math.min(255, parseInt(hex.substring(0, 2), 16) + (255 * amount));
        const g = Math.min(255, parseInt(hex.substring(2, 4), 16) + (255 * amount));
        const b = Math.min(255, parseInt(hex.substring(4, 6), 16) + (255 * amount));
        return `rgb(${Math.round(r)}, ${Math.round(g)}, ${Math.round(b)})`;
    }

    // Legacy API (for backwards compatibility)
    setActiveNotes(notes) {
        // Convert to Set for O(1) lookup
        this.activeNotes = new Set(Array.isArray(notes) ? notes : [notes]);
        this.draw();
    }

    clearActiveNotes() {
        this.activeNotes.clear();
        this.draw();
    }

    // Two-Handed API
    setLeftHandNotes(notes) {
        this.leftHandNotes = new Set(Array.isArray(notes) ? notes : [notes]);
        this.draw();
    }

    setRightHandNotes(notes) {
        this.rightHandNotes = new Set(Array.isArray(notes) ? notes : [notes]);
        this.draw();
    }

    setBothHands(leftNotes, rightNotes) {
        this.leftHandNotes = new Set(Array.isArray(leftNotes) ? leftNotes : [leftNotes]);
        this.rightHandNotes = new Set(Array.isArray(rightNotes) ? rightNotes : [rightNotes]);
        this.draw();
    }

    clearLeftHand() {
        this.leftHandNotes.clear();
        this.draw();
    }

    clearRightHand() {
        this.rightHandNotes.clear();
        this.draw();
    }

    clearAll() {
        this.leftHandNotes.clear();
        this.rightHandNotes.clear();
        this.activeNotes.clear();
        this.draw();
    }

    setupInteraction() {
        // Mouse click
        this.canvas.addEventListener('click', (e) => {
            const key = this.getKeyAtPosition(e);
            if (key) {
                this.onKeyClick(key.note);
            }
        });

        // Touch support for mobile
        this.canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            const key = this.getKeyAtPosition(touch);
            if (key) {
                this.onKeyClick(key.note);
            }
        });

        // Hover effect (optional)
        this.canvas.addEventListener('mousemove', (e) => {
            const key = this.getKeyAtPosition(e);
            this.canvas.style.cursor = key ? 'pointer' : 'default';
        });
    }

    getKeyAtPosition(event) {
        const rect = this.canvas.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        // Check black keys first (they're on top visually)
        for (const key of this.keys.filter(k => k.isBlack)) {
            if (this.isPointInKey(x, y, key)) {
                return key;
            }
        }

        // Then check white keys
        for (const key of this.keys.filter(k => !k.isBlack)) {
            if (this.isPointInKey(x, y, key)) {
                return key;
            }
        }

        return null;
    }

    isPointInKey(x, y, key) {
        return x >= key.x &&
               x <= key.x + key.width &&
               y >= key.y &&
               y <= key.y + key.height;
    }

    onKeyClick(note) {
        // Dispatch custom event that Livewire/Alpine can listen to
        this.canvas.dispatchEvent(new CustomEvent('piano-key-click', {
            detail: { note },
            bubbles: true
        }));
    }

    destroy() {
        window.removeEventListener('resize', this.handleResize);
        // Remove event listeners
        this.canvas.replaceWith(this.canvas.cloneNode(true));
    }
}
