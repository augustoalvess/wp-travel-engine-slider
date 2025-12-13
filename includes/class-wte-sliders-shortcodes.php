<?php
/**
 * Classe para registro e renderização dos shortcodes
 *
 * @package WTE_Sliders
 */

// Prevenir acesso direto
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe WTE_Sliders_Shortcodes
 */
class WTE_Sliders_Shortcodes {

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
     * @param WTE_Sliders_Query           $query           Instância de query
     * @param WTE_Sliders_Template_Loader $template_loader Instância de template loader
     */
    public function __construct( $query, $template_loader ) {
        $this->query = $query;
        $this->template_loader = $template_loader;
        $this->register_shortcodes();
    }

    /**
     * Registrar shortcodes
     */
    private function register_shortcodes() {
        add_shortcode( 'wte_slider', array( $this, 'render_slider' ) );
    }

    /**
     * Renderizar shortcode unificado
     *
     * @param array $atts Atributos do shortcode
     * @return string HTML do slider
     */
    public function render_slider( $atts ) {
        $atts = shortcode_atts(
            array(
                'type'      => '1',
                'tags'      => '',
                'ids'       => '',
                'limit'     => -1,
                'autoplay'  => '',
                'speed'     => 5000,
                'arrows'    => 'true',
                'per_page'  => 3,
                'taxonomy'  => 'trip_tag',
            ),
            $atts,
            'wte_slider'
        );

        // Sanitizar e validar type
        $type = in_array( $atts['type'], array( '1', '2' ), true ) ? $atts['type'] : '1';

        // Determinar autoplay baseado no tipo se não especificado
        if ( empty( $atts['autoplay'] ) || $atts['autoplay'] === 'auto' ) {
            $autoplay = ( $type === '1' ) ? 'true' : 'false';
        } else {
            $autoplay = $atts['autoplay'];
        }

        // Sanitizar outros atributos
        $ids = sanitize_text_field( $atts['ids'] );
        $tags = sanitize_text_field( $atts['tags'] );
        $limit = intval( $atts['limit'] );
        $autoplay_bool = filter_var( $autoplay, FILTER_VALIDATE_BOOLEAN );
        $speed = max( 1000, intval( $atts['speed'] ) );
        $arrows = filter_var( $atts['arrows'], FILTER_VALIDATE_BOOLEAN );
        $per_page = max( 1, min( 12, intval( $atts['per_page'] ) ) );
        $taxonomy = sanitize_key( $atts['taxonomy'] );

        // Buscar viagens: IDs tem prioridade sobre tags
        if ( ! empty( $ids ) ) {
            $trips = $this->query->get_trips_by_ids( $ids, $limit );
        } elseif ( ! empty( $tags ) ) {
            $trips = $this->query->get_trips_by_tags( $tags, $limit, $taxonomy );
        } else {
            return $this->render_error_message(
                __( 'Erro no shortcode: especifique "tags" ou "ids".', 'wte-sliders' )
            );
        }

        if ( empty( $trips ) ) {
            return $this->render_empty_message( $tags, $ids, $taxonomy );
        }

        // Preparar dados
        $data = array(
            'trips'           => $trips,
            'autoplay'        => $autoplay_bool,
            'speed'           => $speed,
            'arrows'          => $arrows,
            'slider_id'       => 'wte-slider-' . uniqid(),
            'query'           => $this->query,
            'template_loader' => $this->template_loader,
        );

        // Adicionar per_page apenas para tipo 2
        if ( $type === '2' ) {
            $data['per_page'] = $per_page;
        }

        // Renderizar template apropriado
        return $this->template_loader->load_template( 'slider-destaque-' . $type, $data );
    }

    /**
     * Renderizar mensagem de erro
     *
     * @param string $message Mensagem de erro
     * @return string HTML da mensagem de erro
     */
    private function render_error_message( $message ) {
        return sprintf(
            '<div class="wte-sliders-error" style="padding: 20px; background: #f8d7da; border-left: 4px solid #dc3545; margin: 20px 0;">
                <p style="margin: 0; color: #721c24;"><strong>%s</strong></p>
            </div>',
            esc_html( $message )
        );
    }

    /**
     * Renderizar mensagem quando nenhuma viagem é encontrada
     *
     * @param string $tags     Tags especificadas
     * @param string $ids      IDs especificados
     * @param string $taxonomy Taxonomia usada
     * @return string HTML da mensagem
     */
    private function render_empty_message( $tags, $ids, $taxonomy ) {
        if ( ! empty( $ids ) ) {
            return $this->render_error_message(
                sprintf( __( 'Nenhuma viagem encontrada com os IDs: %s', 'wte-sliders' ), esc_html( $ids ) )
            );
        }

        $taxonomy_labels = array(
            'trip_tag'                  => 'Trip Tag (Tags de Viagem)',
            'trip-packages-categories'  => 'Trip Package Categories (Categorias de Pacotes)',
            'difficulty'                => 'Difficulty (Dificuldade)',
        );
        $taxonomy_label = isset( $taxonomy_labels[ $taxonomy ] ) ? $taxonomy_labels[ $taxonomy ] : $taxonomy;

        return sprintf(
            '<div class="wte-sliders-notice" style="padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107; margin: 20px 0;">
                <p style="margin: 0;"><strong>%s</strong></p>
                <p style="margin: 10px 0 0 0;">%s</p>
                <ol style="margin: 10px 0 0 20px;">
                    <li>%s</li>
                    <li>%s</li>
                    <li>%s</li>
                </ol>
            </div>',
            esc_html__( 'Nenhuma viagem encontrada para exibir neste slider.', 'wte-sliders' ),
            esc_html__( 'Verifique se:', 'wte-sliders' ),
            sprintf( esc_html__( 'O termo "%s" existe na taxonomia "%s" do WP Travel Engine', 'wte-sliders' ), esc_html( $tags ), esc_html( $taxonomy_label ) ),
            esc_html__( 'Existem viagens publicadas (tipo de post "trip")', 'wte-sliders' ),
            sprintf( esc_html__( 'Essas viagens possuem o termo "%s" atribuído', 'wte-sliders' ), esc_html( $tags ) )
        );
    }

}
