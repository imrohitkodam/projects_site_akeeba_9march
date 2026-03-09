<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Http\Http;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait for managing page template list
 */
trait JPageBuilderFrameworkPageTemplate {
	public function pageTemplateList() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'DELETE',
				'PATCH',
				'PUT'
		], $method );

		switch ($method) {
			case 'GET' :
				$this->getPageTemplateList ();
				break;
		}
	}
	public function getPageTemplateList() {
		$list_files = JPATH_ROOT . '/administrator/components/com_jpagebuilder/templates/list.json';
		$templates = array (); // All pre-defined templates list
		$templatesData = '';

		$response = new stdClass ();

		if (is_file ( $list_files )) {
			$templatesData = file_get_contents ( $list_files );
		}

		if (! empty ( $templatesData )) {
			$templates = json_decode ( $templatesData );
			$pages = [ ];

			foreach ( $templates as $template ) {
				if (! empty ( $template->templates )) {
					foreach ( $template->templates as $item ) {
						if (! empty ( $item->layouts )) {
							foreach ( $item->layouts as $layout ) {
								$key = strtolower ( $layout->title );
								$pages [$key] = ( object ) [ 
										'label' => $layout->title,
										'value' => $key
								];
							}
						}
					}
				}
			}

			if (! empty ( $templates )) {
				$response = [ 
						'pages' => array_values ( $pages ),
						'layouts' => $templates
				];

				$this->sendResponse ( $response );
			}
		}

		$response ['message'] = 'No template found.';
		$this->sendResponse ( $response, 500 );
	}
}
