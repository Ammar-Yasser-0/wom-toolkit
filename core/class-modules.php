<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Modules
{
    private static $option_key = 'wom_toolkit_modules';

    public static function get_all()
    {
        $modules = get_option(self::$option_key, array());

        return is_array($modules) ? $modules : array();
    }

    public static function is_enabled($module_id)
    {
        $module_id = sanitize_key($module_id);
        $modules = self::get_all();

        return isset($modules[$module_id]) ? (bool)$modules[$module_id] : false;
    }

    public static function update($data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $sanitized = array();

        foreach ($data as $module_id => $enabled) {
            $module_id = sanitize_key($module_id);

            if (empty($module_id)) {
                continue;
            }

            $sanitized[$module_id] = $enabled ? 1 : 0;
        }

        update_option(self::$option_key, $sanitized);
    }
}