<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\String\StringHelper;

/**
 * Order Model class.
 *
 * @since  1.6
 */
class Quick2cartModelOrders extends BaseDatabaseModel
{
	protected $data;

	protected $totalRecords = null;

	protected $pagination = null;

	protected $store_id = null;

	protected $customer_count = null;

	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		parent::__construct();

		$this->siteMainHelper = new comquick2cartHelper;
		$app                  = Factory::getApplication();

		// Get the pagination request variables
		$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');

		// Set the limit variable for query later on
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Function to get total orders detail
	 *
	 * @param   INT  $store_id     store_id
	 * @param   INT  $customer_id  customer_id
	 *
	 * @since   2.2
	 *
	 * @return  total
	 */
	public function _buildQuery($store_id = 0, $customer_id = 0)
	{
		$db    = Factory::getDBO();

		// Get the WHERE and ORDER BY clauses for the query
		$where = '';
		$where = $this->_buildContentWhere($store_id, $customer_id);
		$query = "SELECT i.processor, i.amount, i.cdate, i.payee_id, i.status, i.id, i.prefix, i.email, i.currency, u.name, u.username
		 FROM #__kart_orders AS i
		 LEFT JOIN #__users AS u ON u.id = i.payee_id" . $where;

		$app              = Factory::getApplication();
		$jinput           = $app->input;
		$option           = $jinput->get('option');
		$filter_order     = $app->getUserStateFromRequest($option . 'filter_order', 'filter_order', 'cdate', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

		if (!in_array($filter_order_Dir, array('asc', 'desc')))
		{
			$filter_order_Dir = 'desc';
		}

		if ($filter_order)
		{
			$qry1 = "SHOW COLUMNS FROM #__kart_orders";
			$db->setQuery($qry1);
			$exists1 = $db->loadobjectlist();

			foreach ($exists1 as $key1 => $value1)
			{
				$allowed_fields[] = $value1->Field;
			}

			if (in_array($filter_order, $allowed_fields))
			{
				$query .= " ORDER BY $filter_order $filter_order_Dir";
			}
		}

		return $query;
	}

	/**
	 * Function to get total orders detail
	 *
	 * @param   INT  $store_id     store_id
	 * @param   INT  $customer_id  customer_id
	 *
	 * @since   2.2
	 *
	 * @return  total
	 */
	public function _buildContentWhere($store_id = 0, $customer_id = 0)
	{
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$option    = $jinput->get('option', '', 'STRING');
		$layout    = $jinput->get('layout', '', 'STRING');
		$db        = Factory::getDbo();

		$filter_search = $app->getUserStateFromRequest($option . 'filter.search', 'filter_search', '', 'STRING');

		// Stripe html tags
		$filter_search = htmlspecialchars($filter_search, ENT_COMPAT, 'UTF-8');
		$filter_state  = $app->getUserStateFromRequest($option . 'search_list', 'search_list', '', 'STRING');
		$search        = $app->getUserStateFromRequest($option . 'search_select', 'search_select', '', 'STRING');

		// For Filter Based on Gateway
		$search_gateway = '';
		$search_gateway = $app->getUserStateFromRequest($option . 'search_gateway', 'search_gateway', '', 'string');
		$search_gateway = StringHelper::strtolower($search_gateway);

		// For Filter Based on Gateway
		$where = array();

		if ($app->getName() == 'site')
		{
			$user = Factory::getuser();

			if (!empty($store_id))
			{
				$order_ids = $this->getOrderIds($store_id, 1);
				$order_ids = (!empty($order_ids) ? $order_ids : 0);
				$where[]   = "i.id IN ( " . $order_ids . ")";
			}

			if (!empty($customer_id))
			{
				if (is_numeric($customer_id))
				{
					$where[] = "i.payee_id = " . $db->quote($customer_id);
				}
				else
				{
					$where[] = "i.email = " . $db->quote($customer_id);
				}
			}

			if (empty($store_id) && empty($customer_id))
			{
				$where[] = "i.payee_id = " . $user->id;
			}
		}

		// My orders
		if ($layout == 'default')
		{
			$subQuery = "SELECT DISTINCT oi.`order_id` FROM `#__kart_order_item` AS oi";

			if (!empty($search) && $search != -1)
			{
				$subQuery .= ' INNER JOIN `#__kart_orders` AS o ON o.id = oi.order_id';
				$subQuery .= ' WHERE o.user_info_id = ' . $user->id . ' AND o.status = ' . $this->_db->Quote($search);
				$db->setQuery($subQuery);
				$ids = $db->loadColumn();

				if ($ids)
				{
					$ids     = implode(',', $ids);
					$where[] = " i.`id` IN (" . $ids . ")";
				}
				// Else should result in no output so use 0 in where
				else
				{
					$where[] = " i.`id` IN (0)";
				}
			}
		}

		if ($layout == 'mycustomer')
		{
			if ($filter_state)
			{
				$where[] = " UPPER( CONCAT( i.prefix, i.id )) LIKE UPPER('%" . trim($filter_state) . "%')";
			}
		}

		if ($filter_search)
		{
			$filter         = InputFilter::getInstance();
			$filterSearchId = abs($filter->clean($filter_search, 'INT'));
			$idSearchString = '';

			if (!empty($filterSearchId) && strpos($filter_search, 'id:') !== false)
			{
				$idSearchString = "OR i.id LIKE '%" . $db->escape($filterSearchId) . "%'";
			}

			$where[] = "(i.email LIKE '%" . $db->escape($filter_search) . "%'" . $idSearchString . "
			OR i.prefix LIKE '%" . $db->escape($filter_search) . "%'" . "
			OR u.name LIKE '%" . $db->escape($filter_search) . "%'" . "
			OR CONCAT(i.prefix, i.id) LIKE '%" . $db->escape($filter_search) . "%'" . "
			OR u.username LIKE '%" . $db->escape($filter_search) . "%')";
		}

		return $where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
	}

