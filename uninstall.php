<?php
/**
 * Uninstall Smart Caption
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options if any
delete_option('smart_caption_settings');
