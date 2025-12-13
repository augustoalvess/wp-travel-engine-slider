/**
 * Inicialização dos Sliders usando Swiper (Substituto do Vanilla JS)
 * 
 * @package WTE_Sliders
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initSliders();
    });

    function initSliders() {
        const sliders = document.querySelectorAll('.wte-slider-destaque-1, .wte-slider-destaque-2');

        sliders.forEach(function(slider) {
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
                
                // Paginação (Dots)
                pagination: {
                    el: slider.querySelector('.swiper-pagination'),
                    clickable: true,
                },

                // Navegação (Setas)
                navigation: {
                    nextEl: slider.querySelector('.swiper-button-next'),
                    prevEl: slider.querySelector('.swiper-button-prev'),
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
        });
    }

})();
