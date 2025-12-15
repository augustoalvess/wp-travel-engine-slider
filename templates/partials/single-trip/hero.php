<?php
/**
 * Partial: Hero Carousel
 *
 * Carrossel de imagens hero com 3 slides visíveis
 *
 * @var array $gallery Array de imagens
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-trip-hero">
    <div class="wte-trip-hero-slider swiper" id="hero-<?php echo uniqid(); ?>">
        <div class="swiper-wrapper">
            <?php foreach ($gallery as $image): ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url($image['url']); ?>"
                         alt="<?php echo esc_attr($image['alt']); ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>
