<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Plugin
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
        $this->includes();
        $this->init_hooks();
    }

    private function includes()
    {
        require_once WOM_TOOLKIT_PATH . 'core/class-base-module.php';
        require_once WOM_TOOLKIT_PATH . 'core/class-modules.php';
        require_once WOM_TOOLKIT_PATH . 'core/class-settings.php';
        require_once WOM_TOOLKIT_PATH . 'core/class-admin.php';
        require_once WOM_TOOLKIT_PATH . 'core/class-module-manager.php';
        require_once WOM_TOOLKIT_PATH . 'core/class-updater.php';
    }

    private function init_hooks()
    {
        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        Admin::instance();
        Module_Manager::instance();
        Updater::instance();
    }
}