<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\Archive\Archive;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Uri\Uri;

require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/assetcssparser.php';
/**
 * Asset Controller class
 *
 * @since 4.0.0
 */
class JpagebuilderControllerAsset extends FormController {
	/**
	 * Send JSON Response to the client.
	 *
	 * @param array $response
	 *        	The response array or data.
	 * @param int $statusCode
	 *        	The status code of the HTTP response.
	 *        	
	 * @return void
	 * @since 4.0.0
	 */
	private function sendResponse($response, int $statusCode = 200): void {
		$app = Factory::getApplication ();
		$app->setHeader ( 'status', $statusCode, true );
		$app->sendHeaders ();
		echo new JsonResponse ( $response );
		$app->close ();
	}
	/**
	 * Load custom icons.
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function loadCustomIcons() {
		$app = Factory::getApplication ( 'site' );
		$input = $app->getInput();

		$model = $this->getModel ();
		$response = [ 
				'status' => true,
				'data' => $model->loadCustomIcons ()
		];

		$this->sendResponse ( $response, 200 );
	}

	/**
	 * Delete custom icon by id.
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function deleteCustomIcon() {
		$app = Factory::getApplication ();
		$input = $app->getInput();
		$model = $this->getModel ();

		$id = $input->getInt ( 'id', 0 );

		$response = [ 
				'status' => true,
				'data' => $model->deleteCustomIcon ( $id )
		];

		$this->sendResponse ( $response, 200 );
	}

	/**
	 * Change custom icon's status.
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function changeCustomIconStatus() {
		$app = Factory::getApplication ();
		$input = $app->getInput();
		$model = $this->getModel ();

		$id = $input->getInt ( 'id', 0 );
		$status = $input->getInt ( 'status', null );

		$response = [ 
				'status' => true,
				'data' => $model->changeCustomIconStatus ( $id, $status )
		];

		$this->sendResponse ( $response, 200 );
	}
	public function loadIcons() {
		$input = Factory::getApplication ()->getInput();
		$name = $input->json->get ( 'name', NULL, 'string' );
		$title = $input->json->get ( 'title', NULL, 'string' );

		$rootPath = Uri::base ( true ) . '/media/com_jpagebuilder/assets/iconfont/';
		$report = array ();

		$css = $rootPath . $name . '/' . $name . '.css';

		$model = $this->getModel ();
		$assets = $model->getIconList ( $name );
		$report ['iconList'] = $assets;
		$report ['css'] = $css;

		echo json_encode ( $report );
		die ();
	}

	/**
	 * Unpacks a file and verifies it as a icofont package
	 * Supports .gz .tar .tar.gz and .zip
	 *
	 * @param string $fontPackage
	 *        	The uploaded icon font package file
	 * @param string $name
	 *        	File name.
	 * @return boolean boolean false on failure
	 *        
	 * @since 4.0.0
	 */
	public static function unpack($packageFilename) {
		// Path to the archive
		$archivename = $packageFilename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid ( 'builderCustomIcon_' );

		// Clean the paths to use for archive extraction
		$extractdir = Path::clean ( dirname ( $packageFilename ) . '/' . $tmpdir );
		$archivename = Path::clean ( $archivename );

		// Do the unpacking of the archive
		try {
			$archive = new Archive ( array (
					'tmp_path' => Factory::getApplication()->getConfig()->get ( 'tmp_path' )
			) );
			$extract = $archive->extract ( $archivename, $extractdir );
		} catch ( \Exception $e ) {
			return false;
		}

		if (! $extract) {
			return false;
		}

		/*
		 * Let's set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$retval ['extractdir'] = $extractdir;
		$retval ['packagefile'] = $archivename;

		/*
		 * Try to find the correct install directory. In case the package is inside a
		 * subdirectory detect this and set the install directory to the correct path.
		 *
		 * List all the items in the installation directory. If there is only one, and
		 * it is a folder, then we will set that folder to be the installation folder.
		 */
		$dirList = array_merge ( ( array ) Folder::files ( $extractdir, '' ), ( array ) Folder::folders ( $extractdir, '' ) );

		if (count ( $dirList ) === 1) {
			if (is_dir ( $extractdir . '/' . $dirList [0] )) {
				$extractdir = Path::clean ( $extractdir . '/' . $dirList [0] );
			}
		}

		/*
		 * We have found the install directory so lets set it and then move on
		 * to detecting the extension type.
		 */
		$retval ['dir'] = $extractdir;

		return $retval;
	}
}
