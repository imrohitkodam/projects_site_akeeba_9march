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
		'addon_name' => 'headingbox',
		'title' => Text::_ ( 'Heading' ),
		'desc' => Text::_ ( 'Define different styles for headings.' ),
		'icon' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M18.631 30v-1.648h.517c.445 0 .86-.032 1.248-.094.39-.065.727-.197 1.012-.394.286-.196.516-.482.688-.851.172-.37.257-.862.257-1.477v-9.19H9.65v9.19c0 .614.085 1.105.256 1.477.172.369.401.655.688.851.286.199.625.328 1.02.394.395.063.807.095 1.24.095h.517V30H2v-1.647h.497c.444 0 .86-.032 1.249-.095a2.524 2.524 0 001.021-.394c.292-.198.52-.482.688-.85.165-.37.246-.864.246-1.478V6.31c0-.574-.086-1.036-.257-1.388a2.007 2.007 0 00-.698-.814 2.506 2.506 0 00-1.022-.374 8.227 8.227 0 00-1.228-.086H2V2h11.37v1.647h-.517a7.68 7.68 0 00-1.24.096 2.456 2.456 0 00-1.02.393c-.286.197-.515.481-.688.852-.171.37-.256.862-.256 1.475v7.927H22.35V6.463c0-.614-.085-1.105-.256-1.476-.171-.37-.401-.655-.688-.851a2.452 2.452 0 00-1.012-.393 7.601 7.601 0 00-1.249-.096h-.516V2H30v1.647h-.496c-.445 0-.86.031-1.249.096a2.529 2.529 0 00-1.021.393c-.292.197-.52.481-.687.851-.167.37-.248.862-.248 1.476v19.265c0 .575.087 1.037.257 1.388.171.352.405.617.697.794.291.18.63.297 1.02.356.39.058.799.086 1.23.086H30V30H18.631z" fill="currentColor"/></svg>',
		'category' => 'Interface',
		'settings' => [ 
				'separator_heading_options' => [ 
						'title' => Text::_ ( 'Heading' ),
						'fields' => [ 
								'title' => [ 
										'type' => 'textarea',
										'title' => Text::_ ( 'Content' ),
										'desc' => Text::_ ( 'Enter your desired text to use as the addon heading title.' ),
										'std' => 'Heading Primary'
								],

								'title_link' => [ 
										'type' => 'link',
										'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK' )
								]
						]
				],

				'separator_style_options' => [ 
						'title' => Text::_ ( 'Style' ),
						'fields' => [ 
								'heading_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.tm-title { font-family: {{ VALUE }}; }'
										)
								],

								'heading_style' => [ 
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
										'std' => '',
										'inline' => true
								],

								'heading_font_weight' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Font weight' ),
										'desc' => Text::_ ( 'Add one of the following classes to modify the font weight of your text.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'light' => Text::_ ( 'Light' ),
												'normal' => Text::_ ( 'Normal' ),
												'bold' => Text::_ ( 'Bold' ),
												'lighter' => Text::_ ( 'Lighter' ),
												'bolder' => Text::_ ( 'Bolder' )
										],
										'inline' => true
								],

								'decoration' => [ 
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
										'inline' => true
								],

								'decoration_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Decoration Color' ),
										'depends' => [ 
												[ 
														'decoration',
														'!=',
														''
												]
										]
								],

								'decoration_width' => [ 
										'type' => 'slider',
										'min' => 1,
										'max' => 100,
										'std' => 1,
										'title' => Text::_ ( 'Decoration Width' ),
										'depends' => [ 
												[ 
														'decoration',
														'!=',
														''
												]
										]
								],

								'heading_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Color' ),
										'desc' => Text::_ ( 'Select the text color. If the Background option is selected, styles that don\'t apply a background image use the primary color instead.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'text-muted' => Text::_ ( 'Muted' ),
												'text-emphasis' => Text::_ ( 'Emphasis' ),
												'text-primary' => Text::_ ( 'Primary' ),
												'text-secondary' => Text::_ ( 'Secondary' ),
												'text-success' => Text::_ ( 'Success' ),
												'text-warning' => Text::_ ( 'Warning' ),
												'text-danger' => Text::_ ( 'Danger' ),
												'text-background' => Text::_ ( 'Background' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'title',
														'!=',
														''
												]
										]
								],

								'custom_heading_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Custom Color' ),
										'depends' => [ 
												[ 
														'title',
														'!=',
														''
												],
												[ 
														'heading_color',
														'=',
														''
												]
										]
								],

								'text_transform' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Transform' ),
										'desc' => Text::_ ( 'The following options will transform text into uppercased, capitalized or lowercased characters.' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'uppercase' => Text::_ ( 'Uppercase' ),
												'capitalize' => Text::_ ( 'Capitalize' ),
												'lowercase' => Text::_ ( 'Lowercase' )
										],
										'std' => '',
										'inline' => true
								],

								'heading_selector' => [ 
										'type' => 'headings',
										'title' => Text::_ ( 'HTML Element' ),
										'desc' => Text::_ ( 'Choose one of the eight heading elements to fit your semantic structure.' ),
										'std' => 'h1'
								]
						]
				],

				'group_general_options' => [ 
						'title' => Text::_ ( 'General' ),
						'fields' => [ 
								'parallax_bg' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Background Parallax' ),
										'desc' => Text::_ ( 'Upload a background image for this add-on with and add a parallax effect or fix the background with regard to the viewport while scrolling.' ),
										'std' => 0
								],

								'parallax_bg_image' => [ 
										'type' => 'media',
										'title' => Text::_ ( 'Background Image' ),
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_image_size' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Image Size' ),
										'desc' => Text::_ ( 'Determine whether the image will fit the section dimensions by clipping it or by filling the empty areas with the background color.' ),
										'values' => [ 
												'' => Text::_ ( 'Auto' ),
												'uk-background-cover' => Text::_ ( 'Cover' ),
												'uk-background-contain' => Text::_ ( 'Contain' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_image_effect' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Image Effect' ),
										'desc' => Text::_ ( 'Add a parallax effect or fix the background with regard to the viewport while scrolling.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'parallax' => Text::_ ( 'Parallax' ),
												'fixed' => Text::_ ( 'Fixed' )
										],
										'std' => 'parallax',
										'inline' => true,
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_horizontal_start' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Horizontal Start' ),
										'min' => - 600,
										'max' => 600,
										'desc' => Text::_ ( 'Animate the horizontal position (translateX) in pixels.' ),
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												],
												[ 
														'parallax_bg_image_effect',
														'=',
														'parallax'
												]
										]
								],

								'parallax_bg_horizontal_end' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Horizontal End' ),
										'min' => - 600,
										'max' => 600,
										'desc' => Text::_ ( 'Animate the horizontal position (translateX) in pixels.' ),
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												],
												[ 
														'parallax_bg_image_effect',
														'=',
														'parallax'
												]
										]
								],

								'parallax_bg_vertical_start' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Vertical Start' ),
										'min' => - 600,
										'max' => 600,
										'desc' => Text::_ ( 'Animate the vertical position (translateY) in pixels.' ),
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												],
												[ 
														'parallax_bg_image_effect',
														'=',
														'parallax'
												]
										]
								],

								'parallax_bg_vertical_end' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Vertical End' ),
										'min' => - 600,
										'max' => 600,
										'desc' => Text::_ ( 'Animate the vertical position (translateY) in pixels.' ),
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												],
												[ 
														'parallax_bg_image_effect',
														'=',
														'parallax'
												]
										]
								],

								'parallax_bg_easing' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Easing' ),
										'min' => - 200,
										'max' => 200,
										'desc' => Text::_ ( 'Set the animation easing. Zero transitions at an even speed, a positive value starts off quickly while a negative value starts off slowly.' ),
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												],
												[ 
														'parallax_bg_image_effect',
														'=',
														'parallax'
												]
										]
								],

								'parallax_bg_breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Parallax Breakpoint' ),
										'desc' => Text::_ ( 'Display the parallax effect only on this device width and larger. It is useful to disable the parallax animation on small viewports.' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												's' => Text::_ ( 'Small (Phone)' ),
												'm' => Text::_ ( 'Medium (Tablet)' ),
												'l' => Text::_ ( 'Large (Desktop)' ),
												'xl' => Text::_ ( 'X-Large (Large Screens)' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												],
												[ 
														'parallax_bg_image_effect',
														'=',
														'parallax'
												]
										]
								],

								'parallax_bg_position' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Position' ),
										'desc' => Text::_ ( 'Set the initial background position, relative to the section layer.' ),
										'values' => [ 
												'top-left' => Text::_ ( 'Top Left' ),
												'top-center' => Text::_ ( 'Top Center' ),
												'top-right' => Text::_ ( 'Top Right' ),
												'center-left' => Text::_ ( 'Center Left' ),
												'center-center' => Text::_ ( 'Center Center' ),
												'center-right' => Text::_ ( 'Center Right' ),
												'bottom-left' => Text::_ ( 'Bottom Left' ),
												'bottom-center' => Text::_ ( 'Bottom Center' ),
												'bottom-right' => Text::_ ( 'Bottom Right' )
										],
										'std' => 'center-center',
										'inline' => true,
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_maxwidth' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Max Width' ),
										'desc' => Text::_ ( 'Set the maximum content width.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'xsmall' => Text::_ ( 'X-Small' ),
												'small' => Text::_ ( 'Small' ),
												'large' => Text::_ ( 'Large' ),
												'expand' => Text::_ ( 'Expand' ),
												'none' => Text::_ ( 'None' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_padding' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Padding' ),
										'desc' => Text::_ ( 'Set the vertical padding for content inside parallax background.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'uk-section-xsmall' => Text::_ ( 'X-Small' ),
												'uk-section-small' => Text::_ ( 'Small' ),
												'uk-section-large' => Text::_ ( 'Large' ),
												'uk-section-xlarge' => Text::_ ( 'X-Large' ),
												'uk-padding-remove-vertical' => Text::_ ( 'None' )
										],
										'inline' => true,
										'std' => 'uk-section-large',
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_text_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Text Color' ),
										'desc' => Text::_ ( 'Set light or dark color mode for text, buttons and controls.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'uk-light' => Text::_ ( 'Light' ),
												'uk-dark' => Text::_ ( 'Dark' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Background Color' ),
										'desc' => Text::_ ( 'Use the background color in combination with blend modes, a transparent image or to fill the area, if the image doesn\'t cover the whole section.' ),
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_blend_modes' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Blend mode' ),
										'desc' => Text::_ ( 'Determine how the image will blend with the background color.' ),
										'values' => [ 
												'' => Text::_ ( 'Normal' ),
												'multiply' => Text::_ ( 'Multiply' ),
												'screen' => Text::_ ( 'Screen' ),
												'overlay' => Text::_ ( 'Overlay' ),
												'darken' => Text::_ ( 'Darken' ),
												'lighten' => Text::_ ( 'Lighten' ),
												'color-dodge' => Text::_ ( 'Color Dodge' ),
												'color-burn' => Text::_ ( 'Color Burn' ),
												'hard-light' => Text::_ ( 'Hard Light' ),
												'soft-light' => Text::_ ( 'Soft Light' ),
												'difference' => Text::_ ( 'Difference' ),
												'exclusion' => Text::_ ( 'Exclusion' ),
												'hue' => Text::_ ( 'Hue' ),
												'saturation' => Text::_ ( 'Saturation' ),
												'color' => Text::_ ( 'Color' ),
												'luminosity' => Text::_ ( 'Luminosity' )
										],
										'std' => 'overlay',
										'inline' => true,
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_overlay_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Overlay Color' ),
										'desc' => Text::_ ( 'Set an additional transparent overlay to soften the image.' ),
										'depends' => [ 
												[ 
														'parallax_bg',
														'=',
														1
												]
										]
								],

								'parallax_bg_image_visibility' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Image Visibility' ),
										'desc' => Text::_ ( 'Display the image only on this device width and larger.' ),
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
														'parallax_bg',
														'=',
														1
												]
										]
								],

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

								'alignment' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Text Alignment' ),
										'desc' => Text::_ ( 'Center, left and right alignment.' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'uk-text-left' => Text::_ ( 'Left' ),
												'uk-text-center' => Text::_ ( 'Center' ),
												'uk-text-right' => Text::_ ( 'Right' ),
												'uk-text-justify' => Text::_ ( 'Justify' )
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
														'alignment',
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
														'alignment',
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
										'title' => Text::_ ( 'Animation Repeat' ),
										'desc' => Text::_ ( 'Applies the animation class every time the element is in view.' ),
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
