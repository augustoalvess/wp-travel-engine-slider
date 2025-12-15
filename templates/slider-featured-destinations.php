<?php

/**
 * Template para slider de destinos em destaque (4 itens por slide)
 *
 * @package WTE_Sliders
 * @var array $destinations Array de destinos
 * @var bool $autoplay Se deve fazer autoplay
 * @var int $speed Velocidade do autoplay em ms
 * @var bool $arrows Se deve mostrar setas
 * @var string $slider_id ID único do slider
 */

if (! defined('ABSPATH')) {
    exit;
}

// Configuração fixa de 4 itens por página
$per_page = 4;

// Agrupar destinos em páginas
$pages = array_chunk($destinations, $per_page);
?>

<div class="wte-slider-wrapper wte-slider-wrapper-destinations">
    <div class="wte-slider-featured-destinations swiper"
        id="<?php echo esc_attr($slider_id); ?>"
        data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
        data-speed="<?php echo esc_attr($speed); ?>"
        data-arrows="<?php echo $arrows ? 'true' : 'false'; ?>"
        data-per-page="<?php echo esc_attr($per_page); ?>">

        <div class="swiper-wrapper">
            <?php foreach ($pages as $page) : ?>
                <div class="swiper-slide wte-slider-slide">
                    <div class="wte-destinations-grid">
                        <?php foreach ($page as $destination) : ?>
                            <a href="<?php echo esc_url($destination->permalink); ?>" class="wte-destination-card" title="<?php echo esc_attr($destination->title); ?>">
                                <div class="wte-destination-media">
                                    <?php if (! empty($destination->image)) : ?>
                                        <img src="<?php echo esc_url($destination->image); ?>" alt="<?php echo esc_attr($destination->title); ?>">
                                    <?php else : ?>
                                        <div class="wte-placeholder-image"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="wte-destination-content">
                                    <h3 class="wte-destination-title"><?php echo esc_html($destination->title); ?></h3>
                                    <?php if (! empty($destination->description)) : ?>
                                        <div class="wte-destination-description"><?php echo wp_trim_words($destination->description, 10); ?></div>
                                    <?php endif; ?>
                                    <div class="wte-destination-footer">
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>

                        <?php
                        // Preencher espaços vazios se a última página não estiver cheia
                        $remaining = $per_page - count($page);
                        if ($remaining > 0) {
                            for ($i = 0; $i < $remaining; $i++) {
                                echo '<div class="wte-destination-card wte-card-empty"></div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($arrows && count($pages) > 1) : ?>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    <?php endif; ?>
</div>