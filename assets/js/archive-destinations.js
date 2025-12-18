/**
 * Script para o Template de Arquivo de Destinos
 *
 * Gerencia a funcionalidade de ordenação A-Z/Z-A dos destinos.
 * Recarrega a página com o parâmetro de ordenação atualizado na URL.
 *
 * @package WTE_Sliders
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        /**
         * Handler para mudança no dropdown de ordenação
         */
        $('#dest-orderby').on('change', function() {
            const orderValue = $(this).val();
            const currentUrl = new URL(window.location.href);

            // Atualizar ou adicionar parâmetro orderby na URL
            currentUrl.searchParams.set('orderby', orderValue);

            // Recarregar página com nova ordenação
            window.location.href = currentUrl.toString();
        });
    });

})(jQuery);
