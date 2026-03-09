<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;

/**
 * AppConfig Model Class for managing app configs.
 * 
 * @version 4.1.0
 */
class JpagebuilderModelAppconfig extends ListModel {
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
	public function getPageList() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select(['id', 'title'])
			->from($db->quoteName('#__jpagebuilder'))
			->where($db->quoteName('published') . ' = 1')
			->order($db->quoteName('title') . ' ASC');

		$db->setQuery($query);

		try
		{
			return $db->loadObjectList();
		}
		catch (\Exception $e)
		{
			return [];
		}
	}
	
	public function getCategories() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select(['id', 'title', 'level', 'lft'])
			  ->from($db->quoteName('#__categories'))
			  ->where($db->quoteName('published') . ' = 1')
			  ->where($db->quoteName('extension') . ' = ' . $db->quote('com_jpagebuilder'))
			  ->order($db->quoteName('lft') . ' ASC');

		$db->setQuery($query);
		try {
			return $db->loadObjectList ();
		} catch ( \Exception $e ) {
			return [ ];
		}
	}
	
	public function getMenus() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select(['id', 'title', 'link'])
			  ->from($db->quoteName('#__menu'))
			  ->where($db->quoteName('published') . ' = 1')
			  ->where($db->quoteName('id') . ' > 1')
			  ->where($db->quoteName('client_id') . ' = 0')
			  ->order($db->quoteName('title') . ' ASC');
		
		$db->setQuery ( $query );
		$menuItems = [ ];

		try {
			$menuItems = $db->loadObjectList ();
		} catch ( \Exception $e ) {
			return [ ];
		}

		if (! empty ( $menuItems )) {
			foreach ( $menuItems as &$item ) {
				$item->id = $item->link . '&Itemid=' . $item->id;
				unset ( $item->link );
			}

			unset ( $item );
		}

		return $menuItems;
	}
	
	public function getModules() {
		return [ ];
	}
	
	public function getAccessLevels() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select(['id', 'title'])
			  ->from($db->quoteName('#__viewlevels'))
			  ->order($db->quoteName('ordering') . ' ASC');

		$db->setQuery($query);
		try {
			return $db->loadObjectList ();
		} catch ( \Exception $e ) {
			return [ ];
		}
	}
	
	public function getLanguages() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select([$db->quoteName('lang_code', 'id'), 'title'])
			  ->from($db->quoteName('#__languages'))
			  ->where($db->quoteName('published') . ' = 1')
			  ->order($db->quoteName('ordering') . ' ASC');

		$db->setQuery($query);
		try {
			return $db->loadObjectList ();
		} catch ( \Exception $e ) {
			return [ ];
		}
	}

	public function getUserPermissions($pageId = 0) {
		$user = Factory::getApplication()->getIdentity();

		if (! $user->id) {
			return [ 
					'admin' => false,
					'manage' => false,
					'create' => false,
					'edit' => false,
					'edit_state' => false,
					'edit_own' => false,
					'delete' => false,
					'page' => [ 
							'edit' => false,
							'delete' => false,
							'edit_state' => false
					]
			];
		}

		$isAdmin = $user->authorise ( 'core.admin', 'com_jpagebuilder' );
		$canManage = $user->authorise ( 'core.manage', 'com_jpagebuilder' );
		$canCreate = $user->authorise ( 'core.create', 'com_jpagebuilder' );
		$canEdit = $user->authorise ( 'core.edit', 'com_jpagebuilder' );
		$canEditState = $user->authorise ( 'core.edit.state', 'com_jpagebuilder' );
		$canEditOwn = $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );
		$canDelete = $user->authorise ( 'core.delete', 'com_jpagebuilder' );

		$canEditPage = $user->authorise ( 'core.edit', 'com_jpagebuilder.page.' . $pageId );
		$canDeletePage = $user->authorise ( 'core.delete', 'com_jpagebuilder.page.' . $pageId );
		$canEditStatePage = $user->authorise ( 'core.edit.state', 'com_jpagebuilder.page.' . $pageId );

		return [ 
				'admin' => $isAdmin,
				'manage' => $canManage,
				'create' => $canCreate,
				'edit' => $canEdit,
				'edit_state' => $canEditState,
				'edit_own' => $canEditOwn,
				'delete' => $canDelete,
				'page' => [ 
						'edit' => $canEditPage,
						'delete' => $canDeletePage,
						'edit_state' => $canEditStatePage
				]
		];
	}
}
