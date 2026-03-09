<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct accees
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
JpagebuilderConfig::addonConfig ( array (
		'type' => 'repeatable',
		'addon_name' => 'testimonialslider',
		'title' => Text::_ ( 'Testimonial Slider' ),
		'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TESTIMONIAL_PRO_DESC' ),
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/testimonialslider/assets/images/icon.png',
		'category' => 'Interface',
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
								'title' => Text::_ ( 'Block Title' ),
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
						'ui_testimonialslider_item' => array (
								'title' => Text::_ ( 'Items' ),
								'attr' => array (
										'title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Name' ),
												'std' => 'Peter Holland'
										),

										'company' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Meta' ),
												'std' => 'Customer'
										),
										'avatar' => array (
												'type' => 'media',
												'title' => Text::_ ( 'Avatar' ),
												'placeholder' => 'http://www.example.com/my-photo.jpg'
										),
										'alt_text' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Image ALT' ),
												'placeholder' => 'Image Alt',
												'depends' => array (
														array (
																'avatar',
																'!=',
																''
														)
												)
										),
										'client_review' => array (
												'type' => 'select',
												'title' => Text::_ ( 'Client Rating' ),
												'values' => array (
														'' => Text::_ ( 'None' ),
														'1' => Text::_ ( '1' ),
														'2' => Text::_ ( '2' ),
														'3' => Text::_ ( '3' ),
														'4' => Text::_ ( '4' ),
														'5' => Text::_ ( '5' )
												),
												'std' => ''
										),
										'message' => array (
												'type' => 'editor',
												'title' => Text::_ ( 'Content' ),
												'std' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
										),

										'link' => array (
												'type' => 'media',
												'format' => 'attachment',
												'title' => Text::_ ( 'Link' ),
												'placeholder' => 'http://www.example.com',
												'std' => '',
												'hide_preview' => true
										)
								)
						),
						'separator_columns_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Item Width' )
						),
						'ts_phone_portrait' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Phone Portrait' ),
								'desc' => Text::_ ( 'Set the item width for each breakpoint. Inherit refers to the item width of the next smaller screen size.' ),
								'values' => array (
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => '1-1'
						),
						'ts_phone_landscape' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Phone Landscape' ),
								'desc' => Text::_ ( 'Set the item width for each breakpoint. Inherit refers to the item width of the next smaller screen size.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => ''
						),
						'ts_tablet_landscape' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Tablet Landscape' ),
								'desc' => Text::_ ( 'Set the item width for each breakpoint. Inherit refers to the item width of the next smaller screen size.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => '1-2'
						),
						'ts_desktop' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Desktop' ),
								'desc' => Text::_ ( 'Set the item width for each breakpoint. Inherit refers to the item width of the next smaller screen size.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => ''
						),
						'ts_large_screens' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Large Screens' ),
								'desc' => Text::_ ( 'Set the item width for each breakpoint. Inherit refers to the item width of the next smaller screen size.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => ''
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
								'std' => ''
						),
						'divider' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Show dividers' ),
								'desc' => Text::_ ( 'Select this option to separate grid cells with lines.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'separator_testimonial_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Testimonial' )
						),
						'header_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Header Position' ),
								'values' => array (
										'' => Text::_ ( 'Top' ),
										'bottom' => Text::_ ( 'Bottom' )
								),
								'std' => ''
						),
						'header_margin_top' => array (
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
												'header_alignment',
												'=',
												'bottom'
										)
								)
						),
						'icon_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Rating Icon Color' ),
								'std' => '#fba311'
						),
						'icon_rating' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Rating Icon Type' ),
								'values' => array (
										'' => Text::_ ( 'FontAwesome' ),
										'uikit' => Text::_ ( 'Uikit' )
								),
								'std' => ''
						),
						'rating_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Rating Position' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'image' => Text::_ ( 'Below Image' )
								),
								'std' => ''
						),
						'separator_card_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Card' )
						),

						'card_styles' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select one of the boxed card styles or a blank panel.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'default' => Text::_ ( 'Card Default' ),
										'primary' => Text::_ ( 'Card Primary' ),
										'secondary' => Text::_ ( 'Card Secondary' ),
										'hover' => Text::_ ( 'Card Hover' ),
										'custom' => Text::_ ( 'Custom' )
								),
								'std' => ''
						),
						'panel_link' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Link Card' ),
								'desc' => Text::_ ( 'Link the whole card if a link exists.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'card_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'std' => '#1e87f0',
								'depends' => array (
										array (
												'card_styles',
												'=',
												'custom'
										)
								)
						),
						'card_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' ),
								'depends' => array (
										array (
												'card_styles',
												'=',
												'custom'
										)
								)
						),
						'card_size' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Padding' ),
								'desc' => Text::_ ( 'Define the card\'s size by selecting the padding between the card and its content.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'uk-card-small' => Text::_ ( 'Small' ),
										'uk-card-large' => Text::_ ( 'Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'card_styles',
												'!=',
												''
										)
								)
						),
						'card_content_padding' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Padding' ),
								'desc' => Text::_ ( 'Add padding to the content if the image is top, bottom, left or right aligned.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'default' => Text::_ ( 'Default' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'card_styles',
												'=',
												''
										)
								)
						),

						'card_width' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Max Width' ),
								'desc' => Text::_ ( 'Set the maximum width.' ),
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
						'separator_animation_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Animation' )
						),
						'slidesets' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Slide all visible items at once' ),
								'desc' => Text::_ ( 'Group items into sets. The number of items within a set depends on the defined item width, e.g. 33% means that eaach set contains 3 items.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'center_slide' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Center the active slide' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'velocity' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Velocity' ),
								'desc' => Text::_ ( 'Set the velocity in pixels per milliseconds.' ),
								'min' => 20,
								'max' => 300
						),
						'finite_slide' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Disable infinite scrolling' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'autoplay' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Enable autoplay' ),
								'desc' => Text::_ ( 'To activate Slider autoplays to the attribute. ' ),
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
						'separator_navigation_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Navigation' )
						),
						'navigation' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Navigation Display' ),
								'desc' => Text::_ ( 'Select the navigation type, show or hide navigation control.' ),
								'values' => array (
										'' => Text::_ ( 'Hide' ),
										'dotnav' => Text::_ ( 'Dotnav' )
								),
								'std' => 'dotnav'
						),
						'navigation_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Position' ),
								'desc' => Text::_ ( 'Select the position of the navigation.' ),
								'values' => array (
										'left' => Text::_ ( 'Left' ),
										'center' => Text::_ ( 'Center' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => 'center'
						),
						'navigation_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'desc' => Text::_ ( 'Set the vertical margin.' ),
								'values' => array (
										'uk-margin-small-top' => Text::_ ( 'Small' ),
										'uk-margin-top' => Text::_ ( 'Default' ),
										'uk-margin-medium-top' => Text::_ ( 'Medium' )
								),
								'std' => 'uk-margin-top'
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
								'std' => 's'
						),
						'navigation_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Color' ),
								'values' => array (
										'light' => Text::_ ( 'Light' ),
										'' => Text::_ ( 'None' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => ''
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
										'' => Text::_ ( 'Hide' ),
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
						'slidenav_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'desc' => Text::_ ( 'Apply a margin between the slidnav and the slider container.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => 'medium'
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
												'outside'
										)
								)
						),
						'slidenav_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Color' ),
								'desc' => Text::_ ( 'Set light or dark color mode for the slidenav.' ),
								'values' => array (
										'light' => Text::_ ( 'Light' ),
										'' => Text::_ ( 'None' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												'outside'
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
								'title' => Text::_ ( 'Outside Color' ),
								'desc' => Text::_ ( 'Set light or dark color if the slidenav is outside of the slider' ),
								'values' => array (
										'light' => Text::_ ( 'Light' ),
										'' => Text::_ ( 'None' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'slidenav_position',
												'=',
												'outside'
										)
								)
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
										'0' => Text::_ ( 'JNO' ),
										'1' => Text::_ ( 'JYES' )
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
						'separator_image_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Image' )
						),
						'image_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Image Position' ),
								'desc' => Text::_ ( 'Display image above/below/inline/ testimonial content or none.' ),
								'values' => array (
										'top' => Text::_ ( 'Top' ),
										'bottom' => Text::_ ( 'Bottom' ),
										'' => Text::_ ( 'Left' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => ''
						),
						'image_grid_width' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Width' ),
								'desc' => Text::_ ( 'Define the width of the image within the grid. Choose between percent and fixed widths or expand columns to the width of their content.' ),
								'values' => array (
										'auto' => Text::_ ( 'Auto' ),
										'4-5' => Text::_ ( '80%' ),
										'3-4' => Text::_ ( '75%' ),
										'2-3' => Text::_ ( '66%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'2-5' => Text::_ ( '40%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' ),
										'2xlarge' => Text::_ ( '2X-Large' )
								),
								'std' => 'auto',
								'depends' => array (
										array (
												'image_position',
												'!=',
												'top'
										),
										array (
												'image_position',
												'!=',
												'bottom'
										)
								)
						),
						'image_grid_column_gap' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Column Gap' ),
								'desc' => Text::_ ( 'Set the size of the gap between the image and the content.' ),
								'values' => array (
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'' => Text::_ ( 'Default' ),
										'large' => Text::_ ( 'Large' ),
										'collapse' => Text::_ ( 'None' )
								),
								'std' => 'small',
								'depends' => array (
										array (
												'image_position',
												'!=',
												'top'
										),
										array (
												'image_position',
												'!=',
												'bottom'
										)
								)
						),
						'image_grid_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Breakpoint' ),
								'desc' => Text::_ ( 'Set the breakpoint from which grid cells will stack.' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 'm',
								'depends' => array (
										array (
												'image_position',
												'!=',
												'top'
										),
										array (
												'image_position',
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
												'image_position',
												'!=',
												'top'
										),
										array (
												'image_position',
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
												'image_position',
												'=',
												'bottom'
										)
								)
						),
						'image_link' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Link image' ),
								'desc' => Text::_ ( 'Link the image if a link exists.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'avatar_width' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TESTIMONIAL_CLIENT_AVATAR_WIDTH' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TESTIMONIAL_CLIENT_AVATAR_WIDTH_DESC' ),
								'std' => 90,
								'min' => 16,
								'max' => 128
						),
						'avatar_shape' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Border' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'uk-border-rounded' => Text::_ ( 'Rounded' ),
										'uk-border-circle' => Text::_ ( 'Circle' ),
										'uk-border-pill' => Text::_ ( 'Pill' )
								),
								'std' => 'uk-border-circle'
						),
						'image_svg_inline' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Inline SVG' ),
								'desc' => Text::_ ( 'Inject SVG images into the page markup, so that they can easily be styled with CSS.' ),
								'std' => 0
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
												'image_svg_inline',
												'=',
												1
										)
								)
						),
						'separator_name_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Name' )
						),

						'title_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Name Style' ),
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
						'title_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.ui-author { font-family: {{ VALUE }}; }'
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
						'title_text_color' => array (
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
										'danger' => Text::_ ( 'Danger' ),
										'background' => Text::_ ( 'Background' )
								),
								'std' => ''
						),
						'custom_title_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Custom Color' ),
								'depends' => array (
										array (
												'title_text_color',
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
						'link_title' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Link Title' ),
								'desc' => Text::_ ( 'Link the title if a link exists.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'title_hover_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Hover Style' ),
								'desc' => Text::_ ( 'Set the hover style for a linked title.' ),
								'values' => array (
										'reset' => Text::_ ( 'None' ),
										'heading' => Text::_ ( 'Heading Link' ),
										'' => Text::_ ( 'Default Link' )
								),
								'std' => 'reset',
								'depends' => array (
										array (
												'link_title',
												'=',
												1
										)
								)
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
								'std' => 'remove'
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
								'std' => 'text-meta'
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
						'meta_text_color' => array (
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
										'danger' => Text::_ ( 'Danger' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => ''
						),
						'custom_meta_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Custom Color' ),
								'depends' => array (
										array (
												'meta_text_color',
												'=',
												''
										)
								)
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
								'std' => 'remove'
						),
						'separator_content_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Content' )
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
						'content_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.ui-content { font-family: {{ VALUE }}; }'
								)
						),
						'content_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' )
						),
						'content_dropcap' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Drop Cap' ),
								'desc' => Text::_ ( 'Display the first letter of the paragraph as a large initial.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'content_column' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Columns' ),
								'desc' => Text::_ ( 'Set the number of text columns.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'1-2' => Text::_ ( 'Halves' ),
										'1-3' => Text::_ ( 'Thirds' ),
										'1-4' => Text::_ ( 'Quarters' ),
										'1-5' => Text::_ ( 'Fifths' ),
										'1-6' => Text::_ ( 'Sixths' )
								),
								'std' => ''
						),
						'content_column_divider' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Show dividers' ),
								'desc' => Text::_ ( 'Show a divider between text columns.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'content_column',
												'!=',
												''
										)
								)
						),
						'content_column_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Columns Breakpoint' ),
								'desc' => Text::_ ( 'Set the device width from which the text columns should apply' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 'm',
								'depends' => array (
										array (
												'content_column',
												'!=',
												''
										)
								)
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
						'separator_link_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Link' )
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
						'separator_general_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'General' )
						),

						'text_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Text Alignment' ),
								'desc' => Text::_ ( 'Center, left and right alignment.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'left' => Text::_ ( 'Left' ),
										'center' => Text::_ ( 'Center' ),
										'right' => Text::_ ( 'Right' ),
										'justify' => Text::_ ( 'Justify' )
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
										'right' => Text::_ ( 'Right' )
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
