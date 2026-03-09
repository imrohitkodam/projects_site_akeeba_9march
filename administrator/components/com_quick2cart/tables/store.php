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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

/**
 * JTable class for Region.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartTableStore extends Table
{
	/**
	 * Constructor
	 *
	 * @param   Joomla\Database\DatabaseDriver  $_db  Database connector object
	 *
	 * @since 1.5
	 */
	public function __construct ($_db)
	{
		parent::__construct('#__kart_store', 'id', $_db);
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

		if (! Factory::getUser()->authorise('core.admin', 'com_quick2cart.store.' . $array['id']))
		{
			$actions = Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_quick2cart/access.xml', "/access/section[@name='store']/");
			$default_actions = Access::getAssetRules('com_quick2cart.store.' . $array['id'])->getData();
			$array_jaccess = array();

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
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;

		// If there are no primary keys set check to see if the instance key is
		// set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array(
					$this->$k
				);
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
		if (! property_exists($this, 'checked_out') || ! property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		if (is_array($pks))
		{
			$params = ComponentHelper::getParams('com_quick2cart');
			$admin_approval_stores = (int) $params->get('admin_approval_stores');

			foreach ($pks as $pk)
			{
				$query = $this->_db->getQuery(true);

				// Update the state flag
				$query->update($this->_db->quoteName('#__kart_store'))
					->set($this->_db->quoteName('live') . ' = ' . $state)
					->where($this->_db->quoteName('id') . ' = ' . $pk);

				$this->_db->setQuery($query);

				// Check for a database error.
				try
				{
					$this->_db->execute();
				}
				catch (\RuntimeException $e)
				{
					$this->setError($e->getMessage());

					return false;
				}

				if ($state == 1)
				{
					// If admin approval is on for stores
					if ($admin_approval_stores === 1)
					{
						$query = $this->_db->getQuery(true);
						$query->select('*')
						->from('#__kart_store')
						->where($this->_db->quoteName('id') . ' = ' . $pk);

						$this->_db->setQuery($query);
						$details = $this->_db->loadObject();

						$sendMailOnApproval = $this->sendMailToOwnerOnStoreApproval($details);
					}
				}
			}
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
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

		return 'com_quick2cart.store.' . (int) $this->$k;
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
		$storeHelper = new storeHelper;

		if (is_array($pk))
		{
			foreach ($pk as $pkid)
			{
				$entryStatus = $storeHelper->isAllowedToDelStore($pkid);

				if ($entryStatus === true)
				{
					$query = $this->_db->getQuery(true);

					// @TODO use query builder
					$query = 'DELETE
					 FROM `#__kart_role`, `#__kart_store`
					 USING `#__kart_store`
					 INNER JOIN `#__kart_role`
					 WHERE `#__kart_store`.id = `#__kart_role`.store_id
					 AND `#__kart_store`.id = ' . $pkid;

					$this->_db->setQuery($query);

					try
					{
						$this->_db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return false;
					}
				}
			}
		}
		else
		{
			$entryStatus = $storeHelper->isAllowedToDelStore($pk);

			if ($entryStatus === true)
			{
				$query = $this->_db->getQuery(true);

				// @TODO use query builder
				$query = 'DELETE
				 FROM `#__kart_role`, `#__kart_store`
				 USING `#__kart_store`
				 INNER JOIN `#__kart_role`
				 WHERE `#__kart_store`.id = `#__kart_role`.store_id
				 AND `#__kart_store`.id = ' . $pk;

				$this->_db->setQuery($query);

				try
				{
					$this->_db->execute();
				}
				catch (RuntimeException $e)
				{
					$this->setError($e->getMessage());

					return false;
				}
			}
			else
			{
				return false;
			}
		}

		return true;
	}

	/** Send mail to owner on store approval
	 *
	 * @param   Object  $store  store details
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function sendMailToOwnerOnStoreApproval($store)
	{
		$app      = Factory::getApplication();
		$sitename = $app->get('sitename');

		$subject = Text::_('COM_QUICK2CART_STORE_APPROVED_SUBJECT');
		$subject = str_replace('{storename}', $store->title, $subject);
		$subject = str_replace('{sitename}', $sitename, $subject);

		$body = Text::_('COM_QUICK2CART_STORE_APPROVED_BODY');
		$body = str_replace('{sitename}', $sitename, $body);
		$body = str_replace('{storename}', $store->title, $body);

		$comquick2cartHelper = new comquick2cartHelper;
		$result = $comquick2cartHelper->sendmail($store->store_email, $subject, $body);

		return $result;
	}
}
