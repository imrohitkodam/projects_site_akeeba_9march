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
		'addon_name' => 'testimonial_widget',
		'title' => Text::_ ( 'Testimonial' ),
		'desc' => Text::_ ( 'Nice things clients say about us' ),
		'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6,18.71 L6,14 L1,14 L1,1 L19,1 L19,14 L10.71,14 L6,18.71 L6,18.71 Z M2,13 L7,13 L7,16.29 L10.29,13 L18,13 L18,2 L2,2 L2,13 L2,13 Z"></path></svg>',
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

				'testimonial_options' => [ 
						'title' => Text::_ ( 'Testimonial Content' ),
						'fields' => [ 
								'name' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Name' ),
										'std' => 'Sarah Jones'
								],

								'company' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Company' ),
										'std' => 'Customer'
								],

								'avatar' => [ 
										'type' => 'media',
										'hide_alt_text' => true,
										'title' => Text::_ ( 'Avatar' ),
										'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_IMAGE_SELECT_DESC' )
								],

								'alt_text' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Image Alt' ),
										'placeholder' => 'Image Alt',
										'depends' => [ 
												[ 
														'avatar',
														'!=',
														''
												]
										],
										'inline' => true
								],
								'testimonial_alignment_separator' => [ 
										'type' => 'separator'
								],
								'client_review' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Client Rating' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'1' => Text::_ ( '1' ),
												'2' => Text::_ ( '2' ),
												'3' => Text::_ ( '3' ),
												'4' => Text::_ ( '4' ),
												'5' => Text::_ ( '5' )
										],
										'std' => '',
										'inline' => true
								],

								'message' => [ 
										'type' => 'editor',
										'title' => Text::_ ( 'Content' ),
										'std' => 'Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch.'
								],

								'link' => [ 
										'type' => 'link',
										'title' => Text::_ ( 'Link' )
								]
						]
				],

				'separator_testimonial_options' => [ 
						'title' => Text::_ ( 'Testimonial' ),
						'fields' => [ 
								'header_alignment' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Header Position' ),
										'values' => [ 
												'' => Text::_ ( 'Top' ),
												'bottom' => Text::_ ( 'Bottom' )
										],
										'std' => ''
								],

								'header_margin_top' => [ 
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
														'header_alignment',
														'=',
														'bottom'
												]
										]
								],

								'icon_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Rating Icon Color' ),
										'std' => '#fba311'
								],

								'icon_rating' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Rating Icon Type' ),
										'values' => [ 
												'' => Text::_ ( 'FontAwesome' ),
												'uikit' => Text::_ ( 'Uikit' )
										],
										'std' => ''
								],

								'rating_alignment' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Rating Position' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'image' => Text::_ ( 'Below Image' )
										],
										'std' => ''
								]
						]
				],

				'separator_card_options' => [ 
						'title' => Text::_ ( 'Card' ),
						'fields' => [ 
								'card_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'Select one of the boxed card styles or a blank panel.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'default' => Text::_ ( 'Card Default' ),
												'primary' => Text::_ ( 'Card Primary' ),
												'secondary' => Text::_ ( 'Card Secondary' ),
												'hover' => Text::_ ( 'Card Hover' ),
												'custom' => Text::_ ( 'Custom' )
										],
										'std' => '',
										'inline' => true
								],

								'panel_link' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Link Card' ),
										'desc' => Text::_ ( 'Link the whole card if a link exists.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0
								],

								'card_background' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Background Color' ),
										'std' => '#1e87f0',
										'depends' => [ 
												[ 
														'card_style',
														'=',
														'custom'
												]
										]
								],

								'card_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Color' ),
										'depends' => [ 
												[ 
														'card_style',
														'=',
														'custom'
												]
										]
								],

								'card_size' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Padding' ),
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

								'card_content_padding' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Padding' ),
										'desc' => Text::_ ( 'Add padding to the content if the image is top, bottom, left or right aligned.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'small' => Text::_ ( 'Small' ),
												'default' => Text::_ ( 'Default' ),
												'large' => Text::_ ( 'Large' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'card_style',
														'=',
														''
												]
										]
								]
						]
				],

				'content_style_tab_options' => [ 
						'title' => Text::_ ( 'Content Style' ),
						'fields' => [ 
								'content_style_tab' => [ 
										'type' => 'buttons',
										'std' => 'normal',
										'values' => [ 
												[ 
														'label' => 'Image',
														'value' => 'image'
												],
												[ 
														'label' => 'Name',
														'value' => 'name'
												],
												[ 
														'label' => 'Meta',
														'value' => 'meta'
												],
												[ 
														'label' => 'Content',
														'value' => 'content'
												]
										],
										'std' => 'image',
										'tabs' => true
								],

								'image_loading' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Load image eagerly' ),
										'desc' => Text::_ ( 'By default, images are loaded lazy. Enable eager loading for images in the initial viewport.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],

								'position' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Image Position' ),
										'desc' => Text::_ ( 'Display image above/below/inline/ testimonial content or none.' ),
										'values' => [ 
												'' => Text::_ ( 'Left' ),
												'right' => Text::_ ( 'Right' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],
								'image_grid_width' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Grid Width' ),
										'desc' => Text::_ ( 'Define the width of the image within the grid. Choose between percent and fixed widths or expand columns to the width of their content.' ),
										'values' => [ 
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
										],
										'std' => 'auto',
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],
								'image_grid_column_gap' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Grid Column Gap' ),
										'desc' => Text::_ ( 'Set the size of the gap between the image and the content.' ),
										'values' => [ 
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'' => Text::_ ( 'Default' ),
												'large' => Text::_ ( 'Large' ),
												'collapse' => Text::_ ( 'None' )
										],
										'std' => 'small',
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],
								'image_grid_breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Grid Breakpoint' ),
										'desc' => Text::_ ( 'Set the breakpoint from which grid cells will stack.' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												's' => Text::_ ( 'Small (Phone Landscape)' ),
												'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
												'l' => Text::_ ( 'Large (Desktop)' ),
												'xl' => Text::_ ( 'X-Large (Large Screens)' )
										],
										'std' => 'm',
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],
								'vertical_alignment' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Vertical Alignment' ),
										'desc' => Text::_ ( 'Vertically center grid cells.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 1,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],
								'avatar_width' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Avatar width' ),
										'std' => 90,
										'min' => 16,
										'max' => 128,
										'depends' => [ 
												[ 
														'avatar',
														'!=',
														''
												],
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],
								'avatar_shape' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Border radius' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'uk-border-rounded' => Text::_ ( 'Rounded' ),
												'uk-border-circle' => Text::_ ( 'Circle' ),
												'uk-border-pill' => Text::_ ( 'Pill' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'avatar',
														'!=',
														''
												],
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],
								'image_svg_inline' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Inline SVG' ),
										'desc' => Text::_ ( 'Inject SVG images into the page markup, so that they can easily be styled with CSS.' ),
										'std' => 0,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],
								'image_svg_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'SVG Color' ),
										'desc' => Text::_ ( 'Select the SVG color. It will only apply to supported elements defined in the SVG.' ),
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
										'depends' => [ 
												[ 
														'image_svg_inline',
														'=',
														1
												],
												[ 
														'content_style_tab',
														'=',
														'image'
												]
										]
								],

								'title_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Name Style' ),
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
														'content_style_tab',
														'=',
														'name'
												]
										]
								],

								'title_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.ui-author { font-family: {{ VALUE }}; }'
										),
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'name'
												]
										]
								],

								'font_weight' => [ 
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
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'name'
												]
										]
								],

								'title_text_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Color' ),
										'desc' => Text::_ ( 'Select the predefined title text color.' ),
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
														'content_style_tab',
														'=',
														'name'
												]
										]
								],

								'custom_title_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Custom Color' ),
										'depends' => [ 
												[ 
														'title_text_color',
														'=',
														''
												],
												[ 
														'content_style_tab',
														'=',
														'name'
												]
										]
								],

								'title_text_transform' => [ 
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
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'name'
												]
										]
								],

								'link_title' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Link Title' ),
										'desc' => Text::_ ( 'Link the title if a link exists.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'name'
												]
										]
								],

								'title_hover_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Hover Style' ),
										'desc' => Text::_ ( 'Set the hover style for a linked title.' ),
										'values' => [ 
												'reset' => Text::_ ( 'None' ),
												'heading' => Text::_ ( 'Heading Link' ),
												'' => Text::_ ( 'Default Link' )
										],
										'std' => 'reset',
										'inline' => true,
										'depends' => [ 
												[ 
														'link_title',
														'=',
														1
												],
												[ 
														'content_style_tab',
														'=',
														'name'
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
										'std' => 'remove',
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'name'
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
														'content_style_tab',
														'=',
														'name'
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
														'content_style_tab',
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
												'text-meta' => Text::_ ( 'Text Meta' ),
												'heading-small' => Text::_ ( 'Text Small' ),
												'h1' => Text::_ ( 'Heading H1' ),
												'h2' => Text::_ ( 'Heading H2' ),
												'h3' => Text::_ ( 'Heading H3' ),
												'h4' => Text::_ ( 'Heading H4' ),
												'h5' => Text::_ ( 'Heading H5' ),
												'h6' => Text::_ ( 'Heading H6' )
										],
										'std' => 'text-meta',
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'meta'
												]
										]
								],

								'meta_font_weight' => [ 
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
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'meta'
												]
										]
								],

								'meta_text_transform' => [ 
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
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'meta'
												]
										]
								],

								'meta_text_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Color' ),
										'desc' => Text::_ ( 'Select the predefined meta text color.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'muted' => Text::_ ( 'Muted' ),
												'emphasis' => Text::_ ( 'Emphasis' ),
												'primary' => Text::_ ( 'Primary' ),
												'secondary' => Text::_ ( 'Secondary' ),
												'success' => Text::_ ( 'Success' ),
												'warning' => Text::_ ( 'Warning' ),
												'danger' => Text::_ ( 'Danger' ),
												'background' => Text::_ ( 'Background' ),
												'light' => Text::_ ( 'Light' ),
												'dark' => Text::_ ( 'Dark' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
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
														'meta_text_color',
														'=',
														''
												],
												[ 
														'content_style_tab',
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
										'std' => 'remove',
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'meta'
												]
										]
								],

								'meta_element' => [ 
										'type' => 'headings',
										'title' => Text::_ ( 'HTML Element' ),
										'desc' => Text::_ ( 'Choose one of the eight heading elements to fit your semantic structure.' ),
										'std' => 'div',
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'meta'
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
												'h6' => Text::_ ( 'Heading H6' ),
												'custom' => Text::_ ( 'Custom' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'content'
												]
										]
								],

								'content_size' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Content Font Size' ),
										'placeholder' => 16,
										'std' => '16',
										'max' => 400,
										'depends' => [ 
												[ 
														'content_style',
														'=',
														'custom'
												],
												[ 
														'content_style_tab',
														'=',
														'content'
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
														'content_style_tab',
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
														'content_style_tab',
														'=',
														'content'
												]
										]
								],

								'content_dropcap' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Drop Cap' ),
										'desc' => Text::_ ( 'Display the first letter of the paragraph as a large initial.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
										'depends' => [ 
												[ 
														'content_style_tab',
														'=',
														'content'
												]
										]
								],

								'content_column' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Columns' ),
										'desc' => Text::_ ( 'Set the number of text columns.' ),
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
														'content_style_tab',
														'=',
														'content'
												]
										]
								],

								'content_column_divider' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Show dividers' ),
										'desc' => Text::_ ( 'Show a divider between text columns.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
										'depends' => [ 
												[ 
														'content_column',
														'!=',
														''
												]
										]
								],

								'content_column_breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Columns Breakpoint' ),
										'desc' => Text::_ ( 'Set the device width from which the text columns should apply' ),
										'values' => [ 
												'' => Text::_ ( 'Always' ),
												's' => Text::_ ( 'Small (Phone Landscape)' ),
												'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
												'l' => Text::_ ( 'Large (Desktop)' ),
												'xl' => Text::_ ( 'X-Large (Large Screens)' )
										],
										'std' => 'm',
										'depends' => [ 
												[ 
														'content_column',
														'!=',
														''
												]
										]
								],

								'content_text_transform' => [ 
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
										'inline' => true,
										'depends' => [ 
												[ 
														'content_style_tab',
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
														'content_style_tab',
														'=',
														'content'
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
