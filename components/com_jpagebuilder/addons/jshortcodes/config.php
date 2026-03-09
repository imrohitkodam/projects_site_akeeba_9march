<?php
/**
 * @package JSHORTCODES::JPAGEBUILDER
 * @subpackage plugins
 * @author Joomla! Extensions Store
 * @copyright (C)2024 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct accees
defined ( '_JEXEC' ) or die ( 'Restricted Aceess' );
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

JpagebuilderConfig::addonConfig ( array (
		'type' => 'content',
		'addon_name' => 'jshortcodes',
		'title' => 'JShortcodes',
		'desc' => 'This is the addon for JShortcodes, to integrate shortcodes into JPageBuilder',
		'icon' => Uri::root () . 'components/com_jpagebuilder/addons/jshortcodes/assets/images/icon.svg',
		'category' => 'Shortcodes',
		'attr' => array (
				'general' => array (
						// Content
						'shortcode' => array (
								'type' => 'editor',
								'title' => Text::_ ( 'Enter shortcodes here' ),
								'desc' => Text::_( 'Enter shortcodes for JShortcodes in this editor area. You can also generate shortcodes through the JShortcodes interface using the Joomla editor' )
						),
						'admin_label' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL_DESC' ),
								'std' => ''
						),
						'addon_margin' => array (
								'type' => 'margin',
								'title' => 'Margin',
								'std' => '0px 0px 30px 0px',
								'responsive' => true
						),
						'addon_padding' => array (
								'type' => 'padding',
								'title' => 'Padding',
								'std' => '0px 0px 0px 0px',
								'responsive' => true
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