	/**
	 * Function to get total orders detail
	 *
	 * @param   INT  $store_id     store_id
	 * @param   INT  $customer_id  customer_id
	 *
	 * @since   2.2
	 *
	 * @return  total
	 */
	public function getOrders($store_id = 0, $customer_id = 0)
	{
		if (empty($this->data))
		{
			$query       = $this->_buildQuery($store_id, $customer_id);
			$this->data  = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->data;
	}

	/**
	 * Function to get total orders for pagination
	 *
	 * @param   INT  $store_id  store_id
	 *
	 * @since   2.2
	 *
	 * @return  total
	 */
	public function getTotal($store_id = 0)
	{
		// Lets load the content if it doesn’t already exist
		if (empty($this->totalRecords))
		{
			$query              = $this->_buildQuery($store_id);
			$this->totalRecords = $this->_getListCount($query);
		}

		return $this->totalRecords;
	}

	/**
	 * Function to get pagination
	 *
	 * @param   INT  $count     count
	 * @param   INT  $store_id  store_id
	 *
	 * @since   2.2
	 *
	 * @return  pagination
	 */
	public function getPagination($count = 0, $store_id = 0)
	{
		if (empty($count))
		{
			// Use count from of order for my order view
			$count = $this->getTotal($store_id);
		}

		$this->pagination = new Pagination($count, $this->getState('limitstart'), $this->getState('limit'));

		return $this->pagination;
	}

	/**
	 * Function to place order
	 *
	 * @param   INT  $store_id  store_id.
	 *
	 * @since   2.2
	 * @return  if 1 = success,2= error,3 = refund order,4 =  future use,5 = future use,6 = for store owner,
	 * Dont allow to change status  from S/Cancelled/RF to C
	 */
	public function store($store_id = 0)
	{
		// Load language file for plugin frontend
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		$returnvaule = 1;
		$jinput      = Factory::getApplication()->input;
		$status      = $jinput->get('status');
		$data        = $jinput->post;

		//  For list view
		$orderid      = $data->get('id', 0, 'INTEGER');
		$notify_chk   = $data->get('notify_chk' . '|' . $orderid, '');
		$notify_chk   = (!empty($notify_chk)) ? 1 : 0;
		$add_note_chk = $data->get('add_note_chk' . '|' . $orderid);
		$note         = '';
		$note         = $data->get('order_note' . '|' . $orderid, '', "STRING");
		$updateStatus = $this->siteMainHelper->updatestatus($orderid, $status, $note, $notify_chk, $store_id);

		if ($updateStatus === false)
		{
			return false;
		}

		if ($status == 'RF')
		{
			$returnvaule = 3;
		}

		// Save order history
		if ($orderid && $store_id)
		{
			// Update item status
			$this->siteMainHelper->updatestatus($orderid, $status, $note, $notify_chk, $store_id);

			// Save order history
			$orderItems = $this->getOrderItems($orderid);

			foreach ($orderItems as $oitemId)
			{
				// Save order item status history
				$this->siteMainHelper->saveOrderStatusHistory($orderid, $oitemId, $status, $note, $notify_chk);
			}
		}

		return $returnvaule;
	}

	/**
	 * Return order item ids list
	 *
	 * @param   string  $orderid  order_id.
	 *
	 * @since   2.2
	 * @return  list.
	 */
	public function getOrderItems($orderid)
	{
		if ($orderid)
		{
			$db    = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('order_item_id');
			$query->from('#__kart_order_item AS oi');
			$query->where("oi.order_id= " . $orderid);
			$db->setQuery($query);

			return $orderList = $db->loadColumn();
		}
	}

	/**
	 * function to get list of payment pateways
	 *
	 * @since   2.2
	 *
	 * @return   status
	 */
	public function gatewaylist()
	{
		$db    = Factory::getDBO();
		$query = "SELECT DISTINCT(`processor`) FROM #__kart_orders";
		$db->setQuery($query);
		$gatewaylist = $db->loadObjectList();

		if (!$gatewaylist)
		{
			return 0;
		}
		else
		{
			return $gatewaylist;
		}
	}

	/**
	 * function to get order ids
	 *
	 * @param   INT  $store_id        store id
	 * @param   INT  $useorderStatus  useorderStatus
	 *
	 * @since   2.2
	 *
	 * @return   status
	 */
	public function getOrderIds($store_id, $useorderStatus = 0)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT(order_id)');
		$query->from($db->quoteName('#__kart_order_item'));
		$query->where($db->quoteName('#__kart_order_item.store_id') . ' = ' . (int) $store_id);

		// If called for store order view
		if ($useorderStatus == 1)
		{
			$app    = Factory::getApplication();
			$jinput = $app->input;
			$option = $jinput->get('option');
			$search = $app->getUserStateFromRequest($option . 'search_select', 'search_select', '', 'string');

			//  filter is enabled
			if (!empty($search) && $search != -1)
			{
				$query->where($db->quoteName('#__kart_order_item.status') . ' = ' . $db->quote($search));
			}
		}

		$db->setQuery($query);
		$ids = $db->loadColumn();

		return implode(',', $ids);
	}

