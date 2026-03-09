<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;

// no direct access
defined ( '_JEXEC' ) or die ();

require_once (JPATH_ROOT . '/components/com_jpagebuilder/helpers/route.php');
require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/models/page.php');

class JpagebuilderModelForm extends JpagebuilderModelPage {
	protected $_conText = 'com_jpagebuilder.page';
	protected $_item = array ();
	protected function populateState() {
		$app = Factory::getApplication ( 'site' );

		$pageId = $app->getInput()->getInt ( 'id' );
		$this->setState ( 'page.id', $pageId );

		$user = Factory::getApplication()->getIdentity();

		if ((! $user->authorise ( 'core.edit.state', 'com_jpagebuilder' )) && (! $user->authorise ( 'core.edit', 'com_jpagebuilder' ))) {
			$this->setState ( 'filter.published', 1 );
		}
	}
	public function getItem($pageId = null) {
		/** @var CMSApplication */
		$app = Factory::getApplication ();
		$user = Factory::getApplication()->getIdentity();
		$pageId = (! empty ( $pageId )) ? $pageId : ( int ) $this->getState ( 'page.id' );

		if ($user->guest) {
			$return = 'index.php?option=com_jpagebuilder&view=form&layout=edit&tmpl=component&id=' . $pageId;
			$loginUrl = Route::_ ( 'index.php?option=com_users&view=login&return=' . base64_encode ( $return ), false );
			$app->redirect ( $loginUrl );
		}

		$canEdit = $user->authorise ( 'core.edit', 'com_jpagebuilder' );
		$canEditOwn = $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );

		if ($canEditOwn) {
			$item_info = JpagebuilderModelPage::getPageInfoById ( $pageId );

			$canEditOwn = $item_info->created_by == $user->id;
		}

		if (! $canEdit && ! $canEditOwn) {
			throw new Exception ( Text::_ ( 'COM_JPAGEBUILDER_INVALID_EDIT_ACCESS' ), 403 );
		}

		if (! isset ( $this->_item [$pageId] )) {
			try {
				$db = Factory::getContainer()->get('DatabaseDriver');
				$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

				$query->select ( 'a.*' )
					  ->from ( $db->quoteName ( '#__jpagebuilder', 'a' ) )
					  ->where ( $db->quoteName ( 'a.id' ) . ' = ' . ( int ) $pageId );

				$query->select ( $db->quoteName ( 'l.title', 'language_title' ) )
					  ->join ( 'LEFT', $db->quoteName ( '#__languages', 'l' ) . ' ON ' . $db->quoteName ( 'l.lang_code' ) . ' = ' . $db->quoteName ( 'a.language' ) );

				$query->select ( $db->quoteName ( 'ua.name', 'author_name' ) )
					  ->join ( 'LEFT', $db->quoteName ( '#__users', 'ua' ) . ' ON ' . $db->quoteName ( 'ua.id' ) . ' = ' . $db->quoteName ( 'a.created_by' ) );

				$published = $this->getState ( 'filter.published' );

				if (is_numeric ( $published )) {
					$query->where ( $db->quoteName ( 'a.published' ) . ' = ' . ( int ) $published );
				} elseif ($published === '') {
					$query->where ( $db->quoteName ( 'a.published' ) . ' IN (0, 1)' );
				}

				$db->setQuery ( $query );
				$data = $db->loadObject ();

				if (empty ( $data )) {
					throw new Exception ( Text::_ ( 'COM_JPAGEBUILDER_ERROR_PAGE_NOT_FOUND' ), 404 );
				}

				if ($this->getState ( 'filter.access' )) {
					$data->access_view = true;
				} else {
					$user = Factory::getApplication()->getIdentity();
					$groups = $user->getAuthorisedViewLevels ();

					$data->access_view = in_array ( $data->access, $groups );
				}

				if (isset ( $data->attribs )) {
					$attribs = json_decode ( $data->attribs );
				} else {
					$attribs = new stdClass ();
				}

				$data->link = JpagebuilderHelperRoute::getPageRoute ( $data->id, $data->language );
				$data->formLink = JpagebuilderHelperRoute::getFormRoute ( $data->id, $data->language );

				$data->meta_title = (isset ( $attribs->meta_title ) && $attribs->meta_title) ? $attribs->meta_title : '';
				$data->meta_description = (isset ( $attribs->meta_description ) && $attribs->meta_description) ? $attribs->meta_description : '';
				$data->meta_keywords = (isset ( $attribs->meta_keywords ) && $attribs->meta_keywords) ? $attribs->meta_keywords : '';
				$data->robots = (isset ( $attribs->robots ) && $attribs->robots) ? $attribs->robots : '';
				$data->og_type = (isset ( $attribs->og_type ) && $attribs->og_type) ? $attribs->og_type : 'website';

				$menu = $this->getMenuByPageId ( $data->id );
				$data->menuid = (isset ( $menu->id ) && $menu->id) ? $menu->id : 0;
				$data->menutitle = (isset ( $menu->title ) && $menu->title) ? $menu->title : '';
				$data->menualias = (isset ( $menu->alias ) && $menu->alias) ? $menu->alias : '';
				$data->menutype = (isset ( $menu->menutype ) && $menu->menutype) ? $menu->menutype : '';
				$data->menuparent_id = (isset ( $menu->parent_id ) && $menu->parent_id) ? $menu->parent_id : 0;
				$data->menuordering = (isset ( $menu->id ) && $menu->id) ? $menu->id : - 2;

				$this->_item [$pageId] = $data;
			} catch ( Exception $e ) {
				if ($e->getCode () == 404) {
					throw new Exception ( $e->getMessage (), 404 );
				} else {
					$this->setError ( $e );
					$this->_item [$pageId] = false;
				}
			}
		}

