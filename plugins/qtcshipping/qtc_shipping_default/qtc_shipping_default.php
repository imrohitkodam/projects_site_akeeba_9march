<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

$lang = Factory::getLanguage();
$lang->load('plg_qtcshipping_qtc_shipping_default', JPATH_ADMINISTRATOR);

/**
 * System plguin
 *
 * @package     Plgshare_For_Discounts
 * @subpackage  site
 * @since       1.0
 */
class PlgQtcshippingqtc_Shipping_Default extends CMSPlugin
{
	/**
	 * [Gives applicable Shipping charges.]
	 *
	 * @param   integer  $amt   [cart subtotal (after discounted amount )]
	 * @param   object   $vars  [object with cartdetail,billing and shipping details.]
	 *
	 * @return  [type]         [it should return array that contain [charges]=>>shipcharges [DetailMsg]=>after_ship_totalamt or return empty array]
	 */
	public function onQ2cShipping($amt, $vars='')
	{
		$shipping_limit = $this->params->get('shipping_limit');
		$return = array();

		// These field must returned from each shipping plugins
		$return['allowToPlaceOrder'] = 1;
		$return['charges'] = 0;

		// If want to stop order (allowToPlaceOrder = 0 ) then add detail msg in this variable
		// This will be displayed in checkout process
		$return['detailMsg'] = '';

		if ((float) $amt < $shipping_limit)
		{
			$shipping_per = $this->params->get('shipping_per');

			// $shipping_value = ($shipping_per*$amt)/100;
			$return['charges'] = $shipping_per;
			$return['detailMsg'] = '';
		}

		return $return;
	}
}
