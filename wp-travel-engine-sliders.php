<?php
/**
 * Plugin Name: WP Travel Engine Sliders
 * Plugin URI: https://github.com/your-username/wp-travel-engine-sliders
 * Description: Sliders personalizados para viagens do WP Travel Engine com shortcodes configuráveis
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://seusite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wte-sliders
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevenir acesso direto
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes do plugin
define( 'WTE_SLIDERS_VERSION', '1.0.0' );
define( 'WTE_SLIDERS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WTE_SLIDERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WTE_SLIDERS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Verificar se o WP Travel Engine está ativo
 */
function wte_sliders_check_dependencies() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( ! is_plugin_active( 'wp-travel-engine/wp-travel-engine.php' ) && ! class_exists( 'WP_Travel_Engine' ) ) {
        add_action( 'admin_notices', 'wte_sliders_dependency_notice' );
        deactivate_plugins( WTE_SLIDERS_PLUGIN_BASENAME );
        return false;
    }

    return true;
}

/**
 * Exibir aviso de dependência
 */
function wte_sliders_dependency_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <strong>WP Travel Engine Sliders</strong> requer que o plugin
            <strong>WP Travel Engine</strong> esteja instalado e ativado.
        </p>
    </div>
    <?php
}

/**
 * Autoloader simples para classes do plugin
 */
spl_autoload_register( function( $class ) {
    $prefix = 'WTE_Sliders_';
    $base_dir = WTE_SLIDERS_PLUGIN_DIR . 'includes/';

    $len = strlen( $prefix );
    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        return;
    }

    // Converter o nome completo da classe para o formato de arquivo
    $file = $base_dir . 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

    if ( file_exists( $file ) ) {
        require $file;
    }
});

/**
 * Inicializar o plugin
 */
function wte_sliders_init() {
    if ( ! wte_sliders_check_dependencies() ) {
        return;
    }

    // Inicializar a classe principal
    WTE_Sliders_Main::get_instance();
}
add_action( 'plugins_loaded', 'wte_sliders_init' );

/**
 * Hook de ativação do plugin
 */
function wte_sliders_activate() {
    if ( ! wte_sliders_check_dependencies() ) {
        wp_die(
            'WP Travel Engine Sliders requer que o plugin WP Travel Engine esteja instalado e ativado.',
            'Dependência não encontrada',
            array( 'back_link' => true )
        );
    }

    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wte_sliders_activate' );

/**
 * Hook de desativação do plugin
 */
function wte_sliders_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wte_sliders_deactivate' );
