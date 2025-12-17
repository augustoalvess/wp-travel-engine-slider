<?php

/**
 * Classe para gerenciamento de templates de arquivo (archive) do WP Travel Engine
 *
 * @package WTE_Sliders
 */

// Prevenir acesso direto
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Classe WTE_Sliders_Archive
 */
class WTE_Sliders_Archive
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
        add_action('pre_get_posts', array($this, 'modify_archive_query'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_archive_assets'));
    }

    /**
     * Verificar se é página de arquivo do WP Travel Engine
     *
     * @return bool
     */
    private function is_wte_archive()
    {
        // Verificar se é arquivo do post type 'trip'
        if (is_post_type_archive('trip')) {
            return true;
        }

        // Verificar se é taxonomia do WP Travel Engine
        $wte_taxonomies = array('destination', 'trip_tag', 'trip_types', 'trip-packages-categories', 'difficulty');
        foreach ($wte_taxonomies as $taxonomy) {
            if (is_tax($taxonomy)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar se deve sobrescrever o template e retornar caminho customizado
     *
     * @param string $template Caminho do template original
     * @return string Caminho do template (original ou customizado)
     */
    public function maybe_override_template($template)
    {
        // Verificar se é página de arquivo WTE
        if (! $this->is_wte_archive()) {
            return $template;
        }

        // Verificar se o template customizado está ativado nas configurações
        $options = get_option('wte_sliders_options', array());
        if (empty($options['enable_archive_template'])) {
            return $template;
        }

        // Localizar template customizado
        $custom_template = $this->template_loader->locate_template('archive-trips');

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
     * Modificar query principal com filtros personalizados
     *
     * @param WP_Query $query Instância da query
     */
    public function modify_archive_query($query)
    {
        // Apenas modificar query principal em páginas de arquivo WTE
        if (!$query->is_main_query() || !$this->is_wte_archive()) {
            return;
        }

        // Tax query para filtros de Destino e Tipo de Viagem
        if (!empty($_GET['wte_destination']) || !empty($_GET['wte_trip_type'])) {
            $tax_query = array('relation' => 'AND');

            // Filtro de Destino
            if (!empty($_GET['wte_destination'])) {
                $destinations = array_map('intval', (array) $_GET['wte_destination']);
                $tax_query[] = array(
                    'taxonomy' => 'destination',
                    'field'    => 'term_id',
                    'terms'    => $destinations,
                    'operator' => 'IN',
                );
            }

            // Filtro de Tipo de Viagem
            if (!empty($_GET['wte_trip_type'])) {
                $trip_types = array_map('intval', (array) $_GET['wte_trip_type']);
                $tax_query[] = array(
                    'taxonomy' => 'trip_types',
                    'field'    => 'term_id',
                    'terms'    => $trip_types,
                    'operator' => 'IN',
                );
            }

            $query->set('tax_query', $tax_query);
        }

        // Meta query para filtros de Preço e Duração
        $meta_query = array('relation' => 'AND');
        $has_meta_filters = false;

        // Filtro de Duração (do slider)
        if (!empty($_GET['wte_duration_min']) && !empty($_GET['wte_duration_max'])) {
            $duration_min = intval($_GET['wte_duration_min']);
            $duration_max = intval($_GET['wte_duration_max']);

            // Adicionar callback para filtrar posts por duração
            add_filter('posts_where', function($where) use ($duration_min, $duration_max) {
                global $wpdb;

                // Buscar trips onde o meta 'wp_travel_engine_setting' contenha trip_duration entre min e max
                $where .= $wpdb->prepare(
                    " AND {$wpdb->posts}.ID IN (
                        SELECT post_id FROM {$wpdb->postmeta}
                        WHERE meta_key = 'wp_travel_engine_setting'
                        AND CAST(
                            SUBSTRING_INDEX(
                                SUBSTRING_INDEX(meta_value, '\"trip_duration\";s:', -1),
                                '\"',
                                2
                            ) AS UNSIGNED
                        ) BETWEEN %d AND %d
                    )",
                    $duration_min,
                    $duration_max
                );

                return $where;
            });

            $has_meta_filters = true;
        }

        // Filtro de Preço (do slider)
        if (!empty($_GET['wte_price_min']) && !empty($_GET['wte_price_max'])) {
            $price_min = intval($_GET['wte_price_min']);
            $price_max = intval($_GET['wte_price_max']);

            // Adicionar callback para filtrar posts por preço
            add_filter('posts_where', function($where) use ($price_min, $price_max) {
                global $wpdb;

                // Buscar trips onde o meta 'wp_travel_engine_setting' contenha price entre min e max
                $where .= $wpdb->prepare(
                    " AND {$wpdb->posts}.ID IN (
                        SELECT post_id FROM {$wpdb->postmeta}
                        WHERE meta_key = 'wp_travel_engine_setting'
                        AND (
                            CAST(
                                SUBSTRING_INDEX(
                                    SUBSTRING_INDEX(meta_value, '\"price\";s:', -1),
                                    '\"',
                                    2
                                ) AS UNSIGNED
                            ) BETWEEN %d AND %d
                            OR
                            CAST(
                                SUBSTRING_INDEX(
                                    SUBSTRING_INDEX(meta_value, '\"sale_price\";s:', -1),
                                    '\"',
                                    2
                                ) AS UNSIGNED
                            ) BETWEEN %d AND %d
                        )
                    )",
                    $price_min,
                    $price_max,
                    $price_min,
                    $price_max
                );

                return $where;
            });

            $has_meta_filters = true;
        }

        // Aplicar meta query se houver filtros
        if ($has_meta_filters) {
            $query->set('meta_query', $meta_query);
        }
    }

    /**
     * Enfileirar CSS e JS específicos para páginas de arquivo
     */
    public function enqueue_archive_assets()
    {
        // Apenas enfileirar em páginas de arquivo WTE
        if (! $this->is_wte_archive()) {
            return;
        }

        // Verificar se o template customizado está ativado
        $options = get_option('wte_sliders_options', array());
        if (empty($options['enable_archive_template'])) {
            return;
        }

        // ion.rangeSlider CSS
        wp_enqueue_style(
            'ion-rangeslider',
            'https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.1/css/ion.rangeSlider.min.css',
            array(),
            '2.3.1'
        );

        // CSS
        wp_enqueue_style(
            'wte-sliders-archive',
            WTE_SLIDERS_PLUGIN_URL . 'assets/css/archive-trips.css',
            array('wte-sliders-base', 'ion-rangeslider'),
            WTE_SLIDERS_VERSION,
            'all'
        );

        // ion.rangeSlider JS
        wp_enqueue_script(
            'ion-rangeslider',
            'https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.1/js/ion.rangeSlider.min.js',
            array('jquery'),
            '2.3.1',
            true
        );

        // JS
        wp_enqueue_script(
            'wte-sliders-archive-filters',
            WTE_SLIDERS_PLUGIN_URL . 'assets/js/archive-filters.js',
            array('jquery', 'ion-rangeslider'),
            WTE_SLIDERS_VERSION,
            true
        );
    }
}
