<?php

/**
 * Partial: Title Bar
 *
 * Barra de título com nome, duração e localização da viagem
 *
 * @var string $title Nome da viagem
 * @var string $duration Duração formatada
 * @var string $destination Destino
 * @var WTE_Sliders_Template_Loader $template_loader
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-trip-title-bar">
    <h1><?php echo esc_html($title); ?></h1>
    <div class="wte-trip-meta-bar">
        <?php if (!empty($duration)): ?>
            <span class="wte-meta-item wte-meta-duration">
                <img src="<?php echo esc_url(WTE_SLIDERS_PLUGIN_URL . 'assets/images/icon-clock-type1.svg'); ?>" alt="" width="16" height="16">
                <?php echo esc_html($duration); ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($destination)): ?>
            <span class="wte-meta-item wte-meta-location">
                <img src="<?php echo esc_url(WTE_SLIDERS_PLUGIN_URL . 'assets/images/icon-location-type1.svg'); ?>" alt="" width="16" height="16">
                <?php echo esc_html($destination); ?>
            </span>
        <?php endif; ?>
    </div>
</div>