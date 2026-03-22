<?php
/**
 * Plugin Name: WOM Toolkit
 * Description: Internal modular toolkit for WordPress frontend enhancements.
 * Version: 1.0.0
 * Author: Word of Mouth
 */

if (!defined('ABSPATH'))
    exit;

// Constants
define('WOM_TOOLKIT_PATH', plugin_dir_path(__FILE__));
define('WOM_TOOLKIT_URL', plugin_dir_url(__FILE__));
define('WOM_TOOLKIT_VERSION', '1.0.0');

// Core includes
require_once WOM_TOOLKIT_PATH . 'core/class-plugin.php';

// Boot plugin
function wom_toolkit_init()
{
    return \WOMToolkit\Core\Plugin::instance();
}
wom_toolkit_init();