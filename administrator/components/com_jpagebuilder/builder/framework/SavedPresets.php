<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait of saved addons providers.
 *
 * @version 4.1.0
 */
trait JPageBuilderFrameworkSavedPresets {

	/**
	 * Get all the saved addons form the database.
	 *
	 * @return void
	 */
	private function getSavedPresetById() {
		$id = $this->getInput ( 'id', null, 'int' );

		try {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select ( $db->quoteName ( array (
					'id',
					'title',
					'preset',
					'is_default',
					'created',
					'created_by'
			) ) );
			$query->from ( $db->quoteName ( '#__jpagebuilder_presets' ) );
			$query->order ( $db->quoteName ( 'ordering' ) . ' ASC' );
			$query->where ( $db->quoteName ( 'id' ) . ' = ' . $db->quote ( $id ) );
			$db->setQuery ( $query );
			$result = $db->loadObject ();

			if (! empty ( $result )) {
				$result->created = (new DateTime ( $result->created ))->format ( 'j F, Y' );
				$result->author = Factory::getContainer()->get('user.factory')->loadUserById($result->created_by)->name;
				$result->preset = $result->preset;
			}

			$this->sendResponse ( $result );
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			$this->sendResponse ( $response, 500 );
		}
	}

	/**
	 * Save Addon for future use.
	 *
	 * @return void
	 */
	private function savePreset() {
		$title = $this->getInput ( 'title', '', 'string' );
		$addonName = $this->getInput ( 'addon_name', '', 'string' );
		$preset = $this->getInput ( 'preset', '', 'RAW' );

		if (empty ( $title ) || empty ( $preset )) {
			$response ['message'] = 'Information missing';
			$this->sendResponse ( $response, 404 );
		}

		if (is_array ( $preset )) {
			$preset = json_encode ( $preset );
		}

		$data = new stdClass ();
		$data->title = $title;
		$data->preset = $preset;
		$data->addon_name = $addonName;
		$data->created = Factory::getDate ()->toSql ();
		$data->created_by = Factory::getApplication ()->getIdentity ()->id;

		try {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$db->insertObject ( '#__jpagebuilder_presets', $data, 'id' );

			$this->sendResponse ( $db->insertid (), 201 );
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			$this->sendResponse ( $response, 500 );
		}
	}
	/**
	 * Icon API endpoint for saved addons.
	 *
	 * @return void
	 * @version 4.1.0
	 */
	public function savedPresets() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'PUT',
				'PATCH'
		], $method );

		switch ($method) {
			case 'GET' :
				$this->getSavedPresetById ();
				break;
			case 'POST' :
				$this->savePreset ();
				break;
		}
	}
}