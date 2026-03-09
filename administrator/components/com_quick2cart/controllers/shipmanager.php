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
use Joomla\CMS\Language\Text;

/**
 * Ship manager controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerShipmanager extends quick2cartController
{
	/**
	 * Function to load state
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function loadState()
	{
		$db      = Factory::getDBO();
		$jinput  = Factory::getApplication()->input;
		$country = $jinput->get('country');
		$model   = $this->getModel('shipmanager');
		$state   = $model->getStatelist($country);
		$data    = array();
		$data[0] = $state;
		echo json_encode($data);
		jexit();
	}

	/**
	 * Function to load city
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function loadCity()
	{
		$db     = Factory::getDBO();
		$jinput = Factory::getApplication()->input;
		$state  = $jinput->get('state');
		$model  = $this->getModel('shipmanager');
		$cities = $model->getCity($state);
		echo json_encode($cities);
		jexit();
	}

	/**
	 * Function to save shipping method
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function saveShipOption()
	{
		$jinput = Factory::getApplication()->input;
		$data   = $jinput->get;
		$model = $this->getModel('shipmanager');
		$model->storeShipData($data);
		$msg = "";

		$this->setRedirect('index.php?option=com_quick2cart&view=shipmanager&layout=list', $msg);
	}

	/**
	 * Function to remove shipping method
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function remove()
	{
		$jinput = Factory::getApplication()->input;
		$post   = $jinput->post;
		$model   = $this->getModel('shipmanager');
		$orderid = $post['cid'];
		$msg     = Text::_('C_ORDER_DELETED_ERROR');

		if ($model->deletshiplist($orderid))
		{
			$msg = Text::_('C_ORDER_DELETED_SCUSS');
		}

		$this->setRedirect('index.php?option=com_quick2cart&view=shipmanager&layout=list', $msg);
	}

	/**
	 * Find the geo locations according the geo db
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function findgeo()
	{
		$geodata        = $_POST['geo'];
		$element	    = Factory::getApplication()->input->get('element');
		$element_val    = Factory::getApplication()->input->get('request_term');
		$query_condi    = array();
		$query_table    = array();
		$first          = 1;
		$first_key      = key($geodata);
		$previous_field = '';
		$loca_list      = array();

		foreach ($geodata as $key => $value)
		{
			$value = trim($value);

			if ($first)
			{
				$query_table[] = '#__kart_' . $key . ' as ' . $key;
			}
			elseif ($element == $key )
			{
				$query_table[] = '#__kart_' . $key . ' as ' . $key . ' ON ' . $key . '.'
				. $previous_field . '_code = ' . $previous_field . '.' . $previous_field . '_code';
			}

			$value = str_replace("||", "','", $value);
			$value = str_replace('|', '', $value);

			if ($element == $key )
			{
				$element_table_name = $key;
				$query_condi[] = $key . "." . $key . " LIKE '%" . trim($element_val) . "%'";

				if (trim($value))
				{
					$query_condi[] = $key . "." . $key . " NOT IN ('" . trim($value) . "')";
				}

				break;

				$previous_field = $key;
			}
			elseif (trim($value) && $first )
			{
				$query_condi[]  = $key . "." . $key . " IN ('" . trim($value) . "')";
				$previous_field = $key;
			}

			$first = 0;
		}

		$tables = (count($query_table) ? ' FROM ' . implode("\n LEFT JOIN ", $query_table) : '');

		if ($tables)
		{
			$where = (count($query_condi) ? ' WHERE ' . implode("\n AND ", $query_condi) : '');

			if ($where)
			{
				$db = Factory::getDBO();
				$query = "SELECT distinct(" . $element_table_name . "." . $element . ") \n " . $tables . " \n " . $where;
				$db->setQuery($query);
				$loca_list = $db->loadRowList();
			}
		}

		$data = array();

		if ($loca_list)
		{
			foreach ($loca_list as $row)
			{
				$json = array();

				// Name of the location
				$data[] = $row['0'];
			}
		}

		echo json_encode($data);
		jexit();
	}
}
