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
		'type' => 'content',
		'addon_name' => 'toggle',
		'title' => Text::_ ( 'Toggle' ),
		'desc' => Text::_ ( 'Hide, switch or change the appearance of different contents through a toggle.' ),
		'icon' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 21.7a5.2 5.2 0 100-10.4 5.2 5.2 0 000 10.4zm0 2.3a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" fill="currentColor"/><path opacity=".5" fill-rule="evenodd" clip-rule="evenodd" d="M24.5 21.7a5.2 5.2 0 100-10.4 5.2 5.2 0 000 10.4zm0 2.3a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" fill="currentColor"/></svg>',
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

				'separator_toggle_options' => [ 
						'title' => Text::_ ( 'Toggle' ),
						'fields' => [ 
								'toggle_type' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Content Type' ),
										'desc' => Text::_ ( 'Select toggle type, change the appearance of different contents through a toggle.' ),
										'values' => [ 
												'' => Text::_ ( 'Switcher' ),
												'toogle_text' => Text::_ ( 'Button' )
										],
										'std' => '',
										'inline' => true
								],
								'text_separator_before' => [ 
										'type' => 'separator'
								],
								'before_toggle' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Text before' ),
										'std' => 'Annual',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														'toogle_text'
												]
										]
								],

								'after_toggle' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Text after' ),
										'std' => 'Lifetime',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														'toogle_text'
												]
										]
								],
								'text_separator_after' => [ 
										'type' => 'separator'
								],
								'toggle_align' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Button Alignment' ),
										'desc' => Text::_ ( 'These classes define the alignment of flex items.' ),
										'values' => [ 
												'uk-flex-left' => Text::_ ( 'Left' ),
												'uk-flex-center' => Text::_ ( 'Center' ),
												'uk-flex-right' => Text::_ ( 'Right' )
										],
										'std' => 'uk-flex-center',
										'inline' => true
								],

								'toggle_animation' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Animation' ),
										'desc' => Text::_ ( 'Add animations to items when toggling between them.' ),
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
												'slide-right;' => Text::_ ( 'Slide Right 100%' )
										],
										'std' => 'fade',
										'inline' => true
								],

								'toggle_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Onclick Background' ),
										'desc' => Text::_ ( 'Background color for toggle color' ),
										'std' => '#1e87f0',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														'toogle_text'
												]
										]
								],
								'background_separator_after' => [ 
										'type' => 'separator'
								],
								'visible_text' => [ 
										'type' => 'textarea',
										'title' => Text::_ ( 'Visible Content' ),
										'desc' => Text::_ ( 'Content visible before clicking toggle button' ),
										'std' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														'toogle_text'
												]
										]
								],

								'hidden_text' => [ 
										'type' => 'textarea',
										'title' => Text::_ ( 'Hidden Content' ),
										'desc' => Text::_ ( 'Content display after clicking on toggle button' ),
										'std' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														'toogle_text'
												]
										]
								],

								'button_title' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Button text' ),
										'std' => 'Read more',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														''
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
														'button_style',
														'=',
														'custom'
												],
												[ 
														'toggle_type',
														'!=',
														''
												]
										]
								],

								'button_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'Set the button style.' ),
										'values' => [ 
												'' => Text::_ ( 'Button Default' ),
												'primary' => Text::_ ( 'Button Primary' ),
												'secondary' => Text::_ ( 'Button Secondary' ),
												'danger' => Text::_ ( 'Button Danger' ),
												'text' => Text::_ ( 'Button Text' ),
												'link' => Text::_ ( 'Link' ),
												'link-muted' => Text::_ ( 'Link Muted' ),
												'link-text' => Text::_ ( 'Link Text' ),
												'custom' => Text::_ ( 'Custom' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														''
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
														'button_style',
														'=',
														'custom'
												]
										]
								],

								'button_background' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Background Color' ),
										'std' => '#1e87f0',
										'depends' => [ 
												[ 
														'button_style',
														'=',
														'custom'
												],
												[ 
														'button_status',
														'=',
														'normal'
												],
												[ 
														'toggle_type',
														'!=',
														''
												]
										]
								],

								'button_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Button Color' ),
										'std' => '#ffffff',
										'depends' => [ 
												[ 
														'button_style',
														'=',
														'custom'
												],
												[ 
														'button_status',
														'=',
														'normal'
												],
												[ 
														'toggle_type',
														'!=',
														''
												]
										]
								],

								'button_background_hover' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Hover Background Color' ),
										'std' => '#0f7ae5',
										'depends' => [ 
												[ 
														'button_style',
														'=',
														'custom'
												],
												[ 
														'button_status',
														'=',
														'hover'
												],
												[ 
														'toggle_type',
														'!=',
														''
												]
										]
								],

								'button_hover_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Hover Button Color' ),
										'std' => '#ffffff',
										'depends' => [ 
												[ 
														'button_style',
														'=',
														'custom'
												],
												[ 
														'button_status',
														'=',
														'hover'
												],
												[ 
														'toggle_type',
														'!=',
														''
												]
										]
								],

								'button_size' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Button Size' ),
										'desc' => Text::_ ( 'Set the size for multiple buttons.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'uk-button-small' => Text::_ ( 'Small' ),
												'uk-button-large' => Text::_ ( 'Large' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														''
												]
										]
								],

								'button_hidden_text' => [ 
										'type' => 'textarea',
										'title' => Text::_ ( 'Content on click' ),
										'desc' => Text::_ ( 'Content display after clicking on text button' ),
										'std' => '',
										'depends' => [ 
												[ 
														'toggle_type',
														'!=',
														''
												]
										]
								]
						]
				],

				'separator_title_style_options' => [ 
						'title' => Text::_ ( 'Title' ),
						'fields' => [ 
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
										'std' => 'h5',
										'inline' => true
								],

								'title_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.ui-rbs-head-1, .ui-rbs-head-2 { font-family: {{ VALUE }}; }'
										)
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
										'inline' => true
								],

								'title_color' => [ 
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
										'inline' => true
								],

								'custom_title_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Custom Color' ),
										'depends' => [ 
												[ 
														'title_color',
														'=',
														''
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
										'inline' => true
								],

								'heading_selector' => [ 
										'type' => 'headings',
										'title' => Text::_ ( 'HTML Element' ),
										'desc' => Text::_ ( 'Choose one of the eight heading elements to fit your semantic structure.' ),
										'std' => 'div'
								]
						]
				],

				'separator_content_style_options' => [ 
						'title' => Text::_ ( 'Content' ),
						'fields' => [ 
								'content_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.toggle-content-inner { font-family: {{ VALUE }}; }'
										)
								],

								'content_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Color' )
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
