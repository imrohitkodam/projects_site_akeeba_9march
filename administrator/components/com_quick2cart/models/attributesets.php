<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Methods supporting a list of Quick2cart records.
 *
 * @since  2.5
 *
 */
class Quick2cartModelAttributesets extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   2.5
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'state', 'a.state',
				'global_attribute_ids', 'a.global_attribute_ids',
				'global_attribute_set_name', 'a.global_attribute_set_name',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   STRING  $ordering   record ordering
	 *
	 * @param   STRING  $direction  record direction
	 *
	 * @return null
	 *
	 * @since  2.5
	 */
	protected function populateState($ordering = 'a.id', $direction = 'DESC')
	{
		$app = Factory::getApplication('administrator');

		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', null, 'array'))
		{
			foreach ($filters as $name => $value)
			{
				$this->setState('filter.' . $name, $value);
			}
		}

		// Load the parameters.
		$params = ComponentHelper::getParams('com_quick2cart');
		$this->setState('params', $params);

		parent::populateState($ordering, $direction);
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
	 * @since   2.5
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
	 * @return	JDatabaseQuery
	 *
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'DISTINCT a.*'));
		$query->from('`#__kart_global_attribute_set` AS a');

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
				$query->where('( a.global_attribute_set_name LIKE ' . $search .
				' )');
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
	 * Method to get a records.
	 *
	 * @return  mixed	Object on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}

	/**
	 * Function to delete attribute sets
	 *
	 * @return  null
	 *
	 * @since  2.5
	 *
	 * */
	public function delete()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');

		if (!empty($cid))
		{
			foreach ($cid as $attributeSetId)
			{
				$count_products = $this->checkForProductsInAttributeSetId($attributeSetId);

				if (!empty($count_products))
				{
					$app->enqueueMessage(sprintf(Text::_('COM_QUICK2CART_ATTRIBUTE_SET_DELETE_ERROR'), $attributeSetId, implode(',', $count_products)), 'Error');
				}
				else
				{
					return true;
				}
			}
		}

		$app->redirect("index.php?option=com_quick2cart&view=attributesets");
	}

	/**
	 * Function to check if products present for perticulat attribute set
	 *
	 * @param   Int  $attributeSetId  attribute set id
	 *
	 * @return  null|array
	 *
	 * @since  2.5
	 */
	public function checkForProductsInAttributeSetId($attributeSetId)
	{
		$attributesetModel = BaseDatabaseModel::getInstance('Attributeset', 'Quick2cartModel');
		$categorys         = $attributesetModel->getCategorysForAttributeSet($attributeSetId);

		if (!empty($categorys))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('a.item_id');
			$query->from($db->quoteName('#__kart_items', 'a'));
			$query->where($db->quoteName('a.category') . ' IN (' . implode(',', $categorys) . ')');

			$db->setQuery($query);
			$count = $db->loadColumn();
		}

		return $count;
	}
}
