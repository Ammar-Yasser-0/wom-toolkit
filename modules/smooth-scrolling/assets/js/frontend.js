(function () {

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

        var isMobile = window.innerWidth < config.mobileBreakpoint;
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        var lenis = null;

        if (!isMobile && !reducedMotion) {
            lenis = new Lenis({
                autoRaf: true,
                duration: config.duration,
                smoothWheel: true,
                wheelMultiplier: config.wheelMultiplier,
                touchMultiplier: config.touchMultiplier,
                infinite: false
            });

            window.lenis = lenis;
        }

        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var href = this.getAttribute('href');
                if (!href || href === '#') return;

                var target = document.querySelector(href);
                if (!target) return;

                e.preventDefault();

                if (lenis) {
                    lenis.scrollTo(target, {
                        offset: -config.offset,
                        duration: config.duration
                    });
                } else {
                    var top = target.getBoundingClientRect().top + window.pageYOffset - config.offset;

                    window.scrollTo({
                        top: top,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    window.addEventListener('load', function () {
        initLenis();
    });

})();