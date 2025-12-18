<?php

/**
 * Partial: Card de Destino
 *
 * Exibe um card individual de destino com imagem/cor de fallback,
 * nome, descrição e preço mínimo.
 *
 * @package WTE_Sliders
 *
 * @var object $destination Objeto com dados do destino
 * @var WTE_Sliders_Template_Loader $loader Instância do template loader
 */

if (! defined('ABSPATH')) {
    exit;
}

// Cor de fallback quando não houver imagem
$fallback_color = '#b57d7e';
$has_image = ! empty($destination->image);

// Estilo inline para background (imagem ou cor)
$background_style = $has_image
    ? 'background-image: url(' . esc_url($destination->image) . ');'
    : 'background-color: ' . esc_attr($fallback_color) . ';';
?>

<a href="<?php echo esc_url($destination->permalink); ?>" class="wte-destination-card">
    <div class="wte-destination-card-inner" style="<?php echo $background_style; ?>">

        <!-- Overlay com gradiente para legibilidade do texto -->
        <div class="wte-destination-overlay"></div>

        <!-- Conteúdo (Nome e Descrição) - Canto Inferior Esquerdo -->
        <div class="wte-destination-content">
            <h3 class="wte-destination-name">
                <?php echo esc_html($destination->name); ?>
            </h3>

            <?php if (! empty($destination->description)) : ?>
                <p class="wte-destination-description">
                    <?php echo esc_html(wp_trim_words($destination->description, 15)); ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Seção de Preço - Canto Inferior Direito -->
        <?php if (! empty($destination->formatted_price)) : ?>
            <div class="wte-destination-price">
                <div class="wte-destination-price-label">
                    <?php esc_html_e('A partir de', 'wte-sliders'); ?>
                </div>
                <div class="wte-destination-price-value">
                    <?php echo esc_html($destination->formatted_price); ?>
                </div>
                <div class="wte-destination-price-per">
                    <?php
                    // Destinations show minimum price, default to per-person
                    $pricing_type = $destination->min_price['pricing_type'] ?? 'per-person';
                    echo esc_html($pricing_type === 'per-group' ? __('por grupo', 'wte-sliders') : __('por pessoa', 'wte-sliders'));
                    ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</a>