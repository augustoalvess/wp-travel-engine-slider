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
        add_action('wp_ajax_wte_filter_trips', array($this, 'ajax_filter_trips'));
        add_action('wp_ajax_nopriv_wte_filter_trips', array($this, 'ajax_filter_trips'));
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

        // Pesquisa por texto
        if (!empty($_GET['wte_search'])) {
            $search_term = sanitize_text_field($_GET['wte_search']);
            $query->set('s', $search_term);
        }

        // Ordenação
        if (!empty($_GET['wte_orderby'])) {
            $orderby = sanitize_text_field($_GET['wte_orderby']);

            switch ($orderby) {
                case 'title':
                    $query->set('orderby', 'title');
                    $query->set('order', 'ASC');
                    break;

                case 'price_low':
                case 'price_high':
                    // Ordenação por preço requer lógica customizada
                    // Implementar se necessário no futuro
                    break;

                case 'date':
                default:
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
            }
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

        // Filtros de Preço e Duração usando post__in
        $filtered_post_ids = array();
        $has_price_filter = !empty($_GET['wte_price_min']) && !empty($_GET['wte_price_max']);
        $has_duration_filter = !empty($_GET['wte_duration_min']) && !empty($_GET['wte_duration_max']);

        if ($has_price_filter || $has_duration_filter) {
            // Buscar todos os posts do tipo 'trip'
            $all_trips = get_posts(array(
                'post_type' => 'trip',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
            ));

            foreach ($all_trips as $trip_id) {
                $settings = get_post_meta($trip_id, 'wp_travel_engine_setting', true);

                if (!is_array($settings)) {
                    continue;
                }

                $passes_filters = true;

                // Filtro de Duração
                if ($has_duration_filter) {
                    $duration_min = intval($_GET['wte_duration_min']);
                    $duration_max = intval($_GET['wte_duration_max']);
                    $trip_duration = isset($settings['trip_duration']) ? intval($settings['trip_duration']) : 0;

                    if ($trip_duration < $duration_min || $trip_duration > $duration_max) {
                        $passes_filters = false;
                    }
                }

                // Filtro de Preço
                if ($has_price_filter && $passes_filters) {
                    $price_min = intval($_GET['wte_price_min']);
                    $price_max = intval($_GET['wte_price_max']);

                    // Verificar sale_price primeiro, depois price regular
                    $trip_price = 0;
                    if (isset($settings['sale_price']) && !empty($settings['sale_price'])) {
                        $trip_price = intval($settings['sale_price']);
                    } elseif (isset($settings['price']) && !empty($settings['price'])) {
                        $trip_price = intval($settings['price']);
                    }

                    if ($trip_price < $price_min || $trip_price > $price_max) {
                        $passes_filters = false;
                    }
                }

                if ($passes_filters) {
                    $filtered_post_ids[] = $trip_id;
                }
            }

            // Se não houver posts que passam pelos filtros, definir um ID impossível
            if (empty($filtered_post_ids)) {
                $filtered_post_ids = array(0);
            }

            $query->set('post__in', $filtered_post_ids);
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

        // Localizar script com dados para AJAX
        wp_localize_script(
            'wte-sliders-archive-filters',
            'wteFiltersAjax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wte_filter_trips_nonce'),
            )
        );
    }

    /**
     * Handler AJAX para filtrar viagens
     */
    public function ajax_filter_trips()
    {
        // Verificar nonce
        check_ajax_referer('wte_filter_trips_nonce', 'nonce');

        // Construir argumentos da query
        $args = array(
            'post_type' => 'trip',
            'posts_per_page' => get_option('posts_per_page', 10),
            'post_status' => 'publish',
            'paged' => isset($_POST['paged']) ? intval($_POST['paged']) : 1,
        );

        // Pesquisa por texto
        if (!empty($_POST['wte_search'])) {
            $search_term = sanitize_text_field($_POST['wte_search']);
            $args['s'] = $search_term;
        }

        // Ordenação
        if (!empty($_POST['wte_orderby'])) {
            $orderby = sanitize_text_field($_POST['wte_orderby']);

            switch ($orderby) {
                case 'title':
                    $args['orderby'] = 'title';
                    $args['order'] = 'ASC';
                    break;

                case 'date':
                default:
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
                    break;
            }
        }

        // Tax query para filtros de Destino e Tipo de Viagem
        $tax_query = array('relation' => 'AND');

        // Filtro de Destino
        if (!empty($_POST['wte_destination'])) {
            $destinations = array_map('intval', (array) $_POST['wte_destination']);
            $tax_query[] = array(
                'taxonomy' => 'destination',
                'field' => 'term_id',
                'terms' => $destinations,
                'operator' => 'IN',
            );
        }

        // Filtro de Tipo de Viagem
        if (!empty($_POST['wte_trip_type'])) {
            $trip_types = array_map('intval', (array) $_POST['wte_trip_type']);
            $tax_query[] = array(
                'taxonomy' => 'trip_types',
                'field' => 'term_id',
                'terms' => $trip_types,
                'operator' => 'IN',
            );
        }

        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }

        // Filtros de Preço e Duração
        $filtered_post_ids = array();
        $has_price_filter = !empty($_POST['wte_price_min']) && !empty($_POST['wte_price_max']);
        $has_duration_filter = !empty($_POST['wte_duration_min']) && !empty($_POST['wte_duration_max']);

        if ($has_price_filter || $has_duration_filter) {
            // Buscar todos os posts do tipo 'trip'
            $all_trips = get_posts(array(
                'post_type' => 'trip',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
            ));

            foreach ($all_trips as $trip_id) {
                $settings = get_post_meta($trip_id, 'wp_travel_engine_setting', true);

                if (!is_array($settings)) {
                    continue;
                }

                $passes_filters = true;

                // Filtro de Duração
                if ($has_duration_filter) {
                    $duration_min = intval($_POST['wte_duration_min']);
                    $duration_max = intval($_POST['wte_duration_max']);
                    $trip_duration = isset($settings['trip_duration']) ? intval($settings['trip_duration']) : 0;

                    if ($trip_duration < $duration_min || $trip_duration > $duration_max) {
                        $passes_filters = false;
                    }
                }

                // Filtro de Preço
                if ($has_price_filter && $passes_filters) {
                    $price_min = intval($_POST['wte_price_min']);
                    $price_max = intval($_POST['wte_price_max']);

                    $trip_price = 0;
                    if (isset($settings['sale_price']) && !empty($settings['sale_price'])) {
                        $trip_price = intval($settings['sale_price']);
                    } elseif (isset($settings['price']) && !empty($settings['price'])) {
                        $trip_price = intval($settings['price']);
                    }

                    if ($trip_price < $price_min || $trip_price > $price_max) {
                        $passes_filters = false;
                    }
                }

                if ($passes_filters) {
                    $filtered_post_ids[] = $trip_id;
                }
            }

            if (empty($filtered_post_ids)) {
                $filtered_post_ids = array(0);
            }

            $args['post__in'] = $filtered_post_ids;
        }

        // Executar query
        $query = new WP_Query($args);

        // Preparar resposta
        $response = array(
            'success' => true,
            'html' => '',
            'found_posts' => $query->found_posts,
            'max_pages' => $query->max_num_pages,
        );

        // Renderizar HTML dos resultados
        ob_start();

        if ($query->have_posts()) {
            echo '<div class="wte-trips-grid">';
            while ($query->have_posts()) {
                $query->the_post();

                // Construir dados da viagem
                $trip_data = $this->query->get_trip_data_from_id(get_the_ID());

                // Renderizar card de viagem
                $this->template_loader->get_template_part('partials/trip-card-small', $trip_data);
            }
            echo '</div>';

            // Paginação
            if ($query->max_num_pages > 1) {
                echo '<div class="wte-archive-pagination">';
                echo paginate_links(array(
                    'total' => $query->max_num_pages,
                    'current' => $args['paged'],
                    'format' => '?paged=%#%',
                    'type' => 'list',
                ));
                echo '</div>';
            }
        } else {
            $this->template_loader->get_template_part('partials/archive/empty-state');
        }

        wp_reset_postdata();

        $response['html'] = ob_get_clean();

        wp_send_json($response);
    }
}
