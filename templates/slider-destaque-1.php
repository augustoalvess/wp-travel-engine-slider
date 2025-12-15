<?php

/**
 * Template para slider destaque 1 (1 item por slide)
 *
 * @package WTE_Sliders
 * @var array $trips Array de viagens
 * @var bool $autoplay Se deve fazer autoplay
 * @var int $speed Velocidade do autoplay em ms
 * @var bool $arrows Se deve mostrar setas
 * @var string $slider_id ID único do slider
 * @var WTE_Sliders_Query $query Instância da classe de query
 */

if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-slider-destaque-1 swiper"
    id="<?php echo esc_attr($slider_id); ?>"
    data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
    data-speed="<?php echo esc_attr($speed); ?>"
    data-arrows="<?php echo $arrows ? 'true' : 'false'; ?>">

    <div class="swiper-wrapper">
        <?php foreach ($trips as $trip) : ?>
            <div class="swiper-slide wte-slider-slide">
                <?php
                // Carregar partial do card grande
                $template_loader->load_partial('trip-card-large', array(
                    'trip'   => $trip,
                    'query'  => $query,
                    'loader' => $template_loader,
                ));
                ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($arrows && count($trips) > 1) : ?>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    <?php endif; ?>


</div>