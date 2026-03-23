<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Module_Manager
{
    private static $instance = null;
    private $modules = array();

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->load_modules();
    }

    public function load_modules()
    {
        $modules_path = WOM_TOOLKIT_PATH . 'modules/';

        if (!is_dir($modules_path)) {
            return;
        }

        $folders = scandir($modules_path);

        if (!is_array($folders)) {
            return;
        }

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }

            $module_dir = $modules_path . $folder;

            if (!is_dir($module_dir)) {
                continue;
            }

            $module_file = $module_dir . '/class-module.php';

            if (!file_exists($module_file)) {
                continue;
            }

            require_once $module_file;

            $namespace = str_replace('-', '_', sanitize_key($folder));
            $class = "\\WOMToolkit\\Modules\\{$namespace}\\Module";

            if (!class_exists($class)) {
                continue;
            }

            $instance = new $class();

            if (!method_exists($instance, 'get_id')) {
                continue;
            }

            $module_id = sanitize_key($instance->get_id());

            if (empty($module_id)) {
                continue;
            }

            $this->modules[$module_id] = $instance;
        }
    }

    public function get_modules()
    {
        return $this->modules;
    }

    public function get_module($module_id)
    {
        $module_id = sanitize_key($module_id);

        return isset($this->modules[$module_id]) ? $this->modules[$module_id] : null;
    }
}