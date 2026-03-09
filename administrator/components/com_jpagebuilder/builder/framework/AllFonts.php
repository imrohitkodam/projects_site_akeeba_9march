<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Http\Http;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Get all installed fonts
 *
 * @since 5.0.0
 */
trait JPageBuilderFrameworkAllFonts {
	private function getInstalledFonts() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( '*' )->from ( $db->quoteName ( '#__jpagebuilder_fonts' ) )->where ( $db->quoteName ( 'published' ) . ' = 1' );

		$db->setQuery ( $query );

		try {
			$response = $db->loadObjectList ();

			if (isset ( $response )) {
				foreach ( $response as $key => $value ) {
					if (isset ( $value->data )) {
						$value->data = json_decode ( $value->data );
					}
				}
			}
		} catch ( \Exception $e ) {
			$this->sendResponse ( [ 
					'message' => $e->getMessage ()
			], 500 );
		}

		$this->sendResponse ( $response );
	}
	public function allFonts() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'DELETE',
				'PUT',
				'PATCH'
		], $method );

		if ($method === 'GET') {
			$this->getInstalledFonts ();
		}
	}
}
