<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ANG</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: "Press Start 2P", system-ui;
                overflow: hidden;
            }

            #pixelCanvas {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                image-rendering: pixelated;
                image-rendering: -moz-crisp-edges;
                image-rendering: crisp-edges;
            }

            .content {
                position: relative;
                z-index: 10;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .title {
                font-size: 30px;
                color: #ffffff;
                text-shadow: 
                    4px 4px 0px #000000,
                    -2px -2px 0px rgba(0,0,0,0.5);
                padding: 20px;
                /* background: rgba(0, 0, 0, 0.3); */
                border-radius: 10px;
            }
        </style>
    </head>
    <body>
        <canvas id="pixelCanvas"></canvas>
        <div class="content">
            <span class="title" id="typingText"></span>
        </div>

        <script>
            const canvas = document.getElementById('pixelCanvas');
            const ctx = canvas.getContext('2d');

            // Konfiguracja efektu pisania
            const TYPING_CONFIG = {
                targetText: 'ANG - Rekrutacja PHP',
                windowSpeed: 50, // Jak szybko (w ms) okno przesuwa się o 1
                randomSpeed: 10 // Jak szybko (w ms) losują się znaki w oknie
            };

            // Możliwe znaki do losowania
            const RANDOM_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';

            class TypingEffect {
                constructor(element, config) {
                    this.element = element;
                    this.targetText = config.targetText;
                    this.windowSpeed = config.windowSpeed;
                    this.randomSpeed = config.randomSpeed;
                    this.windowSize = 3; // Rozmiar okna
                    
                    this.currentText = Array(this.targetText.length).fill('');
                    this.windowStart = 0; // Początek okna 3 znaków
                    
                    this.start();
                }

                getRandomChar() {
                    return RANDOM_CHARS[Math.floor(Math.random() * RANDOM_CHARS.length)];
                }

                randomizeWindow() {
                    // Losuj znaki w aktualnym oknie
                    for (let i = 0; i < this.windowSize; i++) {
                        const index = this.windowStart + i;
                        if (index >= this.targetText.length) break;
                        
                        // Nie losuj odkrytych znaków ani spacji
                        if (index < this.windowStart || this.targetText[index] === ' ') {
                            this.currentText[index] = this.targetText[index];
                        } else {
                            this.currentText[index] = this.getRandomChar();
                        }
                    }
                    
                    // Wyświetl wszystkie odkryte znaki
                    for (let i = 0; i < this.windowStart; i++) {
                        this.currentText[i] = this.targetText[i];
                    }

                    this.element.textContent = this.currentText.join('');
                }

                moveWindow() {
                    // Odkryj pierwszy znak w oknie
                    if (this.windowStart < this.targetText.length) {
                        this.currentText[this.windowStart] = this.targetText[this.windowStart];
                        
                        // Przesuń okno o 1
                        this.windowStart++;
                        
                        // Jeśli następny znak to spacja, pomiń ją
                        while (this.windowStart < this.targetText.length && 
                               this.targetText[this.windowStart] === ' ') {
                            this.currentText[this.windowStart] = ' ';
                            this.windowStart++;
                        }
                    }

                    // Kontynuuj jeśli nie wszystko odkryte
                    if (this.windowStart < this.targetText.length) {
                        setTimeout(() => this.moveWindow(), this.windowSpeed);
                    } else {
                        // Odkryj ostatni znak po zakończeniu okna
                        this.element.textContent = this.targetText;
                    }
                }

                start() {
                    // Uruchom losowanie w tle
                    const randomInterval = setInterval(() => {
                        if (this.windowStart >= this.targetText.length) {
                            clearInterval(randomInterval);
                            return;
                        }
                        this.randomizeWindow();
                    }, this.randomSpeed);

                    // Uruchom przesuwanie okna
                    this.moveWindow();
                }
            }

            // Uruchom efekt pisania
            const typingElement = document.getElementById('typingText');
            new TypingEffect(typingElement, TYPING_CONFIG);

            // Predefiniowane kolory pixelowe
            const colors = [
                '#993399',
                '#f16780',
                '#046595'
            ];

            const pixelSize = 6;
            let width, height, cols, rows;
            let blobs = [];
            let mouseX = -1000;
            let mouseY = -1000;
            let isDragging = false;
            let draggedBlob = null;

            function resizeCanvas() {
                width = window.innerWidth;
                height = window.innerHeight;
                canvas.width = width;
                canvas.height = height;
                cols = Math.ceil(width / pixelSize);
                rows = Math.ceil(height / pixelSize);
            }

            class Blob {
                constructor() {
                    this.x = Math.random() * cols;
                    this.y = Math.random() * rows;
                    this.radius = 16 + Math.random() * 36;
                    this.vx = (Math.random() - 0.5) * 0.08;
                    this.vy = (Math.random() - 0.5) * 0.08;
                    this.color = colors[Math.floor(Math.random() * colors.length)];
                    this.phase = Math.random() * Math.PI * 2;
                    this.points = 12;
                    this.noiseOffsets = [];
                    this.noiseStrengths = [];
                    for (let i = 0; i < this.points; i++) {
                        this.noiseOffsets.push(Math.random() * Math.PI * 2);
                        this.noiseStrengths.push(0.5 + Math.random() * 0.7);
                    }
                }

                update() {
                    // Jeśli ta plama jest przeciągana, podążaj za kursorem
                    if (isDragging && draggedBlob === this) {
                        const targetX = mouseX;
                        const targetY = mouseY;
                        const dx = targetX - this.x;
                        const dy = targetY - this.y;
                        
                        // Płynne podążanie za kursorem
                        this.x += dx * 0.2;
                        this.y += dy * 0.2;
                        this.vx = dx * 0.1;
                        this.vy = dy * 0.1;
                    } else {
                        // Stopniowo wracaj do normalnej prędkości
                        this.vx *= 0.95;
                        this.vy *= 0.95;

                        // Dodaj mały losowy ruch
                        this.vx += (Math.random() - 0.5) * 0.01;
                        this.vy += (Math.random() - 0.5) * 0.01;
                    }

                    this.x += this.vx;
                    this.y += this.vy;
                    this.phase += 0.005;

                    // Aktualizuj offsety dla płynnej animacji
                    for (let i = 0; i < this.noiseOffsets.length; i++) {
                        this.noiseOffsets[i] += 0.008;
                    }

                    // Odbicie od krawędzi
                    if (this.x < 0 || this.x > cols) this.vx *= -1;
                    if (this.y < 0 || this.y > rows) this.vy *= -1;

                    // Utrzymanie w granicach
                    this.x = Math.max(0, Math.min(cols, this.x));
                    this.y = Math.max(0, Math.min(rows, this.y));
                }

                getRadiusAtAngle(angle) {
                    // Interpolacja między punktami kontrolnymi dla płynnego kształtu
                    const segmentSize = (Math.PI * 2) / this.points;
                    const normalizedAngle = (angle + Math.PI) % (Math.PI * 2);
                    const segment = normalizedAngle / segmentSize;
                    const index1 = Math.floor(segment);
                    const index2 = (index1 + 1) % this.points;
                    const t = segment - index1;
                    
                    // Kubiczna interpolacja dla gładkości
                    const smoothT = t * t * (3 - 2 * t);
                    
                    const noise1 = Math.sin(this.noiseOffsets[index1]) * this.noiseStrengths[index1];
                    const noise2 = Math.sin(this.noiseOffsets[index2]) * this.noiseStrengths[index2];
                    const interpolatedNoise = noise1 + (noise2 - noise1) * smoothT;
                    
                    const pulseRadius = this.radius + Math.sin(this.phase) * 1.5;
                    return pulseRadius + interpolatedNoise * 4;
                }

                draw() {
                    for (let i = 0; i < cols; i++) {
                        for (let j = 0; j < rows; j++) {
                            const dx = i - this.x;
                            const dy = j - this.y;
                            const distance = Math.sqrt(dx * dx + dy * dy);
                            const angle = Math.atan2(dy, dx);
                            
                            const blobRadius = this.getRadiusAtAngle(angle);
                            
                            if (distance < blobRadius) {
                                // Gradient z miękkimi krawędziami
                                const normalizedDist = distance / blobRadius;
                                const alpha = Math.pow(1 - normalizedDist, 3) * 0.9;
                                
                                if (alpha > 0.05) {
                                    ctx.fillStyle = this.color + Math.floor(alpha * 255).toString(16).padStart(2, '0');
                                    ctx.fillRect(i * pixelSize, j * pixelSize, pixelSize, pixelSize);
                                }
                            }
                        }
                    }
                }
            }

            function init() {
                resizeCanvas();
                blobs = [];
                for (let i = 0; i < 5; i++) {
                    blobs.push(new Blob());
                }
            }

            function animate() {
                // Tło z gradientem
                const gradient = ctx.createLinearGradient(0, 0, width, height);
                gradient.addColorStop(0, '#0a0a0a');
                gradient.addColorStop(1, '#1a1a2e');
                ctx.fillStyle = gradient;
                ctx.fillRect(0, 0, width, height);

                // Grupuj plamy według koloru
                const blobsByColor = {};
                blobs.forEach(blob => {
                    if (!blobsByColor[blob.color]) {
                        blobsByColor[blob.color] = [];
                    }
                    blobsByColor[blob.color].push(blob);
                });

                // Rysuj każdą grupę kolorów
                let isFirstColor = true;
                Object.keys(blobsByColor).forEach(color => {
                    const colorBlobs = blobsByColor[color];
                    
                    if (isFirstColor) {
                        // Pierwszy kolor rysuj normalnie
                        ctx.globalCompositeOperation = 'source-over';
                        ctx.globalAlpha = 1.0;
                        isFirstColor = false;
                    } else {
                        // Kolejne kolory z przezroczystością aby się przenikały
                        ctx.globalCompositeOperation = 'source-over';
                        ctx.globalAlpha = 0.75;
                    }
                    
                    colorBlobs.forEach(blob => {
                        for (let i = 0; i < cols; i++) {
                            for (let j = 0; j < rows; j++) {
                                const dx = i - blob.x;
                                const dy = j - blob.y;
                                const distance = Math.sqrt(dx * dx + dy * dy);
                                const angle = Math.atan2(dy, dx);
                                const blobRadius = blob.getRadiusAtAngle(angle);
                                
                                if (distance < blobRadius) {
                                    ctx.fillStyle = blob.color;
                                    ctx.fillRect(i * pixelSize + 1, j * pixelSize + 1, pixelSize - 2, pixelSize - 2);
                                }
                            }
                        }
                    });
                });

                // Przywróć normalne ustawienia
                ctx.globalCompositeOperation = 'source-over';
                ctx.globalAlpha = 1.0;

                // Aktualizuj plamy
                blobs.forEach(blob => {
                    blob.update();
                });

                requestAnimationFrame(animate);
            }

            window.addEventListener('resize', resizeCanvas);
            
            window.addEventListener('mousemove', (e) => {
                mouseX = Math.floor(e.clientX / pixelSize);
                mouseY = Math.floor(e.clientY / pixelSize);
            });
            
            window.addEventListener('mousedown', (e) => {
                const clickX = Math.floor(e.clientX / pixelSize);
                const clickY = Math.floor(e.clientY / pixelSize);
                
                // Sprawdź która plama została kliknięta
                for (let i = blobs.length - 1; i >= 0; i--) {
                    const blob = blobs[i];
                    const dx = clickX - blob.x;
                    const dy = clickY - blob.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    const angle = Math.atan2(dy, dx);
                    const blobRadius = blob.getRadiusAtAngle(angle);
                    
                    if (distance < blobRadius) {
                        isDragging = true;
                        draggedBlob = blob;
                        break;
                    }
                }
            });
            
            window.addEventListener('mouseup', () => {
                isDragging = false;
                draggedBlob = null;
            });
            
            window.addEventListener('mouseleave', () => {
                mouseX = -1000;
                mouseY = -1000;
                isDragging = false;
                draggedBlob = null;
            });
            init();
            animate();
        </script>
    </body>
</html>
