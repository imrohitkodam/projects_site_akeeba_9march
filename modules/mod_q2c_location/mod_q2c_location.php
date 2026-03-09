<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2020 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

if (ComponentHelper::getComponent('com_quick2cart', true)->enabled)
{
	$quick2cartParams = ComponentHelper::getParams('com_quick2cart');
	$q2cPinwiseShippingPlugin = PluginHelper::getPlugin('qtcshipping', 'qtc_pinwise_shipping');

	$googleMapsApiKey = $params->get('google_map_api_key', '', 'STRING');
	$city             = $params->get('buyer_default_city');
	$location         = $params->get('buyer_default_location');
	$zipCode          = $params->get('buyer_default_pincode');
	$shippingMode     = $quick2cartParams->get('shippingMode', 'itemLevel', 'String');

	$serviceablePincodes = array();

	// We are checking here if qtc_pinwise_shipping plugin install and enabled? If yes, then have site admin configured Pincode for this. It is used to validate that for user-selected Pincode, shipping services are available or not
	if (!empty($q2cPinwiseShippingPlugin) && $shippingMode == 'orderLeval')
	{
		$pluginParams    = new Registry($q2cPinwiseShippingPlugin->params);
		$shippingCharges = $pluginParams->get('shipping_charges');

		if (!empty($shippingCharges))
		{
			foreach ($shippingCharges as $shippingSubformData)
			{
				$pincodeArr = array();

				if (!empty($shippingSubformData->pincode))
				{
					$pincodeArr = explode(",", $shippingSubformData->pincode);

					foreach ($pincodeArr as $key => $pinnum)
					{
						array_push($serviceablePincodes, trim($pinnum));
					}
				}
			}

			$serviceablePincodes = array_unique($serviceablePincodes);
		}
	}

	$user = Factory::getUser();
	$userId = (!empty($user->id)) ? $user->id : 0;
	$latestAddress = array();

	if (!empty($userId))
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__kart_customer_address', 'ca'));
		$query->where($db->qn('ca.user_id') . ' = ' . (int) $userId);
		$db->setQuery($query);
		$addresses = $db->loadObjectList();

		$comquick2cartHelper = new comquick2cartHelper;
		$modelsPath          = JPATH_SITE . '/components/com_quick2cart/models/cartcheckout.php';
		$cartCheckoutModel   = $comquick2cartHelper->loadqtcClass($modelsPath, "Quick2cartModelcartcheckout");

		$userCountry = array();
		$userState   = array();

		if (!empty($addresses))
		{
			foreach ($addresses as $item)
			{
				$shippingFlag = (!empty($item->last_used_for_shipping)) ? 1 : 0;
				$billingFlag  = (!empty($item->last_used_for_billing)) ? 1 : 0;
			}

			// Pre select first address as shipping address
			if (empty($shippingFlag))
			{
				$addresses[0]->last_used_for_shipping = 1;
			}

			// Pre select first address as billing address
			if (empty($billingFlag))
			{
				$addresses[0]->last_used_for_billing = 1;
			}

			foreach ($addresses as $item)
			{
				if (!array_key_exists($item->country_code, $userCountry) && (!empty($item->country_code)))
				{
					$userCountry[$item->country_code] = $cartCheckoutModel->getCountryName($item->country_code);
				}

				$item->country_name = (isset($userCountry[$item->country_code])) ? $userCountry[$item->country_code] : '';

				if (!array_key_exists($item->state_code, $userState) && (!empty($item->state_code)))
				{
					$userState[$item->state_code] = $cartCheckoutModel->getStateName($item->state_code);
				}

				$item->state_name = (isset($userState[$item->state_code])) ? $userState[$item->state_code] : '';

				if ($item->last_used_for_billing == 1 || $item->last_used_for_billing == 1)
				{
					$latestAddress = $item;
				}
			}
		}
	}

	if (!empty($latestAddress))
	{
		$city     = $latestAddress->city;
		$location = $latestAddress->address . ', ' . $latestAddress->country_name;
		$zipCode  = $latestAddress->zipcode;
	}

	Text::script('MOD_Q2C_LOCATION_LOCATOR_DENIED');
	Text::script('MOD_Q2C_LOCATION_NO_SHIPPING_AVAILABLE');
	Text::script('MOD_Q2C_LOCATION_SELECT_LOCALITY_OR_PINCODE');

	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
	require ModuleHelper::getLayoutPath('mod_q2c_location', $params->get('layout', 'default'));
}