	/**
	 * function to get customers
	 *
	 * @param   INT  $store_id  store id
	 *
	 * @since   2.2
	 *
	 * @return   status
	 */
	public function getCustomers($store_id)
	{
		$query = $this->buildCustomer($store_id);

		if (!empty($query))
		{
			require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
			$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');

			$this->data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

			foreach ($this->data as $item)
			{
				$item->countryName = '';
				$item->countryName = $tjGeoHelper->getCountryNameFromId($item->country_code);
			}

			return $this->data;
		}
		else
		{
			return;
		}
	}

	/**
	 * function to build customers
	 *
	 * @param   INT  $store_id  store id
	 *
	 * @since   2.2
	 *
	 * @return   status
	 */
	public function buildCustomer($store_id)
	{
		$db        = Factory::getDbo();
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$option    = $jinput->get('option', '', 'STRING');
		$order_ids = $this->getOrderIds($store_id);
		$query     = "";

		if (!empty($order_ids))
		{
			$query = "select * from
			 (SELECT  u.* FROM  `#__kart_orders` AS o
			 LEFT JOIN  `#__kart_users` AS u ON o.`email` = u.`user_email`
			 WHERE u.`address_type` =  'BT'
			 AND o.id=u.order_id
			 AND u.`order_id` IN (" . $order_ids . " )
			 order by u.id  DESC
			) AS newtb ";

			$filter_order     = $app->getUserStateFromRequest($option . 'filter_order', 'filter_order', 'firstname', 'cmd');
			$filter_order_Dir = $app->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
			$filter_search    = $app->getUserStateFromRequest($option . 'filter.search', 'filter_search', '', 'STRING');

			// Stripe html tags
			$filter_search = htmlspecialchars($filter_search, ENT_COMPAT, 'UTF-8');

			if (!in_array($filter_order_Dir, array('asc', 'desc')))
			{
				$filter_order_Dir = 'desc';
			}

			// NOTE:: 1. FIND ALL WHERE AND APPEND TO QUERY
			if (!empty($filter_search))
			{
				$where = " WHERE ((`firstname` LIKE \"%" . $db->escape($filter_search) . "%\") OR (`lastname` LIKE \"%" . $db->escape($filter_search) . "%\"))";
				$query .= $where;
			}

			// NOTE:: 2. USE GROUP BY IF ANY
			$groupby = " group by newtb.user_email ";
			$query .= $groupby;

			// NOTE:: 3. USE FILTER
			if ($filter_order)
			{
				$comquick2cartHelper = new comquick2cartHelper;
				$allowed_fields      = $comquick2cartHelper->getColumns('#__kart_users');

				if (in_array($filter_order, $allowed_fields))
				{
					$query .= " ORDER BY " . $filter_order . ' ' . $filter_order_Dir;
				}
			}
		}

		return $query;
	}

