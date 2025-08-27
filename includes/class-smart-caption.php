<?php
/**
 * Main plugin class for Smart Caption
 *
 * @package SmartCaption
 * @since 1.0.0
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
        add_action('admin_init', array($this, 'register_settings'));
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

        // Set up script translations for JavaScript
        wp_set_script_translations('smart-caption-editor', 'smart-caption');

        // Fallback: Manually localize translations for JavaScript
        wp_localize_script('smart-caption-editor', 'smartCaptionTranslations', array(
            'universalFigure' => __('Universal Figure', 'smart-caption'),
            'figureSettings' => __('Figure Settings', 'smart-caption'),
            'caption' => __('Caption', 'smart-caption'),
            'captionPosition' => __('Caption Position', 'smart-caption'),
            'aboveContent' => __('Above Content', 'smart-caption'),
            'belowContent' => __('Below Content', 'smart-caption'),
        ));

        // Pass allowed blocks to JavaScript
        $allowed_blocks = get_option('smart_caption_allowed_blocks', $this->get_default_allowed_blocks());
        
        // Exclude Smart Caption block itself to prevent nested structures
        $allowed_blocks = array_diff($allowed_blocks, array('smart-caption/universal-figure'));
        
        wp_localize_script('smart-caption-editor', 'smartCaptionData', array(
            'allowedBlocks' => $allowed_blocks
        ));

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
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'smart_caption_settings',
            'smart_caption_allowed_blocks',
            array(
                'type' => 'array',
                'default' => $this->get_default_allowed_blocks(),
                'sanitize_callback' => array($this, 'sanitize_allowed_blocks')
            )
        );

        add_settings_section(
            'smart_caption_main',
            __('Block Whitelist Settings', 'smart-caption'),
            array($this, 'settings_section_callback'),
            'smart_caption_settings'
        );

        add_settings_field(
            'allowed_blocks',
            __('Allowed Blocks', 'smart-caption'),
            array($this, 'allowed_blocks_field_callback'),
            'smart_caption_settings',
            'smart_caption_main'
        );

        // Add custom save action
        add_action('update_option_smart_caption_allowed_blocks', array($this, 'on_settings_saved'), 10, 3);
    }

    /**
     * Get default allowed blocks
     */
    private function get_default_allowed_blocks() {
        return array(
            'core/list',
            'core/quote',
            'core/code'
        );
    }

    /**
     * Get blocks that use figure or similar semantic elements
     */
    private function get_figure_related_blocks() {
        return array(
            'core/image' => __('Image', 'smart-caption'),
            'core/gallery' => __('Gallery', 'smart-caption'),
            'core/audio' => __('Audio', 'smart-caption'),
            'core/video' => __('Video', 'smart-caption'),
            'core/embed' => __('Embed', 'smart-caption'),
            'core/table' => __('Table', 'smart-caption'),
        );
    }

    /**
     * Get blocks that are semantically inappropriate for figure wrapping
     */
    private function get_inappropriate_blocks() {
        return array(
            'core/paragraph' => __('Paragraph', 'smart-caption'),
            'core/heading' => __('Heading', 'smart-caption'),
            'core/list-item' => __('List Item', 'smart-caption'),
            'core/group' => __('Group', 'smart-caption'),
            'core/columns' => __('Columns', 'smart-caption'),
            'core/buttons' => __('Buttons', 'smart-caption'),
            'core/spacer' => __('Spacer', 'smart-caption'),
            'core/separator' => __('Separator', 'smart-caption'),
            'core/html' => __('Custom HTML', 'smart-caption'),
        );
    }

    /**
     * Sanitize allowed blocks
     */
    public function sanitize_allowed_blocks($input) {
        $sanitized = array();
        if (is_array($input)) {
            foreach ($input as $block_name) {
                $sanitized[] = sanitize_text_field($block_name);
            }
        }
        return $sanitized;
    }

    /**
     * Handle settings saved
     */
    public function on_settings_saved($old_value, $new_value, $option) {
        // Add success message
        add_settings_error(
            'smart_caption_settings',
            'settings_updated',
            __('Block whitelist settings saved successfully.', 'smart-caption'),
            'updated'
        );
    }

    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . __('Select which blocks are allowed inside the Universal Figure block.', 'smart-caption') . '</p>';
    }

    /**
     * Allowed blocks field callback
     */
    public function allowed_blocks_field_callback() {
        $allowed_blocks = get_option('smart_caption_allowed_blocks', $this->get_default_allowed_blocks());
        $all_blocks = $this->get_all_registered_blocks();
        $figure_blocks = $this->get_figure_related_blocks();
        $inappropriate_blocks = $this->get_inappropriate_blocks();

        echo '<div id="smart-caption-blocks-settings">';

        // Current allowed blocks preview (moved to top)
        echo '<h3>' . __('Currently Allowed Blocks', 'smart-caption') . '</h3>';
        echo '<div class="allowed-blocks-preview" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; padding: 15px; background: #e7f3ff; border: 1px solid #b3d7ff; border-radius: 4px;">';

        if (!empty($allowed_blocks)) {
            foreach ($allowed_blocks as $block_name) {
                if (isset($all_blocks[$block_name])) {
                    $block_info = $all_blocks[$block_name];
                    $title = isset($block_info['title']) ? $block_info['title'] : $block_name;
                    $category = isset($block_info['category']) ? $block_info['category'] : 'common';

                    echo '<span style="display: inline-flex; align-items: center; padding: 4px 8px; background: #fff; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">';
                    echo '<span style="font-weight: bold; margin-right: 4px;">' . esc_html($title) . '</span>';
                    echo '<span style="color: #666;">(' . esc_html($category) . ')</span>';
                    echo '</span>';
                }
            }
        } else {
            echo '<em style="color: #666;">' . __('No blocks selected', 'smart-caption') . '</em>';
        }

        echo '</div>';

        // Regular blocks section
        echo '<h3>' . __('Available Blocks', 'smart-caption') . '</h3>';
        echo '<div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin-bottom: 20px;">';
        echo '<p style="color: #495057; font-style: italic; margin-bottom: 10px;">' . __('Select which blocks are allowed inside the Universal Figure block.', 'smart-caption') . '</p>';
        echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 12px; margin-bottom: 15px;">';
        echo '<strong style="color: #856404;">' . __('HTML5 Semantic Guidance:', 'smart-caption') . '</strong><br>';
        echo '<span style="color: #856404; font-size: 14px; line-height: 1.4;">';
        echo __('The figure element is used to group self-contained content such as photos, diagrams, code blocks, etc., that can be referenced independently from the main text. You can add blocks to the whitelist that you deem appropriate for this definition based on your own judgment.', 'smart-caption') . '<br><br>';
        echo __('I created this plugin because I felt that figcaption is more appropriate than heading blocks for ul/ol and dl elements (added by other plugins).', 'smart-caption');
        echo '</span>';
        echo '</div>';
        echo '</div>';
        echo '<input type="text" id="block-search" placeholder="' . __('Search blocks...', 'smart-caption') . '" style="width: 100%; margin-bottom: 10px;">';
        echo '<div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">';

        foreach ($all_blocks as $block_name => $block_info) {
            // Skip figure-related and inappropriate blocks as they're shown below
            // Also skip Smart Caption block itself to prevent nested structures
            if (array_key_exists($block_name, $figure_blocks) || 
                array_key_exists($block_name, $inappropriate_blocks) || 
                $block_name === 'smart-caption/universal-figure') {
                continue;
            }

            $checked = in_array($block_name, $allowed_blocks) ? 'checked' : '';
            $title = isset($block_info['title']) ? $block_info['title'] : $block_name;
            $category = isset($block_info['category']) ? $block_info['category'] : 'common';

            // Add special note for table block
            $special_note = '';
            if ($block_name === 'core/table') {
                $special_note = '<br><small style="color: #666; font-style: italic;">' . __('Note: Table blocks have their own caption option', 'smart-caption') . '</small>';
            }

            echo '<label style="display: block; margin-bottom: 5px;">';
            echo '<input type="checkbox" name="smart_caption_allowed_blocks[]" value="' . esc_attr($block_name) . '" ' . $checked . '>';
            echo '<span class="block-title">' . esc_html($title) . '</span>';
            echo '<span class="block-name"> (' . esc_html($block_name) . ')</span>';
            echo '<span class="block-category"> - ' . esc_html($category) . '</span>';
            echo $special_note;
            echo '</label>';
        }

        echo '</div>';

        // Figure-related blocks section
        echo '<h3>' . __('Excluded Blocks - Figure/Semantic Elements', 'smart-caption') . '</h3>';
        echo '<p style="color: #666; font-style: italic;">' . __('These blocks already use figure or similar semantic elements and cannot be used inside Universal Figure to avoid nested structures.', 'smart-caption') . '</p>';
        echo '<div style="margin-bottom: 20px; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">';

        foreach ($figure_blocks as $block_name => $block_title) {
            if (isset($all_blocks[$block_name])) {
                echo '<label style="display: block; margin-bottom: 5px; opacity: 0.6;">';
                echo '<input type="checkbox" disabled>';
                echo '<span class="block-title">' . esc_html($block_title) . '</span>';
                echo '<span class="block-name"> (' . esc_html($block_name) . ')</span>';
                echo '<em style="color: #6c757d; margin-left: 10px;">' . __('WordPress standard figure/figcaption support', 'smart-caption') . '</em>';
                echo '</label>';
            }
        }

        echo '</div>';

        // Semantically inappropriate blocks section
        echo '<h3>' . __('Excluded Blocks - Semantically Inappropriate', 'smart-caption') . '</h3>';
        echo '<p style="color: #856404; font-style: italic;">' . __('These blocks are semantically inappropriate for figure wrapping and may cause accessibility issues or invalid HTML structure.', 'smart-caption') . '</p>';
        echo '<div style="margin-bottom: 20px; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;">';

        foreach ($inappropriate_blocks as $block_name => $block_title) {
            if (isset($all_blocks[$block_name])) {
                $reason = '';
                switch ($block_name) {
                    case 'core/list-item':
                        $reason = __('Must be inside a list container (ul/ol)', 'smart-caption');
                        break;
                    default:
                        $reason = __('Semantically inappropriate for figure', 'smart-caption');
                }
                
                echo '<label style="display: block; margin-bottom: 5px; opacity: 0.6;">';
                echo '<input type="checkbox" disabled>';
                echo '<span class="block-title">' . esc_html($block_title) . '</span>';
                echo '<span class="block-name"> (' . esc_html($block_name) . ')</span>';
                echo '<em style="color: #856404; margin-left: 10px;">' . esc_html($reason) . '</em>';
                echo '</label>';
            }
        }

        echo '</div>';
        echo '</div>';

        // Add JavaScript for search functionality and real-time updates
        ?>
        <script>
        // Function to update the allowed blocks preview
        function updateAllowedBlocksPreview() {
            const allowedBlocksContainer = document.querySelector('.allowed-blocks-preview');
            const checkboxes = document.querySelectorAll('input[name="smart_caption_allowed_blocks[]"]:checked');
            const allBlocks = <?php echo json_encode($all_blocks); ?>;

            console.log('Updating preview, checkboxes found:', checkboxes.length);
            console.log('Container found:', allowedBlocksContainer);
            console.log('All blocks:', allBlocks);

            if (!allowedBlocksContainer) {
                console.error('Allowed blocks container not found!');
                return;
            }

            // Clear current content
            console.log('Before clear, container HTML length:', allowedBlocksContainer.innerHTML.length);
            allowedBlocksContainer.innerHTML = '';
            console.log('After clear, container HTML length:', allowedBlocksContainer.innerHTML.length);

            if (checkboxes.length > 0) {
                checkboxes.forEach(function(checkbox) {
                    const blockName = checkbox.value;
                    console.log('Processing block:', blockName);
                    if (allBlocks[blockName]) {
                        const blockInfo = allBlocks[blockName];
                        const title = blockInfo.title || blockName;
                        const category = blockInfo.category || 'common';

                        const span = document.createElement('span');
                        span.style.display = 'inline-flex';
                        span.style.alignItems = 'center';
                        span.style.padding = '4px 8px';
                        span.style.background = '#fff';
                        span.style.border = '1px solid #ddd';
                        span.style.borderRadius = '4px';
                        span.style.fontSize = '12px';
                        span.style.marginRight = '4px';
                        span.style.marginBottom = '4px';

                        span.innerHTML = '<span style="font-weight: bold; margin-right: 4px;">' + title + '</span><span style="color: #666;">(' + category + ')</span>';
                        allowedBlocksContainer.appendChild(span);
                    }
                });
            } else {
                const em = document.createElement('em');
                em.style.color = '#666';
                em.textContent = 'No blocks selected';
                allowedBlocksContainer.appendChild(em);
            }
        }

        // Search functionality
        const searchInput = document.getElementById('block-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const labels = document.querySelectorAll('#smart-caption-blocks-settings label');

                labels.forEach(function(label) {
                    const text = label.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        label.style.display = 'block';
                    } else {
                        label.style.display = 'none';
                    }
                });
            });
        }

        // Real-time update of allowed blocks preview
        let isUpdating = false;
        document.addEventListener('change', function(e) {
            if (e.target.name === 'smart_caption_allowed_blocks[]' && !isUpdating) {
                isUpdating = true;
                console.log('Checkbox changed:', e.target.value, e.target.checked);
                updateAllowedBlocksPreview();
                setTimeout(() => { isUpdating = false; }, 100);
            }
        });

        // Handle form submission with custom save message
        const form = document.querySelector('form[action="options.php"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Show loading state
                const submitButton = form.querySelector('input[type="submit"]');
                if (submitButton) {
                    submitButton.value = 'Saving...';
                    submitButton.disabled = true;
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing preview');
            updateAllowedBlocksPreview();
        });
        </script>
        <?php
    }

    /**
     * Get all registered blocks
     */
    private function get_all_registered_blocks() {
        $blocks = array();

        if (class_exists('WP_Block_Type_Registry')) {
            $registry = WP_Block_Type_Registry::get_instance();
            $registered_blocks = $registry->get_all_registered();

            foreach ($registered_blocks as $block_name => $block_type) {
                // Exclude Smart Caption block itself to prevent nested structures
                if ($block_name === 'smart-caption/universal-figure') {
                    continue;
                }
                
                // Simple heuristic: exclude blocks that are likely inner-block only
                // This is not perfect but covers common cases
                $is_likely_inner_only = $this->is_likely_inner_block_only($block_name, $block_type);
                if ($is_likely_inner_only) {
                    continue;
                }
                
                $blocks[$block_name] = array(
                    'title' => $block_type->title,
                    'category' => $block_type->category,
                    'icon' => $block_type->icon
                );
            }
        }

        // Sort blocks by title
        uasort($blocks, function($a, $b) {
            return strcmp($a['title'], $b['title']);
        });

        return $blocks;
    }

    /**
     * Simple heuristic to detect inner-block only blocks
     * This is not perfect but covers common cases
     */
    private function is_likely_inner_block_only($block_name, $block_type) {
        // Known inner-block only blocks
        $known_inner_blocks = array(
            'core/list-item',
            'core/quote',
            // Add more known inner-block only blocks here
        );
        
        if (in_array($block_name, $known_inner_blocks)) {
            return true;
        }
        
        // Heuristic: blocks with 'item', 'child', or 'inner' in the name
        // are often inner-block only
        $inner_keywords = array('item', 'child', 'inner', 'sub');
        foreach ($inner_keywords as $keyword) {
            if (strpos($block_name, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Settings page callback
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Smart Caption Settings', 'smart-caption'); ?></h1>
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('smart_caption_settings');
                do_settings_sections('smart_caption_settings');
                submit_button(__('Save Changes', 'smart-caption'));
                ?>
            </form>
        </div>
        <?php
    }
}
