<?php
/**
 * Partial: Overview/Description
 *
 * Seção de descrição da viagem
 *
 * @var string $content Conteúdo da viagem
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-trip-overview">
    <h2><?php esc_html_e('Título', 'wte-sliders'); ?></h2>
    <div class="wte-trip-description">
        <?php echo $content; // Já filtrado por the_content ?>
    </div>
</div>
