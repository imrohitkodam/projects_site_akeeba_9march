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

/**
 * Managecoupon controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerManagecoupon extends quick2cartController
{
	/**
	 * Method Findauto find the auto suggestion according the db.
	 *
	 * @return  Json formate data
	 *
	 * @since   1.6
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

		$autodata = str_replace("||", "','", $autodata);
		$autodata = str_replace('|', '', $autodata);

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

		$query_table[]      = '#__' . $element_table . ' as ' . $element;
		$element_table_name = $element;
		$query_condi[]      = $element . "." . $element_field . " LIKE '%" . trim($element_val) . "%'";

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
				$json          = array();

				// Name of the location
				$json['label'] = $row['1'];

				// Id of the location
				$json['value'] = $row['0'];
				$data[]        = $json;

				// Name of the location
				/*$data[] = $row['0'];*/
			}
		}

		echo json_encode($data);
		jexit();
	}

	/**
	 * Method Save fields.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function save()
	{
		// Check for request forgeries
		$app    = Factory::getApplication();
		$model  = $this->getModel('managecoupon');
		$jinput = $app->input;
		$post   = $jinput->post;

		// Allow name only to contain html
		$model->setState('request', $post);
		$cid       = $jinput->get('cid', '0');
		$cancelmsg = Text::_('FIELD_CANCEL_MSG');

		// VALIDATION ::  CHKECK FOR store_id
		$current_store = $post->get('current_store', '', 'INT');

		if (empty($current_store))
		{
			$invalid_storeid = Text::_('COUPON_INVALID_STOREID_MSG');
			$app->redirect("index.php?option=com_quick2cart&view=managecoupon&layout=default", $invalid_storeid);
		}

		// Check for coupon exit
		if (empty($cid))
		{
			$coupon_code = $post->get('code', '', 'RAW');

			// If coupon code is exit
			if ($model->getcode($coupon_code))
			{
				$app->redirect("index.php?option=com_quick2cart&view=managecoupon&layout=default", $cancelmsg);
			}
		}

		$msg = Text::_('C_SAVE_M_NS');

		if ($model->store($post))
		{
			$msg = Text::_('C_SAVE_M_S');
		}

		$task = $jinput->get('task');

		switch ($task)
		{
			case 'cancel':
				$app->redirect(Uri::base() . "index.php?option=com_quick2cart&view=managecoupon&layout=default", $cancelmsg);
				break;
			case 'save':
				$app->redirect(Uri::base() . "index.php?option=com_quick2cart&view=managecoupon&layout=default", $msg);
				break;
		}
	}

	/**
	 * Method Cancel.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function cancel()
	{
		$cancelmsg = Text::_('FIELD_CANCEL_MSG');
		$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=managecoupon&layout=default", $cancelmsg);
	}

	/**
	 * Method Getcode.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
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
	 * Method getselectcode.
	 *
	 * @return  void
	 *
	 * @since   1.6
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
