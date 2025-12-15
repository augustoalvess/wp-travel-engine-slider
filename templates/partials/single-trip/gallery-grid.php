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

<div class="wte-trip-gallery-section">
    <h2><?php esc_html_e('Galeria', 'wte-sliders'); ?></h2>
    <div class="wte-gallery-grid">
        <?php foreach ($gallery as $image): ?>
            <div class="wte-gallery-item">
                <img src="<?php echo esc_url($image['thumb']); ?>"
                     alt="<?php echo esc_attr($image['alt']); ?>"
                     loading="lazy">
            </div>
        <?php endforeach; ?>
    </div>
</div>
