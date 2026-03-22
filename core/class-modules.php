<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH'))
    exit;

class Modules
{

    private static $option_key = 'wom_toolkit_modules';

    public static function get_all()
    {
        return get_option(self::$option_key, array());
    }

    public static function is_enabled($module_id)
    {
        $modules = self::get_all();
        return isset($modules[$module_id]) ? (bool)$modules[$module_id] : false;
    }

    public static function update($data)
    {
        update_option(self::$option_key, $data);
    }
}