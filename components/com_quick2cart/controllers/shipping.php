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
defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Shipping list controller class
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerShipping extends quick2cartController
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();

		//$this->set('suffix', 'shipping');

		$this->suffix = 'shipping';
	}

	/**
	 * Method Ship view.
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function getShipView()
	{
		$app                 = Factory::getApplication();
		$qtcshiphelper       = new qtcshiphelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$plgActionRes        = array();

		$jinput       = $app->input;
		$extension_id = $jinput->get('extension_id');
		$plugview     = $jinput->get('plugview');

		// Plugin view is not found in URL then check in post array.
		if (empty($plugview))
		{
			$plugview = $jinput->post->get('plugview');
		}

		// If extension related view
		if (!empty($extension_id))
		{
			$plugName = $qtcshiphelper->getPluginDetail($extension_id);

			// Call specific plugin trigger
			PluginHelper::importPlugin('tjshipping', $plugName);
			$plgRes = $app->triggerEvent('onTjShip_plugActionkHandler', array($jinput));

			if (!empty($plgRes))
			{
				$plgActionRes = $plgRes[0];
			}
		}
		// Enque msg
		if (!empty($plgActionRes['statusMsg']))
		{
			$app->enqueueMessage($plgActionRes['statusMsg']);
		}

		$plgUrlParam = '&plugview=';

		// Extra plugin Url params.
		if (!empty($plgActionRes['urlPramStr']))
		{
			$plgUrlParam = '&' . $plgActionRes['urlPramStr'];
		}

		$itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$link   = 'index.php?option=com_quick2cart&view=shipping&layout=list' . $plgUrlParam . '&extension_id=' . $extension_id . '&Itemid=' . $itemid;
		$this->setRedirect(Route::_($link, false));
	}

	/**
	 * This function calls respective task on respective plugin.
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function qtcHandleShipAjaxCall ()
	{
		$plgActionRes  = '';
		$app           = Factory::getApplication();
		$qtcshiphelper = new qtcshiphelper;
		$jinput        = $app->input;
		$extension_id  = $jinput->get('extension_id');

		// Get plugin detail
		$plugName = $qtcshiphelper->getPluginDetail($extension_id);

		// Call specific plugin trigger
		PluginHelper::importPlugin('tjshipping', $plugName);
		$plgRes = $app->triggerEvent('onTjShip_AjaxCallHandler', array($jinput));

		if (!empty($plgRes))
		{
			$plgActionRes = $plgRes[0];
		}

		echo $plgActionRes;
		$app->close();
	}

	/**
	 * Function checkDeliveryAvailability.
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function checkDeliveryAvailability()
	{
		$app    = Factory::getApplication();
		$input  = $app->input;
		$params = ComponentHelper::getParams('com_quick2cart');

		// @TODO SET ITEM LEVEL AS DEF
		$shippingMode     = $params->get('shippingMode', 'itemLevel');
		$store_id         = $input->get('store_id', '0', 'int');
		$item_id          = $input->get('item_id', '', 'int');
		$delivery_pincode = $input->get('delivery_pincode', '', 'int');

		$shippingInfo                   = new stdclass;
		$shippingInfo->store_id         = $store_id;
		$shippingInfo->item_id          = $item_id;
		$shippingInfo->delivery_pincode = $delivery_pincode;

		if ($shippingMode == "orderLeval")
		{
			PluginHelper::importPlugin('qtcshipping');
			$app->triggerEvent('onGetShippingProviders', array($shippingInfo));
		}
		else
		{
			PluginHelper::importPlugin('tjshipping');
			$app->triggerEvent('onGetShippingProviders', array($shippingInfo));
		}
	}
}
