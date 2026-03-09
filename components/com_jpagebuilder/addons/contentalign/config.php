<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct accees
defined ( '_JEXEC' ) or die ( 'restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

JpagebuilderConfig::addonConfig ( array (
		'type' => 'content',
		'addon_name' => 'contentalign',
		'title' => Text::_ ( 'Content Align' ),
		'desc' => Text::_ ( 'Control the alignment of inline elements depending on the viewport size.' ),
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/contentalign/assets/images/icon.png',
		'category' => 'Interface',
		'attr' => array (
				'general' => array (
						'admin_label' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL_DESC' ),
								'std' => ''
						),
						'card_title' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Title' ),
								'std' => 'Title'
						),
						// Repeatable Items
						'ui_slideshow_items' => array (
								'title' => Text::_ ( 'Image Items' ),
								'attr' => array (
										'media_item' => array (
												'type' => 'media',
												'title' => Text::_ ( 'Select Image' ),
												'placeholder' => 'http://www.example.com/my-photo.jpg',
												'std' => 'https://storejextensions.org/cdn/addons/carousel-bg.jpg'
										),
										'image_alt' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Image Alt' ),
												'std' => 'Image Alt'
										),
										'image_panel' => array (
												'type' => 'checkbox',
												'title' => Text::_ ( 'Blend Mode Settings' ),
												'values' => array (
														1 => Text::_ ( 'JYES' ),
														0 => Text::_ ( 'JNO' )
												),
												'std' => 0
										),
										'media_background' => array (
												'type' => 'color',
												'title' => Text::_ ( 'Background Color' ),
												'desc' => Text::_ ( 'Use the background color in combination with blend modes.' ),
												'depends' => array (
														array (
																'image_panel',
																'=',
																1
														)
												)
										),
										'media_blend_mode' => array (
												'type' => 'select',
												'title' => Text::_ ( 'Blend modes' ),
												'desc' => Text::_ ( 'Determine how the image will blend with the background color.' ),
												'values' => array (
														'' => Text::_ ( 'None' ),
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
														'color' => Text::_ ( 'Color' ),
														'luminosity' => Text::_ ( 'Luminosity' )
												),
												'std' => '',
												'depends' => array (
														array (
																'image_panel',
																'=',
																1
														),
														array (
																'media_background',
																'!=',
																''
														)
												)
										),
										'media_overlay' => array (
												'type' => 'color',
												'title' => Text::_ ( 'Overlay Color' ),
												'desc' => Text::_ ( 'Set an additional transparent overlay to soften the image.' ),
												'depends' => array (
														array (
																'image_panel',
																'=',
																1
														)
												)
										),
										'navigation_image_item' => array (
												'type' => 'media',
												'title' => Text::_ ( 'Navigation Thumbnail' ),
												'desc' => Text::_ ( 'This option is only used if the thumbnail navigation is set.' ),
												'placeholder' => 'http://www.example.com/my-photo.jpg'
										)
								)
						),
						'text' => array (
								'type' => 'editor',
								'title' => Text::_ ( 'Content' ),
								'std' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>'
						),
						'title_link' => array (
								'type' => 'media',
								'format' => 'attachment',
								'title' => Text::_ ( 'Link' ),
								'placeholder' => 'http://www.example.com',
								'hide_preview' => true
						),
						'link_new_tab' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB_DESC' ),
								'values' => array (
										'' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_SAME_WINDOW' ),
										'_blank' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_NEW_WINDOW' )
								),
								'depends' => array (
										array (
												'title_link',
												'!=',
												''
										)
								)
						),
						'button_title' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Link Text' ),
								'std' => 'Read more',
								'placeholder' => 'Read more',
								'depends' => array (
										array (
												'title_link',
												'!=',
												''
										)
								)
						),
						'separator_slideshow_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'SlideShow' )
						),
						'ratio' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Ratio' ),
								'desc' => Text::_ ( 'Set a ratio. It\'s recommended to use the same ratio of the background image. Just use its width and height, like 1600:900' ),
								'std' => '',
								'placeholder' => '16:9',
								'depends' => array (
										array (
												'height',
												'=',
												''
										)
								)
						),
						'min_height' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Min Height' ),
								'min' => 200,
								'max' => 800,
								'desc' => Text::_ ( 'Set the minimum height. This is useful if the content is too large on small devices' ),
								'std' => 300
						),
						'max_height' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Max Height' ),
								'min' => 500,
								'max' => 1600,
								'desc' => Text::_ ( 'Set the maximum height' )
						),
						'box_shadow' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Box Shadow' ),
								'desc' => Text::_ ( 'Select the slideshow\'s box shadow size.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' )
								),
								'std' => ''
						),

						'separator_animations_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Animation' )
						),
						'slideshow_transition' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Transition' ),
								'desc' => Text::_ ( 'Select the transition between two slides' ),
								'values' => array (
										'' => Text::_ ( 'Slide' ),
										'pull' => Text::_ ( 'Pull' ),
										'push' => Text::_ ( 'Push' ),
										'fade' => Text::_ ( 'Fade' ),
										'scale' => Text::_ ( 'Scale' )
								),
								'std' => ''
						),
						'velocity' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Velocity' ),
								'desc' => Text::_ ( 'Set the velocity in pixels per milliseconds.' ),
								'min' => 20,
								'max' => 300
						),
						'autoplay' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Autoplay' ),
								'desc' => Text::_ ( 'To activate Slideslide autoplays to the attribute. ' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'pause' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Pause autoplay on hover' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'autoplay',
												'=',
												1
										)
								)
						),
						'autoplay_interval' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Interval' ),
								'desc' => Text::_ ( 'Set the autoplay interval in seconds.' ),
								'placeholder' => '7',
								'min' => 5,
								'max' => 15,
								'depends' => array (
										array (
												'autoplay',
												'=',
												1
										)
								)
						),
						'kenburns_transition' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Ken Burns Effect' ),
								'desc' => Text::_ ( 'Select the transformation origin for the Ken Burns animation' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'top-left' => Text::_ ( 'Top Left' ),
										'top-center' => Text::_ ( 'Top Center' ),
										'top-right' => Text::_ ( 'Top Right' ),
										'center-left' => Text::_ ( 'Center Left' ),
										'center-center' => Text::_ ( 'Center Center' ),
										'center-right' => Text::_ ( 'Center Right' ),
										'bottom-left' => Text::_ ( 'Bottom Left' ),
										'bottom-center' => Text::_ ( 'Bottom Center' ),
										'bottom-right' => Text::_ ( 'Bottom Right' )
								),
								'std' => ''
						),
						'kenburns_duration' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Duration' ),
								'min' => 10,
								'max' => 30,
								'std' => 15,
								'desc' => Text::_ ( 'Set the duration for the Ken Burns effect in seconds.' ),
								'depends' => array (
										array (
												'kenburns_transition',
												'!=',
												''
										)
								)
						),
						'separator_navigation_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Navigation' )
						),
						'navigation' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Navigation Display' ),
								'desc' => Text::_ ( 'Select the navigation type.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'dotnav' => Text::_ ( 'Dotnav' ),
										'thumbnav' => Text::_ ( 'Thumbnav' )
								),
								'std' => 'dotnav'
						),
						'navigation_below' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Show below slideshow' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_below_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Position' ),
								'desc' => Text::_ ( 'Select the position of the navigation.' ),
								'values' => array (
										'left' => Text::_ ( 'Left' ),
										'center' => Text::_ ( 'Center' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => 'center',
								'depends' => array (
										array (
												'navigation_below',
												'=',
												1
										),
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_below_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'values' => array (
										'small-top' => Text::_ ( 'Small' ),
										'top' => Text::_ ( 'Default' ),
										'medium-top' => Text::_ ( 'Medium' )
								),
								'std' => 'top',
								'depends' => array (
										array (
												'navigation_below',
												'=',
												1
										),
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_vertical' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Vertical navigation' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'navigation_below',
												'!=',
												1
										),
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Position' ),
								'desc' => Text::_ ( 'Select the position of the navigation.' ),
								'values' => array (
										'top-left' => Text::_ ( 'Top Left' ),
										'top-right' => Text::_ ( 'Top Right' ),
										'center-left' => Text::_ ( 'Center Left' ),
										'center-right' => Text::_ ( 'Center Right' ),
										'bottom-left' => Text::_ ( 'Bottom Left' ),
										'bottom-center' => Text::_ ( 'Bottom Center' ),
										'bottom-right' => Text::_ ( 'Bottom Right' )
								),
								'std' => 'bottom-center',
								'depends' => array (
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => 'medium',
								'depends' => array (
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Breakpoint' ),
								'desc' => Text::_ ( 'Display the navigation only on this device width and larger' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 's',
								'depends' => array (
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Color' ),
								'desc' => Text::_ ( 'Set light or dark color if the navigation is below the slideshow.' ),
								'values' => array (
										'light' => Text::_ ( 'Light' ),
										'' => Text::_ ( 'None' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'thumbnav_wrap' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Thumbnav Wrap' ),
								'desc' => Text::_ ( 'Don\'t wrap into multiple lines. Define whether thumbnails wrap into multiple lines or not if the container is too small.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'navigation',
												'=',
												'thumbnav'
										)
								)
						),
						'thumbnail_width' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Thumbnail Width' ),
								'min' => 0,
								'max' => 400,
								'desc' => Text::_ ( 'Settings just one value preserves the original proportions. The image will be resized and croped automatically, and where possible, high resolution images will be auto-generated.' ),
								'std' => 100,
								'depends' => array (
										array (
												'navigation',
												'=',
												'thumbnav'
										)
								)
						),

						'thumbnail_height' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Thumbnail Height' ),
								'min' => 0,
								'max' => 400,
								'desc' => Text::_ ( 'Settings just one value preserves the original proportions. The image will be resized and croped automatically, and where possible, high resolution images will be auto-generated.' ),
								'std' => 75,
								'depends' => array (
										array (
												'navigation',
												'=',
												'thumbnav'
										)
								)
						),
						'image_svg_inline' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Make SVG stylable with CSS' ),
								'desc' => Text::_ ( 'Inject SVG images into the page markup, so that they can easily be styled with CSS.' ),
								'std' => 0,
								'depends' => array (
										array (
												'navigation',
												'=',
												'thumbnav'
										)
								)
						),
						'image_svg_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'SVG Color' ),
								'desc' => Text::_ ( 'Select the SVG color. It will only apply to supported elements defined in the SVG.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'muted' => Text::_ ( 'Muted' ),
										'emphasis' => Text::_ ( 'Emphasis' ),
										'primary' => Text::_ ( 'Primary' ),
										'secondary' => Text::_ ( 'Secondary' ),
										'success' => Text::_ ( 'Success' ),
										'warning' => Text::_ ( 'Warning' ),
										'danger' => Text::_ ( 'Danger' )
								),
								'std' => '',
								'depends' => array (
										array (
												'navigation',
												'=',
												'thumbnav'
										),
										array (
												'image_svg_inline',
												'=',
												1
										)
								)
						),
						'separator_slidenav_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'SlideNav' )
						),
						'slidenav_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Position' ),
								'desc' => Text::_ ( 'Select the position of the slidenav.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'default' => Text::_ ( 'Default' ),
										'outside' => Text::_ ( 'Outside' ),
										'top-left' => Text::_ ( 'Top Left' ),
										'top-right' => Text::_ ( 'Top Right' ),
										'center-left' => Text::_ ( 'Center Left' ),
										'center-right' => Text::_ ( 'Center Right' ),
										'bottom-left' => Text::_ ( 'Bottom Left' ),
										'bottom-center' => Text::_ ( 'Bottom Center' ),
										'bottom-right' => Text::_ ( 'Bottom Right' )
								),
								'std' => 'default'
						),
						'slidenav_on_hover' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Show on hover only' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),
						'larger_style' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Larger style' ),
								'desc' => Text::_ ( 'To increase the size of the slidenav icons' ),
								'values' => array (
										'0' => Text::_ ( 'JYES' ),
										'1' => Text::_ ( 'JNO' )
								),
								'std' => '0',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),
						'slidenav_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'desc' => Text::_ ( 'Apply a margin between the slidnav and the slideshow container.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => 'medium',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),
						'slidenav_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Breakpoint' ),
								'desc' => Text::_ ( 'Display the slidenav on this device width and larger.' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 's',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),

						'slidenav_outside_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Outside Breakpoint' ),
								'desc' => Text::_ ( 'Display the slidenav only outside on this device width and larger. Otherwise it will be displayed inside' ),
								'values' => array (
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 'xl',
								'depends' => array (
										array (
												'slidenav_position',
												'=',
												'outside'
										)
								)
						),
						'slidenav_outside_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Color' ),
								'desc' => Text::_ ( 'Set light or dark color for slidenav.' ),
								'values' => array (
										'light' => Text::_ ( 'Light' ),
										'' => Text::_ ( 'Default' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),
						'separator_img_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Image' )
						),
						'image_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Alignment' ),
								'desc' => Text::_ ( 'Align the image to the top,left,right or place it between the title and the content' ),
								'values' => array (
										'top' => Text::_ ( 'Top' ),
										'bottom' => Text::_ ( 'Bottom' ),
										'left' => Text::_ ( 'Left' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => 'top'
						),
						'grid_width' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Width' ),
								'desc' => Text::_ ( 'Define the width of the image within the grid. Choose between percent and fixed widths or expand columns to the width of their content.' ),
								'values' => array (
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'2xlarge' => Text::_ ( '2X-Large' )
								),
								'std' => '1-2',
								'depends' => array (
										array (
												'image_alignment',
												'!=',
												'top'
										),
										array (
												'image_alignment',
												'!=',
												'bottom'
										)
								)
						),
						'grid_column_gap' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Column Gap' ),
								'desc' => Text::_ ( 'Set the size of the gap between the grid columns.' ),
								'values' => array (
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'' => Text::_ ( 'Default' ),
										'large' => Text::_ ( 'Large' ),
										'collapse' => Text::_ ( 'None' )
								),
								'std' => '',
								'depends' => array (
										array (
												'image_alignment',
												'!=',
												'top'
										),
										array (
												'image_alignment',
												'!=',
												'bottom'
										)
								)
						),
						'grid_row_gap' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Row Gap' ),
								'desc' => Text::_ ( 'Set the size of the gap between the grid rows.' ),
								'values' => array (
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'' => Text::_ ( 'Default' ),
										'large' => Text::_ ( 'Large' ),
										'collapse' => Text::_ ( 'None' )
								),
								'std' => '',
								'depends' => array (
										array (
												'image_alignment',
												'!=',
												'top'
										),
										array (
												'image_alignment',
												'!=',
												'bottom'
										)
								)
						),
						'grid_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Breakpoint' ),
								'desc' => Text::_ ( 'Set the breakpoint from which grid cells will stack.' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' )
								),
								'std' => 'm',
								'depends' => array (
										array (
												'image_alignment',
												'!=',
												'top'
										),
										array (
												'image_alignment',
												'!=',
												'bottom'
										)
								)
						),
						'vertical_alignment' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Vertical Alignment' ),
								'desc' => Text::_ ( 'Vertically center grid cells.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 1,
								'depends' => array (
										array (
												'image_alignment',
												'!=',
												'top'
										),
										array (
												'image_alignment',
												'!=',
												'bottom'
										)
								)
						),
						'image_margin_top' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin Top' ),
								'desc' => Text::_ ( 'Set the top margin.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'remove' => Text::_ ( 'None' )
								),
								'std' => '',
								'depends' => array (
										array (
												'image_alignment',
												'!=',
												'top'
										),
										array (
												'image_alignment',
												'!=',
												'left'
										),
										array (
												'image_alignment',
												'!=',
												'right'
										)
								)
						),
						'separator_title_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Title' )
						),
						'title_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.ui-title { font-family: {{ VALUE }}; }'
								)
						),
						'heading_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Heading styles differ in font-size but may also come with a predefined color, size and font' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'heading-2xlarge' => Text::_ ( '2XLarge' ),
										'heading-xlarge' => Text::_ ( 'XLarge' ),
										'heading-large' => Text::_ ( 'Large' ),
										'heading-medium' => Text::_ ( 'Medium' ),
										'heading-small' => Text::_ ( 'Small' ),
										'h1' => Text::_ ( 'H1' ),
										'h2' => Text::_ ( 'H2' ),
										'h3' => Text::_ ( 'H3' ),
										'h4' => Text::_ ( 'H4' ),
										'h5' => Text::_ ( 'H5' ),
										'h6' => Text::_ ( 'H6' )
								),
								'std' => ''
						),
						'title_decoration' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Decoration' ),
								'desc' => Text::_ ( 'Decorate the title with a divider, bullet or a line that is vertically centered to the title' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'uk-heading-divider' => Text::_ ( 'Divider' ),
										'uk-heading-bullet' => Text::_ ( 'Bullet' ),
										'uk-heading-line' => Text::_ ( 'Line' )
								),
								'std' => ''
						),
						'title_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Select the predefined title text color.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'muted' => Text::_ ( 'Muted' ),
										'emphasis' => Text::_ ( 'Emphasis' ),
										'primary' => Text::_ ( 'Primary' ),
										'secondary' => Text::_ ( 'Secondary' ),
										'success' => Text::_ ( 'Success' ),
										'warning' => Text::_ ( 'Warning' ),
										'danger' => Text::_ ( 'Danger' )
								),
								'std' => ''
						),
						'custom_title_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Custom Color' ),
								'depends' => array (
										array (
												'title_color',
												'=',
												''
										)
								)
						),
						'heading_selector' => array (
								'type' => 'select',
								'title' => Text::_ ( 'HTML Element' ),
								'desc' => Text::_ ( 'Choose one of the six heading elements to fit your semantic structure.' ),
								'values' => array (
										'h1' => Text::_ ( 'H1' ),
										'h2' => Text::_ ( 'H2' ),
										'h3' => Text::_ ( 'H3' ),
										'h4' => Text::_ ( 'H4' ),
										'h5' => Text::_ ( 'H5' ),
										'h6' => Text::_ ( 'H6' )
								),
								'std' => 'h3'
						),
						'title_margin_top' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin Top' ),
								'desc' => Text::_ ( 'Set the top margin.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'remove' => Text::_ ( 'None' )
								),
								'std' => ''
						),
						'separator_content_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Content' )
						),
						'content_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.ui-content { font-family: {{ VALUE }}; }'
								)
						),
						'content_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a predefined meta text style, including color, size and font-family' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'text-lead' => Text::_ ( 'Lead' )
								),
								'std' => ''
						),
						'content_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' )
						),
						'content_margin_top' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin Top' ),
								'desc' => Text::_ ( 'Set the top margin.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'remove' => Text::_ ( 'None' )
								),
								'std' => ''
						),

						'separator_button_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Link' )
						),
						'link_button_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Set the button style.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'primary' => Text::_ ( 'Primary' ),
										'secondary' => Text::_ ( 'Secondary' ),
										'danger' => Text::_ ( 'Danger' ),
										'text' => Text::_ ( 'Text' ),
										'link' => Text::_ ( 'Link' ),
										'link-muted' => Text::_ ( 'Link Muted' ),
										'link-text' => Text::_ ( 'Link Text' ),
										'custom' => Text::_ ( 'Custom' )
								),
								'std' => ''
						),
						'separator_button_custom_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Custom Button Style' ),
								'depends' => array (
										array (
												'link_button_style',
												'=',
												'custom'
										)
								)
						),
						'button_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'depends' => array (
										array (
												'link_button_style',
												'=',
												'custom'
										)
								),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.uk-button-custom { font-family: {{ VALUE }}; }'
								)
						),
						'button_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'std' => '#1e87f0',
								'depends' => array (
										array (
												'link_button_style',
												'=',
												'custom'
										)
								)
						),
						'button_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Button Color' ),
								'depends' => array (
										array (
												'link_button_style',
												'=',
												'custom'
										)
								)
						),
						'button_background_hover' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Hover Background Color' ),
								'std' => '#0f7ae5',
								'depends' => array (
										array (
												'link_button_style',
												'=',
												'custom'
										)
								)
						),
						'button_hover_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Hover Button Color' ),
								'depends' => array (
										array (
												'link_button_style',
												'=',
												'custom'
										)
								)
						),
						'link_button_size' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Button Size' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'uk-button-small' => Text::_ ( 'Small' ),
										'uk-button-large' => Text::_ ( 'Large' )
								),
								'std' => ''
						),
						'button_margin_top' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin Top' ),
								'desc' => Text::_ ( 'Set the top margin.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'remove' => Text::_ ( 'None' )
								),
								'std' => ''
						),

						'separator_general_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'General' )
						),

						'alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Text Alignment' ),
								'desc' => Text::_ ( 'Center, left and right alignment.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'uk-text-left' => Text::_ ( 'Left' ),
										'uk-text-center' => Text::_ ( 'Center' ),
										'uk-text-right' => Text::_ ( 'Right' ),
										'uk-text-justify' => Text::_ ( 'Justify' )
								),
								'std' => ''
						),
						'text_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Text Breakpoint' ),
								'desc' => Text::_ ( 'Display the text alignment only on this device width and larger' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => '',
								'depends' => array (
										array (
												'alignment',
												'!=',
												''
										)
								)
						),
						'text_alignment_fallback' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Text Alignment Fallback' ),
								'desc' => Text::_ ( 'Define an alignment fallback for device widths below the breakpoint' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'left' => Text::_ ( 'Left' ),
										'center' => Text::_ ( 'Center' ),
										'right' => Text::_ ( 'Right' ),
										'justify' => Text::_ ( 'Justify' )
								),
								'std' => '',
								'depends' => array (
										array (
												'text_breakpoint',
												'!=',
												''
										),
										array (
												'alignment',
												'!=',
												''
										)
								)
						),
						'addon_max_width' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Max Width' ),
								'desc' => Text::_ ( 'Set the maximum content width.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'2xlarge' => Text::_ ( '2X-Large' )
								),
								'std' => ''
						),
						'addon_max_width_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Max Width Alignment' ),
								'desc' => Text::_ ( 'Define the alignment in case the container exceeds the element\'s max-width.' ),
								'values' => array (
										'' => Text::_ ( 'Left' ),
										'auto' => Text::_ ( 'Center' ),
										'auto-left' => Text::_ ( 'Right' )
								),
								'std' => '',
								'depends' => array (
										array (
												'addon_max_width',
												'!=',
												''
										)
								)
						),
						'addon_max_width_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Max Width Breakpoint' ),
								'desc' => Text::_ ( 'Define the device width from which the element\'s max-width will apply.' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => '',
								'depends' => array (
										array (
												'addon_max_width',
												'!=',
												''
										)
								)
						),
						'addon_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'desc' => Text::_ ( 'Set the vertical margin. Note: The first element\'s top margin and the last element\'s bottom margin are always removed. Define those in the grid settings instead.' ),
								'values' => array (
										'' => Text::_ ( 'Keep existing' ),
										'small' => Text::_ ( 'Small' ),
										'default' => Text::_ ( 'Default' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'remove-vertical' => Text::_ ( 'None' )
								),
								'std' => ''
						),
						'animation' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Animation' ),
								'desc' => Text::_ ( 'A collection of smooth animations to use within your page.' ),
								'values' => array (
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
								),
								'std' => ''
						),
						'animation_repeat' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Repeat Animation' ),
								'desc' => Text::_ ( 'Applies the animation class every time the element is in view' ),
								'std' => 0,
								'depends' => array (
										array (
												'animation',
												'!=',
												''
										),
										array (
												'animation',
												'!=',
												'parallax'
										)
								)
						),

						'separator_parallax_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Parallax Animation Settings' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'horizontal_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal Start' ),
								'min' => - 600,
								'max' => 600,
								'desc' => Text::_ ( 'Animate the horizontal position (translateX) in pixels.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'horizontal_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal End' ),
								'min' => - 600,
								'max' => 600,
								'desc' => Text::_ ( 'Animate the horizontal position (translateX) in pixels.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'vertical_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical Start' ),
								'min' => - 600,
								'max' => 600,
								'desc' => Text::_ ( 'Animate the vertical position (translateY) in pixels.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'vertical_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical End' ),
								'min' => - 600,
								'max' => 600,
								'desc' => Text::_ ( 'Animate the vertical position (translateY) in pixels.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'scale_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale Start' ),
								'min' => 50,
								'max' => 200,
								'desc' => Text::_ ( 'Animate the scaling. Min: 50, Max: 200 =>  100 means 100% scale, 200 means 200% scale, and 50 means 50% scale.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'scale_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale End' ),
								'min' => 50,
								'max' => 200,
								'desc' => Text::_ ( 'Animate the scaling. Min: 50, Max: 200 =>  100 means 100% scale, 200 means 200% scale, and 50 means 50% scale.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'rotate_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate Start' ),
								'min' => 0,
								'max' => 360,
								'desc' => Text::_ ( 'Animate the rotation clockwise in degrees.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'rotate_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate End' ),
								'min' => 0,
								'max' => 360,
								'desc' => Text::_ ( 'Animate the rotation clockwise in degrees.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'opacity_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity Start' ),
								'min' => 0,
								'max' => 100,
								'desc' => Text::_ ( 'Animate the opacity. 100 means 100% opacity, and 0 means 0% opacity.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'opacity_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity End' ),
								'min' => 0,
								'max' => 100,
								'desc' => Text::_ ( 'Animate the opacity. 100 means 100% opacity, and 0 means 0% opacity.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'easing' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Easing' ),
								'min' => - 200,
								'max' => 200,
								'desc' => Text::_ ( 'Set the animation easing. A value below 100 is faster in the beginning and slower towards the end while a value above 100 behaves inversely.' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'viewport' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Viewport' ),
								'min' => 10,
								'max' => 100,
								'desc' => Text::_ ( 'Set the animation end point relative to viewport height, e.g. 50 for 50% of the viewport' ),
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),

						'breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Breakpoint' ),
								'desc' => Text::_ ( 'Display the parallax effect only on this device width and larger. It is useful to disable the parallax animation on small viewports.' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone)' ),
										'm' => Text::_ ( 'Medium (Tablet)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => '',
								'depends' => array (
										array (
												'animation',
												'=',
												'parallax'
										)
								)
						),
						'visibility' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Visibility' ),
								'desc' => Text::_ ( 'Display the element only on this device width and larger.' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										'uk-visible@s' => Text::_ ( 'Small (Phone Landscape)' ),
										'uk-visible@m' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'uk-visible@l' => Text::_ ( 'Large (Desktop)' ),
										'uk-visible@xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => ''
						),
						'class' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_CLASS' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_CLASS_DESC' ),
								'std' => ''
						)
				)
		)
) );