		return $this->_item [$pageId];
	}
	public function getForm($data = array (), $loadData = true) {
		$app = Factory::getApplication ();
		$user = Factory::getApplication()->getIdentity();

		// Get the form.
		$form = $this->loadForm ( 'com_jpagebuilder.page', 'page', array (
				'control' => 'jform',
				'load_data' => $loadData
		) );

		if (empty ( $form )) {
			return false;
		}

		// Manually check-out
		$pageId = (! empty ( $pageId )) ? $pageId : ( int ) $this->getState ( 'page.id' );
		if ($user->id) {
			$this->checkout ( $pageId );
		}

		return parent::getForm ();
	}
	public function savePage($data) {
		return parent::save ( $data );
	}
	public function save($data) {
		$attribs = array ();

		if (isset ( $data ['meta_title'] ) && $data ['meta_title']) {
			$attribs ['meta_title'] = $data ['meta_title'];
		}
		
		if (isset ( $data ['meta_description'] ) && $data ['meta_description']) {
			$attribs ['meta_description'] = $data ['meta_description'];
		}

		if (isset ( $data ['meta_keywords'] ) && $data ['meta_keywords']) {
			$attribs ['meta_keywords'] = $data ['meta_keywords'];
		}

		if (isset ( $data ['robots'] ) && $data ['robots']) {
			$attribs ['robots'] = $data ['robots'];
		}

		if (isset ( $data ['og_type'] ) && $data ['og_type']) {
			$attribs ['og_type'] = $data ['og_type'];
		}

		$data ['attribs'] = json_encode ( $attribs );

		return parent::save ( $data );
	}
	public function getMenuByPageId($pageId = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( array (
				'a.*'
		) );
		$query->from ( '#__menu as a' );
		$query->where ( 'a.link = ' . $db->quote ( 'index.php?option=com_jpagebuilder&view=page&id=' . $pageId ) );
		$query->where ( 'a.client_id = 0' );
		$db->setQuery ( $query );

		return $db->loadObject ();
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
	public function getMenuByAlias($alias, $menuId = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( array (
				'a.id',
				'a.title',
				'a.alias',
				'a.menutype',
				'a.parent_id',
				'a.component_id'
		) );
		$query->from ( '#__menu as a' );
		$query->where ( 'a.alias = ' . $db->quote ( $alias ) );
		if ($menuId) {
			$query->where ( 'a.id != ' . ( int ) $menuId );
		}
		$query->where ( 'a.client_id = 0' );
		$db->setQuery ( $query );

		return $db->loadObject ();
	}
	public function createNewPage($title) {
		$user = Factory::getApplication()->getIdentity();
		$date = Factory::getDate ();
		$db = Factory::getContainer()->get('DatabaseDriver');
		$page = new stdClass ();
		$page->title = $title;
		$page->Text = '[]';
		$page->extension = 'com_jpagebuilder';
		$page->extension_view = 'page';
		$page->published = 1;
		$page->created_by = ( int ) $user->id;
		$page->created_on = $date->toSql ();
		$page->language = '*';
		$page->access = 1;
		$db->insertObject ( '#__jpagebuilder', $page );

		return $db->insertid ();
	}
	public function deletePage($id = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$conditions = array (
				$db->quoteName ( 'id' ) . ' = ' . $id
		);
		$query->delete ( $db->quoteName ( '#__jpagebuilder' ) );
		$query->where ( $conditions );
		$db->setQuery ( $query );
		$result = $db->execute ();
		return $result;
	}
	public function getPageItem($id = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( array (
				'extension',
				'extension_view',
				'view_id',
				'catid'
		) );
		$query->from ( $db->quoteName ( '#__jpagebuilder' ) );
		$query->where ( $db->quoteName ( 'id' ) . ' = ' . $db->quote ( $id ) );
		$db->setQuery ( $query );
		$result = $db->loadObject ();

		if (count ( ( array ) $result )) {
			return $result;
		}

		return false;
	}
	public function addArticleFullText($id, $data) {
		$article = new stdClass ();
		$article->id = $id;
		$article->fulltext = JpagebuilderHelperSite::getPrettyText ( $data );

		$db = Factory::getContainer()->get('DatabaseDriver');
		$db->updateObject ( '#__content', $article, 'id' );
	}
	/**
	 * Save Data function
	 *
	 * @param array $data
	 * @return void
	 *
	 * @since 4.0.0
	 */
	public function saveData($data, $id) {
		$attrKeys = [ 
				'meta_title',
				'meta_description',
				'meta_keywords',
				'robots',
				'seo_spacer',
				'og_type'
		];

		$menuKeys = [ 
				'menuid',
				'menutitle',
				'menualias',
				'menutype',
				'menuparent_id',
				'menuordering'
		];

		$attribs = [ ];

		foreach ( $data as $key => $value ) {
			if (in_array ( $key, $attrKeys )) {
				$attribs [$key] = $value;
				unset ( $data [$key] );
			}

			if (in_array ( $key, $menuKeys )) {
				unset ( $data [$key] );
			}
		}

		$data ['attribs'] = json_encode ( $attribs );

		if (! empty ( $data ['og_image'] ) && ! is_string ( $data ['og_image'] )) {
			$data ['og_image'] = json_encode ( $data ['og_image'] );
		}

		if (empty ( $data ['catid'] )) {
			$data ['catid'] = 0;
		}

		$data = ( object ) $data;

		if (empty ( $id )) {
			$response ['status'] = false;
			$response ['message'] = 'No ID Provided!';

			echo json_encode ( $response );
			die ();
		}

		$this->setState ( 'filter.access', $data->access );

		try {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$db->updateObject ( '#__jpagebuilder', $data, 'id', true );

			$response ['status'] = true;
			$response ['message'] = 'Updated';
		} catch ( Exception $e ) {
			$response ['status'] = false;
			$response ['message'] = $e->getMessage ();
		}

		return $response;
	}
	public function getPageCreatorId($pageId) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( 'created_by' )
			  ->from ( $db->quoteName ( '#__jpagebuilder' ) )
			  ->where ( $db->quoteName ( 'id' ) . ' = ' . $pageId );

		$db->setQuery ( $query );

		try {
			return $db->loadResult ();
		} catch ( \Exception $e ) {
			return 0;
		}
	}
	public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?FormFactoryInterface $formFactory = null) {
		parent::__construct ( $config );
		
		$app = Factory::getApplication();
		$dispatcher = $app->getDispatcher();
		$this->setDispatcher($dispatcher);
	}}
