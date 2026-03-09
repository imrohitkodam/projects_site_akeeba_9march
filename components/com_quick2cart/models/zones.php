<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
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
class Quick2cartModelZones extends ListModel
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
				'zone_id', 'a.id',
				'name', 'a.name',
				'store_id', 'a.store_id',
				'state', 'a.state',
				'ordering', 'a.ordering',
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

		// Load the store id.
		$sel_store = $this->getUserStateFromRequest($this->context . '.filter.sel_store', 'filter_store');
		$this->setState('filter.sel_store', $sel_store);

		// Load the filter search.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the filter ppublsihed.
		$published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_quick2cart');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'desc');
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
	 * @return   string  A store id.
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
		// Create a new query object.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__kart_zone` AS a');
		$query->select("s.title");
		$query->JOIN("LEFT", '#__kart_store AS s ON s.id = a.store_id');

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

		$filter_store = (int) $this->getState('filter.sel_store');

		if ($filter_store)
		{
			$query->where('a.store_id = ' . $filter_store);
		}
		else
		{
			// Getting user accessible store ids
			$storeHelper = new storeHelper;

			// Get all stores.
			$user      = Factory::getUser();
			$storeList = $storeHelper->getUserStore($user->id);
			$storeIds  = array();

			foreach ($storeList as $store)
			{
				$storeIds[] = $store['id'];
			}

			$accessibleStoreIds = '';

			// Make string
			if (!empty($storeIds))
			{
				$accessibleStoreIds = implode(',', $storeIds);
				$query->where('(a.store_id IN (' . $accessibleStoreIds . '))');
			}
			else
			{
				$query->where('a.store_id = -1');
			}
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
				$query->where('(a.name LIKE ' . $search .
				' OR s.title LIKE ' . $search .
				')');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

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
	 * This function manage items published or unpublished state
	 *
	 * @param   array  $items  delelte taxrate ids.
	 * @param   int    $state  1 for publish and 0 for unpublish.
	 *
	 * @since   2.2
	 * @return  void
	 */
	public function setItemState($items, $state)
	{
		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$db = Factory::getDBO();
				$query = "UPDATE #__kart_zone SET state=" . $state . " WHERE id=" . $id;
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
			}
		}

		return true;
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   array  $items  An array of primary key value to delete.
	 *
	 * @return   int  Returns count of success
	 */
	public function delete($items)
	{
		$db   = Factory::getDBO();
		$app  = Factory::getApplication();

		// Load tables
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

		$path = JPATH_SITE . '/components/com_quick2cart/helpers/zoneHelper.php';

		if (!class_exists('zoneHelper'))
		{
			JLoader::register('zoneHelper', $path);
			JLoader::load('zoneHelper');
		}

		$zoneHelper = new zoneHelper;

		if (is_array($items))
		{
			$successCount = 0;

			foreach ($items as $id)
			{
				// Check whether zone is allowed to delete or not.  If not the enqueue error message accordingly.
				$count_id  = $zoneHelper->isAllowedToDelZone($id);
				$zoneTable = Table::getInstance('zone', 'Quick2cartTable', array('dbo', $db));
				$zoneTable->load(array('id' => $id));
				$data      = $zoneTable->getProperties();

				// On before zone delete
				PluginHelper::importPlugin("actionlog");
				$app->triggerEvent("onBeforeQ2cDeleteZone", array($data));

				if ($count_id === true)
				{
					if ($zoneTable->delete($data['id']))
					{
						// On after zone delete
						PluginHelper::importPlugin("actionlog");
						$app->triggerEvent("onAfterQ2cDeleteZone", array($data));

						// For enqueue success msg along with error msg.
						$successCount++;
					}
				}
			}

			return $successCount;
		}
	}
}
