<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\Archive\Archive;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Component\ComponentHelper;

require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/helpers/IconHelper.php');

/**
 * Trait of Icons for upload, delete and all custom icons.
 *
 * @version 4.1.0
 */
trait JPageBuilderFrameworkIcons {
	/**
	 * Get all the published custom icons from the database.
	 *
	 * @return void
	 * @version 4.1.0
	 */
	private function getAllIcons($status) {
		$model = $this->getModel ( 'Icon' );

		$icons = $model->getAllIcons ( $status );

		$this->sendResponse ( $icons );
	}

	/**
	 * Icon API endpoint for CRUD operations.
	 *
	 * @return void
	 * @version 4.1.0
	 */
	public function icons() {
		$method = $this->getInputMethod ();
		$status = $this->getInput ( 'status', null, 'int' );
		$this->checkNotAllowedMethods ( [ 
				'PUT'
		], $method );

		switch ($method) {
			case 'GET' :
				$this->getAllIcons ( $status );
				break;
			case 'PATCH' :
				$this->changeCustomIconStatus ();
				break;
			case 'DELETE' :
				$this->deleteCustomIcon ();
				break;
		}
	}

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

	/**
	 * Delete custom icon by id.
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function deleteCustomIcon() {
		$model = $this->getModel ( 'Icon' );
		$id = $this->getInput ( 'id', 0 );

		$this->sendResponse ( $model->deleteCustomIcon ( $id ) );
	}

	/**
	 * Change custom icon's status.
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function changeCustomIconStatus() {
		$model = $this->getModel ( 'Icon' );
		$id = $this->getInput ( 'id', 0, 'int' );
		$status = $this->getInput ( 'status', null, 'int' );

		$this->sendResponse ( $model->changeCustomIconStatus ( $id, $status ) );
	}
}
