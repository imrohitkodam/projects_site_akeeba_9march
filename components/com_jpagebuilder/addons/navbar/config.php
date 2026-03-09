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
		'type' => 'general',
		'addon_name' => 'navbar',
		'title' => Text::_ ( 'Navbar' ),
		'desc' => Text::_ ( 'Create a navigation bar that can be used for your main site navigation.' ),
		'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="4" width="16" height="1"></rect><rect x="2" y="9" width="16" height="1"></rect><rect x="2" y="14" width="16" height="1"></rect></svg>',
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

				'separator_logo_options' => [ 
						'title' => Text::_ ( 'Logo' ),
						'fields' => [ 
								'image' => [ 
										'type' => 'media',
										'hide_alt_text' => true,
										'title' => Text::_ ( 'Logo Image' )
								],

								'logo_position' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Position' ),
										'values' => [ 
												'left' => Text::_ ( 'Left' ),
												'right' => Text::_ ( 'Right' )
										],
										'std' => 'left',
										'inline' => true,
										'depends' => [ 
												[ 
														'image',
														'!=',
														''
												]
										]
								],

								'alt_text' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_ALT_TEXT' ),
										'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_ALT_TEXT_DESC' ),
										'std' => 'Image',
										'depends' => [ 
												[ 
														'image',
														'!=',
														''
												]
										]
								],
								'logo_alignment_separator' => [ 
										'type' => 'separator'
								],
								'logo_margin' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Margin' ),
										'desc' => Text::_ ( 'Set the vertical margin for Logo.' ),
										'values' => [ 
												'' => 'Keep existing',
												'uk-margin-small' => 'Small',
												'uk-margin' => 'Default',
												'uk-margin-medium' => 'Medium',
												'uk-margin-large' => 'Large',
												'uk-margin-xlarge' => 'X-Large',
												'uk-margin-remove-vertical' => 'None'
										],
										'std' => 'uk-margin-small',
										'inline' => true,
										'depends' => [ 
												[ 
														'image',
														'!=',
														''
												]
										]
								],

								'logo_link' => [ 
										'type' => 'link',
										'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK' ),
										'depends' => [ 
												[ 
														'image',
														'!=',
														''
												]
										]
								]
						]
				],
				'link_list_item' => [ 
						'title' => Text::_ ( 'Navbar Items' ),
						'fields' => [ 
								'sp_link_list_item' => [ 
										'type' => 'repeatable',
										'title' => Text::_ ( 'Items' ),
										'attr' => [ 

												'title' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Menu item title' ),
														'std' => 'Item'
												],

												'url' => [ 
														'type' => 'link',
														'title' => Text::_ ( 'Item URL' )
												],

												'icon' => [ 
														'type' => 'select',
														'title' => Text::_ ( 'Uikit Icons' ),
														'desc' => Text::_ ( 'Place scalable vector icons anywhere in your content.' ),
														'values' => [ 
																'' => Text::_ ( '...Select icon...' ),
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
																'tiktok' => 'Tiktok',
																'twitch' => 'Twitch',
																'discord' => 'Discord',
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
														]
												],

												'active' => [ 
														'type' => 'checkbox',
														'title' => Text::_ ( 'Enable it to indicate an active menu item.' ),
														'std' => 0
												],

												'dropdown' => [ 
														'type' => 'textarea',
														'title' => Text::_ ( 'Dropdown content' ),
														'desc' => Text::_ ( 'Custom dropdown content with special columns. Wrap your content inside a div to define the columns' ),
														'std' => '',
														'depends' => [ 
																[ 
																		'title',
																		'!=',
																		''
																]
														]
												],

												'dropdown_columns' => [ 
														'type' => 'select',
														'title' => Text::_ ( 'Dropdown columns' ),
														'values' => [ 
																'1' => Text::_ ( '1 Columns' ),
																'2' => Text::_ ( '2 Columns' ),
																'3' => Text::_ ( '3 Columns' ),
																'4' => Text::_ ( '4 Columns' ),
																'5' => Text::_ ( '5 Columns' )
														],
														'std' => '2',
														'inline' => true,
														'depends' => [ 
																[ 
																		'title',
																		'!=',
																		''
																]
														]
												],

												'item_class' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_CLASS' ),
														'std' => '',
														'depends' => [ 
																[ 
																		'title',
																		'!=',
																		''
																]
														]
												]
										]
								]
						]
				],

				'separator_common_options' => [ 
						'title' => Text::_ ( 'Common' ),
						'fields' => [ 
								'type' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Navbar Style' ),
										'desc' => Text::_ ( 'Select navigation style for menu, you can use navbar type or nav type.' ),
										'values' => [ 
												'uk-navbar-nav' => Text::_ ( 'Navbar' ),
												'uk-nav uk-nav-default' => Text::_ ( 'Nav' )
										],
										'inline' => true,
										'std' => 'uk-navbar-nav'
								],

								'menu_breakpoint' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Breakpoint' ),
										'desc' => Text::_ ( 'Select the device size that will replace the default header with the mobile header.' ),
										'values' => [ 
												's' => Text::_ ( 'Small' ),
												'm' => Text::_ ( 'Medium' ),
												'l' => Text::_ ( 'Large' )
										],
										'std' => 'm',
										'inline' => true,
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],

								'icon_position' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Icon position' ),
										'values' => [ 
												'left' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LEFT' ),
												'right' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_RIGHT' ),
												'top' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_TOP' )
										],
										'std' => 'left',
										'inline' => true
								],
								'toggle_separator_above' => [ 
										'type' => 'separator'
								],
								'responsive_menu' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Toggle Menu' ),
										'desc' => Text::_ ( 'Check this option to create an icon as a toggle for offcanvas menu' ),
										'std' => 1,
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],

								'toggle_mode' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Toggle Mode' ),
										'values' => [ 
												'slide' => Text::_ ( 'Default' ),
												'push' => Text::_ ( 'Push' ),
												'reveal' => Text::_ ( 'Reveal' ),
												'none' => Text::_ ( 'None' )
										],
										'std' => 'slide',
										'inline' => true,
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],

								'responsive_menu_class' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Toggle Menu Class' ),
										'desc' => Text::_ ( 'You can use custom toggle menu class, i.e uk-hidden@l to hidden the toogle on desktop and visible on mobile/tablet devices' ),
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												],
												[ 
														'responsive_menu',
														'=',
														1
												]
										]
								],

								'right_menu' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Right Toggle Menu' ),
										'desc' => Text::_ ( 'Assign the toggle menu icon to the right position' ),
										'std' => 0,
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												],
												[ 
														'responsive_menu',
														'=',
														1
												]
										]
								],
								'toggle_separator_below' => [ 
										'type' => 'separator'
								],
								'flip' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Flip Offcanvas' ),
										'desc' => Text::_ ( 'Choose this option to adjust its alignment, so that it slides in from the right.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												],
												[ 
														'responsive_menu',
														'=',
														1
												]
										]
								],

								'sticky_menu' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Enable Navbar Sticky' ),
										'desc' => Text::_ ( 'Make elements remain at the top of the viewport, like a sticky navigation.' ),
										'std' => 0
								],

								'transparent_menu' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Transparent Navbar' ),
										'std' => 0,
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],

								'scroll_to' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Scroll smoothly' ),
										'desc' => Text::_ ( 'Scroll smoothly when jumping to different sections on a page.' ),
										'std' => 0
								],

								'scroll_offset' => [ 
										'type' => 'number',
										'title' => Text::_ ( 'Pixel offset added to scroll top.' ),
										'std' => 90,
										'depends' => [ 
												[ 
														'scroll_to',
														'=',
														1
												]
										]
								],

								'card_style' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Style' ),
										'desc' => Text::_ ( 'UIkit includes a number of modifiers that can be used to add a specific style to cards.' ),
										'values' => [ 
												'uk-card-default' => Text::_ ( 'Card Default' ),
												'uk-card-primary' => Text::_ ( 'Card Primary' ),
												'uk-card-secondary' => Text::_ ( 'Card Secondary' )
										],
										'std' => 'uk-card-default',
										'inline' => true,
										'depends' => [ 
												[ 
														'type',
														'!=',
														'uk-navbar-nav'
												]
										]
								],

								'card_size' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Card Size' ),
										'desc' => Text::_ ( 'You can apply different size modifiers to cards that will decrease or increase their padding.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'uk-card-small' => Text::_ ( 'Small' ),
												'uk-card-large' => Text::_ ( 'Large' )
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'type',
														'!=',
														'uk-navbar-nav'
												]
										]
								],

								'hover' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Hover' ),
										'desc' => Text::_ ( 'This comes in handy when working with anchors and can be combined with the other card modifiers.' ),
										'values' => [ 
												'' => Text::_ ( 'No' ),
												'uk-card-hover' => Text::_ ( 'Yes' )
										],
										'inline' => true,
										'depends' => [ 
												[ 
														'type',
														'!=',
														'uk-navbar-nav'
												]
										]
								],

								'label_text' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Label' ),
										'desc' => Text::_ ( 'Easily create nice looking notification badges.' ),
										'std' => '',
										'depends' => [ 
												[ 
														'type',
														'!=',
														'uk-navbar-nav'
												]
										]
								],

								'label_styles' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Label Style' ),
										'desc' => Text::_ ( 'Indicate important notes and highlight parts of your content.' ),
										'values' => [ 
												'' => Text::_ ( 'Default' ),
												'uk-label-success' => Text::_ ( 'Success' ),
												'uk-label-warning' => Text::_ ( 'Warning' ),
												'uk-label-danger' => Text::_ ( 'Danger' )
										],
										'inline' => true,
										'depends' => [ 
												[ 
														'type',
														'!=',
														'uk-navbar-nav'
												]
										]
								],

								'align' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Menu Alignment' ),
										'desc' => Text::_ ( 'Add one of these option to align the navigation.' ),
										'values' => [ 
												'left' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LEFT' ),
												'right' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_RIGHT' ),
												'center' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_CENTER' )
										],
										'std' => 'left',
										'inline' => true
								]
						]
				],

				'item_dropdown_style' => [ 
						'title' => Text::_ ( 'Dropdown Options' ),
						'fields' => [ 
								'enable_dropbar' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Enable Dropbar' ),
										'desc' => Text::_ ( 'A dropbar extends to the full width of the navbar and displays the dropdown without its default styling.' ),
										'std' => 0
								],

								'enable_boundary' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Boundary' ),
										'desc' => Text::_ ( 'Dropdowns can be aligned to the navbar\'s boundary. By default, dropdowns are aligned to the left.' ),
										'std' => 0
								],

								'boundary_alignment' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Boundary alignment' ),
										'values' => [ 
												'left' => Text::_ ( 'Left' ),
												'right' => Text::_ ( 'Right' ),
												'center' => Text::_ ( 'Center' )
										],
										'std' => 'left',
										'inline' => true,
										'depends' => [ 
												[ 
														'enable_boundary',
														'=',
														1
												]
										]
								]
						]
				],

				'navbar_style' => [ 
						'title' => Text::_ ( 'Navbar' ),
						'fields' => [ 
								'nav_style' => [ 
										'type' => 'separator',
										'title' => Text::_ ( 'Nav Style' ),
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-nav uk-nav-default'
												]
										]
								],

								'text_transform' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Title transform' ),
										'desc' => Text::_ ( 'The following options will transform title into uppercased, capitalized or lowercased characters.' ),
										'values' => [ 
												'' => 'Inherit',
												'uppercase' => 'Uppercase',
												'capitalize' => 'Capitalize',
												'lowercase' => 'Lowercase'
										],
										'std' => '',
										'inline' => true,
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],

								'nav_bg' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Nav Background' ),
										'std' => '#f8f8f8',
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-nav uk-nav-default'
												]
										]
								],

								'nav_fontsize' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Nav Font Size' ),
										'std' => '',
										'max' => 200,
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-nav uk-nav-default'
												]
										]
								],

								'nav_item_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Nav Color' ),
										'std' => '#999999',
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-nav uk-nav-default'
												]
										]
								],

								'nav_item_color_active' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Nav Hover/Active Color' ),
										'std' => '#666666',
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-nav uk-nav-default'
												]
										]
								],

								'navbar_bg' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Navbar Background' ),
										'std' => '#f8f8f8',
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],

								'navbar_item_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Navbar Color' ),
										'std' => '#999999',
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],
								'navbar_separator_above' => [ 
										'type' => 'separator'
								],
								'navbar_item_color_active' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Hover/Active Color' ),
										'std' => '#666666',
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],

								'toggle_color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Toggle Color Item' ),
										'std' => '#999999',
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
												]
										]
								],

								'toggle_color_active' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'Toggle Active Color' ),
										'std' => '#666666',
										'depends' => [ 
												[ 
														'type',
														'=',
														'uk-navbar-nav'
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
