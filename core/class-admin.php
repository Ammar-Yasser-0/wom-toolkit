<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Admin
{
    private static $instance = null;

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_menu', array($this, 'register_menu'));
    }

    public function register_menu()
    {
        add_menu_page(
            'WOM Toolkit',
            'WOM Toolkit',
            'manage_options',
            WOM_TOOLKIT_SLUG,
            array($this, 'render_page'),
            'dashicons-admin-generic',
            58
        );
    }

    public function get_tabs()
    {
        $tabs = array(
            'dashboard' => 'Dashboard',
            'modules' => 'Modules',
        );

        $manager = Module_Manager::instance();
        $modules = $manager->get_modules();

        foreach ($modules as $module) {
            if (method_exists($module, 'has_admin_tab') && $module->has_admin_tab()) {
                $tabs[$module->get_id()] = $module->get_title();
            }
        }

        return $tabs;
    }

    public function render_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'wom-toolkit'));
        }

        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';
        $tabs = $this->get_tabs();

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('WOM Toolkit', 'wom-toolkit') . '</h1>';

        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $slug => $label) {
            $class = ($active_tab === $slug) ? 'nav-tab nav-tab-active' : 'nav-tab';
            $url = admin_url('admin.php?page=' . WOM_TOOLKIT_SLUG . '&tab=' . $slug);

            echo '<a href="' . esc_url($url) . '" class="' . esc_attr($class) . '">' . esc_html($label) . '</a>';
        }
        echo '</h2>';

        echo '<div style="margin-top:20px;">';

        switch ($active_tab) {
            case 'modules':
                $this->render_modules_tab();
                break;

            case 'dashboard':
                echo '<h2>' . esc_html__('Dashboard', 'wom-toolkit') . '</h2>';
                echo '<p>' . esc_html__('Overview later.', 'wom-toolkit') . '</p>';
                break;

            default:
                $manager = Module_Manager::instance();
                $module = $manager->get_module($active_tab);

                if ($module && method_exists($module, 'render_settings_page')) {
                    $module->render_settings_page();
                }
                else {
                    echo '<p>' . esc_html__('Tab not found.', 'wom-toolkit') . '</p>';
                }
                break;
        }

        echo '</div>';
        echo '</div>';
    }

    private function render_modules_tab()
    {
        $manager = Module_Manager::instance();
        $registered_modules = $manager->get_modules();

        if (isset($_POST['wom_save_modules'])) {
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You do not have permission to access this page.', 'wom-toolkit'));
            }

            check_admin_referer('wom_toolkit_save_modules', 'wom_toolkit_modules_nonce');

            $modules_input = isset($_POST['modules']) && is_array($_POST['modules']) ? $_POST['modules'] : array();
            $modules = array();

            foreach ($registered_modules as $module) {
                $module_id = sanitize_key($module->get_id());
                $modules[$module_id] = isset($modules_input[$module_id]) ? 1 : 0;
            }

            \WOMToolkit\Core\Modules::update($modules);

            echo '<div class="updated"><p>' . esc_html__('Modules saved.', 'wom-toolkit') . '</p></div>';
        }

        $saved_modules = \WOMToolkit\Core\Modules::get_all();

        echo '<form method="post">';
        wp_nonce_field('wom_toolkit_save_modules', 'wom_toolkit_modules_nonce');
        echo '<table class="form-table">';

        foreach ($registered_modules as $module) {
            $module_id = sanitize_key($module->get_id());
            $enabled = isset($saved_modules[$module_id]) ? $saved_modules[$module_id] : 0;

            echo '<tr>';
            echo '<th>' . esc_html($module->get_title()) . '</th>';
            echo '<td>';
            echo '<label>';
            echo '<input type="checkbox" name="modules[' . esc_attr($module_id) . ']" ' . checked($enabled, 1, false) . '> ';
            echo esc_html(sprintf(__('Enable %s', 'wom-toolkit'), $module->get_title()));
            echo '</label>';

            if (method_exists($module, 'get_description')) {
                echo '<p class="description">' . esc_html($module->get_description()) . '</p>';
            }

            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '<p><button class="button button-primary" name="wom_save_modules" value="1">' . esc_html__('Save Modules', 'wom-toolkit') . '</button></p>';
        echo '</form>';
    }
}