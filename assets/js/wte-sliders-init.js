/**
 * Inicialização dos Sliders usando Swiper (Substituto do Vanilla JS)
 * 
 * @package WTE_Sliders
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initSliders();
    });

    function initSliders() {
        const sliders = document.querySelectorAll('.wte-slider-destaque-1, .wte-slider-destaque-2, .wte-slider-featured-destinations');

        sliders.forEach(function (slider) {
            // Evitar dupla inicialização
            if (slider.classList.contains('swiper-initialized')) {
                return;
            }

            // Ler configurações dos atributos data
            const autoplay = slider.dataset.autoplay === 'true';
            const speed = parseInt(slider.dataset.speed) || 5000;
            const arrows = slider.dataset.arrows === 'true';

            // Configuração base do Swiper
            const swiperConfig = {
                loop: true,
                speed: 800, // Velocidade de transição suave
                spaceBetween: 30,
                autoHeight: true, // Ajustar altura automaticamente
                observer: true, // Importante para detectar mudanças no DOM
                observeParents: true, // Importante para sliders dentro de tabs/modais ou hidden containers

                observeParents: true, // Importante para sliders dentro de tabs/modais ou hidden containers

                // Navegação (Setas)
                navigation: {
                    nextEl: slider.closest('.wte-slider-wrapper') ? slider.closest('.wte-slider-wrapper').querySelector('.swiper-button-next') : slider.querySelector('.swiper-button-next'),
                    prevEl: slider.closest('.wte-slider-wrapper') ? slider.closest('.wte-slider-wrapper').querySelector('.swiper-button-prev') : slider.querySelector('.swiper-button-prev'),
                },
            };

            // Adicionar Autoplay se configurado
            if (autoplay) {
                swiperConfig.autoplay = {
                    delay: speed,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                };
            }

            // Inicializar Swiper
            new Swiper(slider, swiperConfig);

            // Ajuste específico para Setas do Tipo 1 (Centralizar no vídeo)
            if (slider.classList.contains('wte-slider-destaque-1') || slider.closest('.wte-slider-wrapper-1')) {
                alignArrowsByType1(slider);
                window.addEventListener('resize', function () {
                    alignArrowsByType1(slider);
                });
                // Tentar alinhar novamente após carregar imagens
                window.addEventListener('load', function () {
                    alignArrowsByType1(slider);
                });
            }
        });
    }

    function alignArrowsByType1(slider) {
        const wrapper = slider.closest('.wte-slider-wrapper-1');
        if (!wrapper) return;

        const media = wrapper.querySelector('.wte-trip-media');
        const prevBtn = wrapper.querySelector('.swiper-button-prev');
        const nextBtn = wrapper.querySelector('.swiper-button-next');

        if (media && prevBtn && nextBtn) {
            const mediaHeight = media.offsetHeight;
            const mediaRect = media.getBoundingClientRect();
            const wrapperRect = wrapper.getBoundingClientRect();

            // Calculate offset from top of wrapper to middle of media
            const mediaOffsetTop = mediaRect.top - wrapperRect.top;
            const topPosition = mediaOffsetTop + (mediaHeight / 2);

            prevBtn.style.top = topPosition + 'px';
            nextBtn.style.top = topPosition + 'px';
        }
    }

})();
