<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

require_once JPATH_COMPONENT . '/builder/classes/ajax.php';
if (! class_exists ( 'JpagebuilderHelperSite' )) {
	require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/helper.php';
}

$user = Factory::getApplication()->getIdentity();
$app = Factory::getApplication ();
$requestArray = &$_POST;

$authorised = $user->authorise ( 'core.edit', 'com_jpagebuilder' ) || ($user->authorise ( 'core.edit.own', 'com_jpagebuilder' ) && ($this->item->created_by == $user->id));
if ($authorised !== true) {
	$app->enqueueMessage ( Text::_ ( 'JERROR_ALERTNOAUTHOR' ), 'error' );
	$app->setHeader ( 'status', 403, true );

	return false;
}

JpagebuilderHelperSite::loadLanguage ();

$input = Factory::getApplication ()->getInput();
$action = $input->get ( 'callback', '', 'string' );
function sanitizeAddonData($data) {
	if (\is_object ( $data )) {
		foreach ( $data as &$value ) {
			if (\is_object ( $value )) {
				$value = sanitizeAddonData ( $value );
			} else if (is_string ( $value )) {
				switch (\strtolower ( $value )) {
					case 'true' :
						$value = '1';
						break;
					case 'false' :
						$value = '0';
						break;
				}
			}
		}
		unset ( $value );
	}

	return $data;
}

// all settings loading
if ($action === 'addon') {
	require_once JPATH_COMPONENT . '/editor/addonparser.php';

	if (! class_exists ( 'JpagebuilderAddon' )) {
		require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/addon.php';
	}

	$post_data = $requestArray ['addon'];
	$post_data_options = $requestArray ['options'] ?? [ ];

	$addon = json_decode ( json_encode ( $post_data ) );
	$addon = sanitizeAddonData ( $addon );

	$addon_name = $addon->name;
	$class_name = JpagebuilderApplicationHelper::generateSiteClassName ( $addon_name );
	$addon_path = JpagebuilderAddonParser::getAddonPath ( $addon_name );

	$addon_options = [ ];

	if ((! isset ( $addon->type ) || $addon->type !== 'inner_row') && isset ( $addon_list [$addon->name] ['attr'] ) && $addon_list [$addon->name] ['attr']) {
		$addon_groups = $addon_list [$addon->name] ['attr'];

		if (is_array ( $addon_groups )) {
			foreach ( $addon_groups as $addon_group ) {
				$addon_options += $addon_group;
			}
		}
	}

	$store = new \stdClass ();

	foreach ( $addon->settings as $key => &$setting ) {
		if (\is_object ( $setting )) {
			$original = \json_decode ( \json_encode ( $setting ) );
		}

		if (isset ( $setting->md )) {
			$md = isset ( $setting->md ) ? $setting->md : "";
			$sm = isset ( $setting->sm ) ? $setting->sm : "";
			$xs = isset ( $setting->xs ) ? $setting->xs : "";

			$xl = isset ( $setting->xl ) ? $setting->xl : $md;
			$lg = isset ( $setting->lg ) ? $setting->lg : $md;

			$keySm = $key . '_sm';
			$keyXs = $key . '_xs';
			$keyLg = $key . '_lg';
			$keyXl = $key . '_xl';
			$keyMd = $key . '_md';
			$addon->settings->$keySm = $sm;
			$addon->settings->$keyXs = $xs;
			$addon->settings->$keyXl = $xl;
			$addon->settings->$keyLg = $lg;
			$addon->settings->$keyMd = $md;
			$originalKey = $key . '_original';
			$store->$originalKey = $original;
		}

		if (isset ( $addon_options [$key] ['selector'] )) {
			$addon_selector = $addon_options [$key] ['selector'];

			if (isset ( $addon->settings->{$key} ) && ! empty ( $addon->settings->{$key} )) {
				$selector_value = $addon->settings->{$key};
				$addon->settings->{$key . '_selector'} = str_replace ( '{{ VALUE }}', $selector_value, $addon_selector );
			}
		}

		// Repeatable
		if ((! isset ( $addon->type ) || $addon->type !== 'inner_row') && (($key == 'jp_' . $addon->name . '_item') || ($key == $addon->name . '_item'))) {
			if (count ( ( array ) $setting )) {
				foreach ( $setting as &$options ) {
					foreach ( $options as $key2 => &$opt ) {

						if (isset ( $opt->md )) {
							$md = isset ( $opt->md ) ? $opt->md : "";
							$sm = isset ( $opt->sm ) ? $opt->sm : "";
							$xs = isset ( $opt->xs ) ? $opt->xs : "";
							$opt = $md;
							$options->{$key2 . '_sm'} = $sm;
							$options->{$key2 . '_xs'} = $xs;
						}

						if (isset ( $addon_options [$key] ['attr'] [$key2] ['selector'] )) {
							$addon_selector = $addon_options [$key] ['attr'] [$key2] ['selector'];
							if (isset ( $options->{$key2} ) && ! empty ( $options->{$key2} )) {
								$selector_value = $options->{$key2};
								$options->{$key2 . '_selector'} = str_replace ( '{{ VALUE }}', $selector_value, $addon_selector );
							}
						}
					}
				}
			}
		}
	}

	unset ( $setting );

	foreach ( $store as $key => $value ) {
		$addon->settings->$key = $value;
	}

	$output = '';

	require_once $addon_path . '/block.php';

	$assets = array ();
	$css = LayoutHelper::render ( 'addon.css', array (
			'addon' => $addon
	) );

	if (class_exists ( $class_name )) {
		$addon->listIndex = $post_data_options ['collectionItemIndex'] ?? 0;

		$addon_obj = new $class_name ( $addon ); // initialize addon class
		$addon_output = $addon_obj->render ();

		// css
		if (method_exists ( $class_name, 'css' )) {
			$css .= $addon_obj->css ();
		}

		// js
		if (method_exists ( $class_name, 'js' )) {
			$assets ['js'] = $addon_obj->js ();
		}
	} else {
		$addon_output = JpagebuilderAddonParser::runAddon ( JpagebuilderAddonParser::generateShortcode ( $addon, 0, 0 ) );
	}

	if ($css) {
		$assets ['css'] = $css;
	}

	if (empty ( $addon_output )) {
		$addon_output = '<div class="jpb-empty-addon">
						 <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="140.1px" height="24.2px" viewBox="0 0 576 512">
						 <path fill="#000" d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z"/>
						 </svg></div>';
	}

	if (! empty ( $addon_output )) {
		$output .= LayoutHelper::render ( 'addon.start', array (
				'addon' => $addon
		) ); // start addon
		$output .= $addon_output;
		$output .= LayoutHelper::render ( 'addon.end' ); // end addon
	}

	echo json_encode ( array (
			'html' => htmlspecialchars_decode ( $output ),
			'status' => 'true',
			'assets' => $assets
	) );
	die ();
}

