<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Http\Http;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Layout Import Trait
 */
trait JPageBuilderFrameworkLayoutImport {
	private function uploadLayout() {
		$params = ComponentHelper::getParams ( 'com_jpagebuilder' );

		$id = $this->getInput ( 'id', 0, 'int' );

		if (empty ( $id )) {
			$response ['message'] = 'Invalid layout ID given!';
			$this->sendResponse ( $response, 500 );
		}

		// Load the template file
		$templateJsonFile = file_get_contents ( JPATH_ROOT . '/administrator/components/com_jpagebuilder/templates/list.json' );
		if (! $templateJsonFile) {
			$response ['message'] = 'Invalid layout file!';
			$this->sendResponse ( $response, 500 );
		}

		$templateName = '';
		$layoutMapping = [ ];
		$templateLayoutMapping = [ ];
		$templatesObject = json_decode ( $templateJsonFile, true );
		if (is_array ( $templatesObject )) {
			foreach ( $templatesObject as $templateCategory ) {
				foreach ( $templateCategory ['templates'] as $template ) {
					$templateName = StringHelper::str_ireplace ( ' ', '', $template ['title'] );
					foreach ( $template ['layouts'] as $layout ) {
						$templateLayoutMapping [$layout ['id']] = $templateName;
						$layoutMapping [$layout ['id']] = $layout ['page'] ?? '';
					}
				}
			}
		}

		if (! array_key_exists ( $id, $layoutMapping )) {
			$response ['message'] = 'Invalid layout file!';
			$this->sendResponse ( $response, 500 );
		}

		// Setup the layout template file mapped by unique ID
		$layoutMappedFile = JPATH_ROOT . '/administrator/components/com_jpagebuilder/templates/' . $templateLayoutMapping [$id] . '/' . $layoutMapping [$id];
		$pageData = file_get_contents ( $layoutMappedFile );

		if (! empty ( $pageData )) {
			$pageData = json_decode ( $pageData );
			$pageDataContent = $pageData->data;
			$content = ( object ) [ 
					'template' => '',
					'css' => ''
			];

			if (! isset ( $pageDataContent->template )) {
				$content->template = json_encode ( $pageDataContent );
			} else {
				$pageDataContent->template = ! \is_string ( $pageDataContent->template ) ? json_encode ( $pageDataContent->template ) : $pageDataContent->template;

				$content = $pageDataContent;
			}

			require_once JPATH_COMPONENT_SITE . '/helpers/helper.php';
			$content->template = JpagebuilderApplicationHelper::sanitizePageText ( $content->template );

			$this->sendResponse ( ($content), 200 );
		}
	}
	public function import() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'PUT',
				'DELETE',
				'PATCH'
		], $method );

		switch ($method) {
			case 'GET' :
				$this->uploadLayout ();
				break;
		}
	}
}
