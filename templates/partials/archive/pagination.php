<?php

/**
 * Paginação para Arquivo de Viagens
 *
 * @package WTE_Sliders
 * @var WTE_Sliders_Template_Loader $loader
 */

if (! defined('ABSPATH')) {
    exit;
}

// Gerar paginação
$pagination = paginate_links(array(
    'mid_size'  => 2,
    'prev_text' => __('&laquo; Anterior', 'wte-sliders'),
    'next_text' => __('Próxima &raquo;', 'wte-sliders'),
    'type'      => 'list',
));

if ($pagination) :
    ?>
    <nav class="wte-archive-pagination" aria-label="<?php esc_attr_e('Navegação de páginas', 'wte-sliders'); ?>">
        <?php echo $pagination; ?>
    </nav>
    <?php
endif;
