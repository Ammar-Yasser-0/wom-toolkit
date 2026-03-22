window.addEventListener('load', function () {
    const isDesktopPointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (!isDesktopPointer) return;

    if (window.__womCursorInitialized) return;
    window.__womCursorInitialized = true;

    const settings = window.WOMCustomCursorSettings || {};

    document.documentElement.style.setProperty('--wom-cursor-dot-color', settings.dotColor || '#000000');
    document.documentElement.style.setProperty('--wom-cursor-ring-color', settings.ringColor || '#000000');
    document.documentElement.style.setProperty('--wom-cursor-hover-bg', settings.hoverBg || 'rgba(0, 0, 0, 0.06)');
    document.documentElement.style.setProperty('--wom-cursor-click-bg', settings.clickBg || 'rgba(0, 0, 0, 0.12)');
    document.documentElement.style.setProperty('--wom-cursor-dot-size', (settings.dotSize || 6) + 'px');
    document.documentElement.style.setProperty('--wom-cursor-ring-size', (settings.ringSize || 26) + 'px');
    document.documentElement.style.setProperty('--wom-cursor-hover-size', (settings.hoverSize || 42) + 'px');
    document.documentElement.style.setProperty('--wom-cursor-click-size', (settings.clickSize || 22) + 'px');
    document.documentElement.style.setProperty('--wom-cursor-ring-border-width', (settings.ringBorderWidth || 1.5) + 'px');
    document.documentElement.style.setProperty('--wom-cursor-pulse-size', (settings.pulseSize || 18) + 'px');
    document.documentElement.style.setProperty('--wom-cursor-pulse-end-size', (settings.pulseEndSize || 52) + 'px');
    document.documentElement.style.setProperty('--wom-cursor-pulse-border-width', (settings.pulseBorderWidth || 1.5) + 'px');
    document.documentElement.style.setProperty('--wom-cursor-z-index', settings.zIndex || 999999);

    document.documentElement.classList.add('wom-custom-cursor');

    const dot = document.createElement('div');
    dot.className = 'wom-cursor-dot is-hidden';

    const ring = document.createElement('div');
    ring.className = 'wom-cursor-ring is-hidden';

    const clickPulse = document.createElement('div');
    clickPulse.className = 'wom-cursor-click';

    document.body.appendChild(dot);
    document.body.appendChild(ring);
    document.body.appendChild(clickPulse);

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let dotX = mouseX;
    let dotY = mouseY;
    let ringX = mouseX;
    let ringY = mouseY;

    const dotSpeed = parseFloat(settings.dotSpeed || 0.45);
    const ringSpeed = parseFloat(settings.ringSpeed || 0.18);

    const hoverSelectors = settings.hoverSelectors || [
        'a',
        'button',
        '[role="button"]',
        'input[type="submit"]',
        'input[type="button"]',
        '.elementor-button',
        '.elementor-icon',
        '.elementor-icon-box',
        '.elementor-image-box',
        '.clickable'
    ].join(',');

    document.addEventListener('mousemove', function (e) {
        mouseX = e.clientX;
        mouseY = e.clientY;

        dot.classList.remove('is-hidden');
        ring.classList.remove('is-hidden');

        const target = e.target.closest(hoverSelectors);
        if (target) {
            ring.classList.add('is-hover');
        } else {
            ring.classList.remove('is-hover');
        }
    }, { passive: true });

    document.addEventListener('mousedown', function (e) {
        ring.classList.add('is-clicking');

        clickPulse.style.left = e.clientX + 'px';
        clickPulse.style.top = e.clientY + 'px';

        clickPulse.classList.remove('is-animating');
        void clickPulse.offsetWidth;
        clickPulse.classList.add('is-animating');
    });

    document.addEventListener('mouseup', function () {
        ring.classList.remove('is-clicking');
    });

    document.addEventListener('mouseleave', function () {
        dot.classList.add('is-hidden');
        ring.classList.add('is-hidden');
    });

    document.addEventListener('mouseenter', function () {
        dot.classList.remove('is-hidden');
        ring.classList.remove('is-hidden');
    });

    window.addEventListener('blur', function () {
        dot.classList.add('is-hidden');
        ring.classList.add('is-hidden');
    });

    function animate() {
        dotX += (mouseX - dotX) * dotSpeed;
        dotY += (mouseY - dotY) * dotSpeed;

        ringX += (mouseX - ringX) * ringSpeed;
        ringY += (mouseY - ringY) * ringSpeed;

        dot.style.transform = 'translate(' + dotX + 'px, ' + dotY + 'px) translate(-50%, -50%)';
        ring.style.transform = 'translate(' + ringX + 'px, ' + ringY + 'px) translate(-50%, -50%)';

        requestAnimationFrame(animate);
    }

    requestAnimationFrame(animate);
});