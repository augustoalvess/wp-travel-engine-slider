<?php

/**
 * Template: Single Trip Page
 *
 * Página customizada para exibição individual de viagens
 *
 * Override em tema: seu-tema/wte-sliders/single-trip.php
 *
 * @package WTE_Sliders
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Obter instâncias globais do plugin
global $post, $wte_sliders_query, $wte_sliders_template_loader;

// Usar variáveis locais para facilitar uso no template
$query = $wte_sliders_query;
$template_loader = $wte_sliders_template_loader;

// Obter dados da viagem
$trip = $query->get_single_trip_data($post->ID);

// Se não conseguiu carregar dados da viagem, retornar
if (!$trip) {
    echo '<p>' . esc_html__('Viagem não encontrada.', 'wte-sliders') . '</p>';
    get_footer();
    return;
}

// Obter configurações globais
$settings = get_option('wte_sliders_options', array());
?>

<div class="wte-single-trip-container">
    <?php
    // Hero Gallery Carousel
    if (!empty($trip->gallery)) {
        $template_loader->load_partial('single-trip/hero', array(
            'gallery' => $trip->gallery,
        ));
    }
    ?>

    <?php
    // Title Bar (Nome, Duração, Localização)
    $template_loader->load_partial('single-trip/title-bar', array(
        'title'           => $trip->title,
        'duration'        => $trip->duration,
        'destination'     => $trip->destination,
        'template_loader' => $template_loader,
    ));
    ?>

    <div class="wte-trip-content-wrapper">
        <div class="wte-trip-main-content">
            <?php
            // Overview/Description
            $template_loader->load_partial('single-trip/overview', array(
                'content' => $trip->content,
            ));
            ?>

            <?php
            // Highlights (3 ícones)
            if (!empty($trip->highlights)) {
                $template_loader->load_partial('single-trip/highlights', array(
                    'highlights'      => $trip->highlights,
                    'template_loader' => $template_loader,
                ));
            }
            ?>

            <?php
            // Itinerário
            if (!empty($trip->itinerary)) {
                $template_loader->load_partial('single-trip/itinerary', array(
                    'itinerary' => $trip->itinerary,
                ));
            }
            ?>
        </div>

        <aside class="wte-trip-sidebar">
            <?php
            // Pricing Box
            $template_loader->load_partial('single-trip/pricing-box', array(
                'price'            => $trip->price,
                'whatsapp'         => isset($settings['whatsapp_number']) ? $settings['whatsapp_number'] : '',
                'whatsapp_message' => isset($settings['whatsapp_message']) ? $settings['whatsapp_message'] : '',
                'trip_title'       => $trip->title,
                'trip_url'         => $trip->permalink,
            ));
            ?>
        </aside>
    </div>

    <?php
    // Gallery Grid - Full Width Section
    if (!empty($trip->gallery) && count($trip->gallery) > 1) {
        $template_loader->load_partial('single-trip/gallery-grid', array(
            'gallery' => array_slice($trip->gallery, 0, 5),
        ));
    }
    ?>
</div>

<?php get_footer(); ?>