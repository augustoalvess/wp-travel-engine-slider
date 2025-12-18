<?php

/**
 * Handler para o template de arquivo de destinos
 *
 * Gerencia a exibição customizada da página de arquivo de destinos,
 * substituindo o template padrão do WP Travel Engine com um layout
 * em cards com imagens, preços calculados dinamicamente e ordenação A-Z/Z-A.
 *
 * @package WTE_Sliders
 */

// Prevenir acesso direto
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Classe WTE_Sliders_Destination_Archive
 */
class WTE_Sliders_Destination_Archive
{

    /**
     * Instância da classe de queries
     *
     * @var WTE_Sliders_Query
     */
    private $query;

    /**
     * Instância do template loader
     *
     * @var WTE_Sliders_Template_Loader
     */
    private $template_loader;

    /**
     * Construtor
     *
     * @param WTE_Sliders_Query          $query           Instância da classe de queries
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
        // Hook para substituir template (prioridade muito alta para sobrescrever WP Travel Engine)
        add_filter('template_include', array($this, 'maybe_override_destination_template'), 9999);

        // Hook específico para páginas (prioridade muito alta)
        add_filter('page_template', array($this, 'maybe_override_destination_page_template'), 9999);

        // Hook para enfileirar assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_destination_archive_assets'));
    }

    /**
     * Verificar se está na página de arquivo de destinos
     *
     * @return bool
     */
    private function is_destination_archive()
    {
        // APENAS verificar se é a página de destinos do WP Travel Engine
        // NÃO aplicar em taxonomy archives (ex: /destinations/rio-grande-do-sul/)
        if (is_page('destination') || is_page(331786)) {
            return true;
        }

        // Verificar se a query var está definida
        global $wp_query;
        if (isset($wp_query->query_vars['pagename']) && $wp_query->query_vars['pagename'] === 'destination') {
            return true;
        }

        return false;
    }

    /**
     * Substituir template de página se for a página de destinos
     *
     * @param string $template Caminho do template atual
     * @return string Caminho do template a usar
     */
    public function maybe_override_destination_page_template($template)
    {
        // Verificar se é a página de destinos
        if (! is_page('destination') && ! is_page(331786)) {
            return $template;
        }

        // Verificar se o template customizado está habilitado nas configurações
        $options = get_option('wte_sliders_options', array());
        if (empty($options['enable_destination_archive_template'])) {
            return $template; // Retornar template padrão se desabilitado
        }

        // Definir variáveis globais para uso no template
        global $wte_sliders_destination_archive, $wte_sliders_template_loader;
        $wte_sliders_destination_archive = $this;
        $wte_sliders_template_loader = $this->template_loader;

        // Localizar template customizado (sem .php, o locate_template adiciona automaticamente)
        $custom_template = $this->template_loader->locate_template('archive-destinations');

        if ($custom_template) {
            return $custom_template;
        }

        return $template;
    }

    /**
     * Substituir template se estiver no arquivo de destinos
     *
     * @param string $template Caminho do template atual
     * @return string Caminho do template a usar
     */
    public function maybe_override_destination_template($template)
    {
        if (! $this->is_destination_archive()) {
            return $template;
        }

        // Verificar se o template customizado está habilitado nas configurações
        $options = get_option('wte_sliders_options', array());
        if (empty($options['enable_destination_archive_template'])) {
            return $template; // Retornar template padrão se desabilitado
        }

        // Definir variáveis globais para uso no template
        global $wte_sliders_destination_archive, $wte_sliders_template_loader;
        $wte_sliders_destination_archive = $this;
        $wte_sliders_template_loader = $this->template_loader;

        // Localizar template customizado (sem .php, o locate_template adiciona automaticamente)
        $custom_template = $this->template_loader->locate_template('archive-destinations');

        if ($custom_template) {
            return $custom_template;
        }

        return $template;
    }

