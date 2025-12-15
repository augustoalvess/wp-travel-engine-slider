<?php
/**
 * Partial: Social Media
 *
 * Links de redes sociais
 *
 * @var string $instagram Usuário do Instagram
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wte-trip-social">
    <h3><?php esc_html_e('Rede social:', 'wte-sliders'); ?></h3>
    <div class="wte-social-links">
        <a href="https://instagram.com/<?php echo esc_attr($instagram); ?>"
           target="_blank"
           rel="noopener noreferrer"
           class="wte-social-instagram">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4c0 3.2-2.6 5.8-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8C2 4.6 4.6 2 7.8 2m-.2 2C5.6 4 4 5.6 4 7.6v8.8C4 18.4 5.6 20 7.6 20h8.8c2 0 3.6-1.6 3.6-3.6V7.6C20 5.6 18.4 4 16.4 4H7.6m9.65 1.5c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1M12 7c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5m0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
            </svg>
            @<?php echo esc_html($instagram); ?>
        </a>
    </div>
</div>
