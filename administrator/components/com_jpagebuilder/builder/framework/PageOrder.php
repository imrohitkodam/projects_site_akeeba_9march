<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

/**
 * No direct access.
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\Utilities\ArrayHelper;

/**
 * Trait for managing the page ordering.
 */
trait JPageBuilderFrameworkPageOrder {
	private function ordering() {
		$pks = $this->getInput ( 'ids', '', 'string' );
		$orders = $this->getInput ( 'orders', '', 'string' );

		if (empty ( $pks ) || empty ( $orders )) {
			$response ['message'] = 'Missing ids or orders.';
			$this->sendResponse ( $response, 400 );
		}

		$pks = ArrayHelper::toInteger ( explode ( ',', $pks ) );
		$orders = ArrayHelper::toInteger ( explode ( ',', $orders ) );

		$model = $this->getModel ( 'Editor' );

		try {
			$model->saveorder ( $pks, $orders );
			$this->sendResponse ( true );
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			$this->sendResponse ( $response, 500 );
		}
	}
	public function pageOrder() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'GET',
				'PUT',
				'POST',
				'DELETE'
		], $method );

		$this->ordering ();
	}
}
