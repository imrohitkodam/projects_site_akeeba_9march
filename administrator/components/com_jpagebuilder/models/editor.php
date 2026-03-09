<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;

if (! \class_exists ( 'JpagebuilderEditorUtils' )) {
	require_once __DIR__ . './../builder/helpers/EditorUtils.php';
}
require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/tables/editor.php');

class JpagebuilderModelEditor extends AdminModel {
	public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?FormFactoryInterface $formFactory = null) {
		parent::__construct ( $config );
		
		$app = Factory::getApplication();
		$dispatcher = $app->getDispatcher();
		$this->setDispatcher($dispatcher);
	}

	/**
	 * Method for getting a form.
	 *
	 * @param array $data
	 *        	Data for the form.
	 * @param bool $loadData
	 *        	True if the form is to load its own data (default case), false if not.
	 * @return void
	 */
	public function getForm($data = array (), $loadData = true) {
		$form = $this->loadForm ( 'com_jpagebuilder.page', 'page', array (
				'control' => 'jform',
				'load_data' => $loadData
		) );

		if (empty ( $form )) {
			return false;
		}

		$jinput = Factory::getApplication ()->getInput();

		$id = $jinput->get ( 'id', 0 );

		// Determine correct permissions to check.
		if ($this->getState ( 'page.id' )) {
			$id = $this->getState ( 'page.id' );

			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute ( 'catid', 'action', 'core.edit' );

			// Existing record. Can only edit own pages in selected categories.
			$form->setFieldAttribute ( 'catid', 'action', 'core.edit.own' );
		} else {
			// New record. Can only create in selected categories.
			$form->setFieldAttribute ( 'catid', 'action', 'core.create' );
		}

		$user = Factory::getApplication()->getIdentity();

		// Modify the form based on Edit State access controls.
		if ($id != 0 && (! $user->authorise ( 'core.edit.state', 'com_jpagebuilder.page.' . ( int ) $id )) || ($id == 0 && ! $user->authorise ( 'core.edit.state', 'com_jpagebuilder' ))) {
			// Disable fields for display.
			$form->setFieldAttribute ( 'published', 'disabled', 'true' );

			// Disable fields while saving.
			// The controller has already verified this is an page you can edit.
			$form->setFieldAttribute ( 'published', 'filter', 'unset' );
		}

		return $form;
	}
	public function getItem($pk = NULL) {
		if ($item = parent::getItem ( $pk )) {
			$item = parent::getItem ( $pk );

			// Get item language code
			$lang_code = (isset ( $item->language ) && $item->language && explode ( '-', $item->language ) [0]) ? explode ( '-', $item->language ) [0] : '';

			// Preview URL
			$item->link = 'index.php?option=com_jpagebuilder&task=page.edit&id=' . $item->id;

			$item->preview = JpagebuilderHelperRoute::getPageRoute ( $item->id, $lang_code );
			$item->frontend_edit = JpagebuilderHelperRoute::getFormRoute ( $item->id, $lang_code );
		}

		return $item;
	}
	protected function loadFormData() {
		/** @var CMSApplication */
		$app = Factory::getApplication ();
		$data = $app->getUserState ( 'com_jpagebuilder.edit.page.data', array () );

		if (empty ( $data )) {
			$data = $this->getItem ();
		}

		$this->preprocessData ( 'com_jpagebuilder.page', $data );

		return $data;
	}
	protected function canEditState($item) {
		return Factory::getApplication ()->getIdentity ()->authorise ( 'core.edit.state', 'com_jpagebuilder.page.' . $item->id );
	}
	public function getTable($name = 'Editor', $prefix = 'JpagebuilderTable', $options = array ()) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$editorTable = new JpagebuilderTableEditor($db);
		return $editorTable;
	}
	public function sortBy(string $param) {
		$firstCharacter = substr ( $param, 0, 1 );
		$orderDirection = "ASC";

		if ($firstCharacter === '-') {
			$param = substr ( $param, 1 );
			$orderDirection = "DESC";
		}

		$ordering = new stdClass ();
		$ordering->field = $param;
		$ordering->direction = $orderDirection;

		return $ordering;
	}
	public function getPages($pageData) {
		$cParams = ComponentHelper::getParams('com_jpagebuilder');
		
		$search = $pageData->search;
		$offset = $pageData->offset;
		$limit = $pageData->limit;
		$sortBy = $pageData->sortBy;
		$category = $pageData->category;
		$language = $pageData->language;
		$status = $pageData->status;
		$extension = $pageData->extension;
		$extensionView = $pageData->extension_view;

		$response = new stdClass ();

		try {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

			$query->select ( 'p.id, p.asset_id, p.title, p.text, p.content, p.extension_view, p.catid, p.published, p.created_on, p.created_by, p.language, p.hits, p.checked_out, p.css, p.attribs, p.og_title, p.og_description, p.og_image' );
			$query->select ( 'c.title as category' );

			$query->from ( $db->quoteName ( '#__jpagebuilder', 'p' ) );
			
			if(!$cParams->get('integrationarticle', 0)) {
				$query->where ( $db->quoteName ( 'p.extension' ) . ' = ' . $db->quote ( $extension ) );
				$query->where ( $db->quoteName ( 'p.extension_view' ) . ' = ' . $db->quote ( $extensionView ) );
			}

			$query->select ( 'l.title AS language_title' )->join ( 'LEFT', $db->quoteName ( '#__languages', 'l' ) . ' ON l.lang_code = p.language' );

			$query->join ( 'LEFT', $db->quoteName ( '#__categories', 'c' ) . ' ON (' . $db->quoteName ( 'p.catid' ) . ' = ' . $db->quoteName ( 'c.id' ) . ')' );

			$query->select ( 'ug.title AS access_title' )->join ( 'LEFT', $db->quoteName ( '#__viewlevels', 'ug' ) . ' ON (' . $db->quoteName ( 'ug.id' ) . ' = ' . $db->quoteName ( 'p.access' ) . ')' );

			if (is_numeric ( $status )) {
				$query->where ( $db->quoteName ( 'p.published' ) . ' = ' . $db->quote ( $status ) );
			} else {
				$query->where ( $db->quoteName ( 'p.published' ) . ' IN (0, 1)' );
			}

			if (! empty ( $access )) {
				$query->where ( $db->quoteName ( 'p.access' ) . ' = ' . $db->quote ( $access ) );
			}

			if (! empty ( $category )) {
				$query->where ( $db->quoteName ( 'p.catid' ) . ' = ' . $db->quote ( $category ) );
			}

			if (! empty ( $language )) {
				$query->where ( $db->quoteName ( 'p.language' ) . ' = ' . $db->quote ( $language ) );
			}

			$search = trim ( $search );
			if (! empty ( $search )) {
				$search = preg_replace ( "@\s+@", ' ', $search );
				$search = explode ( ' ', $search );
				$search = array_filter ( $search, function ($word) {
					return ! empty ( $word );
				} );
				$search = array_map ( function ($item) {
					return preg_quote ( $item, '/' );
				}, $search );
				$search = implode ( '|', $search );
				$query->where ( $db->quoteName ( 'p.title' ) . ' REGEXP ' . $db->quote ( $search ) );
			}

			if (! empty ( $sortBy )) {
				$ordering = $this->sortBy ( $sortBy );
				$query->order ( $db->quoteName ( 'p.' . $ordering->field ) . ' ' . $ordering->direction );
			}

			$fullQuery = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$fullQuery = $query->__toString ();

			if (! empty ( $limit )) {
				$query->setLimit ( $limit, $offset );
			}

			$db->setQuery ( $query );

			try {
				$results = $db->loadObjectList ();
			} catch ( \Exception $e ) {
				$results = [ ];
			}

			if (! empty ( $results )) {
				if (! class_exists ( 'JpagebuilderHelperRoute' )) {
					require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/route.php';
				}

				foreach ( $results as &$result ) {
					if ($result->created_on) {
						$result->created = (new DateTime ( $result->created_on ))->format ( 'j F, Y' );
						unset ( $result->created_on );
					}

					if (! empty ( $result->created_by )) {
						$result->author = Factory::getContainer()->get('user.factory')->loadUserById($result->created_by)->name;
					}

					if (empty ( $result->category )) {
						$result->category = '-';
					}

					if (! empty ( $result->content )) {
						$result->text = $result->content;
						unset ( $result->content );
					}

					if (! empty ( $result->attribs )) {
						$result->attribs = json_decode ( $result->attribs );
					}

					if (! empty ( $result->og_image )) {
						$result->og_image = json_decode ( $result->og_image );
					}

					$result->url = JpagebuilderHelperRoute::getFormRoute ( $result->id, $result->language, 0, null, $extensionView === 'popup' );
					$result->preview = JpagebuilderHelperRoute::getPageRoute ( $result->id, $result->language, null, $extensionView === 'popup' );
				}

				unset ( $result );
			}

			$db->setQuery ( $fullQuery );
			$db->execute ();
			$allItems = $db->getNumRows ();

			$response->totalItems = $allItems;
			$response->totalPages = ceil ( $allItems / $limit );
			$response->results = JpagebuilderEditorUtils::parsePageListData ( $results );
			$response->code = 200;

			return $response;
		} catch ( Exception $e ) {
			$response->totalItems = 0;
			$response->totalPages = 0;
			$response->results = $e->getMessage ();
			$response->code = 500;

			return $response;
		}
	}

	/**
	 * Count total number of pages.
	 *
	 * @param string $keyword
	 *        	The search keyword.
	 *        	
	 * @return int
	 * @since 4.0.0
	 */
	public function countTotalPages($keyword = ''): int {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( 'COUNT(*)' )
			  ->from ( $db->quoteName ( '#__jpagebuilder' ) )
			  ->where ( $db->quoteName ( 'extension' ) . ' = ' . $db->quote ( 'com_jpagebuilder' ) );

		if (! empty ( $keyword )) {
			$query->where ( $db->quoteName ( 'title' ) . ' REGEXP ' . $db->quote ( $keyword ) );
		}

		$db->setQuery ( $query );

		try {
			return $db->loadResult ();
		} catch ( Exception $e ) {
			return 0;
		}
	}
	public function getPageContent(int $id) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( [ 
				'id',
				'asset_id',
				'title',
				'text',
				'content',
				'extension',
				'extension_view',
				'view_id',
				'created_by',
				'published',
				'catid',
				'access',
				'created_on',
				'attribs',
				'og_title',
				'og_description',
				'og_image',
				'language',
				'css',
				'version'
		] )->from ( $db->quoteName ( '#__jpagebuilder' ) )
		   ->where ( $db->quoteName ( 'id' ) . ' = ' . $id );

		$db->setQuery ( $query );
		$result = null;

		try {
			$result = $db->loadObject ();
		} catch ( \Exception $e ) {
			return $result;
		}

		$defaultAttributes = ( object ) [
				'meta_title' => '',
				'meta_description' => '',
				'meta_keywords' => '',
				'robots' => 'global'
		];

		$defaultImage = ( object ) [ 
				'src' => ''
		];

		if (! empty ( $result )) {
			$result->text = ! empty ( $result ) ? \json_decode ( $result->text ) : null;
			$result->attribs = \json_decode ( $result->attribs );
			
			if($result->og_image) {
				$result->og_image = \json_decode ( $result->og_image );
			}

			if (empty ( $result->attribs )) {
				$result->attribs = $defaultAttributes;
			}

			if (empty ( $result->og_image )) {
				$result->og_image = $defaultImage;
			}

			$result->url = JpagebuilderHelperRoute::getFormRoute ( $result->id, $result->language );
		}

		return $result;
	}
	public function getProductPageContent(string $extension, string $extensionView) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( [ 
				'id',
				'title',
				'text',
				'content',
				'extension',
				'extension_view',
				'view_id',
				'created_by',
				'catid',
				'access',
				'created_on',
				'attribs',
				'og_title',
				'og_description',
				'og_image',
				'language',
				'published',
				'css',
				'version'
		] )->from ( $db->quoteName ( '#__jpagebuilder' ) )
		   ->where ( $db->quoteName ( 'extension' ) . ' = ' . $db->quote ( $extension ) )
		   ->where ( $db->quoteName ( 'extension_view' ) . ' = ' . $db->quote ( $extensionView ) );

		$db->setQuery ( $query );
		$result = null;

		try {
			$result = $db->loadObject ();
		} catch ( \Exception $error ) {
			return $result;
		}

		$defaultAttributes = ( object ) [ 
				'meta_description' => '',
				'meta_keywords' => '',
				'robots' => 'global'
		];

		$defaultImage = ( object ) [ 
				'src' => ''
		];

		if (! empty ( $result )) {
			$result->text = ! empty ( $result ) ? \json_decode ( $result->text ) : null;
			$result->attribs = \json_decode ( $result->attribs );
			$result->og_image = \json_decode ( $result->og_image );

			if (empty ( $result->attribs )) {
				$result->attribs = $defaultAttributes;
			}

			if (empty ( $result->og_image )) {
				$result->og_image = $defaultImage;
			}

			$result->url = JpagebuilderHelperRoute::getFormRoute ( $result->id, $result->language );
		}

		return $result;
	}
	public function savePage(array $data) {
		$data ['css'] = $data ['css'] ?? '';

		try {
			if (! parent::save ( $data )) {
				throw new Exception ( 'Error while saving the page' );
			}
		} catch ( Throwable $error ) {
			throw $error;
		}
	}
	public function duplicatePage(int $id) {
		try {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select ( "*" );
			$query->from ( $db->quoteName ( '#__jpagebuilder' ) );
			$query->where ( $db->quoteName ( 'id' ) . '=' . $db->quote ( $id ) );
			$db->setQuery ( $query );

			$page = $db->loadObject ();

			if (! empty ( $page )) {
				$page->title = $this->generatePageNewTitle ( $page->title );
				$page->hits = 0;
				$page->id = '';
				$db->insertObject ( '#__jpagebuilder', $page, 'id' );
				$this->checkin ( $id );

				return ( object ) [ 
						'response' => [ 
								'status' => true,
								'id' => $page->id,
								'message' => Text::_ ( "COM_JPAGEBUILDER_SUCCESS_MSG_FOR_PAGE_DUPLICATED" )
						],
						'code' => 201
				];
			}
		} catch ( Exception $e ) {
			return ( object ) [ 
					'status' => false,
					'message' => $e->getMessage (),
					'code' => 500
			];
		}
	}

	/**
	 * Generate page title
	 *
	 * @param string $title
	 *        	current page title.
	 *        	
	 * @return string
	 */
	public function generatePageNewTitle($title) {
		$table = $this->getTable ();

		while ( $table->load ( [ 
				'title' => $title
		] ) ) {
			$title = StringHelper::increment ( $title );
		}

		return $title;
	}

	/**
	 * Get Menu List
	 *
	 * @return object The response
	 * @since 4.0.0
	 */
	public function getMenus(): object {
		$response = new stdClass ();

		try {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

			$query->select ( 'id, menutype, title' )->from ( $db->quoteName ( '#__menu_types' ) )->where ( $db->quoteName ( 'client_id' ) . ' = 0' );

			$db->setQuery ( $query );

			$result = $db->loadObjectList ();
			$data = [ ];

			if (! empty ( $result )) {
				foreach ( $result as $value ) {
					$data [] = ( object ) [ 
							'value' => $value->menutype,
							'label' => $value->title
					];
				}
			}

			$response->data = $data;
			$response->code = 200;

			return $response;
		} catch ( Exception $e ) {
			$response->message = $e->getMessage ();
			$response->code = 500;

			return $response;
		}
	}

	/**
	 * Get Menu List
	 *
	 * @param string $menuType
	 *        	The menu type
	 * @return object The response
	 * @since 4.0.0
	 */
	public function getParentItems(string $menuType, int $id = 0): object {
		$response = new stdClass ();

		try {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select ( 'DISTINCT(a.id) AS value, a.title AS text, a.level, a.lft' )
				  ->from ( $db->quoteName ( '#__menu', 'a' ) )
				  ->where ( $db->quoteName ( 'a.menutype' ) . ' = ' . $db->quote ( $menuType ) )
				  ->where ( $db->quoteName ( 'a.client_id' ) . ' = 0' );

			if ($id > 0) {
				$query->join ( 'LEFT', $db->quoteName ( '#__menu' ) . ' AS p ON p.id = ' . ( int ) $id )->where ( 'NOT(a.lft >= p.lft AND a.rgt <= p.rgt)' );
			}

			$query->where ( 'a.published != -2' )->order ( 'a.lft ASC' );

			$db->setQuery ( $query );

			$result = $db->loadObjectList ();
			$data = [ ];

			if (! empty ( $result )) {
				foreach ( $result as $value ) {
					$data [] = ( object ) [ 
							'value' => $value->value,
							'label' => $value->text
					];
				}
			}

			$rootItem = ( object ) [ 
					'value' => 1,
					'label' => Text::_ ( 'COM_JPAGEBUILDER_MENU_ITEM_ROOT' )
			];
			array_unshift ( $data, $rootItem );

			$response->data = $data;
			$response->code = 200;

			return $response;
		} catch ( Exception $e ) {
			$response->data = $e->getMessage ();
			$response->code = 500;

			return $response;
		}
	}
	public function getMenuByPageId($pageId = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( '*' );
		$query->from ( '#__menu' );
		$query->where ( $db->quoteName ( 'link' ) . ' = ' . $db->quote ( 'index.php?option=com_jpagebuilder&view=page&id=' . $pageId ) );
		$query->where ( $db->quoteName ( 'client_id' ) . '= 0' );
		$db->setQuery ( $query );

		return $db->loadObject ();
	}
	public function applyBulkActions(object $params) {
		switch ($params->type) {
			case 'published' :
				return $this->changeStatus ( $params->ids, 1 );
			case 'unpublished' :
				return $this->changeStatus ( $params->ids, 0 );
			case 'trash' :
				return $this->changeStatus ( $params->ids, - 2 );
			case 'delete' :
				return $this->deletePages ( $params->ids );
			case 'check-out' :
				return $this->checkOutItems ( $params->ids );
			case 'rename' :
				return $this->renamePage ( $params->ids, $params->value );
			case 'language' :
				return $this->performBulkActions ( $params->ids, 'language', $params->value );
			case 'access' :
				return $this->performBulkActions ( $params->ids, 'access', $params->value );
			case 'category' :
				return $this->performBulkActions ( $params->ids, 'catid', $params->value );
		}
	}
	private function performBulkActions(string $ids, string $type, string $value) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->update ( $db->quoteName ( '#__jpagebuilder' ) )
			  ->set ( $db->quoteName ( $type ) . ' = ' . $db->quote ( $value ) )
			  ->where ( $db->quoteName ( 'id' ) . ' IN (' . $ids . ')' );

		$db->setQuery ( $query );

		try {
			$db->execute ();
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
	private function renamePage(string $id, string $value) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->update ( $db->quoteName ( '#__jpagebuilder' ) )
			  ->set ( $db->quoteName ( 'title' ) . ' = ' . $db->quote ( $value ) )
			  ->where ( $db->quoteName ( 'id' ) . ' = ' . $id );

		$db->setQuery ( $query );

		try {
			$db->execute ();
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
	private function changeStatus(string $ids, int $status) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->update ( $db->quoteName ( '#__jpagebuilder' ) )
			  ->set ( $db->quoteName ( 'published' ) . ' = ' . $status )
			  ->where ( $db->quoteName ( 'id' ) . ' IN (' . $ids . ')' );

		$db->setQuery ( $query );

		try {
			$db->execute ();
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
	private function deletePages(string $ids) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->delete ( $db->quoteName ( '#__jpagebuilder' ) )
			  ->where ( $db->quoteName ( 'id' ) . ' IN (' . $ids . ')' );

		$db->setQuery ( $query );

		try {
			$db->execute ();
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
	private function checkOutItems(string $ids) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->update ( $db->quoteName ( '#__jpagebuilder' ) )
			  ->set ( $db->quoteName ( 'checked_out' ) . ' = 0' )
			  ->where ( $db->quoteName ( 'id' ) . ' IN (' . $ids . ')' );
		$db->setQuery ( $query );

		try {
			$db->execute ();
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
	public function getMenuById($menuId = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( array (
				'a.*'
		) );
		$query->from ( '#__menu as a' );
		$query->where ( 'a.id = ' . $menuId );
		$query->where ( 'a.client_id = 0' );
		$db->setQuery ( $query );

		return $db->loadObject ();
	}
	public function createPage(array $data) {
		try {
			if (! parent::save ( $data )) {
				throw new Exception ( 'Failed creating the page.' );
			}

			$id = $this->getState ( $this->getName () . '.id' ) ?? 0;

			return $id;
		} catch ( Exception $error ) {
			return [ 
					'message' => $error->getMessage ()
			];
		}
	}

	/**
	 * Toggle Integration
	 *
	 * @return boolean
	 * @since 4.0.0
	 */
	public function toggleIntegration($group = '', $name = '') {
		$enabled = PluginHelper::isEnabled ( $group, $name );
		$status = $enabled ? 0 : 1;

		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$fields = [ 
				$db->quoteName ( 'enabled' ) . ' = ' . $status
		];

		$conditions = [ 
				$db->quoteName ( 'type' ) . ' = ' . $db->quote ( 'plugin' ),
				$db->quoteName ( 'element' ) . ' = ' . $db->quote ( $name ),
				$db->quoteName ( 'folder' ) . ' = ' . $db->quote ( $group )
		];

		$query->update ( $db->quoteName ( '#__extensions' ) )->set ( $fields )->where ( $conditions );
		$db->setQuery ( $query );
		$db->execute ();

		return $status;
	}
	public function getPageCreator($id) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( 'created_by' )->from ( $db->quoteName ( '#__jpagebuilder' ) )->where ( $db->quoteName ( 'id' ) . ' = ' . $id );

		$db->setQuery ( $query );

		try {
			return $db->loadResult ();
		} catch ( \Exception $e ) {
			return 0;
		}
	}
}