if ($action === 'get-page-data') {
	$page_path = $requestArray ['pagepath'];
	if (file_exists ( $page_path )) {
		$content = file_get_contents ( $page_path );

		if (is_array ( json_decode ( $content ) )) {
			require_once JPATH_COMPONENT . '/builder/classes/addon.php';
			$content = JpagebuilderAddon::__ ( $content, true );
			$content = JpagebuilderAddon::getFontendEditingPage ( $content );

			echo json_encode ( array (
					'status' => true,
					'data' => $content
			) );
			die ();
		}
	}

	echo json_encode ( array (
			'status' => false,
			'data' => 'Something worng there.'
	) );
	die ();
}

// all settings loading
if ($action === 'setting_value') {
	require_once JPATH_COMPONENT . '/builder/classes/base.php';
	require_once JPATH_COMPONENT . '/builder/classes/config.php';

	$addon_name = $requestArray ['name'];
	$addon_id = $requestArray ['id'];
	JpagebuilderBase::loadSingleAddon ( $addon_name );
	$addonList = JpagebuilderConfig::$addons;
	$addonItem = $addonList [$addon_name];
	$addonItem = JpagebuilderAddonsHelper::modernizeAddonStructure ( $addonItem );

	require_once JPATH_COMPONENT . '/editor/addonparser.php';

	$settings = ! empty ( $addonItem ) ? JpagebuilderEditorUtils::extractSettingsDefaultValues ( $addonItem ['settings'] ) : [ ];
	$globalDefaults = [ ];
	$globalSettingsGroups = [ 
			'style',
			'advanced',
			'interaction'
	];
	$globalSettings = JpagebuilderBase::addonOptions ();

	foreach ( $globalSettingsGroups as $groupName ) {
		$globalDefaults = array_merge ( $globalDefaults, JpagebuilderEditorUtils::extractSettingsDefaultValues ( $globalSettings [$groupName] ) );
	}

	$settings = array_merge ( $settings, $globalDefaults );

	$addon = json_decode ( json_encode ( array (
			'id' => $addon_id,
			'name' => $addon_name,
			'settings' => $settings
	) ) );

	$class_name = JpagebuilderApplicationHelper::generateSiteClassName ( $addon_name );
	$addon_path = JpagebuilderAddonParser::getAddonPath ( $addon_name );

	$output = '';

	require_once $addon_path . '/block.php';

	$assets = array ();
	$css = LayoutHelper::render ( 'addon.css', array (
			'addon' => $addon
	) );

	if (class_exists ( $class_name )) {
		$addon->listIndex = $requestArray ['collectionItemIndex'] ?? 0;

		$addon_obj = new $class_name ( $addon ); // initialize addon class
		$addon_output = $addon_obj->render ();

		// css
		if (method_exists ( $class_name, 'css' )) {
			$css .= $addon_obj->css ();
		}

		// js
		if (method_exists ( $class_name, 'js' )) {
			$assets ['js'] = $addon_obj->js ();
		}
	} else {
		$addon_output = JpagebuilderAddonParser::runAddon ( JpagebuilderAddonParser::generateShortcode ( $addon, 0, 0 ) );
	}

	if ($css) {
		$assets ['css'] = $css;
	}

	if (empty ( $addon_output )) {
		$addon_output = '<div class="jpb-empty-addon">
						 <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="140.1px" height="24.2px" viewBox="0 0 576 512">
						 <path fill="#000" d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z"/>
						 </svg></div>';
	}

	$output .= LayoutHelper::render ( 'addon.start', array (
			'addon' => $addon
	) ); // start addon
	$output .= $addon_output;
	$output .= LayoutHelper::render ( 'addon.end' ); // end addon

	echo json_encode ( array (
			'formData' => json_encode ( $settings ),
			'html' => htmlspecialchars_decode ( $output ),
			'status' => 'true',
			'assets' => $assets
	) );
	die ();
}

require_once JPATH_COMPONENT . '/helpers/ajax.php';
