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
		'type' => 'content',
		'addon_name' => 'calltoaction',
		'title' => Text::_ ( 'Call To Action' ),
		'desc' => Text::_ ( 'Create Beautiful Call To Action That Drive Engagement' ),
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/calltoaction/assets/images/icon.png',
		'category' => 'Interface',
		'attr' => array (
				'general' => array (
						'admin_label' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL_DESC' ),
								'std' => ''
						),
						// Title
						'title' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Title' ),
								'std' => 'Are you looking for something individual?'
						),
						// Content
						'content' => array (
								'type' => 'editor',
								'title' => Text::_ ( 'Content' ),
								'std' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.'
						),
						'title_link' => array (
								'type' => 'media',
								'format' => 'attachment',
								'title' => Text::_ ( 'Link' ),
								'desc' => Text::_ ( 'Enter or pick a link, an image or a video file.' ),
								'placeholder' => 'http://',
								'std' => '#',
								'hide_preview' => true
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
						'separator_layout_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Layout' )
						),
						'button_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Button Alignment' ),
								'desc' => Text::_ ( 'Align the button to the top,left,right or place it between the title and the content' ),
								'values' => array (
										'top' => Text::_ ( 'Top' ),
										'bottom' => Text::_ ( 'Bottom' ),
										'left' => Text::_ ( 'Left' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => 'right'
						),
						'grid_width' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Width' ),
								'desc' => Text::_ ( 'Define the width of the button within the grid. Choose between percent and fixed widths or expand columns to the width of their content.' ),
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
								'std' => '1-5',
								'depends' => array (
										array (
												'button_alignment',
												'!=',
												'top'
										),
										array (
												'button_alignment',
												'!=',
												'bottom'
										)
								)
						),
						'grid_gutter' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Grid Gutter' ),
								'desc' => Text::_ ( 'Select the gutter width between the button and content items.' ),
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
												'button_alignment',
												'!=',
												'top'
										),
										array (
												'button_alignment',
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
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 'm',
								'depends' => array (
										array (
												'button_alignment',
												'!=',
												'top'
										),
										array (
												'button_alignment',
												'!=',
												'bottom'
										)
								)
						),
						'grid_divider' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Show dividers' ),
								'desc' => Text::_ ( 'Select this option to separate grid cells with lines.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'grid_gutter',
												'!=',
												'collapse'
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
								'std' => 0,
								'depends' => array (
										array (
												'button_alignment',
												'!=',
												'top'
										),
										array (
												'button_alignment',
												'!=',
												'bottom'
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
						'decoration' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Heading Decoration' ),
								'desc' => Text::_ ( 'Decorate the heading with a divider, bullet or a line that is vertically centered to the heading' ),
								'values' => array (
										'' => 'None',
										'divider' => 'Divider',
										'bullet' => 'Bullet',
										'line' => 'Line'
								),
								'std' => ''
						),
						'title_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Select the predefined title text color.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-muted' => Text::_ ( 'Muted' ),
										'text-primary' => Text::_ ( 'Primary' ),
										'text-secondary' => Text::_ ( 'Secondary' ),
										'text-success' => Text::_ ( 'Success' ),
										'text-warning' => Text::_ ( 'Warning' ),
										'text-danger' => Text::_ ( 'Danger' ),
										'text-background' => Text::_ ( 'Background' )
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
								'desc' => Text::_ ( 'Choose one of the six heading elements to fit your semantic structure.' ),
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

						'separator_content_options' => array (
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
						'content_colors' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Select the predefined content text color.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-muted' => Text::_ ( 'Muted' ),
										'text-emphasis' => Text::_ ( 'Emphasis' ),
										'text-primary' => Text::_ ( 'Primary' ),
										'text-secondary' => Text::_ ( 'Secondary' ),
										'text-success' => Text::_ ( 'Success' ),
										'text-warning' => Text::_ ( 'Warning' ),
										'text-danger' => Text::_ ( 'Danger' )
								),
								'std' => ''
						),
						'custom_content_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' ),
								'depends' => array (
										array (
												'content_colors',
												'=',
												''
										)
								)
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
						'separator_button_style_options' => array (
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
								),
								'std' => '',
								'depends' => array (
										array (
												'button_title',
												'!=',
												''
										)
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
								'std' => '#1e87f0',
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
						'button_width' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Full Width' ),
								'std' => 0
						),
						'link_margin_top' => array (
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
