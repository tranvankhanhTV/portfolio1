// ===== Advanced Starfield Animation =====
const canvas = document.getElementById('starfield');
const ctx = canvas.getContext('2d');

let width, height, stars = [], numStars = 300;
let mouseX = 0, mouseY = 0;

function resize() {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
}

class Star {
    constructor() {
        this.reset();
    }

    reset() {
        this.x = Math.random() * width;
        this.y = Math.random() * height;
        this.z = Math.random() * 1000;
        this.size = Math.random() * 2.5 + 0.5;
        this.speed = Math.random() * 3 + 1;
        this.opacity = Math.random() * 0.6 + 0.4;
        this.color = this.getRandomColor();
        this.twinkleSpeed = Math.random() * 0.02 + 0.01;
        this.twinklePhase = Math.random() * Math.PI * 2;
    }

    getRandomColor() {
        const colors = [
            'rgba(255, 255, 255,',     // White
            'rgba(0, 212, 255,',       // Cyan
            'rgba(59, 130, 246,',      // Blue
            'rgba(255, 215, 0,',       // Gold
            'rgba(255, 0, 110,',       // Pink
            'rgba(168, 85, 247,'       // Purple
        ];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    update() {
        this.z -= this.speed;
        this.twinklePhase += this.twinkleSpeed;
        
        // Parallax effect based on mouse
        const dx = (mouseX - width / 2) * 0.0001 * (1000 - this.z);
        const dy = (mouseY - height / 2) * 0.0001 * (1000 - this.z);
        
        if (this.z <= 0) {
            this.reset();
            this.z = 1000;
        }
        
        this.x += dx;
        this.y += dy;
    }

    draw() {
        const perspective = 1000 / this.z;
        const x = (this.x - width / 2) * perspective + width / 2;
        const y = (this.y - height / 2) * perspective + height / 2;
        const radius = this.size * perspective * 0.15;
        
        // Twinkle effect
        const twinkle = Math.sin(this.twinklePhase) * 0.3 + 0.7;
        const currentOpacity = this.opacity * twinkle;

        if (x > 0 && x < width && y > 0 && y < height) {
            ctx.beginPath();
            ctx.arc(x, y, radius, 0, Math.PI * 2);
            ctx.fillStyle = this.color + currentOpacity + ')';
            ctx.fill();

            // Enhanced glow for larger stars
            if (radius > 1.2) {
                const gradient = ctx.createRadialGradient(x, y, 0, x, y, radius * 3);
                gradient.addColorStop(0, this.color + (currentOpacity * 0.6) + ')');
                gradient.addColorStop(1, 'transparent');
                
                ctx.beginPath();
                ctx.arc(x, y, radius * 3, 0, Math.PI * 2);
                ctx.fillStyle = gradient;
                ctx.fill();
            }
        }
    }
}

function initStars() {
    stars = [];
    for (let i = 0; i < numStars; i++) {
        stars.push(new Star());
    }
}

function animateStars() {
    // Trail effect for smooth animation
    ctx.fillStyle = 'rgba(3, 7, 20, 0.15)';
    ctx.fillRect(0, 0, width, height);

    stars.forEach(star => {
        star.update();
        star.draw();
    });

    requestAnimationFrame(animateStars);
}

window.addEventListener('resize', () => {
    resize();
    initStars();
});

document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
});

resize();
initStars();
animateStars();

// ===== Three.js 3D Scene =====
let scene, camera, renderer, geometry, material, mesh;
let threeContainer = document.getElementById('three-container');

// Guard against a missing/blocked Three.js CDN so the rest of the page still runs
if (threeContainer && typeof THREE !== 'undefined') {
    try {
        init3D();
        animate3D();
    } catch (error) {
        console.warn('3D scene disabled:', error);
    }
} else if (threeContainer) {
    console.warn('Three.js not loaded - 3D scene disabled');
}

function init3D() {
    scene = new THREE.Scene();
    
    camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.z = 5;
    
    renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    threeContainer.appendChild(renderer.domElement);
    
    // Create geometric shape
    geometry = new THREE.IcosahedronGeometry(2, 1);
    material = new THREE.MeshBasicMaterial({ 
        color: 0x00d4ff, 
        wireframe: true,
        transparent: true,
        opacity: 0.3
    });
    mesh = new THREE.Mesh(geometry, material);
    scene.add(mesh);
    
    // Add particles
    const particlesGeometry = new THREE.BufferGeometry();
    const particlesCount = 500;
    const posArray = new Float32Array(particlesCount * 3);
    
    for (let i = 0; i < particlesCount * 3; i++) {
        posArray[i] = (Math.random() - 0.5) * 20;
    }
    
    particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
    const particlesMaterial = new THREE.PointsMaterial({
        size: 0.02,
        color: 0x00d4ff,
        transparent: true,
        opacity: 0.6
    });
    
    const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
    scene.add(particlesMesh);
    
    window.addEventListener('resize', onWindowResize3D);
}

