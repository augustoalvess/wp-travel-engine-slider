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
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/>
                </svg>
                <?php echo esc_html($duration); ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($destination)): ?>
            <span class="wte-meta-item wte-meta-location">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <?php echo esc_html($destination); ?>
            </span>
        <?php endif; ?>
    </div>
</div>
