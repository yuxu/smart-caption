=== Smart Caption ===

Contributors: yuxu
Tags: gutenberg, block, figure, figcaption, caption, semantic, html5
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: smart-caption

A WordPress plugin that provides a custom block for figure and figcaption elements in Gutenberg editor with advanced block management.

== Description ==

Smart Caption provides a universal figure block that allows users to add captions to various content types while maintaining proper HTML5 semantics. The plugin includes advanced block whitelist management and supports multiple languages.

**Features:**
* Custom Gutenberg block for figure elements with figcaption support
* Configurable caption position (above or below content)
* Advanced admin settings to manage allowed inner blocks
* Block categorization (figure-related, semantically inappropriate)
* Real-time preview of allowed blocks
* Multilingual support (i18n ready)
* Semantic HTML5 compliance
* Accessibility focused

**Supported Content Types:**
* Lists (ul/ol)
* Quotes (blockquote)
* Code blocks
* Custom content blocks

**Excluded Content Types:**
* Media blocks (images, videos, audio) - use native figure support
* Structural blocks (paragraphs, headings, groups)
* Interactive blocks (buttons, forms)

== Installation ==

1. Upload the `smart-caption` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings > Smart Caption to configure allowed blocks.
4. The "Universal Figure" block will be available in the Gutenberg editor.

== Frequently Asked Questions ==

= How do I use the block? =

1. After activating the plugin, open the Gutenberg editor.
2. Search for "Universal Figure" block or find it in the Common blocks section.
3. Add your content inside the block.
4. Set a caption in the block settings panel.
5. Choose caption position (above or below content).

= How do I configure allowed blocks? =

Go to **Settings > Smart Caption** in your WordPress admin panel. There you can:
- Select which blocks are allowed inside the Universal Figure block
- View excluded blocks with explanations
- Search through available blocks
- See real-time preview of your selections

= What blocks are excluded and why? =

**Figure/Semantic Elements:** Blocks like images, videos, and tables that already use figure elements natively.

**Semantically Inappropriate:** Blocks like paragraphs, headings, and groups that don't belong in figure elements.

**List Items:** Must be inside list containers (ul/ol) to be valid HTML.

= Does this plugin support multiple languages? =

Yes! The plugin is fully internationalized and ready for translation. Translation files can be added to the `/languages/` directory.

== Screenshots ==

1. Universal Figure block in editor
2. Admin settings page for block management
3. Block categorization interface

== Changelog ==

= 1.0.0 =
* Initial release with universal figure block
* Advanced block whitelist management
* Multilingual support (i18n)
* Semantic HTML5 compliance
* Admin settings with real-time preview
* Block categorization system

== Upgrade Notice ==

= 1.0.0 =
Initial release - no upgrade needed.

== Credits ==

**Developed by:** Yuxu
**GitHub:** https://github.com/yuxu/smart-caption
**Website:** https://github.com/yuxu

This plugin was created to provide a better semantic solution for figure elements in WordPress Gutenberg editor.

**Special Thanks:**
- WordPress Core Team for the Gutenberg editor
- WordPress Community for coding standards and best practices

**Libraries/Tools Used:**
- WordPress Core APIs
- Gutenberg Block Editor
- PHP 7.0+
- JavaScript ES6

Developed with WordPress coding standards and accessibility guidelines (WCAG 2.1) in mind.

== Translation ==

The plugin is translation-ready. To contribute translations:

1. Use the .pot file in `/languages/smart-caption.pot`
2. Create .po and .mo files for your language
3. Name them as `smart-caption-{locale}.po/.mo`
4. Place them in the `/languages/` directory

Example: `smart-caption-ja.po` for Japanese translations.
