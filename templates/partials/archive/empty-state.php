<?php

/**
 * Estado Vazio - Nenhum resultado encontrado
 *
 * Exibido quando os filtros não retornam nenhum resultado.
 *
 * @package WTE_Sliders
 */

if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-archive-empty">
    <h2><?php esc_html_e('Nenhuma viagem encontrada', 'wte-sliders'); ?></h2>
    <p><?php esc_html_e('Tente ajustar os filtros para ver mais resultados.', 'wte-sliders'); ?></p>
</div>
