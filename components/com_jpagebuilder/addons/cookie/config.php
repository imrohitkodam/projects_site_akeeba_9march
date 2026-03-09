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

JpagebuilderConfig::addonConfig ( array (
		'type' => 'general',
		'addon_name' => 'cookie',
		'title' => Text::_ ( 'Cookie Bar' ),
		'desc' => Text::_ ( 'Cookie Bar addon for alerting users about the use of cookies on your website.' ),
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="none" stroke="#000000" stroke-width="1.0006" stroke-linejoin="round" stroke-miterlimit="10" d="M9.991,1.117  c-0.26,0-0.516,0.017-0.77,0.039c-0.1,1.935-1.687,3.478-3.647,3.478c-0.25,0-0.49-0.033-0.725-0.081  C5.247,5.139,5.48,5.846,5.48,6.608c0,2.025-1.642,3.667-3.667,3.667c-0.205,0-0.402-0.028-0.597-0.06  c0.163,4.711,4.023,8.483,8.775,8.483c4.855,0,8.791-3.936,8.791-8.791S14.847,1.117,9.991,1.117z"/>  <circle fill="none" stroke="#000000" stroke-width="1.0006" stroke-miterlimit="10" cx="12.438" cy="6.42" r="1.974"/> <circle fill="none" stroke="#000000" stroke-width="1.0006" stroke-miterlimit="10" cx="12.249" cy="13.189" r="1.505"/> <circle stroke="#000000" cx="6.702" cy="11.592" r="1.128"/> </svg>',
		'category' => 'Interface',
		'attr' => array (
				'general' => array (
						'admin_label' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL_DESC' ),
								'std' => ''
						),
						'position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'Position' ),
								'values' => array (
										'bottom' => Text::_ ( 'Banner bottom' ),
										'top' => Text::_ ( 'Banner top' ),
										'left' => Text::_ ( 'Floating left' ),
										'right' => Text::_ ( 'Floating right' )
								),
								'std' => 'left'
						),
						'cookie_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Background' ),
								'std' => '#252e39'
						),
						'cookie_button_background' => array (
								'type' => 'color',
								'title' => Text::_ ( 'Button Background' ),
								'std' => '#1e87f0'
						),
						'message' => array (
								'type' => 'editor',
								'title' => Text::_ ( 'Message' ),
								'std' => 'This website uses cookies to ensure you get the best experience on our website.'
						),
						'dismiss' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Dismiss' ),
								'std' => 'Got it'
						),
						'url' => array (
								'type' => 'media',
								'format' => 'attachment',
								'title' => Text::_ ( 'Link' ),
								'placeholder' => 'http://',
								'hide_preview' => true
						),
						'link' => array (
								'type' => 'text',
								'title' => Text::_ ( 'Policy' ),
								'std' => 'Learn more'
						),
						'target' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK_NEWTAB_DESC' ),
								'values' => array (
										'_self' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_SAME_WINDOW' ),
										'_blank' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_GLOBAL_TARGET_NEW_WINDOW' )
								)
						)
				)
		)
) );
