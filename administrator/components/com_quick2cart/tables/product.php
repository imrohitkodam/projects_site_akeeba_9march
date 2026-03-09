<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Access\Access;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\AbstractEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

if (JVERSION >= '4.0.0')
{
	/**
	 * JTable class for Region.
	 *
	 * @package     Quick2cart
	 * @subpackage  com_quick2cart
	 * @since       2.2
	 */
	class Quick2cartTableProduct extends Table implements VersionableTableInterface, TaggableTableInterface
	{
		use TaggableTableTrait;

		/**
		 * Name of the database table to model.
		 *
		 * @var    string
		 * @since  1.7.0
		 */
		protected $_tbl = '';

		/**
		 * Name of the primary key field in the table.
		 *
		 * @var    string
		 * @since  1.7.0
		 */
		protected $_tbl_key = '';

		/**
		 * Name of the primary key fields in the table.
		 *
		 * @var    array
		 * @since  3.0.1
		 */
		protected $_tbl_keys = array();

		/**
		 * DatabaseDriver object.
		 *
		 * @var    DatabaseDriver
		 * @since  1.7.0
		 */
		protected $_db;

		/**
		 * Should rows be tracked as ACL assets?
		 *
		 * @var    boolean
		 * @since  1.7.0
		 */
		protected $_trackAssets = false;

		/**
		 * The rules associated with this record.
		 *
		 * @var    Rules  A Rules object.
		 * @since  1.7.0
		 */
		protected $_rules;

		/**
		 * Indicator that the tables have been locked.
		 *
		 * @var    boolean
		 * @since  1.7.0
		 */
		protected $_locked = false;

		/**
		 * The UCM type alias. Used for tags, content versioning etc. Leave blank to effectively disable these features.
		 *
		 * @var    string
		 * @since  4.0.0
		 */
		public $typeAlias = null;

		/**
		 * Constructor
		 *
		 * @param   Joomla\Database\DatabaseDriver  $db  Database connector object
		 *
		 * @since 1.5
		 */
		public function __construct ($db)
		{
			$this->setColumnAlias('published', 'state');
			$this->typeAlias = 'com_quick2cart.product';
			parent::__construct('#__kart_items', 'item_id', $db);
		}

		/**
		 * Overloaded bind function
		 *
		 * @param   array  $array   Named array to bind
		 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
		 *
		 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
		 *
		 * @since   1.5
		 */
		public function bind ($array, $ignore = '')
		{
			if (isset($array['params']) && is_array($array['params']))
			{
				$registry = new Registry;
				$registry->loadArray($array['params']);
				$array['params'] = (string) $registry;
			}

			if (isset($array['metadata']) && is_array($array['metadata']))
			{
				$registry = new Registry;
				$registry->loadArray($array['metadata']);
				$array['metadata'] = (string) $registry;
			}

			if (!Factory::getUser()->authorise('core.admin', 'com_quick2cart.product.' . $array['item_id']))
			{
				$actions         = Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_quick2cart/access.xml', "/access/section[@name='product']/");
				$default_actions = Access::getAssetRules('com_quick2cart.product.' . $array['item_id'])->getData();
				$array_jaccess   = array();

				foreach ($actions as $action)
				{
					$array_jaccess[$action->name] = $default_actions[$action->name];
				}

				$array['rules'] = $this->JAccessRulestoArray($array_jaccess);
			}

			// Bind the rules for ACL where supported.
			if (isset($array['rules']) && is_array($array['rules']))
			{
				$this->setRules($array['rules']);
			}

			return parent::bind($array, $ignore);
		}

		/**
		 * This function convert an array of JAccessRule objects into an rules array.
		 *
		 * @param   type  $jaccessrules  an array of JAccessRule objects.
		 *
		 * @return  mixed  $rules  Set of rules
		 */
		private function JAccessRulestoArray ($jaccessrules)
		{
			$rules = array();

			foreach ($jaccessrules as $action => $jaccess)
			{
				$actions = array();

				foreach ($jaccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool) $allow);
				}

				$rules[$action] = $actions;
			}

			return $rules;
		}

		/**
		 * Overloaded check function
		 *
		 * @return  boolean
		 *
		 * @see     JTable::check
		 * @since   1.5
		 */
		public function check ()
		{
			// If there is an ordering column and this is a new row then get the
			// next ordering value
			if (property_exists($this, 'ordering') && $this->id == 0)
			{
				$this->ordering = self::getNextOrder();
			}

			return parent::check();
		}

		/**
		 * Define a namespaced asset name for inclusion in the #__assets table
		 *
		 * @return string The asset name
		 *
		 * @see JTable::_getAssetName
		 */
		protected function _getAssetName ()
		{
			$k = $this->_tbl_key;

			return 'com_quick2cart.product.' . (int) $this->$k;
		}

		/**
		 * Method to return the title to use for the asset table.
		 *
		 * @return  string
		 *
		 * @since   1.6
		 */
		protected function _getAssetTitle()
		{
			return $this->name;
		}

		/**
		 * Method to get the parent asset under which to register this one.
		 * By default, all assets are registered to the ROOT node with ID,
		 * which will default to 1 if none exists.
		 * The extended class can define a table and id to lookup.  If the
		 * asset does not exist it will be created.
		 *
		 * @param   Table   $table  A JTable object for the asset parent.
		 * @param   integer  $id     Id to look up
		 *
		 * @return  integer
		 *
		 * @since   11.1
		 */
		protected function _getAssetParentId (Table $table = null, $id = null)
		{
			// We will retrieve the parent-asset from the Asset-table
			$assetParent = Table::getInstance('Asset');

			// Default: if no asset-parent can be found we take the global asset
			$assetParentId = $assetParent->getRootId();

			// The item has the component as asset-parent
			$assetParent->loadByName('com_quick2cart');

			// Return the found asset-parent-id
			if ($assetParent->id)
			{
				$assetParentId = $assetParent->id;
			}

			return $assetParentId;
		}

		/**
		 * Method to delete a row from the database table by primary key value.
		 *
		 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
		 *
		 * @return  boolean  True on success.
		 *
		 * @link    http://docs.joomla.org/JTable/delete
		 * @since   11.1
		 * @throws  UnexpectedValueException
		 */
		public function delete ($pk = null)
		{
			$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

			if (!class_exists('productHelper'))
			{
				JLoader::register('productHelper', $path);
				JLoader::load('productHelper');
			}

			$productHelper = new productHelper;

			if (is_array($pk))
			{
				$status = false;

				foreach ($pk as $pkid)
				{
					$oneProdStatus = $productHelper->deleteWholeProduct($pkid);

					if ($oneProdStatus === true)
					{
						// If atleast one prod is deleted successfull and other not still reurn true.
						$status = true;
					}
				}
			}
			else
			{
				$status = $productHelper->deleteWholeProduct($pk);
			}

			return $status;
		}

		/**
		 * Method to send mail to stoew owner for approval
		 *
		 * @param   Object  $owner  store owner's store id
		 *
		 * @return  boolean  True on success.
		 *
		 * @since   1.0
		 */
		public function SendMailToOwnerAfterApproval($owner)
		{
			$comquick2cartHelper = new comquick2cartHelper;
			$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=category&layout=default');

			$app = Factory::getApplication();
			$fromname = $app->get('fromname');
			$sitename = $app->get('sitename');

			$subject = Text::_('COM_Q2C_PRODUCT_AAPROVED_SUBJECT');
			$subject = str_replace('{sellername}', $owner->name, $subject);

			$body = Text::_('COM_Q2C_PRODUCT_APPROVED_BODY');
			$body = str_replace('{name}', $owner->name, $body);
			$body = str_replace('{admin}', $fromname, $body);
			$body = str_replace('{link}', Uri::root() . 'index.php?option=com_quick2cart&view=category&layout=default&Itemid=' . $itemid, $body);
			$body = str_replace('{sitelink}', Uri::root(), $body);
			$body = str_replace('{sitename}', $sitename, $body);

			$res = $comquick2cartHelper->sendmail($owner->email, $subject, $body);

			return $res;
		}

		/**
		 * Overloaded store method for the order table.
		 *
		 * @param   boolean  $updateNulls  Toggle whether null values should be updated.
		 *
		 * @return  boolean  True on success, false on failure.
		 *
		 * @since   2.5.0
		 */
		public function store($updateNulls = false)
		{
			$db     = Factory::getDbo();
			$query  = $db->getQuery(true);
			$result = true;
			$k      = $this->_tbl_keys;

			if (JVERSION >= '4.0.0')
			{
				// Pre-processing by observers
				$event = AbstractEvent::create(
					'onTableBeforeStore',
					[
						'subject'     => $this,
						'updateNulls' => $updateNulls,
						'k'           => $k,
					]
				);

				$this->getDispatcher()->dispatch('onTableBeforeStore', $event);
			}
			else
			{
				// Implement \JObservableInterface: Pre-processing by observers
				$this->_observers->update('onBeforeStore', array($updateNulls, $k));
			}

			$currentAssetId = 0;

			if (!empty($this->asset_id))
			{
				$currentAssetId = $this->asset_id;
			}

			// The asset id field is managed privately by this class.
			if ($this->_trackAssets)
			{
				unset($this->asset_id);
			}

			// We have to unset typeAlias since updateObject / insertObject will try to insert / update all public variables...
			$typeAlias = $this->typeAlias;
			unset($this->typeAlias);

			try
			{
				// If a primary key exists update the object, otherwise insert it.
				if ($this->hasPrimaryKey())
				{
					$db->updateObject($this->_tbl, $this, $this->_tbl_keys, $updateNulls);
				}
				else
				{
					$db->insertObject($this->_tbl, $this, $this->_tbl_keys[0]);
				}
			}
			catch (\Exception $e)
			{
				$this->setError($e->getMessage());
				$result = false;
			}

			$this->typeAlias = $typeAlias;

			// If the table is not set to track assets return true.
			if ($this->_trackAssets)
			{
				if ($this->_locked)
				{
					$this->_unlock();
				}

				/*
				* Asset Tracking
				*/
				$parentId = $this->_getAssetParentId();
				$name     = $this->_getAssetName();
				$title    = $this->_getAssetTitle();

				/** @var  \JTableAsset  $asset */
				$asset = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
				$asset->loadByName($name);

				// Re-inject the asset id.
				$this->asset_id = $asset->id;

				// Check for an error.
				$error = $asset->getError();

				if ($error)
				{
					$this->setError($error);

					return false;
				}
				else
				{
					// Specify how a new or moved node asset is inserted into the tree.
					if (empty($this->asset_id) || $asset->parent_id != $parentId)
					{
						$asset->setLocation($parentId, 'last-child');
					}

					// Prepare the asset to be stored.
					$asset->parent_id = $parentId;
					$asset->name      = $name;
					$asset->title     = StringHelper::substr($title, 0, 100);

					if ($this->_rules instanceof Rules)
					{
						$asset->rules = (string) $this->_rules;
					}

					/*For storing table column value as Null (want to allow stock value as null also) so that made this change,
					entire function copied from /libraries/src/Table/Table.php only below single line change has made*/
					/*if (!$asset->check() || !$asset->store($updateNulls))*/
					if (!$asset->check() || !$asset->store())
					{
						$this->setError($asset->getError());

						return false;
					}
					else
					{
						// Create an asset_id or heal one that is corrupted.
						if (empty($this->asset_id) || ($currentAssetId != $this->asset_id && !empty($this->asset_id)))
						{
							// Update the asset_id field in this table.
							$this->asset_id = (int) $asset->id;

							$query = $db->getQuery(true)
								->update($db->quoteName($this->_tbl))
								->set('asset_id = ' . (int) $this->asset_id);
							$this->appendPrimaryKeys($query);
							$db->setQuery($query)->execute();
						}
					}
				}
			}

			// Post-processing by observers
			$event = AbstractEvent::create(
				'onTableAfterStore',
				[
					'subject' => $this,
					'result'  => &$result,
				]
			);
			$this->getDispatcher()->dispatch('onTableAfterStore', $event);

			return $result;
		}
		
		/**
		 * Get the type alias for UCM features
		 *
		 * @return  string  The alias as described above
		 *
		 * @since   4.0.0
		 */
		public function getTypeAlias()
		{
			return $this->typeAlias;
		}
	}
}
else
{
	class Quick2cartTableProduct extends Table
	{
		public $_observers;

		/**
		 * Constructor
		 *
		 * @param   JDatabaseDriver  &$_db  Database connector object
		 *
		 * @since 1.5
		 */
		public function __construct (&$_db)
		{
			$this->_observers = new JObserverUpdater($this);
			JObserverMapper::attachAllObservers($this);
			JObserverMapper::addObserverClassToClass('JTableObserverTags', 'Quick2cartTableProduct', array('typeAlias' => 'com_quick2cart.product'));
			parent::__construct('#__kart_items', 'item_id', $_db);
		}

		/**
		 * Overloaded bind function
		 *
		 * @param   array  $array   Named array to bind
		 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
		 *
		 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
		 *
		 * @since   1.5
		 */
		public function bind ($array, $ignore = '')
		{
			if (isset($array['params']) && is_array($array['params']))
			{
				$registry = new Registry;
				$registry->loadArray($array['params']);
				$array['params'] = (string) $registry;
			}

			if (isset($array['metadata']) && is_array($array['metadata']))
			{
				$registry = new Registry;
				$registry->loadArray($array['metadata']);
				$array['metadata'] = (string) $registry;
			}

			if (!Factory::getUser()->authorise('core.admin', 'com_quick2cart.product.' . $array['item_id']))
			{
				$actions         = Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_quick2cart/access.xml', "/access/section[@name='product']/");
				$default_actions = Access::getAssetRules('com_quick2cart.product.' . $array['item_id'])->getData();
				$array_jaccess   = array();

				foreach ($actions as $action)
				{
					$array_jaccess[$action->name] = $default_actions[$action->name];
				}

				$array['rules'] = $this->JAccessRulestoArray($array_jaccess);
			}

			// Bind the rules for ACL where supported.
			if (isset($array['rules']) && is_array($array['rules']))
			{
				$this->setRules($array['rules']);
			}

			return parent::bind($array, $ignore);
		}

		/**
		 * This function convert an array of JAccessRule objects into an rules array.
		 *
		 * @param   type  $jaccessrules  an array of JAccessRule objects.
		 *
		 * @return  mixed  $rules  Set of rules
		 */
		private function JAccessRulestoArray ($jaccessrules)
		{
			$rules = array();

			foreach ($jaccessrules as $action => $jaccess)
			{
				$actions = array();

				foreach ($jaccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool) $allow);
				}

				$rules[$action] = $actions;
			}

			return $rules;
		}

		/**
		 * Overloaded check function
		 *
		 * @return  boolean
		 *
		 * @see     JTable::check
		 * @since   1.5
		 */
		public function check ()
		{
			// If there is an ordering column and this is a new row then get the
			// next ordering value
			if (property_exists($this, 'ordering') && $this->id == 0)
			{
				$this->ordering = self::getNextOrder();
			}

			return parent::check();
		}

		/**
		 * Method to set the publishing state for a row or list of rows in the database
		 * table.  The method respects checked out rows by other users and will attempt
		 * to checkin rows that it can after adjustments are made.
		 *
		 * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
		 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published, 2=archived, -2=trashed]
		 * @param   integer  $userId  The user id of the user performing the operation.
		 *
		 * @return  boolean  True on success.
		 *
		 * @since   1.6
		 */
		public function publish ($pks = null, $state = 1, $userId = 0)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			// Initialise variables.
			$k = $this->_tbl_key;

			// Sanitize input.
			ArrayHelper::toInteger($pks);
			$userId = (int) $userId;
			$state  = (int) $state;

			// If there are no primary keys set check to see if the instance key is
			// set.
			if (empty($pks))
			{
				if ($this->$k)
				{
					$pks = array($this->$k);
				}
				// Nothing to set publishing state on, return false.
				else
				{
					$this->setError(Text::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

					return false;
				}
			}

			// Build the WHERE clause for the primary keys.
			$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

			// Determine if there is checkin support for the table.
			if (!property_exists($this, 'checked_out') || ! property_exists($this, 'checked_out_time'))
			{
				$checkin = '';
			}
			else
			{
				$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
			}

			if (is_array($pks))
			{
				$params         = ComponentHelper::getParams('com_quick2cart');
				$admin_approval = (int) $params->get('admin_approval');

				foreach ($pks as $pk)
				{
					$query = $db->getQuery(true);

					// Update the state flag
					$query->update($db->quoteName('#__kart_items'))
						->set($db->quoteName('state') . ' = ' . $state)
						->where($db->quoteName('item_id') . ' = ' . $pk);
					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (\RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return false;
					}

					if ($state == 1)
					{
						// If admin approval is on for products
						if ($admin_approval === 1)
						{
							$query = $db->getQuery(true);
							$query = "SELECT DISTINCT u.email, u.name, u.username, i.item_id, i.name
							FROM #__users AS u,
							#__kart_items AS i,
							#__kart_store AS s
							WHERE u.id = s.owner
							AND s.id = i.store_id
							AND i.item_id = " . $pk;

							$db->setQuery($query);
							$owner = $db->loadObject();

							$this->SendMailToOwnerAfterApproval($owner);
						}
					}
				}
			}

			// If checkin is supported and all rows were adjusted, check them in.
			if ($checkin && (count($pks) == $db->getAffectedRows()))
			{
				// Checkin each row.
				foreach ($pks as $pk)
				{
					$this->checkin($pk);
				}
			}

			// If the JTable instance value is in the list of primary keys that were
			// set, set the instance.
			if (in_array($this->$k, $pks))
			{
				$this->state = $state;
			}

			$this->setError('');

			return true;
		}

		/**
		 * Define a namespaced asset name for inclusion in the #__assets table
		 *
		 * @return string The asset name
		 *
		 * @see JTable::_getAssetName
		 */
		protected function _getAssetName ()
		{
			$k = $this->_tbl_key;

			return 'com_quick2cart.product.' . (int) $this->$k;
		}

		/**
		 * Method to get the parent asset under which to register this one.
		 * By default, all assets are registered to the ROOT node with ID,
		 * which will default to 1 if none exists.
		 * The extended class can define a table and id to lookup.  If the
		 * asset does not exist it will be created.
		 *
		 * @param   JTable   $table  A JTable object for the asset parent.
		 * @param   integer  $id     Id to look up
		 *
		 * @return  integer
		 *
		 * @since   11.1
		 */
		protected function _getAssetParentId (Table $table = null, $id = null)
		{
			// We will retrieve the parent-asset from the Asset-table
			$assetParent = Table::getInstance('Asset');

			// Default: if no asset-parent can be found we take the global asset
			$assetParentId = $assetParent->getRootId();

			// The item has the component as asset-parent
			$assetParent->loadByName('com_quick2cart');

			// Return the found asset-parent-id
			if ($assetParent->id)
			{
				$assetParentId = $assetParent->id;
			}

			return $assetParentId;
		}

		/**
		 * Method to delete a row from the database table by primary key value.
		 *
		 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
		 *
		 * @return  boolean  True on success.
		 *
		 * @link    http://docs.joomla.org/JTable/delete
		 * @since   11.1
		 * @throws  UnexpectedValueException
		 */
		public function delete ($pk = null)
		{
			$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

			if (!class_exists('productHelper'))
			{
				JLoader::register('productHelper', $path);
				JLoader::load('productHelper');
			}

			$productHelper = new productHelper;

			if (is_array($pk))
			{
				$status = false;

				foreach ($pk as $pkid)
				{
					$oneProdStatus = $productHelper->deleteWholeProduct($pkid);

					if ($oneProdStatus === true)
					{
						// If atleast one prod is deleted successfull and other not still reurn true.
						$status = true;
					}
				}
			}
			else
			{
				$status = $productHelper->deleteWholeProduct($pk);
			}

			return $status;
		}

		/**
		 * Method to send mail to stoew owner for approval
		 *
		 * @param   Object  $owner  store owner's store id
		 *
		 * @return  boolean  True on success.
		 *
		 * @since   1.0
		 */
		public function SendMailToOwnerAfterApproval($owner)
		{
			$comquick2cartHelper = new comquick2cartHelper;
			$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=category&layout=default');

			$app = Factory::getApplication();
			$fromname = $app->get('fromname');
			$sitename = $app->get('sitename');

			$subject = JText::_('COM_Q2C_PRODUCT_AAPROVED_SUBJECT');
			$subject = str_replace('{sellername}', $owner->name, $subject);

			$body = JText::_('COM_Q2C_PRODUCT_APPROVED_BODY');
			$body = str_replace('{name}', $owner->name, $body);
			$body = str_replace('{admin}', $fromname, $body);
			$body = str_replace('{link}', Uri::root() . 'index.php?option=com_quick2cart&view=category&layout=default&Itemid=' . $itemid, $body);
			$body = str_replace('{sitelink}', Uri::root(), $body);
			$body = str_replace('{sitename}', $sitename, $body);

			$res = $comquick2cartHelper->sendmail($owner->email, $subject, $body);

			return $res;
		}

		/**
		 * Overloaded store method for the order table.
		 *
		 * @param   boolean  $updateNulls  Toggle whether null values should be updated.
		 *
		 * @return  boolean  True on success, false on failure.
		 *
		 * @since   __DEPLOY_VERSION__
		 */
		public function store($updateNulls = false)
		{
			$db     = Factory::getDbo();
			$query  = $db->getQuery(true);
			$result = true;
			$k      = $this->_tbl_keys;

			// Implement \JObservableInterface: Pre-processing by observers
			$this->_observers->update('onBeforeStore', array($updateNulls, $k));

			$currentAssetId = 0;

			if (!empty($this->asset_id))
			{
				$currentAssetId = $this->asset_id;
			}

			// The asset id field is managed privately by this class.
			if ($this->_trackAssets)
			{
				unset($this->asset_id);
			}

			// If a primary key exists update the object, otherwise insert it.
			if ($this->hasPrimaryKey())
			{
				$db->updateObject($this->_tbl, $this, $this->_tbl_keys, $updateNulls);
			}
			else
			{
				$db->insertObject($this->_tbl, $this, $this->_tbl_keys[0]);
			}

			// If the table is not set to track assets return true.
			if ($this->_trackAssets)
			{
				if ($this->_locked)
				{
					$this->_unlock();
				}

				/*
				* Asset Tracking
				*/
				$parentId = $this->_getAssetParentId();
				$name     = $this->_getAssetName();
				$title    = $this->_getAssetTitle();

				/** @var  \JTableAsset  $asset */
				$asset = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
				$asset->loadByName($name);

				// Re-inject the asset id.
				$this->asset_id = $asset->id;

				// Check for an error.
				$error = $asset->getError();

				if ($error)
				{
					$this->setError($error);

					return false;
				}
				else
				{
					// Specify how a new or moved node asset is inserted into the tree.
					if (empty($this->asset_id) || $asset->parent_id != $parentId)
					{
						$asset->setLocation($parentId, 'last-child');
					}

					// Prepare the asset to be stored.
					$asset->parent_id = $parentId;
					$asset->name      = $name;
					$asset->title     = $title;

					if ($this->_rules instanceof \JAccessRules)
					{
						$asset->rules = (string) $this->_rules;
					}

					/*For storing table column value as Null (want to allow stock value as null also) so that made this change,
					entire function copied from /libraries/src/Table/Table.php only below single line change has made*/
					/*if (!$asset->check() || !$asset->store($updateNulls))*/
					if (!$asset->check() || !$asset->store())
					{
						$this->setError($asset->getError());

						return false;
					}
					else
					{
						// Create an asset_id or heal one that is corrupted.
						if (empty($this->asset_id) || ($currentAssetId != $this->asset_id && !empty($this->asset_id)))
						{
							// Update the asset_id field in this table.
							$this->asset_id = (int) $asset->id;

							$query = $db->getQuery(true)
								->update($db->quoteName($this->_tbl))
								->set('asset_id = ' . (int) $this->asset_id);
							$this->appendPrimaryKeys($query);
							$db->setQuery($query)->execute();
						}
					}
				}
			}

			// Implement \JObservableInterface: Post-processing by observers
			$this->_observers->update('onAfterStore', array(&$result));

			return $result;
		}
	}
}
