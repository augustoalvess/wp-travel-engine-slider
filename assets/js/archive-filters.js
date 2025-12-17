/**
 * Archive Filters Handler
 *
 * Gerencia a funcionalidade de filtros na página de arquivo de viagens.
 * Filtros são aplicados via AJAX sem reload de página.
 *
 * @package WTE_Sliders
 */

(function($) {
    'use strict';

    let isFiltering = false;
    let filterTimeout = null;

    /**
     * Coletar dados dos filtros
     *
     * @return {Object} Dados dos filtros
     */
    function getFilterData() {
        const data = {
            action: 'wte_filter_trips',
            nonce: wteFiltersAjax.nonce,
            paged: 1,
        };

        // Filtro de Destino
        const destinations = [];
        $('input[name="wte_destination[]"]:checked').each(function() {
            destinations.push($(this).val());
        });
        if (destinations.length > 0) {
            data.wte_destination = destinations;
        }

        // Filtro de Tipo de Viagem
        const tripTypes = [];
        $('input[name="wte_trip_type[]"]:checked').each(function() {
            tripTypes.push($(this).val());
        });
        if (tripTypes.length > 0) {
            data.wte_trip_type = tripTypes;
        }

        // Filtro de Preço (do slider)
        const priceSlider = $("#wte-price-slider").data("ionRangeSlider");
        if (priceSlider) {
            data.wte_price_min = priceSlider.result.from;
            data.wte_price_max = priceSlider.result.to;
        }

        // Filtro de Duração (do slider)
        const durationSlider = $("#wte-duration-slider").data("ionRangeSlider");
        if (durationSlider) {
            data.wte_duration_min = durationSlider.result.from;
            data.wte_duration_max = durationSlider.result.to;
        }

        return data;
    }

    /**
     * Aplicar filtros via AJAX
     */
    function applyFiltersAjax() {
        if (isFiltering) {
            return;
        }

        isFiltering = true;

        // Adicionar indicador de loading
        $('.wte-archive-main').css('opacity', '0.5');
        $('.wte-archive-main').append('<div class="wte-filter-loading"><div class="spinner"></div></div>');

        const filterData = getFilterData();

        $.ajax({
            url: wteFiltersAjax.ajaxurl,
            type: 'POST',
            data: filterData,
            success: function(response) {
                if (response.success) {
                    // Substituir conteúdo
                    $('.wte-archive-main').html(response.html);

                    // Scroll suave para o topo dos resultados
                    $('html, body').animate({
                        scrollTop: $('.wte-archive-main').offset().top - 100
                    }, 300);
                }
            },
            error: function() {
                alert('Erro ao aplicar filtros. Por favor, tente novamente.');
            },
            complete: function() {
                isFiltering = false;
                $('.wte-archive-main').css('opacity', '1');
                $('.wte-filter-loading').remove();
            }
        });
    }

    /**
     * Aplicar filtros com debounce (para sliders)
     */
    function applyFiltersDebounced() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            applyFiltersAjax();
        }, 500);
    }

    /**
     * Limpar todos os filtros
     */
    function resetFilters() {
        // Desmarcar checkboxes
        $('input[name="wte_destination[]"]:checked').prop('checked', false);
        $('input[name="wte_trip_type[]"]:checked').prop('checked', false);

        // Resetar sliders
        const priceSlider = $("#wte-price-slider").data("ionRangeSlider");
        if (priceSlider) {
            priceSlider.reset();
        }

        const durationSlider = $("#wte-duration-slider").data("ionRangeSlider");
        if (durationSlider) {
            durationSlider.reset();
        }

        // Aplicar filtros limpos
        applyFiltersAjax();
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
                grid_num: 5,
                onFinish: function() {
                    applyFiltersDebounced();
                }
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
                grid_num: 6,
                onFinish: function() {
                    applyFiltersDebounced();
                }
            });
        }

        // Aplicar filtros ao mudar checkboxes
        $('.wte-filter-checkbox input[type="checkbox"]').on('change', function() {
            applyFiltersAjax();
        });

        // Botão "Limpar Filtros"
        $(document).on('click', '.wte-filter-reset', function(e) {
            e.preventDefault();
            resetFilters();
        });
    });

})(jQuery);
