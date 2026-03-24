<?php
/**
 * Plugin Name: WOM Toolkit
 * Description: Internal modular toolkit for WordPress frontend enhancements.
 * Version: 1.0.4
 * Author: Mirox
 * Text Domain: wom-toolkit
 */

if (!defined('ABSPATH')) {
    exit;
}

// Constants
define('WOM_TOOLKIT_PATH', plugin_dir_path(__FILE__));
define('WOM_TOOLKIT_URL', plugin_dir_url(__FILE__));
define('WOM_TOOLKIT_VERSION', '1.0.4');
define('WOM_TOOLKIT_SLUG', 'wom-toolkit');

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

// Add Settings link in plugins page
function wom_toolkit_plugin_action_links($links)
{
    $settings_url = admin_url('admin.php?page=' . WOM_TOOLKIT_SLUG);
    $settings_link = '<a href="' . esc_url($settings_url) . '">Settings</a>';

    array_unshift($links, $settings_link);

    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wom_toolkit_plugin_action_links');