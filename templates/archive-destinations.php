<?php

/**
 * Template de Arquivo para Destinos do WP Travel Engine
 *
 * Template customizado para exibição de todos os destinos em formato de cards.
 * Exibe destinos com imagem, descrição, preço mínimo e ordenação A-Z/Z-A.
 *
 * @package WTE_Sliders
 */

if (! defined('ABSPATH')) {
    exit;
}

// Acessar instâncias globais
global $wte_sliders_destination_archive, $wte_sliders_template_loader;

// Obter ordenação via parâmetro GET
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'asc';
$order = ($orderby === 'desc') ? 'DESC' : 'ASC';

// Buscar destinos com preços
$destinations = $wte_sliders_destination_archive->get_destinations_with_prices($order);

get_header();
?>

<div class="wte-destinations-container">
    <!-- Cabeçalho da Página -->
    <div class="wte-destinations-header">
        <h1 class="wte-destinations-title">
            <?php esc_html_e('Destinos', 'wte-sliders'); ?>
        </h1>
        <p class="wte-destinations-subtitle">
            <?php esc_html_e('Explore nossos destinos incríveis', 'wte-sliders'); ?>
        </p>
    </div>

    <!-- Barra de Ferramentas (Contagem e Ordenação) -->
    <div class="wte-destinations-toolbar">
        <div class="wte-destinations-count">
            <?php
            printf(
                esc_html(_n('%d destino disponível', '%d destinos disponíveis', count($destinations), 'wte-sliders')),
                count($destinations)
            );
            ?>
        </div>

        <div class="wte-destinations-sort">
            <label for="dest-orderby">
                <?php esc_html_e('Ordenar:', 'wte-sliders'); ?>
            </label>
            <select id="dest-orderby" name="orderby">
                <option value="asc" <?php selected($orderby, 'asc'); ?>>
                    <?php esc_html_e('A-Z', 'wte-sliders'); ?>
                </option>
                <option value="desc" <?php selected($orderby, 'desc'); ?>>
                    <?php esc_html_e('Z-A', 'wte-sliders'); ?>
                </option>
            </select>
        </div>
    </div>

    <!-- Grid de Cards dos Destinos -->
    <div class="wte-destinations-grid">
        <?php if (! empty($destinations)) : ?>
            <?php foreach ($destinations as $destination) : ?>
                <?php
                // Carregar partial do card de destino
                $wte_sliders_template_loader->load_partial('destination-card', array(
                    'destination' => $destination,
                    'loader'      => $wte_sliders_template_loader,
                ));
                ?>
            <?php endforeach; ?>
        <?php else : ?>
            <!-- Estado Vazio -->
            <div class="wte-destinations-empty">
                <h2><?php esc_html_e('Nenhum destino disponível', 'wte-sliders'); ?></h2>
                <p><?php esc_html_e('Novos destinos serão adicionados em breve.', 'wte-sliders'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
