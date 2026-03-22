<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH'))
    exit;

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

    public function __construct()
    {
        $this->load_modules();
    }

    public function load_modules()
    {
        $modules_path = WOM_TOOLKIT_PATH . 'modules/';

        if (!is_dir($modules_path)) {
            return;
        }

        foreach (scandir($modules_path) as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }

            $module_file = $modules_path . $folder . '/class-module.php';

            if (file_exists($module_file)) {
                require_once $module_file;

                $namespace = str_replace('-', '_', $folder);
                $class = "\\WOMToolkit\\Modules\\{$namespace}\\Module";

                if (class_exists($class)) {
                    $instance = new $class();

                    if (method_exists($instance, 'get_id')) {
                        $this->modules[$instance->get_id()] = $instance;
                    }
                    else {
                        $this->modules[$folder] = $instance;
                    }
                }
            }
        }
    }

    public function get_modules()
    {
        return $this->modules;
    }

    public function get_module($module_id)
    {
        return isset($this->modules[$module_id]) ? $this->modules[$module_id] : null;
    }
}