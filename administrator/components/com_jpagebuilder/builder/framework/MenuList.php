<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * API endpoints for menu list.
 */
trait JPageBuilderFrameworkMenuList {
	private function getMenus() {
		$model = $this->getModel ();

		$response = $model->getMenus ();

		$this->sendResponse ( $response->data, $response->code );
	}
	public function getMenuList() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'DELETE',
				'PATCH',
				'PUT'
		], $method );

		if ($method === 'GET') {
			$this->getMenus ();
		}
	}
}