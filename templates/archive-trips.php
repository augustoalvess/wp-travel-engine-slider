<?php

/**
 * Template de Arquivo para Viagens do WP Travel Engine
 *
 * Template customizado para páginas de arquivo e taxonomias de viagens.
 * Exibe sidebar com filtros e grid de viagens.
 *
 * @package WTE_Sliders
 */

if (! defined('ABSPATH')) {
    exit;
}

// Acessar instâncias globais
global $wte_sliders_query, $wte_sliders_template_loader;

get_header();
?>

<div class="wte-archive-container">
    <div class="wte-archive-header">
        <h1 class="wte-archive-title">
            <?php
            if (is_tax('destination')) {
                single_term_title();
            } elseif (is_tax('trip-packages-categories')) {
                single_term_title();
            } elseif (is_post_type_archive('trip')) {
                esc_html_e('Todas as Viagens', 'wte-sliders');
            } else {
                the_archive_title();
            }
            ?>
        </h1>

        <?php if (is_tax() && term_description()) : ?>
            <div class="wte-archive-description">
                <?php echo term_description(); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="wte-archive-layout">
        <!-- Sidebar com Filtros -->
        <aside class="wte-archive-sidebar">
            <?php
            $wte_sliders_template_loader->load_partial('archive/filters-sidebar', array(
                'loader' => $wte_sliders_template_loader,
            ));
            ?>
        </aside>

        <!-- Área de Conteúdo Principal -->
        <main class="wte-archive-main">
            <?php if (have_posts()) : ?>
                <div class="wte-trips-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        // Usar método público da query class para obter dados
                        $trip_data = $wte_sliders_query->get_trip_data_from_id(get_the_ID());

                        // Carregar card genérico com contexto de arquivo
                        $wte_sliders_template_loader->load_partial('trip-card', array(
                            'trip'    => (object) $trip_data,
                            'loader'  => $wte_sliders_template_loader,
                            'context' => 'archive',
                            'options' => array(
                                'excerpt_length' => 20,
                                'button_text'    => __('Saiba mais', 'wte-sliders'),
                            ),
                        ));
                        ?>
                    <?php endwhile; ?>
                </div>

                <!-- Paginação -->
                <?php
                $wte_sliders_template_loader->load_partial('archive/pagination', array(
                    'loader' => $wte_sliders_template_loader,
                ));
                ?>

            <?php else : ?>
                <div class="wte-archive-empty">
                    <h2><?php esc_html_e('Nenhuma viagem encontrada', 'wte-sliders'); ?></h2>
                    <p><?php esc_html_e('Tente ajustar os filtros ou volte para a página inicial.', 'wte-sliders'); ?></p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php
get_footer();
