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
		'type' => 'general',
		'addon_name' => 'buttonstyled',
		'title' => Text::_ ( 'Button Styled' ),
		'desc' => Text::_ ( 'Easily create nice looking buttons, which come in different styles.' ),
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/buttonstyled/assets/images/icon.png',
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
										'' => Text::_ ( 'Default' ),
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
										'' => Text::_ ( 'Default' ),
										'text-muted' => Text::_ ( 'Muted' ),
										'text-emphasis' => Text::_ ( 'Emphasis' ),
										'text-primary' => Text::_ ( 'Primary' ),
										'text-secondary' => Text::_ ( 'Secondary' ),
										'text-success' => Text::_ ( 'Success' ),
										'text-warning' => Text::_ ( 'Warning' ),
										'text-danger' => Text::_ ( 'Danger' )
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
						'ui_list_buttons' => array (
								'title' => Text::_ ( 'Items' ),
								'attr' => array (
										'title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Content' ),
												'std' => 'Item'
										),
										'link' => array (
												'type' => 'media',
												'format' => 'attachment',
												'title' => Text::_ ( 'Link' ),
												'desc' => Text::_ ( 'Enter or pick a link, an image or a video file.' ),
												'placeholder' => 'http://',
												'hide_preview' => true
										),
										'link_title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Link Title' ),
												'desc' => Text::_ ( 'Enter an optional text for the title attribute of the link, which will appear on hover.' )
										),
										'target' => array (
												'type' => 'select',
												'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB' ),
												'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB_DESC' ),
												'values' => array (
														'' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_SAME_WINDOW' ),
														'_blank' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_NEW_WINDOW' )
												),
												'depends' => array (
														array (
																'link',
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
														'' => Text::_ ( 'FontAwesome' ),
														'uikit' => Text::_ ( 'Uikit' ),
														'custom' => Text::_ ( 'Custom' )
												),
												'std' => ''
										),
										'btn_icon' => array (
												'type' => 'icon',
												'title' => Text::_ ( 'Icon' ),
												'depends' => array (
														array (
																'icon_type',
																'!=',
																'uikit'
														),
														array (
																'icon_type',
																'!=',
																'custom'
														)
												)
										),
										'custom_icon' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Icon Class Name' ),
												'placeholder' => 'flaticon-check',
												'depends' => array (
														array (
																'icon_type',
																'=',
																'custom'
														)
												)
										),
										'uikit_icon' => array ( // New Uikit Icon
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
																'icon_type',
																'=',
																'uikit'
														)
												)
										),
										'icon_position' => array (
												'type' => 'select',
												'title' => Text::_ ( 'Icon Alignment' ),
												'desc' => Text::_ ( 'Choose the icon position.' ),
												'values' => array (
														'' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LEFT' ),
														'right' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_RIGHT' )
												),
												'std' => '',
												'depends' => array (
														array (
																'btn_icon',
																'!=',
																''
														)
												)
										),
										'button_style' => array (
												'type' => 'select',
												'title' => Text::_ ( 'Style' ),
												'desc' => Text::_ ( 'Set the button style.' ),
												'values' => array (
														'' => Text::_ ( 'Default' ),
														'primary' => Text::_ ( 'Primary' ),
														'secondary' => Text::_ ( 'Secondary' ),
														'danger' => Text::_ ( 'Danger' ),
														'text' => Text::_ ( 'Text' ),
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
										)
								)
						),
						'separator_misc_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Misc Settings' )
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
						'button_size' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Button Size' ),
								'desc' => Text::_ ( 'Set the size for multiple buttons.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'large' => Text::_ ( 'Large' )
								)
						),
						'grid_width' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Full width button' ),
								'std' => 0
						),
						'grid_column_gap' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Column Gap' ),
								'desc' => Text::_ ( 'Set the size of the column gap between multiple buttons.' ),
								'values' => array (
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'' => Text::_ ( 'Default' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => 'small'
						),
						'grid_row_gap' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Row Gap' ),
								'desc' => Text::_ ( 'Set the size of the row gap between multiple buttons.' ),
								'values' => array (
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'' => Text::_ ( 'Default' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => 'small'
						),
						'separator_button_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'General' )
						),
						'button_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Text Alignment' ),
								'desc' => Text::_ ( 'Center, left and right alignment may depend on a breakpoint and require a fallback.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'left' => Text::_ ( 'Left' ),
										'center' => Text::_ ( 'Center' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => ''
						),
						'button_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Text Alignment Breakpoint' ),
								'desc' => Text::_ ( 'Display the button alignment only on this device width and larger' ),
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
												'button_alignment',
												'!=',
												''
										)
								)
						),
						'button_fallback' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Text Alignment Fallback' ),
								'desc' => Text::_ ( 'Define an alignment fallback for device widths below the breakpoint.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'left' => Text::_ ( 'Left' ),
										'center' => Text::_ ( 'Center' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => '',
								'depends' => array (
										array (
												'button_alignment',
												'!=',
												''
										),
										array (
												'button_breakpoint',
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
								'title' => Text::_ ( 'Customizing Parallax' ),
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
