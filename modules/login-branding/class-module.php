<?php
namespace WOMToolkit\Modules\login_branding;

if (!defined('ABSPATH'))
    exit;

class Module extends \WOMToolkit\Core\Base_Module
{
    public function __construct()
    {
        add_action('login_enqueue_scripts', array($this, 'enqueue_login_styles'), 100);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_filter('login_headerurl', array($this, 'login_logo_url'));
        add_filter('login_headertext', array($this, 'login_logo_title'));
    }

    public function get_id()
    {
        return 'login-branding';
    }

    public function get_title()
    {
        return 'Login Branding';
    }

    public function get_description()
    {
        return 'Customize the WordPress login page logo, form styling, and background.';
    }

    public function render_settings_page()
    {
        $settings = \WOMToolkit\Core\Settings::get();

        if (isset($_POST['wom_save_login_branding'])) {
            $settings['login-branding'] = array(
                'logo_url' => isset($_POST['logo_url']) ? esc_url_raw($_POST['logo_url']) : '',
                'logo_width' => isset($_POST['logo_width']) ? intval($_POST['logo_width']) : 180,
                'logo_height' => isset($_POST['logo_height']) ? intval($_POST['logo_height']) : 80,
                'logo_object_fit' => isset($_POST['logo_object_fit']) ? sanitize_text_field($_POST['logo_object_fit']) : 'contain',
                'logo_object_position' => isset($_POST['logo_object_position']) ? sanitize_text_field($_POST['logo_object_position']) : 'center center',
                'logo_margin_bottom' => isset($_POST['logo_margin_bottom']) ? intval($_POST['logo_margin_bottom']) : 24,

                'form_background' => isset($_POST['form_background']) ? sanitize_text_field($_POST['form_background']) : 'rgba(255,255,255,0.96)',
                'form_text_color' => isset($_POST['form_text_color']) ? sanitize_hex_color($_POST['form_text_color']) : '#1d2327',
                'input_background' => isset($_POST['input_background']) ? sanitize_text_field($_POST['input_background']) : '#ffffff',
                'input_text_color' => isset($_POST['input_text_color']) ? sanitize_hex_color($_POST['input_text_color']) : '#1d2327',
                'input_border_color' => isset($_POST['input_border_color']) ? sanitize_hex_color($_POST['input_border_color']) : '#8c8f94',
                'input_focus_color' => isset($_POST['input_focus_color']) ? sanitize_hex_color($_POST['input_focus_color']) : '#2271b1',
                'button_background' => isset($_POST['button_background']) ? sanitize_hex_color($_POST['button_background']) : '#2271b1',
                'button_text_color' => isset($_POST['button_text_color']) ? sanitize_hex_color($_POST['button_text_color']) : '#ffffff',
                'link_color' => isset($_POST['link_color']) ? sanitize_hex_color($_POST['link_color']) : '#2271b1',
                'link_hover_color' => isset($_POST['link_hover_color']) ? sanitize_hex_color($_POST['link_hover_color']) : '#135e96',

                'background_type' => isset($_POST['background_type']) ? sanitize_text_field($_POST['background_type']) : 'color',
                'background_color' => isset($_POST['background_color']) ? sanitize_hex_color($_POST['background_color']) : '#f0f0f1',
                'background_gradient_color_1' => isset($_POST['background_gradient_color_1']) ? sanitize_hex_color($_POST['background_gradient_color_1']) : '#0f172a',
                'background_gradient_color_2' => isset($_POST['background_gradient_color_2']) ? sanitize_hex_color($_POST['background_gradient_color_2']) : '#1e293b',
                'background_gradient_degree' => isset($_POST['background_gradient_degree']) ? intval($_POST['background_gradient_degree']) : 135
            );

            \WOMToolkit\Core\Settings::update($settings);

            echo '<div class="updated"><p>Saved</p></div>';
        }

        $module_settings = \WOMToolkit\Core\Settings::get('login-branding');

        $logo_url = isset($module_settings['logo_url']) ? $module_settings['logo_url'] : '';
        $logo_width = isset($module_settings['logo_width']) ? $module_settings['logo_width'] : 180;
        $logo_height = isset($module_settings['logo_height']) ? $module_settings['logo_height'] : 80;
        $logo_object_fit = isset($module_settings['logo_object_fit']) ? $module_settings['logo_object_fit'] : 'contain';
        $logo_object_position = isset($module_settings['logo_object_position']) ? $module_settings['logo_object_position'] : 'center center';
        $logo_margin_bottom = isset($module_settings['logo_margin_bottom']) ? $module_settings['logo_margin_bottom'] : 24;

        $form_background = isset($module_settings['form_background']) ? $module_settings['form_background'] : 'rgba(255,255,255,0.96)';
        $form_text_color = isset($module_settings['form_text_color']) ? $module_settings['form_text_color'] : '#1d2327';
        $input_background = isset($module_settings['input_background']) ? $module_settings['input_background'] : '#ffffff';
        $input_text_color = isset($module_settings['input_text_color']) ? $module_settings['input_text_color'] : '#1d2327';
        $input_border_color = isset($module_settings['input_border_color']) ? $module_settings['input_border_color'] : '#8c8f94';
        $input_focus_color = isset($module_settings['input_focus_color']) ? $module_settings['input_focus_color'] : '#2271b1';
        $button_background = isset($module_settings['button_background']) ? $module_settings['button_background'] : '#2271b1';
        $button_text_color = isset($module_settings['button_text_color']) ? $module_settings['button_text_color'] : '#ffffff';
        $link_color = isset($module_settings['link_color']) ? $module_settings['link_color'] : '#2271b1';
        $link_hover_color = isset($module_settings['link_hover_color']) ? $module_settings['link_hover_color'] : '#135e96';

        $background_type = isset($module_settings['background_type']) ? $module_settings['background_type'] : 'color';
        $background_color = isset($module_settings['background_color']) ? $module_settings['background_color'] : '#f0f0f1';
        $background_gradient_color_1 = isset($module_settings['background_gradient_color_1']) ? $module_settings['background_gradient_color_1'] : '#0f172a';
        $background_gradient_color_2 = isset($module_settings['background_gradient_color_2']) ? $module_settings['background_gradient_color_2'] : '#1e293b';
        $background_gradient_degree = isset($module_settings['background_gradient_degree']) ? $module_settings['background_gradient_degree'] : 135;

        echo '<form method="post">';
        echo '<table class="form-table">';

        echo '<tr><th colspan="2"><h2>Logo</h2></th></tr>';
        echo '<tr><th>Logo Image</th><td>';
        echo '<input type="text" name="logo_url" id="wom_login_logo_url" value="' . esc_attr($logo_url) . '" class="regular-text">';
        echo ' <button type="button" class="button" id="wom_login_logo_upload">Upload</button>';
        echo '</td></tr>';

        echo '<tr><th>Logo Width</th><td><input type="number" min="1" name="logo_width" value="' . esc_attr($logo_width) . '"> px</td></tr>';
        echo '<tr><th>Logo Height</th><td><input type="number" min="1" name="logo_height" value="' . esc_attr($logo_height) . '"> px</td></tr>';

        echo '<tr><th>Logo Fit</th><td>';
        echo '<select name="logo_object_fit">';
        echo '<option value="contain"' . selected($logo_object_fit, 'contain', false) . '>contain</option>';
        echo '<option value="cover"' . selected($logo_object_fit, 'cover', false) . '>cover</option>';
        echo '<option value="auto"' . selected($logo_object_fit, 'auto', false) . '>auto</option>';
        echo '<option value="100% 100%"' . selected($logo_object_fit, '100% 100%', false) . '>stretch (100% 100%)</option>';
        echo '</select>';
        echo '<p class="description">Applied as background-size for accurate logo control on the WordPress login page.</p>';
        echo '</td></tr>';

        echo '<tr><th>Logo Position</th><td><input type="text" name="logo_object_position" value="' . esc_attr($logo_object_position) . '" class="regular-text"><p class="description">Example: center center, top center, left center, 20% 50%</p></td></tr>';
        echo '<tr><th>Logo Bottom Margin</th><td><input type="number" min="0" name="logo_margin_bottom" value="' . esc_attr($logo_margin_bottom) . '"> px</td></tr>';

        echo '<tr><th colspan="2"><h2>Form Styling</h2></th></tr>';
        echo '<tr><th>Form Background</th><td><input type="text" name="form_background" value="' . esc_attr($form_background) . '" class="regular-text"></td></tr>';
        echo '<tr><th>Form Text Color</th><td><input type="color" name="form_text_color" value="' . esc_attr($form_text_color) . '"></td></tr>';
        echo '<tr><th>Input Background</th><td><input type="text" name="input_background" value="' . esc_attr($input_background) . '" class="regular-text"></td></tr>';
        echo '<tr><th>Input Text Color</th><td><input type="color" name="input_text_color" value="' . esc_attr($input_text_color) . '"></td></tr>';
        echo '<tr><th>Input Border Color</th><td><input type="color" name="input_border_color" value="' . esc_attr($input_border_color) . '"></td></tr>';
        echo '<tr><th>Input Focus Color</th><td><input type="color" name="input_focus_color" value="' . esc_attr($input_focus_color) . '"></td></tr>';
        echo '<tr><th>Button Background</th><td><input type="color" name="button_background" value="' . esc_attr($button_background) . '"></td></tr>';
        echo '<tr><th>Button Text Color</th><td><input type="color" name="button_text_color" value="' . esc_attr($button_text_color) . '"></td></tr>';
        echo '<tr><th>Link Color</th><td><input type="color" name="link_color" value="' . esc_attr($link_color) . '"></td></tr>';
        echo '<tr><th>Link Hover Color</th><td><input type="color" name="link_hover_color" value="' . esc_attr($link_hover_color) . '"></td></tr>';

        echo '<tr><th colspan="2"><h2>Background</h2></th></tr>';
        echo '<tr><th>Background Type</th><td>';
        echo '<select name="background_type">';
        echo '<option value="color"' . selected($background_type, 'color', false) . '>Color</option>';
        echo '<option value="gradient"' . selected($background_type, 'gradient', false) . '>Gradient</option>';
        echo '</select>';
        echo '</td></tr>';

        echo '<tr><th>Background Color</th><td><input type="color" name="background_color" value="' . esc_attr($background_color) . '"></td></tr>';
        echo '<tr><th>Gradient Color 1</th><td><input type="color" name="background_gradient_color_1" value="' . esc_attr($background_gradient_color_1) . '"></td></tr>';
        echo '<tr><th>Gradient Color 2</th><td><input type="color" name="background_gradient_color_2" value="' . esc_attr($background_gradient_color_2) . '"></td></tr>';
        echo '<tr><th>Gradient Degree</th><td><input type="number" name="background_gradient_degree" min="0" max="360" value="' . esc_attr($background_gradient_degree) . '"> deg</td></tr>';

        echo '</table>';
        echo '<p><button class="button button-primary" name="wom_save_login_branding">Save</button></p>';
        echo '</form>';
    }

