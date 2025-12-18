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
            <!-- Barra de Pesquisa e Ordenação -->
            <div class="wte-archive-toolbar">
                <div class="wte-search-box">
                    <input type="search"
                        id="wte-search-input"
                        name="wte_search"
                        placeholder="<?php esc_attr_e('Buscar viagens...', 'wte-sliders'); ?>"
                        value="<?php echo isset($_GET['wte_search']) ? esc_attr($_GET['wte_search']) : ''; ?>"
                        autocomplete="off">
                    <span class="wte-search-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>

                <div class="wte-sort-box">
                    <label for="wte-orderby"><?php esc_html_e('Ordenar por:', 'wte-sliders'); ?></label>
                    <select id="wte-orderby" name="wte_orderby">
                        <option value="date" <?php selected(isset($_GET['wte_orderby']) ? $_GET['wte_orderby'] : 'date', 'date'); ?>>
                            <?php esc_html_e('Mais Recentes', 'wte-sliders'); ?>
                        </option>
                        <option value="title" <?php selected(isset($_GET['wte_orderby']) ? $_GET['wte_orderby'] : '', 'title'); ?>>
                            <?php esc_html_e('Título (A-Z)', 'wte-sliders'); ?>
                        </option>
                        <option value="price_low" <?php selected(isset($_GET['wte_orderby']) ? $_GET['wte_orderby'] : '', 'price_low'); ?>>
                            <?php esc_html_e('Preço (Menor)', 'wte-sliders'); ?>
                        </option>
                        <option value="price_high" <?php selected(isset($_GET['wte_orderby']) ? $_GET['wte_orderby'] : '', 'price_high'); ?>>
                            <?php esc_html_e('Preço (Maior)', 'wte-sliders'); ?>
                        </option>
                    </select>
                </div>
            </div>

            <!-- Resultados -->
            <div id="wte-archive-results">
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
            </div>
        </main>
    </div>
</div>

<?php
get_footer();
