/**
 * Archive Filters Handler
 *
 * Gerencia a funcionalidade de filtros na página de arquivo de viagens.
 *
 * @package WTE_Sliders
 */

(function($) {
    'use strict';

    /**
     * Construir URL com parâmetros de filtro
     *
     * @return {string} URL com query string de filtros
     */
    function buildFilterUrl() {
        const params = new URLSearchParams();

        // Filtro de Destino
        $('input[name="wte_destination[]"]:checked').each(function() {
            params.append('wte_destination[]', $(this).val());
        });

        // Filtro de Tipo de Viagem
        $('input[name="wte_trip_type[]"]:checked').each(function() {
            params.append('wte_trip_type[]', $(this).val());
        });

        // Filtro de Preço (do slider)
        const priceSlider = $("#wte-price-slider").data("ionRangeSlider");
        if (priceSlider) {
            params.append('wte_price_min', priceSlider.result.from);
            params.append('wte_price_max', priceSlider.result.to);
        }

        // Filtro de Duração (do slider)
        const durationSlider = $("#wte-duration-slider").data("ionRangeSlider");
        if (durationSlider) {
            params.append('wte_duration_min', durationSlider.result.from);
            params.append('wte_duration_max', durationSlider.result.to);
        }

        // Construir URL
        const baseUrl = window.location.pathname;
        const queryString = params.toString();

        return queryString ? `${baseUrl}?${queryString}` : baseUrl;
    }

    /**
     * Aplicar filtros (redirecionar para URL com filtros)
     */
    function applyFilters() {
        const url = buildFilterUrl();
        window.location.href = url;
    }

    /**
     * Limpar todos os filtros (redirecionar para URL sem query string)
     */
    function resetFilters() {
        window.location.href = window.location.pathname;
    }

    /**
     * Inicialização
     */
    $(document).ready(function() {
        // Inicializar slider de preço
        if ($("#wte-price-slider").length) {
            $("#wte-price-slider").ionRangeSlider({
                type: "double",
                min: 0,
                max: 5000,
                from: parseInt($("#wte-price-slider").data('from')),
                to: parseInt($("#wte-price-slider").data('to')),
                prefix: "R$ ",
                grid: true,
                grid_num: 5
            });
        }

        // Inicializar slider de duração
        if ($("#wte-duration-slider").length) {
            $("#wte-duration-slider").ionRangeSlider({
                type: "double",
                min: 1,
                max: 30,
                from: parseInt($("#wte-duration-slider").data('from')),
                to: parseInt($("#wte-duration-slider").data('to')),
                postfix: " dias",
                grid: true,
                grid_num: 6
            });
        }

        // Botão "Aplicar Filtros"
        $('.wte-filter-apply').on('click', function(e) {
            e.preventDefault();
            applyFilters();
        });

        // Botão "Limpar Filtros"
        $('.wte-filter-reset').on('click', function(e) {
            e.preventDefault();
            resetFilters();
        });

        // Opcional: Aplicar filtros ao pressionar Enter em qualquer checkbox
        $('.wte-filter-checkbox input').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                applyFilters();
            }
        });
    });

})(jQuery);
