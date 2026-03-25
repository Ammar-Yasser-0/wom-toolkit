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
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function register_menu()
    {
        add_menu_page(
            WOM_TOOLKIT_NAME,
            WOM_TOOLKIT_NAME,
            'manage_options',
            WOM_TOOLKIT_SLUG,
            array($this, 'render_page'),
            'dashicons-admin-generic',
            58
        );
    }

    public function enqueue_assets($hook)
    {
        if (strpos($hook, WOM_TOOLKIT_SLUG) === false) {
            return;
        }

        wp_enqueue_style(
            'wom-toolkit-admin',
            WOM_TOOLKIT_URL . 'assets/admin/admin.css',
            array(),
            WOM_TOOLKIT_VERSION . '.2'
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

        echo '<div class="wrap wom-toolkit-admin">';
        echo '<div class="wom-toolkit-header">';
        echo '<div class="wom-toolkit-header__content">';
        echo '<div>';
        echo '<h1>' . esc_html(WOM_TOOLKIT_NAME) . '</h1>';
        echo '<p>' . esc_html__('Modular enhancements for frontend experience and admin control.', 'wom-toolkit') . '</p>';
        echo '</div>';
        echo '<div class="wom-toolkit-header__meta">';
        echo '<span class="wom-toolkit-badge">Version ' . esc_html(WOM_TOOLKIT_VERSION) . '</span>';
        echo '<a class="wom-toolkit-badge wom-toolkit-badge--dark wom-toolkit-badge--link" href="' . esc_url('https://ammaryasser.site') . '" target="_blank" rel="noopener noreferrer">' . esc_html__('by Mirox', 'wom-toolkit') . '</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<h2 class="nav-tab-wrapper wom-toolkit-tabs">';
        foreach ($tabs as $slug => $label) {
            $class = ($active_tab === $slug) ? 'nav-tab nav-tab-active' : 'nav-tab';
            $url = admin_url('admin.php?page=' . WOM_TOOLKIT_SLUG . '&tab=' . $slug);

            echo '<a href="' . esc_url($url) . '" class="' . esc_attr($class) . '">' . esc_html($label) . '</a>';
        }
        echo '</h2>';

        echo '<div class="wom-toolkit-content">';

        switch ($active_tab) {
            case 'modules':
                $this->render_modules_tab();
                break;

            case 'dashboard':
                $this->render_dashboard_tab();
                break;

            default:
                $manager = Module_Manager::instance();
                $module = $manager->get_module($active_tab);

                echo '<div class="wom-toolkit-panel">';

                if ($module && method_exists($module, 'render_settings_page')) {
                    $module_title = $module->get_title();
                    $module_description = method_exists($module, 'get_description') ? $module->get_description() : '';
                    $module_icon = $this->get_module_icon_text($module_title);

                    echo '<div class="wom-toolkit-panel__head wom-toolkit-panel__head--module">';
                    echo '<div class="wom-toolkit-panel__title-wrap">';
                    echo '<span class="wom-toolkit-module-icon wom-toolkit-module-icon--large">' . esc_html($module_icon) . '</span>';
                    echo '<div>';
                    echo '<h2>' . esc_html($module_title) . '</h2>';

                    if (!empty($module_description)) {
                        echo '<p>' . esc_html($module_description) . '</p>';
                    }

                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                    echo '<div class="wom-toolkit-panel__body wom-toolkit-settings-body">';
                    $module->render_settings_page();
                    echo '</div>';
                }
                else {
                    echo '<div class="wom-toolkit-panel__body">';
                    echo '<p>' . esc_html__('Tab not found.', 'wom-toolkit') . '</p>';
                    echo '</div>';
                }

                echo '</div>';
                break;
        }

        echo '</div>';
        echo '</div>';
    }

    private function render_dashboard_tab()
    {
        $manager = Module_Manager::instance();
        $modules = $manager->get_modules();
        $saved_modules = \WOMToolkit\Core\Modules::get_all();

        $enabled_count = 0;

        foreach ($modules as $module) {
            $module_id = sanitize_key($module->get_id());
            if (!empty($saved_modules[$module_id])) {
                $enabled_count++;
            }
        }

        echo '<div class="wom-toolkit-grid">';
        echo '<div class="wom-toolkit-card wom-toolkit-stat">';
        echo '<span class="wom-toolkit-stat__label">Registered Modules</span>';
        echo '<strong class="wom-toolkit-stat__value">' . esc_html(count($modules)) . '</strong>';
        echo '</div>';

        echo '<div class="wom-toolkit-card wom-toolkit-stat">';
        echo '<span class="wom-toolkit-stat__label">Enabled Modules</span>';
        echo '<strong class="wom-toolkit-stat__value">' . esc_html($enabled_count) . '</strong>';
        echo '</div>';

        echo '<div class="wom-toolkit-card wom-toolkit-stat">';
        echo '<span class="wom-toolkit-stat__label">Toolkit Version</span>';
        echo '<strong class="wom-toolkit-stat__value">' . esc_html(WOM_TOOLKIT_VERSION) . '</strong>';
        echo '</div>';
        echo '</div>';

        echo '<div class="wom-toolkit-panel">';
        echo '<div class="wom-toolkit-panel__head">';
        echo '<h2>' . esc_html__('Welcome to Mirox Toolkit', 'wom-toolkit') . '</h2>';
        echo '<p>' . esc_html__('Use the Modules tab to enable features and open each module tab to configure its settings.', 'wom-toolkit') . '</p>';
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

            echo '<div class="wom-toolkit-inline-notice wom-toolkit-inline-notice--success"><p>' . esc_html__('Modules saved.', 'wom-toolkit') . '</p></div>';
        }

        $saved_modules = \WOMToolkit\Core\Modules::get_all();

        echo '<form method="post">';
        wp_nonce_field('wom_toolkit_save_modules', 'wom_toolkit_modules_nonce');

        echo '<div class="wom-toolkit-modules-grid">';

        foreach ($registered_modules as $module) {
            $module_id = sanitize_key($module->get_id());
            $enabled = isset($saved_modules[$module_id]) ? $saved_modules[$module_id] : 0;
            $tab_url = admin_url('admin.php?page=' . WOM_TOOLKIT_SLUG . '&tab=' . $module_id);
            $title = $module->get_title();
            $description = method_exists($module, 'get_description') ? $module->get_description() : '';
            $icon_text = $this->get_module_icon_text($title);

            echo '<div class="wom-toolkit-module-card">';
            echo '<div class="wom-toolkit-module-card__head">';
            echo '<div class="wom-toolkit-module-card__title-group">';
            echo '<span class="wom-toolkit-module-icon">' . esc_html($icon_text) . '</span>';
            echo '<div>';
            echo '<h3>' . esc_html($title) . '</h3>';

            if (!empty($description)) {
                echo '<p>' . esc_html($description) . '</p>';
            }

            echo '</div>';
            echo '</div>';

            echo '<span class="wom-toolkit-status ' . ($enabled ? 'is-enabled' : 'is-disabled') . '">';
            echo $enabled ? esc_html__('Enabled', 'wom-toolkit') : esc_html__('Disabled', 'wom-toolkit');
            echo '</span>';
            echo '</div>';

            echo '<div class="wom-toolkit-module-card__footer">';
            echo '<label class="wom-toolkit-switch">';
            echo '<input type="checkbox" name="modules[' . esc_attr($module_id) . ']" ' . checked($enabled, 1, false) . '>';
            echo '<span class="wom-toolkit-switch__slider"></span>';
            echo '<span class="wom-toolkit-switch__label">' . esc_html__('Enable module', 'wom-toolkit') . '</span>';
            echo '</label>';

            if (method_exists($module, 'has_admin_tab') && $module->has_admin_tab()) {
                echo '<a class="button wom-toolkit-secondary-button" href="' . esc_url($tab_url) . '">' . esc_html__('Open Settings', 'wom-toolkit') . '</a>';
            }

            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        echo '<div class="wom-toolkit-actions">';
        echo '<button class="button button-primary wom-toolkit-save-button" name="wom_save_modules" value="1">' . esc_html__('Save Modules', 'wom-toolkit') . '</button>';
        echo '</div>';

        echo '</form>';
    }

    private function get_module_icon_text($title)
    {
        $words = preg_split('/\s+/', trim((string)$title));
        $icon = '';

        if (is_array($words)) {
            foreach ($words as $word) {
                if ($word === '') {
                    continue;
                }

                $icon .= strtoupper(function_exists('mb_substr') ? mb_substr($word, 0, 1) : substr($word, 0, 1));

                if (strlen($icon) >= 2) {
                    break;
                }
            }
        }

        if ($icon === '') {
            $icon = 'M';
        }

        return $icon;
    }
}