    public function enqueue_admin_assets($hook)
    {
        if (!$this->is_enabled()) {
            return;
        }

        if (strpos($hook, 'wom-toolkit') === false) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script(
            'wom-login-branding-admin',
            WOM_TOOLKIT_URL . 'modules/login-branding/assets/js/admin.js',
            array('jquery'),
            WOM_TOOLKIT_VERSION . '.5',
            true
        );
    }

    public function enqueue_login_styles()
    {
        if (!$this->is_enabled()) {
            return;
        }

        $settings = \WOMToolkit\Core\Settings::get('login-branding');

        $logo_url = isset($settings['logo_url']) ? $settings['logo_url'] : '';
        $logo_width = isset($settings['logo_width']) ? intval($settings['logo_width']) : 180;
        $logo_height = isset($settings['logo_height']) ? intval($settings['logo_height']) : 80;
        $logo_object_fit = isset($settings['logo_object_fit']) ? $settings['logo_object_fit'] : 'contain';
        $logo_object_position = isset($settings['logo_object_position']) ? $settings['logo_object_position'] : 'center center';
        $logo_margin_bottom = isset($settings['logo_margin_bottom']) ? intval($settings['logo_margin_bottom']) : 24;

        $form_background = isset($settings['form_background']) ? $settings['form_background'] : 'rgba(255,255,255,0.96)';
        $form_text_color = isset($settings['form_text_color']) ? $settings['form_text_color'] : '#1d2327';
        $input_background = isset($settings['input_background']) ? $settings['input_background'] : '#ffffff';
        $input_text_color = isset($settings['input_text_color']) ? $settings['input_text_color'] : '#1d2327';
        $input_border_color = isset($settings['input_border_color']) ? $settings['input_border_color'] : '#8c8f94';
        $input_focus_color = isset($settings['input_focus_color']) ? $settings['input_focus_color'] : '#2271b1';
        $button_background = isset($settings['button_background']) ? $settings['button_background'] : '#2271b1';
        $button_text_color = isset($settings['button_text_color']) ? $settings['button_text_color'] : '#ffffff';
        $link_color = isset($settings['link_color']) ? $settings['link_color'] : '#2271b1';
        $link_hover_color = isset($settings['link_hover_color']) ? $settings['link_hover_color'] : '#135e96';

        $background_type = isset($settings['background_type']) ? $settings['background_type'] : 'color';
        $background_color = isset($settings['background_color']) ? $settings['background_color'] : '#f0f0f1';
        $background_gradient_color_1 = isset($settings['background_gradient_color_1']) ? $settings['background_gradient_color_1'] : '#0f172a';
        $background_gradient_color_2 = isset($settings['background_gradient_color_2']) ? $settings['background_gradient_color_2'] : '#1e293b';
        $background_gradient_degree = isset($settings['background_gradient_degree']) ? intval($settings['background_gradient_degree']) : 135;

        wp_enqueue_style(
            'wom-login-branding',
            WOM_TOOLKIT_URL . 'modules/login-branding/assets/css/login.css',
            array(),
            WOM_TOOLKIT_VERSION . '.5'
        );

        $background_css = $background_color;

        if ($background_type === 'gradient') {
            $background_css = 'linear-gradient(' . $background_gradient_degree . 'deg, ' . $background_gradient_color_1 . ' 0%, ' . $background_gradient_color_2 . ' 100%)';
        }

        $custom_css = '
        body.login {
            background: ' . $background_css . ';
        }

        body.login #login h1 a {
            width: ' . $logo_width . 'px;
            height: ' . $logo_height . 'px;
            margin-bottom: ' . $logo_margin_bottom . 'px;
            position: relative;
            background: none !important;
            box-shadow: none;
            outline: 0;
        }

