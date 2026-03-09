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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Methods supporting a list of stores records.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelStores extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'title', 'a.description',
				'vendor_name', 'u.name',
				'published', 'a.published',
				'email', 'a.store_email',
				'telephone', 'a.phone'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_quick2cart');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$db    = Factory::getDbo();
		$user  = Factory::getUser();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState('list.select',
			' DISTINCT(a.`id`), a.`owner`, a.`title`, a.`description`, a.`address`, a.`phone`' .
			', a.`store_email`, a.`store_avatar`, a.`fee`, a.`live` AS published, a.`cdate`, a.`mdate`' .
			', a.`extra`, a.`company_name`, a.`vanityurl`'
			)
		);
		$query->from('`#__kart_store` AS a');

		// Show only logged in user's stores
		$query->where('a.owner=' . $user->id);
		$query->select(' u.`username`, u.`name`, u.`email`');
		$query->join('LEFT', '`#__users` AS u ON a.owner=u.id');
		$query->select(' r.`role`');
		$query->join('LEFT', '`#__kart_role` AS r ON r.store_id=a.id');
		$query->where('r.user_id=' . $user->id);

		// Filter by published state.
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.live = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.live IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.title LIKE ' . $search .
					' OR  u.username LIKE ' . $search .
					' OR  u.name LIKE ' . $search .
					' OR  a.store_email LIKE ' . $search . ' )'
				);
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get a list of stores.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		$items = parent::getItems();

		// Get store roles
		$comquick2cartHelper = new comquick2cartHelper;

		foreach ($items as $item)
		{
			$item->role = $comquick2cartHelper->getRole($item->role);
		}

		return $items;
	}

	/**
	 * Method to set model state variables
	 *
	 * @param   array    $items  The array of record ids.
	 * @param   integer  $state  The value of the property to set or null.
	 *
	 * @return  integer  The number of records updated.
	 *
	 * @since   2.2
	 */
	public function setItemState($items, $state)
	{
		$db                  = Factory::getDbo();
		$app                 = Factory::getApplication();
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$user      = Factory::getUser();
		$isAdmin    = $user->authorise('core.admin');

		if ($state === 1)
		{
			$params                = ComponentHelper::getParams('com_quick2cart');
			$admin_approval_stores = (int) $params->get('admin_approval_stores');

			// If admin approval is on for stores
			if ($admin_approval_stores === 1 && !$isAdmin)
			{
				$app->enqueueMessage(Text::_('COM_QUICK2CART_ERR_MSG_ADMIN_APPROVAL_NEEDED_STORES'), 'error');

				return 0;
			}
		}

		$count = 0;

		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$storeOwner  = $storeHelper->getStoreOwner($id);
				$canSetState = $comquick2cartHelper->checkOwnership($storeOwner);

				// Checked if the user can set the state
				if ($canSetState)
				{
					$query = $db->getQuery(true);

					// Update the reset flag
					$query->update($db->quoteName('#__kart_store'));
					$query->set($db->quoteName('live') . ' = ' . $state);
					$query->where($db->quoteName('id') . ' = ' . $id);

					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return 0;
					}

					$count++;
				}
				else
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
				}
			}
		}

		return $count;
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   array  $items  An array of primary key value to delete.
	 *
	 * @return  int  Returns count of success
	 */
	public function delete($items)
	{
		$app   = Factory::getApplication();
		$db    = Factory::getDbo();
		$count = 0;
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;

		// Load tables
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$storeOwner = $storeHelper->getStoreOwner($id);
				$canDelete  = $comquick2cartHelper->checkOwnership($storeOwner);

				// Checked if the user can delete the store
				if ($canDelete)
				{
					$storeTable = Table::getInstance('Store', 'Quick2cartTable', array('dbo', $db));
					$storeTable->load(array('id' => $id));
					$data = $storeTable->getProperties();

					// On before store delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onBeforeQ2cDeleteStore", array($data));

					$query = $db->getQuery(true);
					$conditions = array(
					$db->quoteName('#__kart_store.id') . ' = ' . $db->quoteName('#__kart_role.store_id'),
					$db->quoteName('#__kart_store.id') . ' IN (' . $db->quote($id) . ' )');
					$query->delete($db->quoteName('#__kart_role'));
					$query->delete($db->quoteName('#__kart_store') . ' USING ' . $db->quoteName('#__kart_store'));
					$query->join('INNER', $db->quoteName('#__kart_role'));
					$query->where($conditions);
					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return 0;
					}

					// On after store delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onAfterQ2cDeleteStore", array($data));

					$count++;
				}
				else
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
				}
			}
		}

		return $count;
	}
}
