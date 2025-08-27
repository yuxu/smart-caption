<?php
/**
 * Main plugin class for Smart Caption
 */

class Smart_Caption {

    /**
     * Initialize the plugin
     */
    public function run() {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
        add_action('enqueue_block_assets', array($this, 'enqueue_frontend_assets'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    /**
     * Register the custom block
     */
    public function register_block() {
        register_block_type('smart-caption/universal-figure', array(
            'editor_script' => 'smart-caption-editor',
            'editor_style' => 'smart-caption-editor',
            'style' => 'smart-caption-frontend',
            // 'render_callback' => array($this, 'render_block'), // Static block now
        ));
    }

    /**
     * Enqueue editor assets
     */
    public function enqueue_editor_assets() {
        wp_enqueue_script(
            'smart-caption-editor',
            SMART_CAPTION_PLUGIN_URL . 'assets/js/editor.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            SMART_CAPTION_VERSION,
            true
        );

        wp_enqueue_style(
            'smart-caption-editor',
            SMART_CAPTION_PLUGIN_URL . 'assets/css/editor.css',
            array(),
            SMART_CAPTION_VERSION
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'smart-caption-frontend',
            SMART_CAPTION_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            SMART_CAPTION_VERSION
        );
    }

    /**
     * Render the block on frontend
     */
    public function render_block($attributes, $content) {
        $caption = isset($attributes['caption']) ? $attributes['caption'] : '';
        $position = isset($attributes['position']) ? $attributes['position'] : 'below';

        $output = '<figure class="wp-block-smart-caption-universal-figure">';

        if ($position === 'above' && !empty($caption)) {
            $output .= '<figcaption>' . esc_html($caption) . '</figcaption>';
        }

        $output .= '<div class="figure-content">' . $content . '</div>';

        if ($position === 'below' && !empty($caption)) {
            $output .= '<figcaption>' . esc_html($caption) . '</figcaption>';
        }

        $output .= '</figure>';

        return $output;
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Smart Caption Settings',
            'Smart Caption',
            'manage_options',
            'smart-caption',
            array($this, 'settings_page')
        );
    }

    /**
     * Settings page callback
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Smart Caption Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('smart_caption_settings');
                do_settings_sections('smart_caption_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
