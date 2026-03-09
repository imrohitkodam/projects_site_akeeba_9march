<?php
/**
 * @package    AdminTools
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 * @license    GNU General Public License version 3, or later
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;

require_once JPATH_SITE . '/libraries/lib_db_sync/lib_db_sync.php';

/**
 * Quick2cart Installer
 *
 * @since  1.0.0
 */
class Com_Quick2cartInstallerScript
{
	use LibDBSync;

	/** @var array The list of extra modules and plugins to install */
	private $oldversion = "";

	// Used to identify new install or update
	private $componentStatus = "install";

	/** @var array Obsolete files and folders to remove*/
	private $removeFilesAndFolders = array(
		'files'	=> array(
			// Removed since 2.2

			'administrator/components/com_quick2cart/views/vendor/tmpl/approvestore.php',
			'administrator/components/com_quick2cart/views/vendor/tmpl/default.php',
			'administrator/components/com_quick2cart/views/vendor/tmpl/newvender.php',
			'components/com_quick2cart/views/vendor/tmpl/default.php',
			'components/com_quick2cart/views/vendor/tmpl/default.xml',
			'components/com_quick2cart/views/managecoupon/metadata.xml',
			'components/com_quick2cart/views/reports/metadata.xml',
			'components/com_quick2cart/views/reports/tmpl/mypayouts.xml',

			/* version 2.3.1*/
			'components/com_quick2cart/views/zones/tmpl/default2.php',
			'com_quick2cart/productpage/popupslide.php',

			/* version 2.5.1*/
			'components/com_quick2cart/assets/css/bootstrap-slider.css',
			'components/com_quick2cart/assets/js/bootstrap-slider.js',

			/* version 2.6*/
			'components/com_quick2cart/views/orders/tmpl/default_store_cartdetail.php',
			'components/com_quick2cart/helpers/user.php',

			/* Removed in version 2.8*/
			'components/com_quick2cart/views/coupons/metadata.xml',
			'components/com_quick2cart/views/productpage/tmpl/users.php',
			'modules/mod_quick2cart/tmpl/default_itemrow.php',
			'modules/mod_quick2cart/tmpl/default_itemshort.php',
			'components/com_quick2cart/views/cartcheckout/tmpl/default_cartdetail.php',
			'administrator/components/com_quick2cart/defines.php',

			/* Removed while making Q2C compatible with joomla 4*/
			'administrator/components/com_quick2cart/views/product/tmpl/new_extrafields_tab_body.php',
			'administrator/components/com_quick2cart/views/product/tmpl/new_extrafields_tab_header.php',
			'administrator/components/com_quick2cart/views/product/tmpl/options.php',
			'administrator/components/com_quick2cart/views/product/tmpl/taxship.php',
			'administrator/components/com_quick2cart/views/product/tmpl/attribute.php',
			'administrator/components/com_quick2cart/views/product/tmpl/attribute2.php',
			'administrator/components/com_quick2cart/views/product/tmpl/media.php',
			'administrator/components/com_quick2cart/views/product/tmpl/medialist.php',

			// Removed sql files after sql from quick2cart_database.xml
			'administrator/components/com_quick2cart/sql/updates/mysql/2.2.0.sql',
			'administrator/components/com_quick2cart/sql/updates/mysql/2.9.18.sql',
			'administrator/components/com_quick2cart/sql/updates/mysql/5.0.0.sql',
			'administrator/components/com_quick2cart/sql/updates/mysql/3.0.0.sql',
			'administrator/components/com_quick2cart/sql/updates/mysql/4.0.0.sql',
			'administrator/components/com_quick2cart/sql/updates/mysql/4.0.1.sql',

		),
		'folders' => array(
			/* Version 2.3.1*/
			'components/com_quick2cart/views/managecoupon',
			'components/com_quick2cart/views/reports',
			'components/com_quick2cart/assets/font-awesome'
		)
	);

