<?php

/**
 * Partial para card pequeno (slider destaque 2)
 *
 * WRAPPER DE COMPATIBILIDADE: Este arquivo agora é um wrapper para o componente
 * genérico trip-card.php. Mantido para compatibilidade com o slider Type 2.
 *
 * @package WTE_Sliders
 * @var object $trip Dados da viagem
 * @var WTE_Sliders_Template_Loader $loader Instância do template loader
 */

if (! defined('ABSPATH')) {
    exit;
}

// Carregar componente genérico com contexto de slider
// Nota: $loader é passado como $template_loader para o novo componente
$template_loader = $loader;

// Incluir o componente genérico diretamente
// (load_partial não pode ser usado aqui pois criaria recursão)
$context = 'slider';
$options = array(
    'card_classes'   => 'wte-trip-card-small',
    'excerpt_length' => 20,
    'button_text'    => __('Saiba mais', 'wte-sliders'),
);

include dirname(__FILE__) . '/trip-card.php';