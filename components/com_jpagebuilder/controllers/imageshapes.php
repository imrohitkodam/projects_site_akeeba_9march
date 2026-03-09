<?php
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Response\JsonResponse;

/**
 *
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/framework/ImageShapes.php';
class JpagebuilderControllerImageshapes extends FormController {
	use JPageBuilderFrameworkImageShapes;

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
		/** @var CMSAPplication */
		$app = Factory::getApplication ();
		$app->setHeader ( 'status', $statusCode, true );
		$app->sendHeaders ();
		echo new JsonResponse ( $response );
		$app->close ();
	}
	public function getImageShapesAPI() {
		$response = $this->processGetImageShapes ();
		$this->sendResponse ( $response ['response'], $response ['statusCode'] );
	}
	public function addImageShapeAPI() {
		$input = Factory::getApplication ()->getInput();
		$shape = $input->json->get ( 'shape', '', 'base64' );

		if (empty ( $shape )) {
			$response ['message'] = 'Information missing';
			$this->sendResponse ( $response, 400 );
		}

		$response = $this->processAddImageShape ( $shape );
		$this->sendResponse ( $response ['response'], $response ['statusCode'] );
	}
	public function updateImageShapeAPI() {
		$input = Factory::getApplication ()->getInput();
		$id = $input->json->get ( 'id', '', 'string' );
		$name = $input->json->get ( 'name', '', 'string' );
		$shape = $input->json->get ( 'shape', '', 'string' );

		if (empty ( $id ) || empty ( $name ) || empty ( $shape )) {
			$response ['message'] = 'Information missing';
			$this->sendResponse ( $response, 404 );
		}

		$response = $this->processUpdateImageShape ( $id, $name, $shape );
		$this->sendResponse ( $response ['response'], $response ['statusCode'] );
	}
	public function deleteImageShapeAPI() {
		$input = Factory::getApplication ()->getInput();
		$id = $input->json->get ( 'id', '', 'int' );

		if (empty ( $id )) {
			$response ['message'] = 'Information missing';
			$this->sendResponse ( $response, 404 );
		}

		$response = $this->processDeleteImageShape ( $id );
		$this->sendResponse ( $response ['response'], $response ['statusCode'] );
	}
}
