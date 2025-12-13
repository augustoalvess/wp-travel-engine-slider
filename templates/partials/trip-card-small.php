<?php
/**
 * Partial para card pequeno (slider destaque 2)
 *
 * @package WTE_Sliders
 * @var object $trip Dados da viagem
 * @var WTE_Sliders_Query $query Instância da classe de query
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wte-trip-card-small">
    <div class="wte-trip-image-wrapper">
        <?php if ( ! empty( $trip->image ) ) : ?>
            <img src="<?php echo esc_url( $trip->image ); ?>" alt="<?php echo esc_attr( $trip->title ); ?>" loading="lazy">
        <?php endif; ?>

        <?php if ( $trip->has_promo ) : ?>
            <div class="wte-trip-badge">
                <?php esc_html_e( 'Promoção', 'wte-sliders' ); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="wte-trip-card-content">
        <?php if ( ! empty( $trip->duration ) ) : ?>
            <div class="wte-trip-duration">
                <img src="<?php echo esc_url( $loader->get_asset_url( 'images/icon-clock.svg' ) ); ?>" alt="" width="16" height="16">
                <span><?php echo esc_html( $trip->duration ); ?></span>
            </div>
        <?php endif; ?>

        <h3 class="wte-trip-title">
            <a href="<?php echo esc_url( $trip->permalink ); ?>">
                <?php echo esc_html( $trip->title ); ?>
            </a>
        </h3>

        <?php if ( ! empty( $trip->destination ) ) : ?>
            <div class="wte-trip-location">
                <img src="<?php echo esc_url( $loader->get_asset_url( 'images/icon-location.svg' ) ); ?>" alt="" width="16" height="16">
                <span><?php echo esc_html( $trip->destination ); ?></span>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $trip->excerpt ) ) : ?>
            <div class="wte-trip-excerpt">
                <?php echo wp_kses_post( wp_trim_words( $trip->excerpt, 20 ) ); ?>
            </div>
        <?php endif; ?>

        <div class="wte-trip-footer">
            <a href="<?php echo esc_url( $trip->permalink ); ?>" class="wte-trip-button">
                <?php esc_html_e( 'Saiba mais', 'wte-sliders' ); ?>
            </a>

            <div class="wte-trip-price">
                <?php if ( $trip->has_promo && $trip->price['regular'] > 0 ) : ?>
                    <div class="wte-price-from">
                        <?php esc_html_e( 'De', 'wte-sliders' ); ?>
                        R$ <?php echo esc_html( number_format( $trip->price['regular'], 2, ',', '.' ) ); ?>
                        <?php esc_html_e( 'por', 'wte-sliders' ); ?>
                    </div>
                <?php endif; ?>

                <div class="wte-price-current">
                    <?php echo esc_html( $trip->price['formatted'] ); ?>
                </div>

                <div class="wte-price-per">
                    <?php esc_html_e( 'por pessoa', 'wte-sliders' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
