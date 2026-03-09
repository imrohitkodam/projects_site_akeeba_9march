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
		'addon_name' => 'infobox',
		'title' => Text::_ ( 'Info Box' ),
		'desc' => Text::_ ( 'Use the Infobox Add-on that lets you add a heading prefix, heading, description, icon and more. ' ),
		'icon' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path opacity=".5" fill-rule="evenodd" clip-rule="evenodd" d="M29 26a1 1 0 01-1 1H4a1 1 0 110-2h24a1 1 0 011 1zM24 31a1 1 0 01-1 1H8a1 1 0 110-2h15a1 1 0 011 1z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.458 0c.423 0 .81.24.996.62l2.63 5.327 5.882.86a1.111 1.111 0 01.614 1.895l-4.255 4.144 1.005 5.855a1.111 1.111 0 01-1.613 1.171l-5.259-2.765-5.26 2.765a1.111 1.111 0 01-1.611-1.17l1.004-5.856-4.255-4.144a1.111 1.111 0 01.614-1.895l5.882-.86L15.462.62c.187-.379.573-.619.996-.619zm0 3.621l-1.892 3.833a1.111 1.111 0 01-.836.608l-4.232.618 3.062 2.982c.262.255.382.623.32.984l-.723 4.211 3.784-1.99c.324-.17.71-.17 1.034 0l3.784 1.99-.723-4.21a1.11 1.11 0 01.32-.985l3.062-2.982-4.232-.618a1.111 1.111 0 01-.836-.608l-1.892-3.833z" fill="currentColor"/></svg>',
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

				'grid_item' => [ 
						'title' => Text::_ ( 'Content' ),
						'fields' => [ 
								'ui_grid_item' => [ 
										'type' => 'repeatable',
										'title' => Text::_ ( 'Items' ),
										'attr' => [ 
												'title' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Title' ),
														'std' => 'Item'
												],

												'label_text' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Label' ),
														'desc' => Text::_ ( 'To position a badge inside a grid item.' )
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
														'depends' => [ 
																[ 
																		'label_text',
																		'!=',
																		''
																]
														]
												],

												'meta' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Meta' )
												],

												'card_content' => [ 
														'type' => 'editor',
														'title' => Text::_ ( 'Content' ),
														'std' => 'Lorem Ipsum is simply text the printing and typesetting standard industry. So you like a demo website and you want to add.'
												],

												'media_type' => [ 
														'type' => 'select',
														'title' => Text::_ ( 'Media Type' ),
														'desc' => Text::_ ( 'Select icon or image info box type from the list' ),
														'values' => [ 
																'' => Text::_ ( 'Image' ),
																'fontawesome_icon' => Text::_ ( 'FontAwesome' ),
																'uikit_icon' => Text::_ ( 'Uikit' ),
																'custom' => Text::_ ( 'Custom Icon' )
														],
														'std' => ''
												],

												'image' => [ 
														'type' => 'media',
														'hide_alt_text' => true,
														'title' => Text::_ ( 'Select Image' ),
														'depends' => [ 
																[ 
																		'media_type',
																		'!=',
																		'fontawesome_icon'
																],
																[ 
																		'media_type',
																		'!=',
																		'icon'
																],
																[ 
																		'media_type',
																		'!=',
																		'uikit_icon'
																],
																[ 
																		'media_type',
																		'!=',
																		'custom'
																]
														]
												],

												'alt_text' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Image Alt' ),
														'std' => 'Image Alt',
														'depends' => [ 
																[ 
																		'media_type',
																		'!=',
																		'fontawesome_icon'
																],
																[ 
																		'media_type',
																		'!=',
																		'icon'
																],
																[ 
																		'media_type',
																		'!=',
																		'uikit_icon'
																],
																[ 
																		'media_type',
																		'!=',
																		'custom'
																]
														]
												],

												'custom_icon' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Icon Class Name' ),
														'placeholder' => 'flaticon-check',
														'depends' => [ 
																[ 
																		'media_type',
																		'=',
																		'custom'
																]
														]
												],

												'faw_icon' => [ 
														'type' => 'icon',
														'title' => Text::_ ( 'FontAwesome Icon' ),
														'std' => '',
														'depends' => [ 
																[ 
																		'media_type',
																		'=',
																		'fontawesome_icon'
																]
														]
												],

												'uikit' => [ 
														'type' => 'select',
														'title' => Text::_ ( 'Uikit Icon' ),
														'desc' => Text::_ ( 'Select an SVG icon from the list.' ),
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
														],
														'std' => 'check',
														'depends' => [ 
																[ 
																		'media_type',
																		'=',
																		'uikit_icon'
																]
														]
												],

												'title_link' => [ 
														'type' => 'link',
														'title' => Text::_ ( 'Link' )
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
				'separator_grid_style_tab' => [ 
						'title' => Text::_ ( 'Display' ),
						'fields' => [ 
								'grid_style_tab' => [ 
										'type' => 'buttons',
										'std' => 'normal',
										'values' => [ 
												[ 
														'label' => 'Layout',
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

								'masonry' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Enable Masonry' ),
										'desc' => Text::_ ( 'Create a gap-free masonry layout if grid items have different heights. Pack items into columns with the most room or show them in their natural order. Optionally, use a parallax animation to move columns while scrolling until they justify at the bottom.' ),
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
								'masonry_layout' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Masonry Layout' ),
										'values' => [ 
												'pack' => Text::_ ( 'Pack' ),
												'next' => Text::_ ( 'Next' )
										],
										'std' => 'pack',
										'inline' => true,
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												],
												[ 
														'masonry',
														'!=',
														0
												]
										]
								],
								'grid_parallax' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Parallax' ),
										'desc' => Text::_ ( 'The parallax animation moves single grid columns at different speeds while scrolling. Define the vertical parallax offset in pixels. Alternatively, move columns with different heights until they justify at the bottom.' ),
										'min' => 0,
										'max' => 600,
										'std' => '',
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												]
										]
								],
								'justify_columns' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Justify columns at the bottom' ),
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
								'grid_parallax_start' => [ 
										'type' => 'text',
										'inline' => true,
										'title' => Text::_ ( 'Parallax Start' ),
										'desc' => Text::_ ( 'The animation starts when the element enters the viewport and ends when it leaves the viewport. Optionally, set a start and end offset, e.g. 100px, 50vh or 50vh + 50%. Percent relates to the element\'s height.' ),
										'depends' => [ 
												[ 
														'grid_style_tab',
														'=',
														'grid'
												]
										]
								],
								'grid_parallax_end' => [ 
										'type' => 'text',
										'inline' => true,
										'title' => Text::_ ( 'Parallax End' ),
										'desc' => Text::_ ( 'The animation starts when the element enters the viewport and ends when it leaves the viewport. Optionally, set a start and end offset, e.g. 100px, 50vh or 50vh + 50%. Percent relates to the element\'s height.' ),
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

								'grid_divider' => [ 
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
														'grid_column_gap',
														'!=',
														'collapse'
												],
												[ 
														'grid_row_gap',
														'!=',
														'collapse'
												],
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
												'hover' => Text::_ ( 'Card Hover' )
										],
										'std' => '',
										'inline' => true
								],

								'panel_link' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Link panel' ),
										'desc' => Text::_ ( 'Link the whole card if a link exists.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0
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

								'image_padding' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Align image without padding' ),
										'desc' => Text::_ ( 'This option won\'t have any effect unless card styles are enabled. The image boder, box shadow and hover box shadow are disabled if you use this option.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0,
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
														'card_alignment',
														'!=',
														'between'
												],
												[ 
														'card_style',
														'=',
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
								]
						]
				],

				'separator_img_options' => [ 
						'title' => Text::_ ( 'Image' ),
						'fields' => [ 
								'img_width' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Width' ),
										'desc' => Text::_ ( 'Setting just one value preserves the original proportions. The image will be resized and cropped automatically.' ),
										'min' => 10,
										'max' => 1200
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
														'image_padding',
														'!=',
														1
												]
										]
								],

								'image_box_shadow' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Box Shadow' ),
										'desc' => Text::_ ( 'Select the image\'s box shadow size.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' )
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
								],

								'image_link' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Link image' ),
										'desc' => Text::_ ( 'Link the image if a link exists.' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0
								],

								'image_transition' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Hover Transition' ),
										'desc' => Text::_ ( 'Set the hover transition for a linked image.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'scale-up' => Text::_ ( 'Scale Up' ),
												'scale-down' => Text::_ ( 'Scale Down' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'image_link',
														'=',
														1
												]
										]
								],

								'image_hover_box_shadow' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Hover Box Shadow' ),
										'desc' => Text::_ ( 'Select the image\'s box shadow size on hover.' ),
										'values' => [ 
												'' => Text::_ ( 'None' ),
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'large' => Text::_ ( 'Large' ),
												'xlarge' => Text::_ ( 'X-Large' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'card_style',
														'=',
														''
												],
												[ 
														'image_link',
														'=',
														1
												]
										]
								],

								'card_alignment' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Alignment' ),
										'desc' => Text::_ ( 'Align the image to the top, left, right or place it between the title and the content' ),
										'values' => [ 
												'top' => Text::_ ( 'Top' ),
												'bottom' => Text::_ ( 'Bottom' ),
												'left' => Text::_ ( 'Left' ),
												'right' => Text::_ ( 'Right' ),
												'between' => Text::_ ( 'Between' )
										],
										'std' => 'top',
										'inline' => true
								],

								'grid_width' => [ 
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
										'std' => '1-2',
										'depends' => [ 
												[ 
														'card_alignment',
														'!=',
														'top'
												],
												[ 
														'card_alignment',
														'!=',
														'bottom'
												],
												[ 
														'card_alignment',
														'!=',
														'between'
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
										'std' => '',
										'depends' => [ 
												[ 
														'card_alignment',
														'!=',
														'top'
												],
												[ 
														'card_alignment',
														'!=',
														'bottom'
												],
												[ 
														'card_alignment',
														'!=',
														'between'
												]
										]
								],

								'image_grid_row_gap' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Grid Row Gap' ),
										'desc' => Text::_ ( 'Set the size of the gap if the grid items stack.' ),
										'values' => [ 
												'small' => Text::_ ( 'Small' ),
												'medium' => Text::_ ( 'Medium' ),
												'' => Text::_ ( 'Default' ),
												'large' => Text::_ ( 'Large' ),
												'collapse' => Text::_ ( 'None' )
										],
										'std' => '',
										'depends' => [ 
												[ 
														'card_alignment',
														'!=',
														'top'
												],
												[ 
														'card_alignment',
														'!=',
														'bottom'
												],
												[ 
														'card_alignment',
														'!=',
														'between'
												]
										]
								],

								'grid_breakpoint' => [ 
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
														'card_alignment',
														'!=',
														'top'
												],
												[ 
														'card_alignment',
														'!=',
														'bottom'
												],
												[ 
														'card_alignment',
														'!=',
														'between'
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
										'std' => 0,
										'depends' => [ 
												[ 
														'card_alignment',
														'!=',
														'top'
												],
												[ 
														'card_alignment',
														'!=',
														'bottom'
												],
												[ 
														'card_alignment',
														'!=',
														'between'
												]
										]
								],

								'image_margin_top' => [ 
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
										'depends' => [ 
												[ 
														'card_alignment',
														'!=',
														'top'
												],
												[ 
														'card_alignment',
														'!=',
														'left'
												],
												[ 
														'card_alignment',
														'!=',
														'right'
												]
										]
								],

								'image_svg_inline' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Inline SVG' ),
										'desc' => Text::_ ( 'Inject SVG images into the page markup, so that they can easily be styled with CSS.' ),
										'std' => 0
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
												]
										]
								]
						]
				],

				'separator_icon_options' => [ 
						'title' => Text::_ ( 'Icon' ),
						'fields' => [ 
								'faw_icon_size' => [ 
										'type' => 'slider',
										'title' => Text::_ ( 'Icon Size' ),
										'placeholder' => 36,
										'std' => '36',
										'max' => 400
								],

								'color' => [ 
										'type' => 'color',
										'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_COLOR' )
								]
						]
				],
				'style_tab_options' => [ 
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
										'inline' => true,
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
														'style_tab',
														'=',
														'title'
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

								'decoration_color' => [ 
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

								'decoration_width' => [ 
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
										'desc' => Text::_ ( 'Select the predefined title text color.' ),
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
								'title_color_separator' => [ 
										'type' => 'separator',
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
														'style_tab',
														'=',
														'title'
												]
										]
								],

								'title_align' => [ 
										'type' => 'radio',
										'title' => Text::_ ( 'Alignment' ),
										'desc' => Text::_ ( 'Align the title to the top or left in regards to the content.' ),
										'values' => [ 
												'' => Text::_ ( 'Top' ),
												'left' => Text::_ ( 'Left' )
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
								'title_alignment_separator_above' => [ 
										'type' => 'separator',
										'depends' => [ 
												[ 
														'title_align',
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
								'title_grid_width' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Grid Width' ),
										'desc' => Text::_ ( 'Define the width of the title within the grid. Choose between percent and fixed widths or expand columns to the width of their content.' ),
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
										'std' => '1-2',
										'inline' => true,
										'depends' => [ 
												[ 
														'title_align',
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

								'title_grid_column_gap' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Grid Column Gap' ),
										'desc' => Text::_ ( 'Set the size of the gap between the title and the content.' ),
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
														'title_align',
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

								'title_grid_row_gap' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Grid Row Gap' ),
										'desc' => Text::_ ( 'Set the size of the gap if the grid items stack.' ),
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
														'title_align',
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

								'title_breakpoint' => [ 
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
										'inline' => true,
										'depends' => [ 
												[ 
														'title_align',
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
								'title_alignment_separator_below' => [ 
										'type' => 'separator',
										'depends' => [ 
												[ 
														'title_align',
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

								'heading_selector' => [ 
										'type' => 'headings',
										'title' => Text::_ ( 'HTML Element' ),
										'desc' => Text::_ ( 'Choose one of the eight heading elements to fit your semantic structure.' ),
										'std' => 'h3',
										'depends' => [ 
												[ 
														'title_align',
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
												'text-meta' => Text::_ ( 'Text Meta' ),
												'heading-small' => Text::_ ( 'Text Small' ),
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
														'meta'
												]
										]
								],

								'meta_color' => [ 
										'type' => 'select',
										'title' => Text::_ ( 'Color' ),
										'desc' => Text::_ ( 'Select the predefined meta text color.' ),
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
												'top' => Text::_ ( 'Above Title' ),
												'' => Text::_ ( 'Below Title' ),
												'above' => Text::_ ( 'Above Content' ),
												'content' => Text::_ ( 'Below Content' )
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

								'meta_element' => [ 
										'type' => 'headings',
										'title' => Text::_ ( 'HTML Element' ),
										'desc' => Text::_ ( 'Choose one of the eight heading elements to fit your semantic structure.' ),
										'std' => 'div',
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
														'style_tab',
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
														'style_tab',
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
												],
												[ 
														'style_tab',
														'=',
														'content'
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
												],
												[ 
														'style_tab',
														'=',
														'content'
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

								'button_font_family' => [ 
										'type' => 'fonts',
										'title' => Text::_ ( 'Font Family' ),
										'selector' => array (
												'type' => 'font',
												'font' => '{{ VALUE }}',
												'css' => '.uk-button { font-family: {{ VALUE }}; }'
										),
										'depends' => [ 
												[ 
														'style_tab',
														'=',
														'link'
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
										'std' => 'primary',
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
														'button_style',
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
												array (
														'label' => 'Normal',
														'value' => 'normal'
												),
												array (
														'label' => 'Hover',
														'value' => 'hover'
												)
										],
										'tabs' => true,
										'depends' => [ 
												[ 
														'button_style',
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
														'button_style',
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
										'std' => '',
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
