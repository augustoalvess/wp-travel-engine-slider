<?php
/**
 * Classe para carregamento de templates
 *
 * @package WTE_Sliders
 */

// Prevenir acesso direto
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe WTE_Sliders_Template_Loader
 */
class WTE_Sliders_Template_Loader {

    /**
     * Caminho base dos templates
     *
     * @var string
     */
    private $template_path;

    /**
     * Construtor
     */
    public function __construct() {
        $this->template_path = WTE_SLIDERS_PLUGIN_DIR . 'templates/';
    }

    /**
     * Carregar template e retornar HTML
     *
     * @param string $template_name Nome do arquivo de template (sem .php)
     * @param array  $data          Dados para passar ao template
     * @return string HTML renderizado
     */
    public function load_template( $template_name, $data = array() ) {
        $template_file = $this->locate_template( $template_name );

        if ( ! $template_file ) {
            return '';
        }

        // Extrair dados para variáveis no escopo do template
        extract( $data );

        // Capturar output do template
        ob_start();
        include $template_file;
        $output = ob_get_clean();

        return $output;
    }

    /**
     * Localizar arquivo de template
     *
     * Permite override em tema child ou tema parent
     *
     * @param string $template_name Nome do template
     * @return string|bool Caminho do template ou false se não encontrado
     */
    private function locate_template( $template_name ) {
        $template_file = $template_name . '.php';

        // Verificar em tema child
        $child_theme_path = get_stylesheet_directory() . '/wte-sliders/' . $template_file;
        if ( file_exists( $child_theme_path ) ) {
            return $child_theme_path;
        }

        // Verificar em tema parent
        $parent_theme_path = get_template_directory() . '/wte-sliders/' . $template_file;
        if ( file_exists( $parent_theme_path ) ) {
            return $parent_theme_path;
        }

        // Verificar no plugin
        $plugin_path = $this->template_path . $template_file;
        if ( file_exists( $plugin_path ) ) {
            return $plugin_path;
        }

        return false;
    }

    /**
     * Carregar partial (template parcial)
     *
     * @param string $partial_name Nome do partial
     * @param array  $data         Dados para passar ao partial
     * @return void
     */
    public function load_partial( $partial_name, $data = array() ) {
        $partial_file = $this->locate_template( 'partials/' . $partial_name );

        if ( $partial_file ) {
            extract( $data );
            include $partial_file;
        }
    }

    /**
     * Obter URL de asset (imagem, ícone, etc)
     *
     * @param string $asset_path Caminho relativo do asset dentro de assets/
     * @return string URL completa do asset
     */
    public function get_asset_url( $asset_path ) {
        return WTE_SLIDERS_PLUGIN_URL . 'assets/' . ltrim( $asset_path, '/' );
    }
}
