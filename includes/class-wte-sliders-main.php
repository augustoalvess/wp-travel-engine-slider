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
        add_filter('query_vars', array($this, 'add_destination_query_var'));
        add_filter('request', array($this, 'handle_destinations_base_request'));
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
     * Handle /destinations/ base URL via request filter
     *
     * Funciona com todas as estruturas de permalinks, incluindo /index.php/
     *
     * @param array $query_vars Query vars from WordPress
     * @return array Modified query vars
     */
    public function handle_destinations_base_request($query_vars)
    {
        // Get the request URI and remove leading/trailing slashes
        $request_uri = trim($_SERVER['REQUEST_URI'], '/');

        // Remove WordPress subdirectory if exists (e.g., 'wordpress')
        $script_name = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if (!empty($script_name)) {
            $request_uri = str_replace($script_name, '', $request_uri);
            $request_uri = trim($request_uri, '/');
        }

        // Remove index.php if present
        $request_uri = str_replace('index.php/', '', $request_uri);
        $request_uri = str_replace('index.php', '', $request_uri);
        $request_uri = trim($request_uri, '/');

        // Check if this is exactly the /destinations/ base URL
        if ($request_uri === 'destinations' || $request_uri === 'destinations/') {
            // Modify query to be a valid trip archive with our marker
            $query_vars = array(
                'post_type' => 'trip',
                'wte_destination_base' => '1',
            );
        }

        return $query_vars;
    }
}
