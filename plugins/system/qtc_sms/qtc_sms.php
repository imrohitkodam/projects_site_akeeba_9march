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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

/*load language file for plugin frontend*/
$lang = Factory::getLanguage();
$lang->load('plg_system_qtc_sms', JPATH_ADMINISTRATOR);

/**
 * PlgSystemQtc_Sms plugin
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class PlgSystemQtc_Sms extends CMSPlugin
{
	/**
	 * Onq2corderUpdate Trigger
	 *
	 * @param   Array  $order_obj  Order Object
	 * @param   Object  $data       User Info
	 *
	 * @return  array
	 *
	 * @since   2.7
	 */
	public function onAfterQ2cOrderPlace($order_obj, $data)
	{
		$ship            = $data->address->shipping;
		$bill            = $data->address->billing;
		$orederStatusArr = array('C', 'RF', 'S', 'P');

		if ($ship->phone)
		{
			$order_id_before_prefix = $ship->order_id;
			$mob_no  = $ship->phone;
		}
		elseif ($bill->phone)
		{
			$order_id_before_prefix = $bill->order_id;
			$mob_no  = $bill->phone;
		}

		if (in_array($order_obj['order']->status, $orederStatusArr))
		{
			$currentOrderStatus = $order_obj['order']->status;
		}

		// Check Here.
		switch ($currentOrderStatus)
		{
			case 'C' :
				$whichever = Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_CONFIRMED');
			break;

			case 'RF' :
				$whichever = Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_REFUND');
			break;

			case 'S' :
				$whichever = Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_SHIPPED');
			break;

			case 'P' :
				$whichever = Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_PENDING');
			break;
		}

		if (!class_exists('Quick2cartModelpayment'))
		{
			JLoader::register('Quick2cartModelpayment', JPATH_SITE . '/components/com_quick2cart/models/payment.php');
			JLoader::load('Quick2cartModelpayment');
		}

		$Quick2cartModelpayment = new Quick2cartModelpayment;
		$order_id_prefix        = $Quick2cartModelpayment->generate_prefix($order_id_before_prefix);
		$order_id               = $order_id_prefix . $order_id_before_prefix;
		$find                   = array('{ORDERNO}','{STATUS}');
		$replace                = array($order_id, $whichever);
		$message                = str_replace($find, $replace, Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_MESSAGE'));
		$plugin_name            = $this->params['sms_options'];
		$selected_order_status  = $this->params['order_status'] ? $this->params['order_status'] : array();

		if (in_array($whichever, $selected_order_status))
		{
			PluginHelper::importPlugin('tjsms');
			Factory::getApplication()->triggerEvent('onSend_SMS', array(trim($mob_no), $message));
		}
	}

	/**
	 * Onq2corderUpdate Trigger
	 *
	 * @param   Object  $orderobj        Order Object
	 *
	 * @param   Array  $orderIitemInfo  Order Item data
	 *
	 * @return  array
	 *
	 * @since   2.7
	 */
	public function onAfterQ2cOrderUpdate($orderobj, $orderIitemInfo)
	{
		$ship = $orderIitemInfo['order_info'][0];
		$bill = $orderIitemInfo['order_info'][1];

		$orederStatusArr = array('C', 'RF', 'S', 'P');

		if ($ship->phone)
		{
			$order_id_before_prefix = $orderobj->id;
			$mob_no  = $ship->phone;
		}
		elseif ($bill->phone)
		{
			$order_id_before_prefix = $bill->order_id;
			$mob_no  = $bill->phone;
		}

		if (in_array($orderobj->status, $orederStatusArr))
		{
			$currentOrderStatus = $orderobj->status;
		}

		// Check Here.
		switch ($currentOrderStatus)
		{
			case 'C' :
				$whichever = Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_CONFIRMED');
			break;

			case 'RF' :
				$whichever = Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_REFUND');
			break;

			case 'S' :
				$whichever = Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_SHIPPED');
			break;

			case 'P' :
				$whichever = Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_PENDING');
			break;
		}

		if (!class_exists('Quick2cartModelpayment'))
		{
			JLoader::register('Quick2cartModelpayment', JPATH_SITE . '/components/com_quick2cart/models/payment.php');
			JLoader::load('Quick2cartModelpayment');
		}

		$Quick2cartModelpayment = new Quick2cartModelpayment;
		$order_id_prefix        = $Quick2cartModelpayment->generate_prefix($order_id_before_prefix);
		$order_id              = $order_id_prefix . $order_id_before_prefix;
		$find                  = array('{ORDERNO}','{STATUS}');
		$replace               = array($order_id, $whichever);
		$message               = str_replace($find, $replace, Text::_('PLG_SYSTEM_QTC_SMS_ORDER_STATUS_MESSAGE'));
		$plugin_name           = $this->params['sms_options'];
		$selected_order_status = $this->params['order_status'] ? $selected_order_status : array();

		if (in_array($whichever, $selected_order_status))
		{
			PluginHelper::importPlugin('tjsms');
			Factory::getApplication()->triggerEvent('onSend_SMS', array(trim($mob_no), $message));
		}
	}
}
