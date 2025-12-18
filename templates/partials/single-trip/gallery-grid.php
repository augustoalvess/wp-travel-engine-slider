<?php
/**
 * Partial: Gallery Grid
 *
 * Grid de galeria de imagens (máximo 5)
 *
 * @var array $gallery Array de imagens
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-trip-gallery-section wte-full-width">
    <h2><?php esc_html_e('Galeria', 'wte-sliders'); ?></h2>
    <div class="wte-gallery-grid">
        <?php foreach ($gallery as $index => $image): ?>
            <div class="wte-gallery-item">
                <a href="<?php echo esc_url($image['url'] ?? $image['thumb']); ?>"
                   class="glightbox"
                   data-gallery="trip-gallery"
                   data-glightbox="title: <?php echo esc_attr($image['alt']); ?>">
                    <img src="<?php echo esc_url($image['thumb']); ?>"
                         alt="<?php echo esc_attr($image['alt']); ?>"
                         loading="lazy">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
