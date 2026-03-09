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
		'addon_name' => 'modalbox',
		'title' => Text::_ ( 'Modal' ),
		'desc' => Text::_ ( 'Create modal dialogs with different styles and transitions.' ),
		'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="4,5 1,5 1,9 2,9 2,6 4,6"></polygon><polygon points="1,16 2,16 2,18 4,18 4,19 1,19"></polygon><polygon points="14,16 14,19 11,19 11,18 13,18 13,16"></polygon><rect fill="none" stroke="#000" x="5.5" y="1.5" width="13" height="13"></rect><rect x="1" y="11" width="1" height="3"></rect><rect x="6" y="18" width="3" height="1"></rect></svg>',
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

				'separator_modal_options' => [ 
						'title' => Text::_ ( 'Modal' ),
						'fields' => [ 
								'modal_selector' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Type' ),
										'desc' => Text::_ ( 'Select a type of modal selector from the below list' ),
										'values' => [ 
												'' => Text::_ ( 'Button' ),
												'image_selector' => Text::_ ( 'Image' )
										],
										'std' => '',
										'inline' => true
								],
								'modal_alignment_separator' => [ 
										'type' => 'separator'
								],
								'button_text' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Link Text' ),
										'std' => 'Open Popup',
										'depends' => [ 
												[ 
														'modal_selector',
														'!=',
														'image_selector'
												]
										]
								],

								'button_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'depends' => [ 
												[ 
														'modal_selector',
														'!=',
														'image_selector'
												]
										],
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.uk-button-custom { font-family: {{ VALUE }}; }'
										)
								],

								'button_type' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Button Style' ),
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
														'modal_selector',
														'!=',
														'image_selector'
												]
										]
								],
								'button_separator_above' => [ 
										'type' => 'separator',
										'depends' => [ 
												[ 
														'button_type',
														'=',
														'custom'
												],
												[ 
														'modal_selector',
														'!=',
														'image_selector'
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
														'button_type',
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
														'button_type',
														'=',
														'custom'
												],
												[ 
														'modal_selector',
														'!=',
														'image_selector'
												],
												[ 
														'button_status',
														'=',
														'normal'
												]
										]
								],

								'button_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Button Color' ),
										'std' => '#ffffff',
										'depends' => [ 
												[ 
														'button_type',
														'=',
														'custom'
												],
												[ 
														'modal_selector',
														'!=',
														'image_selector'
												],
												[ 
														'button_status',
														'=',
														'normal'
												]
										]
								],

								'button_background_hover' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Hover Background Color' ),
										'std' => '#0f7ae5',
										'depends' => [ 
												[ 
														'button_type',
														'=',
														'custom'
												],
												[ 
														'modal_selector',
														'!=',
														'image_selector'
												],
												[ 
														'button_status',
														'=',
														'hover'
												]
										]
								],

								'button_hover_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Hover Button Color' ),
										'std' => '#ffffff',
										'depends' => [ 
												[ 
														'button_type',
														'=',
														'custom'
												],
												[ 
														'modal_selector',
														'!=',
														'image_selector'
												],
												[ 
														'button_status',
														'=',
														'hover'
												]
										]
								],
								'button_separator_below' => [ 
										'type' => 'separator',
										'depends' => [ 
												[ 
														'button_type',
														'=',
														'custom'
												],
												[ 
														'modal_selector',
														'!=',
														'image_selector'
												]
										]
								],
								'button_size' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Button Size' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'small' => Text::_ ( 'Small' ),
												'large' => Text::_ ( 'Large' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'modal_selector',
														'!=',
														'image_selector'
												]
										],
										'inline' => true
								],

								'image' => [ 
										'type' => 'media',
										'title' => Text::_ ( 'Image' ),
										'std' => '',
										'depends' => [ 
												[ 
														'modal_selector',
														'=',
														'image_selector'
												]
										]
								],

								'alt_text' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_ALT_TEXT' ),
										'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_ALT_TEXT_DESC' ),
										'std' => 'Image ALT',
										'depends' => [ 
												[ 
														'modal_selector',
														'=',
														'image_selector'
												]
										]
								]
						]
				],

				'separator_image_options' => [ 
						'title' => Text::_ ( 'Image Settings' ),
						'fields' => [ 
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

								'image_border' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Border' ),
										'desc' => Text::_ ( 'Select the image\'s border style.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'uk-border-circle' => Text::_ ( 'Circle' ),
												'uk-border-rounded' => Text::_ ( 'Rounded' ),
												'uk-border-pill' => Text::_ ( 'Pill' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'image',
														'!=',
														''
												]
										]
								],

								'image_transition' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Transition' ),
										'desc' => Text::_ ( 'Select the image\'s transition style.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'scale-up' => Text::_ ( 'Scales Up' ),
												'scale-down' => Text::_ ( 'Scales Down' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'image',
														'!=',
														''
												]
										]
								],

								'box_shadow' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Box Shadow' ),
										'desc' => Text::_ ( 'Select the image\'s box shadow size.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'uk-box-shadow-small' => Text::_ ( 'Small' ),
												'uk-box-shadow-medium' => Text::_ ( 'Medium' ),
												'uk-box-shadow-large' => Text::_ ( 'Large' ),
												'uk-box-shadow-xlarge' => Text::_ ( 'X-Large' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'image',
														'!=',
														''
												]
										]
								],

								'hover_box_shadow' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Hover Box Shadow' ),
										'desc' => Text::_ ( 'Select the image\'s box shadow size on hover.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'uk-box-shadow-hover-small' => Text::_ ( 'Small' ),
												'uk-box-shadow-hover-medium' => Text::_ ( 'Medium' ),
												'uk-box-shadow-hover-large' => Text::_ ( 'Large' ),
												'uk-box-shadow-hover-xlarge' => Text::_ ( 'X-Large' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'image',
														'!=',
														''
												]
										]
								]
						],
						'depends' => [ 
								[ 
										'image',
										'!=',
										''
								]
						]
				],

				'separator_modal_content_options' => [ 
						'title' => Text::_ ( 'Modal Content' ),
						'fields' => [ 
								'modal_content_type' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Type' ),
										'desc' => Text::_ ( 'Select the modal content type like text or image/video' ),
										'values' => [ 
												'text' => Text::_ ( 'Text' ),
												'image' => Text::_ ( 'Image' ),
												'video' => Text::_ ( 'Html5 Video' ),
												'youtube' => Text::_ ( 'Youtube Video' ),
												'vimeo' => Text::_ ( 'Vimeo Video' )
										],
										'std' => 'text',
										'inline' => true
								],
								'modal_type_separator' => [ 
										'type' => 'separator'
								],
								'modal_content_title' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Title' ),
										'desc' => Text::_ ( 'Create The Modal Title.' ),
										'std' => 'Modal Popup',
										'depends' => [ 
												[ 
														'modal_content_type',
														'=',
														'text'
												]
										]
								],

								'modal_content_text' => [ 
										'type' => 'editor',
										'title' => Text::_ ( 'Content' ),
										'desc' => Text::_ ( 'Insert modal content that will be displayed in modal popup window.' ),
										'std' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
										'depends' => [ 
												[ 
														'modal_content_type',
														'!=',
														'video'
												],
												[ 
														'modal_content_type',
														'!=',
														'image'
												],
												[ 
														'modal_content_type',
														'!=',
														'youtube'
												],
												[ 
														'modal_content_type',
														'!=',
														'vimeo'
												]
										]
								],

								'modal_content_image' => [ 
										'type' => 'media',
										'title' => Text::_ ( 'Modal Image' ),
										'desc' => Text::_ ( 'Add image for content modal.' ),
										'std' => 'https://storejextensions.org/cdn/addons/image-placeholder.png',
										'depends' => [ 
												[ 
														'modal_content_type',
														'!=',
														'text'
												],
												[ 
														'modal_content_type',
														'!=',
														'video'
												],
												[ 
														'modal_content_type',
														'!=',
														'youtube'
												],
												[ 
														'modal_content_type',
														'!=',
														'vimeo'
												]
										]
								],

								'modal_content_video_url_mp4' => [ 
										'type' => 'media',
										'format' => 'video',
										'title' => Text::_ ( 'Video MP4' ),
										'std' => [ 
												'src' => 'https://storejextensions.org/cdn/templatesvideos/video-placeholder.mp4'
										],
										'hide_preview' => true,
										'depends' => [ 
												[ 
														'modal_content_type',
														'!=',
														'text'
												],
												[ 
														'modal_content_type',
														'!=',
														'image'
												],
												[ 
														'modal_content_type',
														'!=',
														'youtube'
												],
												[ 
														'modal_content_type',
														'!=',
														'vimeo'
												]
										]
								],

								'modal_content_video_url_ogv' => [ 
										'type' => 'media',
										'format' => 'video',
										'title' => Text::_ ( 'Video OGV URL' ),
										'desc' => Text::_ ( 'Add the ogv video format.' ),
										'hide_preview' => true,
										'depends' => [ 
												[ 
														'modal_content_type',
														'!=',
														'text'
												],
												[ 
														'modal_content_type',
														'!=',
														'image'
												],
												[ 
														'modal_content_type',
														'!=',
														'youtube'
												],
												[ 
														'modal_content_type',
														'!=',
														'vimeo'
												]
										]
								],

								'modal_content_video_youtube_url' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Youtube Video ID' ),
										'desc' => Text::_ ( 'The unique Youtube ID. Example: https://www.youtube.com/watch?v=usnlMCcuex8 so the Unique ID is: usnlMCcuex8' ),
										'std' => 'usnlMCcuex8',
										'depends' => [ 
												[ 
														'modal_content_type',
														'!=',
														'text'
												],
												[ 
														'modal_content_type',
														'!=',
														'image'
												],
												[ 
														'modal_content_type',
														'!=',
														'video'
												],
												[ 
														'modal_content_type',
														'!=',
														'vimeo'
												]
										]
								],

								'modal_content_video_vimeo_url' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Vimeo Video ID' ),
										'desc' => Text::_ ( 'The unique Vimeo video ID. Example: https://player.vimeo.com/video/1084537 so the Unique ID is: 1084537' ),
										'depends' => [ 
												[ 
														'modal_content_type',
														'!=',
														'text'
												],
												[ 
														'modal_content_type',
														'!=',
														'image'
												],
												[ 
														'modal_content_type',
														'!=',
														'video'
												],
												[ 
														'modal_content_type',
														'!=',
														'youtube'
												]
										]
								],

								'muted_video' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Automute Video' ),
										'std' => 0,
										'depends' => [ 
												[ 
														'modal_content_type',
														'!=',
														'text'
												],
												[ 
														'modal_content_type',
														'!=',
														'image'
												]
										]
								],

								'center_modal' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Center modal' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0
								]
						],
						'depends' => [ 
								[ 
										'image',
										'!=',
										''
								]
						]
				],

				'separator_title_style_options' => [ 
						'title' => Text::_ ( 'Title' ),
						'fields' => [ 
								'modal_heading_style' => [ 
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

								'modal_heading_selector' => [ 
										'type' => 'headings',
										'title' => Text::_ ( 'HTML Element' ),
										'desc' => Text::_ ( 'Choose one of the eight heading elements to fit your semantic structure.' ),
										'std' => 'h3'
								]
						],
						'depends' => [ 
								[ 
										'modal_content_type',
										'=',
										'text'
								]
						]
				],

				'separator_content_style_options' => [ 
						'title' => Text::_ ( 'Content' ),
						'fields' => [ 
								'content_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'Select a predefined content text style.' ),
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
										'depends' => [ 
												[ 
														'modal_content_type',
														'=',
														'text'
												]
										],
										'std' => '',
										'inline' => true
								],

								'content_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Color' ),
										'depends' => [ 
												[ 
														'modal_content_type',
														'=',
														'text'
												]
										]
								]
						],
						'depends' => [ 
								[ 
										'modal_content_type',
										'=',
										'text'
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