	/**
	 * This function get count of customers
	 *
	 * @param   INT  $store_id  store id
	 *
	 * @since   2.2
	 *
	 * @return   store items
	 */
	public function getCustomerTotal($store_id)
	{
		$query = $this->buildCustomer($store_id);

		if (!empty($query))
		{
			return $this->customer_count = $this->_getListCount($query);
		}
		else
		{
			return;
		}
	}

	/**
	 * This function get all product list and and its final price against store_id,order_id
	 *
	 * @param   ARRAY  $storeids  array of store ids
	 * @param   INT    $orderid   order id
	 *
	 * @since   2.2
	 *
	 * @return   store items
	 */
	public function getStore_items($storeids, $orderid)
	{
		$db    = Factory::getDBO();
		$query = "SELECT `order_item_name`,`product_final_price` from `#__kart_order_item` where `store_id`=" . $storeids . " AND order_id=" . $orderid;
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Function to get store product price
	 *
	 * @param   ARRAY  $storeids  array of store ids
	 * @param   INT    $orderid   order id
	 *
	 * @since   2.2
	 *
	 * @return   product price
	 */
	public function getStore_product_price($storeids, $orderid)
	{
		$db    = Factory::getDBO();
		$query = "SELECT SUM(`product_final_price`)
		AS store_items_price from `#__kart_order_item` where `store_id`=" . $storeids . " AND order_id=" . $orderid;
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to get store id
	 *
	 * @since   2.2
	 *
	 * @return   store id
	 */
	public function getstoreId()
	{
		$user  = Factory::getUser();
		$db    = Factory::getDBO();
		$query = "select id from `#__kart_store` where `owner`=" . $user->id;
		$db    = $db->setQuery($query);

		return $db->loadResult($query);
	}

	/**
	 * Amol change : Function to resend invoice : For now called it from backend order details view. Added by Vijay
	 *
	 * @since   2.2.7
	 *
	 * @return   status.
	 */
	public function resendInvoice()
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$post   = $jinput->post;
		$comquick2cartHelper = new comquick2cartHelper;

		$orderid    = $jinput->get('orderid', '', 'INT');
		$notify_chk = 1;
		$store_id   = $jinput->get('store_id', '', 'INT');

		$db    = Factory::getDBO();
		$query = "SELECT o.status FROM #__kart_orders as o WHERE o.id =" . $orderid;
		$db->setQuery($query);
		$order_oldstatus = $db->loadResult();

		$comment = $post->get('comment', '', 'STRING');

		if (($order_oldstatus == 'C' || $order_oldstatus == 'S'))
		{
			$comquick2cartHelper->sendInvoice($orderid, $order_oldstatus, $comment, $notify_chk, $store_id);
		}
		else
		{
			echo Text::_("COM_QUICK2CART_INVOICE_SENDING_FAILED_REASON");

			return false;
		}

		return true;
	}

	/**
	 * Changes Get order history
	 *
	 * @param   integer  $order_id  order_id.
	 * @param   integer  $store_id  store_id.
	 *
	 * @return  result.
	 *
	 * @since   1.6
	 */
	public function getOrderHistory($order_id, $store_id)
	{
		if (!empty($order_id))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query = $db->getQuery(true);
			$query->select("oh.*,i.name");
			$query->from($db->quoteName('#__kart_orders_history') . ' AS oh');
			$query->join('INNER', "#__kart_order_item AS oi ON oh.order_item_id = oi.order_item_id");
			$query->join('INNER', "#__kart_items AS i ON i.item_id = oi.item_id");
			$query->where("oh.order_id = " . $order_id);

			if (!empty($store_id))
			{
				$query->where("oi.store_id = " . $store_id);
			}

			$query->order("oh.order_item_id ASC");
			$query->order("oh.mdate  ASC");
			$db->setQuery($query);

			return $db->loadObjectList();
		}

		return false;
	}

