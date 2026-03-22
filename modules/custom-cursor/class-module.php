<?php
namespace WOMToolkit\Modules\custom_cursor;

if (!defined('ABSPATH'))
    exit;

class Module extends \WOMToolkit\Core\Base_Module
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue'), 100);
    }

    public function get_id()
    {
        return 'custom-cursor';
    }

    public function get_title()
    {
        return 'Custom Cursor';
    }

    public function get_description()
    {
        return 'Adds a desktop-only custom cursor with dot, ring, hover state, and click pulse animation.';
    }

    public function render_settings_page()
    {
        $settings = \WOMToolkit\Core\Settings::get();

        if (isset($_POST['wom_save_custom_cursor'])) {
            $settings['custom-cursor'] = array(
                'dot_color' => isset($_POST['dot_color']) ? sanitize_hex_color($_POST['dot_color']) : '#000000',
                'ring_color' => isset($_POST['ring_color']) ? sanitize_hex_color($_POST['ring_color']) : '#000000',
                'hover_bg' => isset($_POST['hover_bg']) ? sanitize_text_field($_POST['hover_bg']) : 'rgba(0,0,0,0.06)',
                'click_bg' => isset($_POST['click_bg']) ? sanitize_text_field($_POST['click_bg']) : 'rgba(0,0,0,0.12)',
                'dot_size' => isset($_POST['dot_size']) ? intval($_POST['dot_size']) : 6,
                'ring_size' => isset($_POST['ring_size']) ? intval($_POST['ring_size']) : 26,
                'hover_size' => isset($_POST['hover_size']) ? intval($_POST['hover_size']) : 42,
                'click_size' => isset($_POST['click_size']) ? intval($_POST['click_size']) : 22,
                'ring_border_width' => isset($_POST['ring_border_width']) ? floatval($_POST['ring_border_width']) : 1.5,
                'pulse_size' => isset($_POST['pulse_size']) ? intval($_POST['pulse_size']) : 18,
                'pulse_end_size' => isset($_POST['pulse_end_size']) ? intval($_POST['pulse_end_size']) : 52,
                'pulse_border_width' => isset($_POST['pulse_border_width']) ? floatval($_POST['pulse_border_width']) : 1.5,
                'z_index' => isset($_POST['z_index']) ? intval($_POST['z_index']) : 999999,
                'dot_speed' => isset($_POST['dot_speed']) ? floatval($_POST['dot_speed']) : 0.45,
                'ring_speed' => isset($_POST['ring_speed']) ? floatval($_POST['ring_speed']) : 0.18,
            );

            \WOMToolkit\Core\Settings::update($settings);

            echo '<div class="updated"><p>Saved</p></div>';
        }

        $module_settings = \WOMToolkit\Core\Settings::get('custom-cursor');

        $dot_color = isset($module_settings['dot_color']) ? $module_settings['dot_color'] : '#000000';
        $ring_color = isset($module_settings['ring_color']) ? $module_settings['ring_color'] : '#000000';
        $hover_bg = isset($module_settings['hover_bg']) ? $module_settings['hover_bg'] : 'rgba(0,0,0,0.06)';
        $click_bg = isset($module_settings['click_bg']) ? $module_settings['click_bg'] : 'rgba(0,0,0,0.12)';
        $dot_size = isset($module_settings['dot_size']) ? $module_settings['dot_size'] : 6;
        $ring_size = isset($module_settings['ring_size']) ? $module_settings['ring_size'] : 26;
        $hover_size = isset($module_settings['hover_size']) ? $module_settings['hover_size'] : 42;
        $click_size = isset($module_settings['click_size']) ? $module_settings['click_size'] : 22;
        $ring_border_width = isset($module_settings['ring_border_width']) ? $module_settings['ring_border_width'] : 1.5;
        $pulse_size = isset($module_settings['pulse_size']) ? $module_settings['pulse_size'] : 18;
        $pulse_end_size = isset($module_settings['pulse_end_size']) ? $module_settings['pulse_end_size'] : 52;
        $pulse_border_width = isset($module_settings['pulse_border_width']) ? $module_settings['pulse_border_width'] : 1.5;
        $z_index = isset($module_settings['z_index']) ? $module_settings['z_index'] : 999999;
        $dot_speed = isset($module_settings['dot_speed']) ? $module_settings['dot_speed'] : 0.45;
        $ring_speed = isset($module_settings['ring_speed']) ? $module_settings['ring_speed'] : 0.18;

        echo '<form method="post">';
        echo '<table class="form-table">';

        echo '<tr><th>Dot Color</th><td><input type="color" name="dot_color" value="' . esc_attr($dot_color) . '"></td></tr>';
        echo '<tr><th>Ring Color</th><td><input type="color" name="ring_color" value="' . esc_attr($ring_color) . '"></td></tr>';
        echo '<tr><th>Hover Background</th><td><input type="text" name="hover_bg" value="' . esc_attr($hover_bg) . '" class="regular-text"></td></tr>';
        echo '<tr><th>Click Background</th><td><input type="text" name="click_bg" value="' . esc_attr($click_bg) . '" class="regular-text"></td></tr>';
        echo '<tr><th>Dot Size</th><td><input type="number" min="1" name="dot_size" value="' . esc_attr($dot_size) . '"> px</td></tr>';
        echo '<tr><th>Ring Size</th><td><input type="number" min="1" name="ring_size" value="' . esc_attr($ring_size) . '"> px</td></tr>';
        echo '<tr><th>Hover Size</th><td><input type="number" min="1" name="hover_size" value="' . esc_attr($hover_size) . '"> px</td></tr>';
        echo '<tr><th>Click Size</th><td><input type="number" min="1" name="click_size" value="' . esc_attr($click_size) . '"> px</td></tr>';
        echo '<tr><th>Ring Border Width</th><td><input type="number" min="0" step="0.1" name="ring_border_width" value="' . esc_attr($ring_border_width) . '"> px</td></tr>';
        echo '<tr><th>Pulse Start Size</th><td><input type="number" min="1" name="pulse_size" value="' . esc_attr($pulse_size) . '"> px</td></tr>';
        echo '<tr><th>Pulse End Size</th><td><input type="number" min="1" name="pulse_end_size" value="' . esc_attr($pulse_end_size) . '"> px</td></tr>';
        echo '<tr><th>Pulse Border Width</th><td><input type="number" min="0" step="0.1" name="pulse_border_width" value="' . esc_attr($pulse_border_width) . '"> px</td></tr>';
        echo '<tr><th>Z-Index</th><td><input type="number" min="1" name="z_index" value="' . esc_attr($z_index) . '"></td></tr>';
        echo '<tr><th>Dot Speed</th><td><input type="number" min="0.01" max="1" step="0.01" name="dot_speed" value="' . esc_attr($dot_speed) . '"></td></tr>';
        echo '<tr><th>Ring Speed</th><td><input type="number" min="0.01" max="1" step="0.01" name="ring_speed" value="' . esc_attr($ring_speed) . '"></td></tr>';

        echo '</table>';
        echo '<p><button class="button button-primary" name="wom_save_custom_cursor">Save</button></p>';
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

        $settings = \WOMToolkit\Core\Settings::get('custom-cursor');

        $dot_color = isset($settings['dot_color']) ? $settings['dot_color'] : '#000000';
        $ring_color = isset($settings['ring_color']) ? $settings['ring_color'] : '#000000';
        $hover_bg = isset($settings['hover_bg']) ? $settings['hover_bg'] : 'rgba(0,0,0,0.06)';
        $click_bg = isset($settings['click_bg']) ? $settings['click_bg'] : 'rgba(0,0,0,0.12)';
        $dot_size = isset($settings['dot_size']) ? intval($settings['dot_size']) : 6;
        $ring_size = isset($settings['ring_size']) ? intval($settings['ring_size']) : 26;
        $hover_size = isset($settings['hover_size']) ? intval($settings['hover_size']) : 42;
        $click_size = isset($settings['click_size']) ? intval($settings['click_size']) : 22;
        $ring_border_width = isset($settings['ring_border_width']) ? floatval($settings['ring_border_width']) : 1.5;
        $pulse_size = isset($settings['pulse_size']) ? intval($settings['pulse_size']) : 18;
        $pulse_end_size = isset($settings['pulse_end_size']) ? intval($settings['pulse_end_size']) : 52;
        $pulse_border_width = isset($settings['pulse_border_width']) ? floatval($settings['pulse_border_width']) : 1.5;
        $z_index = isset($settings['z_index']) ? intval($settings['z_index']) : 999999;
        $dot_speed = isset($settings['dot_speed']) ? floatval($settings['dot_speed']) : 0.45;
        $ring_speed = isset($settings['ring_speed']) ? floatval($settings['ring_speed']) : 0.18;

        wp_enqueue_style(
            'wom-custom-cursor',
            WOM_TOOLKIT_URL . 'modules/custom-cursor/assets/css/frontend.css',
            array(),
            WOM_TOOLKIT_VERSION . '.1'
        );

        wp_enqueue_script(
            'wom-custom-cursor',
            WOM_TOOLKIT_URL . 'modules/custom-cursor/assets/js/frontend.js',
            array(),
            WOM_TOOLKIT_VERSION . '.1',
            true
        );

        $default_hover_selectors = array(
            'a',
            'button',
            '[role="button"]',
            'input[type="submit"]',
            'input[type="button"]',
            '.elementor-button',
            '.elementor-icon',
            '.elementor-icon-box',
            '.elementor-image-box',
            '.clickable');

        $custom_css = '
:root {
    --wom-cursor-dot-color: ' . esc_attr($dot_color) . ';
    --wom-cursor-ring-color: ' . esc_attr($ring_color) . ';
    --wom-cursor-hover-bg: ' . esc_attr($hover_bg) . ';
    --wom-cursor-click-bg: ' . esc_attr($click_bg) . ';
    --wom-cursor-dot-size: ' . $dot_size . 'px;
    --wom-cursor-ring-size: ' . $ring_size . 'px;
    --wom-cursor-hover-size: ' . $hover_size . 'px;
    --wom-cursor-click-size: ' . $click_size . 'px;
    --wom-cursor-ring-border-width: ' . $ring_border_width . 'px;
    --wom-cursor-pulse-size: ' . $pulse_size . 'px;
    --wom-cursor-pulse-end-size: ' . $pulse_end_size . 'px;
    --wom-cursor-pulse-border-width: ' . $pulse_border_width . 'px;
    --wom-cursor-z-index: ' . $z_index . ';
}';

        wp_add_inline_style('wom-custom-cursor', $custom_css);

        $custom_js = '
window.WOMCustomCursorSettings = {
    dotColor: ' . json_encode($dot_color) . ',
    ringColor: ' . json_encode($ring_color) . ',
    hoverBg: ' . json_encode($hover_bg) . ',
    clickBg: ' . json_encode($click_bg) . ',
    dotSize: ' . json_encode($dot_size) . ',
    ringSize: ' . json_encode($ring_size) . ',
    hoverSize: ' . json_encode($hover_size) . ',
    clickSize: ' . json_encode($click_size) . ',
    ringBorderWidth: ' . json_encode($ring_border_width) . ',
    pulseSize: ' . json_encode($pulse_size) . ',
    pulseEndSize: ' . json_encode($pulse_end_size) . ',
    pulseBorderWidth: ' . json_encode($pulse_border_width) . ',
    zIndex: ' . json_encode($z_index) . ',
    dotSpeed: ' . json_encode($dot_speed) . ',
    ringSpeed: ' . json_encode($ring_speed) . ',
    hoverSelectors: ' . json_encode(implode(',', array_filter($default_hover_selectors))) . '
};';

        wp_add_inline_script('wom-custom-cursor', $custom_js, 'before');
    }
}