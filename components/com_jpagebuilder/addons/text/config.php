<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Language\Text;

// no direct accees
defined ( '_JEXEC' ) or die ( 'restricted access' );

JpagebuilderConfig::addonConfig ( [ 
		'type' => 'content',
		'addon_name' => 'text',
		'title' => Text::_ ( 'Text' ),
		'desc' => Text::_ ( 'A collection of useful utility classes to style your content.' ),
		'icon' => '<svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg" class=" uk-svg"> <rect x="2" y="8" width="26" height="2"></rect> <rect x="2" y="12" width="26" height="2"></rect> <rect x="2" y="16" width="26" height="2"></rect> <rect x="2" y="20" width="20" height="2"></rect> </svg>',
		'category' => 'Interface',
		'settings' => [ 
				'addon_title_options' => [ 
						'title' => Text::_ ( 'Block Title' ),
						'fields' => [ 
								'title_addon' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Title' ),
										'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_DESC' ),
										'std' => ''
								],
								'title_heading_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'Heading styles differ in font-size but may also come with a predefined color, size and font' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'heading-3xlarge' => Text::_ ( 'Heading 3X-Large' ),
												'heading-2xlarge' => Text::_ ( 'Heading 2X-Large' ),
												'heading-xlarge' => Text::_ ( 'Heading X-Large' ),
												'heading-large' => Text::_ ( 'Heading Large' ),
												'heading-medium' => Text::_ ( 'Heading Medium' ),
												'heading-small' => Text::_ ( 'Heading Small' ),
												'h1' => Text::_ ( 'Heading H1' ),
												'h2' => Text::_ ( 'Heading H2' ),
												'h3' => Text::_ ( 'Heading H3' ),
												'h4' => Text::_ ( 'Heading H4' ),
												'h5' => Text::_ ( 'Heading H5' ),
												'h6' => Text::_ ( 'Heading H6' ),
												'text-meta' => Text::_ ( 'Text Meta' ),
												'text-lead' => Text::_ ( 'Text Lead' ),
												'text-small' => Text::_ ( 'Text Small' ),
												'text-large' => Text::_ ( 'Text Large' )
										],
										'std' => 'h3',
										'inline' => true,
										'depends' => [ 
												[ 
														'title_addon',
														'!=',
														''
												]
										]
								],
								'heading_addon_margin' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Margin' ),
										'desc' => Text::_ ( 'Set the vertical margin. Note: The first element\'s top margin and the last element\'s bottom margin are always removed. Define those in the grid settings instead.' ),
										'values' => [ 
												'' => Text::_ ( 'Keep existing' ),
												'small' => Text::_ ( 'Small' ),
												'default' => Text::_ ( 'Default' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' ),
												'remove-vertical' => Text::_ ( 'None' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'title_addon',
														'!=',
														''
												]
										]
								],

								'title_heading_decoration' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Decoration' ),
										'desc' => Text::_ ( 'Decorate the heading with a divider, bullet or a line that is vertically centered to the heading' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'uk-heading-divider' => Text::_ ( 'Divider' ),
												'uk-heading-bullet' => Text::_ ( 'Bullet' ),
												'uk-heading-line' => Text::_ ( 'Line' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'title_addon',
														'!=',
														''
												]
										]
								],

								'title_heading_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Color' ),
										'desc' => Text::_ ( 'Select the text color. If the background option is selected, you can use Light or Dark color text mode to inverse the text style.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'text-muted' => Text::_ ( 'Muted' ),
												'text-emphasis' => Text::_ ( 'Emphasis' ),
												'text-primary' => Text::_ ( 'Primary' ),
												'text-secondary' => Text::_ ( 'Secondary' ),
												'text-success' => Text::_ ( 'Success' ),
												'text-warning' => Text::_ ( 'Warning' ),
												'text-danger' => Text::_ ( 'Danger' ),
												'text-background' => Text::_ ( 'Background' ),
												'light' => Text::_ ( 'Light' ),
												'dark' => Text::_ ( 'Dark' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'title_addon',
														'!=',
														''
												]
										]
								],

								'title_heading_selector' => [ 
										'type' => 'headings',
										'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS' ),
										'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS_DESC' ),
										'std' => 'h3',
										'depends' => [ 
												[ 
														'title_addon',
														'!=',
														''
												]
										]
								]
						]
				],

				'content' => [ 
						'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_CONTENT' ),
						'fields' => [ 
								'text' => [ 
										'type' => 'editor',
										'title' => Text::_ ( 'Content' ),
										'std' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
								]
						]
				],

				'separator_text_style_options' => [ 
						'title' => Text::_ ( 'Style' ),
						'fields' => [ 
								'title_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.ui-text { font-family: {{ VALUE }}; }'
										)
								],

								'dropcap' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Enable drop cap' ),
										'desc' => Text::_ ( 'Display the first letter of the paragraph as a large initial' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0
								],

								'dropcap_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_DROPCAP_COLOR' ),
										'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_DROPCAP_COLOR_DESC' ),
										'depends' => [ 
												[ 
														'dropcap',
														'=',
														1
												]
										]
								],

								'text_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Text Style' ),
										'desc' => Text::_ ( 'Select a predefined text style, including color, size and font-family.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'lead' => Text::_ ( 'Text Lead' ),
												'meta' => Text::_ ( 'Text Meta' )
										],
										'std' => '',
										'inline' => true
								],

								'text_size' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Text Size' ),
										'desc' => Text::_ ( 'Select the text size.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'small' => Text::_ ( 'Small' ),
												'large' => Text::_ ( 'Large' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'text_style',
														'!=',
														'lead'
												],
												[ 
														'text_style',
														'!=',
														'meta'
												]
										]
								],

								'text_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Text Color' ),
										'desc' => Text::_ ( 'Select the text color. If the Background option is selected, styles that don\'t apply a background image use the primary color instead.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'uk-text-muted' => Text::_ ( 'Muted' ),
												'uk-text-emphasis' => Text::_ ( 'Emphasis' ),
												'uk-text-primary' => Text::_ ( 'Primary' ),
												'uk-text-secondary' => Text::_ ( 'Secondary' ),
												'uk-text-success' => Text::_ ( 'Success' ),
												'uk-text-warning' => Text::_ ( 'Warning' ),
												'uk-text-danger' => Text::_ ( 'Danger' ),
												'uk-text-background' => Text::_ ( 'Background' )
										],
										'std' => '',
										'inline' => true
								],

								'custom_title_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Custom Color' ),
										'depends' => [ 
												[ 
														'text_color',
														'=',
														''
												]
										]
								],

								'columns' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Columns' ),
										'desc' => Text::_ ( 'Display your content in multiple text columns without having to split it in several containers.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'1-2' => Text::_ ( 'Halves' ),
												'1-3' => Text::_ ( 'Thirds' ),
												'1-4' => Text::_ ( 'Quarters' ),
												'1-5' => Text::_ ( 'Fifths' ),
												'1-6' => Text::_ ( 'Sixths' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'text',
														'!=',
														''
												]
										]
								],

								'divider' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Divider' ),
										'desc' => Text::_ ( 'Show dividers between the text columns.' ),
										'std' => 0,
										'depends' => [ 
												[ 
														'columns',
														'!=',
														''
												]
										]
								],

								'columns_breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Columns Breakpoint' ),
										'desc' => Text::_ ( 'Set the device width from which the text columns should apply. Note: For each breakpoint downward the number of columns will be reduced by one.' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												's' => Text::_ ( 'Small (Phone Landscape)' ),
												'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
												'l' => Text::_ ( 'Large (Desktop)' ),
												'xl' => Text::_ ( 'X-Large (Large Screens)' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'columns',
														'!=',
														''
												]
										]
								]
						]
				],

				'group_general_options' => [ 
						'title' => Text::_ ( 'General' ),
						'fields' => [ 
								'addon_margin' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Margin' ),
										'desc' => Text::_ ( 'Set the vertical margin. Note: The first element\'s top margin and the last element\'s bottom margin are always removed. Define those in the grid settings instead.' ),
										'values' => [ 
												'' => Text::_ ( 'Keep existing' ),
												'small' => Text::_ ( 'Small' ),
												'default' => Text::_ ( 'Default' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' ),
												'remove-vertical' => Text::_ ( 'None' )
										],
										'std' => '',
										'inline' => true
								],

								'addon_max_width' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Max Width' ),
										'desc' => Text::_ ( 'Set the maximum content width.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' ),
												'2xlarge' => Text::_ ( '2X-Large' )
										],
										'std' => '',
										'inline' => true
								],

								'addon_max_width_breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Max Width Breakpoint' ),
										'desc' => Text::_ ( 'Define the device width from which the element\'s max-width will apply.' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												's' => Text::_ ( 'Small (Phone Landscape)' ),
												'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
												'l' => Text::_ ( 'Large (Desktop)' ),
												'xl' => Text::_ ( 'X-Large (Large Screens)' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'addon_max_width',
														'!=',
														''
												]
										]
								],

								'block_align' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Block Alignment' ),
										'desc' => Text::_ ( 'Define the alignment in case the container exceeds the element\'s max-width.' ),
										'values' => [ 
												'' => Text::_ ( 'Left' ),
												'center' => Text::_ ( 'Center' ),
												'right' => Text::_ ( 'Right' )
										],
										'std' => '',
										'inline' => true,
										'depends' => array (
												array (
														'addon_max_width',
														'!=',
														''
												)
										)
								],

								'block_align_breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Block Alignment Breakpoint' ),
										'desc' => Text::_ ( 'Define the device width from which the alignment will apply.' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												's' => Text::_ ( 'Small (Phone Landscape)' ),
												'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
												'l' => Text::_ ( 'Large (Desktop)' ),
												'xl' => Text::_ ( 'X-Large (Large Screens)' )
										],
										'std' => '',
										'inline' => true,
										'depends' => array (
												array (
														'addon_max_width',
														'!=',
														''
												)
										)
								],

								'block_align_fallback' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Block Alignment Fallback' ),
										'desc' => Text::_ ( 'Define the alignment in case the container exceeds the element\'s max-width.' ),
										'values' => [ 
												'' => Text::_ ( 'Left' ),
												'center' => Text::_ ( 'Center' ),
												'right' => Text::_ ( 'Right' )
										],
										'std' => '',
										'inline' => true,
										'depends' => array (
												array (
														'addon_max_width',
														'!=',
														''
												),
												array (
														'block_align_breakpoint',
														'!=',
														''
												)
										)
								],

								'text_alignment' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Text Alignment' ),
										'desc' => Text::_ ( 'Center, left and right alignment.' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'left' => Text::_ ( 'Left' ),
												'center' => Text::_ ( 'Center' ),
												'right' => Text::_ ( 'Right' ),
												'justify' => Text::_ ( 'Justify' )
										],
										'std' => '',
										'inline' => true
								],

								'text_breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Text Alignment Breakpoint' ),
										'desc' => Text::_ ( 'Display the text alignment only on this device width and larger' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												's' => Text::_ ( 'Small (Phone Landscape)' ),
												'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
												'l' => Text::_ ( 'Large (Desktop)' ),
												'xl' => Text::_ ( 'X-Large (Large Screens)' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'text_alignment',
														'!=',
														''
												]
										]
								],

								'text_alignment_fallback' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Text Alignment Fallback' ),
										'desc' => Text::_ ( 'Define an alignment fallback for device widths below the breakpoint' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'left' => Text::_ ( 'Left' ),
												'center' => Text::_ ( 'Center' ),
												'right' => Text::_ ( 'Right' ),
												'justify' => Text::_ ( 'Justify' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'text_breakpoint',
														'!=',
														''
												],
												[ 
														'text_alignment',
														'!=',
														''
												]
										]
								],

								'animation' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Animation' ),
										'desc' => Text::_ ( 'A collection of smooth animations to use within your page.' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'fade' => Text::_ ( 'Fade' ),
												'scale-up' => Text::_ ( 'Scale Up' ),
												'scale-down' => Text::_ ( 'Scale Down' ),
												'slide-top-small' => Text::_ ( 'Slide Top Small' ),
												'slide-bottom-small' => Text::_ ( 'Slide Bottom Small' ),
												'slide-left-small' => Text::_ ( 'Slide Left Small' ),
												'slide-right-small' => Text::_ ( 'Slide Right Small' ),
												'slide-top-medium' => Text::_ ( 'Slide Top Medium' ),
												'slide-bottom-medium' => Text::_ ( 'Slide Bottom Medium' ),
												'slide-left-medium' => Text::_ ( 'Slide Left Medium' ),
												'slide-right-medium' => Text::_ ( 'Slide Right Medium' ),
												'slide-top' => Text::_ ( 'Slide Top 100%' ),
												'slide-bottom' => Text::_ ( 'Slide Bottom 100%' ),
												'slide-left' => Text::_ ( 'Slide Left 100%' ),
												'slide-right' => Text::_ ( 'Slide Right 100%' ),
												'parallax' => Text::_ ( 'Parallax' )
										],
										'std' => '',
										'inline' => true
								],

								'animation_repeat' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Repeat Animation' ),
										'desc' => Text::_ ( 'Applies the animation class every time the element is in view' ),
										'std' => 0,
										'depends' => [ 
												[ 
														'animation',
														'!=',
														''
												],
												[ 
														'animation',
														'!=',
														'parallax'
												]
										]
								],

								'horizontal_start' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Horizontal Start' ),
										'min' => - 600,
										'max' => 600,
										'desc' => Text::_ ( 'Animate the horizontal position (translateX) in pixels.' ),
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'horizontal_end' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Horizontal End' ),
										'min' => - 600,
										'max' => 600,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'vertical_start' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Vertical Start' ),
										'min' => - 600,
										'max' => 600,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'vertical_end' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Vertical End' ),
										'min' => - 600,
										'max' => 600,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'scale_start' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Scale Start' ),
										'min' => 50,
										'max' => 200,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'scale_end' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Scale End' ),
										'min' => 50,
										'max' => 200,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'rotate_start' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Rotate Start' ),
										'min' => 0,
										'max' => 360,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'rotate_end' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Rotate End' ),
										'min' => 0,
										'max' => 360,
										'desc' => Text::_ ( 'Animate the rotation clockwise in degrees.' ),
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'opacity_start' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Opacity Start' ),
										'min' => 0,
										'max' => 100,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'opacity_end' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Opacity End' ),
										'min' => 0,
										'max' => 100,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'easing' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Easing' ),
										'min' => - 200,
										'max' => 200,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'viewport' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Viewport' ),
										'min' => 10,
										'max' => 100,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'parallax_target' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Target' ),
										'std' => 0,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'parallax_zindex' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Z Index' ),
										'std' => 0,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Breakpoint' ),
										'desc' => Text::_ ( 'Display the parallax effect only on this device width and larger. It is useful to disable the parallax animation on small viewports.' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												's' => Text::_ ( 'Small (Phone)' ),
												'm' => Text::_ ( 'Medium (Tablet)' ),
												'l' => Text::_ ( 'Large (Desktop)' ),
												'xl' => Text::_ ( 'X-Large (Large Screens)' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'animation',
														'=',
														'parallax'
												]
										]
								],

								'visibility' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Visibility' ),
										'desc' => Text::_ ( 'Display the element only on this device width and larger.' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												'uk-visible@s' => Text::_ ( 'Small (Phone Landscape)' ),
												'uk-visible@m' => Text::_ ( 'Medium (Tablet Landscape)' ),
												'uk-visible@l' => Text::_ ( 'Large (Desktop)' ),
												'uk-visible@xl' => Text::_ ( 'X-Large (Large Screens)' ),
												'uk-hidden@s' => Text::_ ( 'Hidden Small (Phone Landscape)' ),
												'uk-hidden@m' => Text::_ ( 'Hidden Medium (Tablet Landscape)' ),
												'uk-hidden@l' => Text::_ ( 'Hidden Large (Desktop)' ),
												'uk-hidden@xl' => Text::_ ( 'Hidden X-Large (Large Screens)' )
										],
										'std' => '',
										'inline' => true
								]
						]
				]
		]
] );