	/**
	 * Generate valid order status list
	 *
	 * @param   string  $status       Order's status
	 *
	 * @param   array   $allStatuses  All Status
	 *
	 * @return  array
	 */
	public function getValidOrderStatusList($status, $allStatuses)
	{
		$validStatus = array(
			"P"   => array(0 => "P", 1 => "C", 2 => "S", 3 => "E"),
			"C"   => array(0 => "C", 1 => "RF"),
			"RF"  => array(0 => "RF"),
			"S"   => array(0 => "S",  1 => "C", 2 => "E"),
			"E"   => array(0 => "E", 1 => "C")
		);

		foreach ($allStatuses as $key => $allStatus)
		{
			if (is_array($validStatus[$status]) && !in_array($allStatus->value, $validStatus[$status]))
			{
				unset($allStatuses[$key]);
			}
		}

		return $allStatuses;
	}

	/**
	 * This function delete the Order.
	 *
	 * @param   null|array  $odid  order id.
	 *
	 * @since   2.0
	 *
	 * @return   store id.
	 */
	public function delete($odid)
	{
		$app        = Factory::getApplication();
		$db         = Factory::getDbo();
		$orderCount = count($odid);

		for ($i = 0; $i < $orderCount; $i++)
		{
			$orderData = array();
			$orderData = $this->getorderinfo($odid[$i])['order_info'][0];

			$query = $db->getQuery(true);
			$query->select('order_item_id');
			$query->from($db->qn('#__kart_order_item'));
			$query->where($db->qn('#__kart_order_item.order_id') . ' = ' . $odid[$i]);
			$db->setQuery($query);
			$order_item = $db->loadResult();

			if (!empty($order_item))
			{
				// Del order item files.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__kart_orderItemFiles'));
				$query->where($db->quoteName('order_item_id') . ' = ' . $order_item);
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

				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__kart_order_itemattributes'));
				$query->where($db->quoteName('order_item_id') . ' = ' . $order_item);
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

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__kart_orders'));
			$query->where($db->quoteName('id') . ' = ' . $odid[$i]);
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

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__kart_order_item'));
			$query->where($db->quoteName('order_id') . ' = ' . $odid[$i]);
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

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__kart_users'));
			$query->where($db->quoteName('order_id') . ' = ' . $odid[$i]);
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

			//~ // START Q2C Sample development
			PluginHelper::importPlugin('system');
			PluginHelper::importPlugin('actionlog');
			$app->triggerEvent('onAfterQ2cOrderDelete', array($orderData));
		}

		return true;
	}

	/**
	 * @param   integer  $orderid  order id.
	 *
	 * @since   2.2.7
	 *
	 * @return   object.
	 */
	public function getorderinfo($orderid)
	{
		$db    = Factory::getDBO();
		// Get Order Info
		$order = $this->siteMainHelper->getorderinfo($orderid);

		// Get item attribute details
		foreach ($order['items'] as $key => $item)
		{
			$productHelper              = new productHelper;

			// Get cart items attribute details
			$item->prodAttributeDetails = $productHelper->getItemCompleteAttrDetail($item->item_id);
			$product_attributes = explode(',', $item->product_attributes);

			foreach ($item->prodAttributeDetails as $optionDetails)
			{
				foreach ($optionDetails->optionDetails as $option)
				{
					if (in_array($option->itemattributeoption_id, $product_attributes))
					{
						$selected_value = $option->itemattributeoption_id;

						if (!empty($selected_value))
						{
							$query = $db->getQuery(true);
							$query->select("`orderitemattribute_id`, `orderitemattribute_name`");
							$query->from('#__kart_order_itemattributes');
							$query->where("itemattributeoption_id =" . $selected_value . "");
							$query->where("order_item_id = " . $item->order_item_id);
							$db->setQuery($query);

							$itemattributes                       = $db->LoadObject();
							$optionDetails->orderitemattribute_id = $itemattributes->orderitemattribute_id;
							$optionDetails->selected              = $selected_value;

							if (!empty($optionDetails->attributeFieldType) && ($optionDetails->attributeFieldType == 'Textbox'))
							{
								$optionDetails->orderitemattribute_name = $itemattributes->orderitemattribute_name;
							}

							break;
						}
					}
				}
			}
		}

		return $order;
	}
}
