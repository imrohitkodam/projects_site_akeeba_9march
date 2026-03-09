<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Language\Text;

// no direct accees
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JpagebuilderConfig::addonConfig ( [ 
		'type' => 'repeatable',
		'addon_name' => 'cardheader',
		'title' => Text::_ ( 'Card Header' ),
		'desc' => Text::_ ( 'Card Header and Footer' ),
		'icon' => '<svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg"> <polyline fill="none" stroke="#444" stroke-width="1.9" points="7,12.728 11.728,8 17.909,14.182"></polyline> <polyline fill="none" stroke="#444" stroke-width="1.9" points="15.455,11 17.273,9.182 23,14.909"></polyline> <rect fill="#444" x="6" y="18" width="18" height="2"></rect> <rect fill="#444" x="8" y="21" width="14" height="2"></rect> <rect fill="none" stroke="#444" stroke-width="2" x="3" y="3" width="24" height="24"></rect> </svg>',
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
				'cardheader_item' => [ 
						'title' => Text::_ ( 'Card Header Items' ),
						'fields' => [ 
								'ui_cardheader_item' => [ 
										'type' => 'repeatable',
										'title' => Text::_ ( 'Items' ),
										'attr' => [ 

												'title' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Title' ),
														'std' => 'Item'
												],

												'company' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Meta' ),
														'std' => 'Product Manager'
												],

												'avatar' => [ 
														'type' => 'media',
														'hide_alt_text' => true,
														'title' => Text::_ ( 'Image' )
												],

												'message' => [ 
														'type' => 'editor',
														'title' => Text::_ ( 'Content' ),
														'std' => 'Our Sales Consultant will be available to advise and guide you throughout the move process. Our coordinators are focused on ensuring that every move runs smoothly.'
												],

												'title_link' => [ 
														'type' => 'link',
														'title' => Text::_ ( 'Link' ),
														'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_URL_DESC' )
												],

												'button_title' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Link Text' ),
														'std' => 'Read more',
														'depends' => [ 
																[ 
																		'title_link',
																		'!=',
																		''
																]
														]
												],

												'link_aria_label' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Link ARIA Label' ),
														'desc' => Text::_ ( 'Enter a descriptive text label to make it accessible if the link has no visible text.' ),
														'std' => ''
												]
										]
								]
						]
				],

				'grid_style_options' => [ 
						'title' => Text::_ ( 'Display' ),
						'fields' => [ 
								'grid_style_tab' => [ 
										'type' => 'buttons',
										'std' => 'normal',
										'values' => [ 
												[ 
														'label' => 'Grid',
														'value' => 'grid'
												],
												[ 
														'label' => 'Columns',
														'value' => 'columns'
												]
										],
										'std' => 'grid',
										'tabs' => true
								],

								'parallax' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Parallax' ),
										'desc' => Text::_ ( 'To move single columns of a grid at different speeds while scrolling. Turn off global animation if you use parallax feature.' ),
										'min' => 0,
										'max' => 600,
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												]
										]
								],

								'grid_column_gap' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Column Gap' ),
										'desc' => Text::_ ( 'Set the size of the gap between the grid columns.' ),
										'values' => [ 
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'' => Text::_ ( 'Default' ),
												'large' => Text::_ ( 'Large' ),
												'collapse' => Text::_ ( 'None' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												]
										]
								],

								'grid_row_gap' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Row Gap' ),
										'desc' => Text::_ ( 'Set the size of the gap between the grid rows.' ),
										'values' => [ 
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'' => Text::_ ( 'Default' ),
												'large' => Text::_ ( 'Large' ),
												'collapse' => Text::_ ( 'None' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												]
										]
								],

								'divider' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Show dividers' ),
										'desc' => Text::_ ( 'Select this option to separate grid cells with lines.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												]
										]
								],

								'grid_column_align' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Center columns' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												]
										]
								],

								'grid_row_align' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Center rows' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												]
										]
								],

								'phone_portrait' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Phone Portrait' ),
										'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
										'values' => [ 
												'1' => Text::_ ( '1 Columns' ),
												'2' => Text::_ ( '2 Columns' ),
												'3' => Text::_ ( '3 Columns' ),
												'4' => Text::_ ( '4 Columns' ),
												'5' => Text::_ ( '5 Columns' ),
												'6' => Text::_ ( '6 Columns' ),
												'auto' => Text::_ ( 'Auto' )
										],
										'std' => '1',
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'columns'
												]
										]
								],

								'phone_landscape' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Phone Landscape' ),
										'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'1' => Text::_ ( '1 Columns' ),
												'2' => Text::_ ( '2 Columns' ),
												'3' => Text::_ ( '3 Columns' ),
												'4' => Text::_ ( '4 Columns' ),
												'5' => Text::_ ( '5 Columns' ),
												'6' => Text::_ ( '6 Columns' ),
												'auto' => Text::_ ( 'Auto' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'columns'
												]
										]
								],

								'tablet_landscape' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Tablet Landscape' ),
										'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'1' => Text::_ ( '1 Columns' ),
												'2' => Text::_ ( '2 Columns' ),
												'3' => Text::_ ( '3 Columns' ),
												'4' => Text::_ ( '4 Columns' ),
												'5' => Text::_ ( '5 Columns' ),
												'6' => Text::_ ( '6 Columns' ),
												'auto' => Text::_ ( 'Auto' )
										],
										'std' => '3',
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'columns'
												]
										]
								],

								'desktop' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Desktop' ),
										'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'1' => Text::_ ( '1 Columns' ),
												'2' => Text::_ ( '2 Columns' ),
												'3' => Text::_ ( '3 Columns' ),
												'4' => Text::_ ( '4 Columns' ),
												'5' => Text::_ ( '5 Columns' ),
												'6' => Text::_ ( '6 Columns' ),
												'auto' => Text::_ ( 'Auto' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'columns'
												]
										]
								],

								'large_screens' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Large Screens' ),
										'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
										'values' => [ 
												'' => Text::_ ( 'Inherit' ),
												'1' => Text::_ ( '1 Columns' ),
												'2' => Text::_ ( '2 Columns' ),
												'3' => Text::_ ( '3 Columns' ),
												'4' => Text::_ ( '4 Columns' ),
												'5' => Text::_ ( '5 Columns' ),
												'6' => Text::_ ( '6 Columns' ),
												'auto' => Text::_ ( 'Auto' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'columns'
												]
										]
								]
						]
				],

				'style_tab_options' => [ 
						'title' => Text::_ ( 'Card & Image' ),
						'fields' => [ 
								'card_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'Select on of the boxed card styles or a blank card.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'default' => Text::_ ( 'Card Default' ),
												'primary' => Text::_ ( 'Card Primary' ),
												'secondary' => Text::_ ( 'Card Secondary' ),
												'hover' => Text::_ ( 'Card Hover' )
										],
										'std' => 'default',
										'inline' => true
								],

								'card_size' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Size' ),
										'desc' => Text::_ ( 'Define the card\'s size by selecting the padding between the card and its content.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'uk-card-small' => Text::_ ( 'Small' ),
												'uk-card-large' => Text::_ ( 'Large' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'card_style',
														'!=',
														''
												]
										]
								],

								'card_width' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Max Width' ),
										'desc' => Text::_ ( 'Set the maximum width.' ),
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

								'image_loading' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Load image eagerly' ),
										'desc' => Text::_ ( 'By default, images are loaded lazy. Enable eager loading for images in the initial viewport.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0
								],

								'avatar_width' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Image width' ),
										'desc' => Text::_ ( 'Setting just one value preserves the original proportions. The image will be resized and cropped automatically, and where possible, high resolution images will be auto-generated.' ),
										'std' => 40,
										'min' => 16,
										'max' => 128
								],

								'avatar_shape' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Border' ),
										'desc' => Text::_ ( 'Select the image\'s border style.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'uk-border-rounded' => Text::_ ( 'Rounded' ),
												'uk-border-circle' => Text::_ ( 'Circle' ),
												'uk-border-pill' => Text::_ ( 'Pill' )
										],
										'std' => '',
										'inline' => true
								]
						]
				],

				'separator_style_tab' => [ 
						'title' => Text::_ ( 'Content Style' ),
						'fields' => [ 
								'style_tab' => [ 
										'type' => 'buttons',
										'std' => 'normal',
										'values' => [ 
												[ 
														'label' => 'Title',
														'value' => 'title'
												],
												[ 
														'label' => 'Meta',
														'value' => 'meta'
												],
												[ 
														'label' => 'Content',
														'value' => 'content'
												],
												[ 
														'label' => 'Link',
														'value' => 'link'
												]
										],
										'std' => 'title',
										'tabs' => true
								],

								'title_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.ui-title { font-family: {{ VALUE }}; }'
										),
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'title'
												]
										]
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
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'title_decoration' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Decoration' ),
										'desc' => Text::_ ( 'Decorate the title with a divider, bullet or a line that is vertically centered to the title' ),
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
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'title_decoration_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Decoration Color' ),
										'depends' => [ 
												[ 
														'title_decoration',
														'!=',
														''
												],
												[ 
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'title_decoration_width' => [ 
										'type' => 'slider',
										'min' => 1,
										'max' => 100,
										'std' => 1,
										'title' => Text::_ ( 'Decoration Width' ),
										'depends' => [ 
												[ 
														'title_decoration',
														'!=',
														''
												],
												[ 
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'title_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Color' ),
										'desc' => Text::_ ( 'Select the title text color.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'muted' => Text::_ ( 'Muted' ),
												'emphasis' => Text::_ ( 'Emphasis' ),
												'primary' => Text::_ ( 'Primary' ),
												'secondary' => Text::_ ( 'Secondary' ),
												'success' => Text::_ ( 'Success' ),
												'warning' => Text::_ ( 'Warning' ),
												'danger' => Text::_ ( 'Danger' ),
												'background' => Text::_ ( 'Background' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'custom_title_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Custom Color' ),
										'depends' => [ 
												[ 
														'title_color',
														'=',
														''
												],
												[ 
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'heading_selector' => [ 
										'type' => 'headings',
										'title' => Text::_ ( 'HTML Element' ),
										'desc' => Text::_ ( 'Choose one of the eight heading elements to fit your semantic structure.' ),
										'std' => 'h3',
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'title_margin_top' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Margin Top' ),
										'desc' => Text::_ ( 'Set the top margin.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' ),
												'remove' => Text::_ ( 'None' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'meta_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.ui-meta { font-family: {{ VALUE }}; }'
										),
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'meta'
												]
										]
								],

								'meta_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'Select a predefined meta text style, including color, size and font-family' ),
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
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'meta'
												]
										]
								],

								'meta_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Color' ),
										'desc' => Text::_ ( 'Select the title text color.' ),
										'values' => [ 
												'' => Text::_ ( 'Custom' ),
												'muted' => Text::_ ( 'Muted' ),
												'emphasis' => Text::_ ( 'Emphasis' ),
												'primary' => Text::_ ( 'Primary' ),
												'secondary' => Text::_ ( 'Secondary' ),
												'success' => Text::_ ( 'Success' ),
												'warning' => Text::_ ( 'Warning' ),
												'danger' => Text::_ ( 'Danger' ),
												'background' => Text::_ ( 'Background' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'meta'
												]
										]
								],

								'custom_meta_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Custom Color' ),
										'depends' => [ 
												[ 
														'meta_color',
														'=',
														''
												],
												[ 
														'style_tab',
														'=',
														'meta'
												]
										]
								],

								'meta_alignment' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Alignment' ),
										'desc' => Text::_ ( 'Align the meta text above or below the title.' ),
										'values' => [ 
												'top' => Text::_ ( 'Top' ),
												'' => Text::_ ( 'Bottom' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'meta'
												]
										]
								],

								'meta_margin_top' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Margin Top' ),
										'desc' => Text::_ ( 'Set the top margin.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' ),
												'remove' => Text::_ ( 'None' )
										],
										'std' => 'small',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'meta'
												]
										]
								],

								'content_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.ui-content { font-family: {{ VALUE }}; }'
										),
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'content'
												]
										]
								],

								'content_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'Select a predefined meta text style, including color, size and font-family' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'text-meta' => Text::_ ( 'Text Meta' ),
												'text-lead' => Text::_ ( 'Text Lead' ),
												'text-small' => Text::_ ( 'Text Small' ),
												'text-large' => Text::_ ( 'Text Large' ),
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
												'h6' => Text::_ ( 'Heading H6' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'content'
												]
										]
								],

								'content_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Color' ),
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'content'
												]
										]
								],

								'content_margin_top' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Margin Top' ),
										'desc' => Text::_ ( 'Set the top margin.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' ),
												'remove' => Text::_ ( 'None' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'content'
												]
										]
								],

								'all_button_title' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Text' ),
										'std' => 'Read more',
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],

								'link_button_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'Set the button style.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'primary' => Text::_ ( 'Primary' ),
												'secondary' => Text::_ ( 'Secondary' ),
												'danger' => Text::_ ( 'Danger' ),
												'text' => Text::_ ( 'Text' ),
												'link' => Text::_ ( 'Link' ),
												'link-muted' => Text::_ ( 'Link Muted' ),
												'link-text' => Text::_ ( 'Link Text' ),
												'custom' => Text::_ ( 'Custom' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],
								'button_separator_above' => [ 
										'type' => 'separator',
										'depends' => [ 
												[ 
														'link_button_style',
														'=',
														'custom'
												],
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],
								'button_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.uk-button-custom { font-family: {{ VALUE }}; }'
										),
										'depends' => [ 
												[ 
														'link_button_style',
														'=',
														'custom'
												],
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],

								'button_status' => [ 
										'type' => 'buttons',
										'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_ENABLE_BACKGROUND_OPTIONS' ),
										'std' => 'normal',
										'values' => [ 
												[ 
														'label' => 'Normal',
														'value' => 'normal'
												],
												[ 
														'label' => 'Hover',
														'value' => 'hover'
												]
										],
										'tabs' => true,
										'depends' => [ 
												[ 
														'link_button_style',
														'=',
														'custom'
												],
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],

								'button_background' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Background Color' ),
										'std' => '#1e87f0',
										'depends' => [ 
												[ 
														'link_button_style',
														'=',
														'custom'
												],
												[ 
														'button_status',
														'=',
														'normal'
												],
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],

								'button_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Button Color' ),
										'std' => '#ffffff',
										'depends' => [ 
												[ 
														'link_button_style',
														'=',
														'custom'
												],
												[ 
														'button_status',
														'=',
														'normal'
												],
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],

								'button_background_hover' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Hover Background Color' ),
										'std' => '#0f7ae5',
										'depends' => [ 
												[ 
														'link_button_style',
														'=',
														'custom'
												],
												[ 
														'button_status',
														'=',
														'hover'
												],
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],

								'button_hover_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Hover Button Color' ),
										'std' => '#ffffff',
										'depends' => [ 
												[ 
														'link_button_style',
														'=',
														'custom'
												],
												[ 
														'button_status',
														'=',
														'hover'
												],
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],
								'button_separator_below' => [ 
										'type' => 'separator',
										'depends' => [ 
												[ 
														'link_button_style',
														'=',
														'custom'
												],
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],
								'link_button_size' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Button Size' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'uk-button-small' => Text::_ ( 'Small' ),
												'uk-button-large' => Text::_ ( 'Large' )
										],
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'link'
												]
										]
								],

								'button_margin_top' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Margin Top' ),
										'desc' => Text::_ ( 'Set the top margin.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' ),
												'remove' => Text::_ ( 'None' )
										],
										'std' => 'remove',
										'inline' => true,
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'link'
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
										'depends' => [ 
												[ 
														'addon_max_width',
														'!=',
														''
												]
										]
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
										'depends' => [ 
												[ 
														'addon_max_width',
														'!=',
														''
												]
										]
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
										'depends' => [ 
												[ 
														'addon_max_width',
														'!=',
														''
												],
												[ 
														'block_align_breakpoint',
														'!=',
														''
												]
										]
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

								'delay_element_animations' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Animation Delay' ),
										'desc' => Text::_ ( 'Delay the element animations in milliseconds.' ),
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
