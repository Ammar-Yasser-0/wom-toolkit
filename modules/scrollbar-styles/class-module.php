<?php
namespace WOMToolkit\Modules\scrollbar_styles;

if (!defined('ABSPATH')) {
    exit;
}

class Module extends \WOMToolkit\Core\Base_Module
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue'), 100);
    }

    public function get_id()
    {
        return 'scrollbar-styles';
    }

    public function get_title()
    {
        return 'Scrollbar Styles';
    }

    public function get_description()
    {
        return 'Customize the frontend scrollbar colors, width, background, radius, and single-side visual border with RTL support.';
    }

    public function render_settings_page()
    {
        $settings = \WOMToolkit\Core\Settings::get();

        if (isset($_POST['wom_save_scrollbar_styles'])) {
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You do not have permission to access this page.', 'wom-toolkit'));
            }

            check_admin_referer('wom_toolkit_save_scrollbar_styles', 'wom_toolkit_scrollbar_styles_nonce');

            $settings['scrollbar-styles'] = array(
                'thumb_color' => isset($_POST['thumb_color']) ? sanitize_hex_color($_POST['thumb_color']) : '#B01F24',
                'thumb_hover_color' => isset($_POST['thumb_hover_color']) ? sanitize_hex_color($_POST['thumb_hover_color']) : '#273B80',
                'track_background_color' => isset($_POST['track_background_color']) ? sanitize_hex_color($_POST['track_background_color']) : '#ffffff',
                'width' => isset($_POST['width']) ? intval($_POST['width']) : 8,
                'border_radius' => isset($_POST['border_radius']) ? intval($_POST['border_radius']) : 2,
                'border_width' => isset($_POST['border_width']) ? intval($_POST['border_width']) : 1,
                'border_color' => isset($_POST['border_color']) ? sanitize_hex_color($_POST['border_color']) : '#273B80',
            );

            \WOMToolkit\Core\Settings::update($settings);

            echo '<div class="wom-toolkit-inline-notice"><p>' . esc_html__('Settings saved.', 'wom-toolkit') . '</p></div>';
        }

        $module_settings = \WOMToolkit\Core\Settings::get('scrollbar-styles');
        $thumb_color = isset($module_settings['thumb_color']) ? $module_settings['thumb_color'] : '#B01F24';
        $thumb_hover_color = isset($module_settings['thumb_hover_color']) ? $module_settings['thumb_hover_color'] : '#273B80';
        $track_background_color = isset($module_settings['track_background_color']) ? $module_settings['track_background_color'] : '#ffffff';
        $width = isset($module_settings['width']) ? $module_settings['width'] : 8;
        $border_radius = isset($module_settings['border_radius']) ? $module_settings['border_radius'] : 2;
        $border_width = isset($module_settings['border_width']) ? $module_settings['border_width'] : 1;
        $border_color = isset($module_settings['border_color']) ? $module_settings['border_color'] : '#273B80';

        echo '<form method="post">';
        wp_nonce_field('wom_toolkit_save_scrollbar_styles', 'wom_toolkit_scrollbar_styles_nonce');
        echo '<table class="form-table">';

        echo '<tr><th>Thumb Color</th><td><input type="color" name="thumb_color" value="' . esc_attr($thumb_color) . '"></td></tr>';
        echo '<tr><th>Thumb Hover Color</th><td><input type="color" name="thumb_hover_color" value="' . esc_attr($thumb_hover_color) . '"></td></tr>';
        echo '<tr><th>Track Background</th><td><input type="color" name="track_background_color" value="' . esc_attr($track_background_color) . '"></td></tr>';
        echo '<tr><th>Scrollbar Width</th><td><input type="number" min="1" name="width" value="' . esc_attr($width) . '"> px</td></tr>';
        echo '<tr><th>Border Radius</th><td><input type="number" min="0" name="border_radius" value="' . esc_attr($border_radius) . '"> px</td></tr>';
        echo '<tr><th>Border Width</th><td><input type="number" min="0" name="border_width" value="' . esc_attr($border_width) . '"> px</td></tr>';
        echo '<tr><th>Border Color</th><td><input type="color" name="border_color" value="' . esc_attr($border_color) . '"></td></tr>';

        echo '</table>';
        echo '<p><button class="button button-primary" name="wom_save_scrollbar_styles">Save</button></p>';
        echo '</form>';
    }

    public function enqueue()
    {
        if (is_admin()) {
            return;
        }

        if (!$this->is_enabled()) {
            return;
        }

        $settings = \WOMToolkit\Core\Settings::get('scrollbar-styles');

        $thumb_color = isset($settings['thumb_color']) ? $settings['thumb_color'] : '#B01F24';
        $thumb_hover_color = isset($settings['thumb_hover_color']) ? $settings['thumb_hover_color'] : '#273B80';
        $track_background_color = isset($settings['track_background_color']) ? $settings['track_background_color'] : '#ffffff';
        $width = isset($settings['width']) ? intval($settings['width']) : 8;
        $border_radius = isset($settings['border_radius']) ? intval($settings['border_radius']) : 2;
        $border_width = isset($settings['border_width']) ? intval($settings['border_width']) : 1;
        $border_color = isset($settings['border_color']) ? $settings['border_color'] : '#273B80';

        $track_shadow = is_rtl()
            ? 'inset -' . $border_width . 'px 0 0 0 ' . esc_attr($border_color)
            : 'inset ' . $border_width . 'px 0 0 0 ' . esc_attr($border_color);

        wp_enqueue_style(
            'wom-scrollbar-styles',
            WOM_TOOLKIT_URL . 'modules/scrollbar-styles/assets/css/frontend.css',
            array(),
            WOM_TOOLKIT_VERSION . '.6'
        );

        $custom_css = '
        ::-webkit-scrollbar {
            width: ' . $width . 'px;
            height: ' . $width . 'px;
            background: ' . esc_attr($track_background_color) . ';
        }

        ::-webkit-scrollbar-track {
            background: ' . esc_attr($track_background_color) . ';
            box-shadow: ' . $track_shadow . ';
        }

        ::-webkit-scrollbar-thumb {
            background: ' . esc_attr($thumb_color) . ';
            border-radius: ' . $border_radius . 'px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: ' . esc_attr($thumb_hover_color) . ';
        }';

        wp_add_inline_style('wom-scrollbar-styles', $custom_css);
    }
}