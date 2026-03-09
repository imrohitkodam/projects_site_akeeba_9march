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
		'addon_name' => 'simplepricing',
		'title' => Text::_ ( 'Simple Pricing' ),
		'desc' => Text::_ ( 'Price box allows you to display not just the cost, but also the features you wish to portray.' ),
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/simplepricing/assets/images/icon.png',
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
						'price_title' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Title' ),
								'std' => 'Basic'
						),
						'price_description' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Description' ),
								'std' => 'Free trial 30 days.'
						),
						'price' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Price' ),
								'desc' => Text::_ ( 'Define the price for price box' ),
								'std' => '49'
						),
						'currency' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Currency' ),
								'placeholder' => '$',
								'std' => '$'
						),

						'period' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Period' ),
								'std' => '',
								'placeholder' => '/ monthly'
						),
						'label_text' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Highlight' ),
								'desc' => Text::_ ( 'Indicate important notes and highlight parts of your content.' ),
								'std' => '',
								'placeholder' => 'Popular'
						),
						'label_styles' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Highlight Style' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'uk-label-success' => Text::_ ( 'Success' ),
										'uk-label-warning' => Text::_ ( 'Warning' ),
										'uk-label-danger' => Text::_ ( 'Danger' ),
										'uk-label-custom' => Text::_ ( 'Custom' )
								),
								'depends' => array (
										array (
												'label_text',
												'!=',
												''
										)
								)
						),
						'label_background_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'depends' => array (
										array (
												'label_text',
												'!=',
												''
										),
										array (
												'label_styles',
												'=',
												'uk-label-custom'
										)
								)
						),
						'label_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.uk-label-custom { font-family: {{ VALUE }}; }'
								),
								'depends' => array (
										array (
												'label_text',
												'!=',
												''
										),
										array (
												'label_styles',
												'=',
												'uk-label-custom'
										)
								)
						),
						'label_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Label Color' ),
								'depends' => array (
										array (
												'label_text',
												'!=',
												''
										),
										array (
												'label_styles',
												'=',
												'uk-label-custom'
										)
								)
						),
						// Repeatable Item
						'ui_feature_items' => array (
								'title' => Text::_ ( 'Items' ),
								'attr' => array (
										'title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Item' ),
												'std' => 'Item'
										),
										'title_link' => array (
												'type' => 'media',
												'format' => 'attachment',
												'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK' ),
												'placeholder' => 'http://www.example.com',
												'std' => '',
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
										'icon_type' => array (
												'type' => 'select',
												'title' => Text::_ ( 'Icon Type' ),
												'desc' => Text::_ ( 'Select icon type from the list' ),
												'values' => array (
														'' => Text::_ ( 'None' ),
														'fontawesome_icon' => Text::_ ( 'FontAwesome' ),
														'uikit_icon' => Text::_ ( 'Uikit' )
												),
												'std' => 'fontawesome_icon'
										),
										'icon_name' => array (
												'type' => 'icon',
												'title' => Text::_ ( 'Icon' ),
												'std' => 'check',
												'depends' => array (
														array (
																'icon_type',
																'=',
																'fontawesome_icon'
														)
												)
										),
										'uikit' => array ( // New Uikit Icon
												'type' => 'select',
												'title' => Text::_ ( 'Uikit Icon' ),
												'desc' => Text::_ ( 'Select an SVG icon from the list.' ),
												'values' => array (
														'' => Text::_ ( 'Select an optional icon.' ),
														'home' => 'Home',
														'sign-in' => 'Sign-in',
														'sign-out' => 'Sign-out',
														'user' => 'User',
														'users' => 'Users',
														'lock' => 'Lock',
														'unlock' => 'Unlock',
														'settings' => 'Settings',
														'cog' => 'Cog',
														'nut' => 'Nut',
														'comment' => 'Comment',
														'commenting' => 'Commenting',
														'comments' => 'Comments',
														'hashtag' => 'Hashtag',
														'tag' => 'Tag',
														'cart' => 'Cart',
														'credit-card' => 'Credit-card',
														'mail' => 'Mail',
														'receiver' => 'Receiver',
														'search' => 'Search',
														'location' => 'Location',
														'bookmark' => 'Bookmark',
														'code' => 'Code',
														'paint-bucket' => 'Paint-bucket',
														'camera' => 'Camera',
														'bell' => 'Bell',
														'bolt' => 'Bolt',
														'star' => 'Star',
														'heart' => 'Heart',
														'happy' => 'Happy',
														'lifesaver' => 'Lifesaver',
														'rss' => 'Rss',
														'social' => 'Social',
														'git-branch' => 'Git-branch',
														'git-fork' => 'Git-fork',
														'world' => 'World',
														'calendar' => 'Calendar',
														'clock' => 'Clock',
														'history' => 'History',
														'future' => 'Future',
														'pencil' => 'Pencil',
														'trash' => 'Trash',
														'move' => 'Move',
														'link' => 'Link',
														'question' => 'Question',
														'info' => 'Info',
														'warning' => 'Warning',
														'image' => 'Image',
														'thumbnails' => 'Thumbnails',
														'table' => 'Table',
														'list' => 'List',
														'menu' => 'Menu',
														'grid' => 'Grid',
														'more' => 'More',
														'more-vertical' => 'More-vertical',
														'plus' => 'Plus',
														'plus-circle' => 'Plus-circle',
														'minus' => 'Minus',
														'minus-circle' => 'Minus-circle',
														'close' => 'Close',
														'check' => 'Check',
														'ban' => 'Ban',
														'refresh' => 'Refresh',
														'play' => 'Play',
														'play-circle' => 'Play-circle',
														'tv' => 'Tv',
														'desktop' => 'Desktop',
														'laptop' => 'Laptop',
														'tablet' => 'Tablet',
														'phone' => 'Phone',
														'tablet-landscape' => 'Tablet-landscape',
														'phone-landscape' => 'Phone-landscape',
														'file' => 'File',
														'copy' => 'Copy',
														'file-edit' => 'File-edit',
														'folder' => 'Folder',
														'album' => 'Album',
														'push' => 'Push',
														'pull' => 'Pull',
														'server' => 'Server',
														'database' => 'Database',
														'cloud-upload' => 'Cloud-upload',
														'cloud-download' => 'Cloud-download',
														'download' => 'Download',
														'upload' => 'Upload',
														'reply' => 'Reply',
														'forward' => 'Forward',
														'expand' => 'Expand',
														'shrink' => 'Shrink',
														'arrow-up' => 'Arrow-up',
														'arrow-down' => 'Arrow-down',
														'arrow-left' => 'Arrow-left',
														'arrow-right' => 'Arrow-right',
														'chevron-up' => 'Chevron-up',
														'chevron-down' => 'Chevron-down',
														'chevron-left' => 'Chevron-left',
														'chevron-right' => 'Chevron-right',
														'triangle-up' => 'Triangle-up',
														'triangle-down' => 'Triangle-down',
														'triangle-left' => 'Triangle-left',
														'triangle-right' => 'Triangle-right',
														'bold' => 'Bold',
														'italic' => 'Italic',
														'strikethrough' => 'Strikethrough',
														'video-camera' => 'Video-camera',
														'quote-right' => 'Quote-right',
														'500px' => '500px',
														'behance' => 'Behance',
														'dribbble' => 'Dribbble',
														'facebook' => 'Facebook',
														'flickr' => 'Flickr',
														'foursquare' => 'Foursquare',
														'github' => 'Github',
														'github-alt' => 'Github-alt',
														'gitter' => 'Gitter',
														'google' => 'Google',
														'instagram' => 'Instagram',
														'joomla' => 'Joomla',
														'linkedin' => 'Linkedin',
														'pagekit' => 'Pagekit',
														'pinterest' => 'Pinterest',
														'soundcloud' => 'Soundcloud',
														'tripadvisor' => 'Tripadvisor',
														'tumblr' => 'Tumblr',
														'twitter' => 'Twitter',
														'uikit' => 'Uikit',
														'etsy' => 'Etsy',
														'vimeo' => 'Vimeo',
														'whatsapp' => 'Whatsapp',
														'wordpress' => 'Wordpress',
														'xing' => 'Xing',
														'yelp' => 'Yelp',
														'youtube' => 'Youtube',
														'print' => 'Print',
														'reddit' => 'Reddit',
														'file-text' => 'File Text',
														'file-pdf' => 'File Pdf',
														'chevron-double-left' => 'Chevron Double Left',
														'chevron-double-right' => 'Chevron Double Right'
												),
												'std' => 'check',
												'depends' => array (
														array (
																'icon_type',
																'=',
																'uikit_icon'
														)
												)
										),
										'icon_color' => array (
												'type' => 'color',
												'title' => Text::_ ( 'Icon color' ),
												'depends' => array (
														array (
																'icon_type',
																'!=',
																''
														)
												)
										)
								)
						),

						'separator_header_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Header' )
						),
						'header_card_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select on of the boxed card styles or a blank card.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'default' => Text::_ ( 'Default' ),
										'muted' => Text::_ ( 'Muted' ),
										'primary' => Text::_ ( 'Primary' ),
										'secondary' => Text::_ ( 'Secondary' ),
										'custom' => Text::_ ( 'Custom' )
								),
								'std' => ''
						),
						'header_background_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'depends' => array (
										array (
												'header_card_style',
												'=',
												'custom'
										)
								)
						),
						'header_padding_top' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Top' ),
								'std' => '25',
								'min' => 0,
								'max' => 100
						),
						'header_padding_bottom' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Bottom' ),
								'std' => '25',
								'min' => 0,
								'max' => 100
						),
						'header_padding_left' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Left' ),
								'std' => '25',
								'min' => 0,
								'max' => 100
						),
						'header_padding_right' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Right' ),
								'std' => '25',
								'min' => 0,
								'max' => 100
						),
						'divider_type' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Divider' ),
								'desc' => Text::_ ( 'Create dividers to separate content and apply different styles to them.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'icon' => Text::_ ( 'Icon' ),
										'small' => Text::_ ( 'Small' )
								),
								'std' => ''
						),
						'divider_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Divider Color' ),
								'depends' => array (
										array (
												'divider_type',
												'=',
												'small'
										)
								)
						),
						'divider_height' => array (
								'type' => 'slider',
								'min' => 1,
								'max' => 100,
								'std' => 1,
								'title' => Text::_ ( 'Divider Height' ),
								'depends' => array (
										array (
												'divider_type',
												'=',
												'small'
										)
								)
						),
						'divider_align' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Alignment' ),
								'desc' => Text::_ ( 'Align the divider above or below the price.' ),
								'values' => array (
										'' => Text::_ ( 'Below Price' ),
										'top' => Text::_ ( 'Above Price' )
								),
								'depends' => array (
										array (
												'divider_type',
												'!=',
												''
										)
								),
								'std' => ''
						),
						'use_header_background' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Use Header Background' ),
								'desc' => Text::_ ( 'Applies the background image for header with blend modes option. This feature will replace the background color.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'header_image' => array (
								'type' => 'media',
								'title' => Text::_ ( 'Image' ),
								'placeholder' => 'http://www.example.com/my-photo.jpg',
								'depends' => array (
										array (
												'use_header_background',
												'=',
												'1'
										)
								)
						),
						'image_blend_bg_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'desc' => Text::_ ( 'Use the background color in combination with blend modes, a transparent image or to fill the area, if the image doesn\'t cover the whole section.' ),
								'depends' => array (
										array (
												'use_header_background',
												'=',
												'1'
										)
								)
						),
						'image_blend_modes' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Blend modes' ),
								'desc' => Text::_ ( 'Apply different blend modes to your backgrounds, for example when placing them on images.' ),
								'values' => array (
										'uk-background-blend-multiply' => Text::_ ( 'Multiply' ),
										'uk-background-blend-screen' => Text::_ ( 'Screen' ),
										'uk-background-blend-overlay' => Text::_ ( 'Overlay' ),
										'uk-background-blend-darken' => Text::_ ( 'Darken' ),
										'uk-background-blend-lighten' => Text::_ ( 'Lighten' ),
										'uk-background-blend-color-dodge' => Text::_ ( 'Color Dodge' ),
										'uk-background-blend-color-burn' => Text::_ ( 'Color Burn' ),
										'uk-background-blend-hard-light' => Text::_ ( 'Hard Light' ),
										'uk-background-blend-soft-light' => Text::_ ( 'Soft Light' ),
										'uk-background-blend-difference' => Text::_ ( 'Difference' ),
										'uk-background-blend-exclusion' => Text::_ ( 'Exclusion' ),
										'uk-background-blend-hue' => Text::_ ( 'Hue' ),
										'uk-background-blend-saturation' => Text::_ ( 'Saturation' ),
										'uk-background-blend-color' => Text::_ ( 'Color' ),
										'uk-background-blend-luminosity' => Text::_ ( 'Luminosity' )
								),
								'std' => 'uk-background-blend-soft-light',
								'depends' => array (
										array (
												'use_header_background',
												'=',
												'1'
										)
								)
						),
						'media_overlay' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Overlay Color' ),
								'desc' => Text::_ ( 'Set an additional transparent overlay to soften the image.' ),
								'depends' => array (
										array (
												'use_header_background',
												'=',
												1
										)
								)
						),
						'bg_content_inverse' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Inverse' ),
								'desc' => Text::_ ( 'Choose Light or Dark mode option so that elements will be optimized for better visibility on dark or light images.' ),
								'values' => array (
										'light' => Text::_ ( 'Light' ),
										'' => Text::_ ( 'None' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'use_header_background',
												'=',
												'1'
										)
								)
						),
						'separator_price_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Price' )
						),
						'price_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.tm-price { font-family: {{ VALUE }}; }'
								)
						),
						'price_heading' => array (
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
								'std' => 'heading-medium',
								'depends' => array (
										array (
												'price',
												'!=',
												''
										)
								)
						),
						'price_fontsize' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Font Size' ),
								'std' => '70',
								'max' => 200,
								'depends' => array (
										array (
												'price_heading',
												'=',
												''
										)
								)
						),
						'price_padding_left' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Left' ),
								'std' => '25',
								'max' => 200
						),
						'price_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Select the predefined price color. If the background option is selected, you can use Light or Dark color text mode to inverse the text style.' ),
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
						'custom_price_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' ),
								'depends' => array (
										array (
												'price_color',
												'=',
												''
										)
								)
						),
						'price_margin_top' => array (
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
								'std' => 'small'
						),
						'separator_price_symbol_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Symbol' )
						),
						'currency_fontsize' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Currency Font Size' ),
								'std' => '25',
								'max' => 100
						),
						'currency_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Currency Color' ),
								'desc' => Text::_ ( 'Select the predefined currency text color. If the background option is selected, you can use Light or Dark color text mode to inverse the text style.' ),
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
						'custom_currency_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' ),
								'depends' => array (
										array (
												'currency_color',
												'=',
												''
										)
								)
						),
						'currency_margin' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Currency Margin Top' ),
								'std' => '15',
								'max' => 100
						),
						'separator_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Body' )
						),
						'body_background_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' )
						),
						'body_padding_top' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Top' ),
								'std' => '25',
								'min' => 0,
								'max' => 100
						),
						'body_padding_bottom' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Bottom' ),
								'std' => '25',
								'min' => 0,
								'max' => 100
						),
						'body_padding_left' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Left' ),
								'std' => '25',
								'min' => 0,
								'max' => 100
						),
						'body_padding_right' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Padding Right' ),
								'std' => '25',
								'min' => 0,
								'max' => 100
						),
						'box_shadow' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Box shadow' ),
								'desc' => Text::_ ( 'You can apply different box shadows to elements. Just add one of the following classes.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'Xlarge' )
								),
								'std' => ''
						),
						'hover' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Hover Effect' ),
								'desc' => Text::_ ( 'To apply a box shadow on hover, add one of the following classes.' ),
								'values' => array (
										'' => Text::_ ( 'No' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'Xlarge' )
								),
								'std' => ''
						),
						'separator_list_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'List Items' )
						),
						'title_text_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Use one of these classes to apply a different color to text elements.' ),
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
						'custom_title_text_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' ),
								'depends' => array (
										array (
												'title_text_color',
												'=',
												''
										)
								)
						),

						'list_marker' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Marker' ),
								'desc' => Text::_ ( 'Select the marker of the list items.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'disc' => Text::_ ( 'Disc' ),
										'circle' => Text::_ ( 'Circle' ),
										'square' => Text::_ ( 'Square' ),
										'decimal' => Text::_ ( 'Decimal' ),
										'hyphen' => Text::_ ( 'Hyphen' ),
										'bullet' => Text::_ ( 'Image Bullet' )
								),
								'std' => ''
						),

						'list_marker_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Marker Color' ),
								'desc' => Text::_ ( 'Select the color of the list markers.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'muted' => Text::_ ( 'Muted' ),
										'emphasis' => Text::_ ( 'Emphasis' ),
										'primary' => Text::_ ( 'Primary' ),
										'secondary' => Text::_ ( 'Secondary' )
								),
								'std' => ''
						),

						'list_styles' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select the list style.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'divider' => Text::_ ( 'Divider' ),
										'striped' => Text::_ ( 'Striped' )
								),
								'std' => ''
						),
						'scrollable' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Scrollable panel' ),
								'desc' => Text::_ ( 'Use this option if its content exceeds the height' ),
								'values' => array (
										'' => Text::_ ( 'JNO' ),
										'uk-panel-scrollable' => Text::_ ( 'JYES' )
								)
						),
						'list_title_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.uk-list .ui-item { font-family: {{ VALUE }}; }'
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
						'separator_description_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Description' )
						),
						'meta_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a predefined meta text style, including color, size and font-family' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-meta' => Text::_ ( 'Meta' ),
										'text-lead' => Text::_ ( 'Lead' ),
										'h1' => Text::_ ( 'H1' ),
										'h2' => Text::_ ( 'H2' ),
										'h3' => Text::_ ( 'H3' ),
										'h4' => Text::_ ( 'H4' ),
										'h5' => Text::_ ( 'H5' ),
										'h6' => Text::_ ( 'H6' )
								),
								'std' => ''
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
										'content' => Text::_ ( 'Below Pricing' )
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

						'separator_icon_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Icon' )
						),
						'all_icon_type' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Icon Type' ),
								'desc' => Text::_ ( 'Select icon type from the list' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'fontawesome_icon' => Text::_ ( 'FontAwesome' ),
										'uikit_icon' => Text::_ ( 'Uikit' )
								),
								'std' => 'fontawesome_icon'
						),
						'all_icon_name' => array (
								'type' => 'icon',
								'title' => Text::_ ( 'Icon' ),
								'std' => '',
								'depends' => array (
										array (
												'all_icon_type',
												'=',
												'fontawesome_icon'
										)
								)
						),
						'all_uikit' => array ( // New Uikit Icon
								'type' => 'select',
								'title' => Text::_ ( 'Uikit Icon' ),
								'desc' => Text::_ ( 'Select an SVG icon from the list.' ),
								'values' => array (
										'' => Text::_ ( 'Select an optional icon.' ),
										'home' => 'Home',
										'sign-in' => 'Sign-in',
										'sign-out' => 'Sign-out',
										'user' => 'User',
										'users' => 'Users',
										'lock' => 'Lock',
										'unlock' => 'Unlock',
										'settings' => 'Settings',
										'cog' => 'Cog',
										'nut' => 'Nut',
										'comment' => 'Comment',
										'commenting' => 'Commenting',
										'comments' => 'Comments',
										'hashtag' => 'Hashtag',
										'tag' => 'Tag',
										'cart' => 'Cart',
										'credit-card' => 'Credit-card',
										'mail' => 'Mail',
										'receiver' => 'Receiver',
										'search' => 'Search',
										'location' => 'Location',
										'bookmark' => 'Bookmark',
										'code' => 'Code',
										'paint-bucket' => 'Paint-bucket',
										'camera' => 'Camera',
										'bell' => 'Bell',
										'bolt' => 'Bolt',
										'star' => 'Star',
										'heart' => 'Heart',
										'happy' => 'Happy',
										'lifesaver' => 'Lifesaver',
										'rss' => 'Rss',
										'social' => 'Social',
										'git-branch' => 'Git-branch',
										'git-fork' => 'Git-fork',
										'world' => 'World',
										'calendar' => 'Calendar',
										'clock' => 'Clock',
										'history' => 'History',
										'future' => 'Future',
										'pencil' => 'Pencil',
										'trash' => 'Trash',
										'move' => 'Move',
										'link' => 'Link',
										'question' => 'Question',
										'info' => 'Info',
										'warning' => 'Warning',
										'image' => 'Image',
										'thumbnails' => 'Thumbnails',
										'table' => 'Table',
										'list' => 'List',
										'menu' => 'Menu',
										'grid' => 'Grid',
										'more' => 'More',
										'more-vertical' => 'More-vertical',
										'plus' => 'Plus',
										'plus-circle' => 'Plus-circle',
										'minus' => 'Minus',
										'minus-circle' => 'Minus-circle',
										'close' => 'Close',
										'check' => 'Check',
										'ban' => 'Ban',
										'refresh' => 'Refresh',
										'play' => 'Play',
										'play-circle' => 'Play-circle',
										'tv' => 'Tv',
										'desktop' => 'Desktop',
										'laptop' => 'Laptop',
										'tablet' => 'Tablet',
										'phone' => 'Phone',
										'tablet-landscape' => 'Tablet-landscape',
										'phone-landscape' => 'Phone-landscape',
										'file' => 'File',
										'copy' => 'Copy',
										'file-edit' => 'File-edit',
										'folder' => 'Folder',
										'album' => 'Album',
										'push' => 'Push',
										'pull' => 'Pull',
										'server' => 'Server',
										'database' => 'Database',
										'cloud-upload' => 'Cloud-upload',
										'cloud-download' => 'Cloud-download',
										'download' => 'Download',
										'upload' => 'Upload',
										'reply' => 'Reply',
										'forward' => 'Forward',
										'expand' => 'Expand',
										'shrink' => 'Shrink',
										'arrow-up' => 'Arrow-up',
										'arrow-down' => 'Arrow-down',
										'arrow-left' => 'Arrow-left',
										'arrow-right' => 'Arrow-right',
										'chevron-up' => 'Chevron-up',
										'chevron-down' => 'Chevron-down',
										'chevron-left' => 'Chevron-left',
										'chevron-right' => 'Chevron-right',
										'triangle-up' => 'Triangle-up',
										'triangle-down' => 'Triangle-down',
										'triangle-left' => 'Triangle-left',
										'triangle-right' => 'Triangle-right',
										'bold' => 'Bold',
										'italic' => 'Italic',
										'strikethrough' => 'Strikethrough',
										'video-camera' => 'Video-camera',
										'quote-right' => 'Quote-right',
										'500px' => '500px',
										'behance' => 'Behance',
										'dribbble' => 'Dribbble',
										'facebook' => 'Facebook',
										'flickr' => 'Flickr',
										'foursquare' => 'Foursquare',
										'github' => 'Github',
										'github-alt' => 'Github-alt',
										'gitter' => 'Gitter',
										'google' => 'Google',
										'google-plus' => 'Google-plus',
										'instagram' => 'Instagram',
										'joomla' => 'Joomla',
										'linkedin' => 'Linkedin',
										'pagekit' => 'Pagekit',
										'pinterest' => 'Pinterest',
										'soundcloud' => 'Soundcloud',
										'tripadvisor' => 'Tripadvisor',
										'tumblr' => 'Tumblr',
										'twitter' => 'Twitter',
										'uikit' => 'Uikit',
										'etsy' => 'Etsy',
										'vimeo' => 'Vimeo',
										'whatsapp' => 'Whatsapp',
										'wordpress' => 'Wordpress',
										'xing' => 'Xing',
										'yelp' => 'Yelp',
										'youtube' => 'Youtube',
										'print' => 'Print',
										'reddit' => 'Reddit',
										'file-text' => 'File Text',
										'file-pdf' => 'File Pdf',
										'chevron-double-left' => 'Chevron Double Left',
										'chevron-double-right' => 'Chevron Double Right'
								),
								'std' => 'check',
								'depends' => array (
										array (
												'all_icon_type',
												'=',
												'uikit_icon'
										)
								)
						),
						'all_icon_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Icon color' ),
								'depends' => array (
										array (
												'all_icon_type',
												'!=',
												''
										)
								)
						),
						'icon_size' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Icon Size' ),
								'placeholder' => 20,
								'std' => '20',
								'max' => 400
						),

						'separator_button_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Link' )
						),
						'button_title' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Text' ),
								'std' => 'Learn More'
						),
						'button_link' => array (
								'type' => 'media',
								'format' => 'attachment',
								'title' => Text::_ ( 'Link' ),
								'placeholder' => 'http://www.example.com',
								'hide_preview' => true
						),
						'button_link_new_tab' => array (
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
								'std' => '',
								'depends' => array (
										array (
												'button_title',
												'!=',
												''
										)
								)
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
								'std' => '#1e87f0',
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
								),
								'depends' => array (
										array (
												'button_title',
												'!=',
												''
										)
								)
						),
						'button_width' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Full width button' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'button_title',
												'!=',
												''
										)
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
