<?php

/**
 * Partial para card grande (slider destaque 1)
 *
 * @package WTE_Sliders
 * @var object $trip Dados da viagem
 * @var WTE_Sliders_Query $query Instância da classe de query
 */

if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-trip-card-large">
    <div class="wte-trip-media">
        <?php if (! empty($trip->video)) : ?>
            <?php $embed_url = $query->get_video_embed_url($trip->video); ?>
            <?php if ($embed_url) : ?>
                <div class="wte-trip-video">
                    <iframe
                        src="<?php echo esc_url($embed_url); ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
            <?php endif; ?>
        <?php elseif (! empty($trip->image)) : ?>
            <div class="wte-trip-image">
                <img src="<?php echo esc_url($trip->image); ?>" alt="<?php echo esc_attr($trip->title); ?>" loading="lazy">
                <div class="wte-trip-play-overlay">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="40" fill="white" opacity="0.9" />
                        <path d="M32 25L55 40L32 55V25Z" fill="#00BCD4" />
                    </svg>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="wte-trip-content">
        <h3 class="wte-trip-title">
            <a href="<?php echo esc_url($trip->permalink); ?>">
                <?php echo esc_html($trip->title); ?>
            </a>
        </h3>

        <div class="wte-trip-meta">
            <?php if (! empty($trip->duration)) : ?>
                <div class="wte-trip-duration">
                    <img src="<?php echo esc_url($loader->get_asset_url('images/icon-clock-type1.svg')); ?>" alt="" width="16" height="16">
                    <span><?php echo esc_html($trip->duration); ?></span>
                </div>
            <?php endif; ?>

            <?php if (! empty($trip->destination)) : ?>
                <div class="wte-trip-location">
                    <img src="<?php echo esc_url($loader->get_asset_url('images/icon-location-type1.svg')); ?>" alt="" width="16" height="16">
                    <span><?php echo esc_html($trip->destination); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="wte-trip-actions">
            <a href="<?php echo esc_url($trip->permalink); ?>" class="wte-trip-button">
                <?php esc_html_e('Saiba mais', 'wte-sliders'); ?>
            </a>
        </div>
    </div>
</div>