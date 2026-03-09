<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Plugin\PluginHelper;

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Addon Config class.
 *
 * @since 1.0.0
 */
class JpagebuilderConfig {
	/**
	 * Addons list
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	public static $addons = array ();

	/**
	 * Replace jp_ string form addon name.
	 *
	 * @param string $from
	 *        	_sp string
	 * @param string $to
	 *        	'' String
	 * @param string $subject
	 *        	addon name
	 *        	
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private static function str_replace_first($from, $to, $subject) {
		$from = '/' . preg_quote ( $from, '/' ) . '/';

		return preg_replace ( $from, $to, $subject, 1 );
	}

	
	/**
	 * Get Current Template
	 *
	 * @return void
	 * @since 4.0.0
	 */
	private static function getTemplateName() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'template'
		) ) );
		$query->from ( $db->quoteName ( '#__template_styles' ) );
		$query->where ( $db->quoteName ( 'client_id' ) . ' = 0' );
		$query->where ( $db->quoteName ( 'home' ) . ' = 1' );
		$db->setQuery ( $query );
		
		return $db->loadObject ()->template;
	}
	
	/**
	 * Generate addon list form addon config array.
	 *
	 * @param array $attributes
	 *        	addon attributes
	 *        	
	 * @return array addon list.
	 *        
	 * @since 1.0.0
	 */
	public static function addonConfig($attributes) {
		if (empty ( $attributes ['addon_name'] ) || empty ( $attributes ))
			return;

		$addon = self::str_replace_first ( 'jp_', '', $attributes ['addon_name'] );
		$app = Factory::getApplication ();
		$com_option = $app->getInput()->get ( 'option', '', 'str' );
		$com_view = $app->getInput()->get ( 'view', '', 'str' );
		$com_id = $app->getInput()->get ( 'id', 0, 'int' );

		if ($app->isClient ( 'administrator' ) || ($com_option === 'com_jpagebuilder' && $com_view === 'form' && $com_id)) {
			if (! isset ( $attributes ['icon'] ) || ! $attributes ['icon'])
				$attributes ['icon'] = self::getIcon ( $addon );
		}

		$addon = self::str_replace_first ( 'jp_', '', $attributes ['addon_name'] );

		$app = Factory::getApplication ();
		$com_option = $app->getInput()->get ( 'option', '', 'str' );
		$com_view = $app->getInput()->get ( 'view', '', 'str' );
		$com_id = $app->getInput()->get ( 'id', 0, 'int' );

		if ($app->isClient ( 'administrator' ) || ($com_option === 'com_jpagebuilder' && $com_view === 'form' && $com_id)) {
			if (! isset ( $attributes ['icon'] ) || ! $attributes ['icon']) {
				$attributes ['icon'] = self::getIcon ( $addon );
			}
		}

		if (isset ( $attributes ['attr'] ) && is_array ( $attributes ['attr'] )) {
			if (! isset ( $attributes ['attr'] ['general'] )) {
				foreach ( $attributes ['attr'] as $key => $attr ) {
					if (isset ( $attributes ['attr'] [$key] ) && $attributes ['attr'] [$key]) {
						unset ( $attributes ['attr'] [$key] );
					}
					$attributes ['attr'] ['general'] [$key] = $attr;
				}
			}
		}

		self::$addons [$addon] = $attributes;
	}

	/**
	 * Load the addons from the `#__jpagebuilder_addonlist` table.
	 * This table store the status of the addons.
	 *
	 * @return array
	 *
	 * @since 4.0.0
	 */
	public static function loadAddonList(): array {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'id, name, status, is_favorite' )
			  ->from ( $db->quoteName ( '#__jpagebuilder_addonlist' ) );
		$db->setQuery ( $query );

		try {
			return $db->loadObjectList ( 'name' );
		} catch ( Exception $e ) {
			return [ ];
		}

		return [ ];
	}

	/**
	 * Get active addons list.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	public static function getAddons() {
		$addonList = self::loadAddonList ();

		if (empty ( $addonList )) {
			return self::$addons;
		}

		return array_filter ( self::$addons, function ($_addon) use ($addonList) {
			$addonName = $_addon ['addon_name'] ?? '';

			if (empty ( $addonName )) {
				return true;
			}

			if (! isset ( $addonList [$addonName] )) {
				return true;
			}

			return $addonList [$addonName]->status == 1;
		} );
	}

	/**
	 * Get addon icon
	 *
	 * @param stdClass $addon
	 * @return string
	 * @since 4.0.0
	 */
	public static function getIcon($addon) {
		if (isset ( $addon->icon )) {
			return $addon->icon;
		} elseif (isset ( $addon ['icon'] )) {
			return $addon ['icon'];
		} elseif (! empty ( $addon )) {
			$template_name = self::getTemplateName ();
			$template_path = JPATH_ROOT . '/templates/' . $template_name . '/jpagebuilder/addons/' . $addon . '/assets/images/icon.png';
			$com_file_path = JPATH_ROOT . '/components/com_jpagebuilder/addons/' . $addon . '/assets/images/icon.png';

			$path = JPATH_PLUGINS . '/jpagebuilder';
			$plg_file_path = '';
			$plg_icon_path = '';

			if (is_dir ( $path )) {
				$plugins = Folder::folders ( $path );

				if (count ( ( array ) $plugins )) {
					foreach ( $plugins as $plugin ) {
						if (PluginHelper::isEnabled ( 'jpagebuilder', $plugin )) {
							if (is_dir ( JPATH_PLUGINS . '/jpagebuilder/' . $plugin . '/addons/' . $addon )) {
								$plg_icon_path = JPATH_PLUGINS . '/jpagebuilder/' . $plugin . '/addons/' . $addon . '/assets/images/icon.png';

								if (file_exists ( $plg_icon_path )) {
									$plg_file_path = Uri::root ( TRUE ) . '/plugins/jpagebuilder/' . $plugin . '/addons/' . $addon . '/assets/images/icon.png';
								}
							}
						}
					}
				}
			}

			if (file_exists ( $template_path )) {
				$icon = Uri::root ( true ) . '/templates/' . $template_name . '/jpagebuilder/addons/' . $addon . '/assets/images/icon.png';
			} elseif (file_exists ( $com_file_path )) {
				$icon = Uri::root ( true ) . '/components/com_jpagebuilder/addons/' . $addon . '/assets/images/icon.png';
			} elseif (! empty ( $plg_icon_path ) && file_exists ( $plg_icon_path )) {
				$icon = $plg_file_path;
			} else {
				$icon = Uri::root ( true ) . '/administrator/components/com_jpagebuilder/assets/img/addon-default.png';
			}

			return '<img src="' . $icon . '"/>';
		}
	}
}
