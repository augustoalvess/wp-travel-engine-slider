<?php

/**
 * Template para slider destaque 2 (3 itens por slide)
 *
 * @package WTE_Sliders
 * @var array $trips Array de viagens
 * @var bool $autoplay Se deve fazer autoplay
 * @var int $speed Velocidade do autoplay em ms
 * @var bool $arrows Se deve mostrar setas
 * @var int $per_page Número de itens por página
 * @var string $slider_id ID único do slider
 * @var WTE_Sliders_Query $query Instância da classe de query
 */

if (! defined('ABSPATH')) {
    exit;
}

// Agrupar viagens em páginas
$pages = array_chunk($trips, $per_page);
?>

<div class="wte-slider-destaque-2 swiper"
    id="<?php echo esc_attr($slider_id); ?>"
    data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
    data-speed="<?php echo esc_attr($speed); ?>"
    data-arrows="<?php echo $arrows ? 'true' : 'false'; ?>"
    data-per-page="<?php echo esc_attr($per_page); ?>">

    <div class="swiper-wrapper">
        <?php foreach ($pages as $page) : ?>
            <div class="swiper-slide wte-slider-slide">
                <div class="wte-slider-grid">
                    <?php foreach ($page as $trip) : ?>
                        <?php
                        // Carregar partial do card pequeno
                        $template_loader->load_partial('trip-card-small', array(
                            'trip'   => $trip,
                            'query'  => $query,
                            'loader' => $template_loader,
                        ));
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($arrows && count($pages) > 1) : ?>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    <?php endif; ?>


</div>