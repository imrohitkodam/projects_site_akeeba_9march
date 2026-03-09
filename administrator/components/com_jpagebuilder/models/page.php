<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;

require_once (JPATH_ROOT . '/components/com_jpagebuilder/helpers/route.php');
require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/tables/page.php');

class JpagebuilderModelPage extends AdminModel {
	public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?FormFactoryInterface $formFactory = null) {
		parent::__construct ( $config );
		
		$app = Factory::getApplication();
		$dispatcher = $app->getDispatcher();
		$this->setDispatcher($dispatcher);
	}
	public function getTable($type = 'Page', $prefix = 'JpagebuilderTable', $config = array ()) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$table = new JpagebuilderTablePage($db);
		return $table;
	}
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
	public function save($data) {
		$app = Factory::getApplication ();

		if ($app->getInput()->get ( 'task' ) == 'save2copy') {
			$data ['title'] = $this->pageGenerateNewTitle ( $data ['title'] );
		}

		if (! empty ( $data ['created_by'] )) {
			$data ['created_by'] = $this->checkExistingUser ( $data ['created_by'] );
		}

		parent::save ( $data );
		return true;
	}
	protected function checkExistingUser($id) {
		$currentUser = Factory::getApplication()->getIdentity();
		$user_id = $currentUser->id;

		if ($id) {
			$user = Factory::getContainer()->get('user.factory')->loadUserById($id);
			if ($user->id) {
				$user_id = $id;
			}
		}

		return $user_id;
	}
	public static function pageGenerateNewTitle($title) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$pageTable = new JpagebuilderTablePage($db);
		
		while ( $pageTable->load ( array (
				'title' => $title
		) ) ) {
			$m = null;
			if (preg_match ( '#\((\d+)\)$#', $title, $m )) {
				$title = preg_replace ( '#\(\d+\)$#', '(' . ($m [1] + 1) . ')', $title );
			} else {
				$title .= ' (2)';
			}
		}

		return $title;
	}
	public static function getPageInfoById($pageId) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( array (
				'a.*'
		) );
		$query->from ( $db->quoteName ( '#__jpagebuilder', 'a' ) );
		$query->where ( $db->quoteName ( 'a.id' ) . " = " . $db->quote ( $pageId ) );
		$db->setQuery ( $query );
		$result = $db->loadObject ();

		return $result;
	}
	public function getMySections() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'id',
				'title',
				'section',
				'created',
				'created_by'
		) ) );
		$query->from ( $db->quoteName ( '#__jpagebuilder_sections' ) );
		$query->order ( 'ordering ASC' );
		$db->setQuery ( $query );
		$results = $db->loadObjectList ();

		if (! empty ( $results )) {
			foreach ( $results as &$result ) {
				$result->created = (new DateTime ( $result->created ))->format ( 'j F, Y' );
				$result->author = Factory::getContainer()->get('user.factory')->loadUserById($result->created_by)->name;
				$result->section = JpagebuilderHelper::formatSavedSection ( $result->section );
				unset ( $result->created_by );
			}

			unset ( $result );
		}

		return json_encode ( $results );
	}
	public function deleteSection($id) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		// delete all custom keys for user 1001.
		$conditions = array (
				$db->quoteName ( 'id' ) . ' = ' . $id
		);

		$query->delete ( $db->quoteName ( '#__jpagebuilder_sections' ) );
		$query->where ( $conditions );

		$db->setQuery ( $query );

		return $db->execute ();
	}
	public function saveSection($title, $section) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$user = Factory::getApplication()->getIdentity();
		$obj = new stdClass ();
		$obj->title = $title;
		$obj->section = $section;
		$obj->created = Factory::getDate ()->toSql ();
		$obj->created_by = $user->get ( 'id' );

		$db->insertObject ( '#__jpagebuilder_sections', $obj );

		return $db->insertid ();
	}
	public function getMyAddons() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'id',
				'title',
				'code',
				'created',
				'created_by'
		) ) );
		$query->from ( $db->quoteName ( '#__jpagebuilder_addons' ) );

		$query->order ( 'ordering ASC' );
		$db->setQuery ( $query );
		$results = $db->loadObjectList ();

		if (! empty ( $results )) {
			foreach ( $results as &$result ) {
				$result->created = (new DateTime ( $result->created ))->format ( 'j F, Y' );
				$result->author = Factory::getContainer()->get('user.factory')->loadUserById($result->created_by)->name;
				$result->code = JpagebuilderHelper::formatSavedAddon ( $result->code );
				unset ( $result->created_by );
			}

			unset ( $result );
		}

		return json_encode ( $results );
	}
	public function saveAddon($title, $addon) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$user = Factory::getApplication()->getIdentity();
		$obj = new stdClass ();
		$obj->title = $title;
		$obj->code = $addon;
		$obj->created = Factory::getDate ()->toSql ();
		$obj->created_by = $user->get ( 'id' );

		$db->insertObject ( '#__jpagebuilder_addons', $obj );

		return $db->insertid ();
	}
	public function deleteAddon($id) {
		$db = Factory::getContainer()->get('DatabaseDriver');

		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		// delete all custom keys for user 1001.
		$conditions = array (
				$db->quoteName ( 'id' ) . ' = ' . $id
		);

		$query->delete ( $db->quoteName ( '#__jpagebuilder_addons' ) );
		$query->where ( $conditions );

		$db->setQuery ( $query );

		return $db->execute ();
	}
	public function createBrandNewPage($title = '', $extension = '', $extension_view = '', $view_id = 0) {
		$user = Factory::getApplication()->getIdentity();
		$date = Factory::getDate ();
		$db = Factory::getContainer()->get('DatabaseDriver');
		$page = new stdClass ();
		$page->title = $title;
		$page->text = '[]';
		$page->extension = $extension;
		$page->extension_view = $extension_view;
		$page->view_id = $view_id;
		$page->published = 1;
		$page->created_by = ( int ) $user->id;
		$page->created_on = $date->toSql ();
		$page->modified = $date->toSql ();
		$page->language = '*';
		$page->access = 1;
		$page->active = 1;
		$page->css = $page->css ?? '';
		$db->insertObject ( '#__jpagebuilder', $page );

		return $db->insertid ();
	}
	public function get_module_page_data($id) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'id'
		) ) );
		$query->from ( $db->quoteName ( '#__jpagebuilder' ) );
		$query->where ( $db->quoteName ( 'extension' ) . ' = ' . $db->quote ( 'mod_jpagebuilder' ) );
		$query->where ( $db->quoteName ( 'extension_view' ) . ' = ' . $db->quote ( 'module' ) );
		$query->where ( $db->quoteName ( 'view_id' ) . ' = ' . $db->quote ( $id ) );
		$query->order ( 'ordering ASC' );
		$db->setQuery ( $query );
		$result = $db->loadResult ();

		return $result;
	}
	private function save_module_data($id, $title, $content) {
		$user = Factory::getApplication()->getIdentity();
		$date = Factory::getDate ();
		$db = Factory::getContainer()->get('DatabaseDriver');
		$module = new stdClass ();
		$module->title = $title;
		$module->text = $content;
		$module->extension = 'mod_spmodulebuilder';
		$module->extension_view = 'module';
		$module->view_id = $id;
		$module->published = 1;
		$module->created_by = ( int ) $user->id;
		$module->created_on = $date->toSql ();
		$module->language = '*';
		$module->access = 1;
		$module->active = 1;

		$db->insertObject ( '#__jpagebuilder', $module );
		return $db->insertid ();
	}
	public function update_module_data($view_id, $id, $title, $content) {
		$user = Factory::getApplication()->getIdentity();
		$date = Factory::getDate ();

		$db = Factory::getContainer()->get('DatabaseDriver');
		$module = new stdClass ();
		$module->id = $view_id;
		$module->title = $title;
		$module->text = $content;
		$module->modified_by = ( int ) $user->id;
		$module->modified = $date->toSql ();

		$db->updateObject ( '#__jpagebuilder', $module, 'id' );
		return $db->insertid ();
	}
	public function getLanguages() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( [ 
				$db->quoteName ( 'lang_code', 'value' ),
				$db->quoteName ( 'title', 'text' )
		] )->from ( $db->quoteName ( '#__languages' ) )
		   ->where ( $db->quoteName ( 'published' ) . ' = 1' )
		   ->order ( $db->quoteName ( 'ordering' ) . ' ASC' );

		$db->setQuery ( $query );

		try {
			$languageList = $db->loadObjectList ();
			$allLanguage = ( object ) [ 
					'text' => Text::_ ( 'JALL' ),
					'value' => '*'
			];

			array_unshift ( $languageList, $allLanguage );

			return $languageList;
		} catch ( \Exception $e ) {
			return [ ];
		}
	}
}
