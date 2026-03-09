<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Layout Import Trait
 */
trait JPageBuilderFrameworkImport {
	private function importLayout() {
		$file = Factory::getApplication ()->getInput()->files->get ( 'page' );

		if (isset ( $file ) && $file ['error'] === 0) {
			$fileName = $file ['name'];
			$fileExtension = substr ( $fileName, - 5 );
			$fileExtensionLower = strtolower ( $fileExtension );

			if ($fileExtensionLower === '.json') {
				$content = file_get_contents ( $file ['tmp_name'] );
				$importingContent = ( object ) [ 
						'template' => '',
						'css' => '',
						'seo' => ''
				];

				if (! empty ( $content )) {
					// Support migration from sp builder prefixes
					$content = StringHelper::str_ireplace(['sppb','sp_'], ['jpb', 'jp_'], $content);
					
					$parsedContent = json_decode ( $content );

					if (! isset ( $parsedContent->template )) {
						$importingContent->template = $content;
					} else {
						$importingContent = $parsedContent;
					}
				}

				if (! empty ( $importingContent )) {
					require_once JPATH_COMPONENT_SITE . '/builder/classes/addon.php';
					require_once JPATH_COMPONENT_SITE . '/helpers/helper.php';

					$templateContent = ! is_string ( $importingContent->template ) ? json_encode ( $importingContent->template ) : $importingContent->template;
					$content = JpagebuilderApplicationHelper::sanitizePageText ( $templateContent );
					$content = json_encode ( $content );

					/**
					 * Sanitize the old data with new data format.
					 */
					$importingContent->template = JpagebuilderHelperSite::sanitizeImportJSON ( $content );

					$this->sendResponse ( $importingContent, 200 );
				}
			}
		}

		$response ['message'] = 'Something wrong there.';
		$this->sendResponse ( $response, 500 );
	}
	public function importJson() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'PUT',
				'DELETE',
				'PATCH'
		], $method );

		switch ($method) {
			case 'POST' :
				$this->importLayout ();
				break;
		}
	}
}
