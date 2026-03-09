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
defined('_JEXEC') or die(';)');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Pagination\Pagination;

/**
 * Store model class.
 *
 * @package  Quick2cart
 * @since    2.7
 */
class Quick2cartModelstore extends BaseDatabaseModel
{
	/**
	 * Class constructor.
	 *
	 * @since   2.7
	 */
	public function __construct()
	{
		parent::__construct();

		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$store_cat = $jinput->get('store_cat', '', 'INTEGER');

		$this->setState('store_cat', $store_cat);

		// Get component limit for pagination
		$params = ComponentHelper::getParams('com_quick2cart');

		// AllCCK for store home page
		$comp_limit = $params->get('all_prod_pagination_limit');

		// Get the pagination request variables
		$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $comp_limit, 'int');
		$limitstart = $jinput->get('limitstart', 0, '', 'int');

		// Set the limit variable for query later on
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Function _buildQuery
	 *
	 * @param   Integer  $store_id       Store_id
	 * @param   String   $client         Client
	 * @param   Integer  $fetchFeatured  fetch Featured if set to 1 otherwise fetch all product which are not featured
	 *
	 * @return  query
	 *
	 * @since   1.8
	 */
	public function _buildQuery($store_id = '', $client = '', $fetchFeatured = 1)
	{
		$where = $this->_buildContentWhere($store_id, $client, $fetchFeatured);
		$query = "SELECT a.*  from #__kart_items as a" . $where . ' ORDER BY a.item_id DESC';

		return $query;
	}

	/**
	 * Function _buildContentWhere
	 *
	 * @param   Integer  $store_id       Store_id
	 * @param   String   $parent         Parent
	 * @param   Integer  $fetchFeatured  fetch Featured if set to 1 otherwise fetch all product which are not featured
	 *
	 * @return  condition
	 *
	 * @since   1.8
	 */
	public function _buildContentWhere($store_id = '', $parent = "", $fetchFeatured = 1)
	{
		$app     = Factory::getApplication();
		$jinput  = $app->input;
		$where   = array();

		// PRODUCT WHICH ARE PUBLISHED
		$where[] = ' a.`state`=1';
		$where[] = ' a.`display_in_product_catlog`=1';

		// 	CHECK FOR parent
		if (!empty($parent))
		{
			$where[] = " a.parent='" . $parent . "' ";
		}

		if (!empty($store_id))
		{
			$where[] = ' a.`store_id`=\'' . $store_id . '\'';
		}

		if (empty($fetchFeatured))
		{
			$where[] = ' a.`featured`= 0';
		}

		// CATEGORY FILTER

		$store_cat = $jinput->get('store_cat', 0, 'INTEGER');

		// If category is selected the don't show
		if (trim($store_cat) != 0)
		{
			$where[] = " a.category=" . $store_cat . " ";
		}

		return $where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
	}

	/**
	 * Function getTotal
	 *
	 * @param   String   $client    Client
	 * @param   Integer  $store_id  Store_id
	 *
	 * @return  Total
	 *
	 * @since   1.8
	 */
	public function getTotal($client = '', $store_id = '')
	{
		$query        = $this->_buildQuery($store_id, $client);
		$this->_total = $this->_getListCount($query);

		return $this->_total;
	}

	/**
	 * Function getPagination
	 *
	 * @param   String   $client    Client
	 * @param   Integer  $store_id  Store_id
	 *
	 * @return  Pagination
	 *
	 * @since   1.8
	 */
	public function getPagination($client = '', $store_id = '')
	{
		$this->_pagination = new Pagination($this->getTotal($client, $store_id), $this->getState('limitstart'), $this->getState('limit'));

		return $this->_pagination;
	}

	/**
	 * Function getAllStoreProducts
	 *
	 * @param   String   $client         Client
	 * @param   Integer  $store_id       Store_id
	 * @param   Integer  $fetchFeatured  Fetch featured product also.
	 *
	 * @return  array
	 *
	 * @since   1.8
	 */
	public function getAllStoreProducts($client = '', $store_id = '', $fetchFeatured = 1)
	{
		$query       = $this->_buildQuery($store_id, $client, $fetchFeatured);
		$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		return $this->_data;
	}

