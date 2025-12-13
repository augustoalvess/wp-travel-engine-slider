/**
 * Vanilla JavaScript Slider para WP Travel Engine
 *
 * @package WTE_Sliders
 */

class WTESlider {
    constructor(element) {
        // Prevenir dupla inicialização
        if (element.hasAttribute('data-wte-initialized')) {
            return;
        }
        element.setAttribute('data-wte-initialized', 'true');

        this.slider = element;
        this.track = element.querySelector('.wte-slider-track');
        this.slides = element.querySelectorAll('.wte-slider-slide');
        this.prevButton = element.querySelector('.wte-slider-prev');
        this.nextButton = element.querySelector('.wte-slider-next');
        this.dotsContainer = element.querySelector('.wte-slider-dots');

        // Configurações
        this.currentIndex = 0;
        this.autoplay = element.dataset.autoplay === 'true';
        this.speed = parseInt(element.dataset.speed) || 5000;
        this.arrows = element.dataset.arrows === 'true';
        this.autoplayTimer = null;

        // Validar se há slides
        if (this.slides.length === 0) {
            return;
        }

        this.init();
    }

    init() {
        // Criar dots
        this.createDots();

        // Adicionar event listeners
        if (this.prevButton && this.nextButton) {
            this.prevButton.addEventListener('click', () => this.prev());
            this.nextButton.addEventListener('click', () => this.next());
        }

        // Touch/swipe support
        this.addTouchSupport();

        // Keyboard navigation
        this.addKeyboardSupport();

        // Iniciar autoplay se configurado
        if (this.autoplay) {
            this.startAutoplay();
        }

        // Pausar autoplay ao hover
        this.slider.addEventListener('mouseenter', () => this.pauseAutoplay());
        this.slider.addEventListener('mouseleave', () => {
            if (this.autoplay) {
                this.startAutoplay();
            }
        });

        // Atualizar navegação inicial
        this.updateNavigation();
    }

    createDots() {
        if (!this.dotsContainer || this.slides.length <= 1) {
            return;
        }

        for (let i = 0; i < this.slides.length; i++) {
            const dot = document.createElement('button');
            dot.classList.add('wte-slider-dot');
            dot.setAttribute('aria-label', `Ir para slide ${i + 1}`);

            if (i === 0) {
                dot.classList.add('active');
            }

            dot.addEventListener('click', () => this.goTo(i));
            this.dotsContainer.appendChild(dot);
        }
    }

    updateDots() {
        if (!this.dotsContainer) {
            return;
        }

        const dots = this.dotsContainer.querySelectorAll('.wte-slider-dot');
        dots.forEach((dot, index) => {
            if (index === this.currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    updateNavigation() {
        // Atualizar estado dos botões
        if (this.prevButton) {
            this.prevButton.disabled = this.currentIndex === 0;
        }
        if (this.nextButton) {
            this.nextButton.disabled = this.currentIndex === this.slides.length - 1;
        }

        // Atualizar dots
        this.updateDots();
    }

    goTo(index) {
        if (index < 0 || index >= this.slides.length) {
            return;
        }

        this.currentIndex = index;
        const offset = -index * 100;
        this.track.style.transform = `translateX(${offset}%)`;
        this.updateNavigation();

        // Resetar autoplay
        if (this.autoplay) {
            this.pauseAutoplay();
            this.startAutoplay();
        }
    }

    next() {
        if (this.currentIndex < this.slides.length - 1) {
            this.goTo(this.currentIndex + 1);
        } else if (this.autoplay) {
            // Loop no autoplay
            this.goTo(0);
        }
    }

    prev() {
        if (this.currentIndex > 0) {
            this.goTo(this.currentIndex - 1);
        }
    }

    startAutoplay() {
        this.pauseAutoplay();
        this.autoplayTimer = setInterval(() => {
            this.next();
        }, this.speed);
    }

    pauseAutoplay() {
        if (this.autoplayTimer) {
            clearInterval(this.autoplayTimer);
            this.autoplayTimer = null;
        }
    }

    addTouchSupport() {
        let startX = 0;
        let endX = 0;
        const threshold = 50; // Mínimo de pixels para considerar um swipe

        this.slider.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, { passive: true });

        this.slider.addEventListener('touchmove', (e) => {
            endX = e.touches[0].clientX;
        }, { passive: true });

        this.slider.addEventListener('touchend', () => {
            const diff = startX - endX;

            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    // Swipe left - next
                    this.next();
                } else {
                    // Swipe right - prev
                    this.prev();
                }
            }
        });
    }

    addKeyboardSupport() {
        document.addEventListener('keydown', (e) => {
            // Verificar se o slider está visível
            const rect = this.slider.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight && rect.bottom > 0;

            if (!isVisible) {
                return;
            }

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this.prev();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                this.next();
            }
        });
    }
}

// API Global para inicialização e re-inicialização manual
window.WTESliders = {
    /**
     * Inicializar sliders
     *
     * @param {string} selector - Seletor CSS para os sliders
     */
    init: function(selector) {
        const sliders = document.querySelectorAll(selector || '.wte-slider-destaque-1, .wte-slider-destaque-2');
        sliders.forEach(slider => {
            if (!slider.hasAttribute('data-wte-initialized')) {
                new WTESlider(slider);
            }
        });
    },

    /**
     * Re-inicializar um slider específico
     *
     * @param {HTMLElement} element - Elemento do slider
     */
    reinit: function(element) {
        element.removeAttribute('data-wte-initialized');
        new WTESlider(element);
    }
};

// Inicializar todos os sliders quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.WTESliders.init();
});
