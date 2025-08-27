/**
 * Editor script for Smart Caption block
 */

(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, RadioControl } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('smart-caption/universal-figure', {
        title: __('Universal Figure', 'smart-caption'),
        icon: 'format-image',
        category: 'common',
        attributes: {
            caption: {
                type: 'string',
                default: '',
            },
            position: {
                type: 'string',
                default: 'below',
            },
        },
        edit: function({ attributes, setAttributes }) {
            const blockProps = useBlockProps();
            const { caption, position } = attributes;

            return wp.element.createElement(
                wp.element.Fragment,
                null,
                wp.element.createElement(
                    InspectorControls,
                    null,
                    wp.element.createElement(
                        PanelBody,
                        { title: __('Figure Settings', 'smart-caption') },
                        wp.element.createElement(TextControl, {
                            label: __('Caption', 'smart-caption'),
                            value: caption,
                            onChange: function(value) {
                                setAttributes({ caption: value });
                            }
                        }),
                        wp.element.createElement(RadioControl, {
                            label: __('Caption Position', 'smart-caption'),
                            selected: position,
                            options: [
                                { label: __('Above Content', 'smart-caption'), value: 'above' },
                                { label: __('Below Content', 'smart-caption'), value: 'below' },
                            ],
                            onChange: function(value) {
                                setAttributes({ position: value });
                            }
                        })
                    )
                ),
                wp.element.createElement(
                    'div',
                    blockProps,
                    position === 'above' && caption && wp.element.createElement('figcaption', null, caption),
                    wp.element.createElement(
                        'div',
                        { className: 'figure-content' },
                        wp.element.createElement(InnerBlocks, null)
                    ),
                    position === 'below' && caption && wp.element.createElement('figcaption', null, caption)
                )
            );
        },
        save: function({ attributes }) {
            const { caption, position } = attributes;

            return wp.element.createElement(
                'figure',
                { className: 'wp-block-smart-caption-universal-figure' },
                position === 'above' && caption && wp.element.createElement('figcaption', null, caption),
                wp.element.createElement(
                    'div',
                    { className: 'figure-content' },
                    wp.element.createElement(InnerBlocks.Content, null)
                ),
                position === 'below' && caption && wp.element.createElement('figcaption', null, caption)
            );
        },
    });
})(window.wp);
