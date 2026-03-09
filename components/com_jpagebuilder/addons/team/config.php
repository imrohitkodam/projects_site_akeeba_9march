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
		'addon_name' => 'team',
		'title' => Text::_ ( 'Team' ),
		'desc' => Text::_ ( 'Create a responsive team slider.' ),
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/team/assets/images/icon.png',
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
						'ui_team_item' => array (
								'title' => Text::_ ( 'Items' ),
								'attr' => array (
										'avatar' => array (
												'type' => 'media',
												'title' => Text::_ ( 'Image' ),
												'placeholder' => 'http://www.example.com/my-photo.jpg'
										),
										'image_panel' => array (
												'type' => 'checkbox',
												'title' => Text::_ ( 'Image Settings' ),
												'values' => array (
														1 => Text::_ ( 'JYES' ),
														0 => Text::_ ( 'JNO' )
												),
												'std' => 0
										),
										'media_background' => array (
												'type' => 'color',
												'title' => Text::_ ( 'Background Color' ),
												'desc' => Text::_ ( 'Use the background color in combination with blend modes.' ),
												'depends' => array (
														array (
																'image_panel',
																'=',
																1
														)
												)
										),
										'media_blend_mode' => array (
												'type' => 'select',
												'title' => Text::_ ( 'Blend modes' ),
												'desc' => Text::_ ( 'Determine how the image will blend with the background color.' ),
												'values' => array (
														'' => Text::_ ( 'None' ),
														'multiply' => Text::_ ( 'Multiply' ),
														'screen' => Text::_ ( 'Screen' ),
														'overlay' => Text::_ ( 'Overlay' ),
														'darken' => Text::_ ( 'Darken' ),
														'lighten' => Text::_ ( 'Lighten' ),
														'color-dodge' => Text::_ ( 'Color Dodge' ),
														'color-burn' => Text::_ ( 'Color Burn' ),
														'hard-light' => Text::_ ( 'Hard Light' ),
														'soft-light' => Text::_ ( 'Soft Light' ),
														'difference' => Text::_ ( 'Difference' ),
														'exclusion' => Text::_ ( 'Exclusion' ),
														'hue' => Text::_ ( 'Hue' ),
														'color' => Text::_ ( 'Color' ),
														'luminosity' => Text::_ ( 'Luminosity' )
												),
												'std' => '',
												'depends' => array (
														array (
																'image_panel',
																'=',
																1
														),
														array (
																'media_background',
																'!=',
																''
														)
												)
										),
										'media_overlay' => array (
												'type' => 'color',
												'title' => Text::_ ( 'Overlay Color' ),
												'desc' => Text::_ ( 'Set an additional transparent overlay to soften the image.' ),
												'depends' => array (
														array (
																'image_panel',
																'=',
																1
														)
												)
										),
										'title' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Name' ),
												'desc' => Text::_ ( 'Input the name of the person' ),
												'std' => 'Item'
										),
										'email' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Email' ),
												'desc' => Text::_ ( 'Input the person\'s email.' ),
												'placeholder' => 'hello@example.com',
												'std' => ''
										),
										'designation' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Designation' ),
												'desc' => Text::_ ( 'Input the person\'s designation.' ),
												'placeholder' => 'Designer',
												'std' => 'Designer'
										),
										'introtext' => array (
												'type' => 'editor',
												'title' => Text::_ ( 'Description' ),
												'desc' => Text::_ ( 'Input the person\'s description' ),
												'std' => ''
										),

										'socials' => array (
												'type' => 'checkbox',
												'title' => Text::_ ( 'Social Icons' ),
												'desc' => Text::_ ( 'Show social icons link for team' ),
												'std' => 0
										),
										'facebook' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Facebook' ),
												'std' => 'http://www.facebook.com/storejoomla',
												'depends' => array (
														'socials' => 1
												)
										),

										'twitter' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Twitter' ),
												'std' => 'http://twitter.com/storejoomla',
												'depends' => array (
														'socials' => 1
												)
										),

										'youtube' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Youtube' ),
												'depends' => array (
														'socials' => 1
												)
										),

										'linkedin' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Linkedin' ),
												'depends' => array (
														'socials' => 1
												)
										),

										'pinterest' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Pinterest' ),
												'depends' => array (
														'socials' => 1
												)
										),

										'flickr' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Flickr' ),
												'depends' => array (
														'socials' => 1
												)
										),

										'dribbble' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Dribbble' ),
												'depends' => array (
														'socials' => 1
												)
										),

										'behance' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Behance' ),
												'depends' => array (
														'socials' => 1
												)
										),

										'instagram' => array (
												'type' => 'text',
												'title' => Text::_ ( 'Instagram' ),
												'depends' => array (
														'socials' => 1
												),
												'std' => 'https://www.instagram.com'
										)
								)
						),
						'separator_card_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Card' )
						),
						'card_styles' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select on of the boxed card styles or a blank card.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'default' => Text::_ ( 'Card Default' ),
										'primary' => Text::_ ( 'Card Primary' ),
										'secondary' => Text::_ ( 'Card Secondary' ),
										'hover' => Text::_ ( 'Card Hover' ),
										'custom' => Text::_ ( 'Custom' )
								),
								'std' => ''
						),
						'card_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'std' => '#1e87f0',
								'depends' => array (
										array (
												'card_styles',
												'=',
												'custom'
										)
								)
						),
						'card_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' ),
								'depends' => array (
										array (
												'card_styles',
												'=',
												'custom'
										)
								)
						),
						'card_size' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Size' ),
								'desc' => Text::_ ( 'Define the card\'s size by selecting the padding between the card and its content.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'card_styles',
												'!=',
												''
										)
								)
						),
						'image_padding' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Align image without padding' ),
								'desc' => Text::_ ( 'Top, left or right aligned images can be attached to the card\'s edge. If image is aligned to the left or right, it will also exten to cover the whole space' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'card_styles',
												'!=',
												''
										)
								)
						),
						'card_content_padding' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Padding' ),
								'desc' => Text::_ ( 'Add padding to the content if the image is top, bottom, left or right aligned.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'default' => Text::_ ( 'Default' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'card_styles',
												'=',
												''
										)
								)
						),
						'card_width' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Max Width' ),
								'desc' => Text::_ ( 'Set the maximum width.' ),
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
						'separator_person_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Image' )
						),
						'image_styles' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Border' ),
								'desc' => Text::_ ( 'To modify the border radius of an element, like an image.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'rounded' => Text::_ ( 'Rounded' ),
										'circle' => Text::_ ( 'Circle' ),
										'pill' => Text::_ ( 'Pill' )
								),
								'std' => '',
								'depends' => array (
										array (
												'image_padding',
												'!=',
												1
										)
								)
						),
						'image_transition' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Image Transition' ),
								'desc' => Text::_ ( 'Select the image\'s transition style.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'scale-up' => Text::_ ( 'Scales Up' ),
										'scale-down' => Text::_ ( 'Scales Down' )
								),
								'std' => ''
						),
						'box_shadow' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Box Shadow' ),
								'desc' => Text::_ ( 'Select the card\'s box shadow size.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'card_styles',
												'=',
												''
										)
								)
						),
						'hover_box_shadow' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Hover Box Shadow' ),
								'desc' => Text::_ ( 'Select the card\'s box shadow size on hover.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' ),
										'xlarge' => Text::_ ( 'X-Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'card_styles',
												'=',
												''
										)
								)
						),
						'separator_slider_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Slider' )
						),
						'width_mode' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Item Width Mode' ),
								'desc' => Text::_ ( 'Define whether the width of the slider items is fixed or automatically expanded by its content widths' ),
								'values' => array (
										'fixed' => Text::_ ( 'Fixed' ),
										'' => Text::_ ( 'Auto' )
								),
								'std' => 'fixed'
						),
						'height' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Height' ),
								'desc' => Text::_ ( 'The height will adapt automatically based on its content. Alternatively, the height can adapt to the height of viewport. <br/> Note: Make sure, no height is set in the section settings when using on of the viewport options.' ),
								'values' => array (
										'' => Text::_ ( 'Auto' ),
										'full' => Text::_ ( 'Viewport' ),
										'percent' => Text::_ ( 'Viewport (Minus 20%)' ),
										'section' => Text::_ ( 'Viewport (Minus the following section)' )
								),
								'std' => '',
								'depends' => array (
										array (
												'width_mode',
												'!=',
												''
										)
								)
						),
						'min_height' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Min Height' ),
								'desc' => Text::_ ( 'Set the minimum height. This is useful if the content is too large on small devices.' ),
								'min' => - 600,
								'max' => 600,
								'std' => 300,
								'depends' => array (
										array (
												'width_mode',
												'!=',
												''
										),
										array (
												'height',
												'!=',
												''
										)
								)
						),
						'team_grid_column_gap' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Column Gap' ),
								'desc' => Text::_ ( 'Set the size of the gap between the grid columns.' ),
								'values' => array (
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'' => Text::_ ( 'Default' ),
										'large' => Text::_ ( 'Large' ),
										'collapse' => Text::_ ( 'None' )
								),
								'std' => ''
						),
						'divider' => array (
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
												'width_mode',
												'!=',
												''
										),
										array (
												'grid_column_gap',
												'!=',
												'collapse'
										)
								)
						),
						'separator_columns_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Item Width' ),
								'depends' => array (
										array (
												'width_mode',
												'!=',
												''
										)
								)
						),
						'phone_portrait' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Phone Portrait' ),
								'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
								'values' => array (
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => '1-1',
								'depends' => array (
										array (
												'width_mode',
												'!=',
												''
										)
								)
						),
						'phone_landscape' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Phone Landscape' ),
								'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => '',
								'depends' => array (
										array (
												'width_mode',
												'!=',
												''
										)
								)
						),
						'tablet_landscape' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Tablet Landscape' ),
								'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => '1-3',
								'depends' => array (
										array (
												'width_mode',
												'!=',
												''
										)
								)
						),
						'desktop' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Desktop' ),
								'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => '',
								'depends' => array (
										array (
												'width_mode',
												'!=',
												''
										)
								)
						),
						'large_screens' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Large Screens' ),
								'desc' => Text::_ ( 'Set the number of grid columns for each breakpoint. Inherit refers to the number of columns on the next smaller screen size.' ),
								'values' => array (
										'' => Text::_ ( 'Inherit' ),
										'1-1' => Text::_ ( '100%' ),
										'5-6' => Text::_ ( '83%' ),
										'4-5' => Text::_ ( '80%' ),
										'3-5' => Text::_ ( '60%' ),
										'1-2' => Text::_ ( '50%' ),
										'1-3' => Text::_ ( '33%' ),
										'1-4' => Text::_ ( '25%' ),
										'1-5' => Text::_ ( '20%' ),
										'1-6' => Text::_ ( '16%' )
								),
								'std' => '',
								'depends' => array (
										array (
												'width_mode',
												'!=',
												''
										)
								)
						),
						'separator_animation_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Animation' )
						),
						'slidesets' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Slide all visible items at once' ),
								'desc' => Text::_ ( 'To loop through a set of slides instead of single items.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'center_slide' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Center the active slide' ),
								'desc' => Text::_ ( 'Check this option to center the list items.' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'velocity' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Velocity' ),
								'desc' => Text::_ ( 'Set the velocity in pixels per milliseconds.' ),
								'min' => 20,
								'max' => 300
						),
						'finite_slide' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Disable infinite scrolling' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'autoplay' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Autoplay' ),
								'desc' => Text::_ ( 'To activate Slider autoplays to the attribute. ' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0
						),
						'pause' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Pause autoplay on hover' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'autoplay',
												'=',
												1
										)
								)
						),
						'autoplay_interval' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'Interval' ),
								'desc' => Text::_ ( 'Set the autoplay interval in seconds.' ),
								'placeholder' => '7',
								'min' => 5,
								'max' => 15,
								'depends' => array (
										array (
												'autoplay',
												'=',
												1
										)
								)
						),
						'separator_navigation_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Navigation' )
						),
						'navigation' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Navigation Display' ),
								'desc' => Text::_ ( 'Select the navigation type, show or hide navigation control.' ),
								'values' => array (
										'' => Text::_ ( 'Hide' ),
										'dotnav' => Text::_ ( 'Dotnav' )
								),
								'std' => 'dotnav'
						),
						'navigation_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Position' ),
								'desc' => Text::_ ( 'Select the position of the navigation.' ),
								'values' => array (
										'left' => Text::_ ( 'Left' ),
										'center' => Text::_ ( 'Center' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => 'center',
								'depends' => array (
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'nav_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'desc' => Text::_ ( 'Set the vertical margin.' ),
								'values' => array (
										'small' => Text::_ ( 'Small' ),
										'' => Text::_ ( 'Default' ),
										'medium' => Text::_ ( 'Medium' )
								),
								'std' => '',
								'depends' => array (
										array (
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Breakpoint' ),
								'desc' => Text::_ ( 'Display the navigation only on this device width and larger' ),
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
												'navigation',
												'!=',
												''
										)
								)
						),
						'navigation_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Color' ),
								'desc' => Text::_ ( 'Set light or dark color if the navigation is below the slideshow.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'navigation',
												'!=',
												''
										)
								)
						),

						'separator_slidenav_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'SlideNav' )
						),
						'slidenav_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Position' ),
								'desc' => Text::_ ( 'Select the position of the slidenav.' ),
								'values' => array (
										'' => Text::_ ( 'Hide' ),
										'default' => Text::_ ( 'Default' ),
										'outside' => Text::_ ( 'Outside' ),
										'top-left' => Text::_ ( 'Top Left' ),
										'top-right' => Text::_ ( 'Top Right' ),
										'center-left' => Text::_ ( 'Center Left' ),
										'center-right' => Text::_ ( 'Center Right' ),
										'bottom-left' => Text::_ ( 'Bottom Left' ),
										'bottom-center' => Text::_ ( 'Bottom Center' ),
										'bottom-right' => Text::_ ( 'Bottom Right' )
								),
								'std' => 'default'
						),
						'slidenav_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'desc' => Text::_ ( 'Apply a margin between the slidnav and the slider container.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => 'medium',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),
						'slidenav_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Breakpoint' ),
								'desc' => Text::_ ( 'Display the slidenav on this device width and larger.' ),
								'values' => array (
										'' => Text::_ ( 'Always' ),
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 's',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												'outside'
										),
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),
						'slidenav_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Color' ),
								'desc' => Text::_ ( 'Set light or dark color mode for the slidenav.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												'outside'
										),
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),
						'slidenav_outside_breakpoint' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Outside Breakpoint' ),
								'desc' => Text::_ ( 'Display the slidenav only outside on this device width and larger. Otherwise it will be displayed inside' ),
								'values' => array (
										's' => Text::_ ( 'Small (Phone Landscape)' ),
										'm' => Text::_ ( 'Medium (Tablet Landscape)' ),
										'l' => Text::_ ( 'Large (Desktop)' ),
										'xl' => Text::_ ( 'X-Large (Large Screens)' )
								),
								'std' => 'xl',
								'depends' => array (
										array (
												'slidenav_position',
												'=',
												'outside'
										)
								)
						),
						'slidenav_outside_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Outside Color' ),
								'desc' => Text::_ ( 'Set light or dark color if the slidenav is outside of the slider' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => '',
								'depends' => array (
										array (
												'slidenav_position',
												'=',
												'outside'
										)
								)
						),
						'slidenav_on_hover' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Show on hover only' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),
						'larger_style' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Larger style' ),
								'desc' => Text::_ ( 'To increase the size of the slidenav icons' ),
								'values' => array (
										'0' => Text::_ ( 'JNO' ),
										'1' => Text::_ ( 'JYES' )
								),
								'std' => '0',
								'depends' => array (
										array (
												'slidenav_position',
												'!=',
												''
										)
								)
						),

						'separator_title_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Name' )
						),
						'heading_font_family' => array (
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
								'std' => 'h3'
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
						'name_color' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Select the predefined title text color.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-muted' => Text::_ ( 'Muted' ),
										'text-emphasis' => Text::_ ( 'Emphasis' ),
										'text-primary' => Text::_ ( 'Primary' ),
										'text-secondary' => Text::_ ( 'Secondary' ),
										'text-success' => Text::_ ( 'Success' ),
										'text-warning' => Text::_ ( 'Warning' ),
										'text-danger' => Text::_ ( 'Danger' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => ''
						),
						'custom_title_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Custom Color' ),
								'depends' => array (
										array (
												'name_color',
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
								'std' => ''
						),
						'separator_meta_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Designation' )
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
						'meta_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a predefined meta text style, including color, size and font-family' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-meta' => Text::_ ( 'Meta' ),
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
						'designation_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Predefined text style for designation.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'muted' => Text::_ ( 'Muted' ),
										'emphasis' => Text::_ ( 'Emphasis' ),
										'primary' => Text::_ ( 'Primary' ),
										'secondary' => Text::_ ( 'Secondary' ),
										'success' => Text::_ ( 'Success' ),
										'warning' => Text::_ ( 'Warning' ),
										'danger' => Text::_ ( 'Danger' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => 'muted'
						),
						'custom_meta_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Custom Color' ),
								'depends' => array (
										array (
												'designation_style',
												'=',
												''
										)
								)
						),
						'text_transform' => array (
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
										'top' => Text::_ ( 'Above Price' ),
										'' => Text::_ ( 'Below Title' ),
										'content' => Text::_ ( 'Below Content' )
								),
								'std' => ''
						),
						'meta_element' => array (
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
								'std' => 'small'
						),

						'separator_email_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Email' )
						),
						'email_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'Font Family' ),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.ui-email { font-family: {{ VALUE }}; }'
								)
						),
						'email_style' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Predefined Color' ),
								'desc' => Text::_ ( 'Add predefined text color to email elements.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'muted' => Text::_ ( 'Muted' ),
										'emphasis' => Text::_ ( 'Emphasis' ),
										'primary' => Text::_ ( 'Primary' ),
										'secondary' => Text::_ ( 'Secondary' ),
										'success' => Text::_ ( 'Success' ),
										'warning' => Text::_ ( 'Warning' ),
										'danger' => Text::_ ( 'Danger' ),
										'light' => Text::_ ( 'Light' ),
										'dark' => Text::_ ( 'Dark' )
								),
								'std' => 'muted'
						),
						'email_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' ),
								'std' => '#999999',
								'depends' => array (
										array (
												'email_style',
												'=',
												''
										)
								)
						),
						'email_class' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a predefined text style, including color, size and font-family' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'text-meta' => Text::_ ( 'Meta' ),
										'h4' => Text::_ ( 'H4' ),
										'h5' => Text::_ ( 'H5' ),
										'h6' => Text::_ ( 'H6' )
								),
								'std' => 'h6'
						),
						'email_text_transform' => array (
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
						'email_margin_top' => array (
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
										'css' => '.ui-content { font-family: {{ VALUE }}; }'
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
								'std' => 'small'
						),

						'separator_social_style_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'Social' )
						),
						'icons_button' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Display icons as buttons' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 1
						),
						'icon_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'depends' => array (
										array (
												'icons_button',
												'=',
												1
										)
								)
						),
						'icon_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Color' )
						),
						'social_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Position' ),
								'desc' => Text::_ ( 'Place social links before, after description text or overlay (place content on top of an image).' ),
								'values' => array (
										'before' => Text::_ ( 'Before' ),
										'after' => Text::_ ( 'After' ),
										'overlay' => Text::_ ( 'Overlay' )
								),
								'std' => 'after'
						),
						'social_margin_top' => array (
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
								'std' => '',
								'depends' => array (
										array (
												'social_position',
												'!=',
												'overlay'
										)
								)
						),
						'overlay_on_hover' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Display overlay on hover' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 1,
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										)
								)
						),
						'vertical_icons' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'Vertical Social Icons' ),
								'values' => array (
										1 => Text::_ ( 'JYES' ),
										0 => Text::_ ( 'JNO' )
								),
								'std' => 0,
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										)
								)
						),
						'overlay_styles' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Style' ),
								'desc' => Text::_ ( 'Select a style for the overlay.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'overlay-default' => Text::_ ( 'Overlay Default' ),
										'overlay-primary' => Text::_ ( 'Overlay Primary' ),
										'tile-default' => Text::_ ( 'Tile Default' ),
										'tile-muted' => Text::_ ( 'Tile Muted' ),
										'tile-primary' => Text::_ ( 'Tile Primary' ),
										'tile-secondary' => Text::_ ( 'Tile Secondary' ),
										'overlay-custom' => Text::_ ( 'Custom' )
								),
								'std' => 'overlay-primary',
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										)
								)
						),
						'overlay_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background Color' ),
								'std' => '#ffd49b',
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										),
										array (
												'overlay_styles',
												'=',
												'overlay-custom'
										)
								)
						),
						'overlay_padding' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Padding' ),
								'desc' => Text::_ ( 'Set the padding between the overlay and its content.' ),
								'values' => array (
										'' => Text::_ ( 'Default' ),
										'small' => Text::_ ( 'Small' ),
										'large' => Text::_ ( 'Large' ),
										'remove' => Text::_ ( 'None' )
								),
								'std' => '',
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										)
								)
						),
						'overlay_positions' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Overlay Positions' ),
								'desc' => Text::_ ( 'A collection of utility classes to position content.' ),
								'values' => array (
										'top' => Text::_ ( 'Top' ),
										'bottom' => Text::_ ( 'Bottom' ),
										'left' => Text::_ ( 'Left' ),
										'right' => Text::_ ( 'Right' ),
										'top-left' => Text::_ ( 'Top Left' ),
										'top-center' => Text::_ ( 'Top Center' ),
										'top-right' => Text::_ ( 'Top Right' ),
										'bottom-left' => Text::_ ( 'Bottom Left' ),
										'bottom-center' => Text::_ ( 'Bottom Center' ),
										'bottom-right' => Text::_ ( 'Bottom Right' ),
										'center' => Text::_ ( 'Center' ),
										'center-left' => Text::_ ( 'Center Left' ),
										'center-right' => Text::_ ( 'Center Right' )
								),
								'std' => 'center',
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										)
								)
						),
						'overlay_margin' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Margin' ),
								'desc' => Text::_ ( 'Apply a margin between the overlay and the image container.' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'small' => Text::_ ( 'Small' ),
										'medium' => Text::_ ( 'Medium' ),
										'large' => Text::_ ( 'Large' )
								),
								'std' => '',
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										)
								)
						),
						'overlay_alignment' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Alignment' ),
								'values' => array (
										'' => Text::_ ( 'None' ),
										'left' => Text::_ ( 'Left' ),
										'center' => Text::_ ( 'Center' ),
										'right' => Text::_ ( 'Right' )
								),
								'std' => 'center',
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										)
								)
						),
						'overlay_transition' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Overlay Transition' ),
								'desc' => Text::_ ( 'Select a hover transition for the overlay.' ),
								'values' => array (
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
										'slide-right' => Text::_ ( 'Slide Right 100%' )
								),
								'std' => 'fade',
								'depends' => array (
										array (
												'social_position',
												'=',
												'overlay'
										),
										array (
												'overlay_on_hover',
												'=',
												1
										)
								)
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
