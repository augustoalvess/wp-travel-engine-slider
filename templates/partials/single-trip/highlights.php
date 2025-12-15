<?php
/**
 * Partial: Highlights
 *
 * Seção de destaques/features com ícones (máximo 3)
 *
 * @var array $highlights Array com highlights
 * @var WTE_Sliders_Template_Loader $template_loader
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-trip-highlights">
    <?php foreach ($highlights as $highlight): ?>
        <div class="wte-highlight-item">
            <div class="wte-highlight-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                </svg>
            </div>
            <p><?php echo esc_html($highlight); ?></p>
        </div>
    <?php endforeach; ?>
</div>
