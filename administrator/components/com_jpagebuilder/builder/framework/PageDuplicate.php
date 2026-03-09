<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Defines the trait for a Editor Controller Class.
 *
 * @since 4.1.0
 */
trait JPageBuilderFrameworkPageDuplicate {
	private function duplicatePage() {
		$id = $this->getInput ( 'id', 0, 'int' );
		$model = $this->getModel ( 'Editor' );

		if (! $id) {
			$response ['message'] = Text::_ ( "COM_JPAGEBUILDER_PAGE_ID_MISSING" );
			$this->sendResponse ( $response, 400 );
		}

		$data = $model->duplicatePage ( $id );

		$this->sendResponse ( $data->response, $data->code );
	}

	/**
	 * Method to duplicate page item into the table.
	 *
	 * @param integer $id
	 *        	Key to the jpagebuilder table.
	 * @param Table $table
	 *        	Content table object being loaded.
	 *        	
	 * @return mixed return the response.
	 *        
	 * @since 4.1.0
	 */
	public function duplicate() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'PUT',
				'DELETE',
				'PATCH'
		], $method );

		$this->duplicatePage ();
	}
}