	/**
	 * Removes obsolete files and folders
	 *
	 * @param   array  $removeFilesAndFolders  remove Files And Folders
	 *
	 * @return  ''.
	 */
	private function _removeObsoleteFilesAndFolders($removeFilesAndFolders)
	{
		// Remove files

		if (!empty($removeFilesAndFolders['files']))
		{
			foreach ($removeFilesAndFolders['files'] as $file)
			{
				$f = JPATH_ROOT . '/' . $file;

				if (!File::exists($f))
				{
					continue;
				}

				File::delete($f);
			}
		}

		// Remove folders
		if (!empty($removeFilesAndFolders['folders']))
		{
			foreach ($removeFilesAndFolders['folders'] as $folder)
			{
				$f = JPATH_ROOT . '/' . $folder;

				if (!Folder::exists($f))
				{
					continue;
				}

				Folder::delete($f);
			}
		}
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string      $type    install, update or discover_update
	 * @param   JInstaller  $parent  parent
	 *
	 * @return void
	 */
	public function preflight($type, $parent)
	{
		/*
		$parent is the class calling this method
		$type is the type of change (install, update or discover_install)
		Only allow to install on Joomla! 2.5.0 or later
		return version_compare(JVERSION, '2.5.0', 'ge');
		*/
	}

	/**
	 * Runs after install, update or discover_update
	 *
	 * @param   string      $type    install, update or discover_update
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  ''.
	 */
	public function postflight($type, $parent)
	{
		if ($type != 'uninstall')
		{
			$lang = Factory::getLanguage();
			$lang->load('com_quick2cart', JPATH_SITE);

			$msgBox = array();

			/* AS We are loading the strapper from com_tjfields first
			* $straperStatus = $this->_installStraper($parent);
			* */

			// Remove obsolete files and folders
			$removeFilesAndFolders = $this->removeFilesAndFolders;
			$this->_removeObsoleteFilesAndFolders($removeFilesAndFolders);

			// Add Uncategorised __categories in #__categories table
			$this->addUncategorisedCat();

			// Create default store
			$storeMsg = $this->createSuperuserstore();

			if (!empty($storeMsg))
			{
				// Not msg return mean not create
				$msgBox['Stores'] = $storeMsg;
			}

			// ADD STORE DASHBOARD MENU IN MAIN MENU
			$menusMsg = $this->addMenuItems();

			if (!empty($storeMsg))
			{
				// Not msg return mean not create
				$msgBox['Menus'] = $menusMsg;
			}

			// Since version 2.2
			$this->fix_menus_on_update();

			// Write template file for email and pdf template
			$this->_writeTemplate();

			/*
			$this->migrateCountryRelatedFields();
			ADD QUICK2CART MENUES IN JS TOOLBAR
			$this->addDefaultToolbarMenus();
			*/

			if (!Folder::exists(JPATH_ROOT . '/images/quick2cart'))
			{
				Folder::create(JPATH_ROOT . '/images/quick2cart');
			}


			//Create folder for storing images added by customer for product review.
			if (!Folder::exists(JPATH_ROOT . '/images/reviews'))
			{
				Folder::create(JPATH_ROOT . '/images/reviews');
			}

			if (!Folder::exists(JPATH_ROOT . '/images/reviews'))
			{
				Folder::create(JPATH_ROOT . '/images/reviews');
			}

			// Load bootstrap and jquery for installation screen
			HTMLHelper::_('script', Uri::root(true) . '/media/techjoomla_strapper/js/akeebajq.js');

			// Do all releated Tag line/ logo etc
			$this->taglinMsg();

			// Migrate country and region table
			$this->migrateDbfix();

			// Add default permissions
			$this->permissionsFix();
			$this->migrateTaxShipDetails();
			$this->migrateCouponChangesTO28version();
			$this->deleteLog();

			// Set default layouts to load
			$this->setDefaultLayout($type);
		}
	}



	/**
	 * method to install default email templates
	 *
	 * @return void
	 */
	private function _writeTemplate()
	{
		// Insert the required data for notifications template
		$this->_installNotificationsTemplates();
	}


	/**
	 * Installed Notifications
	 * method to install default email templates
	 *
	 * @return  void
	 */
	public function _installNotificationsTemplates()
	{
		$client = 'com_quick2cart';
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
		require_once JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models/notification.php';
		$notificationsModel = JModelLegacy::getInstance('Notification', 'TJNotificationsModel');

		$existingKeys = array();
		$filePath = JPATH_ADMINISTRATOR . '/components/com_quick2cart/quick2cartTemplate.json';
		$str = file_get_contents($filePath);
		$json = json_decode($str, true);
		$existingKeys = $notificationsModel->getKeys($client);

		if (!empty($json))
		{
			foreach ($json as $template => $array)
			{
				$replacementTagCount = $notificationsModel->getReplacementTagsCount($array['key'], $client);

				// If template doesn't exist then we add notification template.
				if (!in_array($array['key'], $existingKeys))
				{
					$notificationsModel->createTemplates($array);
				}
				else
				{
					$notificationsModel->updateTemplates($array, $client);
				}

				// If replacement tags are changed update those
				if (in_array($array['key'], $existingKeys) && isset($array['replacement_tags']) && count($array['replacement_tags']) != $replacementTagCount)
				{
					$notificationsModel->updateReplacementTags($array);
				}
			}
		}
	}

	/**
	 * method to install the component
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return void
	 */
	public function install($parent)
	{
		// ~$parent is the class calling this method
		$this->installSqlFiles($parent);

		$this->setQuick2cartDefaultBehavior();
		/* On new installation - keep bootstrap 3 layouts as default
		$this->changeBSViews();
		*/
	}

	/**
	 * Install Sql Files
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  ''.
	 *
	 * @since  1.0.2
	 */
	public function installSqlFiles($parent)
	{
		$db = Factory::getDBO();
		// Old code that used to install database from .sql file

		//  Database sync
		$path         = method_exists($parent, 'extension_root') ? $parent->getPath('extension_root') . '/admin/quick2cart_database.xml' : $parent->getParent()->getPath('extension_root') . '/quick2cart_database.xml';
		$this->syncDatabase($path);

		// Insert the data into kart_lengths table(#kart_lengths) 
		$this->runSQL($parent, 'lengths.sql');

		// Insert the data into kart_weights table(#kart_weights) 
		$this->runSQL($parent, 'weights.sql');
	}

	/**
	 * Runs on uninstallation
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  ''.
	 */
	public function uninstall($parent)
	{
		/* ~ $status = $this->_uninstallSubextensions($parent);
		Show the post-uninstallation page
		~ $this->_renderPostUninstallation($status, $parent);
		*/
	}

	/**
	 * method to update the component
	 *
	 * @param   object  $parent  parent
	 *
	 * @return void
	 */
	public function update($parent)
	{
		$this->componentStatus = "update";
		$this->installSqlFiles($parent);
		$this->setQuick2cartDefaultBehavior();
	}

	// End of update

	/**
	 * Run sqls
	 *
	 * @param   object  $parent   parent
	 * @param   string  $sqlfile  sql file
	 *
	 * @return  ''.
	 *
	 * @since  1.0.2
	 */
	public function runSQL($parent,$sqlfile)
	{
		$db = Factory::getDBO();

		// Obviously you may have to change the path and name if your installation SQL file ;)
		if (method_exists($parent, 'extension_root'))
		{
			$sqlfile = $parent->getPath('extension_root') . '/admin/sql/' . $sqlfile;
		}
		else
		{
			$sqlfile = $parent->getParent()->getPath('extension_root') . '/sql/' . $sqlfile;
		}

		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);

		if ($buffer !== false)
		{
			$queries = \JDatabaseDriver::splitSql($buffer);

			if (count($queries) != 0)
			{
				foreach ($queries as $query)
				{
					$query = trim($query);

					if ($query != '' && $query[0] != '#')
					{
						$db->setQuery($query);

						if (!$db->execute())
						{
							$this->setMessage(Text::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), 'error');

							return false;
						}
					}
				}
			}
		}
	}

	/**
	 * Tag line, version etc
	 *
	 * @return ''.
	 */
	public function taglinMsg()
	{
		echo Text::_("<h4>Thank you for installing Quick2Cart by Techjoomla, the powerful ecommerce component for Joomla! </h4>");

		// Version check
		$formXml    = simplexml_load_file(JPATH_ROOT . '/administrator/components/com_quick2cart/quick2cart.xml');
		$xml        = (array) $formXml->version;
		$oldversion = (string) $xml[0];

		require_once JPATH_ROOT . '/components/com_quick2cart/helper.php';
		$helperobj = new comquick2cartHelper;
		$latestversion = $helperobj->getVersion();

		if (version_compare($oldversion, $latestversion, 'lt'))
		{
			echo "<span id='NewVersion' style='padding-top: 5px; color: red; font-weight: bold;
			padding-left: 5px;'>" . Text::_("It seems that you have installed an older version.
			Latest Version is : ") . $latestversion . "</span>";
		}

		echo "<br/>";
	}
	// End of tagline msg

	/**
	 * Get columns
	 *
	 * @param   string  $table  table name
	 *
	 * @return  array   this function return array of column names
	 */
	public function getColumns($table)
	{
		$db = Factory::getDBO();
		$field_array = array();
		$query = "SHOW COLUMNS FROM " . $table;
		$db->setQuery($query);
		$columns = $db->loadobjectlist();

		for ($i = 0; $i < count($columns); $i++)
		{
			$field_array[] = $columns[$i]->Field;
		}

		return $field_array;
	}

	/**
	 * Update itemid
	 *
	 * @param   string  $tbname      table name
	 * @param   string  $primarykey  primary key
	 *
	 * @return ''.
	 */
	public function migrateData($tbname,$primarykey)
	{
		$db = Factory::getDBO();
		$query = "select " . $primarykey . " , parent, product_id from " . $tbname;
		$db->setQuery($query);
		$rawdata = $db->loadAssocList();

		foreach ($rawdata as $rec)
		{
			$item_id = 0;
			$item_id = $this->getitemid($rec['product_id'], $rec['parent']);

			// 1 update item id againt primary key
			$row = new stdClass;
			$row->$primarykey = $rec[$primarykey];
			$row->item_id = $item_id;

			if (!$db->updateObject($tbname, $row, $primarykey))
			{
				echo $this->_db->stderr();

				return false;
			}
		}

		// 2.delete product_id,parent coloum
		$colarray = '';
		$colarray = $this->getColumns($tbname);

		if (in_array('product_id', $colarray))
		{
			$query = "ALTER TABLE `" . $tbname . "` DROP column `product_id`";
			$db->setQuery($query);
			$db->execute();
		}

		if (in_array('parent', $colarray))
		{
			$query = "ALTER TABLE `" . $tbname . "` DROP column `parent`";
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Update itemid
	 *
	 * @param   string  $tbname      table name
	 * @param   string  $primarykey  primary key
	 * @param   string  $item_id     item id
	 *
	 * @return ''.
	 */
	public function updateItemId($tbname, $primarykey, $item_id)
	{
		$db = Factory::getDBO();
		$row = new stdClass;
		$row->$primarykey = $primarykey;

		if (!$db->updateObject($tbname, $row, $primarykey))
		{
			echo $this->_db->stderr();

			return false;
		}
	}

	/**
	 * Get itemid
	 *
	 * @param   string  $product_id  product id
	 * @param   string  $client      client
	 *
	 * @return ''.
	 */
	public function getitemid($product_id, $client)
	{
		$db = Factory::getDBO();
		$query = "SELECT `item_id` FROM `#__kart_items`  where `product_id`=" . (int) $product_id . " AND parent='$client'";

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Add Uncategorised __categories in #__categories table
	 *
	 * @return ''.
	 */
	public function addUncategorisedCat()
	{
			$db = Factory::getDBO();
			$query  = 'SELECT `id` FROM `#__categories` WHERE `extension` = \'com_quick2cart\' AND `title`=\'Uncategorised\'';
			$db->setQuery($query);
			$result = $db->loadResult();

			if (empty($result))
			{
				$catobj = new stdClass;
				$catobj->title = 'Uncategorised';
				$catobj->alias = 'uncategorised';
				$catobj->extension = "com_quick2cart";
				$catobj->path = " uncategorised";
				$catobj->parent_id = 1;
				$catobj->level = 1;
				$paramdata = array();
				$paramdata['category_layout'] = '';
				$paramdata['image'] = '';
				$catobj->params = json_encode($paramdata);

				// LOGGED user id
				$user = Factory::getUser();
				$catobj->created_user_id = $user->id;
				$catobj->language = "*";
				$catobj->published = 1;
				$catobj->access = 1;

				$createdDateTime = Factory::getDate('now');
				$catobj->created_time = $createdDateTime->toSQL();
				$catobj->modified_time = $createdDateTime->toSQL();

				if (!$db->insertObject('#__categories', $catobj, 'id'))
				{
					echo $db->stderr();

					return false;
				}
			}
	}

	/**
	 * Create default store on install
	 *
	 * @return ''.
	 */
	public function createSuperuserstore()
	{
		$storeMsg = array();

		// CHECK EXISTANCE OF DEFAULT STORE
		$db = Factory::getDBO();
		$query = "SELECT `id`,`extra` FROM `#__kart_store` WHERE `extra` IS NOT NULL";
		$db->setQuery($query);
		$storedata = $db->loadAssocList();
		$default_store = 0;

		foreach ($storedata as $data)
		{
			$extraField = json_decode($data['extra'], 1);

			if (!empty($extraField['default']))
			{
				$default_store = $data['id'];
				break;
			}
		}

		// If no default store is found then create
		if (empty($default_store))
		{
				$product_path = JPATH_SITE . '/components/com_quick2cart/models/vendor.php';

				if (!class_exists('quick2cartModelVendor'))
				{
					// ~require_once $path;
					JLoader::register('quick2cartModelVendor', $product_path);
					JLoader::load('quick2cartModelVendor');
				}

				$user = Factory::getUser();
				$app = Factory::getApplication();
				$jinput = $app->input;
				$post = $jinput->post;

				// ~$post = array();
				$sitename = $app->get('sitename');
				$post->set('title', $sitename);
				$post->set('description', '');
				$post->set('companyname', '');
				$post->set('address', '');
				$post->set('phone', '');
				$post->set('email', $app->get('mailfrom'));
				$post->set('paymentMode', 1);
				$post->set('otherPayMethod', $app->get('mailfrom'));
				$post->set('storeVanityUrl', '');

				$extraArray = array();
				$extraArray['default'] = 1;
				$extraArray = json_encode($extraArray);
				$post->set('extra', $extraArray);
				$avtar_path = 'components/com_quick2cart/images/no_user.png';
				$post->set('avatar', $avtar_path);
				$storeheader_path = 'components/com_quick2cart/images/header_default2.jpg';
				$post->set('storeheader',  $storeheader_path);
				$quick2cartModelVendor = new quick2cartModelVendor;
				$quick2cartModelVendor->store($post);
				$default_store = $sitename . " (Default Store) ";
				$storeMsg[$default_store] = "Created";

				// Text::_("COM_QUICK2CART_ADDED_STORE_ON_INSTALL_MSG");;

				return $storeMsg;
		}
	}

	/**
	 * This function add dashboard menu entry in mainmainu
	 *
	 * @return ''.
	 */
	public function addMenuItems()
	{
		$addedMenuMsg = array();
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$db = Factory::getDBO();

		// Get new component id.
		$component    = ComponentHelper::getComponent('com_quick2cart');
		$component_id = 0;

		if (is_object($component) && isset($component->id))
		{
			$component_id = $component->id;
		}

		/*
		$column_name = JOOMLA_MENU_NAME;
		$column_cid  = JOOMLA_MENU_COMPONENT_ID;

		Get the default menu type
		2 Joomla bugs occur in /Administrator mode
		Bug 1: Factory::getApplication('site') failed. It always return id = 'administrator'.
		Bug 2: JMenu::getDefault('*') failed. JAdministrator::getLanguageFilter() doesn't exist.
		If these 2 bugs are fixed, we can call the following syntax:
		$defaultMenuType	= Factory::getApplication('sites')->getMenu()->getDefault()->menutype;
		*/
		$defaultMenuType = CMSApplication::getInstance('site')->getMenu()->getDefault('workaround_joomla_bug')->menutype;

		// Update the existing menu items.
		$row				= Table::getInstance('menu', 'JTable');
		$row->menutype		= $defaultMenuType;
		$row->title	= Text::_('COM_QUICK2CART_DASHBOARDMENU');
		$row->alias			= 'vendor-dashboard';
		$row->path			= 'vendor-dashboard';
		$row->access		= 1;
		$row->link			= 'index.php?option=com_quick2cart&view=vendor&layout=cp';
		$row->type			= 'component';
		$row->published		= 0;
		$row->component_id	= $component_id;

		// New item
		$row->id			= null;
		$row->language		= '*';

		$row->check();

		$ispresent = $this->isMenuItemPresent($row->link);

		if (empty($ispresent))
		{
			// ADD MENU
			$var = $row->store();

			// UPDATE MENU
			$query = 'UPDATE ' . $db->quoteName('#__menu')
			. ' SET `parent_id` = ' . $db->quote(1)
			. ', `level` = ' . $db->quote(1)
			. ' WHERE `id` = ' . $db->quote($row->id);
			$db->setQuery($query);
			$db->execute();

			try
			{
				$db->execute();
			}
			catch (\RuntimeException $e)
			{
				return false;
			}

			// AS ADDD DASHBOARD MENU. Use it for display
			// 	Text::_("COM_QUICK2CART_MENU_ON_INSTALL");
			$menuIndex_Name = $row->title . " MENU ";
			$addedMenuMsg[$menuIndex_Name] = Text::_("COM_QUICK2CART_ADDED_MENU_ON_INSTALL");
		}

		$row				= Table::getInstance('menu', 'JTable');
		$row->menutype		= $defaultMenuType;
		$row->title	= Text::_('COM_QUICK2CART_ALLPRODUCTSMENU');
		$row->alias			= 'all-products';
		$row->path			= 'all-products';
		$row->access		= 1;
		$row->link			= 'index.php?option=com_quick2cart&view=category&layout=default';
		$row->type			= 'component';
		$row->published		= 0;
		$row->component_id	= $component_id;

		// New item
		$row->id			= null;
		$row->language		= '*';

		$row->check();

		$ispresent = $this->isMenuItemPresent($row->link);

		if (empty($ispresent))
		{
			// ADD new MENU
			$var = $row->store();

			// UPDATE
			$query = 'UPDATE ' . $db->quoteName('#__menu')
				. ' SET `parent_id` = ' . $db->quote(1)
				. ', `level` = ' . $db->quote(1)
				. ' WHERE `id` = ' . $db->quote($row->id);
			$db->setQuery($query);
			$db->execute();

			try
			{
				$db->execute();
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

			// AS ADDD ALL PRODUCT MENU. Use it for display
			// Text::_("COM_QUICK2CART_MENU_ON_INSTALL");
			$menuIndex_Name = $row->title . " MENU ";
			$addedMenuMsg[$menuIndex_Name] = Text::_("COM_QUICK2CART_ADDED_MENU_ON_INSTALL");

			return $addedMenuMsg;

			// ~return true;
		}
	}

	/**
	 * Check whether menu for link is resent or not
	 *
	 * @param   string       $link      link for menu
	 * @param   string|null  $menutype  type of menu
	 *
	 * @return  ''.
	 */
	public function isMenuItemPresent($link, $menutype='mainmenu')
	{
		$db = Factory::getDBO();
		$query = 'SELECT `id` from `#__menu` where `link` LIKE ' . "\"%$link%\" ";

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Add default toolbar  menu
	 *
	 * @return  boolean
	 */
	public function addDefaultToolbarMenus()
	{
		$target = JPATH_ROOT . '/administrator/components/com_community';

		if (Folder::exists($target))
		{
			// GETTING COMPONENT ID
			$component    = ComponentHelper::getComponent('com_quick2cart');
			$component_id = 0;

			if (is_object($component) && isset($component->id))
			{
				$component_id = $component->id;
			}

			$db				= Factory::getDBO();
			$file			= JPATH_ROOT . '/administrator/components/com_quick2cart/toolbar.xml';
			$menu_name		= "title";
			$menu_parent	= "parent_id";
			$menu_level		= "level";
			$items			= new SimpleXMLElement($file, null, true);
			$items			= $items->items;

			$i	= 1;

			foreach ($items->children() as $item)
			{
				// Each menu
				$obj				= new stdClass;
				$obj->$menu_name	= (string) $item->name;
				$obj->$menu_name = Text::_($obj->$menu_name);

				$obj->alias			= (string) $item->alias;
				$obj->path			= (string) $item->alias;
				$obj->link			= (string) $item->link;
				$obj->access		= (string) $item->access;
				$obj->menutype		= 'jomsocial';
				$obj->type			= 'component';
				$obj->published		= 1;
				$obj->$menu_parent	= 1;
				$obj->level	= 1;
				$obj->language		= '*';
				$obj->component_id = $component_id;

				// GETTING CHILDS
				$childs	= $item->childs;
				$manuid = $this->menuExists($obj->link, $obj->menutype);

				if (!empty($manuid))
				{
					// Update menu
					$parentId = $obj->id = $manuid;

					if (!$db->updateObject('#__menu', $obj, 'id'))
					{
						echo $this->_db->stderr();

						return false;
					}
				}
				else
				{
					$query 	= 'SELECT ' . $db->quoteName('rgt') . ' '
					. 'FROM ' . $db->quoteName('#__menu') . ' '
						. 'ORDER BY ' . $db->quoteName('rgt') . ' DESC LIMIT 1';

					$db->setQuery($query);
					$obj->lft 	= $db->loadResult() + 1;
					$totalchild = $childs?count($childs->children()):0;
					$obj->rgt	= $obj->lft + $totalchild * 2 + 1;

					// Insert
					$db->insertObject('#__menu', $obj);

					// J1.6: menu item ordering follow lft and rgt
					if ($db->getErrorNum())
					{
						return false;
					}

					$parentId = $db->insertid();
				}

				// CHECK FOR CHILDS
				if ($childs)
				{
					$x	= 1;

					foreach ($childs->children() as $child)
					{
						$childObj		= new stdClass;
						$childObj->$menu_name	= (string) $child->name;
						$childObj->$menu_name = Text::_($childObj->$menu_name);

						$childObj->alias		= (string) $child->alias;
						$childObj->path = $item->alias . '/' . $childObj->alias;

						$childObj->link			= (string) $child->link;
						$childObj->access		= (string) $item->access;
						$childObj->menutype		= 'jomsocial';
						$childObj->type			= 'component';
						$childObj->published	= 1;
						$childObj->$menu_parent	= $parentId;
						$childObj->$menu_level	= 1 + 1;
						$childObj->language		= '*';
						$childObj->component_id = $component_id;

						$childMenuId = $this->menuExists($childObj->link, $childObj->menutype);

						if (!empty($childMenuId))
						{
								// Update CHILD menu
								$childObj->id = $childMenuId;

								try
								{
									$db->updateObject('#__menu', $childObj, 'id');
								}
								catch(\RuntimeException $e)
								{
									$this->setError($e->getMessage());

									return false;
								}
						}
						else
						{
							// J1.6: menu item ordering follow lft and rgt
							$childObj->lft = $obj->lft + ($x - 1) * 2 + 1;
							$childObj->rgt = $childObj->lft + 1;

							try
							{
								$db->insertObject('#__menu', $childObj);
							}
							catch (\RuntimeException $e)
							{
								$this->setError($e->getMessage());

								return false;
							}
						}

						$x++;
					}
				}

				$i++;
			}

			return true;
		}
	}

	/**
	 * This function is used to check menu Exists
	 *
	 * @param   string       $link      link for menu
	 * @param   string|null  $menutype  type of menu
	 *
	 * @return  ''.
	 *
	 * @since   2.3.0
	 */
	public function menuExists($link, $menutype = null)
	{
		$db = Factory::getDBO();
		$query = 'SELECT `id`
		 FROM `#__menu`
		 WHERE `link` LIKE "%' . $link . '%"';

		if ($menutype != null)
		{
			$query .= ' AND `menutype`="' . $menutype . '"';
		}

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * This function is used to fix menus on update
	 *
	 * @return  ''.
	 *
	 * @since   2.3.0
	 */
	public function fix_menus_on_update()
	{
		// Since 2.2
		// Fix jomsocial menus
		$childMenuId = $this->menuExists('index.php?option=com_quick2cart&view=managecoupon', 'jomsocial');
		$db = Factory::getDBO();

		if (!empty($childMenuId))
		{
			$childObj = new stdClass;
			$childObj->link = 'index.php?option=com_quick2cart&view=coupons&layout=my';
			$childObj->id = $childMenuId;

			if (!$db->updateObject('#__menu', $childObj, 'id'))
			{
				echo $this->_db->stderr();

				return false;
			}
		}

		// Fix q2c menus
		$changedMenus = array();

		// Old , new
		// Since 2.2
		$changedMenus[] = array('index.php?option=com_quick2cart&view=vendor&layout=default', 'index.php?option=com_quick2cart&view=stores&layout=my');
		$changedMenus[] = array('index.php?option=com_quick2cart&view=managecoupon', 'index.php?option=com_quick2cart&view=coupons&layout=my');
		$changedMenus[] = array('index.php?option=com_quick2cart&view=reports&layout=mypayouts', 'index.php?option=com_quick2cart&&view=payouts&layout=my');

		foreach ($changedMenus as $menu)
		{
			$childMenuId = $this->menuExists($menu[0]);

			if (!empty($childMenuId))
			{
				$childObj = new stdClass;
				$childObj->link = $menu[1];
				$childObj->id = $childMenuId;

				if (!$db->updateObject('#__menu', $childObj, 'id'))
				{
					echo $this->_db->stderr();

					return false;
				}
			}
		}

		// Remove duplicate menu item
		// Since 5.0.0
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		if (JVERSION >= '4.0.0')
		{
			$table   = new \Joomla\Component\Menus\Administrator\Table\MenuTable($db);
		} 
		else 
		{
			Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
			$table = Table::getInstance('menu', 'MenusTable', array('dbo', $db));
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('menutype') . ' = ' . $db->quote('main'));
		$query->where($db->quoteName('path') . ' IN ("com-quick2cart-vendor", "com-quick2cart-title-countries",
			"com-quick2cart-title-regions", "com-quick2cart-title-form-group", 
			"com-quick2cart-title-form-fields")');
		$db->setQuery($query);
		$data = $db->loadObjectList();

		foreach ($data as $key => $menuItem) 
		{
			$table->delete($menuItem->id);
		}
	}

	/**
	 * This function is used to migrate  the db fix
	 *
	 * @return  ''.
	 *
	 * @since   2.3.0
	 */
	public function migrateDbfix()
	{
		$db       = Factory::getDBO();
		$config   = Factory::getConfig();
		$dbprefix = $config->get('dbprefix');

		if ($this->componentStatus == "update")
		{
			// Check wheter previoulsy exist or not
			$query = "SHOW TABLES LIKE '" . $dbprefix . "kart_users_backup';";
			$db->setQuery($query);
			$backup_exists = $db->loadResult();
			$query = "Select COUNT(*) From #__kart_users";
			$db->setQuery($query);
			$billing_data = $db->loadObjectlist();

			if (!$backup_exists && !empty($billing_data))
			{
				// Check whether tj field component installed or not
				$query = "SHOW COLUMNS FROM #__tj_country WHERE `Field` = 'country_Text'";
				$db->setQuery($query);
				$check = $db->loadResult();

				$latestversion = 2.2;

				if (!$check)
				{
					echo "<span id='NewVersion' style='padding-top: 5px; color: red;
					font-weight: bold; padding-left: 5px;'>"
					. Text::_("COM_QUICK2CART_PLS_INSTLL_TJFIEDLS_COMP") . $latestversion
					. "</span>";

					return;
				}

				HTMLHelper::_('script', Uri::root(true) . '/media/techjoomla_strapper/js/akeebajq.js');
				?>
				<script src="<?php echo Uri::root(true) . '/media/techjoomla_strapper/js/akeebajq.js'; ?>" type="text/javascript"></script>
				<script type="text/javascript">

			function migrateOrders()
				{
					var root_url="<?php echo Uri::root(); ?>";
					techjoomla.jQuery.ajax({
						url: root_url + 'index.php?option=com_quick2cart&task=cart.migrateCountryRelatedFields&tmpl=component',
						type: 'GET',
						dataType: '',
						beforeSend: function()
						{	techjoomla.jQuery('#qtcLoader_image_div').show();
						},
						complete: function()
						{
							techjoomla.jQuery('#qtcLoader_image_div').hide();

						},
						success: function(data)
						{
							if (data==1)
							{
								techjoomla.jQuery('#qtcStatus').html("<?php echo Text::_("COM_QUICK2CART_MIGRATION_COMPLETED"); ?>");
								techjoomla.jQuery('#qtcStatus').show();
								techjoomla.jQuery('#qtc_migrate_btnDiv').hide();
							}

						}
					});
				}
			</script>
			<div class="q2c-wrapper techjoomla-bootstrap">
				<div class="row-fluid">
					<div class="span12">
					<div id='qtc_migrate_btnDiv'>
						<div class="alert alert-info">
							<input type="button" class="btn btn-primary" value="<?php echo Text::_("COM_QUICK2CART_CLICK_HERE"); ?>"
							onclick="migrateOrders();">
							<b><?php echo Text::_("COM_QUICK2CART_MIGRATE_OLD_ORDERS"); ?></b>
						</div>
					</div>
					<?php
						$image_path = Uri::root() . "components/com_quick2cart/assets/images/ajax.gif";
					?>
					<div class="" id="qtcLoader_image_div" style="display:none;margin-left:50%;">
						<img src='<?php echo $image_path; ?>' width="78" height="15" border="0"/>
					</div>
					<div class="alert alert-info" id="qtcStatus" style="display:none">

					</div>

				</div>
				</div>
			</div>
				<?php
			}
		}
	}

	/**
	 * This function is used to  rename the table
	 *
	 * @param   string   $table           table name
	 * @param   string   $newTable        new table name
	 * @param   boolean  $appendDateTime  append Date Time
	 *
	 * @return  ''.
	 *
	 * @since   2.3.0
	 */
	public function renameTable($table, $newTable, $appendDateTime = 1)
	{
		$db = Factory::getDBO();
		$query = "RENAME TABLE " . $table . " TO " . $newTable;

		if ($appendDateTime)
		{
			$query = $query . '_' . date("Ymd_H_i_s");
		}

		$db->setQuery($query);

		if (!$db->execute())
		{
			echo $db->stderr();

			return false;
		}

		return true;
	}

	/**
	 * This function is used to set permissions
	 *
	 * @return  ''.
	 *
	 * @since   2.3.0
	 */
	public function permissionsFix()
	{
		$db = Factory::getDBO();
		$query = "SELECT id, rules FROM `#__assets` WHERE `name` = 'com_quick2cart' ";
		$db->setQuery($query);
		$result = $db->loadobject();

		if (strlen(trim($result->rules)) <= 3)
		{
			$obj = new Stdclass;
			$obj->id = $result->id;
			$obj->rules = '{"core.admin":[], "core.manage":[], "core.create":{"2":1},
			"core.delete":[], "core.edit":[], "core.edit.state":{"2":1},
			"core.edit.own":{"2":1}}';

			if (!$db->updateObject('#__assets', $obj, 'id'))
			{
				$app = Factory::getApplication();
				$app->enqueueMessage($db->stderr(), 'error');
			}
		}
	}

	/**
	 * This function is used to migrate the tax and shipping charge detail to new format.
	 * OLD: order tax = SUM(item Tax) similary for shipping charges
	 * NEW: Item level tax is not add in order tax fields. Order tax fields contain only order tax.
	 *
	 * @return  ''.
	 *
	 * @since   2.3.0
	 */
	public function migrateTaxShipDetails()
	{
		$db     = Factory::getDBO();
		$comquick2cartHelper = new Comquick2cartHelper;
		$path                = JPATH_SITE . '/components/com_quick2cart/models/cartcheckout.php';
		$Quick2cartModelcartcheckout = $comquick2cartHelper->loadqtcClass($path, "Quick2cartModelcartcheckout");

		// A.Check for column itemTaxShipIncluded. If present then only migration required else not.
		$query = "SHOW COLUMNS FROM `#__kart_orders`";
		$db->setQuery($query);
		$columns = $db->loadColumn();

		// B.If col is not present then add
		if (!in_array('itemTaxShipIncluded', $columns))
		{
			// C. Else add column= 'itemTaxShipIncluded' to db
			$query = "ALTER TABLE  `#__kart_orders` ADD  `itemTaxShipIncluded`
			tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Flag : whether order tax
			and shipping is summation of order item tax and ship or not. 1 =>
			orderTax = sum(order item tax)'";
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (\RuntimeException $e)
			{
				$this->setError(Text::_('COM_QUICK2CART_UNABLE_TO_ALTER_COLUMN') . " - #__kart_orders.");

				return false;
			}

			// D. Set its value column value to 1

			$query = $db->getQuery(true);

			// Fields to update.
			$fields = array($db->quoteName('itemTaxShipIncluded') . ' = 1');

			// Conditions for which records should be updated.
			$query->update($db->quoteName('#__kart_orders'))->set($fields);
			$db->setQuery($query);
			$db->execute();
		}

		// E:
		$query = $db->getQuery(true);
		$query->select(' o.* ');
		$query->from('#__kart_orders as o');
		$query->where("(o.order_tax > 0 OR o.order_shipping > 0)");
		$query->where("itemTaxShipIncluded=1");
		$query->order("o.id DESC");

		$db->setQuery($query);
		$orderList = $db->loadObjectList('id');

		foreach ($orderList as $order)
		{
			$query = $db->getQuery(true);
			$modifiedOrder = new stdClass;
			$modifiedOrder->id = $order->id;

			// Get order item details
			$query->select('order_id, sum(product_final_price) as totalItemprice,sum(item_tax) as totalItemTax,sum(item_shipcharges) as totalShipCharge');
			$query->from('#__kart_order_item as i');
			$query->where("order_id= " . $order->id);
			$db->setQuery($query);
			$itemDetail = $db->loadAssoc('id');

			// 1. Update original amount
			$modifiedOrder->original_amount = $itemDetail['totalItemprice'];

			// 2.update tax and ship
			$OrderLevelTax = $order->order_tax - $itemDetail['totalItemTax'];

			if ($OrderLevelTax >= 0)
			{
				$modifiedOrder->order_tax = $OrderLevelTax;
			}
			else
			{
				$modifiedOrder->order_tax = 0;
			}

			$OrderLevelShip = $order->order_shipping - $itemDetail['totalShipCharge'];

			if ($OrderLevelTax >= 0)
			{
				$modifiedOrder->order_shipping = $OrderLevelShip;
			}
			else
			{
				$modifiedOrder->order_shipping = 0;
			}

			// 3. Check for coupon
			$copDiscount = 0;

			if ($order->coupon_code)
			{
				$copDiscount = $Quick2cartModelcartcheckout->afterDiscountPrice(
					$modifiedOrder->original_amount, $order->coupon_code, "",
					"order", $modifiedOrder->id
				);

				$copDiscount = ($copDiscount >= 0) ? $copDiscount : 0;
			}

			// 4. Update amount column according to discount,tax,shipping details
			$modifiedOrder->amount = $modifiedOrder->original_amount + $modifiedOrder->order_tax + $modifiedOrder->order_shipping - $copDiscount;

			if (!$db->updateObject('#__kart_orders', $modifiedOrder, 'id'))
			{
				$this->setError($db->getErrorMsg());

				return 0;
			}
		}
	}

	/**
	 * Migrate coupon to 2.8 version
	 * 1. Changes in order item table : Add coupon from param->coupon
	 *    code to coupon_code column.
	 * 2. we have to add old coupon price to discount_amount column
	 * 3. Update order table: original_amount= sum (all item final price),
	 *    order's tax, ship column contain only order level taxation and
	 *    shipping plugin detail
	 *
	 * @return  [type]  [description]
	 */
	public function migrateCouponChangesTO28version()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select("order_item_id,order_id,	params,product_item_price,
			product_attributes_price, product_quantity, item_tax,item_shipcharges,
			product_final_price");
		$query->from('#__kart_order_item AS oi');
		$conditions = array(
			$db->quoteName('oi.params') . ' LIKE ' . $db->quote("%coupon_code%")
		);

		$query->where($conditions);
		$db->setQuery($query);
		$orderItems = $db->loadObjectList();

		if (!empty($orderItems))
		{
			// 1: Migrate coupon chnages in kart_order_items
			foreach ($orderItems as $oi)
			{
				$oitem                    = new stdClass;
				$oitem->order_item_id = $oi->order_item_id;

				// Final amount of order item (FP) = (base product price (OP) * qty) + Tax + ship - discount
				//  Discount = (base price product (OP)* qty) + Tax + ship - final amount of order item (FP)
				$discount = (($oi->product_item_price + $oi->product_attributes_price) *
				$oi->product_quantity) + $oi->item_tax + $oi->item_shipcharges -
				$oi->product_final_price;

				if ($discount > 0)
				{
					$oitem->discount = $discount;

					// Update params column
					$param = $oi->params;

					$paramArray = json_decode($param, true);

					// Add old coupon code in coupon_code column and discount in discount column
					if (isset($paramArray['coupon_code']))
					{
						$oitem->coupon_code = $paramArray['coupon_code'];

						unset($paramArray['coupon_code']);
						$oitem->params = json_encode($paramArray);
					}

					$status = $db->updateObject('#__kart_order_item', $oitem, 'order_item_id');

					// If discount found
					if (!$db->updateObject('#__kart_order_item', $oitem, 'order_item_id'))
					{
						echo $db->stderr();

						return 0;
					}
				}
			}
		}

		// A.Check for column itemTaxShipIncluded. If present then only migration required else not.
		$query = "SHOW COLUMNS FROM `#__kart_orders`";
		$db->setQuery($query);
		$columns = $db->loadColumn();

		// B.If col is not present then add
		if (!in_array('migrateto28version', $columns) )
		{
			// C. Else add column= 'itemTaxShipIncluded' to db
			$query = "ALTER TABLE  `#__kart_orders` ADD  `migrateto28version` SMALLINT(3 ) NOT NULL DEFAULT 0 COMMENT 'Flag : to migrate to version 2.8.'";
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (\RuntimeException $e)
			{
				$this->setError(Text::_('COM_QUICK2CART_UNABLE_TO_ALTER_COLUMN') . " - #__kart_orders {For migrateto28version column}.");

				return false;
			}

			// D. Set its value column value to 1
			$query = $db->getQuery(true);

			// Fields to update.
			$fields = array(
				$db->quoteName('migrateto28version') . ' = 1'
			);

			// Conditions for which records should be updated.
			$conditions = array();

			$query->update($db->quoteName('#__kart_orders'))->set($fields);
			$db->setQuery($query);
			$db->execute();
		}

		// 2. Update original_amount, tax, shipping amount from order table
		$query = $db->getQuery(true);
		$query->select('  oi.`order_id` , SUM( `product_item_price` ) AS product_item_price,
			SUM( `product_attributes_price` ) AS product_attributes_price,
			SUM(`product_quantity` ) AS product_quantity, SUM( `discount` ) AS discount,
			SUM( `item_tax` ) AS item_tax, SUM( `item_shipcharges` ) AS item_shipcharges,
			SUM( `product_final_price` ) AS product_final_price,o.id, o.`original_amount` ,
			o.`amount` , o.`order_tax` , o.`order_shipping` , o.`coupon_discount`  ');
		$query->from('#__kart_order_item AS oi');
		$query->join('INNER', '#__kart_orders AS o ON oi.`order_id` = o.`id`');
		$query->where("oi.`order_id` IS NOT NULL");
		$query->group("oi.`order_id`");
		$query->where("o.`migrateto28version` = 1");

		$db->setQuery($query);
		$orderList = $db->loadObjectList();

		if (!empty($orderList))
		{
			foreach ($orderList as $oiSumData)
			{
				$query = $db->getQuery(true);
				$modifiedOrder = new stdClass;
				$modifiedOrder->id = $oiSumData->order_id;

				// 1. Update original amount
				$modifiedOrder->original_amount = $oiSumData->product_final_price;

				// 2.update tax: order tax = Old order tax - sum(item tax)
				if (!empty($oiSumData->item_tax))
				{
					$newTax = $oiSumData->order_tax - $oiSumData->item_tax;
					$modifiedOrder->order_tax = ($newTax < 0) ? 0 : $newTax;
				}
				else
				{
					// Order level tax  is present for db order
					$modifiedOrder->order_tax = $oiSumData->order_tax;
				}

				// 3.update Ship: order ship = Old order shio - sum(item ship)
				if (!empty($oiSumData->item_shipcharges))
				{
					$newShip = $oiSumData->order_shipping - $oiSumData->item_shipcharges;
					$modifiedOrder->order_shipping = ($newShip < 0) ? 0 : $newShip;
				}
				else
				{
					// Order level shipping  is present for db order
					$modifiedOrder->order_shipping = $oiSumData->order_shipping;
				}

				// 4: Need to update discount ($modifiedOrder->amount i old amount)
				$orderDiscount = $modifiedOrder->original_amount + $modifiedOrder->order_tax + $modifiedOrder->order_shipping - $oiSumData->amount;

				$modifiedOrder->coupon_discount = ($orderDiscount > 0) ? $orderDiscount : 0;

				// 5: for amount
				$finalOrderAmt = $modifiedOrder->original_amount + $modifiedOrder->order_tax + $modifiedOrder->order_shipping - $modifiedOrder->coupon_discount;

				if ($oiSumData->amount == $finalOrderAmt)
				{
					$modifiedOrder->migrateto28version = 0;
				}

				// $modifiedOrder->amount = $finalOrderAmt;
				if (!$db->updateObject('#__kart_orders', $modifiedOrder, 'id'))
				{
					$this->setError($db->getErrorMsg());

					return 0;
				}
			}
		}
	}

	/**
	 * Migrate Customer Addresses
	 *
	 * @return  null
	 *
	 * @since   2.9.5
	 */
	public function migrateCustomerAddresses()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__kart_users'));
		$query->where($db->quoteName('address_type') . "= 'BT'");
		$query->group($db->quoteName('user_id'));
		$db->setQuery($query);
		$userAddresses = $db->loadObjectlist();

		foreach ($userAddresses as $userAddress)
		{
			$addressDetails = new stdclass;

			if (empty($userAddress->user_id))
			{
				continue;
			}

			$addressDetails->user_id       = $userAddress->user_id;
			$addressDetails->firstname     = $userAddress->firstname;
			$addressDetails->middlename    = $userAddress->middlename;
			$addressDetails->lastname      = $userAddress->lastname;
			$addressDetails->vat_number    = $userAddress->vat_number;
			$addressDetails->phone         = $userAddress->phone;
			$addressDetails->address_title = $userAddress->firstname;
			$addressDetails->user_email    = $userAddress->user_email;
			$addressDetails->address       = $userAddress->address;
			$addressDetails->land_mark     = $userAddress->land_mark;
			$addressDetails->zipcode       = $userAddress->zipcode;
			$addressDetails->country_code  = $userAddress->country_code;
			$addressDetails->state_code    = $userAddress->state_code;
			$addressDetails->city          = $userAddress->city;
			$db = Factory::getDbo();
			$db->insertObject('#__kart_customer_address', $addressDetails, 'id');
			$db->insertid();
		}
	}

	/**
	 * Generate alias from product title
	 *
	 * @return  null
	 *
	 * @since   2.9.5
	 */
	public function migrateAlias()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__kart_items'));
		$db->setQuery($query);
		$items = $db->loadObjectlist();

		foreach ($items as $item)
		{
			$alias = $item->name;

			if (Factory::getConfig()->get('unicodeslugs') == 1)
			{
				$alias = OutputFilter::stringURLUnicodeSlug($alias);
			}
			else
			{
				$alias = OutputFilter::stringURLSafe($alias);
			}

			// Check if course with same alias is present
			Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_category/tables');
			Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');
			$table = Table::getInstance('Product', 'Quick2cartTable', array('dbo', $db));

			if ($table->load(array('alias' => $alias)))
			{
				while ($table->load(array('alias' => $alias)))
				{
					$alias = StringHelper::increment($alias, 'dash');
				}
			}

			// Check if category with same alias is present
			$category = Table::getInstance('Category', 'JTable', array('dbo', $db));

			if ($category->load(array('alias' => $alias)))
			{
				while ($category->load(array('alias' => $alias)))
				{
					$alias = StringHelper::increment($alias, 'dash');
				}
			}

			$quick2cartViews = array('adduserform', 'createorder', 'productpage', 'shipprofileform', 'vendor',
				'attributes', 'customer_addressform', 'promotion', 'shipprofiles', 'zoneform', 'cart',
				'downloads', 'promotions', 'stores', 'zones', 'cartcheckout', 'taxprofileform', 'category',
				'orders', 'taxprofiles', 'couponform', 'payouts', 'registration', 'taxrateform', 'coupons',
				'product', 'shipping', 'taxrates');

			if (in_array($alias, $quick2cartViews))
			{
				$alias = StringHelper::increment($alias, 'dash');

				while ($table->load(array('alias' => $alias)))
				{
					$alias = StringHelper::increment($alias, 'dash');
				}
			}

			if (trim(str_replace('-', '', $alias)) == '')
			{
				$alias = Factory::getDate()->format("Y-m-d-H-i-s");
			}

			$item->alias = $alias;

			$db = Factory::getDbo();
			$db->updateObject('#__kart_items', $item, 'item_id');
			$db->insertid();
		}
	}

	/**
	 * Delete Payment plugin logs on installation
	 *
	 * @since  2.0.5
	 *
	 * @return  void
	 */
	public function deleteLog()
	{
		// Get log config path
		$config = new JConfig;
		$output = '';

		$logsPath = array(
		"cpg__paypal.log",
		"com_quick2cart_paypal.log",
		"com_quick2cart_authorizenet.log",
		"com_quick2cart_amazon.log",
		"com_quick2cart_2checkout.log",
		"com_quick2cart_blank.log",
		"com_quick2cart_adaptive_paypal.log",
		"com_quick2cart_alphauserpoints.log",
		"com_quick2cart_bycheck.log",
		"com_quick2cart_byorder.log",
		"com_quick2cart_jomsocialpoints.log",
		"com_quick2cart_linkpoint.log",
		"com_quick2cart_payu.log",
		"com_quick2cart_ccavenue.log",
		"com_quick2cart_easysocialpoints.log",
		"com_quick2cart_cod.log",
		"com_quick2cart_code.log",
		"com_quick2cart_epaydk.log",
		"com_quick2cart_ewallet.log",
		"com_quick2cart_eway.log",
		"com_quick2cart_ewayrapid3.log",
		"com_quick2cart_ogone.log",
		"com_quick2cart_pagseguro.log",
		"com_quick2cart_payfast.log",
		"com_quick2cart_paymill.log",
		"com_quick2cart_paypalpro.log",
		"com_quick2cart_payumoney.log",
		"com_quick2cart_razorpay.log",
		"com_quick2cart_transfirst.log");

		foreach ($logsPath as $path)
		{
			// 1. detele log
			if (!File::delete($config->log_path . '/' . $path))
			{
				// 2. if not 1 then clear content
				$output = file_put_contents($config->log_path . '/' . $path, "");

				// 3. if not 2 then create htaccess
				if (empty($output))
				{
					file_put_contents($config->log_path . '/.htaccess', "Deny from all");
				}
			}
		}
	}

	/**
	 * Method to set quick2cart default behavior
	 *
	 * @return void|boolean  in case of success return void incase of failure return false
	 */
	public function setQuick2cartDefaultBehavior()
	{
		$user = Factory::getUser();
		$db   = Factory::getDbo();

		// Check if tag exists
		$sql = $db->getQuery(true)->select($db->qn('type_id'))
			->from($db->qn('#__content_types'))
			->where($db->qn('type_title') . ' = ' . $db->q('Product'))
			->where($db->qn('type_alias') . ' = ' . $db->q('com_quick2cart.product'));
		$db->setQuery($sql);
		$type_id = $db->loadResult();

		// Create tag
		$db                                 = Factory::getDBO();
		$tagobject                          = new stdclass;
		$tagobject->type_id                 = '';
		$tagobject->type_title              = 'Product';
		$tagobject->type_alias              = 'com_quick2cart.product';
		$tagobject->table                   = '{"special":{"dbtable":"#__kart_items","key":"item_id","type":"Quick2cartproduct",'
		. '"prefix":"JTable","config":"array()"},'
		. '"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$tagobject->rules                   = '';

		$field_mappings_arr = array(
		'common' => array(
					"core_content_item_id" => "item_id",
					"core_title" => "name",
					"core_state" => "state",
					"core_alias" => "alias",
					"core_created_time" => "cdate",
					"core_modified_time" => "mdate",
					"core_body_short" => "description",
					"core_body_long" => "null",
					"core_hits" => "null",
					"core_publish_up" => "null",
					"core_publish_down" => "null",
					"core_access" => "null",
					"core_params" => "params",
					"core_featured" => "featured",
					"core_metadata" => "metadesc",
					"core_language" => "null",
					"core_images" => "images",
					"core_urls" => "null",
					"core_version" => "null",
					"core_ordering" => "ordering",
					"core_metakey" => "metakey",
					"core_metadesc" => "metadesc",
					"core_catid" => "category",
					"core_xreference" => "null",
					"asset_id" => "null"
				),
		'special' => array(
					"parent_id" => "parent_id",
					"lft" => "lft",
					"rgt" => "rgt",
					"level" => "level",
					"path" => "path",
					"path" => "path",
					"extension" => "extension",
					"extension" => "extension",
					"note" => "note"
					)
		);
		$tagobject->field_mappings          = json_encode($field_mappings_arr);

		if (!$type_id)
		{
			try
			{
				$db->insertObject('#__content_types', $tagobject, 'type_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}
		else
		{
			$tagobject->type_id = $type_id;

			try
			{
				$db->updateObject('#__content_types', $tagobject, 'type_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}

		/** @var JTableContentType $table */
		$table	= JTable::getInstance('contenttype');
		$table->load(array('type_alias' => 'com_quick2cart.category'));

		if (!$table->type_id)
		{
			                            
			$data	= array(
				'type_title'		=> 'Product Category',
				'type_alias'		=> 'com_quick2cart.category',
				'table'				=> '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
				'rules'				=> '',
				'field_mappings'	=> '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description","core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access","core_params":"params","core_featured":"null","core_metadata":"metadata","core_language":"language","core_images":"null","core_urls":"null","core_version":"version","core_ordering":"null","core_metakey":"metakey","core_metadesc":"metadesc","core_catid":"parent_id","core_xreference":"null","asset_id":"asset_id"},"special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}',
				'content_history_options' => '{"formFile":"administrator\/components\/com_categories\/models\/forms\/category.xml","hideFields":["checked_out","checked_out_time","version","lft","rgt","level","path","extension"],"ignoreChanges":["modified_user_id","modified_time","checked_out","checked_out_time","version","hits","path"],"convertToInt":["publish_up","publish_down"],"displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}',
			);

			$table->bind($data);

			if ($table->check())
			{
				$table->store();
			}
		}

		// Create default category on installation if not exists
		$sql = $db->getQuery(true)->select($db->quoteName('id'))
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_quick2cart'));

		$db->setQuery($sql);
		$cat_id = $db->loadResult();

		if (empty($cat_id))
		{
			$catobj                  = new stdClass;
			$catobj->title           = 'Uncategorised';
			$catobj->alias           = 'uncategorised';
			$catobj->extension       = "com_quick2cart";
			$catobj->path            = "uncategorised";
			$catobj->parent_id       = 1;
			$catobj->level           = 1;
			$catobj->created_user_id = $user->id;
			$catobj->language        = "*";
			$catobj->description     = '<p>This is a default Event category</p>';
			$catobj->published       = 1;
			$catobj->access          = 1;
			$catobj->created_time    = Factory::getDate()->toSql();
			$catobj->modified_time   = Factory::getDate()->toSql();

			if (!$db->insertObject('#__categories', $catobj, 'id'))
			{
				echo $db->stderr();

				return false;
			}
		}
	}

	/**
	 * Set default bootstrap layouts to load
	 *
	 * @param   string  $type  install, update or discover_update
	 * 
	 * @return void
	 *
	 * @since 4.0.0
	 */
	public function setDefaultLayout($type)
	{
		if ($type == 'install' && JVERSION >= '4.0.0')
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
			$query->where($db->quoteName('element') . ' = ' . $db->quote('com_quick2cart'));
			$db->setQuery($query);
			$data = $db->loadObject();

			$params = json_decode($data->params);

			if (!empty($params) && isset($params->bootstrap_version))
			{
				$query = $db->getQuery(true);
				$params->bootstrap_version = 'bs5';
				$fields = array($db->quoteName('params') . ' = ' . $db->quote(json_encode($params)));
				$conditions = array($db->quoteName('extension_id') . ' = ' . $data->extension_id);
				$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
}
