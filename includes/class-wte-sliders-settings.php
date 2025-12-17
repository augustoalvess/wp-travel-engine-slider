<?php

/**
 * Classe para gerenciamento de configurações do plugin
 *
 * @package WTE_Sliders
 */

// Prevenir acesso direto
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Classe WTE_Sliders_Settings
 */
class WTE_Sliders_Settings
{

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->init_hooks();
    }

    /**
     * Inicializar hooks do WordPress
     */
    private function init_hooks()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Adicionar página de configurações no menu do WordPress
     */
    public function add_settings_page()
    {
        add_options_page(
            __('WTE Sliders - Configurações', 'wte-sliders'),
            __('WTE Sliders', 'wte-sliders'),
            'manage_options',
            'wte-sliders-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Registrar configurações e campos
     */
    public function register_settings()
    {
        register_setting(
            'wte_sliders_options_group',
            'wte_sliders_options',
            array($this, 'sanitize_settings')
        );

        // Seção: Configurações Gerais
        add_settings_section(
            'wte_sliders_general_section',
            __('Configurações Gerais', 'wte-sliders'),
            array($this, 'render_general_section_description'),
            'wte-sliders-settings'
        );

        // Campo: Ativar Template Customizado
        add_settings_field(
            'enable_single_trip_template',
            __('Template Customizado de Viagem', 'wte-sliders'),
            array($this, 'render_checkbox_field'),
            'wte-sliders-settings',
            'wte_sliders_general_section',
            array(
                'label_for' => 'enable_single_trip_template',
                'description' => __('Ativar template customizado para páginas individuais de viagens', 'wte-sliders'),
            )
        );

        // Campo: Ativar Template Customizado de Arquivo
        add_settings_field(
            'enable_archive_template',
            __('Template Customizado de Arquivo', 'wte-sliders'),
            array($this, 'render_checkbox_field'),
            'wte-sliders-settings',
            'wte_sliders_general_section',
            array(
                'label_for' => 'enable_archive_template',
                'description' => __('Ativar template customizado para páginas de arquivo e taxonomias de viagens', 'wte-sliders'),
            )
        );

        // Seção: Informações de Contato
        add_settings_section(
            'wte_sliders_contact_section',
            __('Informações de Contato', 'wte-sliders'),
            array($this, 'render_contact_section_description'),
            'wte-sliders-settings'
        );

        // Campo: WhatsApp
        add_settings_field(
            'whatsapp_number',
            __('Número WhatsApp', 'wte-sliders'),
            array($this, 'render_text_field'),
            'wte-sliders-settings',
            'wte_sliders_contact_section',
            array(
                'label_for' => 'whatsapp_number',
                'placeholder' => '+55 51 99999-9999',
                'description' => __('Incluir código do país (ex: +55 para Brasil)', 'wte-sliders'),
            )
        );

        // Campo: Mensagem WhatsApp
        add_settings_field(
            'whatsapp_message',
            __('Mensagem WhatsApp Padrão', 'wte-sliders'),
            array($this, 'render_textarea_field'),
            'wte-sliders-settings',
            'wte_sliders_contact_section',
            array(
                'label_for' => 'whatsapp_message',
                'description' => __('Mensagem padrão ao clicar no botão WhatsApp', 'wte-sliders'),
                'rows' => 3,
            )
        );

        // Seção: Redes Sociais
        add_settings_section(
            'wte_sliders_social_section',
            __('Redes Sociais', 'wte-sliders'),
            array($this, 'render_social_section_description'),
            'wte-sliders-settings'
        );

        // Campo: Instagram
        add_settings_field(
            'instagram_handle',
            __('Usuário do Instagram', 'wte-sliders'),
            array($this, 'render_text_field'),
            'wte-sliders-settings',
            'wte_sliders_social_section',
            array(
                'label_for' => 'instagram_handle',
                'placeholder' => 'seuinstagram',
                'description' => __('Somente o nome de usuário, sem @ (ex: seuinstagram)', 'wte-sliders'),
            )
        );

        // Campo: Facebook
        add_settings_field(
            'facebook_url',
            __('URL da Página do Facebook', 'wte-sliders'),
            array($this, 'render_url_field'),
            'wte-sliders-settings',
            'wte_sliders_social_section',
            array(
                'label_for' => 'facebook_url',
                'placeholder' => 'https://facebook.com/suapagina',
                'description' => __('URL completa da página do Facebook (opcional)', 'wte-sliders'),
            )
        );
    }

    /**
     * Renderizar página de configurações
     */
    public function render_settings_page()
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        // Verificar se salvou configurações
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'wte_sliders_messages',
                'wte_sliders_message',
                __('Configurações salvas com sucesso!', 'wte-sliders'),
                'updated'
            );
        }

        settings_errors('wte_sliders_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wte_sliders_options_group');
                do_settings_sections('wte-sliders-settings');
                submit_button(__('Salvar Configurações', 'wte-sliders'));
                ?>
            </form>

            <div class="card" style="margin-top: 20px;">
                <h2><?php esc_html_e('Ajuda & Documentação', 'wte-sliders'); ?></h2>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><?php esc_html_e('O template customizado será aplicado automaticamente a todas as páginas de viagens quando ativado', 'wte-sliders'); ?></li>
                    <li><?php esc_html_e('O número do WhatsApp deve incluir o código do país (ex: +55 para Brasil)', 'wte-sliders'); ?></li>
                    <li><?php esc_html_e('O usuário do Instagram não deve incluir o símbolo @', 'wte-sliders'); ?></li>
                    <li><?php esc_html_e('Para customizar os templates no seu tema, copie os arquivos de /wp-content/plugins/wp-travel-engine-sliders/templates/ para /seu-tema/wte-sliders/', 'wte-sliders'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Renderizar descrição da seção geral
     */
    public function render_general_section_description()
    {
        echo '<p>' . esc_html__('Configure as opções gerais do plugin.', 'wte-sliders') . '</p>';
    }

    /**
     * Renderizar descrição da seção de contato
     */
    public function render_contact_section_description()
    {
        echo '<p>' . esc_html__('Configure as informações de contato exibidas nas páginas de viagens.', 'wte-sliders') . '</p>';
    }

    /**
     * Renderizar descrição da seção de redes sociais
     */
    public function render_social_section_description()
    {
        echo '<p>' . esc_html__('Configure os links de redes sociais.', 'wte-sliders') . '</p>';
    }

    /**
     * Renderizar campo checkbox
     *
     * @param array $args Argumentos do campo
     */
    public function render_checkbox_field($args)
    {
        $options = get_option('wte_sliders_options', self::get_defaults());
        $field_id = $args['label_for'];
        $checked = !empty($options[$field_id]) ? 'checked' : '';
        ?>
        <label for="<?php echo esc_attr($field_id); ?>">
            <input type="checkbox"
                   id="<?php echo esc_attr($field_id); ?>"
                   name="wte_sliders_options[<?php echo esc_attr($field_id); ?>]"
                   value="1"
                   <?php echo $checked; ?>>
            <?php echo esc_html($args['description']); ?>
        </label>
        <?php
    }

    /**
     * Renderizar campo de texto
     *
     * @param array $args Argumentos do campo
     */
    public function render_text_field($args)
    {
        $options = get_option('wte_sliders_options', self::get_defaults());
        $field_id = $args['label_for'];
        $value = isset($options[$field_id]) ? $options[$field_id] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        ?>
        <input type="text"
               id="<?php echo esc_attr($field_id); ?>"
               name="wte_sliders_options[<?php echo esc_attr($field_id); ?>]"
               value="<?php echo esc_attr($value); ?>"
               placeholder="<?php echo esc_attr($placeholder); ?>"
               class="regular-text">
        <?php if (!empty($args['description'])): ?>
            <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Renderizar campo de URL
     *
     * @param array $args Argumentos do campo
     */
    public function render_url_field($args)
    {
        $options = get_option('wte_sliders_options', self::get_defaults());
        $field_id = $args['label_for'];
        $value = isset($options[$field_id]) ? $options[$field_id] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        ?>
        <input type="url"
               id="<?php echo esc_attr($field_id); ?>"
               name="wte_sliders_options[<?php echo esc_attr($field_id); ?>]"
               value="<?php echo esc_attr($value); ?>"
               placeholder="<?php echo esc_attr($placeholder); ?>"
               class="regular-text">
        <?php if (!empty($args['description'])): ?>
            <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Renderizar campo textarea
     *
     * @param array $args Argumentos do campo
     */
    public function render_textarea_field($args)
    {
        $options = get_option('wte_sliders_options', self::get_defaults());
        $field_id = $args['label_for'];
        $value = isset($options[$field_id]) ? $options[$field_id] : '';
        $rows = isset($args['rows']) ? $args['rows'] : 5;
        ?>
        <textarea id="<?php echo esc_attr($field_id); ?>"
                  name="wte_sliders_options[<?php echo esc_attr($field_id); ?>]"
                  rows="<?php echo esc_attr($rows); ?>"
                  class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php if (!empty($args['description'])): ?>
            <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Sanitizar configurações
     *
     * @param array $input Dados de entrada
     * @return array Dados sanitizados
     */
    public function sanitize_settings($input)
    {
        $sanitized = array();

        // Checkbox: Enable Single Trip Template
        $sanitized['enable_single_trip_template'] = !empty($input['enable_single_trip_template']) ? 1 : 0;

        // Checkbox: Enable Archive Template
        $sanitized['enable_archive_template'] = !empty($input['enable_archive_template']) ? 1 : 0;

        // WhatsApp Number (permitir apenas números, +, espaços e traços)
        if (!empty($input['whatsapp_number'])) {
            $sanitized['whatsapp_number'] = preg_replace('/[^0-9+\s\-]/', '', $input['whatsapp_number']);
        }

        // WhatsApp Message
        if (!empty($input['whatsapp_message'])) {
            $sanitized['whatsapp_message'] = sanitize_textarea_field($input['whatsapp_message']);
        }

        // Instagram Handle (apenas alfanuméricos e underscores)
        if (!empty($input['instagram_handle'])) {
            $sanitized['instagram_handle'] = preg_replace('/[^a-zA-Z0-9_]/', '', $input['instagram_handle']);
        }

        // Facebook URL
        if (!empty($input['facebook_url'])) {
            $sanitized['facebook_url'] = esc_url_raw($input['facebook_url']);
        }

        return $sanitized;
    }

    /**
     * Obter valores padrão das configurações
     *
     * @return array Valores padrão
     */
    public static function get_defaults()
    {
        return array(
            'enable_single_trip_template' => 0,
            'enable_archive_template'     => 0,
            'whatsapp_number'             => '',
            'whatsapp_message'            => __('Olá! Gostaria de saber mais sobre esta viagem.', 'wte-sliders'),
            'instagram_handle'            => '',
            'facebook_url'                => '',
        );
    }

    /**
     * Obter valor de uma configuração específica
     *
     * @param string $key     Chave da configuração
     * @param mixed  $default Valor padrão se não existir
     * @return mixed Valor da configuração
     */
    public static function get_option($key, $default = null)
    {
        $options = get_option('wte_sliders_options', self::get_defaults());

        if ($default === null) {
            $defaults = self::get_defaults();
            $default = isset($defaults[$key]) ? $defaults[$key] : '';
        }

        return isset($options[$key]) ? $options[$key] : $default;
    }
}
