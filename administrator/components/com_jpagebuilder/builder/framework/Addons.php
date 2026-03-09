<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Trait for managing addons API endpoints.
 */
trait JPageBuilderFrameworkAddons {
	private function getAddonByName(string $name) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( '*' )->from ( $db->quoteName ( '#__jpagebuilder_addonlist' ) )->where ( $db->quoteName ( 'name' ) . ' = ' . $db->quote ( $name ) );

		$db->setQuery ( $query );

		return $db->loadObject ();
	}
	private function updateOrCreate($data) {
		$db = Factory::getContainer()->get('DatabaseDriver');

		if (! empty ( $data->id )) {
			$db->updateObject ( '#__jpagebuilder_addonlist', $data, 'id', true );
		} else {
			$db->insertObject ( '#__jpagebuilder_addonlist', $data, 'id' );
			$data->id = $db->insertid ();
		}

		return $data;
	}
	private function getAddonListFromDatabase() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( '*' )->from ( $db->quoteName ( '#__jpagebuilder_addonlist' ) );

		$db->setQuery ( $query );

		return $db->loadObjectList ( 'name' ) ?? [ ];
	}
	private function changeFavoriteState() {
		$addonName = $this->getInput ( 'addon_name', '', 'string' );
		$favoriteState = $this->getInput ( 'state', null, 'int' );

		if (empty ( $addonName )) {
			$this->sendResponse ( [ 
					'message' => 'Missing addon name'
			], 400 );
		}

		if (\is_null ( $favoriteState )) {
			$this->sendResponse ( [ 
					'message' => 'Missing favorite state'
			], 400 );
		}

		$addon = $this->getAddonByName ( $addonName );
		$data = ( object ) [ 
				'name' => $addonName,
				'ordering' => 0,
				'is_favorite' => $favoriteState,
				'status' => 1
		];

		if (! empty ( $addon )) {
			$data = $addon;
			$data->is_favorite = $favoriteState;
		}

		$this->updateOrCreate ( $data );

		$response = ( object ) [ 
				'addon_name' => $addonName,
				'state' => $favoriteState
		];

		$this->sendResponse ( $response );
	}
	public function addons() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'DELETE'
		], $method );

		switch ($method) {
			case 'GET' :
				$this->getAddonList ();
				break;
			case 'PATCH' :
				$this->toggleAddonStatus ();
				break;
			case 'PUT' :
				$this->changeFavoriteState ();
				break;
		}
	}
	public function toggleAddonStatus() {
		$addonName = $this->getInput ( 'addon_name', '', 'string' );

		if (empty ( $addonName )) {
			$this->sendResponse ( [ 
					'message' => 'Missing addon name'
			], 400 );
		}

		$status = 0;
		$addon = $this->getAddonByName ( $addonName );
		$data = ( object ) [ 
				'name' => $addonName,
				'ordering' => 0
		];

		if (! empty ( $addon )) {
			$status = $addon->status ? 0 : 1;
			$data->id = $addon->id;
		}

		$data->status = $status;
		$this->updateOrCreate ( $data );

		$response = ( object ) [ 
				'addon_name' => $addonName,
				'status' => $status
		];

		$this->sendResponse ( $response );
	}
	public function getAddonList() {
		if (! class_exists ( 'JpagebuilderConfig' )) {
			require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/base.php';
			require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/config.php';
		}

		JpagebuilderBase::loadAddons ();
		$addons = JpagebuilderConfig::$addons;
		$type = $this->getInput ( 'type', null, 'string' );
		$flattenList = new stdClass ();
		$parsedAddons = [ ];
		$globalSettings = JpagebuilderBase::addonOptions ();
		$globalDefaults = [ ];
		$globalSettingsGroups = [ 
				'style',
				'advanced',
				'interaction'
		];

		foreach ( $globalSettingsGroups as $groupName ) {
			$globalDefaults = array_merge ( $globalDefaults, JpagebuilderEditorUtils::extractSettingsDefaultValues ( $globalSettings [$groupName] ) );
		}

		$databaseAddons = $this->getAddonListFromDatabase ();
		$parsedAddons ['Favourite'] = [ ];

		foreach ( $addons as $addon ) {
			$hasContext = isset ( $addon ['context'] );

			if ($hasContext && $type !== 'single') {
				$contextArray = explode ( '.', $addon ['context'], 2 );

				if ($contextArray [1] === 'single') {
					continue;
				}
			}

			$category = $addon ['category'] ?? 'General';
			$addon ['default'] = [ ];

			if (! isset ( $parsedAddons [$category] )) {
				$parsedAddons [$category] = [ ];
			}

			$addonName = preg_replace ( '/^jp_/i', '', $addon ['addon_name'] );
			$className = JpagebuilderApplicationHelper::generateSiteClassName ( $addonName );

			$flattenAddon = $addon;

			$addonStructure = JpagebuilderAddonsHelper::modernizeAddonStructure ( $flattenAddon );
			$addonDefaults = JpagebuilderEditorUtils::extractSettingsDefaultValues ( $addonStructure ['settings'] );
			$addonStructure ['default'] = array_merge ( $globalDefaults, $addonDefaults );
			$addonStructure ['desc'] = $addonStructure ['desc'] ?? '';

			$flattenList->$addonName = $addonStructure;

			$addonObject = ( object ) [ 
					'type' => $addonStructure ['type'],
					'name' => $addonName,
					'title' => $addonStructure ['title'],
					'description' => $addonStructure ['desc'],
					'category' => $addonStructure ['category'] ?? 'General',
					'icon' => $addonStructure ['icon'],
					'default' => $addonStructure ['default'],
					'visibility' => true,
					'js_template' => method_exists ( $className, 'getFrontendEditor' ),
					'status' => $databaseAddons [$addonName]->status ?? 1,
					'is_favorite' => $databaseAddons [$addonName]->is_favorite ?? 0,
					'pro' => $addonStructure ['pro'] ?? false
			];

			if ($addonObject->is_favorite) {
				if (! isset ( $parsedAddons ['Favourite'] )) {
					$parsedAddons ['Favourite'] = [ ];
				}

				$parsedAddons ['Favourite'] [] = $addonObject;
			}

			$parsedAddons [$category] [] = $addonObject;
		}

		if (isset ( $parsedAddons ['Structure'] )) {
			$structureGroup = $parsedAddons ['Structure'];
			unset ( $parsedAddons ['Structure'] );
			$parsedAddons = array_merge ( [ 
					'Structure' => $structureGroup
			], $parsedAddons );
		}

		$favoriteGroup = $parsedAddons ['Favourite'];

		if (! empty ( $favoriteGroup )) {
			$parsedAddons = array_merge ( [ 
					'Favourite' => $favoriteGroup
			], $parsedAddons );
		}

		foreach ( $parsedAddons as $category => $_ ) {
			if ($category === 'Structure') {
				continue;
			}

			usort ( $parsedAddons [$category], function ($first, $second) {
				return strcmp ( strtolower ( $first->title ), strtolower ( $second->title ) );
			} );
		}

		$keyOrders = [ 
				'row',
				'columns',
				'div'
		];

		if (isset ( $parsedAddons ['Structure'] )) {
			usort ( $parsedAddons ['Structure'], function ($first, $second) use ($keyOrders) {
				return array_search ( $first->name, $keyOrders ) - array_search ( $second->name, $keyOrders );
			} );
		}

		$response = ( object ) [ 
				'addons' => $flattenList,
				'groups' => $parsedAddons,
				'globals' => $globalSettings
		];

		$this->sendResponse ( $response );
	}
}
