<?php

/**
 * Componente Genérico de Trip Card
 *
 * Componente reutilizável para exibição de cards de viagens.
 * Pode ser usado em sliders e páginas de arquivo.
 *
 * @package WTE_Sliders
 * @var object $trip           Objeto com dados da viagem
 * @var WTE_Sliders_Template_Loader $loader  Instância do template loader
 * @var string $context        Contexto de exibição: 'slider' ou 'archive'
 * @var array  $options        Opções adicionais de configuração
 */

if (! defined('ABSPATH')) {
    exit;
}

// Configurações padrão
$defaults = array(
    'excerpt_length' => 20,
    'card_classes'   => '',
    'button_text'    => __('Saiba mais', 'wte-sliders'),
);

$options = wp_parse_args($options ?? array(), $defaults);

// Classes CSS do card
$card_classes = array(
    'wte-trip-card',
    'wte-trip-card--' . esc_attr($context),
    'wte-trip-card-small'
);

if (! empty($options['card_classes'])) {
    $card_classes[] = esc_attr($options['card_classes']);
}
?>

<div class="<?php echo implode(' ', $card_classes); ?>">
    <div class="wte-trip-image-wrapper">
        <?php if (! empty($trip->image)) : ?>
            <img src="<?php echo esc_url($trip->image); ?>" alt="<?php echo esc_attr($trip->title); ?>" loading="lazy">
        <?php endif; ?>

        <?php if ($trip->has_promo) : ?>
            <div class="wte-trip-badge">
                <?php esc_html_e('Promoção', 'wte-sliders'); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="wte-trip-card-content">
        <?php if (! empty($trip->duration)) : ?>
            <div class="wte-trip-duration">
                <img src="<?php echo esc_url($loader->get_asset_url('images/icon-clock-type2.svg')); ?>" alt="" width="16" height="16">
                <span><?php echo esc_html($trip->duration); ?></span>
            </div>
        <?php endif; ?>

        <h3 class="wte-trip-title">
            <a href="<?php echo esc_url($trip->permalink); ?>">
                <?php echo esc_html($trip->title); ?>
            </a>
        </h3>

        <?php if (! empty($trip->destination)) : ?>
            <div class="wte-trip-location">
                <img src="<?php echo esc_url($loader->get_asset_url('images/icon-location-type2.svg')); ?>" alt="" width="16" height="16">
                <span><?php echo esc_html($trip->destination); ?></span>
            </div>
        <?php endif; ?>

        <?php if (! empty($trip->excerpt)) : ?>
            <div class="wte-trip-excerpt">
                <?php echo wp_kses_post(wp_trim_words($trip->excerpt, $options['excerpt_length'])); ?>
            </div>
        <?php endif; ?>

        <div class="wte-trip-footer">
            <?php if (!empty($trip->price['has_child'])) : ?>
                <!-- Layout Horizontal: Botão + Adulto + Criança -->

                <!-- Botão -->
                <a href="<?php echo esc_url($trip->permalink); ?>" class="wte-trip-button">
                    <?php echo esc_html($options['button_text']); ?>
                </a>

                <div class="wte-trip-prices-horizontal">
                    <!-- Adulto -->
                    <div class="wte-price-section wte-price-adult">
                        <div class="wte-price-label">
                            <?php esc_html_e('Adulto', 'wte-sliders'); ?>
                        </div>
                        <?php if ($trip->price['adult']['has_sale']) : ?>
                            <div class="wte-price-regular-strikethrough">
                                <?php echo esc_html($trip->price['adult']['formatted_regular']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="wte-price-current">
                            <?php echo esc_html($trip->price['adult']['formatted']); ?>
                        </div>
                    </div>

                    <!-- Criança -->
                    <div class="wte-price-section wte-price-child">
                        <div class="wte-price-label">
                            <?php esc_html_e('Criança', 'wte-sliders'); ?>
                        </div>
                        <?php if ($trip->price['child']['has_sale']) : ?>
                            <div class="wte-price-regular-strikethrough">
                                <?php echo esc_html($trip->price['child']['formatted_regular']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="wte-price-current">
                            <?php echo esc_html($trip->price['child']['formatted']); ?>
                        </div>
                    </div>
                </div>

            <?php else : ?>
                <!-- Layout Simples: Botão + Preço (sem label Adulto) -->
                <a href="<?php echo esc_url($trip->permalink); ?>" class="wte-trip-button">
                    <?php echo esc_html($options['button_text']); ?>
                </a>

                <div class="wte-trip-price">
                    <?php if ($trip->price['adult']['has_sale']) : ?>
                        <div class="wte-price-regular-strikethrough">
                            <?php echo esc_html($trip->price['adult']['formatted_regular']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="wte-price-current">
                        <?php echo esc_html($trip->price['adult']['formatted']); ?>
                    </div>

                    <div class="wte-price-per">
                        <?php
                        $pricing_type = $trip->price['adult']['pricing_type'] ?? 'per-person';
                        echo esc_html($pricing_type === 'per-group' ? __('por grupo', 'wte-sliders') : __('por pessoa', 'wte-sliders'));
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>