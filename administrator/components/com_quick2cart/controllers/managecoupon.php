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
use Joomla\CMS\Uri\Uri;

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

/**
 * manage coupon controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerManagecoupon extends quick2cartController
{
	/**
	 * Function to find the auto suggestion according the db
	 *
	 * @return  null
	 */
	public function findauto()
	{
		$jinput      = Factory::getApplication()->input;
		$element     = $jinput->get('element', '', 'STRING');
		$element_val = $jinput->get('request_term', '', 'STRING');
		$autodata    = $_POST[$element];
		$query_condi = array();
		$query_table = array();
		$loca_list   = array();
		$autodata    = str_replace("||", "','", $autodata);
		$autodata    = str_replace('|', '', $autodata);

		if ($element == "item_id")
		{
			$element_table = "kart_items";
			$element_field = "name";
			$store_id      = $jinput->get('store');
			$query_condi[] = $element . ".store_id = " . $store_id;
		}
		elseif ($element == "id")
		{
			$element_table = "users";
			$element_field = "name";
			$query_condi[] = $element . ".block <> 1";
		}

		$query_table[] = '#__' . $element_table . ' as ' . $element;
		$element_table_name = $element;
		$query_condi[] = $element . "." . $element_field . " LIKE '%" . trim($element_val) . "%'";

		if (trim($autodata))
		{
			$query_condi[] = $element . "." . $element . " NOT IN ('" . trim($autodata) . "')";
		}

		$tables = (count($query_table) ? ' FROM ' . implode("\n LEFT JOIN ", $query_table) : '');

		if ($tables)
		{
			$where = (count($query_condi) ? ' WHERE ' . implode("\n AND ", $query_condi) : '');

			if ($where)
			{
				$db    = Factory::getDBO();
				$query = "SELECT distinct(" . $element_table_name . "." . $element . ")," . $element_table_name . "." . $element_field . "
				\n " . $tables . " \n " . $where;

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
				$json['label'] = $row['1'];

				// Id of the location
				$json['value'] = $row['0'];
				$data[] = $json;
			}
		}

		echo json_encode($data);
		jexit();
	}

	/**
	 * Function to save coupon
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function saveCoupon()
	{
		// Check for request forgeries
		$model = $this->getModel('managecoupon');
		$jinput = Factory::getApplication()->input;
		$post = $jinput->post;

		// Allow name only to contain html
		$model->setState('request', $post);

		if ($model->store($post))
		{
			$msg = Text::_('C_SAVE_M_S');
		}
		else
		{
			$msg = Text::_('C_SAVE_M_NS');
		}

		$task = $jinput->get('task');

		switch ($task)
		{
			case 'cancel':
				$cancelmsg = Text::_('FIELD_CANCEL_MSG');
				$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=managecoupon&layout=default", $cancelmsg);
			break;

			case 'saveCoupon':
				$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=managecoupon", $msg);
			break;
		}
	}

	/**
	 * Function to get coupon code
	 *
	 * @return  null
	 *
	 * @since  1.5
	 **/
	public function getcode()
	{
		$jinput       = Factory::getApplication()->input;
		$selectedcode = $jinput->get('selectedcode');
		$model        = $this->getModel('managecoupon');
		$coupon_code  = $model->getcode(trim($selectedcode));
		echo $coupon_code;
		exit();
	}

	/**
	 * Function to get selected coupon code
	 *
	 * @return  null
	 *
	 * @since  1.5
	 */
	public function getselectcode()
	{
		$jinput       = Factory::getApplication()->input;
		$selectedcode = $jinput->get('selectedcode');
		$couponid     = $jinput->get('couponid');
		$model        = $this->getModel('managecoupon');
		$coupon_code  = $model->getselectcode(trim($selectedcode), $couponid);
		echo $coupon_code;
		exit();
	}
}
