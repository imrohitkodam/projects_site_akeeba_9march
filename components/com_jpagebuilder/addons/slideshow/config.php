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
		'type' => 'repeatable',
		'addon_name' => 'slideshow',
		'category' => 'Interface',
		'title' => Text::_ ( 'Slideshow' ),
		'desc' => Text::_ ( 'Create a responsive slideshow with images and videos.' ),
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/slideshow/assets/images/icon.png',
		'attr' => array (
				'general' => array (
						'admin_label' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL_DESC' ),
								'std' => ''
						),
						'title_addon' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Title' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_DESC' ),
								'std' => ''
						),
						'title_heading_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Heading styles differ in font-size but may also come with a predefined color, size and font' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
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
								'std' => 'h3',
								'depends' => array (
										array (
												'title_addon',
												'!=',
												''
										)
								)
						),
						'title_heading_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Title Margin' ),
								'desc' => Text::_ ( 'Set the vertical margin for title.' ),
								'values' => array (
										'' => Text::_ ( 'Keep existing' ),
										'uk-margin-small' => Text::_ ( 'Small' ),
										'uk-margin' => Text::_ ( 'Default' ),
										'uk-margin-medium' => Text::_ ( 'Medium' ),
										'uk-margin-large' => Text::_ ( 'Large' ),
										'uk-margin-xlarge' => Text::_ ( 'X-Large' ),
										'uk-margin-remove-vertical' => Text::_ ( 'None' )
								),
								'std' => 'uk-margin',
								'depends' => array (
										array (
												'title_addon',
												'!=',
												''
										)
								)
						),
						'title_heading_decoration' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Decoration' ),
								'desc' => Text::_ ( 'Decorate the heading with a divider, bullet or a line that is vertically centered to the heading' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'uk-heading-divider' => Text::_ ( 'Divider' ),
										'uk-heading-bullet' => Text::_ ( 'Bullet' ),
										'uk-heading-line' => Text::_ ( 'Line' )
								),
								'std' => '',
								'depends' => array (
										array (
												'title_addon',
												'!=',
												''
										)
								)
						),
						'title_heading_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Color' ),
								'desc' => Text::_ ( 'Select the text color. If the Background option is selected, styles that don\'t apply a background image use the primary color instead.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-muted' => Text::_ ( 'Muted' ),
										'text-emphasis' => Text::_ ( 'Emphasis' ),
										'text-primary' => Text::_ ( 'Primary' ),
										'text-secondary' => Text::_ ( 'Secondary' ),
										'text-success' => Text::_ ( 'Success' ),
										'text-warning' => Text::_ ( 'Warning' ),
										'text-danger' => Text::_ ( 'Danger' ),
										'text-background' => Text::_ ( 'Background' )
								),
								'std' => '',
								'depends' => array (
										array (
												'title_addon',
												'!=',
												''
										)
								)
						),
						'title_heading_selector' => array (
								'type' => 'select',
								'title' => Text::_ ( 'HTML Element' ),
								'desc' => Text::_ ( 'Choose one of the HTML elements to fit your semantic structure.' ),
								'values' => array (
										'h1' => Text::_ ( 'H1' ),
										'h2' => Text::_ ( 'H2' ),
										'h3' => Text::_ ( 'H3' ),
										'h4' => Text::_ ( 'H4' ),
										'h5' => Text::_ ( 'H5' ),
										'h6' => Text::_ ( 'H6' ),
										'div' => Text::_ ( 'Div' )
								),
								'std' => 'h3',
								'depends' => array (
										array (
												'title_addon',
												'!=',
												''
										)
								)
						),
						// Repeatable Items
						'ui_slideshow_items' => array (
								'title' => Text::_ ( 'Items' ),
								'attr' => array (
										'media_item' => array (
												'type' => 'media',
												'title' => Text::_ ( 'Image' ),
												'placeholder' => 'http://www.example.com/my-photo.jpg',
												'std'   => [
														'src'    => 'https://storejextensions.org/cdn/addons/image1.jpg',
														'height' => '',
														'width'  => '',
												]
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
										'image_alt' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Image ALT' ),
												'placeholder' => 'Image Alt'
										),
										'title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Title' ),
												'std' => 'Item'
										),
										'meta' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Meta' )
										),
										'content' => array (
												'type' => 'editor',
												'title' => Text::_ ( 'Content' ),
												'std' => ''
										),
										'title_link' => array (
												'type' => 'media',
												'format' => 'attachment',
												'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK' ),
												'placeholder' => 'http://www.example.com',
												'std' => '',
												'hide_preview' => true
										),
										'button_title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Link Text' ),
												'placeholder' => 'Read more',
												'depends' => array (
														array (
																'title_link',
																'!=',
																''
														)
												)
										),
										'text_item_color' => array (
												'type' => 'select',
												'title' => Text::_ ( 'Text Color' ),
												'desc' => Text::_ ( 'Set light or dark color mode for text, butons and controls' ),
												'values' => array (
														'' => Text::_ ( 'None' ),
														'light' => Text::_ ( 'Light' ),
														'dark' => Text::_ ( 'Dark' )
												),
												'std' => ''
										),
										'navigation_image_item' => array (
												'type' => 'media',
												'title' => Text::_ ( 'Navigation Thumbnail' ),
												'desc' => Text::_ ( 'This option is only used if the thumbnail navigation is set.' ),
												'placeholder' => 'http://www.example.com/my-photo.jpg'
										)
								)
						),
						'separator_slideshow_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'SlideShow' )
						),
						'height' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Height' ),
								'desc' => Text::_ ( 'The slideshow always takes up fullwidth, and  the height will adapt automatically based on the defined ratio. Alternatively, the height can adapt to the height of the viewport.<br/> Note: Make sure, no height is set in the section settings when using one of the viewport options.' ),
								'values' => array (
										'' => Text::_ ( 'Auto' ),
										'full' => Text::_ ( 'Viewport' ),
										'percent' => Text::_ ( 'Viewport (Minus 20%)' ),
										'section' => Text::_ ( 'Viewport (Minus the following section)' )
								),
								'std' => ''
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
								'desc' => Text::_ ( 'Use an optional minimum height to prevent the slideshow from becoming smaller than its content on small devices.' ),
								'std' => 300
						),
						'max_height' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Max Height' ),
								'min' => 500,
								'max' => 1600,
								'desc' => Text::_ ( 'Set the maximum height' ),
								'depends' => array (
										array (
												'height',
												'=',
												''
										)
								)
						),
						'item_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Text Color' ),
								'desc' => Text::_ ( 'Set light or dark color mode for text, butons and controls' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => ''
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
								'std' => 1,
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
								'placeholder' => '15',
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
								'title' => Text::_ ( 'Display' ),
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
												'!=',
												''
										),
										array (
												'slidenav_position',
												'!=',
												'default'
										)
								)
						),
						'slidenav_outside_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Outside Color' ),
								'desc' => Text::_ ( 'Set light or dark color if the slidenav is outside of the slider' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										),
										array (
												'slidenav_position',
												'!=',
												'default'
										)
								)
						),

						'separator_overlay_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Overlay' )
						),
						'overlay_container' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Container Width' ),
								'desc' => Text::_ ( 'Set the maximum content width. Note: The section may already have a maximum width, which you cannot exceed.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'default' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'XLarge' ),
										'expand' => Text::_ ( 'Expand' )
								),
								'std' => ''
						),
						'overlay_container_padding' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Container Padding' ),
								'desc' => Text::_ ( 'Set the vertical container padding to position the overlay.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'xsmall' => Text::_ ( 'X-Small' ),
										'small' => Text::_ ( 'Small' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'overlay_container',
												'!=',
												''
										)
								)
						),
						'overlay_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'desc' => Text::_ ( 'Set the margin between the overlay and the slideshow container.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'large' => Text::_ ( 'Large' ),
										'none' => Text::_ ( 'None' )
								),
								'std' => '',
								'depends' => array (
										array (
												'overlay_container',
												'!=',
												'default'
										),
										array (
												'overlay_container',
												'!=',
												'small'
										),
										array (
												'overlay_container',
												'!=',
												'large'
										),
										array (
												'overlay_container',
												'!=',
												'xlarge'
										),
										array (
												'overlay_container',
												'!=',
												'expand'
										)
								)
						),
						'overlay_positions' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Positions' ),
								'desc' => Text::_ ( 'Select the content position.' ),
								'values' => array (
										'top' => Text::_ ( 'Top' ),
										'bottom' => Text::_ ( 'Bottom' ),
										'left' => Text::_ ( 'Left' ),
										'right' => Text::_ ( 'Right' ),
										'top-left' => Text::_ ( 'Top Left' ),
										'top-center' => Text::_ ( 'Top Center' ),
										'top-right' => Text::_ ( 'Top Right' ),
										'center-left' => Text::_ ( 'Center Left' ),
										'center' => Text::_ ( 'Center Center' ),
										'center-right' => Text::_ ( 'Center Right' ),
										'bottom-left' => Text::_ ( 'Bottom Left' ),
										'bottom-center' => Text::_ ( 'Bottom Center' ),
										'bottom-right' => Text::_ ( 'Bottom Right' )
								),
								'std' => 'center-left'
						),
						'overlay_styles' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a style for the overlay.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'overlay-default' => Text::_ ( 'Overlay Default' ),
										'overlay-primary' => Text::_ ( 'Overlay Primary' ),
										'tile-default' => Text::_ ( 'Tile Default' ),
										'tile-muted' => Text::_ ( 'Tile Muted' ),
										'tile-primary' => Text::_ ( 'Tile Primary' ),
										'tile-secondary' => Text::_ ( 'Tile Secondary' ),
										'overlay-custom' => Text::_ ( 'Custom' )
								),
								'std' => ''
						),
						'overlay_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'std' => '#ffd49b',
								'depends' => array (
										array (
												'overlay_styles',
												'=',
												'overlay-custom'
										)
								)
						),
						'overlay_padding' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Padding' ),
								'desc' => Text::_ ( 'Set the padding between the overlay and its content.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'overlay_styles',
												'!=',
												''
										)
								)
						),
						'overlay_width' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Width' ),
								'desc' => Text::_ ( 'Set a fixed width.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'2xlarge' => Text::_ ( '2X-Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'overlay_positions',
												'!=',
												'top'
										),
										array (
												'overlay_positions',
												'!=',
												'bottom'
										)
								)
						),

						'overlay_transition' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Animation' ),
								'desc' => Text::_ ( 'Choose between a parallax depending on the scroll position or an animation, which is applied once the slide is active.' ),
								'values' => array (
										'' => Text::_ ( 'Parallax' ),
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
										'slide-right' => Text::_ ( 'Slide Right 100%' )
								),
								'std' => ''
						),

						'overlay_horizontal_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal Start' ),
								'min' => - 600,
								'max' => 600,
								'desc' => Text::_ ( 'Animate the horizontal position (translateX) in pixels.' ),
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_horizontal_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal End' ),
								'min' => - 600,
								'max' => 600,
								'desc' => Text::_ ( 'Animate the horizontal position (translateX) in pixels.' ),
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_vertical_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical Start' ),
								'min' => - 600,
								'max' => 600,
								'desc' => Text::_ ( 'Animate the vertical position (translateY) in pixels.' ),
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_vertical_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical End' ),
								'min' => - 600,
								'max' => 600,
								'desc' => Text::_ ( 'Animate the vertical position (translateY) in pixels.' ),
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_scale_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale Start' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_scale_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale End' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_rotate_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate Start' ),
								'min' => 0,
								'max' => 360,
								'desc' => Text::_ ( 'Animate the rotation clockwise in degrees.' ),
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_rotate_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate End' ),
								'min' => 0,
								'max' => 360,
								'desc' => Text::_ ( 'Animate the rotation clockwise in degrees.' ),
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_opacity_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity Start' ),
								'min' => 0,
								'max' => 100,
								'desc' => Text::_ ( 'Animate the opacity. 100 means 100% opacity, and 0 means 0% opacity.' ),
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'overlay_opacity_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity End' ),
								'min' => 0,
								'max' => 100,
								'desc' => Text::_ ( 'Animate the opacity. 100 means 100% opacity, and 0 means 0% opacity.' ),
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'separator_title_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Title' )
						),
						'heading_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Heading styles differ in font-size but may also come with a predefined color, size and font' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
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
						'title_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.ui-title { font-family: {{ VALUE }}; }'
								)
						),
						'font_weight' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Font weight' ),
								'desc' => Text::_ ( 'Add one of the following classes to modify the font weight of your text.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'light' => Text::_ ( 'Light' ),
										'normal' => Text::_ ( 'Normal' ),
										'bold' => Text::_ ( 'Bold' ),
										'lighter' => Text::_ ( 'Lighter' ),
										'bolder' => Text::_ ( 'Bolder' )
								)
						),
						'title_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Select the predefined title text color.' ),
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
						'title_text_transform' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Transform' ),
								'desc' => Text::_ ( 'The following options will transform text into uppercased, capitalized or lowercased characters.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'uppercase' => Text::_ ( 'Uppercase' ),
										'capitalize' => Text::_ ( 'Capitalize' ),
										'lowercase' => Text::_ ( 'Lowercase' )
								),
								'std' => ''
						),
						'heading_selector' => array (
								'type' => 'select',
								'title' => Text::_ ( 'HTML Element' ),
								'desc' => Text::_ ( 'Choose one of the HTML elements to fit your semantic structure.' ),
								'values' => array (
										'h1' => Text::_ ( 'H1' ),
										'h2' => Text::_ ( 'H2' ),
										'h3' => Text::_ ( 'H3' ),
										'h4' => Text::_ ( 'H4' ),
										'h5' => Text::_ ( 'H5' ),
										'h6' => Text::_ ( 'H6' ),
										'div' => Text::_ ( 'Div' )
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
						'use_title_parallax' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Parallax Settings' ),
								'desc' => Text::_ ( 'Add a parallax effect.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_horizontal_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal Start' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_horizontal_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal End' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_vertical_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical Start' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_vertical_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical End' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_scale_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale Start' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_scale_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale End' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_rotate_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate Start' ),
								'min' => 0,
								'max' => 360,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_rotate_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate End' ),
								'min' => 0,
								'max' => 360,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_opacity_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity Start' ),
								'min' => 0,
								'max' => 100,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'title_opacity_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity End' ),
								'min' => 0,
								'max' => 100,
								'depends' => array (
										array (
												'use_title_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'separator_meta_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Meta' )
						),
						'meta_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.ui-meta { font-family: {{ VALUE }}; }'
								)
						),
						'meta_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a predefined meta text style, including color, size and font-family' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-meta' => Text::_ ( 'Meta' ),
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
						'meta_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Select the predefined meta text color.' ),
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
								'std' => ''
						),
						'custom_meta_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Custom Color' ),
								'depends' => array (
										array (
												'meta_color',
												'=',
												''
										)
								)
						),
						'meta_text_transform' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Transform' ),
								'desc' => Text::_ ( 'The following options will transform text into uppercased, capitalized or lowercased characters.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'uppercase' => Text::_ ( 'Uppercase' ),
										'capitalize' => Text::_ ( 'Capitalize' ),
										'lowercase' => Text::_ ( 'Lowercase' )
								),
								'std' => ''
						),
						'meta_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Alignment' ),
								'desc' => Text::_ ( 'Align the meta text above or below the title.' ),
								'values' => array (
										'top' => Text::_ ( 'Above Title' ),
										'' => Text::_ ( 'Below Title' ),
										'content' => Text::_ ( 'Below Content' )
								),
								'std' => ''
						),
						'meta_element' => array (
								'type' => 'select',
								'title' => Text::_ ( 'HTML Element' ),
								'desc' => Text::_ ( 'Choose one of the HTML elements to fit your semantic structure.' ),
								'values' => array (
										'h1' => Text::_ ( 'H1' ),
										'h2' => Text::_ ( 'H2' ),
										'h3' => Text::_ ( 'H3' ),
										'h4' => Text::_ ( 'H4' ),
										'h5' => Text::_ ( 'H5' ),
										'h6' => Text::_ ( 'H6' ),
										'div' => Text::_ ( 'Div' )
								),
								'std' => 'div'
						),
						'meta_margin_top' => array (
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
						'use_meta_parallax' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Parallax Settings' ),
								'desc' => Text::_ ( 'Add a parallax effect.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_horizontal_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal Start' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_horizontal_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal End' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_vertical_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical Start' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_vertical_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical End' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_scale_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale Start' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_scale_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale End' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_rotate_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate Start' ),
								'min' => 0,
								'max' => 360,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_rotate_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate End' ),
								'min' => 0,
								'max' => 360,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_opacity_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity Start' ),
								'min' => 0,
								'max' => 100,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'meta_opacity_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity End' ),
								'min' => 0,
								'max' => 100,
								'depends' => array (
										array (
												'use_meta_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
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
										'' => Text::_ ( 'None' ),
										'text-lead' => Text::_ ( 'Lead' ),
										'text-meta' => Text::_ ( 'Meta' )
								),
								'std' => ''
						),
						'content_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' )
						),
						'content_text_transform' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Transform' ),
								'desc' => Text::_ ( 'The following options will transform text into uppercased, capitalized or lowercased characters.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'uppercase' => Text::_ ( 'Uppercase' ),
										'capitalize' => Text::_ ( 'Capitalize' ),
										'lowercase' => Text::_ ( 'Lowercase' )
								),
								'std' => ''
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
						'use_content_parallax' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Parallax Settings' ),
								'desc' => Text::_ ( 'Add a parallax effect.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_horizontal_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal Start' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_horizontal_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal End' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_vertical_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical Start' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_vertical_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical End' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_scale_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale Start' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_scale_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale End' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_rotate_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate Start' ),
								'min' => 0,
								'max' => 360,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_rotate_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate End' ),
								'min' => 0,
								'max' => 360,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_opacity_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity Start' ),
								'min' => 0,
								'max' => 100,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'content_opacity_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity End' ),
								'min' => 0,
								'max' => 100,
								'depends' => array (
										array (
												'use_content_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'separator_button_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Link' )
						),
						'all_button_title' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Text' ),
								'std' => 'Read more'
						),
						'link_new_tab' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB_DESC' ),
								'values' => array (
										'' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_SAME_WINDOW' ),
										'_blank' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_NEW_WINDOW' )
								)
						),
						'link_button_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Set the button style.' ),
								'values' => array (
										'' => Text::_ ( 'Button Default' ),
										'primary' => Text::_ ( 'Button Primary' ),
										'secondary' => Text::_ ( 'Button Secondary' ),
										'danger' => Text::_ ( 'Button Danger' ),
										'text' => Text::_ ( 'Button Text' ),
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
								)
						),
						'button_margin_top' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin Top' ),
								'desc' => Text::_ ( 'Set the top margin. Note that the margin will only apply if the content field immediately follows another content field.' ),
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
						'use_button_parallax' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Parallax Settings' ),
								'desc' => Text::_ ( 'Add a parallax effect.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_horizontal_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal Start' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_horizontal_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Horizontal End' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_vertical_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical Start' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_vertical_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Vertical End' ),
								'min' => - 600,
								'max' => 600,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_scale_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale Start' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_scale_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Scale End' ),
								'min' => 30,
								'max' => 400,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_rotate_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate Start' ),
								'min' => 0,
								'max' => 360,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_rotate_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Rotate End' ),
								'min' => 0,
								'max' => 360,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_opacity_start' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity Start' ),
								'min' => 0,
								'max' => 100,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
						),
						'button_opacity_end' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Opacity End' ),
								'min' => 0,
								'max' => 100,
								'depends' => array (
										array (
												'use_button_parallax',
												'=',
												1
										),
										array (
												'overlay_transition',
												'=',
												''
										)
								)
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