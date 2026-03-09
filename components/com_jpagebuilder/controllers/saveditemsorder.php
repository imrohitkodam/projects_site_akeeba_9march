<?php
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Utilities\ArrayHelper;

/**
 *
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderControllerSaveditemsorder extends FormController {
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
	 * Update Saved Items Order
	 */
	public function updateSavedItemsOrder() {
		$input = Factory::getApplication ()->getInput();
		$pks = $input->json->get ( 'ids', '', 'string' );
		$orders = $input->json->get ( 'orders', '', 'string' );
		$type = $input->json->get ( 'type', '', 'string' );

		if (empty ( $pks ) || empty ( $orders ) || empty ( $type )) {
			$response ['message'] = 'Missing ids or orders';
			$this->sendResponse ( $response, 400 );
		}

		BaseDatabaseModel::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_jpagebuilder/models' );

		$model = $this->getModel ( $type, 'JpagebuilderModel' );

		$pks = ArrayHelper::toInteger ( $pks );
		$orders = ArrayHelper::toInteger ( $orders );

		try {
			$model->saveorder ( $pks, $orders );
			$this->sendResponse ( true );
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			$this->sendResponse ( $response, 500 );
		}
	}
}
