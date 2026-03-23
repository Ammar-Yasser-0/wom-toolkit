<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Settings
{
    private static $option_key = 'wom_toolkit_settings';

    public static function get($module = null)
    {
        $settings = get_option(self::$option_key, array());

        if (!is_array($settings)) {
            $settings = array();
        }

        if ($module !== null) {
            $module = sanitize_key($module);

            if (empty($module)) {
                return array();
            }

            $module_settings = isset($settings[$module]) ? $settings[$module] : array();

            return is_array($module_settings) ? $module_settings : array();
        }

        return $settings;
    }

    public static function update($data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $sanitized = array();

        foreach ($data as $module_id => $module_settings) {
            $module_id = sanitize_key($module_id);

            if (empty($module_id) || !is_array($module_settings)) {
                continue;
            }

            $sanitized[$module_id] = self::sanitize_recursive($module_settings);
        }

        update_option(self::$option_key, $sanitized);
    }

    private static function sanitize_recursive($value)
    {
        if (is_array($value)) {
            $sanitized = array();

            foreach ($value as $key => $item) {
                $sanitized_key = is_string($key) ? sanitize_key($key) : $key;
                $sanitized[$sanitized_key] = self::sanitize_recursive($item);
            }

            return $sanitized;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return $value;
        }

        return sanitize_text_field((string)$value);
    }
}