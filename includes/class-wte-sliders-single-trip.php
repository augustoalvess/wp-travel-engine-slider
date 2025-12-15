<?php

/**
 * Classe para gerenciamento de templates de single trip
 *
 * @package WTE_Sliders
 */

// Prevenir acesso direto
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Classe WTE_Sliders_Single_Trip
 */
class WTE_Sliders_Single_Trip
{

    /**
     * Instância da classe de queries
     *
     * @var WTE_Sliders_Query
     */
    private $query;

    /**
     * Instância da classe de template loader
     *
     * @var WTE_Sliders_Template_Loader
     */
    private $template_loader;

    /**
     * Construtor
     *
     * @param WTE_Sliders_Query           $query           Instância do query handler
     * @param WTE_Sliders_Template_Loader $template_loader Instância do template loader
     */
    public function __construct($query, $template_loader)
    {
        $this->query = $query;
        $this->template_loader = $template_loader;
        $this->init_hooks();
    }

    /**
     * Inicializar hooks do WordPress
     */
    private function init_hooks()
    {
        add_filter('template_include', array($this, 'maybe_override_template'), 99);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_single_trip_assets'));
    }

    /**
     * Verificar se deve sobrescrever o template e retornar caminho customizado
     *
     * @param string $template Caminho do template original
     * @return string Caminho do template (original ou customizado)
     */
    public function maybe_override_template($template)
    {
        // Verificar se é página single de trip
        if (! is_singular('trip')) {
            return $template;
        }

        // Verificar se o template customizado está ativado nas configurações
        $options = get_option('wte_sliders_options', array());
        if (empty($options['enable_single_trip_template'])) {
            return $template;
        }

        // Verificar se a viagem específica tem override desativado (meta futuro)
        $disable_custom = get_post_meta(get_the_ID(), '_wte_sliders_disable_custom_template', true);
        if ($disable_custom === 'yes') {
            return $template;
        }

        // Localizar template customizado
        $custom_template = $this->template_loader->locate_template('single-trip');

        if (!$custom_template) {
            return $template;
        }

        // Disponibilizar query e template_loader globalmente para o template
        global $wte_sliders_query, $wte_sliders_template_loader;
        $wte_sliders_query = $this->query;
        $wte_sliders_template_loader = $this->template_loader;

        // Retornar template customizado
        return $custom_template;
    }

    /**
     * Enfileirar CSS e JS específicos para single trip
     */
    public function enqueue_single_trip_assets()
    {
        // Apenas enfileirar em páginas single trip
        if (! is_singular('trip')) {
            return;
        }

        // Verificar se o template customizado está ativado
        $options = get_option('wte_sliders_options', array());
        if (empty($options['enable_single_trip_template'])) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'wte-sliders-single-trip',
            WTE_SLIDERS_PLUGIN_URL . 'assets/css/single-trip.css',
            array('wte-sliders-swiper'),
            WTE_SLIDERS_VERSION,
            'all'
        );

        // JS
        wp_enqueue_script(
            'wte-sliders-single-trip',
            WTE_SLIDERS_PLUGIN_URL . 'assets/js/single-trip-init.js',
            array('wte-sliders-swiper'),
            WTE_SLIDERS_VERSION,
            true
        );
    }
}
