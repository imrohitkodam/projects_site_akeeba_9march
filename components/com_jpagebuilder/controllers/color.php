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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Response\JsonResponse;

/**
 * Color Controller class
 *
 * @since 5.0.0
 */
class JpagebuilderControllerColor extends FormController {

	/**
	 * Send JSON Response to the client.
	 *
	 * @param array $response
	 *        	The response array or data.
	 * @param int $statusCode
	 *        	The status code of the HTTP response.
	 *        	
	 * @return void
	 * @since 5.0.0
	 */
	private function sendResponse($response, int $statusCode = 200): void {
		$app = Factory::getApplication ();
		$app->setHeader ( 'status', $statusCode, true );
		$app->sendHeaders ();
		echo new JsonResponse ( $response );
		$app->close ();
	}
	/**
	 * Get global colors
	 */
	public function globalColors() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( [ 
				'id',
				'name',
				'colors'
		] )->from ( $db->quoteName ( '#__jpagebuilder_colors' ) )->where ( $db->quoteName ( 'published' ) . ' = 1' );
		$db->setQuery ( $query );

		$colors = [ ];

		try {
			$colors = $db->loadObjectList ();
		} catch ( \Exception $e ) {
			return [ ];
		}

		if (! empty ( $colors )) {
			foreach ( $colors as &$color ) {
				$color->colors = \json_decode ( $color->colors );
			}

			unset ( $color );
		}

		$this->sendResponse ( $colors );
	}
}