function onWindowResize3D() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
}

function animate3D() {
    requestAnimationFrame(animate3D);
    
    if (mesh) {
        mesh.rotation.x += 0.002;
        mesh.rotation.y += 0.003;
    }
    
    renderer.render(scene, camera);
}

// ===== Particle System =====
class Particle {
    constructor() {
        this.reset();
    }
    
    reset() {
        this.x = Math.random() * width;
        this.y = Math.random() * height;
        this.size = Math.random() * 2 + 1;
        this.speedX = (Math.random() - 0.5) * 0.5;
        this.speedY = (Math.random() - 0.5) * 0.5;
        this.opacity = Math.random() * 0.5 + 0.2;
        this.life = 1;
        this.decay = Math.random() * 0.005 + 0.002;
    }
    
    update() {
        this.x += this.speedX;
        this.y += this.speedY;
        this.life -= this.decay;
        
        if (this.life <= 0 || this.x < 0 || this.x > width || this.y < 0 || this.y > height) {
            this.reset();
        }
    }
    
    draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(0, 212, 255, ${this.opacity * this.life})`;
        ctx.fill();
    }
}

const particles = Array.from({ length: 50 }, () => new Particle());

function animateParticles() {
    particles.forEach(particle => {
        particle.update();
        particle.draw();
    });
}

// Integrate particles into main animation loop
const originalAnimateStars = animateStars;
animateStars = function() {
    ctx.fillStyle = 'rgba(3, 7, 20, 0.15)';
    ctx.fillRect(0, 0, width, height);
    
    stars.forEach(star => {
        star.update();
        star.draw();
    });
    
    animateParticles();
    
    requestAnimationFrame(animateStars);
};

// ===== Typing Animation =====
const typingText = document.querySelector('.typing-text');
const phrases = [
    'Reliable Backend APIs',
    'Full-Stack Products',
    'Data-Driven Systems',
    'Responsive Interfaces'
];

let phraseIndex = 0;
let charIndex = 0;
let isDeleting = false;
let typingSpeed = 150;

function type() {
    const currentPhrase = phrases[phraseIndex];
    
    if (isDeleting) {
        typingText.textContent = currentPhrase.substring(0, charIndex - 1);
        charIndex--;
        typingSpeed = 100;
    } else {
        typingText.textContent = currentPhrase.substring(0, charIndex + 1);
        charIndex++;
        typingSpeed = 150;
    }

    if (!isDeleting && charIndex === currentPhrase.length) {
        typingSpeed = 2000;
        isDeleting = true;
    } else if (isDeleting && charIndex === 0) {
        isDeleting = false;
        phraseIndex = (phraseIndex + 1) % phrases.length;
        typingSpeed = 500;
    }

    setTimeout(type, typingSpeed);
}

setTimeout(type, 1500);

// ===== Smooth Scrolling =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const topOffset = window.matchMedia('(max-width: 900px)').matches ? 72 : 32;
            const offsetTop = window.pageYOffset + target.getBoundingClientRect().top - topOffset;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ===== Navbar Scroll Effect =====
let lastScroll = 0;
const navbar = document.querySelector('.navbar');
const sidebarToggle = document.getElementById('sidebarToggle');
const mobileOverlay = document.getElementById('mobileOverlay');

function setSidebarOpen(isOpen) {
    if (!navbar || !sidebarToggle) return;

    navbar.classList.toggle('active', isOpen);
    sidebarToggle.classList.toggle('active', isOpen);
    sidebarToggle.setAttribute('aria-expanded', String(isOpen));
    sidebarToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');

    if (mobileOverlay) {
        mobileOverlay.classList.toggle('active', isOpen);
    }
}

if (sidebarToggle && navbar) {
    sidebarToggle.addEventListener('click', () => {
        setSidebarOpen(!navbar.classList.contains('active'));
    });
}

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => setSidebarOpen(false));
});

if (mobileOverlay) {
    mobileOverlay.addEventListener('click', () => setSidebarOpen(false));
}

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    if (navbar) {
        // Visual styling lives in CSS (.navbar / .navbar.scrolled) so the
        // refreshed theme stays in control of the design.
        navbar.classList.toggle('scrolled', currentScroll > 100);
    }

    lastScroll = currentScroll;
});

// ===== Active Navigation Highlight =====
const sections = document.querySelectorAll('section[id]');

window.addEventListener('scroll', () => {
    let current = '';
    const scrollOffset = window.matchMedia('(max-width: 900px)').matches ? 84 : 120;
    const scrollPosition = window.pageYOffset + scrollOffset;

    sections.forEach(section => {
        const sectionTop = section.offsetTop;

        if (scrollPosition >= sectionTop) {
            current = section.getAttribute('id');
        }
    });

    // Toggle the .active class so CSS owns the styling (and it stays testable)
    document.querySelectorAll('.nav-link').forEach(link => {
        const isCurrent = link.getAttribute('href') === `#${current}`;
        link.classList.toggle('active', isCurrent);

