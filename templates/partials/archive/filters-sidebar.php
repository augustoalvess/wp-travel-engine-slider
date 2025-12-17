<?php

/**
 * Sidebar de Filtros para Arquivo de Viagens
 *
 * Exibe 4 filtros: Destino, Preço, Duração e Tipo de Viagem
 *
 * @package WTE_Sliders
 * @var WTE_Sliders_Template_Loader $loader
 */

if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-filters-sidebar">
    <h3 class="wte-filters-title">
        <?php esc_html_e('Filtrar Viagens', 'wte-sliders'); ?>
    </h3>

    <!-- Filtro 1: Destino (Hierárquico) -->
    <div class="wte-filter-group">
        <h4 class="wte-filter-label">
            <?php esc_html_e('Destino', 'wte-sliders'); ?>
        </h4>
        <div class="wte-filter-content">
            <?php
            // Buscar apenas destinos de nível superior (estados)
            $parent_destinations = get_terms(array(
                'taxonomy'   => 'destination',
                'parent'     => 0,  // Apenas pais
                'hide_empty' => true,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ));

            if (!empty($parent_destinations) && !is_wp_error($parent_destinations)) :
                $selected_destinations = isset($_GET['wte_destination']) ? (array) $_GET['wte_destination'] : array();

                foreach ($parent_destinations as $parent) :
                    $is_active = in_array($parent->term_id, $selected_destinations);
                    ?>
                    <!-- Estado/Região -->
                    <label class="wte-filter-checkbox wte-filter-parent">
                        <input type="checkbox"
                               name="wte_destination[]"
                               value="<?php echo esc_attr($parent->term_id); ?>"
                               <?php checked($is_active); ?>>
                        <span><?php echo esc_html($parent->name); ?></span>
                        <span class="wte-filter-count">(<?php echo $parent->count; ?>)</span>
                    </label>

                    <?php
                    // Buscar filhos (cidades)
                    $child_destinations = get_terms(array(
                        'taxonomy'   => 'destination',
                        'parent'     => $parent->term_id,
                        'hide_empty' => true,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ));

                    if (!empty($child_destinations) && !is_wp_error($child_destinations)) :
                        foreach ($child_destinations as $child) :
                            $is_child_active = in_array($child->term_id, $selected_destinations);
                            ?>
                            <!-- Cidade (indentada) -->
                            <label class="wte-filter-checkbox wte-filter-child">
                                <input type="checkbox"
                                       name="wte_destination[]"
                                       value="<?php echo esc_attr($child->term_id); ?>"
                                       <?php checked($is_child_active); ?>>
                                <span><?php echo esc_html($child->name); ?></span>
                                <span class="wte-filter-count">(<?php echo $child->count; ?>)</span>
                            </label>
                            <?php
                        endforeach;
                    endif;
                endforeach;
            endif;
            ?>
        </div>
    </div>

    <!-- Filtro 2: Preço -->
    <div class="wte-filter-group">
        <h4 class="wte-filter-label">
            <?php esc_html_e('Preço', 'wte-sliders'); ?>
        </h4>
        <div class="wte-filter-content">
            <input type="text"
                   id="wte-price-slider"
                   name="wte_price_range"
                   value=""
                   data-min="0"
                   data-max="5000"
                   data-from="<?php echo isset($_GET['wte_price_min']) ? intval($_GET['wte_price_min']) : 0; ?>"
                   data-to="<?php echo isset($_GET['wte_price_max']) ? intval($_GET['wte_price_max']) : 5000; ?>" />
        </div>
    </div>

    <!-- Filtro 3: Duração -->
    <div class="wte-filter-group">
        <h4 class="wte-filter-label">
            <?php esc_html_e('Duração (dias)', 'wte-sliders'); ?>
        </h4>
        <div class="wte-filter-content">
            <input type="text"
                   id="wte-duration-slider"
                   name="wte_duration"
                   value=""
                   data-min="1"
                   data-max="30"
                   data-from="<?php echo isset($_GET['wte_duration_min']) ? intval($_GET['wte_duration_min']) : 1; ?>"
                   data-to="<?php echo isset($_GET['wte_duration_max']) ? intval($_GET['wte_duration_max']) : 30; ?>" />
        </div>
    </div>

    <!-- Filtro 4: Tipo de Viagem -->
    <div class="wte-filter-group">
        <h4 class="wte-filter-label">
            <?php esc_html_e('Tipo de Viagem', 'wte-sliders'); ?>
        </h4>
        <div class="wte-filter-content">
            <?php
            $trip_types = get_terms(array(
                'taxonomy'   => 'trip_types',
                'hide_empty' => true,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ));

            if (!empty($trip_types) && !is_wp_error($trip_types)) :
                $selected_types = isset($_GET['wte_trip_type']) ? (array) $_GET['wte_trip_type'] : array();

                foreach ($trip_types as $type) :
                    $is_active = in_array($type->term_id, $selected_types);
                    ?>
                    <label class="wte-filter-checkbox">
                        <input type="checkbox"
                               name="wte_trip_type[]"
                               value="<?php echo esc_attr($type->term_id); ?>"
                               <?php checked($is_active); ?>>
                        <span><?php echo esc_html($type->name); ?></span>
                        <span class="wte-filter-count">(<?php echo $type->count; ?>)</span>
                    </label>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="wte-filter-actions">
        <button type="button" class="wte-filter-reset">
            <?php esc_html_e('Limpar Filtros', 'wte-sliders'); ?>
        </button>
    </div>
</div>
