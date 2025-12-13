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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Agrupar viagens em páginas
$pages = array_chunk( $trips, $per_page );
?>

<div class="wte-slider-destaque-2"
     id="<?php echo esc_attr( $slider_id ); ?>"
     data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
     data-speed="<?php echo esc_attr( $speed ); ?>"
     data-arrows="<?php echo $arrows ? 'true' : 'false'; ?>"
     data-per-page="<?php echo esc_attr( $per_page ); ?>">

    <div class="wte-slider-wrapper">
        <div class="wte-slider-track">
            <?php foreach ( $pages as $page ) : ?>
                <div class="wte-slider-slide">
                    <div class="wte-slider-grid">
                        <?php foreach ( $page as $trip ) : ?>
                            <?php
                            // Carregar partial do card pequeno
                            $template_loader->load_partial( 'trip-card-small', array(
                                'trip'   => $trip,
                                'query'  => $query,
                                'loader' => $template_loader,
                            ) );
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ( $arrows && count( $pages ) > 1 ) : ?>
        <button class="wte-slider-prev" aria-label="<?php esc_attr_e( 'Anterior', 'wte-sliders' ); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <button class="wte-slider-next" aria-label="<?php esc_attr_e( 'Próximo', 'wte-sliders' ); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    <?php endif; ?>

    <?php if ( count( $pages ) > 1 ) : ?>
        <div class="wte-slider-dots"></div>
    <?php endif; ?>
</div>
