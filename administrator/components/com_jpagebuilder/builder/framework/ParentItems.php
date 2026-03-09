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
 * Trait for managing menu parent items API endpoint.
 */
trait JPageBuilderFrameworkParentItems {
	public function getParentItems() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'DELETE',
				'PUT',
				'PATCH'
		], $method );

		if ($method === 'GET') {
			$this->getItems ();
		}
	}

	/**
	 * Get parent Items
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function getItems() {
		$model = $this->getModel ( 'Editor' );

		$menuType = $this->getInput ( 'menutype', 'mainmenu', 'string' );
		$id = $this->getInput ( 'id', 0, 'int' );

		$response = $model->getParentItems ( $menuType, $id );

		$this->sendResponse ( $response->data, $response->code );
	}
}