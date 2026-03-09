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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Methods supporting a list of Quick2cart records.
 *
 * @since  2.2
 **/
class Quick2cartModelTaxrates extends ListModel
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
				'taxrate_id', 'a.taxrate_id',
				'name', 'a.name',
				'percentage', 'a.percentage',
				'zone_id', 'a.zone_id',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'created_by', 'a.created_by',
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
		$app = Factory::getApplication('site');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_quick2cart');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
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
		$comquick2cartHelper = new comquick2cartHelper;

		// Getting user accessible store ids
		$storeList          = $comquick2cartHelper->getStoreIds();
		$storeIds           = array();

		foreach ($storeList as $store)
		{
			$storeIds[] = $store['store_id'];
		}

		$accessibleStoreIds = (!empty($storeIds)) ? implode(',', $storeIds) : '';

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__kart_taxrates` AS a');
		$query->select('z.name as zonename', 'z.store_id');
		$query->select('s.title');
		$query->join('LEFT', '#__kart_zone AS z ON z.id = a.zone_id');
		$query->join('INNER', '#__kart_store AS s ON s.id = z.store_id');
		$query->where('(z.store_id IN (' . $accessibleStoreIds . '))');

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
				$query->where('( a.name LIKE ' . $search . ' OR  a.zone_id LIKE ' . $search .
				' OR s.title LIKE ' . $search .
				' OR z.name LIKE ' . $search .
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
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   array  $items  An array of primary key value to delete.
	 *
	 * @return  int  Returns count of success
	 */
	public function delete($items)
	{
		$db           = Factory::getDBO();
		$successCount = 0;
		$app          = Factory::getApplication();

		// Load tables
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

		if (is_array($items))
		{
			$taxHelper = new taxHelper;

			foreach ($items as $id)
			{
				// Ownership checks for taxrates
				if ($this->isOwner($id) === true)
				{
					// Check whether zone is allowed to delete or not.  If not the enqueue error message accordingly.
					$count_id     = $taxHelper->isAllowedToDelTaxrate($id);
					$taxRateTable = Table::getInstance('TaxRate', 'Quick2cartTable', array('dbo', $db));
					$taxRateTable->load(array('id' => $id));
					$data         = $taxRateTable->getProperties();

					// On before tax rate delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onBeforeQ2cDeleteTaxRate", array($data));

					if ($count_id === true)
					{
						if ($taxRateTable->delete($data['id']))
						{
							// On after tax rate delete
							PluginHelper::importPlugin("actionlog");
							$app->triggerEvent("onAfterQ2cDeleteTaxRate", array($data));

							// For enqueue success msg along with error msg.
							$successCount++;
						}
					}
				}
				else
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
				}
			}
		}

		return $successCount;
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
		$db              = Factory::getDBO();
		$validTaxRateIds = array();

		if (is_array($items))
		{
			// Taxrate Ids running through an owner check
			foreach ($items as $item)
			{
				// Ownership checks for taxrates
				if ($this->isOwner($item) === true)
				{
					$validTaxRateIds[] = $item;
				}
				else
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
				}
			}

			$taxreate_ids = implode(',', $validTaxRateIds);
			$query        = $db->getQuery(true);
			$fields       = array($db->quoteName('state') . ' =' . $state);
			$conditions   = array($db->quoteName('id') . '  IN (' . $taxreate_ids . ')',);
			$query->update($db->quoteName('#__kart_taxrates'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();

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

		return true;
	}

	/**
	 * This function checks if the logged in user is its owner
	 *
	 * @param   integer  $taxRateId  tax rate id
	 *
	 * @since   2.9.9
	 *
	 * @return  boolean
	 */
	public function isOwner($taxRateId)
	{
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_quick2cart/models', 'taxrateForm');
		$taxRateModel        = BaseDatabaseModel::getInstance('taxrateForm', 'Quick2cartModel');
		$taxRateData         = $taxRateModel->getData($taxRateId);
		$zoneModel           = BaseDatabaseModel::getInstance('Zone', 'Quick2cartModel');
		$zoneData            = $zoneModel->getItem($taxRateData->zone_id);
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$storeOwner          = $storeHelper->getStoreOwner($zoneData->store_id);
		$canDelete           = $comquick2cartHelper->checkOwnership($storeOwner);

		return $canDelete;
	}
}
