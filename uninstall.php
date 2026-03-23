<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete WOM Toolkit options
delete_option('wom_toolkit_settings');
delete_option('wom_toolkit_modules');