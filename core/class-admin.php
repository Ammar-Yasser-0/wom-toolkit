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

    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_menu'));
    }

    public function register_menu()
    {
        add_menu_page(
            'WOM Toolkit',
            'WOM Toolkit',
            'manage_options',
            'wom-toolkit',
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
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';
        $tabs = $this->get_tabs();

        echo '<div class="wrap">';
        echo '<h1>WOM Toolkit</h1>';

        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $slug => $label) {
            $class = ($active_tab === $slug) ? 'nav-tab nav-tab-active' : 'nav-tab';
            echo '<a href="?page=wom-toolkit&tab=' . esc_attr($slug) . '" class="' . esc_attr($class) . '">' . esc_html($label) . '</a>';
        }
        echo '</h2>';

        echo '<div style="margin-top:20px;">';

        switch ($active_tab) {
            case 'modules':
                $this->render_modules_tab();
                break;

            case 'dashboard':
                echo '<h2>Dashboard</h2>';
                echo '<p>Overview later.</p>';
                break;

            default:
                $manager = Module_Manager::instance();
                $module = $manager->get_module($active_tab);

                if ($module && method_exists($module, 'render_settings_page')) {
                    $module->render_settings_page();
                }
                else {
                    echo '<p>Tab not found.</p>';
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
            $modules = array();

            foreach ($registered_modules as $module) {
                $modules[$module->get_id()] = isset($_POST['modules'][$module->get_id()]) ? 1 : 0;
            }

            \WOMToolkit\Core\Modules::update($modules);

            echo '<div class="updated"><p>Modules saved.</p></div>';
        }

        $saved_modules = \WOMToolkit\Core\Modules::get_all();

        echo '<form method="post">';
        echo '<table class="form-table">';

        foreach ($registered_modules as $module) {
            $module_id = $module->get_id();
            $enabled = isset($saved_modules[$module_id]) ? $saved_modules[$module_id] : 0;

            echo '<tr>';
            echo '<th>' . esc_html($module->get_title()) . '</th>';
            echo '<td>';
            echo '<label>';
            echo '<input type="checkbox" name="modules[' . esc_attr($module_id) . ']" ' . checked($enabled, 1, false) . '> ';
            echo 'Enable ' . esc_html($module->get_title());
            echo '</label>';

            if (method_exists($module, 'get_description')) {
                echo '<p class="description">' . esc_html($module->get_description()) . '</p>';
            }

            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '<p><button class="button button-primary" name="wom_save_modules">Save Modules</button></p>';
        echo '</form>';
    }
}