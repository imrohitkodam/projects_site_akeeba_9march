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
		'addon_name' => 'table',
		'title' => Text::_ ( 'Table' ),
		'desc' => Text::_ ( 'Easily create nice looking tables, which come in different styles.' ),
		'icon' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.27 4.27A4.333 4.333 0 014.332 3h23.334A4.333 4.333 0 0132 7.333v17.334A4.333 4.333 0 0127.667 29H4.333A4.333 4.333 0 010 24.667V7.333C0 6.184.457 5.082 1.27 4.27zM2 13.666v5.666h13v-5.666H2zm15 0v5.666h13v-5.666H17zm13-2V7.333A2.333 2.333 0 0027.667 5H4.333A2.333 2.333 0 002 7.333v4.334h28zm0 9.666H17V27L27.667 27A2.333 2.333 0 0030 24.667v-3.334zM15 27v-5.666H2v3.334A2.333 2.333 0 004.333 27H15z" fill="currentColor"/></svg>',
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
						// Repeatable Items
						'ui_table_item' => array (
								'title' => Text::_ ( 'Items' ),
								'attr' => array (
										'title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Title' ),
												'std' => 'Item'
										),
										'table_meta' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Meta' )
										),
										'table_content' => array (
												'type' => 'editor',
												'title' => Text::_ ( 'Content' ),
												'std' => 'Lorem ipsum dolor sit amet.'
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
												'title' => Text::_ ( 'Button Title' ),
												'placeholder' => 'Learn more',
												'std' => 'Learn more',
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
						'separator_table_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Table' )
						),
						'style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select the table style, default, divider or striped row style.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'divider' => Text::_ ( 'Divider' ),
										'striped' => Text::_ ( 'Striped' )
								),
								'std' => ''
						),
						'highlight_row' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Highlight the hovered row' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'remove_left_right_padding' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Remove left and right padding' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'size' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Size' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => ''
						),
						'order' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Order' ),
								'desc' => Text::_ ( 'Define the order of the table cells.' ),
								'values' => array (
										'' => Text::_ ( 'Meta, Image, Title, Content, Link' ),
										'2' => Text::_ ( 'Title, Image, Meta, Content, Link' ),
										'3' => Text::_ ( 'Image, Title, Content, Meta, Link' ),
										'4' => Text::_ ( 'Image, Title, Meta, Content, Link' ),
										'5' => Text::_ ( 'Title, Meta, Content, Link, Image' ),
										'6' => Text::_ ( 'Meta, Title, Content, Link, Image' )
								),
								'std' => ''
						),
						'vertical_alignment' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Vertical Alignment' ),
								'desc' => Text::_ ( 'Vertically center table cells.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'responsive' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Responsive' ),
								'desc' => Text::_ ( 'Stack columns on small devices or enable overflow scroll for the container.' ),
								'values' => array (
										'overflow' => Text::_ ( 'Scroll overflow' ),
										'responsive' => Text::_ ( 'Stacked' )
								),
								'std' => 'overflow'
						),
						'table_width_title' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Title Width' ),
								'desc' => Text::_ ( 'Define the width of the title cell.' ),
								'values' => array (
										'' => Text::_ ( 'Expand' ),
										'shrink' => Text::_ ( 'Shrink' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' )
								),
								'std' => 'shrink'
						),
						'table_width_meta' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Meta Width' ),
								'desc' => Text::_ ( 'Define the width of the meta cell.' ),
								'values' => array (
										'' => Text::_ ( 'Expand' ),
										'shrink' => Text::_ ( 'Shrink' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' )
								),
								'std' => 'shrink'
						),
						'table_width_content' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Content Width' ),
								'desc' => Text::_ ( 'Define the width of the content cell.' ),
								'values' => array (
										'' => Text::_ ( 'Expand' ),
										'shrink' => Text::_ ( 'Shrink' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' )
								),
								'std' => ''
						),
						'table_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background' ),
								'std' => '',
								'depends' => array (
										array (
												'style',
												'!=',
												'divider'
										),
										array (
												'style',
												'!=',
												'striped'
										)
								)
						),
						'table_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' ),
								'depends' => array (
										array (
												'style',
												'!=',
												'divider'
										),
										array (
												'style',
												'!=',
												'striped'
										)
								)
						),
						'separator_head_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Table Head' )
						),
						'head_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'std' => '#1e87f0'
						),
						'head_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' )
						),
						'title' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Title' ),
								'placeholder' => 'Title',
								'desc' => Text::_ ( 'Enter a table header text for the title column' )
						),
						'meta' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Meta' ),
								'placeholder' => 'Meta',
								'desc' => Text::_ ( 'Enter a table header text for the meta column' )
						),
						'content' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Content' ),
								'placeholder' => 'Content',
								'desc' => Text::_ ( 'Enter a table header text for the content column' )
						),
						'image' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Image' ),
								'placeholder' => 'Image',
								'desc' => Text::_ ( 'Enter a table header text for the link column' )
						),
						'link' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Link' ),
								'placeholder' => 'Link',
								'desc' => Text::_ ( 'Enter a table header text for the link column' )
						),
						'separator_content_hide_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Display' ),
								'desc' => Text::_ ( 'Show or hide content fields without the need to delete the content itself.' )
						),
						'hide_title' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Hide the title' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'hide_meta' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Hide the meta text' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'hide_content' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Hide the content' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'hide_image' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Hide the image' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'hide_link' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Hide the link' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
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
						'title_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.el-title { font-family: {{ VALUE }}; }'
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
										'css' => '.el-meta { font-family: {{ VALUE }}; }'
								)
						),
						'meta_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a predefined meta text style, including color, size and font-family' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-meta' => Text::_ ( 'Meta' ),
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
										'css' => '.el-content { font-family: {{ VALUE }}; }'
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
						'separator_image_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Image' )
						),
						'image_width' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Image Width' ),
								'desc' => Text::_ ( 'Define image width' ),
								'std' => '225',
								'min' => 120,
								'max' => 1200
						),
						'image_border' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Border' ),
								'desc' => Text::_ ( 'Select the image\'s border style.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'rounded' => Text::_ ( 'Rounded' ),
										'circle' => Text::_ ( 'Circle' ),
										'pill' => Text::_ ( 'Pill' )
								),
								'std' => ''
						),
						'box_shadow' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Box Shadow' ),
								'desc' => Text::_ ( 'Select the image\'s box shadow size.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' )
								),
								'std' => ''
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
						'expand_width' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Expand width to table cell' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
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
										'' => 'Always',
										's' => 'Small (Phone Landscape)',
										'm' => 'Medium (Tablet Landscape)',
										'l' => 'Large (Desktop)',
										'xl' => 'X-Large (Large Screens)'
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
										'' => 'Inherit',
										'left' => 'Left',
										'center' => 'Center',
										'right' => 'Right',
										'justify' => 'Justify'
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
										'' => 'Left',
										'auto' => 'Center',
										'auto-left' => 'Right'
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
										'' => 'Always',
										's' => 'Small (Phone Landscape)',
										'm' => 'Medium (Tablet Landscape)',
										'l' => 'Large (Desktop)',
										'xl' => 'X-Large (Large Screens)'
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