        if (isCurrent) {
            link.setAttribute('aria-current', 'location');
        } else {
            link.removeAttribute('aria-current');
        }
    });
});

// ===== Intersection Observer for Animations =====
const observerOptions = {
    threshold: 0.15,
    rootMargin: '0px 0px -80px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            entry.target.style.transition = 'opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
        }
    });
}, observerOptions);

// Observe elements
document.querySelectorAll('.skill-card, .project-card, .timeline-item, .about-card, .stat-item, .contact-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(40px)';
    el.style.transition = 'opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
    observer.observe(el);
});

// ===== Parallax Effect for Profile Card =====
const profileCard = document.querySelector('.profile-card');

if (profileCard) {
    document.addEventListener('mousemove', (e) => {
        const { clientX, clientY } = e;
        const { innerWidth, innerHeight } = window;
        
        const xPos = (clientX / innerWidth - 0.5) * 20;
        const yPos = (clientY / innerHeight - 0.5) * 20;
        
        profileCard.style.transform = `perspective(1000px) rotateY(${xPos}deg) rotateX(${-yPos}deg)`;
    });
}

// ===== Scroll Indicator =====
const scrollIndicator = document.querySelector('.scroll-indicator');
if (scrollIndicator) {
    scrollIndicator.addEventListener('click', () => {
        const aboutSection = document.querySelector('#about');
        if (aboutSection) {
            aboutSection.scrollIntoView({ behavior: 'smooth' });
        }
    });
}

// ===== Counter Animation for Hero Stats =====
function animateCounter(element, target, suffix = '', duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = Math.floor(start) + suffix;
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target + suffix;
        }
    }
    
    updateCounter();
}

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const stats = entry.target.querySelectorAll('.hero-stat h3');
            stats.forEach(stat => {
                const text = stat.textContent;
                const match = text.match(/^(\d+(?:\.\d+)?)(.*)$/);
                if (match) {
                    animateCounter(stat, Number(match[1]), match[2]);
                }
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const heroStats = document.querySelector('.hero-stats');
if (heroStats) {
    statsObserver.observe(heroStats);
}

// ===== Shooting Star Effect =====
function createShootingStar() {
    const shootingStar = document.createElement('div');
    shootingStar.style.cssText = `
        position: fixed;
        top: ${Math.random() * 50}%;
        left: ${Math.random() * 100}%;
        width: 120px;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(0, 212, 255, 0.9), transparent);
        box-shadow: 
            0 0 15px rgba(0, 212, 255, 0.7),
            0 0 30px rgba(0, 212, 255, 0.5);
        animation: shoot 2.5s linear forwards;
        z-index: 1;
        pointer-events: none;
        border-radius: 2px;
    `;
    document.body.appendChild(shootingStar);

    setTimeout(() => {
        shootingStar.remove();
    }, 2500);
}

// Add shooting star animation
const style = document.createElement('style');
style.textContent = `
    @keyframes shoot {
        0% {
            transform: translateX(0) translateY(0) rotate(-35deg);
            opacity: 1;
        }
        100% {
            transform: translateX(600px) translateY(400px) rotate(-35deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Create shooting stars periodically
setInterval(() => {
    if (Math.random() > 0.6) {
        createShootingStar();
    }
}, 4000);

// Create initial shooting stars
setTimeout(createShootingStar, 1500);
setTimeout(createShootingStar, 3000);
setTimeout(createShootingStar, 4500);

// ===== Page Load Animation =====
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.classList.add('hidden');
        setTimeout(() => {
            loader.style.display = 'none';
        }, 1000);
    }

    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 1.2s ease';
        document.body.style.opacity = '1';
    }, 100);

    // Trigger hero animations
    document.querySelectorAll('.hero-content > *').forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        setTimeout(() => {
            el.style.transition = 'opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 200 + index * 100);
    });
});

// ===== Smooth Reveal on Scroll =====
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('.hero');
    
    if (hero) {
        hero.style.transform = `translateY(${scrolled * 0.4}px)`;
    }
});

// ===== Advanced Glowing Cursor Effect =====
const cursor = document.createElement('div');
const cursorDot = document.createElement('div');
const cursorRing = document.createElement('div');

cursor.style.cssText = `
    position: fixed;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0, 212, 255, 0.2), transparent);
    pointer-events: none;
    z-index: 9999;
    transition: transform 0.15s ease, width 0.3s ease, height 0.3s ease;
    mix-blend-mode: screen;