	/**
	 * Function return store id for $owner
	 *
	 * @param   Integer  $owner  Owner
	 *
	 * @return  integer
	 *
	 * @since   1.8
	 */
	public function getStoreId($owner = 0)
	{
		$db           = Factory::getDbo();
		$query        = $db->getQuery(true);
		$query = "Select id FROM #__kart_store WHERE owner=" . $owner;
		$db->setQuery($query);
		$store = $db->loadResult();

		return $store;
	}

	/**
	 * Function getStore
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	public function getStore()
	{
	}

	/**
	 * Function saveStore
	 *
	 * @param   Array  $data  Data
	 *
	 * @return  array of list
	 *
	 * @since   1.8
	 */
	public function saveStore($data)
	{
		$comparams        = ComponentHelper::getParams('com_quick2cart');
		$row              = new stdClass;
		$row->owner       = $data['owner'];
		$row->title       = $data['title'];
		$row->description = $data['description'];
		$row->address     = $data['address'];
		$row->phone       = $data['phone'];
		$row->store_email = $data['store_email'];

		// TODO get the uploaded image path
		$row->store_avatar    = $data['store_avatar'];
		$comquick2cartHelper  = new comquick2cartHelper;
		$row->currency_name   = (isset($data['currency_name']) ? $data['currency_name'] : $comquick2cartHelper->getCurrencySession());
		$row->use_ship        = (isset($data['use_ship']) ? $data['use_ship'] : $comparams->get('shipping'));
		$row->use_stock       = (isset($data['use_stock']) ? $data['use_stock'] : $comparams->get('usestock'));
		$row->ship_no_stock   = (isset($data['ship_no_stock']) ? $data['ship_no_stock'] : $comparams->get('outofstock_allowship'));
		$row->buy_button_text = (isset($data['buy_button_text']) ? $data['buy_button_text'] : Text::_('QTC_ITEM_BUY'));

		if (isset($data['live']))
		{
			$row->live = $data['live'];
		}

		if (isset($data['extra']))
		{
			$row->extra = $data['extra'];
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = "Select id FROM #__kart_store WHERE owner=" . $data['owner'];
		$db->setQuery($query);
		$store = $db->loadResult();

		// TODO consider multi store for a single user
		if ($store)
		{
			$row->id = $store;
			try
			{
				$db->updateObject('#__kart_store', $row, 'id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}
		else
		{
			try
			{
				$db->insertObject('#__kart_store', $row, 'id');
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
	 * Function saveStoreRole
	 *
	 * @param   Array  $data  Store ID
	 *
	 * @return  array of list
	 *
	 * @since   1.8
	 */
	public function saveStoreRole($data)
	{
		$row           = new stdClass;
		$row->store_id = $data['store_id'];
		$row->user_id  = $data['user_id'];
		$row->role     = $data['role'];
		$db            = Factory::getDbo();

		if (isset($data['id']))
		{
			$row->id = $data['id'];

			try
			{
				$db->updateObject('#__kart_role', $row, 'id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}
		else
		{
			try
			{
				$db->insertObject('#__kart_role', $row, 'id');
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
	 * Function for getAllProductsFromStore
	 *
	 * @param   Integer  $storeId  Store ID
	 *
	 * @return  array of list
	 *
	 * @since   1.8
	 */
	public function getAllProductsFromStore($storeId)
	{
		$db       = Factory::getDbo();
		$query    = $db->getQuery(true);
		$columns  = array('item_id', 'name');
		$query->select($db->quoteName($columns));
		$query->from($db->quoteName('#__kart_items'));
		$query->where($db->quoteName('store_id') . ' = ' . $db->quote($storeId));
		$query->where($db->quoteName('display_in_product_catlog') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$optionList = $db->loadObjectList();

		return $optionList;
	}
}
