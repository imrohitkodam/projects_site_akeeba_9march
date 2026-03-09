<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

JpagebuilderConfig::addonConfig([
    'type'       => 'content',
    'addon_name' => 'tweet',
    'title'      => Text::_('COM_JPAGEBUILDER_ADDON_TWEET'),
    'desc'       => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_DESC'),
    'category'   => 'Media',
    'icon'       => '<svg viewBox="0 0 512 512" width="32" height="32" xmlns="http://www.w3.org/2000/svg"><path fill-rule="nonzero" d="M403.229 0h78.506L310.219 196.04 512 462.799H354.002L230.261 301.007 88.669 462.799h-78.56l183.455-209.683L0 0h161.999l111.856 147.88L403.229 0zm-27.556 415.805h43.505L138.363 44.527h-46.68l283.99 371.278z" fill="currentColor"/></svg>',
    'settings' => [
        'content' => [
            'title' => Text::_('COM_JPAGEBUILDER_GLOBAL_CONTENT'),
            'fields' => [
                'username' => [
                    'type'  => 'text',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_USERNAME'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_USERNAME_DESC'),
                    'std'   => 'storejoomla',
                    'inline' => true,
                ],

                'consumerkey' => [
                    'type'  => 'text',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_COUNSUMER_KEY'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_COUNSUMER_KEY_DESC'),
                    'inline' => true,
                ],

                'consumersecret' => [
                    'type'  => 'text',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_COUNSUMER_SECRETE'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_COUNSUMER_SECRETE_DESC'),
                    'inline' => true,
                ],

                'accesstoken' => [
                    'type'  => 'text',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_ACCESS_TOKEN'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_ACCESS_TOKEN_DESC'),
                    'inline' => true,
                ],

                'accesstokensecret' => [
                    'type'  => 'text',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_ACCESS_TOKEN_SECRETE'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_ACCESS_TOKEN_SECRETE_DESC'),
                    'inline' => true,
                ],
            ],
        ],
        
        'options' => [
            'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_OPTIONS'),
            'fields' => [
                'count' => [
                    'type'  => 'slider',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_COUNT'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_COUNT_DESC'),
                    'std'   => 5,
                ],

                'include_rts' => [
                    'type'   => 'radio',
                    'title'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_INCLUDE_RTS'),
                    'desc'   => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_INCLUDE_RTS_DESC'),
                    'values' => [
                        'true'  => Text::_('JYES'),
                        'false' => Text::_('JNO'),
                    ],
                    'std' => 'false',
                ],

                'ignore_replies' => [
                    'type'   => 'radio',
                    'title'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_IGNORE_REPLIES'),
                    'desc'   => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_IGNORE_REPLIES_DESC'),
                    'values' => [
                        'true'  => Text::_('JYES'),
                        'false' => Text::_('JNO'),
                    ],
                    'std' => 'false',
                ],

                'show_image' => [
                    'type'  => 'checkbox',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_SHOW_IMAGE'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_SHOW_IMAGE_DESC'),
                    'std'   => 1,
                ],

                'show_username' => [
                    'type'  => 'checkbox',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_SHOW_USERNAME'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_SHOW_USERNAME_DESC'),
                    'std'   => 0,
                ],

                'show_avatar' => [
                    'type'  => 'checkbox',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_SHOW_AVATAR'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_SHOW_AVATAR_DESC'),
                    'std'   => 1,
                ],

                'autoplay' => [
                    'type'  => 'checkbox',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_AUTOPLAY'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TWEET_AUTOPLAY_DESC'),
                    'std'   => 1,
                ],
            ]
        ],

        'title' => [
            'title' => Text::_('COM_JPAGEBUILDER_GLOBAL_TITLE_OPTIONS'),
            'fields' => [
                'title' => [
                    'type'  => 'text',
                    'title' => Text::_('COM_JPAGEBUILDER_ADDON_TITLE'),
                    'desc'  => Text::_('COM_JPAGEBUILDER_ADDON_TITLE_DESC'),
                ],
    
                'heading_selector' => [
                    'type'   => 'headings',
                    'title'  => Text::_('COM_JPAGEBUILDER_ADDON_HEADINGS'),
                    'desc'   => Text::_('COM_JPAGEBUILDER_ADDON_HEADINGS_DESC'),
                    'std'   => 'h3',
                ],

                'title_typography' => [
                    'type'   => 'typography',
                    'title'  => Text::_('COM_JPAGEBUILDER_GLOBAL_TYPOGRAPHY'),
                    'fallbacks'   => [
                        'font' => 'title_font_family',
                        'size' => 'title_fontsize',
                        'line_height' => 'title_lineheight',
                        'letter_spacing' => 'title_letterspace',
                        'uppercase' => 'title_font_style.uppercase',
                        'italic' => 'title_font_style.italic',
                        'underline' => 'title_font_style.underline',
                        'weight' => 'title_font_style.weight',
                    ],
                ],

                'title_text_color' => [
                    'type'   => 'color',
                    'title'  => Text::_('COM_JPAGEBUILDER_GLOBAL_COLOR'),
                ],

                'title_margin_top' => [
                    'type'       => 'slider',
                    'title'      => Text::_('COM_JPAGEBUILDER_GLOBAL_MARGIN_TOP'),
                    'max'        => 400,
                    'responsive' => true,
                ],
            
                'title_margin_bottom' => [
                    'type'       => 'slider',
                    'title'      => Text::_('COM_JPAGEBUILDER_GLOBAL_MARGIN_BOTTOM'),
                    'max'        => 400,
                    'responsive' => true,
                ],
            ],
        ],
    ],
]);
