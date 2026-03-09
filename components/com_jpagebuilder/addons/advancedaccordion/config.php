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
		'addon_name' => 'advancedaccordion',
		'title' => Text::_ ( 'Accordion Items' ),
		'desc' => Text::_ ( 'Display an accordion with a list of items.' ),
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/advancedaccordion/assets/images/icon.png',
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
								'desc' => Text::_ ( 'Choose one of the seven heading elements to fit your semantic structure.' ),
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

						// Repeatable Item
						'ui_accordion_item' => array (
								'title' => Text::_ ( 'Items' ),
								'attr' => array (
										'title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Title' ),
												'desc' => Text::_ ( 'Defines and styles the toggle for each accordion item.' ),
												'std' => 'Item'
										),
										'content' => array (
												'type' => 'editor',
												'title' => Text::_ ( 'Content' ),
												'desc' => Text::_ ( 'Defines the content part for each accordion item.' ),
												'std' => 'Guide to setup and configuration. You can present below a guide and a description of how your system configuration works and add some animated screens.'
										),
										'image' => array (
												'type' => 'media',
												'title' => Text::_ ( 'Select Image' ),
												'placeholder' => 'http://www.example.com/my-photo.jpg'
										),
										'alt_text' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Image Alt' ),
												'std' => 'Your Image Title',
												'depends' => array (
														array (
																'image',
																'!=',
																''
														)
												)
										),
										'link' => array (
												'type' => 'media',
												'format' => 'attachment',
												'title' => Text::_ ( 'Link' ),
												'placeholder' => 'http://www.example.com',
												'hide_preview' => true
										),
										'button_title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Link Text' ),
												'depends' => array (
														array (
																'link',
																'!=',
																''
														)
												)
										)
								)
						),
						'separator_accordion_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Accordion' )
						),
						'multiple' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Allow multiple open items' ),
								'desc' => Text::_ ( 'To display multiple content sections at the same time without one collapsing when the other one is opened' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'closed' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Allow all items to be closed' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 1
						),

						'accordion_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a predefined style for accordion' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'default' => Text::_ ( 'Default' ),
										'muted' => Text::_ ( 'Muted' ),
										'primary' => Text::_ ( 'Primary' ),
										'secondary' => Text::_ ( 'Secondary' ),
										'hover' => Text::_ ( 'Hover' )
								),
								'std' => ''
						),
						'card_size' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Size' ),
								'desc' => Text::_ ( 'Define the card\'s size by selecting the padding between the card and its content.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'uk-card-small' => Text::_ ( 'Small' ),
										'uk-card-large' => Text::_ ( 'Large' )
								),
								'std' => 'uk-card-small',
								'depends' => array (
										array (
												'accordion_style',
												'!=',
												''
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
										'css' => '.tm-title { font-family: {{ VALUE }}; }'
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
						'separator_image_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Image' ),
								'desc' => Text::_ ( 'These options are only used, if the image is set for accordion item.' )
						),
						'image_border' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Border' ),
								'desc' => Text::_ ( 'Select the image\'s border style.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'circle' => Text::_ ( 'Circle' ),
										'rounded' => Text::_ ( 'Rounded' ),
										'pill' => Text::_ ( 'Pill' )
								),
								'std' => ''
						),
						'positions' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Alignment' ),
								'desc' => Text::_ ( 'Align the image to the top, left, right or place it between the title and the content.' ),
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
								'desc' => Text::_ ( 'Define the width of the navigation. Choose between percent and fixed widths or expand columns to the width of their content' ),
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
								'std' => '1-2',
								'depends' => array (
										array (
												'positions',
												'!=',
												'top'
										),
										array (
												'positions',
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
								'std' => '',
								'depends' => array (
										array (
												'card_alignment',
												'!=',
												'top'
										),
										array (
												'card_alignment',
												'!=',
												'bottom'
										),
										array (
												'card_alignment',
												'!=',
												'between'
										)
								)
						),
						'image_grid_row_gap' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Row Gap' ),
								'desc' => Text::_ ( 'Set the size of the gap if the grid items stack.' ),
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
												'card_alignment',
												'!=',
												'top'
										),
										array (
												'card_alignment',
												'!=',
												'bottom'
										),
										array (
												'card_alignment',
												'!=',
												'between'
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
												'positions',
												'!=',
												'top'
										),
										array (
												'positions',
												'!=',
												'bottom'
										)
								)
						),
						'grid_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Breakpoint' ),
								'desc' => Text::_ ( 'Set the breakpoint from which the navigation and content will stack.' ),
								'values' => array (
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 'm',
								'depends' => array (
										array (
												'positions',
												'!=',
												'top'
										),
										array (
												'positions',
												'!=',
												'bottom'
										)
								)
						),
						'vertical_alignment' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Vertical Alignment' ),
								'desc' => Text::_ ( 'Vertically center the navigation and content' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'positions',
												'!=',
												'top'
										),
										array (
												'positions',
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
												'positions',
												'=',
												'bottom'
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
								'placeholder' => 'Read more',
								'std' => 'Read more'
						),
						'target' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB_DESC' ),
								'values' => array (
										'' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_SAME_WINDOW' ),
										'_blank' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_NEW_WINDOW' )
								)
						),
						'button_style' => array (
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
												'button_style',
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
												'button_style',
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
												'button_style',
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
												'button_style',
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
												'button_style',
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
												'button_style',
												'=',
												'custom'
										)
								)
						),
						'button_size' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Button Size' ),
								'desc' => Text::_ ( 'Set the size for multiple buttons.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'uk-button-small' => Text::_ ( 'Small' ),
										'uk-button-large' => Text::_ ( 'Large' )
								)
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
						'delay_element_animations' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Delay Element Animations' ),
								'desc' => Text::_ ( 'Delay element animations so that animations are slightly delayed and don\'t play all at the same time. Slide animations can come into effect with a fixed offset or at 100% of the element\’s own size.' ),
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
