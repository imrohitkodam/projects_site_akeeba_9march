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
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Pagination\Pagination;

/**
 * SalesReport Model.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelSalesreport extends BaseDatabaseModel
{
	// Changed by Deepa

	/*protected $_data;
	protected $_total = null;
	protected $_pagination = null;*/

	protected $data;

	protected $total = null;

	protected $pagination = null;

	/**
	 * Constructor.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct()
	{
		parent::__construct();
		$app          = Factory::getApplication();
		$jinput       = $app->input;
		$this->option = $option = $jinput->get('option');
		$this->view   = $jinput->get('view');

		// Get pagination request variables
		$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart = $jinput->get('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		/*	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);*/
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		// STORE FILTER search_store
		$search_store = $app->getUserStateFromRequest($option . $this->view . 'search_store', 'search_store', 0, 'INTEGER');
		$this->setState('search_store', $search_store);
	}

	/**
	 * Method getSalesReport.
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function getSalesReport()
	{
		if (empty($this->_data))
		{
			$query       = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method _buildContentWhere.
	 *
	 * @return	condition
	 *
	 * @since	1.6
	 */
	public function _buildContentWhere()
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->get('option');
		$db     = Factory::getDbo();

		$where   = array();
		$where[] = '( o.`status`="C" || o.`status`="S" )';

		// From date FILTER
		$fromDate = $app->getUserStateFromRequest($option . $this->view . 'salesfromDate', 'salesfromDate', '', 'RAW');
		$this->setState('salesfromDate', $fromDate);

		// To date FILTER
		$toDate = $app->getUserStateFromRequest($option . $this->view . 'salestoDate', 'salestoDate', '', 'RAW');
		$this->setState('salestoDate', $toDate);

		if (!empty($toDate) && !empty($fromDate))
		{
			$where[] = '  DATE(o.`mdate`) BETWEEN \'' . $fromDate . '\' AND  \'' . $toDate . '\'';
		}

		$where[] = ' oi.`item_id`= i.`item_id` ';

		$search = $app->getUserStateFromRequest($option . 'filter_search', 'filter_search', '', 'string');

		if (trim($search) != '')
		{
			/*	// check  where atleast one record is present
			$query="SELECT item_id FROM #__kart_items WHERE name LIKE '%".$search."%'";
			$db->setQuery($query);
			$createid=$db->loadResult();
			// if present
			if($createid)*/
			{
				$where[] = "i.name LIKE '%" . $db->escape($search) . "%'";
			}
		}

		// STORE FILTER
		$search_store = $app->getUserStateFromRequest($option . $this->view . 'search_store', 'search_store', 0, 'INTEGER');

		if (trim($search_store) != 0)
		{
			$where[] = " i.`store_id`=" . $search_store . " ";
		}

		return $where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
	}

	/**
	 * Method Buildquery.
	 *
	 * @return	query
	 *
	 * @since	1.6
	 */
	public function _buildQuery()
	{
		$db        = Factory::getDBO();
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$option    = $jinput->get('option');
		$layout    = $jinput->get('layout', 'salesreport');

		// Get the WHERE and ORDER BY clauses for the query
		$where   = '';
		$me      = Factory::getuser();
		$user_id = $me->id;
		$where   = $this->_buildContentWhere();

		/*if($layout=='default')//payouts report //when called from front end
		{*/
		$query = "SELECT oi.`item_id`, SUM(oi.`product_quantity`) AS 'saleqty', i.`store_id`, i.`name` as item_name, i.`stock`, i.`mdate`, i.`state`
			 FROM `#__kart_order_item` AS oi
			 JOIN `#__kart_orders` AS o ON oi.`order_id` = o.`id`
			 JOIN `#__kart_items` AS i  ON oi.`item_id` = i.`item_id`
			" . $where . "
			GROUP BY `item_id` ";

		$filter_order     = $app->getUserStateFromRequest($option . $this->view . '.filter_order', 'filter_order', 'saleqty', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option . $this->view . 'salesreport.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

		$orderByArray         = array();
		$orderByArray['name'] = "item_name";

		if ($filter_order)
		{
			if (!empty($orderByArray[$filter_order]))
			{
				$query .= " ORDER BY " . $orderByArray['name'] . " $filter_order_Dir";
			}
			else
			{
				$query .= " ORDER BY $filter_order $filter_order_Dir";
			}
		}
		/*}*/

		return $query;
	}

	/**
	 * Method getTotal.
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn’t already exist
		if (empty($this->_total))
		{
			$query        = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method getPagination.
	 *
	 * @return	link
	 *
	 * @since	1.6
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn’t already exist
		if (empty($this->_pagination))
		{
			$this->_pagination = new Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method getCsvexportData.
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function getCsvexportData()
	{
		$query = $this->_buildQuery();
		$db    = Factory::getDBO();
		$query = $db->setQuery($query);

		return $db->loadAssocList();
	}
}
