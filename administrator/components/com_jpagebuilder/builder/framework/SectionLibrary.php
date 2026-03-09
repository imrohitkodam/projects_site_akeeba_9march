<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Http\Http;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Section Library API trait
 */
trait JPageBuilderFrameworkSectionLibrary {
	public function sectionLibrary() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'PUT',
				'DELETE',
				'PATCH'
		], $method );

		if ($method === 'GET') {
			$this->getSectionLibrary ();
		}
	}
	private function getSectionLibrary() {
		$cParams = ComponentHelper::getParams ( 'com_jpagebuilder' );

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
								$block->json = $json;
							}
						}

						unset ( $block );
					}
				}

				unset ( $groups );
			}

			if ((is_array ( $sections ) && count ( $sections )) || is_object ( $sections )) {
				$this->sendResponse ( $sections );
			}
		}

		$response ['message'] = 'Sections not found!';
		$this->sendResponse ( $response, 500 );
	}
}
