<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\Archive\Archive;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait for managing bulk export API endpoint.
 */
trait JPageBuilderFrameworkBulkExport {
	public function bulkExport() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'GET',
				'DELETE',
				'PUT',
				'PATCH'
		], $method );

		if ($method === 'POST') {
			$this->exportBulk ();
		}
	}

	/**
	 * Bulk export pages.
	 *
	 * @return void
	 * @since 5.2.10
	 */
	public function exportBulk() {
		/** @var CMSApplication */
		$app = Factory::getApplication ();
		$config = $app->getConfig ();
		$user = Factory::getApplication()->getIdentity();
		$authorised = $user->authorise ( 'core.edit', 'com_jpagebuilder' );
		$canEditOwn = $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );

		$pageIds = $this->getInput ( 'pageIds', '', 'string' );
		$isSeoChecked = $this->getInput ( 'isSeoChecked', '', 'string' );

		if (empty ( $pageIds )) {
			$this->sendResponse ( [ 
					'message' => 'Page Ids missing.'
			], 400 );
		}

		$pageIdsArray = explode ( ',', $pageIds );

		$pageContents = new stdClass ();
		$model = $this->getModel ( 'Editor' );

		foreach ( $pageIdsArray as $pageId ) {
			$trimmedPageId = trim ( $pageId );

			if (! $trimmedPageId) {
				continue;
			}

			if ($canEditOwn && ! empty ( $trimmedPageId )) {
				require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/models/page.php');

				$item_info = JpagebuilderModelPage::getPageInfoById ( $trimmedPageId );
				$canEditOwn = $item_info->created_by == $user->id;
			}

			if (! $authorised && ! $canEditOwn) {
				die ( 'Restricted Access' );
			}

			$content = $model->getPageContent ( $trimmedPageId );

			if (empty ( $content )) {
				$this->sendResponse ( [ 
						'message' => 'Requesting page not found!'
				], 404 );
			}

			$content = JpagebuilderApplicationHelper::preparePageData ( $content );

			$seoSettings = [ ];

			if ($isSeoChecked) {
				$seoSettings = [ 
						'og_description' => isset ( $content->og_description ) ? $content->og_description : '',
						'og_image' => '',
						'og_title' => isset ( $content->og_title ) ? $content->og_title : '',
						'meta_title' => isset ( $content->attribs ) && isset ( $content->attribs->meta_title ) ? $content->attribs->meta_title : '',
						'meta_description' => isset ( $content->attribs ) && isset ( $content->attribs->meta_description ) ? $content->attribs->meta_description : '',
						'meta_keywords' => isset ( $content->attribs ) && isset ( $content->attribs->meta_keywords ) ? $content->attribs->meta_keywords : '',
						'og_type' => isset ( $content->attribs ) && isset ( $content->attribs->og_type ) ? $content->attribs->og_type : '',
						'robots' => isset ( $content->attribs ) && isset ( $content->attribs->robots ) ? $content->attribs->robots : '',
						'seo_spacer' => isset ( $content->attribs ) && isset ( $content->attribs->seo_spacer ) ? $content->attribs->seo_spacer : ''
				];
			}

			$pageContent = ( object ) [ 
					'template' => isset ( $content->content ) ? $content->content : $content->text,
					'css' => isset ( $content->css ) ? $content->css : '',
					'seo' => json_encode ( $seoSettings ),
					'title' => $content->title,
					'language' => isset ( $content->language ) ? $content->language : '*'
			];

			$title = $content->title . '_' . $this->generateRandomId () . '.json';
			$pageContents->$title = $pageContent;
		}

		$archiveManager = new Archive();
		$archiver = $archiveManager->getAdapter ( 'zip' );
		
		$zipFileName = 'jpagebuilder-pages-' . $this->generateRandomId () . '.zip';
		$tmpPath = $config->get ( 'tmp_path' );
		$zipFilePath = $tmpPath . '/' . $zipFileName;
		$filesArray = [];
		
		foreach ( $pageContents as $fileName => $content ) {
			$stringContent = json_encode ( $content );
			
			$file = array('data' => $stringContent, 'name' => $fileName);
			
			// Assign chunk to container
			$filesArray[] = $file;
			
			
		}
		$archiver->create ( $zipFilePath, $filesArray );

		header ( "Pragma: public" );
		header ( "Expires: 0" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Content-Type: application/force-download" );
		header ( "Content-Type: application/octet-stream" );
		header ( "Content-Type: application/download" );
		header ( 'Content-Disposition: attachment; filename="' . basename ( $zipFilePath ) . '"' );
		header ( "Content-Type: application/zip" );
		header ( "Content-Transfer-Encoding: binary " );
		header ( 'Content-Length: ' . filesize ( $zipFilePath ) );

		echo file_get_contents ( $zipFilePath );
		unlink ( $zipFilePath );

		die ();

		$this->sendResponse ( [ 
				'message' => 'Failed to create zip file'
		], 500 );
	}
	function generateRandomId($length = 8) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$randomId = '';
		$maxIndex = strlen ( $characters ) - 1;
		for($i = 0; $i < $length; $i ++) {
			$randomId .= $characters [mt_rand ( 0, $maxIndex )];
		}

		return $randomId;
	}
}