    /**
     * Buscar todos os destinos com preços mínimos calculados
     *
     * Implementa query otimizada em batch para evitar problema N+1.
     * Faz apenas 2 queries principais (destinos + todas as trips) em vez de
     * 1 query por destino.
     *
     * @param string $order Ordem de classificação: 'ASC' ou 'DESC'
     * @return array Array de objetos com dados dos destinos
     */
    public function get_destinations_with_prices($order = 'ASC')
    {
        // Validar ordem
        $order = strtoupper($order);
        if (! in_array($order, array('ASC', 'DESC'), true)) {
            $order = 'ASC';
        }

        // 1. Buscar todos os termos de destino
        $terms = get_terms(array(
            'taxonomy'   => 'destination',
            'hide_empty' => false, // Mostrar todos os destinos
            'orderby'    => 'name',
            'order'      => $order,
        ));

        if (is_wp_error($terms) || empty($terms)) {
            return array();
        }

        // 2. Coletar IDs dos termos para batch query
        $term_ids = wp_list_pluck($terms, 'term_id');

        // 3. Buscar TODAS as trips associadas aos destinos de uma vez (batch query)
        $trips = get_posts(array(
            'post_type'      => 'trip',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'destination',
                    'field'    => 'term_id',
                    'terms'    => $term_ids,
                ),
            ),
        ));

        // 4. Construir lookup array: destination_id => [preços]
        $destination_prices = array();

        foreach ($trips as $trip) {
            // Obter destinos desta trip
            $trip_destinations = wp_get_post_terms($trip->ID, 'destination', array('fields' => 'ids'));

            if (is_wp_error($trip_destinations)) {
                continue;
            }

            // Obter preço da trip
            $price_data = $this->query->get_trip_price($trip->ID);

            // Se não houver preço válido, pular
            if (empty($price_data['adult']['current']) || $price_data['adult']['current'] <= 0) {
                continue;
            }

            $current_price = floatval($price_data['adult']['current']);

            // Adicionar preço para cada destino desta trip
            foreach ($trip_destinations as $dest_id) {
                if (! isset($destination_prices[$dest_id])) {
                    $destination_prices[$dest_id] = array();
                }
                $destination_prices[$dest_id][] = $current_price;
            }
        }

        // 5. Montar objetos finais com dados completos
        $result = array();

        foreach ($terms as $term) {
            // Calcular preço mínimo
            $min_price = 0;
            if (isset($destination_prices[$term->term_id]) && ! empty($destination_prices[$term->term_id])) {
                $min_price = min($destination_prices[$term->term_id]);
            }

            // Obter imagem do destino (WP Travel Engine usa 'category-image-id')
            $image_id = get_term_meta($term->term_id, 'category-image-id', true);
            $image_url = '';
            if (! empty($image_id)) {
                $image_url = wp_get_attachment_image_url($image_id, 'large');
            }

            // Obter descrição
            $description = get_term_meta($term->term_id, 'wte-shortdesc-textarea', true);

            // Formatar preço
            $formatted_price = '';
            if ($min_price > 0) {
                $formatted_price = 'R$ ' . number_format($min_price, 2, ',', '.');
            }

            // Montar objeto do destino
            $result[] = (object) array(
                'id'              => $term->term_id,
                'name'            => $term->name,
                'slug'            => $term->slug,
                'description'     => $description,
                'image'           => $image_url,
                'permalink'       => get_term_link($term),
                'count'           => $term->count,
                'min_price'       => $min_price,
                'formatted_price' => $formatted_price,
            );
        }

        return $result;
    }

    /**
     * Enfileirar assets CSS e JavaScript para página de destinos
     */
    public function enqueue_destination_archive_assets()
    {
        // Só enfileirar se estiver na página de destinos
        if (! $this->is_destination_archive()) {
            return;
        }

        // Verificar se o template customizado está habilitado
        $options = get_option('wte_sliders_options', array());
        if (empty($options['enable_destination_archive_template'])) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'wte-sliders-archive-destinations',
            WTE_SLIDERS_PLUGIN_URL . 'assets/css/archive-destinations.css',
            array('wte-sliders-base'),
            WTE_SLIDERS_VERSION,
            'all'
        );

        // JavaScript
        wp_enqueue_script(
            'wte-sliders-archive-destinations',
            WTE_SLIDERS_PLUGIN_URL . 'assets/js/archive-destinations.js',
            array('jquery'),
            WTE_SLIDERS_VERSION,
            true
        );
    }
}
