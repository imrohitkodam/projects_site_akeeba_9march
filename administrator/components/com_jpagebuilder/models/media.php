<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\Folder;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;

/**
 * Media Model Class for managing media files.
 * 
 * @version 4.1.0
 */
class JpagebuilderModelMedia extends ListModel {
	/**
	 * Media __construct function
	 *
	 * @param mixed $config
	 */
	public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?FormFactoryInterface $formFactory = null) {
		parent::__construct ( $config );
		
		$app = Factory::getApplication();
		$dispatcher = $app->getDispatcher();
		$this->setDispatcher($dispatcher);
	}

	/**
	 * Get media items from database.
	 * 
	 * @return mixed
	 * @version 4.1.0
	 */
	public function getItems() {
		$input = Factory::getApplication ()->getInput();
		$type = $input->get ( 'type', '*', 'string' );
		$date = $input->get ( 'date', NULL, 'string' );
		$page = $input->get ( 'page', 1, 'int' );
		$search = $input->get ( 'search', NULL, 'string' );
		$limit = $input->get ( 'limit', 30, 'int' );

		$offset = ($page - 1) * $limit;

		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( [ 
				'id',
				'title',
				'path',
				'thumb',
				'media_attr',
				'type',
				'created_on',
				'created_by'
		] );
		$query->from ( $db->quoteName ( '#__jpagebuilder_media' ) );

		if ($search) {
			$search = preg_replace ( '#\xE3\x80\x80#s', " ", trim ( $search ) );
			$search_array = explode ( " ", $search );
			$query->where ( $db->quoteName ( 'title' ) . " LIKE '%" . implode ( "%' OR " . $db->quoteName ( 'title' ) . " LIKE '%", $search_array ) . "%'" );
		}

		if ($date) {
			$year_month = explode ( '-', $date );
			$query->where ( 'YEAR(created_on) = ' . $year_month [0] );
			$query->where ( 'MONTH(created_on) = ' . $year_month [1] );
		}

		if ($type !== '*') {
			$query->where ( $db->quoteName ( 'type' ) . " = " . $db->quote ( $type ) );
		}

		// Check User permission
		$user = Factory::getApplication()->getIdentity();

		if (! $user->authorise ( 'core.edit', 'com_jpagebuilder' )) {
			if ($user->authorise ( 'core.edit.own', 'com_jpagebuilder' )) {
				$query->where ( $db->quoteName ( 'created_by' ) . " = " . $db->quote ( $user->id ) );
			} else {
				return [ ];
			}
		}

		$query->order ( 'created_on DESC' );
		$query->setLimit ( $limit, $offset );
		$db->setQuery ( $query );
		$items = $db->loadObjectList ();

		foreach ( $items as &$item ) {
			$path = $item->path;
			$filename = basename ( $path );
			$item->ext = pathinfo($filename, PATHINFO_EXTENSION);
			$item->media_attr = json_decode ( $item->media_attr );
		}

		unset ( $item );

		return $items;
	}
	public function getDateFilters($date = '', $search = '') {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'DISTINCT YEAR( created_on ) AS year, MONTH( created_on ) AS month' );
		$query->from ( $db->quoteName ( '#__jpagebuilder_media' ) );

		if ($search) {
			$search = preg_replace ( '#\xE3\x80\x80#s', " ", trim ( $search ) );
			$search_array = explode ( " ", $search );
			$query->where ( $db->quoteName ( 'title' ) . " LIKE '%" . implode ( "%' OR " . $db->quoteName ( 'title' ) . " LIKE '%", $search_array ) . "%'" );
		}

		if ($date) {
			$date = explode ( '-', $date );
			$query->where ( 'YEAR(created_on) = ' . $date [0] );
			$query->where ( 'MONTH(created_on) = ' . $date [1] );
		}

		// Check User permission
		$user = Factory::getApplication()->getIdentity();
		if (! $user->authorise ( 'core.edit', 'com_jpagebuilder' )) {
			if ($user->authorise ( 'core.edit.own', 'com_jpagebuilder' )) {
				$query->where ( $db->quoteName ( 'created_by' ) . " = " . $db->quote ( $user->id ) );
			}
		}

		$query->order ( 'created_on DESC' );
		$db->setQuery ( $query );

		return $db->loadObjectList ();
	}
	public function getTotalMedia($date = '', $search = '') {
		$input = Factory::getApplication ()->getInput();
		$type = $input->get ( 'type', '*', 'string' );
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'COUNT(id)' );
		$query->from ( $db->quoteName ( '#__jpagebuilder_media' ) );

		if ($search) {
			$search = preg_replace ( '#\xE3\x80\x80#s', " ", trim ( $search ) );
			$search_array = explode ( " ", $search );
			$query->where ( $db->quoteName ( 'title' ) . " LIKE '%" . implode ( "%' OR " . $db->quoteName ( 'title' ) . " LIKE '%", $search_array ) . "%'" );
		}

		if ($date) {
			$date = explode ( '-', $date );
			$query->where ( 'YEAR(created_on) = ' . $date [0] );
			$query->where ( 'MONTH(created_on) = ' . $date [1] );
		}

		if ($type != '*') {
			$query->where ( $db->quoteName ( 'type' ) . " = " . $db->quote ( $type ) );
		}

		// Check User permission
		$user = Factory::getApplication()->getIdentity();
		if (! $user->authorise ( 'core.edit', 'com_jpagebuilder' )) {
			if ($user->authorise ( 'core.edit.own', 'com_jpagebuilder' )) {
				$query->where ( $db->quoteName ( 'created_by' ) . " = " . $db->quote ( $user->id ) );
			}
		}

		$db->setQuery ( $query );

		return $db->loadResult ();
	}

	public function getMediaCategories() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'type, COUNT(id) AS count' );
		$query->from ( $db->quoteName ( '#__jpagebuilder_media' ) );
		$query->group ( $db->quoteName ( 'type' ) );
		$query->order ( 'count DESC' );
		$db->setQuery ( $query );
		$items = $db->loadObjectList ();

		$categories = array ();
		$all = 0;

		if (! empty ( $items )) {
			foreach ( $items as $key => $item ) {
				$categories [$item->type] = $item->count;
				$all += $item->count;
			}
		}

		return array (
				'all' => $all
		) + $categories;
	}
	public function insertMedia($title, $path, $media_attr = '[]', $thumb = '', $type = 'image') {
		$description = '';
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$user = Factory::getApplication ()->getIdentity ();
		$columns = [ 
				'title',
				'path',
				'thumb',
				'type',
				'description',
				'media_attr',
				'alt',
				'extension',
				'created_on',
				'created_by',
				'modified_on',
				'modified_by'
		];
		$values = [ 
				$db->quote ( $title ),
				$db->quote ( $path ),
				$db->quote ( $thumb ),
				$db->quote ( $type ),
				$db->quote ( $description ),
				$db->quote ( $media_attr ),
				$db->quote ( $title ),
				$db->quote ( 'com_jpagebuilder' ),
				$db->quote ( Factory::getDate ( 'now' ) ),
				$user->id,
				$db->quote ( Factory::getDate ( 'now' ) ),
				$user->id
		];

		$query->insert ( $db->quoteName ( '#__jpagebuilder_media' ) )->columns ( $db->quoteName ( $columns ) )->values ( implode ( ',', $values ) );

		$db->setQuery ( $query );
		$db->execute ();
		$insertid = $db->insertid ();

		return $insertid;
	}
	public function getMediaByID($id) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'id',
				'title',
				'path',
				'thumb',
				'type',
				'media_attr',
				'created_by',
				'created_on'
		) ) );
		$query->from ( $db->quoteName ( '#__jpagebuilder_media' ) );
		$query->where ( $db->quoteName ( 'id' ) . ' = ' . $db->quote ( $id ) );
		$db->setQuery ( $query );

		return $db->loadObject ();
	}

	public function removeMediaByID($id) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$conditions = array (
				$db->quoteName ( 'id' ) . ' = ' . $db->quote ( $id )
		);
		$query->delete ( $db->quoteName ( '#__jpagebuilder_media' ) );
		$query->where ( $conditions );
		$db->setQuery ( $query );

		try {
			$db->execute ();
		} catch ( Exception $e ) {
			return false;
		}

		return true;
	}

	public function removeMediaByPath($path) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$conditions = array (
				$db->quoteName ( 'path' ) . ' LIKE  ' . $db->quote ( '%' . $path . '%' )
		);
		$query->delete ( $db->quoteName ( '#__jpagebuilder_media' ) );
		$query->where ( $conditions );
		$db->setQuery ( $query );
		$db->execute ();
		return true;
	}

	public function editMediaPathById($path, $newPath) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$field = array (
				$db->qn ( 'path' ) . '=REPLACE(' . $db->qn ( 'path' ) . ',' . $db->quote ( $path ) . ',' . $db->quote ( $newPath ) . ')',
				$db->qn ( 'thumb' ) . '=REPLACE(' . $db->qn ( 'thumb' ) . ',' . $db->quote ( $path ) . ',' . $db->quote ( $newPath ) . ')',
				$db->quoteName ( 'modified_on' ) . ' = ' . Factory::getDate ()->toSql (),
				$db->quoteName ( 'modified_by' ) . ' = ' . Factory::getApplication ()->getIdentity ()->id
		);

		$query->update ( $db->quoteName ( '#__jpagebuilder_media' ) );
		$query->set ( $field );
		$db->setQuery ( $query );
		$db->execute ();
		return true;
	}

	// Browse Folders
	public function getFolders() {
		$output = array ();
		$mediaParams = ComponentHelper::getParams ( 'com_media' );
		$file_path = rtrim ( ltrim ( $mediaParams->get ( 'file_path', 'files' ), '/' ), '/' );
		$image_path = rtrim ( ltrim ( $mediaParams->get ( 'image_path', 'images' ), '/' ), '/' );
		$input = Factory::getApplication ()->getInput();
		$path = $input->get ( 'path', '/' . $file_path, 'raw' );
		$rawPath = Path::clean ( $path );
		$path = Path::clean ( JPATH_ROOT . '/' . $path );

		if (! JpagebuilderSecurityHelper::isGetablePath ( $rawPath )) {
			$output ['status'] = false;
			$output ['message'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_FOLDER_NOT_FOUND' );
			return $output;
		}

		try {
			$directory = BuilderMediaHelper::checkForMediaActionBoundary ( $path );
		} catch ( \Exception $e ) {
			$output ['status'] = false;
			$output ['message'] = $e->getMessage ();
			return $output;
		}

		if (! file_exists ( $directory )) {
			$output ['status'] = false;
			$output ['message'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_FOLDER_NOT_FOUND' );
			return $output;
		}

		function getFolderPathIfFile($path) {
			if (is_file($path) || pathinfo($path, PATHINFO_EXTENSION)) {
				return rtrim(dirname($path), '/') . '/';
			}
			return $path; // Return the original path if it's already a folder
		}
		
		$directory = getFolderPathIfFile($directory);

		$items = Folder::files ( $directory, '.png|.jpg|.jpeg|.gif|.svg|.pdf|.webp|.woff2', false, true );
		$folders_list = Folder::folders ( $directory, '.', false, false, array (
				'.svn',
				'CVS',
				'.DS_Store',
				'__MACOSX',
				'_jpmedia_thumbs'
		) );
		$folders = self::listFolderTree ( JPATH_ROOT . '/' . $file_path, '.' );

		// If file_path != image_path ensure that also the image_path is loaded in the root folders dropdown
		if($file_path != $image_path) {
			// First path, index folder names
			$id = ++ $GLOBALS ['_JFolder_folder_tree_index'];
			$fullName = Path::clean ( JPATH_ROOT . '/' . $image_path );
			$fallbackDir [] = array (
					'id' => $id,
					'parent' => '',
					'name' => $image_path,
					'fullname' => $fullName,
					'relname' => str_replace ( '\\', '/', str_replace ( JPATH_ROOT, '', $fullName ) )
			);
			$foldersImages = self::listFolderTree ( JPATH_ROOT . '/' . $image_path, '.' );
			$folders = array_merge($folders, $fallbackDir, $foldersImages);
		}

		$crumbs = explode ( DIRECTORY_SEPARATOR, rtrim ( ltrim ( $rawPath, DIRECTORY_SEPARATOR ), DIRECTORY_SEPARATOR ) );
		$count = count ( $crumbs );

		$breadcrumbs = [ ];

		foreach ( $crumbs as $key => $crumb ) {
			$breadcrumbs [$key] ['label'] = $crumb;
			$breadcrumbs [$key] ['path'] = $key > 0 ? dirname ( $rawPath, $count - $key ) . '/' . $crumb : '/' . $file_path;
		}

		$output ['status'] = true;
		$output ['items'] = $items;
		$output ['folders_list'] = $folders_list;
		$output ['folders'] = $folders;
		$output ['breadcrumbs'] = $breadcrumbs;

		return $output;
	}

	public static function listFolderTree($path, $filter, $maxLevel = 10, $level = 0, $parent = 0) {
		$dirs = array ();

		if ($level == 0) {
			$GLOBALS ['_JFolder_folder_tree_index'] = 0;
		}

		if ($level < $maxLevel) {
			$folders = Folder::folders ( $path, $filter, false, false, array (
					'.svn',
					'CVS',
					'.DS_Store',
					'__MACOSX',
					'_jpmedia_thumbs'
			) );

			// First path, index folder names
			foreach ( $folders as $name ) {
				$id = ++ $GLOBALS ['_JFolder_folder_tree_index'];
				$fullName = Path::clean ( $path . '/' . $name );
				$dirs [] = array (
						'id' => $id,
						'parent' => $parent,
						'name' => $name,
						'fullname' => $fullName,
						'relname' => str_replace ( '\\', '/', str_replace ( JPATH_ROOT, '', $fullName ) )
				);
				$dirs2 = self::listFolderTree ( $fullName, $filter, $maxLevel, $level + 1, $id );
				$dirs = array_merge ( $dirs, $dirs2 );
			}
		}

		return $dirs;
	}

	/**
	 * Return Image Information using image path
	 *
	 * @param
	 *        	string Image Path $path
	 * @return void
	 */
	public function getMediaByPath($path) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'id',
				'title',
				'path',
				'thumb'
		) ) );
		$query->from ( $db->quoteName ( '#__jpagebuilder_media' ) );
		$query->where ( $db->quoteName ( 'path' ) . ' LIKE  ' . $db->quote ( '%' . $path . '%' ) );
		$db->setQuery ( $query );

		return $db->loadObject ();
	}
}
