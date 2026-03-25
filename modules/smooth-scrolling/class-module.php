<?php
namespace WOMToolkit\Modules\smooth_scrolling;

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
        return 'smooth-scrolling';
    }

    public function get_title()
    {
        return 'Smooth Scrolling';
    }

    public function get_description()
    {
        return 'Lenis-based smooth scrolling with anchor support.';
    }

    public function has_admin_tab()
    {
        return true;
    }

    public function render_settings_page()
    {
        $settings = \WOMToolkit\Core\Settings::get();

        if (isset($_POST['wom_save_smooth'])) {
            $settings['smooth-scrolling'] = array(
                'duration' => isset($_POST['duration']) ? floatval($_POST['duration']) : 1.5,
                'wheelMultiplier' => isset($_POST['wheelMultiplier']) ? floatval($_POST['wheelMultiplier']) : 1.5,
                'touchMultiplier' => isset($_POST['touchMultiplier']) ? floatval($_POST['touchMultiplier']) : 1,
                'offset' => isset($_POST['offset']) ? intval($_POST['offset']) : 80,
                'mobileBreakpoint' => isset($_POST['mobileBreakpoint']) ? intval($_POST['mobileBreakpoint']) : 992
            );

            \WOMToolkit\Core\Settings::update($settings);

            echo '<div class="wom-toolkit-inline-notice"><p>' . esc_html__('Settings saved.', 'wom-toolkit') . '</p></div>';
        }

        $module_settings = \WOMToolkit\Core\Settings::get('smooth-scrolling');

        $duration = isset($module_settings['duration']) ? $module_settings['duration'] : 1.5;
        $wheelMultiplier = isset($module_settings['wheelMultiplier']) ? $module_settings['wheelMultiplier'] : 1.5;
        $touchMultiplier = isset($module_settings['touchMultiplier']) ? $module_settings['touchMultiplier'] : 1;
        $offset = isset($module_settings['offset']) ? $module_settings['offset'] : 80;
        $mobileBreakpoint = isset($module_settings['mobileBreakpoint']) ? $module_settings['mobileBreakpoint'] : 992;

        echo '<form method="post">';
        echo '<table class="form-table">';
        echo '<tr><th>Duration</th><td><input type="number" step="0.1" name="duration" value="' . esc_attr($duration) . '"></td></tr>';
        echo '<tr><th>Wheel Multiplier</th><td><input type="number" step="0.1" name="wheelMultiplier" value="' . esc_attr($wheelMultiplier) . '"></td></tr>';
        echo '<tr><th>Touch Multiplier</th><td><input type="number" step="0.1" name="touchMultiplier" value="' . esc_attr($touchMultiplier) . '"></td></tr>';
        echo '<tr><th>Offset</th><td><input type="number" name="offset" value="' . esc_attr($offset) . '"></td></tr>';
        echo '<tr><th>Mobile Breakpoint</th><td><input type="number" name="mobileBreakpoint" value="' . esc_attr($mobileBreakpoint) . '"></td></tr>';
        echo '</table>';
        echo '<p><button class="button button-primary" name="wom_save_smooth">Save</button></p>';
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

        $settings = array();

        if (class_exists('\WOMToolkit\Core\Settings')) {
            $settings = \WOMToolkit\Core\Settings::get('smooth-scrolling');
        }

        $duration = isset($settings['duration']) ? $settings['duration'] : 1.5;
        $wheelMultiplier = isset($settings['wheelMultiplier']) ? $settings['wheelMultiplier'] : 1.5;
        $touchMultiplier = isset($settings['touchMultiplier']) ? $settings['touchMultiplier'] : 1;
        $offset = isset($settings['offset']) ? $settings['offset'] : 80;
        $mobileBreakpoint = isset($settings['mobileBreakpoint']) ? $settings['mobileBreakpoint'] : 992;

        wp_enqueue_script(
            'lenis',
            WOM_TOOLKIT_URL . 'modules/smooth-scrolling/assets/vendor/lenis.min.js',
            array(),
            '1.3.8',
            true
        );

        wp_enqueue_script(
            'wom-smooth-scroll',
            WOM_TOOLKIT_URL . 'modules/smooth-scrolling/assets/js/frontend.js',
            array('lenis'),
            WOM_TOOLKIT_VERSION . '.2',
            true
        );

        wp_localize_script(
            'wom-smooth-scroll',
            'WOMSmoothSettings',
            array(
            'duration' => $duration,
            'wheelMultiplier' => $wheelMultiplier,
            'touchMultiplier' => $touchMultiplier,
            'offset' => $offset,
            'mobileBreakpoint' => $mobileBreakpoint
        )
        );

        wp_enqueue_style(
            'wom-smooth-scroll',
            WOM_TOOLKIT_URL . 'modules/smooth-scrolling/assets/css/frontend.css',
            array(),
            WOM_TOOLKIT_VERSION . '.2'
        );
    }
}