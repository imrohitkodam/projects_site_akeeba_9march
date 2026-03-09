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

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Class Qtc Pinwise Shipping
 *
 * This plugin is used to apply order level shipping charges for valid Pincode/zipcode
 *
 * @since  __DEPLOYE_VERSION__
 */
class PlgQtcshippingQtc_Pinwise_Shipping extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  __DEPLOYE_VERSION__
	 */
	protected $autoloadLanguage = true;

	/**
	 * [Gives applicable Shipping charges.]
	 *
	 * @param   Integer  $amt   [cart subtotal (after discounted amount )]
	 * @param   Object   $vars  [object with cartdetail,billing and shipping details.]
	 *
	 * @return  Array    $return
	 *
	 * since __DEPLOY_VERSION__
	 */
	public function onQ2cShipping($amt, $vars='')
	{
		$shippingCharges             = $this->params->get('shipping_charges');
		$shippingLimit               = $this->params->get('shipping_limit', 200, 'FLOAT');
		$cartItems                   = $vars->cartItemDetail;
		$zipCode                     = $vars->shipping_address['zip'];

		$return                      = array();
		$return['allowToPlaceOrder'] = 1;
		$return['charges']           = 0;
		$return['detailMsg']         = '';

		// Check here is plugin configured.
		if (empty($shippingCharges))
		{
			$return['allowToPlaceOrder'] = 0;
			$return['charges']           = 0;
			$return['detailMsg']         = Text::_("PLG_QTCSHIPPING_QTC_PINWISE_NO_SHIPPING_CHARGES_CONFIGURED");

			return $return;
		}

		$cartItemStoreIds       = array();
		$pluginSubFormStoreIds  = array();
		$pluginStoreShippingArr = array();

		// Get store ids from cart item
		foreach ($cartItems as $key => $item)
		{
			$cartModel          = BaseDatabaseModel::getInstance('cart', 'Quick2cartModel', array('ignore_request' => true));
			$itemDetails        = $cartModel->getItemRec($item['item_id']);

			// Get unique store ids form cartitems
			if (!in_array($itemDetails->store_id, $cartItemStoreIds))
			{
				$cartItemStoreIds[] = $itemDetails->store_id;
			}
		}

		/* $pluginStoreShippingArr output
		Array
		(
			[2] => Array
				(
					[0] => stdClass Object
						(
							[pinwise_shipping_charges] => 30
							[select_store] => 2
							[pincode] => 1234
						)
				)
		)*/
		foreach ($shippingCharges as $key => $value)
		{
			// Get unique store ids configured in plugin subform
			if (!in_array($value->select_store, $pluginSubFormStoreIds))
			{
				$pluginSubFormStoreIds[] = $value->select_store;
			}

			$pluginStoreShippingArr[$value->select_store][] = $value;
		}

		$appliedShippingCharges = 0;

		foreach($cartItemStoreIds as $storeId)
		{
			// Check is cart item store configured shipping charges in plugin or not
			if ($this->isStoreConfigured($storeId,$pluginStoreShippingArr))
			{
				$storeHelper  = new StoreHelper();
				$storeDetails = $storeHelper->getStoreDetail($storeId);

				$return['allowToPlaceOrder'] = 0;
				$return['charges']           = 0;
				$return['detailMsg']         = Text::sprintf("PLG_QTCSHIPPING_QTC_PINWISE_NO_SHIPPING_CHARGES_FOR_STORE", $storeDetails['title']);

				return $return;
			}

			// Validate shipping zipcode with allowed store specific pincode
			$validPinCodeWiseShippingcharges = $this->onGetValidShippingCharges($storeId, $zipCode);

			if (!count($validPinCodeWiseShippingcharges))
			{
				$return['allowToPlaceOrder'] = 0;
				$return['charges']           = 0;
				$return['detailMsg']         = Text::sprintf("PLG_QTCSHIPPING_QTC_PINWISE_PINCODE_IS_NOT_AVAILABEL", $zipCode);

				return $return;
			}

			// Get minimum shipping charges as per zipcode
			// If site admin has configured same store with different shipping changres and same pincode then apply min shipping changres to the user
			$appliedShippingCharges += $this->getMinShippingCharges($validPinCodeWiseShippingcharges);
		}

		if ((float) $amt < $shippingLimit)
		{
			$return['charges']   = $appliedShippingCharges;
			$return['detailMsg'] = Text::_("PLG_QTCSHIPPING_QTC_SUCCESS");
		}

		return $return;
	}

	/**
	 * Checking here in plugin for given store id shipping changres are configured or not
	 *
	 * @param   Integer  $storeId                 Store Id
	 * @param   Array    $pluginStoreShippingArr  Associated array of Storeid with shipping charges and pincodes
	 *
	 * 	Array
		(
			[2] => Array
				(
					[0] => stdClass Object
						(
							[pinwise_shipping_charges] => 30
							[select_store] => 2
							[pincode] => 1234
						)
				)
		)
	 * @return  boolean    true/false
	 *
	 * since __DEPLOY_VERSION__
	 */
	protected function isStoreConfigured($storeId, $pluginStoreShippingArr)
	{
		//For this shop shipping charges not configured
		return (!array_key_exists($storeId, $pluginStoreShippingArr));
	}

	/**
	 * Checking here is given store configured or not in plugin
	 *
	 * @param   Array    $validShippingCharges  Valid shipping changres array
	 *
	 * @return  integer    min shipping charges
	 *
	 * since __DEPLOY_VERSION__
	 */
	protected function getMinShippingCharges($validShippingCharges)
	{
		return min(array_map(function($item){
			return $item->pinwise_shipping_charges;
		},$validShippingCharges));
	}

	/**
	 * Validate pincode and return valid array
	 *
	 * @param   Integer  $storeId  Store Id
	 * @param   String   $zipCode  Buyer Shipping Address zipcode
	 *
	 * @return  Array    valid array contain (pincode data, shipping charges and store id)
	 *
	 * since __DEPLOY_VERSION__
	 */
	public function onGetValidShippingCharges($storeId, $zipCode)
	{
		$shippingCharges = $this->params->get('shipping_charges');
		$pluginSubFormStoreIds  = array();
		$pluginStoreShippingArr = array();

		foreach ($shippingCharges as $key => $value)
		{
			// Get unique store ids configured in plugin subform
			if (!in_array($value->select_store, $pluginSubFormStoreIds))
			{
				$pluginSubFormStoreIds[] = $value->select_store;
			}

			$pluginStoreShippingArr[$value->select_store][] = $value;
		}

		return array_filter($pluginStoreShippingArr[$storeId], function($item) use($zipCode){
			return in_array($zipCode, explode(",", $item->pincode));
		});
	}
}
