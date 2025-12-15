/**
 * Single Trip Template JavaScript
 *
 * Inicialização do carrossel hero e outras interações
 *
 * @package WTE_Sliders
 */

(function() {
    'use strict';

    /**
     * Inicializar Hero Slider com 3 slides visíveis
     */
    function initHeroSlider() {
        const heroSliders = document.querySelectorAll('.wte-trip-hero-slider');

        if (!heroSliders.length) {
            return;
        }

        heroSliders.forEach(function(slider) {
            // Verificar se Swiper está disponível
            if (typeof Swiper === 'undefined') {
                console.warn('WTE Sliders: Swiper library not loaded');
                return;
            }

            new Swiper(slider, {
                slidesPerView: 1,
                spaceBetween: 10,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    // Mobile
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 15,
                    },
                    // Desktop
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    }
                }
            });
        });
    }

    /**
     * Inicializar smooth scroll para âncoras
     */
    function initSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');

        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                if (href === '#') {
                    return;
                }

                const target = document.querySelector(href);

                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Ajustar altura do sidebar sticky conforme scroll
     */
    function adjustStickyHeight() {
        const sidebar = document.querySelector('.wte-trip-sidebar');

        if (!sidebar) {
            return;
        }

        // Calcular altura máxima permitida
        function updateMaxHeight() {
            const windowHeight = window.innerHeight;
            const adminBarHeight = document.getElementById('wpadminbar')
                ? document.getElementById('wpadminbar').offsetHeight
                : 0;
            const topOffset = 100; // Valor do sticky top no CSS

            sidebar.style.maxHeight = (windowHeight - adminBarHeight - topOffset - 40) + 'px';
        }

        updateMaxHeight();
        window.addEventListener('resize', updateMaxHeight);
    }

    /**
     * Inicializar tudo quando DOM estiver pronto
     */
    function init() {
        initHeroSlider();
        initSmoothScroll();
        adjustStickyHeight();
    }

    // DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
