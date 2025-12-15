<?php
/**
 * Partial: Itinerary
 *
 * Seção de roteiro com roteiro encaixado e personalizado
 *
 * @var array $itinerary Array com itinerário
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-trip-itinerary">
    <h2><?php esc_html_e('Roteiro', 'wte-sliders'); ?></h2>

    <?php if (!empty($itinerary)): ?>
    <div class="wte-itinerary-section">
        <h3><?php esc_html_e('Roteiro Encaixado:', 'wte-sliders'); ?></h3>
        <ul class="wte-itinerary-list">
            <?php foreach ($itinerary as $day): ?>
                <li>
                    <strong><?php echo esc_html($day['label']); ?>:</strong>
                    <?php echo esc_html($day['title']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="wte-itinerary-section">
        <h3><?php esc_html_e('Roteiro Personalizado:', 'wte-sliders'); ?></h3>
        <p><?php esc_html_e('Descubra novos caminhos e experiências a bordo do Trem Valentino. Entre em contato conosco e receba atendimento personalizado dos nossos consultores.', 'wte-sliders'); ?></p>
    </div>
</div>