        body.login #login h1 a::before {
            content: "";
            position: absolute;
            inset: 0;
            ' . (!empty($logo_url) ? 'background-image: url("' . esc_url($logo_url) . '");' : '') . '
            background-repeat: no-repeat;
            background-size: ' . esc_attr($logo_object_fit) . ';
            background-position: ' . esc_attr($logo_object_position) . ';
        }

        body.login form {
            background: ' . esc_attr($form_background) . ';
            color: ' . esc_attr($form_text_color) . ';
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        body.login label,
        body.login form .forgetmenot,
        body.login .message,
        body.login #login_error {
            color: ' . esc_attr($form_text_color) . ';
        }

        body.login input[type="text"],
        body.login input[type="password"],
        body.login input[type="email"] {
            background: ' . esc_attr($input_background) . ';
            color: ' . esc_attr($input_text_color) . ';
            border-color: ' . esc_attr($input_border_color) . ';
        }

        body.login input[type="text"]:focus,
        body.login input[type="password"]:focus,
        body.login input[type="email"]:focus {
            border-color: ' . esc_attr($input_focus_color) . ';
            box-shadow: 0 0 0 1px ' . esc_attr($input_focus_color) . ';
        }

        body.login .wp-core-ui .button-primary,
        body.login .wp-core-ui .button.button-primary,
        body.login #loginform .button-primary,
        body.login #loginform .button.button-primary,
        body.login #wp-submit,
        body.login input[type="submit"] {
            background: ' . esc_attr($button_background) . ' !important;
            border-color: ' . esc_attr($button_background) . ' !important;
            color: ' . esc_attr($button_text_color) . ' !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }

        body.login .wp-core-ui .button-primary:hover,
        body.login .wp-core-ui .button-primary:focus,
        body.login .wp-core-ui .button.button-primary:hover,
        body.login .wp-core-ui .button.button-primary:focus,
        body.login #loginform .button-primary:hover,
        body.login #loginform .button-primary:focus,
        body.login #loginform .button.button-primary:hover,
        body.login #loginform .button.button-primary:focus,
        body.login #wp-submit:hover,
        body.login #wp-submit:focus,
        body.login input[type="submit"]:hover,
        body.login input[type="submit"]:focus {
            background: ' . esc_attr($button_background) . ' !important;
            border-color: ' . esc_attr($button_background) . ' !important;
            color: ' . esc_attr($button_text_color) . ' !important;
            box-shadow: none !important;
            text-shadow: none !important;
            filter: brightness(0.95);
        }

        body.login #nav a,
        body.login #backtoblog a {
            color: ' . esc_attr($link_color) . ';
        }

        body.login #nav a:hover,
        body.login #backtoblog a:hover {
            color: ' . esc_attr($link_hover_color) . ';
        }
        ';

        wp_add_inline_style('wom-login-branding', $custom_css);
    }

    public function login_logo_url()
    {
        return home_url('/');
    }

    public function login_logo_title()
    {
        return get_bloginfo('name');
    }
}