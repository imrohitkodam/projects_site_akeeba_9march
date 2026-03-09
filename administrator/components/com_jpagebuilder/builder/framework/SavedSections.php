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
 * Trait of saved sections providers.
 *
 * @version 4.1.0
 */
trait JPageBuilderFrameworkSavedSections {
	/**
	 * Icon API endpoint for saved sections.
	 *
	 * @return void
	 * @version 4.1.0
	 */
	public function savedSection() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'PUT',
				'PATCH'
		], $method );

		switch ($method) {
			case 'GET' :
				$this->getSavedSections ();
				break;
			case 'POST' :
				$this->saveSection ();
				break;
			case 'DELETE' :
				$this->deleteSection ();
				break;
		}
	}

	/**
	 * Get all the saved sections form the database.
	 *
	 * @return void
	 */
	private function getSavedSections() {
		try {
			$db = Factory::getContainer ()->get ( 'DatabaseDriver' );
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select ( $db->quoteName ( [ 
					'id',
					'title',
					'section',
					'created',
					'created_by'
			] ) );
			$query->from ( $db->quoteName ( '#__jpagebuilder_sections' ) );
			$query->order ( $db->quoteName ( 'ordering' ) . ' ASC' );
			$db->setQuery ( $query );
			$results = $db->loadObjectList ();

			if (! empty ( $results )) {
				foreach ( $results as &$result ) {
					$result->created = (new DateTime ( $result->created ))->format ( 'j F, Y' );
					$result->author = Factory::getContainer ()->get ( 'user.factory' )->loadUserById ( $result->created_by )->name;
					$result->section = JpagebuilderHelper::formatSavedSection ( $result->section );
					unset ( $result->created_by );
				}

				unset ( $result );
			}

			$this->sendResponse ( $results );
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			$this->sendResponse ( $response, 500 );
		}
	}

	/**
	 * Save Section for future use.
	 *
	 * @return void
	 */
	private function saveSection() {
		$title = $this->getInput ( 'title', '', 'string' );
		$section = $this->getInput ( 'section', '', 'RAW' );

		if (empty ( $title ) || empty ( $section )) {
			$response ['message'] = 'Information missing';
			$this->sendResponse ( $response, 400 );
		}

		if (is_array ( $section )) {
			$section = json_encode ( $section );
		}

		try {
			$db = Factory::getContainer ()->get ( 'DatabaseDriver' );

			// Verifica se il titolo esiste già
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select ( 'id' )->from ( '#__jpagebuilder_sections' )
				  ->where ( $db->quoteName ( 'title' ) . ' = ' . $db->quote ( $title ) );
			$db->setQuery ( $query );
			$existingId = $db->loadResult ();

			if ($existingId) {
				// Update se il titolo esiste - aggiorna solo section
				$data = new stdClass ();
				$data->id = $existingId;
				$data->section = $section;

				$db->updateObject ( '#__jpagebuilder_sections', $data, 'id' );
				$this->sendResponse ( $existingId, 201 );
			} else {
				// Insert se il titolo non esiste
				$data = new stdClass ();
				$data->title = $title;
				$data->section = $section;
				$data->created = Factory::getDate ()->toSql ();
				$data->created_by = Factory::getApplication ()->getIdentity ()->id;

				$db->insertObject ( '#__jpagebuilder_sections', $data, 'id' );
				$this->sendResponse ( $db->insertid (), 201 );
			}
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			$this->sendResponse ( $response, 500 );
		}
	}

	/**
	 * Delete saved sections form the database.
	 *
	 * @return void
	 */
	private function deleteSection() {
		$id = $this->getInput ( 'id', '', 'int' );

		if (empty ( $id )) {
			$response ['message'] = 'Information missing';
			$this->sendResponse ( $response, 404 );
		}

		try {
			$db = Factory::getContainer ()->get ( 'DatabaseDriver' );

			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->delete ( $db->quoteName ( '#__jpagebuilder_sections' ) );
			$query->where ( $db->quoteName ( 'id' ) . ' = ' . $db->quote ( $id ) );

			$db->setQuery ( $query );
			$db->execute ();

			$this->sendResponse ( 'Section deleted successfully!', 200 );
		} catch ( Exception $e ) {
			$response ['message'] = $e->getMessage ();
			$this->sendResponse ( $response, 500 );
		}
	}
}