`;

cursorDot.style.cssText = `
    position: fixed;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--nebula-cyan);
    pointer-events: none;
    z-index: 9999;
    box-shadow: 0 0 20px var(--nebula-cyan), 0 0 40px var(--nebula-blue);
    transition: transform 0.1s ease;
`;

cursorRing.style.cssText = `
    position: fixed;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid rgba(0, 212, 255, 0.4);
    pointer-events: none;
    z-index: 9998;
    transition: transform 0.3s ease, width 0.3s ease, height 0.3s ease, border-color 0.3s ease;
`;

document.body.appendChild(cursor);
document.body.appendChild(cursorDot);
document.body.appendChild(cursorRing);

let cursorX = 0, cursorY = 0;
let ringX = 0, ringY = 0;

document.addEventListener('mousemove', (e) => {
    cursorX = e.clientX;
    cursorY = e.clientY;
    
    cursorDot.style.left = cursorX - 4 + 'px';
    cursorDot.style.top = cursorY - 4 + 'px';
    
    // Smooth follow for ring
    ringX += (cursorX - ringX) * 0.15;
    ringY += (cursorY - ringY) * 0.15;
    
    cursorRing.style.left = ringX - 17.5 + 'px';
    cursorRing.style.top = ringY - 17.5 + 'px';
});

// Cursor hover effects on interactive elements
document.querySelectorAll('a, button, .btn, .skill-card, .project-card, .contact-item, input, textarea').forEach(el => {
    el.addEventListener('mouseenter', () => {
        cursor.style.transform = 'scale(1.5)';
        cursorRing.style.transform = 'scale(1.3)';
        cursorRing.style.borderColor = 'rgba(0, 212, 255, 0.8)';
        cursorRing.style.width = '45px';
        cursorRing.style.height = '45px';
    });
    
    el.addEventListener('mouseleave', () => {
        cursor.style.transform = 'scale(1)';
        cursorRing.style.transform = 'scale(1)';
        cursorRing.style.borderColor = 'rgba(0, 212, 255, 0.4)';
        cursorRing.style.width = '35px';
        cursorRing.style.height = '35px';
    });
});

// Hide cursor on mobile
if ('ontouchstart' in window) {
    cursor.style.display = 'none';
    cursorDot.style.display = 'none';
    cursorRing.style.display = 'none';
}

// ===== Magnetic Button Effect =====
document.querySelectorAll('.magnetic-btn').forEach(btn => {
    if (btn.closest('.hero-buttons')) return;

    btn.addEventListener('mousemove', (e) => {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        
        btn.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
    });
    
    btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'translate(0, 0)';
    });
});

// ===== Text Reveal Animation =====
const revealElements = document.querySelectorAll('.hero-title, .hero-subtitle, .hero-description');

revealElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
});

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.transition = 'opacity 1s cubic-bezier(0.4, 0, 0.2, 1), transform 1s cubic-bezier(0.4, 0, 0.2, 1)';
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

revealElements.forEach(el => revealObserver.observe(el));

// ===== Performance Optimization =====
let ticking = false;

window.addEventListener('scroll', () => {
    if (!ticking) {
        window.requestAnimationFrame(() => {
            // All scroll-based animations are handled here
            ticking = false;
        });
        ticking = true;
    }
});

// ===== Console Welcome =====
console.log('%c🚀 Portfolio đã sẵn sàng!', 'color: #00d4ff; font-size: 20px; font-weight: bold; text-shadow: 0 0 10px rgba(0, 212, 255, 0.8);');
console.log('%c✨ Starfield: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c💫 Typing Animation: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c🎨 Cosmic Theme: Loaded', 'color: #3b82f6; font-size: 14px;');
console.log('%c🌟 Particles: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c🖱️ Advanced Cursor: Active', 'color: #3b82f6; font-size: 14px;');
// ===== Scroll Progress Bar =====
const scrollProgress = document.getElementById('scrollProgress');
if (scrollProgress) {
    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;
        scrollProgress.style.width = scrollPercent + '%';
    });
}

// ===== Back to Top Button =====
const backToTop = document.getElementById('backToTop');
if (backToTop) {
    window.addEventListener('scroll', () => {
        const homeSection = document.getElementById('home');
        const homeHasCleared = !homeSection ||
            homeSection.getBoundingClientRect().bottom <= window.innerHeight * 0.4;
        const contentSections = [
            document.querySelector('.contact'),
            document.querySelector('.footer')
        ];
        const clearOfContent = contentSections.every(section => {
            if (!section) return true;
            const bounds = section.getBoundingClientRect();
            return bounds.bottom <= 0 || bounds.top >= window.innerHeight;
        });

        if (window.pageYOffset > 500 && homeHasCleared && clearOfContent) {
            backToTop.classList.add('active');
        } else {
            backToTop.classList.remove('active');
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        // Hide immediately: we are already on our way to the top, so the button
        // should not linger until the next scroll event arrives.
        backToTop.classList.remove('active');
    });
}

// ===== Custom Cursor Trail =====
const cursorTrail = document.getElementById('cursorTrail');
let trailCursorX = 0, trailCursorY = 0;
let cursorTrailX = 0, cursorTrailY = 0;

if (cursorTrail && !('ontouchstart' in window)) {
    document.addEventListener('mousemove', (e) => {
        trailCursorX = e.clientX;
        trailCursorY = e.clientY;
    });

    function animateCursorTrail() {
        cursorTrailX += (trailCursorX - cursorTrailX) * 0.15;
        cursorTrailY += (trailCursorY - cursorTrailY) * 0.15;

        cursorTrail.style.left = cursorTrailX - 10 + 'px';
        cursorTrail.style.top = cursorTrailY - 10 + 'px';

        requestAnimationFrame(animateCursorTrail);
    }
    animateCursorTrail();
}

// ===== Section Reveal Animations (.reveal / .reveal-left / .reveal-right / .reveal-stagger) =====
const sectionRevealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-stagger');

if (sectionRevealElements.length) {
    const sectionRevealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                sectionRevealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

    sectionRevealElements.forEach(el => sectionRevealObserver.observe(el));
}

// ===== Book-like section entrance on scroll =====
const pageTurnSections = document.querySelectorAll('.section:not(.hero)');

if (pageTurnSections.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const pageTurnObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('page-turn-in');
                pageTurnObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -8% 0px' });

    pageTurnSections.forEach(section => {
        section.classList.add('page-turn-ready');
        pageTurnObserver.observe(section);
    });
}

// ===== Accessible project category filters =====
const projectGrid = document.getElementById('projectGrid');
const projectFilterStatus = document.getElementById('projectFilterStatus');
const projectFilters = document.querySelectorAll('.project-filter');

if (projectGrid && projectFilterStatus && projectFilters.length) {
    const projectCards = Array.from(projectGrid.querySelectorAll('.project-card[data-category]'));
    const categoryLabels = {
        all: 'project',
        fullstack: 'full-stack project',
        backend: 'backend project',
        frontend: 'frontend project'
    };

    projectFilters.forEach(button => {
        const category = button.dataset.filter;
        const count = category === 'all'
            ? projectCards.length
            : projectCards.filter(card => card.dataset.category.split(/\s+/).includes(category)).length;
        const countBadge = button.querySelector('span');

        if (countBadge) {
            countBadge.textContent = String(count).padStart(2, '0');
        }

        button.addEventListener('click', () => {
            projectFilters.forEach(filter => {
                const isActive = filter === button;
                filter.classList.toggle('is-active', isActive);
                filter.setAttribute('aria-pressed', String(isActive));
            });

            const selectedCategory = button.dataset.filter;
            let visibleCount = 0;

            projectCards.forEach(card => {
                const matches = selectedCategory === 'all'
                    || card.dataset.category.split(/\s+/).includes(selectedCategory);
                card.hidden = !matches;
                if (matches) visibleCount++;
            });

            const label = categoryLabels[selectedCategory] || 'project';
            projectFilterStatus.textContent = `Showing ${visibleCount} ${label}${visibleCount === 1 ? '' : 's'}`;
        });
    });
}

// ===== Enhanced Contact Form =====
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    // Create success message element
    const successMessage = document.createElement('div');
    successMessage.className = 'form-success';
    successMessage.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <h3 style="color: var(--nebula-cyan); margin-bottom: 1rem;">Message Sent!</h3>
        <p style="color: var(--text-secondary);">Thanks for reaching out. I'll get back to you as soon as possible!</p>
    `;
    document.body.appendChild(successMessage);

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const inputs = this.querySelectorAll('input, textarea');
        let isValid = true;
        
        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#ff006e';
                input.style.boxShadow = '0 0 20px rgba(255, 0, 110, 0.5)';
                
                setTimeout(() => {
                    input.style.borderColor = '';
                    input.style.boxShadow = '';
                }, 2000);
            }
        });

        if (isValid) {
            // Show success message
            successMessage.classList.add('show');
            
            // Reset form
            this.reset();
            
            // Hide success message after 3 seconds
            setTimeout(() => {
                successMessage.classList.remove('show');
            }, 3000);
        } else {
            // Shake animation for invalid form
            this.style.animation = 'shake 0.5s ease';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        }
    });

    // Add shake animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }
    `;
    document.head.appendChild(style);
}

// ===== Image Loading Animation =====
document.querySelectorAll('img').forEach(img => {
    if (img.complete) {
        img.classList.add('loaded');
    } else {
        img.addEventListener('load', () => {
            img.classList.add('loaded');
        });
    }
});

// ===== Keyboard Navigation Enhancement =====
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navbar && sidebarToggle && navbar.classList.contains('active')) {
        setSidebarOpen(false);
        sidebarToggle.focus();
    }

    // Arrow keys for section navigation
    if (e.key === 'ArrowDown' && e.ctrlKey) {
        e.preventDefault();
        const currentSection = getCurrentSection();
        const nextSection = currentSection.nextElementSibling;
        if (nextSection && nextSection.tagName === 'SECTION') {
            nextSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    if (e.key === 'ArrowUp' && e.ctrlKey) {
        e.preventDefault();
        const currentSection = getCurrentSection();
        const prevSection = currentSection.previousElementSibling;
        if (prevSection && prevSection.tagName === 'SECTION') {
            prevSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
});

function getCurrentSection() {
    let current = document.querySelector('.hero');
    const scrollPosition = window.pageYOffset + 100;
    
    sections.forEach(section => {
        if (scrollPosition >= section.offsetTop) {
            current = section;
        }
    });
    
    return current;
}

// ===== Performance: Debounce Scroll Events =====
function debounce(func, wait = 20, immediate = true) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// ===== Console Welcome with Stats =====
console.log('%c🚀 Portfolio đã sẵn sàng!', 'color: #00d4ff; font-size: 20px; font-weight: bold; text-shadow: 0 0 10px rgba(0, 212, 255, 0.8);');
console.log('%c✨ Starfield: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c💫 Typing Animation: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c🎨 Cosmic Theme: Loaded', 'color: #3b82f6; font-size: 14px;');
console.log('%c🌟 Particles: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c🖱️ Advanced Cursor: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c🎲 3D Scene: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c📊 Scroll Progress: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c🎯 Section Reveal: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c💫 Card Tilt: Active', 'color: #3b82f6; font-size: 14px;');
console.log('%c🎪 New Interface: Active', 'color: #00d4ff; font-size: 14px; font-weight: bold;');

// ===== Subtle card tilt on precise, motion-enabled pointers =====
const canTiltCards = window.matchMedia('(hover: hover) and (pointer: fine)').matches
    && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (canTiltCards) {
    document.querySelectorAll('.tilt-card').forEach(card => {
        card.addEventListener('pointermove', event => {
            const rect = card.getBoundingClientRect();
            if (!rect.width || !rect.height) return;

            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;
            const rotateX = Math.max(-3, Math.min(3, -y * 6));
            const rotateY = Math.max(-3, Math.min(3, x * 6));

            card.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-3px)`;
        });

        card.addEventListener('pointerleave', () => {
            card.style.removeProperty('transform');
        });
    });
}

// ===== Floating Particles in Hero =====
const hero = document.querySelector('.hero');
if (hero) {
    for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 8 + 's';
        particle.style.animationDuration = (Math.random() * 4 + 6) + 's';
        hero.appendChild(particle);
    }
}
