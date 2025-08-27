</**
 * Plugin Name: Smart Caption
 * Plugin URI: https://github.com/yuxu/smart-caption
 * Description: A WordPress plugin that provides a custom block for figure and figcaption elements in Gutenberg editor with advanced block management and multilingual support.
 * Version: 1.0.0
 * Author: Yuxu
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-caption
 * Domain Path: /languages
 */ Plugin Name: Smart Caption
 * Plugin URI: https://example.com/smart-caption
 * Description: A WordPress plugin that provides a custom block for figure and figcaption elements in Gutenberg editor with advanced block management and multilingual support.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-caption
 * Domain Path: /languages
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
    // Load plugin textdomain for internationalization
    load_plugin_textdomain(
        'smart-caption',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
    
    $plugin = new Smart_Caption();
    $plugin->run();
}
add_action('plugins_loaded', 'smart_caption_init');
