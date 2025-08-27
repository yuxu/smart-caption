/**
 * Editor script for Smart Caption block
 */

(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, RadioControl } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('smart-caption/universal-figure', {
        title: smartCaptionTranslations ? smartCaptionTranslations.universalFigure : __('Universal Figure', 'smart-caption'),
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

            // Get allowed blocks from PHP
            const allowedBlocks = smartCaptionData && smartCaptionData.allowedBlocks && smartCaptionData.allowedBlocks.length > 0 
                ? smartCaptionData.allowedBlocks 
                : ['core/list', 'core/quote', 'core/table', 'core/code'];

            return wp.element.createElement(
                wp.element.Fragment,
                null,
                wp.element.createElement(
                    InspectorControls,
                    null,
                    wp.element.createElement(
                        PanelBody,
                        { title: smartCaptionTranslations ? smartCaptionTranslations.figureSettings : __('Figure Settings', 'smart-caption') },
                        wp.element.createElement(TextControl, {
                            label: smartCaptionTranslations ? smartCaptionTranslations.caption : __('Caption', 'smart-caption'),
                            value: caption,
                            onChange: function(value) {
                                setAttributes({ caption: value });
                            }
                        }),
                        wp.element.createElement(RadioControl, {
                            label: smartCaptionTranslations ? smartCaptionTranslations.captionPosition : __('Caption Position', 'smart-caption'),
                            selected: position,
                            options: [
                                { label: smartCaptionTranslations ? smartCaptionTranslations.aboveContent : __('Above Content', 'smart-caption'), value: 'above' },
                                { label: smartCaptionTranslations ? smartCaptionTranslations.belowContent : __('Below Content', 'smart-caption'), value: 'below' },
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
                        wp.element.createElement(InnerBlocks, {
                            allowedBlocks: allowedBlocks,
                            renderAppender: InnerBlocks.ButtonBlockAppender
                        })
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
