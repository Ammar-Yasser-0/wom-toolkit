<?php
/**
 * Plugin Name: Mirox Toolkit
 * Plugin URI: https://github.com/Ammar-Yasser-0/wom-toolkit
 * Description: Advanced modular toolkit for frontend enhancements and admin experience.
 * Version: 1.0.8
 * Author: Mirox
 * Author URI: https://ammaryasser.site
 * Text Domain: wom-toolkit
 */

if (!defined('ABSPATH')) {
    exit;
}

// Core Constants (DO NOT CHANGE SLUG)
define('WOM_TOOLKIT_PATH', plugin_dir_path(__FILE__));
define('WOM_TOOLKIT_URL', plugin_dir_url(__FILE__));
define('WOM_TOOLKIT_VERSION', '1.0.8');
define('WOM_TOOLKIT_SLUG', 'wom-toolkit');

// Branding
define('WOM_TOOLKIT_NAME', 'Mirox Toolkit');
define('WOM_TOOLKIT_AUTHOR', 'Mirox');

// GitHub updater config
define('WOM_TOOLKIT_GITHUB_REPO', 'Ammar-Yasser-0/wom-toolkit');
define('WOM_TOOLKIT_GITHUB_BRANCH', 'main');

// Core includes
require_once WOM_TOOLKIT_PATH . 'core/class-plugin.php';

// Boot plugin
function wom_toolkit_init()
{
    return \WOMToolkit\Core\Plugin::instance();
}
wom_toolkit_init();

// Settings link
function wom_toolkit_plugin_action_links($links)
{
    $settings_url = admin_url('admin.php?page=' . WOM_TOOLKIT_SLUG);
    $settings_link = '<a href="' . esc_url($settings_url) . '">Settings</a>';

    array_unshift($links, $settings_link);

    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wom_toolkit_plugin_action_links');