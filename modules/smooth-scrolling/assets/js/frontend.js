(function () {

    var lenis = null;
    var currentConfig = null;

    function getIsMobile(breakpoint) {
        return window.innerWidth < breakpoint;
    }

    function getReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function initLenis() {
        if (typeof Lenis === 'undefined') {
            requestAnimationFrame(initLenis);
            return;
        }

        var raw = window.WOMSmoothSettings || {};

        var config = {
            duration: raw.duration !== undefined ? parseFloat(raw.duration) : 1.5,
            wheelMultiplier: raw.wheelMultiplier !== undefined ? parseFloat(raw.wheelMultiplier) : 1.5,
            touchMultiplier: raw.touchMultiplier !== undefined ? parseFloat(raw.touchMultiplier) : 1,
            offset: raw.offset !== undefined ? parseInt(raw.offset, 10) : 80,
            mobileBreakpoint: raw.mobileBreakpoint !== undefined ? parseInt(raw.mobileBreakpoint, 10) : 992
        };

        currentConfig = config;
        applyLenisState(config);

        var motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        motionQuery.addEventListener('change', function () {
            applyLenisState(currentConfig);
        });

        var resizeHandler = null;
        window.addEventListener('resize', function () {
            if (resizeHandler) {
                clearTimeout(resizeHandler);
            }
            resizeHandler = setTimeout(function () {
                resizeHandler = null;
                if (currentConfig) {
                    applyLenisState(currentConfig);
                }
            }, 250);
        });
    }

    function applyLenisState(config) {
        var isMobile = getIsMobile(config.mobileBreakpoint);
        var reducedMotion = getReducedMotion();
        var shouldBeActive = !isMobile && !reducedMotion;

        if (shouldBeActive && !lenis) {
            createLenis(config);
        } else if (!shouldBeActive && lenis) {
            destroyLenis();
        }
    }

    function createLenis(config) {
        if (lenis) {
            return;
        }

        lenis = new Lenis({
            autoRaf: true,
            duration: config.duration,
            smoothWheel: true,
            wheelMultiplier: config.wheelMultiplier,
            touchMultiplier: config.touchMultiplier,
            infinite: false,
            anchors: {
                offset: -config.offset
            },
            respectReducedMotion: true,
            allowNestedScroll: true
        });

        window.lenis = lenis;
    }

    function destroyLenis() {
        if (lenis) {
            lenis.destroy();
            lenis = null;
            window.lenis = null;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLenis);
    } else {
        initLenis();
    }

})();
