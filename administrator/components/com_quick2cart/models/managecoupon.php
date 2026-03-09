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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Pagination\Pagination;

/**
 * Methods supporting coupons.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelManageCoupon extends BaseDatabaseModel
{
	protected $protected_data;

	protected $protected_total = null;

	protected $protected_pagination = null;

	/**
	 * Constructor that retrieves the ID from the request
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		global $option;
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Get the pagination request variables
		$limit            = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart       = $jinput->get('limitstart', 0, '', 'int');
		$filter_order     = $app->getUserStateFromRequest($option . 'filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

		// Set the limit variable for query later on
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);

		// Set the limit variable for query later on
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Function to build query
	 *
	 * @return  null
	 */
	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$query = "SELECT * from #__kart_coupon as a";

		return $query;
	}

	/**
	 * Function to delete coupon
	 *
	 * @param   Array  $zoneid  zone id
	 *
	 * @return  null
	 */
	public function deletecoupon($zoneid)
	{
		if (!empty($zoneid))
		{
			$newzone = implode(',', $zoneid);
			$db = Factory::getDbo();

			$query = "DELETE FROM #__kart_coupon where id IN (" . $newzone . ")";
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

	/**
	 * Function to build where
	 *
	 * @return  null
	 */
	public function _buildContentWhere()
	{
		global $option;
		$app    = Factory::getApplication();
		$db     = Factory::getDbo();
		$search = $app->getUserStateFromRequest($option . 'search', 'search', '', 'string');
		$where  = array();

		if (trim($search) != '')
		{
			$query = "SELECT id FROM #__kart_coupon WHERE name LIKE '%" . $search . "%'";
			$db->setQuery($query);
			$createid = $db->loadResult();

			if ($createid)
			{
				$where[] = "name LIKE '%" . $search . "%'";
			}
		}

		return $where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
	}

	/**
	 * Function to get manage coupon
	 *
	 * @return  null
	 */
	public function getManagecoupon()
	{
		global $option;
		$db     = Factory::getDbo();
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->get('option');
		$query  = $this->_buildQuery();
		$query .= $this->_buildContentWhere();

		$filter_order     = $app->getUserStateFromRequest($option . 'filter_order',  'filter_order', '', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

		if ($filter_order)
		{
			$qry = "SHOW COLUMNS FROM #__kart_coupon";
			$db->setQuery($qry);
			$exists = $db->loadobjectlist();

			foreach ($exists as $key => $value)
			{
				$allowed_fields[] = $value->Field;
			}

			if (in_array($filter_order, $allowed_fields))
			{
				$query .= " ORDER BY $filter_order $filter_order_Dir";
			}
		}

		$this->protected_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		return $this->protected_data;
	}

	/**
	 * Function to edit list
	 *
	 * @param   INT  $zoneid  id
	 *
	 * @return  null
	 */
	public function Editlist($zoneid)
	{
		unset($this->protected_data);
		$query = "SELECT * from #__kart_coupon where id=$zoneid";
		$this->protected_data = $this->_getList($query);

		if (!empty($this->protected_data[0]))
		{
			if ($this->protected_data[0]->item_id)
			{
				$this->protected_data[0]->item_id_name = $this->getautoname_DB($this->protected_data[0]->item_id, 'kart_items', 'item_id', 'name');
			}

			if ($this->protected_data[0]->user_id)
			{
				$this->protected_data[0]->user_id_name = $this->getautoname_DB($this->protected_data[0]->user_id, 'users', 'id', 'name', 'id.block <> 1');
			}
		}

		return $this->protected_data;
	}

	/**
	 * Function to get db name
	 *
	 * @param   STRING  $autodata       data
	 * @param   STRING  $element_table  element table
	 * @param   STRING  $element        element
	 * @param   STRING  $element_value  value
	 * @param   STRING  $extras         extras
	 *
	 * @return  STRING  db name
	 */
	public function getautoname_DB($autodata,$element_table,$element,$element_value,$extras='')
	{
		$autodata = str_replace("||", "','", $autodata);
		$autodata = str_replace('|', '', $autodata);
		$query_table[] = '#__' . $element_table . ' as ' . $element;

		if (trim($autodata))
		{
			$query_condi[] = $element . "." . $element . " IN ('" . trim($autodata) . "')";
		}

		if (trim($extras))
		{
			$query_condi[] = $extras;
		}

		$tables = (count($query_table) ? ' FROM ' . implode("\n LEFT JOIN ", $query_table) : '');

		if ($tables)
		{
			$where = (count($query_condi) ? ' WHERE ' . implode("\n AND ", $query_condi) : '');

			if ($where)
			{
				$db = Factory::getDbo();
				$query = "SELECT " . $element_value . "
				\n " . $tables . " \n " . $where;

				$db->setQuery($query);
				$loca_list = $db->loadColumn();

				return ((!empty($loca_list)) ? "|" . implode('||', $loca_list) . "|" : '');
			}
		}
	}

	/**
	 * Function to get total
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn’t already exist
		if (empty($this->protected_total))
		{
			$query = $this->_buildQuery();
			$this->protected_total = $this->_getListCount($query);
		}

		return $this->protected_total;
	}

	/**
	 * Function to get pagination
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function getPagination()
	{
		if (empty($this->protected_pagination))
		{
			$this->protected_pagination = new Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->protected_pagination;
	}

	/**
	 * Function to set item state
	 *
	 * @param   ARRAY  $items  items
	 * @param   INT    $state  state
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function setItemState($items, $state )
	{
		$db = Factory::getDbo();

		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$db = Factory::getDBO();
				$query = "UPDATE  #__kart_coupon SET published=$state where id=" . $id;
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
	 * function store coupon
	 *
	 * @param   Object  $data  data
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function store($data)
	{
		$jinput = Factory::getApplication()->input;
		$db	    = Factory::getDBO();
		$row1   = new stdClass;
		$coupon_id				= $data->get('coupon_id', '', 'RAW');
		$row1->store_id  		= $data->get('current_store');

		// GET ITEM ID , USER ID FROM AUTO SUGGEST FORMAT
		$item_id = $this->sort_auto($data->get('item_id', '', 'STRING'));
		$row1->item_id = $item_id;

		$row1->name 			= $data->get('coupon_name', '', 'RAW');
		$row1->published 		= $data->get('published');
		$row1->code 			= $db->escape(trim($data->get('code', '', 'RAW')));
		$row1->value 			= $data->get('value');
		$row1->val_type 		= $data->get('val_type');
		$row1->max_use	 		= $data->get('max_use');
		$row1->max_per_user 	= $data->get('max_per_user');
		$row1->description		= $data->get('description', '', 'RAW');
		$row1->params 			= $data->get('params', '', 'RAW');
		$row1->from_date		= $data->get('from_date', '', 'RAW');
		$row1->exp_date 		= $data->get('exp_date', '', 'RAW');

		if ($coupon_id)
		{
			$qry = "SELECT `id` FROM #__kart_coupon WHERE `id` = '{$coupon_id}'";
			$db->setQuery($qry);
			$exists = $db->loadResult();

			// Store the web link table to the database
			if ($exists)
			{
				$row1->id = $coupon_id;
				$db->updateObject('#__kart_coupon', $row1, 'id');
			}
		}
		else
		{
			$db->insertObject('#__kart_coupon', $row1, 'id');
		}

		return true;
	}

	/**
	 * Function to get coupon code
	 *
	 * @param   STRING  $data_auto  data
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function sort_auto($data_auto)
	{
		if ($data_auto)
		{
			$data_auto = substr($data_auto, 1, -1);
			$data_autos = explode("||", $data_auto);
			sort($data_autos, SORT_NUMERIC);
			$data_auto = "|" . implode('||', $data_autos) . "|";

			return $data_auto;
		}
	}

	/**
	 * Function to get coupon code
	 *
	 * @param   STRING  $code  coupon code
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function getcode($code)
	{
		$db	= Factory::getDBO();
		$qry = "SELECT `id` FROM #__kart_coupon WHERE `code` = " . $db->quote($db->escape(trim($code)));
		$db->setQuery($qry);
		$exists = $db->loadResult();

		if ($exists)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Function to get selected code
	 *
	 * @param   STRING  $code  coupon code
	 * @param   INT     $id    id
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function getselectcode($code,$id)
	{
		$db	= Factory::getDBO();
		$qry = "SELECT `code` FROM #__kart_coupon WHERE id<>'{$id}' AND `code` = " . $db->quote($db->escape(trim($code)));
		$db->setQuery($qry);
		$exists = $db->loadResult();

		if ($exists)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Function to get store name
	 *
	 * @param   INT  $store_id  store id
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function getStoreNmae($store_id)
	{
		if (!empty($store_id))
		{
			$db	 = Factory::getDBO();
			$qry = "SELECT `title` FROM #__kart_store WHERE id=" . $store_id;
			$db->setQuery($qry);

			return $db->loadResult();
		}
	}

	/*
		This model function manage items published or unpublished state
	*/
	/*function setItemState($items,$state)
	{
		$db=Factory::getDBO();

		if(is_array($items))
		{
			$row=$this->getTable();

	   		foreach($items as $id)
			{
				$db=Factory::getDBO();
				$query="UPDATE #__kart_coupon SET published=".$state." WHERE id=".$id;
				$db->setQuery( $query );
				if (!$db->execute()) {
				  $this->setError( $this->_db->getErrorMsg() );
				  return false;
				}
			}
		}
	}*/
}
