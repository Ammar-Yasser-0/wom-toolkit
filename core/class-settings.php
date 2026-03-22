<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH'))
    exit;

class Settings
{

    private static $option_key = 'wom_toolkit_settings';

    public static function get($module = null)
    {
        $settings = get_option(self::$option_key, array());

        if ($module) {
            return isset($settings[$module]) ? $settings[$module] : array();
        }

        return $settings;
    }

    public static function update($data)
    {
        update_option(self::$option_key, $data);
    }
}