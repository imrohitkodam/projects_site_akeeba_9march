<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\Archive\Archive;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait for managing bulk import API endpoint.
 */
trait JPageBuilderFrameworkBulkImport {
	public function bulkImport() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'GET',
				'DELETE',
				'PUT',
				'PATCH'
		], $method );

		if ($method === 'POST') {
			$this->importBulk ();
		}
	}

	/**
	 * Bulk import pages.
	 *
	 * @return void
	 * @since 5.2.10
	 */
	public function importBulk() {
		/** @var CMSApplication */
		$app = Factory::getApplication ();
		$config = $app->getConfig ();

		$pagesZip = $this->getFilesInput ( 'pagesZip', null );
		$tmpPath = $config->get ( 'tmp_path' );

		$zipPath = $tmpPath . '/pagesImport.zip';
		$extractPath = $tmpPath . '/unpack-pages';

		if (empty ( $pagesZip )) {
			$this->sendResponse ( [ 
					'status' => false,
					'message' => 'Pages Zip file is required.'
			], 400 );
		}

		$user = Factory::getApplication()->getIdentity();
		$canCreate = $user->authorise ( 'core.create', 'com_jpagebuilder' );

		if (! $canCreate) {
			$this->sendResponse ( [ 
					'message' => Text::_ ( 'COM_JPAGEBUILDER_EDITOR_INVALID_CREATE_ACCESS' )
			], 403 );
		}

		if (file_exists ( $zipPath )) {
			File::delete ( $zipPath );
		}

		if (is_dir ( $extractPath )) {
			Folder::delete ( $extractPath );
		}

		if (File::upload ( $pagesZip ['tmp_name'], $zipPath )) {
			try {
				$archive = new Archive ( [ 
						'tmp_path' => $tmpPath
				] );
				$extractFile = $archive->extract ( $zipPath, $extractPath );

				if (! $extractFile) {
					$this->sendResponse ( [ 
							'status' => false,
							'message' => 'File extract failed.'
					], 500 );
				}

				$extractedFiles = ( array ) Folder::files ( $extractPath, '' );

				// check if extracted files are json and data format is correct
				$pattern = '/\.json$/i';
				foreach ( $extractedFiles as $pageJson ) {
					if (! preg_match ( $pattern, $pageJson )) {
						$this->sendResponse ( [ 
								'status' => false,
								'message' => 'File format is not supported.'
						], 400 );
					}

					$pageJsonFullPath = $extractPath . '/' . $pageJson;
					$pageData = json_decode ( file_get_contents ( $pageJsonFullPath ) );

					if (! isset ( $pageData->template ) || ! isset ( $pageData->css )) {
						$this->sendResponse ( [ 
								'status' => false,
								'message' => 'File is corrupted.'
						], 400 );
					}
				}

				$isAllPagesImported = true;

				foreach ( $extractedFiles as $pageJson ) {
					$pageJsonFullPath = $extractPath . '/' . $pageJson;
					$pageData = json_decode ( file_get_contents ( $pageJsonFullPath ) );

					$isSuccess = $this->createSinglePage ( $pageData );

					$isAllPagesImported = $isAllPagesImported && $isSuccess;
				}

				if (! $isAllPagesImported) {
					$this->sendResponse ( [ 
							'status' => true,
							'message' => 'Some of the pages not imported.'
					], 200 );
				}
			} catch ( \Exception $error ) {
				$this->sendResponse ( [ 
						'status' => false,
						'message' => $error->getMessage ()
				], 500 );
			}

			$this->sendResponse ( [ 
					'status' => true,
					'message' => 'Pages imported successfully.'
			], 200 );
		}

		$this->sendResponse ( [ 
				'status' => false,
				'message' => 'Pages import failed.'
		], 500 );
	}
	public function createSinglePage($pageData) {
		$model = $this->getModel ( 'Editor' );
		$user = Factory::getApplication()->getIdentity();
		$version = JpagebuilderHelper::getVersion ();

		$extension = 'com_jpagebuilder';
		$extensionView = 'page';
		$data = [ ];

		$title = ! empty ( $pageData->title ) ? $pageData->title . uniqid ( '-imported-' ) : uniqid ( 'Imported-' );
		$language = ! empty ( $pageData->language ) ? $pageData->language : '*';
		$css = ! empty ( $pageData->css ) ? $pageData->css : '';
		$pageContent = ! empty ( $pageData->template ) ? $pageData->template : '[]';
		$seoData = ! empty ( $pageData->seo ) ? $pageData->seo : null;

		$data = [ 
				'id' => 0,
				'title' => $title,
				'text' => '[]',
				'content' => json_encode ( $pageContent ),
				'css' => $css,
				'catid' => 0,
				'language' => $language,
				'access' => 1,
				'published' => 1,
				'extension' => $extension,
				'extension_view' => $extensionView,
				'created_on' => Factory::getDate ()->toSql (),
				'created_by' => $user->id,
				'modified' => Factory::getDate ()->toSql (),
				'version' => $version
		];

		if ($seoData) {
			$data ['attribs'] = $seoData;
		}

		$result = $model->createPage ( $data );

		if (! empty ( $result ['message'] )) {
			return false;
		}

		return true;
	}
}
