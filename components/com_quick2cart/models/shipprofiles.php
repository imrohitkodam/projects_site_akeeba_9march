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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Methods supporting a list of Quick2cart records.
 *
 * @since  2.2
 **/
class Quick2cartModelShipprofiles extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id','a.id',
				'name','a.name',
				'store_id','a.store_id',
				'state','a.state',
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
	protected function populateState($ordering = 'id', $direction = 'desc')
	{
		// Initialise variables.
		$app = Factory::getApplication('site');

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'INT');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		// Load the filter search.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'STRING');
		$this->setState('filter.search', $search);

		// Load the store filter
		$storeFilter = $app->getUserStateFromRequest($this->context . '.filter_store', 'filter_store', '', 'INT');
		$this->setState('filter.stores', $storeFilter);

		// Load the filter ppublsihed.
		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '');
		$published = (!empty($published)) ? (int) $published : $published;
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_quick2cart');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($ordering, $direction);
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
		$storeHelper = new storeHelper;

		// Get all stores.
		$user      = Factory::getUser();
		$storeList = $storeHelper->getUserStore($user->id);
		$storeIds  = array();

		foreach ($storeList as $store)
		{
			$storeIds[] = $store['id'];
		}

		$accessibleStoreIds = (!empty($storeIds)) ? implode(',', $storeIds) : '';

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*, s.title as store_title'));
		$query->from('`#__kart_shipprofile` AS a');
		$query->join('INNER', '`#__kart_store` AS s ON s.id=a.store_id');
		$query->where('(a.store_id IN (' . $accessibleStoreIds . '))');

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
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
				$query->where('( a.name LIKE ' . $search . ' OR s.title LIKE ' . $search . ')');
			}
		}

		// Store filter
		$storeFilter = $this->getState('filter.stores');

		if (!empty($storeFilter))
		{
			$query->where('s.id =' . $storeFilter);
		}

		$orderCol  = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Method to get a list of zones.
	 * Overridden to add a check for access levels.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
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
		$db    = $this->getDbo();
		$app   = Factory::getApplication();
		$count = 0;

		// Load tables
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$zoneShippingProfileTable = Table::getInstance('shipprofile', 'Quick2cartTable', array('dbo', $db));
				$zoneShippingProfileTable->load(array('id' => $id));
				$data = $zoneShippingProfileTable->getProperties();

				// On before shipping profile delete
				PluginHelper::importPlugin("actionlog");
				$app->triggerEvent("onBeforeQ2cDeleteShippingProfile", array($data));

				if ($zoneShippingProfileTable->delete($data['id']))
				{
					// On after shipping profile delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onAfterQ2cDeleteShippingProfile", array($data));

					// For enqueue success msg along with error msg.
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * Render view.
	 *
	 * @param   string  $type    An optional associative array of configuration settings.
	 * @param   string  $prefix  An optional associative array of configuration settings.
	 * @param   array   $config  An optional associative array of configuration settings.
	 *
	 * @since   2.2
	 * @return   null
	 */

	public function getTable($type = 'shipprofile', $prefix = 'Quick2cartTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * This function manage items published or unpublished state
	 *
	 * @param   array  $items  delelte taxrate ids.
	 * @param   int    $state  1 for publish and 0 for unpublish.
	 *
	 * @since   2.2
	 * @return  void
	 */
	public function setItemState( $items, $state)
	{
		if (is_array($items))
		{
			$taxreate_ids = implode(',', $items);
			$db           = Factory::getDbo();
			$query        = $db->getQuery(true);

			// Fields to update.
			$fields = array($db->quoteName('state') . ' =' . $state);

			// Conditions for which records should be updated.
			$conditions = array($db->quoteName('id') . '  IN (' . $taxreate_ids . ')',);
			$query->update($db->quoteName('#__kart_shipprofile'))->set($fields)->where($conditions);
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch(\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}

		return true;
	}
}
