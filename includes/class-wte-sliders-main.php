<?php

/**
 * Classe principal do plugin WP Travel Engine Sliders
 *
 * @package WTE_Sliders
 */

// Prevenir acesso direto
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Classe WTE_Sliders_Main
 */
class WTE_Sliders_Main
{

    /**
     * Instância única da classe (Singleton)
     *
     * @var WTE_Sliders_Main
     */
    private static $instance = null;

    /**
     * Instância da classe de queries
     *
     * @var WTE_Sliders_Query
     */
    public $query;

    /**
     * Instância da classe de shortcodes
     *
     * @var WTE_Sliders_Shortcodes
     */
    public $shortcodes;

    /**
     * Instância da classe de template loader
     *
     * @var WTE_Sliders_Template_Loader
     */
    public $template_loader;

    /**
     * Instância do handler de single trip
     *
     * @var WTE_Sliders_Single_Trip
     */
    public $single_trip;

    /**
     * Instância do handler de archive
     *
     * @var WTE_Sliders_Archive
     */
    public $archive;

    /**
     * Instância do gerenciador de configurações
     *
     * @var WTE_Sliders_Settings
     */
    public $settings;

    /**
     * Instância do handler de destination archive
     *
     * @var WTE_Sliders_Destination_Archive
     */
    public $destination_archive;

    /**
     * Obter instância única da classe
     *
     * @return WTE_Sliders_Main
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct()
    {
        $this->init_hooks();
        $this->init_components();
    }

    /**
     * Inicializar hooks do WordPress
     */
    private function init_hooks()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('init', array($this, 'load_textdomain'));
        add_action('init', array($this, 'add_destination_base_rewrite'), 20);
        add_filter('query_vars', array($this, 'add_destination_query_var'));
        add_action('template_redirect', array($this, 'handle_destination_base_template'), 1);
    }

    /**
     * Inicializar componentes do plugin
     */
    private function init_components()
    {
        $this->query = new WTE_Sliders_Query();
        $this->template_loader = new WTE_Sliders_Template_Loader();
        $this->shortcodes = new WTE_Sliders_Shortcodes($this->query, $this->template_loader);

        // Inicializar handler de single trip
        $this->single_trip = new WTE_Sliders_Single_Trip($this->query, $this->template_loader);

        // Inicializar handler de archive
        $this->archive = new WTE_Sliders_Archive($this->query, $this->template_loader);

        // Inicializar handler de destination archive
        $this->destination_archive = new WTE_Sliders_Destination_Archive($this->query, $this->template_loader);

        // Inicializar configurações (apenas admin)
        if (is_admin()) {
            $this->settings = new WTE_Sliders_Settings();
        }
    }

    /**
     * Enfileirar estilos CSS
     */
    public function enqueue_styles()
    {
        // Swiper CSS
        wp_enqueue_style(
            'wte-sliders-swiper',
            WTE_SLIDERS_PLUGIN_URL . 'assets/css/swiper-bundle.min.css',
            array(),
            '11.0.0',
            'all'
        );

        // CSS base do slider
        wp_enqueue_style(
            'wte-sliders-base',
            WTE_SLIDERS_PLUGIN_URL . 'assets/css/slider-base.css',
            array('wte-sliders-swiper'),
            WTE_SLIDERS_VERSION,
            'all'
        );

        // CSS do slider destaque 1
        wp_enqueue_style(
            'wte-sliders-destaque-1',
            WTE_SLIDERS_PLUGIN_URL . 'assets/css/slider-destaque-1.css',
            array('wte-sliders-base'),
            WTE_SLIDERS_VERSION,
            'all'
        );

        // CSS do slider destaque 2
        wp_enqueue_style(
            'wte-sliders-destaque-2',
            WTE_SLIDERS_PLUGIN_URL . 'assets/css/slider-destaque-2.css',
            array('wte-sliders-base'),
            WTE_SLIDERS_VERSION,
            'all'
        );

        // CSS do slider destinos em destaque
        wp_enqueue_style(
            'wte-sliders-featured-destinations',
            WTE_SLIDERS_PLUGIN_URL . 'assets/css/slider-featured-destinations.css',
            array('wte-sliders-base'),
            WTE_SLIDERS_VERSION,
            'all'
        );

        // GLightbox CSS para lightbox de galeria
        wp_enqueue_style(
            'glightbox',
            'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css',
            array(),
            '3.2.0',
            'all'
        );
    }

    /**
     * Enfileirar scripts JavaScript
     */
    public function enqueue_scripts()
    {
        // Swiper JS
        wp_enqueue_script(
            'wte-sliders-swiper',
            WTE_SLIDERS_PLUGIN_URL . 'assets/js/swiper-bundle.min.js',
            array(),
            '11.0.0',
            true
        );

        // Inicialização dos Sliders
        wp_enqueue_script(
            'wte-sliders-init',
            WTE_SLIDERS_PLUGIN_URL . 'assets/js/wte-sliders-init.js',
            array('wte-sliders-swiper'),
            WTE_SLIDERS_VERSION,
            true
        );

        // GLightbox JS para lightbox de galeria
        wp_enqueue_script(
            'glightbox',
            'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js',
            array(),
            '3.2.0',
            true
        );

        // Inicializar GLightbox
        wp_add_inline_script(
            'glightbox',
            "
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof GLightbox !== 'undefined') {
                    const lightbox = GLightbox({
                        selector: '.glightbox',
                        touchNavigation: true,
                        loop: true,
                        autoplayVideos: true,
                        closeButton: true,
                        closeOnOutsideClick: true
                    });
                }
            });
            "
        );
    }

    /**
     * Carregar arquivos de tradução
     */
    public function load_textdomain()
    {
        load_plugin_textdomain(
            'wte-sliders',
            false,
            dirname(WTE_SLIDERS_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Add custom rewrite rule for destination taxonomy base URL
     *
     * Permite acessar /destinations/ sem um termo específico
     */
    public function add_destination_base_rewrite()
    {
        // Redirect /destinations/ to a valid trip archive query with marker
        add_rewrite_rule(
            '^destinations/?$',
            'index.php?post_type=trip&wte_destination_base=1',
            'top'
        );
    }

    /**
     * Add custom query var to WordPress
     *
     * @param array $vars Existing query vars
     * @return array Modified query vars
     */
    public function add_destination_query_var($vars)
    {
        $vars[] = 'wte_destination_base';
        return $vars;
    }

    /**
     * Handle template loading for destination base URL
     *
     * Garante que /destinations/ seja tratado como archive ao invés de 404
     */
    public function handle_destination_base_template()
    {
        // Verificar se estamos na URL base de destinations
        if (!get_query_var('wte_destination_base')) {
            return;
        }

        // Forçar WordPress a reconhecer como archive válido
        global $wp_query;
        $wp_query->is_404 = false;
        $wp_query->is_archive = true;
        $wp_query->is_post_type_archive = true;

        // Definir status header como 200 OK
        status_header(200);
    }
}
