<?php
/**
 * Plugin Name: Smart Caption
 * Plugin URI: https://example.com/smart-caption
 * Description: A WordPress plugin that provides a custom block for figure and figcaption elements in Gutenberg editor.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-caption
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SMART_CAPTION_VERSION', '1.0.0');
define('SMART_CAPTION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SMART_CAPTION_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once SMART_CAPTION_PLUGIN_DIR . 'includes/class-smart-caption.php';

// Initialize the plugin
function smart_caption_init() {
    $plugin = new Smart_Caption();
    $plugin->run();
}
add_action('plugins_loaded', 'smart_caption_init');
