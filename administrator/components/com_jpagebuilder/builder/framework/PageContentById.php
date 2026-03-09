<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

trait JPageBuilderFrameworkPageContentById {
	public function pageContentById() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'PUT',
				'DELETE',
				'PATCH'
		], $method );

		$this->getPageContentById ();
	}
	public function getPageContentById() {
		$id = $this->getInput ( 'id', null, 'int' );
		$model = $this->getModel ( 'Editor' );

		if (! $id) {
			$response ['message'] = 'Missing Page ID';
			$this->sendResponse ( $response, 400 );
		}

		$content = $model->getPageContent ( $id );

		if (empty ( $content )) {
			$this->sendResponse ( [ 
					'message' => 'Requesting page not found!'
			], 404 );
		}

		$content = JpagebuilderApplicationHelper::preparePageData ( $content );

		$routeType = null;

		$content->url = JpagebuilderHelperRoute::getFormRoute ( $content->id, $content->language, 0, $routeType );
		unset ( $content->content );

		$this->sendResponse ( $content );
	}
}
