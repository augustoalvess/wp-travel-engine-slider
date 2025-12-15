<?php

/**
 * Classe para queries de viagens do WP Travel Engine
 *
 * @package WTE_Sliders
 */

// Prevenir acesso direto
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Classe WTE_Sliders_Query
 */
class WTE_Sliders_Query
{

    /**
     * Verificar se um termo existe em uma taxonomia
     *
     * @param string $term_slug Slug do termo
     * @param string $taxonomy  Nome da taxonomia
     * @return bool
     */
    private function term_exists($term_slug, $taxonomy)
    {
        $term = get_term_by('slug', $term_slug, $taxonomy);
        return ($term !== false && ! is_wp_error($term));
    }

    /**
     * Executar query e construir array de dados das viagens
     *
     * @param array $args Argumentos do WP_Query
     * @return array Array de objetos com dados das viagens
     */
    private function execute_query_and_build_data($args)
    {
        $query = new WP_Query($args);
        $trips = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $trip_id = get_the_ID();

                $trip_data = array(
                    'id'          => $trip_id,
                    'title'       => get_the_title(),
                    'excerpt'     => get_the_excerpt(),
                    'permalink'   => get_permalink(),
                    'image'       => $this->get_trip_image($trip_id),
                    'video'       => $this->get_trip_video($trip_id),
                    'duration'    => $this->get_trip_duration($trip_id),
                    'destination' => $this->get_trip_destination($trip_id),
                    'price'       => $this->get_trip_price($trip_id),
                    'has_promo'   => $this->has_promotion($trip_id),
                );

                $trips[] = (object) $trip_data;
            }
            wp_reset_postdata();
        }

        return $trips;
    }

    /**
     * Buscar viagens por uma ou múltiplas tags
     *
     * @param string|array $terms    Tags separadas por vírgula ou array
     * @param int          $limit    Limite de posts (-1 para todos)
     * @param string       $taxonomy Taxonomia a usar
     * @return array Array de objetos com dados das viagens
     */
    public function get_trips_by_tags($terms, $limit = -1, $taxonomy = 'trip_tag')
    {
        // Validar taxonomia
        $valid_taxonomies = array('trip_tag', 'trip-packages-categories', 'difficulty');
        if (! in_array($taxonomy, $valid_taxonomies, true)) {
            $taxonomy = 'trip_tag';
        }

        // Converter para array se for string
        if (is_string($terms)) {
            $terms = array_map('trim', explode(',', $terms));
        }

        // Sanitizar
        $terms = array_map('sanitize_text_field', $terms);
        $terms = array_filter($terms);

        if (empty($terms)) {
            return array();
        }

        // Verificar se pelo menos um termo existe
        $valid_terms = array();
        foreach ($terms as $term_slug) {
            if ($this->term_exists($term_slug, $taxonomy)) {
                $valid_terms[] = $term_slug;
            }
        }

        if (empty($valid_terms)) {
            return array();
        }

        // Usar 'IN' se múltiplos termos, senão termo único
        $operator = (count($valid_terms) > 1) ? 'IN' : 'AND';

        $args = array(
            'post_type'      => 'trip',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $valid_terms,
                    'operator' => $operator,
                ),
            ),
        );

        return $this->execute_query_and_build_data($args);
    }

    /**
     * Buscar viagens por taxonomia (método legado - mantido para retrocompatibilidade)
     *
     * @param string $term_slug Slug do termo para filtrar
     * @param int    $limit     Limite de posts (-1 para todos)
     * @param string $taxonomy  Taxonomia a usar (trip_tag, trip-packages-categories, difficulty)
     * @return array Array de objetos WP_Post com dados adicionais
     */
    public function get_trips_by_tag($term_slug, $limit = -1, $taxonomy = 'trip_tag')
    {
        return $this->get_trips_by_tags($term_slug, $limit, $taxonomy);
    }

    /**
     * Buscar viagens por IDs específicos
     *
     * @param string|array $ids    IDs separados por vírgula ou array
     * @param int          $limit  Limite de posts (-1 para todos)
     * @return array Array de objetos com dados das viagens
     */
    public function get_trips_by_ids($ids, $limit = -1)
    {
        // Converter para array se for string
        if (is_string($ids)) {
            $ids = array_map('trim', explode(',', $ids));
        }

        // Sanitizar IDs
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids); // Remover zeros

        if (empty($ids)) {
            return array();
        }

        // Aplicar limite se especificado
        if ($limit > 0 && count($ids) > $limit) {
            $ids = array_slice($ids, 0, $limit);
        }

        $args = array(
            'post_type'      => 'trip',
            'post__in'       => $ids,
            'orderby'        => 'post__in', // Manter ordem dos IDs
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        return $this->execute_query_and_build_data($args);
    }

    /**
     * Obter imagem destaque da viagem
     *
     * @param int $trip_id ID da viagem
     * @return string URL da imagem ou string vazia
     */
    private function get_trip_image($trip_id)
    {
        $image_url = get_the_post_thumbnail_url($trip_id, 'large');
        return $image_url ? $image_url : '';
    }

    /**
     * Obter vídeo da galeria da viagem
     *
     * @param int $trip_id ID da viagem
     * @return string URL do vídeo (YouTube/Vimeo) ou string vazia
     */
    private function get_trip_video($trip_id)
    {
        // Tentar obter da galeria de vídeos do WP Travel Engine
        $gallery = get_post_meta($trip_id, 'wpte_vid_gallery', true);

        if (is_array($gallery) && ! empty($gallery)) {
            // Estrutura: array( 0 => array( 'id' => '...', 'type' => 'youtube', 'thumb' => '...' ) )
            foreach ($gallery as $video_data) {
                if (isset($video_data['id']) && isset($video_data['type'])) {
                    // Construir URL baseado no tipo
                    if ($video_data['type'] === 'youtube') {
                        return 'https://www.youtube.com/watch?v=' . $video_data['id'];
                    } elseif ($video_data['type'] === 'vimeo') {
                        return 'https://vimeo.com/' . $video_data['id'];
                    }
                }
            }
        }

        // Fallback: tentar campo customizado comum
        $video_url = get_post_meta($trip_id, 'trip_video_url', true);
        return $this->is_video_url($video_url) ? $video_url : '';
    }

    /**
     * Verificar se é URL de vídeo válida
     *
     * @param string $url URL para verificar
     * @return bool
     */
    private function is_video_url($url)
    {
        if (empty($url)) {
            return false;
        }
        return (strpos($url, 'youtube.com') !== false ||
            strpos($url, 'youtu.be') !== false ||
            strpos($url, 'vimeo.com') !== false);
    }

    /**
     * Obter duração da viagem formatada
     *
     * @param int $trip_id ID da viagem
     * @return string Duração formatada (ex: "5 dias")
     */
    private function get_trip_duration($trip_id)
    {
        $settings = get_post_meta($trip_id, 'wp_travel_engine_setting', true);

        if (! is_array($settings)) {
            return '';
        }

        $duration = isset($settings['trip_duration']) ? $settings['trip_duration'] : '';
        $unit = isset($settings['trip_duration_unit']) ? $settings['trip_duration_unit'] : 'days';

        if (empty($duration)) {
            return '';
        }

        // Traduzir unidade
        $unit_labels = array(
            'days'   => 'dias',
            'day'    => 'dia',
            'nights' => 'noites',
            'night'  => 'noite',
            'hours'  => 'horas',
            'hour'   => 'hora',
        );

        $unit_text = isset($unit_labels[$unit]) ? $unit_labels[$unit] : $unit;

        return $duration . ' ' . $unit_text;
    }

    /**
     * Obter destino da viagem
     *
     * @param int $trip_id ID da viagem
     * @return string Nome do destino ou string vazia
     */
    private function get_trip_destination($trip_id)
    {
        $destinations = get_the_terms($trip_id, 'destination');

        if (is_array($destinations) && ! empty($destinations)) {
            $destination = array_shift($destinations);
            return $destination->name;
        }

        return '';
    }

    /**
     * Obter informações de preço da viagem
     *
     * @param int $trip_id ID da viagem
     * @return array Array com estrutura de preços (adult, child, fallback)
     */
    private function get_trip_price($trip_id)
    {
        // Tentar obter package primário
        $primary_package_id = get_post_meta($trip_id, 'primary_package', true);

        if (!empty($primary_package_id) && is_numeric($primary_package_id)) {
            // Obter categorias do package
            $package_categories = get_post_meta($primary_package_id, 'package-categories', true);

            if (is_array($package_categories) && !empty($package_categories['c_ids'])) {
                return $this->extract_package_pricing($package_categories);
            }
        }

        // Fallback para meta keys simples
        return $this->extract_simple_pricing($trip_id);
    }

    /**
     * Extrair dados de preço do array package-categories
     *
     * @param array $package_categories Array de categorias do pacote
     * @return array Estrutura de preços formatada
     */
    private function extract_package_pricing($package_categories)
    {
        $pricing = array(
            'has_child' => false,
            'adult'     => array(),
            'child'     => array(),
            // Compatibilidade reversa
            'regular'   => 0,
            'sale'      => 0,
            'current'   => 0,
            'formatted' => '',
        );

        // Validar estrutura
        if (!isset($package_categories['labels']) || !isset($package_categories['prices'])) {
            return $this->get_empty_price_structure();
        }

        $labels = $package_categories['labels'];
        $prices = $package_categories['prices'];
        $sale_prices = isset($package_categories['sale_prices']) ? $package_categories['sale_prices'] : array();
        $enabled_sale = isset($package_categories['enabled_sale']) ? $package_categories['enabled_sale'] : array();

        // Encontrar índices de adulto e criança
        $adult_index = null;
        $child_index = null;

        foreach ($labels as $index => $label) {
            $label_lower = strtolower(trim($label));
            if ($label_lower === 'adult' || $label_lower === 'adulto') {
                $adult_index = $index;
            } elseif ($label_lower === 'child' || $label_lower === 'criança' || $label_lower === 'crianca') {
                $child_index = $index;
            }
        }

        // Extrair preços de adulto
        if ($adult_index !== null && isset($prices[$adult_index])) {
            $adult_regular = floatval($prices[$adult_index]);
            $adult_sale = 0;

            if (isset($enabled_sale[$adult_index]) && $enabled_sale[$adult_index] == '1'
                && isset($sale_prices[$adult_index])) {
                $adult_sale = floatval($sale_prices[$adult_index]);
            }

            $adult_current = ($adult_sale > 0 && $adult_sale < $adult_regular) ? $adult_sale : $adult_regular;

            $pricing['adult'] = array(
                'regular'           => $adult_regular,
                'sale'              => $adult_sale,
                'current'           => $adult_current,
                'has_sale'          => ($adult_sale > 0 && $adult_sale < $adult_regular),
                'formatted'         => 'R$ ' . number_format($adult_current, 2, ',', '.'),
                'formatted_regular' => 'R$ ' . number_format($adult_regular, 2, ',', '.'),
            );

            // Definir campos de compatibilidade reversa com preços de adulto
            $pricing['regular'] = $adult_regular;
            $pricing['sale'] = $adult_sale;
            $pricing['current'] = $adult_current;
            $pricing['formatted'] = $pricing['adult']['formatted'];
        }

        // Extrair preços de criança
        if ($child_index !== null && isset($prices[$child_index])) {
            $child_regular = floatval($prices[$child_index]);
            $child_sale = 0;

            if (isset($enabled_sale[$child_index]) && $enabled_sale[$child_index] == '1'
                && isset($sale_prices[$child_index])) {
                $child_sale = floatval($sale_prices[$child_index]);
            }

            $child_current = ($child_sale > 0 && $child_sale < $child_regular) ? $child_sale : $child_regular;

            $pricing['child'] = array(
                'regular'           => $child_regular,
                'sale'              => $child_sale,
                'current'           => $child_current,
                'has_sale'          => ($child_sale > 0 && $child_sale < $child_regular),
                'formatted'         => 'R$ ' . number_format($child_current, 2, ',', '.'),
                'formatted_regular' => 'R$ ' . number_format($child_regular, 2, ',', '.'),
            );

            $pricing['has_child'] = ($child_current > 0);
        }

        return $pricing;
    }

    /**
     * Extrair dados de preço de meta keys simples (fallback)
     *
     * @param int $trip_id ID da viagem
     * @return array Estrutura de preços formatada
     */
    private function extract_simple_pricing($trip_id)
    {
        $regular_price = floatval(get_post_meta($trip_id, 'wp_travel_engine_setting_trip_actual_price', true));
        $current_price = floatval(get_post_meta($trip_id, 'wp_travel_engine_setting_trip_price', true));

        // Se preço atual é 0, usar preço regular
        if ($current_price == 0) {
            $current_price = $regular_price;
        }

        // Determinar se está em promoção
        $has_sale = ($regular_price > 0 && $current_price > 0 && $current_price < $regular_price);
        $sale_price = $has_sale ? $current_price : 0;

        return array(
            'has_child' => false,
            'adult'     => array(
                'regular'           => $regular_price,
                'sale'              => $sale_price,
                'current'           => $current_price,
                'has_sale'          => $has_sale,
                'formatted'         => 'R$ ' . number_format($current_price, 2, ',', '.'),
                'formatted_regular' => 'R$ ' . number_format($regular_price, 2, ',', '.'),
            ),
            'child'     => array(),
            // Compatibilidade reversa
            'regular'   => $regular_price,
            'sale'      => $sale_price,
            'current'   => $current_price,
            'formatted' => 'R$ ' . number_format($current_price, 2, ',', '.'),
        );
    }

    /**
     * Obter estrutura de preço vazia
     *
     * @return array Estrutura vazia de preços
     */
    private function get_empty_price_structure()
    {
        return array(
            'has_child' => false,
            'adult'     => array(
                'regular'           => 0,
                'sale'              => 0,
                'current'           => 0,
                'has_sale'          => false,
                'formatted'         => '',
                'formatted_regular' => '',
            ),
            'child'     => array(),
            'regular'   => 0,
            'sale'      => 0,
            'current'   => 0,
            'formatted' => '',
        );
    }

    /**
     * Verificar se a viagem tem promoção
     *
     * @param int $trip_id ID da viagem
     * @return bool
     */
    private function has_promotion($trip_id)
    {
        $price = $this->get_trip_price($trip_id);

        // Verificar flag has_sale do adulto
        if (!empty($price['adult']) && isset($price['adult']['has_sale'])) {
            return $price['adult']['has_sale'];
        }

        // Fallback compatibilidade
        return ($price['sale'] > 0 && $price['sale'] < $price['regular']);
    }

    /**
     * Converter URL de vídeo para embed
     *
     * @param string $video_url URL do vídeo
     * @return string URL embed ou string vazia
     */
    public function get_video_embed_url($video_url)
    {
        if (empty($video_url)) {
            return '';
        }

        // YouTube
        if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $matches);
            if (isset($matches[1])) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        // Vimeo
        if (strpos($video_url, 'vimeo.com') !== false) {
            preg_match('/vimeo\.com\/([0-9]+)/', $video_url, $matches);
            if (isset($matches[1])) {
                return 'https://player.vimeo.com/video/' . $matches[1];
            }
        }

        return '';
    }

    /**
     * Obter destinos em destaque
     *
     * @param int $limit Limite de destinos (-1 para todos)
     * @return array Array de objetos com dados dos destinos
     */
    public function get_featured_destinations($limit = -1)
    {
        $args = array(
            'taxonomy'   => 'destination',
            'hide_empty' => false, // Exibir mesmo sem viagens
            'meta_key'   => 'wte_trip_tax_featured',
            'meta_value' => 'yes',
            'number'     => $limit > 0 ? $limit : 0,
        );

        $terms = get_terms($args);
        $destinations = array();

        if (! empty($terms) && ! is_wp_error($terms)) {
            foreach ($terms as $term) {
                $image_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                $image_url = '';

                if (! empty($image_id)) {
                    $image_url = wp_get_attachment_image_url($image_id, 'large');
                }

                $destinations[] = (object) array(
                    'id'          => $term->term_id,
                    'title'       => $term->name,
                    'description' => get_term_meta($term->term_id, 'wte-shortdesc-textarea', true),
                    'permalink'   => get_term_link($term),
                    'image'       => $image_url,
                    'count'       => $term->count,
                    'slug'        => $term->slug,
                );
            }
        }

        return $destinations;
    }
}
