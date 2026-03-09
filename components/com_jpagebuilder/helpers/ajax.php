<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Http\Http;

$cParams = ComponentHelper::getParams ( 'com_jpagebuilder' );

$app = Factory::getApplication ();
$input = $app->getInput();
$http = new Http ();

// Load Page Template List
if ($action === 'pre-page-list') {

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
					'status' => true,
					'data' => [ 
							'pages' => array_values ( $pages ),
							'layouts' => $templates
					]
			];

			echo json_encode ( $response );
			die ();
		}
	}

	echo json_encode ( $output );
	die ();
}

if ($action === 'pre-section-list') {

	$list_files = JPATH_ROOT . '/administrator/components/com_jpagebuilder/widgets/list.json';

	$sections = [ ];
	$sectionsData = '';

	if (file_exists ( $list_files )) {
		$sectionsData = file_get_contents ( $list_files );
	}

	if (! empty ( $sectionsData )) {
		$sections = json_decode ( $sectionsData );

		/**
		 * Sanitize the blocks data before sending.
		 */
		if (! empty ( $sections->blocks )) {
			foreach ( $sections->blocks as $i => &$groups ) {
				if (! empty ( $groups->blocks )) {
					foreach ( $groups->blocks as $j => &$block ) {
						if (! empty ( $block->json )) {
							$content = json_decode ( $block->json );

							if (\is_object ( $content )) {
								$content = json_encode ( [ 
										$content
								] );
							} elseif (\is_array ( $content )) {
								$content = json_encode ( $content );
							}

							$json = JpagebuilderHelperSite::sanitize ( $content );
							// $parse = json_decode($json);

							// if (\is_array($parse) && !empty($parse))
							// {
							// $json = json_encode($parse[0]);
							// }

							$block->json = $json;
						}
					}

					unset ( $block );
				}
			}

			unset ( $groups );
		}

		if ((is_array ( $sections ) && count ( $sections )) || is_object ( $sections )) {
			$output ['status'] = true;
			$output ['data'] = $sections;
			echo json_encode ( $output );
			die ();
		}
	}

	echo json_encode ( $output );
	die ();
}

// Load page from uploaded page
if ($action === 'upload-page') {
	if (isset ( $_FILES ['page'] ) && $_FILES ['page'] ['error'] === 0) {
		$file_name = $_FILES ['page'] ['name'];
		$file_extension = substr ( $file_name, - 5 );
		$file_extension_lower = strtolower ( $file_extension );

		if ($file_extension_lower === '.json') {
			$content = file_get_contents ( $_FILES ['page'] ['tmp_name'] );

			$importingContent = ( object ) [ 
					'template' => '',
					'css' => '',
					'seo' => ''
			];

			if (! empty ( $content )) {
				$parsedContent = json_decode ( $content );

				if (! isset ( $parsedContent->template )) {
					$importingContent->template = json_decode ( $content );
				} else {
					$importingContent = $parsedContent;
				}
			}

			if (! empty ( $importingContent )) {
				require_once JPATH_COMPONENT_SITE . '/builder/classes/addon.php';
				$content = JpagebuilderApplicationHelper::sanitizePageText ( json_encode ( $importingContent->template ) );

				if ($content !== "[]") {
					$content = json_encode ( $content );
				}

				/**
				 * Sanitize the old data with new data format.
				 */
				$importingContent->template = JpagebuilderHelperSite::sanitizeImportJSON ( $content );

				echo json_encode ( array (
						'status' => true,
						'data' => $importingContent
				) );
				die ();
			}
		}
	}

	echo json_encode ( array (
			'status' => false,
			'data' => 'Something wrong there.'
	) );
	die ();
}