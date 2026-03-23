<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH')) {
    exit;
}

abstract class Base_Module
{
    abstract public function get_id();

    abstract public function get_title();

    public function get_description()
    {
        return '';
    }

    public function has_admin_tab()
    {
        return true;
    }

    public function render_settings_page()
    {
        echo '<p>' . esc_html__('No settings available for this module yet.', 'wom-toolkit') . '</p>';
    }

    public function is_enabled()
    {
        if (!class_exists('\WOMToolkit\Core\Modules')) {
            return false;
        }

        return \WOMToolkit\Core\Modules::is_enabled($this->get_id());
    }
}