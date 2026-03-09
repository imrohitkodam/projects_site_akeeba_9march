<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

/**
 * Quick2cartModelcartcheckout
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class Quick2cartModelcartcheckout extends BaseDatabaseModel
{
	/**
	 * Function to fetch user data
	 *
	 * @return array
	 */
	public function userdata()
	{
		$params   = ComponentHelper::getParams('com_quick2cart');
		$user     = Factory::getUser();
		$userdata = array();
		$query    = "SELECT u.*
		FROM #__kart_users as u
		WHERE  u.user_id = " . $user->id . " order by u.id DESC LIMIT 0 , 2";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		if (!empty($result))
		{
			if ($result[0]->address_type == 'BT')
			{
				$userdata['BT'] = $result[0];

				if ($params->get('shipping') == '1' && !empty($result[1]))
				{
					$userdata['ST'] = $result[1];
				}
			}
			elseif ($result[1]->address_type == 'BT')
			{
				$userdata['BT'] = $result[1];

				if ($params->get('shipping') == '1')
				{
					$userdata['ST'] = $result[0];
				}
			}
		}
		else
		{
			$row = new stdClass;
			$row->user_email = $user->email;
			$userdata['BT']  = $row;
			$userdata['ST']  = $row;
		}

		return $userdata;
	}

	/**
	 * Function to get coupon. [Called while checking the coupon from cart view]
	 *
	 * @param   STRING  $c_code    coupon code
	 * @param   INT     $user_id   user id
	 * @param   STRING  $called    called from
	 * @param   STRING  $order_id  order id
	 *
	 * @return coupon
	 */
	public function getPromoCoupon($c_code = "", $user_id = "", $called = "cart", $order_id = 0)
	{
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/promotion.php';

		if (!class_exists('PromotionHelper'))
		{
			JLoader::register('PromotionHelper', $path);
			JLoader::load('PromotionHelper');
		}

		$PromotionHelper = new PromotionHelper;

		// GETTING CART ITEMS
		JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
		$cartCheckoutModel = new Quick2cartModelcartcheckout;
		$cart              = $cartCheckoutModel->getCheckoutCartitemsDetails();

		// Get valid and all rules for promotion (rules are not validted against cart detail)
		$data['coupon_code'] = $c_code;

		// 0 then get all promotion. If 1 then get only coupon based promotions
		$data['promoType'] = 1;
		$validPromotions   = $PromotionHelper->getValidatePromotions($data);

		if (empty($validPromotions))
		{
			return false;
		}

		// TO validatat, the promotions rules, Format the cart data according to promo
		$formattedCartData = $PromotionHelper->getFormattedCartDetailForPromotion($cart);

		// Rules are not validted against cart detail)
		return $PromotionHelper->getApplicablePromotions($validPromotions, $formattedCartData);
	}

	/**
	 * Function to get coupon
	 *
	 * @param   STRING  $c_code    coupon code
	 * @param   INT     $user_id   user id
	 * @param   STRING  $called    called from
	 * @param   STRING  $order_id  order id
	 *
	 * @return coupon
	 */
	public function getcoupon($c_code = "", $user_id = "", $called = "cart", $order_id = 0)
	{
		$jinput = Factory::getApplication()->input;

		if ($user_id == "")
		{
			$user_id = Factory::getUser()->id;
		}

		$db = Factory::getDbo();

		if (!$c_code)
		{
			$c_code = $jinput->get('coupon_code');
		}

		// MULTIVENDOR ON THEN DONT ALLOW TO APPLY GLOBAL COUPON, store coupon
		$params             = ComponentHelper::getParams('com_quick2cart');
		$multivendor_enable = $params->get('multivendor');
		$noGlobalCop        = '';

		if (!empty($multivendor_enable) & empty($order_id))
		{
			// NO GLOBAL COUPON
			$noGlobalCop = ' AND (cop.`store_id` IS NOT NULL  AND cop.`store_id` <> 0) ';

			// NO STORE RELEATED COUPON
			$noGlobalCop .= ' AND (cop.`item_id` IS NOT NULL ) ';
		}

		$query = "SELECT value,val_type,store_id,
		CASE WHEN store_id IS NOT NULL
		THEN CONCAT( item_id ,',',max_use,',', max_per_user)
		ELSE item_id
		END as item_use_per_user
				FROM #__kart_coupon as cop
				WHERE
				published = 1
				AND code=" . $db->quote($db->escape($c_code)) . "
				AND	 ( (CURDATE() BETWEEN from_date AND exp_date)   OR from_date = '0000-00-00 00:00:00')
				AND (max_use  > (SELECT COUNT(api.coupon_code) FROM #__kart_orders as api
				WHERE api.coupon_code =" . $db->quote($db->escape($c_code)) . ") OR max_use=0)
				AND (max_per_user > (SELECT COUNT(api.coupon_code)
				FROM #__kart_orders as api WHERE api.coupon_code = " . $db->quote($db->escape($c_code)) . " AND api.payee_id= " . $user_id . ") OR max_per_user=0)
				AND
					CASE WHEN user_id IS NOT NULL THEN user_id LIKE '%|" . $user_id . "|%'
					ELSE 1
					END
				" . $noGlobalCop;
		$db->setQuery($query);
		$count = $db->loadObjectList();

		if (!empty($count[0]) && strpos($count[0]->item_use_per_user, '|') !== false)
		{
			// Coupon is product related
			$count[0]->item_id = $this->getCop_item($count[0]->item_use_per_user, $c_code, $user_id, $called, $order_id);

			if (!empty($count[0]->item_id))
			{
				return $count;
			}
			else
			{
				return array();
			}
		}
		elseif(!empty($count[0]) && empty($count[0]->item_use_per_user))
		{
			$count[0]->item_id = array();
		}
		elseif (!empty($count[0]) && !empty($count[0]->store_id) )
		{
			// Coupon is store related
			$query = "SELECT i.item_id
			FROM #__kart_items as i
			WHERE i.store_id =" . $this->_db->escape($count[0]->store_id) . " AND  i.item_id IN (";

			if ($called == "cart")
			{
				$Quick2cartModelcart = new Quick2cartModelcart;
				$cartid = $Quick2cartModelcart->getCartId();
				$query .= "SELECT kc.item_id
				FROM #__kart_cartitems as kc
				WHERE kc.cart_id =" . $cartid;
			}
			else
			{
				$query .= "SELECT oi.item_id
				FROM #__kart_order_item as oi
				WHERE oi.order_id =" . $order_id;
			}

			$query .= ")";
			$this->_db->setQuery($query);
			$in_cop_store = $this->_db->loadColumn();

			if (!empty($in_cop_store))
			{
				$count[0]->item_id = $in_cop_store;

				return $count;
			}
			else
			{
				return array();
			}
		}

		return $count;
	}

	/**
	 * function to get coupon items
	 *
	 * @param   INT     $item_use_per_user  count
	 * @param   STRING  $cop_code           coupon code
	 * @param   INT     $user_id            user id
	 * @param   INT     $called             called from
	 * @param   INT     $order_id           order id
	 *
	 * @return null
	 */
	public function getCop_item($item_use_per_user, $cop_code, $user_id, $called="cart", $order_id=0)
	{
		$cart_item = array();

		if (isset($item_use_per_user))
		{
			$item_use_per_user_array = explode(",", $item_use_per_user);
			$countitem_id            = substr($item_use_per_user_array[0], 1, -1);

			// Fetch all the item ids from coupon
			$cop_itemids = explode("||", $countitem_id);

			if ($called == "cart")
			{
				$Quick2cartModelcart = new Quick2cartModelcart;
				$cartid = $Quick2cartModelcart->getCartId();
			}

			foreach ($cop_itemids as $cop_itemid)
			{
				// Run a loop on all item ids on order item table
				$cart_item_id = $max_use = $max_peruser = $in_cop_store = '';

				if ($called == "cart")
				{
					$query = "SELECT kc.item_id
					FROM #__kart_cartitems as kc
					WHERE kc.cart_id =" . $cartid . " AND kc.item_id LIKE '" . $cop_itemid . "' ";
				}
				else
				{
					$query = "SELECT oi.item_id
					FROM #__kart_order_item as oi
					WHERE oi.order_id =" . $order_id . " AND oi.item_id LIKE '" . $cop_itemid . "' ";
				}

				$this->_db->setQuery($query);
				$cart_item_id = $this->_db->loadResult();

				if ($item_use_per_user_array[1] != 0)
				{
					$query = "SELECT COUNT(oi.params)
					FROM #__kart_order_item as oi
					WHERE oi.params LIKE '%" . $this->_db->escape($cop_code) . "%'
					AND oi.order_id=(SELECT o.id FROM #__kart_orders as o WHERE o.id= oi.order_id AND o.status='C')";
					$this->_db->setQuery($query);
					$max_use = $this->_db->loadResult();
				}

				if ($item_use_per_user_array[2] != 0)
				{
					$query = "SELECT COUNT(oi.params)
					FROM #__kart_order_item as oi
					WHERE oi.params LIKE '%" . $this->_db->escape($cop_code) . "%'
					AND oi.order_id=(SELECT o.id FROM #__kart_orders as o WHERE o.id= oi.order_id AND o.status='C' AND o.payee_id =" . $user_id . ")";
					$this->_db->setQuery($query);
					$max_peruser = $this->_db->loadResult();
				}

				$cart_item_flag = 0;

				if ($cart_item_id && $item_use_per_user_array[1] > $max_use && $item_use_per_user_array[2] > $max_peruser)
				{
					$cart_item_flag = 1;
				}
				else
				{
					$cart_item_flag = 0;
				}

				if ($cart_item_flag)
				{
					$cart_item[] = $cart_item_id;
				}
			}
		}

		return $cart_item;
	}

	/**
	 * Function to get country name
	 *
	 * @param   INT  $countryId  id
	 *
	 * @return country name
	 */
	public function getCountryName($countryId)
	{
		$db    = Factory::getDbo();
		$query = "SELECT `country` FROM `#__tj_country` where id=" . $countryId;
		$db->setQuery($query);
		$rows = $db->loadResult();

		return $rows;
	}

	/**
	 * Function to get state name
	 *
	 * @param   INT  $stateId  product data
	 *
	 * @return state name
	 */
	public function getStateName($stateId)
	{
		$db    = Factory::getDbo();
		$query = "SELECT `region` FROM `#__tj_region` where id=" . $stateId;
		$db->setQuery($query);
		$rows = $db->loadResult();

		return $rows;
	}

	/**
	 * Function to set session
	 *
	 * @param   ARRAY  $data  product data
	 *
	 * @return status
	 */
	public function setsession($data)
	{
		$session = Factory::getSession();
		$session->set('order_id', $data['order_id']);
	}

	/**
	 * function to get formated data
	 *
	 * @param   ARRAY  $data  product data
	 *
	 * @return recalculated data
	 */
	public function getFormated_Data($data)
	{
		$amt    = 0;
		$detail = array();
		$i      = 0;

		foreach ($data['val'] as $k => $val)
		{
			$detail[$i]['val'] = $val;
			$detail[$i]['amt'] = $data['amt'][$k];
			$amt += $data['amt'][$k];
			$i++;
		}

		$result['detail'] = json_encode($detail);
		$result['val']    = $amt;

		return $result;
	}

	/**
	 * This function stores order details in kart_order table
	 *
	 * @return array
	 */
	public function store()
	{
		$app            = Factory::getApplication();
		$user           = Factory::getUser();
		$buildadsession = Factory::getSession();
		$jinput         = $app->input;
		$data           = $jinput->post;
		$params         = ComponentHelper::getParams('com_quick2cart');

		$comquick2cartHelper = new comquick2cartHelper;
		$helperPath          = JPATH_SITE . '/components/com_quick2cart/helpers/createorder.php';
		$createOrderHelper   = $comquick2cartHelper->loadqtcClass($helperPath, "CreateOrderHelper");

		// To check if data
		$data->set('allowToPlaceOrder', 1);
		$orderId             = $data->get('order_id', '', "RAW");
		$userPrivacyAccepted = 0;

		// Load helper file
		JLoader::import('components.com_quick2cart.helpers.products', JPATH_SITE);
		$productHelper = new productHelper;

		$orderStatus = array();

		if (!empty($params->get('termsConditons', 0)) && !empty($params->get('termsConditonsArtId', 0)))
		{
			/*User Privacy Policy Data sanitize here*/
			if (empty($data->get('qtc_accpt_terms', '', 'STRING')) || $data->get('qtc_accpt_terms', '', 'STRING') != 'on')
			{
				$orderStatus['success']     = 0;
				$orderStatus['success_msg'] = Text::_('COM_QUICK2CART_CHECKOUT_ZERO_ORDER_IS_NOT_ALLOWED');
				$orderStatus['order_id']    = 0;

				return $orderStatus;
			}
			else
			{
				$userPrivacyAccepted = 1;
			}
		}

		$Quick2cartModelcart = new Quick2cartModelcart;
		$cart_id             = $Quick2cartModelcart->getCartId();
		$cart_itemsdata      = $Quick2cartModelcart->getCartitems();

		$addressDetails = new stdclass;

		$modelsPath = JPATH_SITE . '/components/com_quick2cart/models/customer_addressform.php';
		$customer_addressform_model = $comquick2cartHelper->loadqtcClass($modelsPath, "Quick2cartModelCustomer_AddressForm");

		// GET BILLING AND SHIPPING ADDRESS - Start
		if (empty($user->id))
		{
			$bill                    = (object) $data->get('bill', array(), "ARRAY");
			$addressDetails->billing = (object) $createOrderHelper->reMapUserAddress($bill);

			$bill     = (array) $bill;
			$ship_chk = $data->get('ship_chk', '0', "INT");

			if (empty($ship_chk))
			{
				$ship = (object) $data->get('ship', array(), "ARRAY");
				$addressDetails->shipping = (object) $createOrderHelper->reMapUserAddress($ship);
				$ship = (array) $ship;
			}
			else
			{
				$addressDetails->shipping = $addressDetails->billing;
			}
		}
		else
		{
			$shipping = $data->get('shipping_address', '', 'INT');
			$billing  = $data->get('billing_address', '', 'INT');

			if (!empty($shipping))
			{
				$shipping = $customer_addressform_model->getAddress($shipping);
				$addressDetails->shipping = $shipping;
			}

			if (!empty($billing))
			{
				$billing = $customer_addressform_model->getAddress($billing);
				$addressDetails->billing = $billing;
			}
		}

		// GET BILLING AND SHIPPING ADDRESS - End

		$orderData          = new stdclass;
		$orderData->userId  = (!empty($user->id))?$user->id:'0';
		$orderData->address = $addressDetails;

		$orderData->products_data = array();

		foreach ($cart_itemsdata as $item)
		{
			$quick2cartModelcart = new Quick2cartModelcart;
			$productDetail       = $quick2cartModelcart->getItemRec($item['item_id']);
			$attributes          = $productHelper->getItemCompleteAttrDetail($item['item_id']);

			if (!empty($attributes))
			{
				$productDetail->itemAttributes = $attributes;
			}

			$productInStock = $productHelper->isInStockProduct($productDetail);

			$itemDetail                     = array();
			$itemDetail['store_id']         = $item['store_id'];
			$itemDetail['product_id']       = $item['item_id'];
			$itemDetail['product_quantity'] = abs($item['qty']);

			$cartDetail = $jinput->get("cartDetail", '', "ARRAY");

			if (!empty($item['product_attributes']))
			{
				$attributes = explode(",", $item['product_attributes']);
				$attributes = array_filter($attributes);

				$att_option = array();

				foreach ($attributes as $attributeOption)
				{
					$attInputOption = array();
					$attributeId    = $this->getAttributeId($attributeOption);

					if (!empty($attributeOption))
					{
						// If input option is used
						if (!empty($cartDetail[$item['id']]['attrDetail'][$attributeId]['type'])
							&& $cartDetail[$item['id']]['attrDetail'][$attributeId]['type'] == 'Textbox')
						{
							$attInputOptionType  = $cartDetail[$item['id']]['attrDetail'][$attributeId]['type'];
							$attInputOptionValue = $cartDetail[$item['id']]['attrDetail'][$attributeId]['value'];
							$attInputOptionId    = $attributeOption;

							$attInputOption['value']     = $attInputOptionValue;
							$attInputOption['option_id'] = $attInputOptionId;
							$att_option[$attributeId]    = $attInputOption;
						}
						else
						{
							$attributeId              = $this->getAttributeId($attributeOption);
							$att_option[$attributeId] = $attributeOption;
						}
					}
				}

				$itemDetail['att_option'] = $att_option;
			}

			if($productInStock == 1)
			{
				$orderData->products_data[] = $itemDetail;
			}
			else
			{
				$orderStatus['success']     = 0;
				$orderStatus['success_msg'] = Text::_('COM_QUICK2CART_CHECKOUT_OUT_OF_STOCK');
				$orderStatus['order_id']    = 0;

				return $orderStatus;
			}
		}

		$qtc_guest_regis = $data->get('qtc_guest_regis', '', "STRING");

		if (!$user->id)
		{
			$user->id = 0;

			// Register a new User if Checkout Method is Register
			if (!empty($qtc_guest_regis) && $qtc_guest_regis != "guest")
			{
				$regdata['user_name']  = $bill['email1'];
				$regdata['user_email'] = $bill['email1'];
				$regdata['name']       = $bill['fnam'];
				JLoader::import('registration', JPATH_SITE . '/components/com_quick2cart/models');

				$Quick2cartModelregistration = new Quick2cartModelregistration;
				$mesage = $Quick2cartModelregistration->store($regdata);

				if ($mesage)
				{
					$user              = Factory::getUser();
					$userid            = $user->id;
					$orderData->userId = $userid;

					$bill = (object) $createOrderHelper->reMapUserAddress($bill);
					$customer_addressform_model->save($bill);
				}
				else
				{
					$orderStatus['success']     = 0;
					$orderStatus['success_msg'] = Text::_('ERR_CONFIG_SAV_LOGIN');
					$orderStatus['order_id']    = 0;

					return $orderStatus;
				}
			}
		}

		$isAllowedZeroPriceOrder = $params->get('orderWithZeroPrice', 0);
		$totalOrderAmount        = 0;

		if (!empty($orderData->products_data))
		{
			$totalOrderAmount = $createOrderHelper->calculateOrderPrice($orderData->products_data);
		}

		// Edit from 1page ckout
		if (empty($orderId))
		{
			if (empty($isAllowedZeroPriceOrder))
			{
				// If FINAL orderPRICE <=0 THEN DONT ALLOW FOR ORDER
				if ($totalOrderAmount <= 0)
				{
					$orderStatus['success']     = 0;
					$orderStatus['success_msg'] = Text::_('COM_QUICK2CART_CHECKOUT_ZERO_ORDER_IS_NOT_ALLOWED');
					$orderStatus['order_id']    = 0;

					return $orderStatus;
				}
			}
		}

		$path = JPATH_SITE . '/components/com_quick2cart/helpers/promotion.php';

		if (!class_exists('PromotionHelper'))
		{
			JLoader::register('PromotionHelper', $path);
			JLoader::load('PromotionHelper');
		}

		$PromotionHelper          = new PromotionHelper;
		$coupon_code              = $PromotionHelper->getSessionCoupon();
		$orderData->coupon_code   = (!empty($coupon_code))?$coupon_code:'';
		$orderData->customer_note = $data->get('comment', '', "String");

		//Set Delivery Date and time;
		$allowToScheduleDeliverySlot = $params->get('allow_schedule_delivery_slot', '0', 'String');

		if (isset($allowToScheduleDeliverySlot) && $allowToScheduleDeliverySlot == '1')
		{
			$orderDeliveryDetails = array();
			$orderDeliveryDetails['deliveryDate'] = $data->get('deliveryDate', '', "String");
			$orderDeliveryDetails['deliveryTime'] = $data->get('deliveryTime', '', "String");

			if (!empty($orderDeliveryDetails))
			{
				$itemDeliveryDateDetails     = $this->getitemDeliveryDateDetails($orderDeliveryDetails);
				$orderData->deliveryfromdate = $itemDeliveryDateDetails['deliveryfromdate'];
				$orderData->deliverytodate   = $itemDeliveryDateDetails['deliverytodate'];
			}
		}

		//Get payment gateway
		$orderData->processor = $data->get('gateways');

		// Place order
		$orderStatus = (array) $createOrderHelper->qtc_place_order($orderData);

		// Terms and condition adding in TJ privacy
		if (!empty($orderStatus['order_id']))
		{
			if (!empty($params->get('termsConditons', 0)) && !empty($params->get('termsConditonsArtId', 0)))
			{
				// Save User Privacy Terms and conditions Data
				$userPrivacyTable = Table::getInstance('tj_consent', 'TjprivacyTable', array());
				$userPrivacyData  = $userPrivacyTable->load(
					array(
							'client' => 'com_quick2cart.order',
							'client_id' => $orderStatus['order_id'] ,
							'user_id' => (!empty($user->id))?$user->id:'0'
						)
				);

				if ($userPrivacyData == false)
				{
					$userPrivacyDataArr              = array();
					$userPrivacyDataArr['client']    = 'com_quick2cart.order';
					$userPrivacyDataArr['client_id'] = $orderStatus['order_id'];
					$userPrivacyDataArr['user_id']   = (!empty($user->id))?$user->id:'0';
					$userPrivacyDataArr['purpose']   = Text::_('COM_QUICK2CART_USER_PRIVACY_TERMS_PURPOSE_FOR_ORDER');
					$userPrivacyDataArr['accepted']  = $userPrivacyAccepted;
					$userPrivacyDataArr['date']      = Factory::getDate('now')->toSQL();

					$tjprivacyModelObj = BaseDatabaseModel::getInstance('tjprivacy', 'TjprivacyModel');
					$tjprivacyModelObj->save($userPrivacyDataArr);
				}
			}
		}

		// Clear the seesion coupon
		$session = Factory::getSession();
		$session->clear("coupon");

		return $orderStatus;
	}

	/**
	 * function to store order items (Currently not used in quick2cart remove after jomsocial notification fix)
	 *
	 * @param   INT     $insert_order_id    order id
	 * @param   ARRAY   $cart_itemsdata     item data
	 * @param   Object  $data               item data
	 * @param   ARRAY   $updateOrderstatus  status
	 *
	 * @since   2.2
	 * @return   status
	 */
	public function addSaveOrderItems($insert_order_id, $cart_itemsdata, $data, $updateOrderstatus)
	{
		$productHelper       = new productHelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$params              = ComponentHelper::getParams('com_quick2cart');

		// GET BILLING AND SHIPPING ADDRESS
		$bill               = $data->get('bill', array(), "ARRAY");
		$ship               = $data->get('ship', array(), "ARRAY");
		$itemsTaxDetail     = $data->get('itemsTaxDetail', array(), 'ARRAY');
		$itemShipMethDetail = $data->get('itemShipMethDetail', array(), 'ARRAY');

		// Row_id= last insert id
		$data->set('order_id', $insert_order_id);

		$store_info = array();

		foreach ($cart_itemsdata as $key => $cart_items)
		{
			$item_id    = $cart_items['item_id'];
			$taxdetail  = '';
			$shipdetail = '';

			// Get item tax detail
			if (!empty($itemsTaxDetail[$key]))
			{
				// Get current item tax detail
				$taxdetail = $itemsTaxDetail[$key];
			}

			// Get item ship detail
			if (!empty($itemShipMethDetail[$key]))
			{
				// Get current item tax detail
				$shipdetail = $itemShipMethDetail[$key];
			}

			$items                  = new stdClass;
			$items->order_id        = $insert_order_id;
			$items->item_id         = $item_id;
			$items->variant_item_id = $cart_items['variant_item_id'];

			// Getting store id from item_id
			$items->store_id                 = $comquick2cartHelper->getSoreID($cart_items['item_id']);
			$items->product_attributes       = $cart_items['product_attributes'];
			$items->product_attribute_names  = $cart_items['options'];
			$items->order_item_name          = $cart_items['title'];
			$items->product_quantity         = abs($cart_items['qty']);
			$items->product_item_price       = $cart_items['amt'];
			$items->product_attributes_price = $cart_items['opt_amt'];
			$items->original_price           = $cart_items['original_price'];
			$items->item_tax                 = !empty($taxdetail['taxAmount']) ? $taxdetail['taxAmount'] : 0;
			$items->item_tax_detail          = !empty($taxdetail) ? json_encode($taxdetail) : '';
			$items->item_shipcharges         = !empty($shipdetail['totalShipCost']) ? $shipdetail['totalShipCost'] : 0;

			// Discount detail
			$items->discount = 0;
			$items->discount_detail = json_encode(array());

			if (!empty($cart_items["discount"]))
			{
				$items->discount = $cart_items["discount"];

				if ($cart_items['discount_detail'])
				{
					$promoDetail            = $cart_items['discount_detail'];
					$items->coupon_code     = !empty($promoDetail['coupon_code']) ? $promoDetail['coupon_code'] : '';
					$items->discount_detail = !empty($promoDetail) ? json_encode($promoDetail) : json_encode(array());
				}
			}

			$items->item_shipDetail     = !empty($shipdetail) ? json_encode($shipdetail) : '';
			$items->product_final_price = $cart_items['tamt'] + $items->item_tax + $items->item_shipcharges - $items->discount;

			$items->params = $cart_items['params'];
			$items->cdate  = date("Y-m-d H:i:s");
			$items->mdate  = date("Y-m-d H:i:s");
			$items->status = 'P';

			try
			{
				$this->_db->insertObject('#__kart_order_item', $items,  'order_item_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return 0;
			}

			// Add entry in order_itemattributes
			$query = "Select * FROM #__kart_cartitemattributes WHERE cart_item_id=" . (int) $cart_items['id'];

			// Cart_item_id as id
			$this->_db->setQuery($query);
			$cartresult = $this->_db->loadAssocList();

			if (!empty($cartresult))
			{
				foreach ($cartresult as $key => $cart_itemopt)
				{
					$items_opt                            = new stdClass;
					$items_opt->order_item_id             = $items->order_item_id;
					$items_opt->itemattributeoption_id 	  = $cart_itemopt['itemattributeoption_id'];
					$items_opt->orderitemattribute_name	  = $cart_itemopt['cartitemattribute_name'];
					$items_opt->orderitemattribute_price  = $this->getAttrOptionPrice($cart_itemopt['itemattributeoption_id']);
					$items_opt->orderitemattribute_prefix = $cart_itemopt['cartitemattribute_prefix'];

					try
					{
						$this->_db->insertObject('#__kart_order_itemattributes', $items_opt, 'orderitemattribute_id');
					}
					catch (\RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return 0;
					}
				}
			}

			$socialintegration = $params->get('integrate_with', 'none');
			$streamBuyProd     = $params->get('streamBuyProd', 0);

			if ( $streamBuyProd && $socialintegration != 'none' )
			{
				// Adding msg in stream
				$user     = Factory::getUser();
				$action   = 'buyproduct';
				$prodLink = '<a class="" href="' . $comquick2cartHelper->getProductLink($cart_items['item_id'], 'detailsLink', 1) .
				'">' . $cart_items['title'] . '</a>';

				$store_info[$items->store_id] = $comquick2cartHelper->getSoreInfo($items->store_id);
				$s_lk                         = 'index.php?option=com_quick2cart&view=vendor&layout=store&store_id=';

				$storeLink = '<a class="" href="' . Uri::root() .
				substr(Route::_($s_lk . $items->store_id), strlen(Uri::base(true)) + 1) . '">' . $store_info[$items->store_id]['title'] . '</a>';

				$originalMsg = Text::sprintf('QTC_ACTIVITY_BUY_PROD', $prodLink, $storeLink);
				$title       = '{actor} ' . $originalMsg;

				// According to integration create social lib class obj.
				$libclass = $comquick2cartHelper->getQtcSocialLibObj();
				$libclass->pushActivity($user->id, $act_type = '', $act_subtype = '',  $originalMsg, $act_link = '', $title = '', $act_access = '');
			}

			// @VM if social activity FOR  js IS SELECTED THEN ONY SHOW
			if (0)
			{
				// Add to JS stream
				if (File::exists(JPATH_SITE . '/components/com_community/libraries/core.php'))
				{
					@$comquick2cartHelper->addJSstream($user->id, $user->id, $title, '', $action, 0);
					require_once JPATH_SITE . '/components/com_community/libraries/core.php';
					$userid = Factory::getUser()->id;

					if ($userid)
					{
						$u_lk = 'index.php?option=com_community&view=profile&userid=';
						$u_nm = Factory::getUser()->name;
						$userLink = '<a class="" href="' . Uri::root() . substr(CRoute::_($u_lk . $userid), strlen(Uri::base(true)) + 1) . '">' . $u_nm . '</a>';
					}
					else
					{
						$userLink = $bill['email1'];
					}

					// Get connected Users of logged in user
					$jsuser = CFactory::getUser($userid);
					$connections_aa = $jsuser->getFriendIds();

					if (!empty($connections_aa))
					{
						foreach ($connections_aa as $connections)
						{
							$notification_subject = Text::sprintf('QTC_NOTIFIY_BUY_PROD_FRN', $userLink, $prodLink);
							@$comquick2cartHelper->addJSnotify($userid, $connections, $notification_subject, 'notif_system_messaging', '0', '');
						}
					}

					$groupIDs = explode(",", $jsuser->_groups);

					if (empty($groupIDs))
					{
						$query = "SELECT groupid FROM #__community_groups_members " .
						"WHERE memberid=" . $userid;
						$this->_db->setQuery($query);
						$groupIDs = $this->_db->loadColumn();
					}

					if (!empty($groupIDs))
					{
						foreach ($groupIDs as $groupID)
						{
							if (!empty($groupID))
							{
								$query = "SELECT name FROM #__community_groups " .
								" WHERE id=" . $groupID;
								$this->_db->setQuery($query);
								$groupName = $this->_db->loadResult();
								$query = "SELECT memberid " .
									"FROM #__community_groups_members " .
									"WHERE groupid=" . $groupID . " AND approved=1 AND memberid<>" . $userid . "";
								$this->_db->setQuery($query);
								$group_ids = $this->_db->loadColumn();

								if (!empty($group_ids))
								{
									foreach ($group_ids as $group_id)
									{
										$notification_subject = Text::sprintf('QTC_NOTIFIY_BUY_PROD_GRP', $userLink, $groupName, $prodLink);
										$comquick2cartHelper->addJSnotify($userid, $group_id, $notification_subject, 'notif_system_messaging', '0', '');
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Gives country list.
	 *
	 * @since   2.2
	 * @return   countryList
	 */
	public function getCountry()
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
		$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');
		$rows        = (array) $tjGeoHelper->getCountryList('com_quick2cart');

		return $rows;
	}

	/**
	 * function to get user state
	 *
	 * @param   integer  $country_id  country id
	 *
	 * @since   2.2
	 * @return   user state
	 */
	public function getuserState($country_id)
	{
		if (!empty($country_id))
		{
			require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
			$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');
			$rows        = (array) $tjGeoHelper->getRegionList($country_id, 'com_quick2cart');

			return $rows;
		}
	}

	/**
	 * Take itemattributeoption_id and fetch price according to currency
	 *
	 * @param   integer  $attop_id  attribute optio id
	 *
	 * @since   2.2
	 * @return   returns the applicatble tax charges
	 */
	public function getAttrOptionPrice($attop_id)
	{
		if ($attop_id)
		{
			$db                  = Factory::getDbo();
			$comquick2cartHelper = new comquick2cartHelper;
			$currency            = $comquick2cartHelper->getCurrencySession();
			$query = "SELECT price FROM `#__kart_option_currency` WHERE itemattributeoption_id= " . (int) $attop_id . " AND currency='$currency'";
			$db->setQuery($query);
			$result  = $db->loadResult();

			return $result;
		}
	}

	/**
	 * Gives applicable tax charges.
	 *
	 * @param   integer  $dis_totalamt  cart subtotal (after discounted amount )
	 * @param   object   $vars          object with cartdetail,billing address and shipping adress details.
	 *
	 * @since   2.2
	 * @return   returns the applicatble tax charges
	 */
	public function afterTaxPrice($dis_totalamt, $vars)
	{
		$app          = Factory::getApplication();
		$jinput       = $app->input;
		$params       = ComponentHelper::getParams('com_quick2cart');
		$shippingMode = $params->get('shippingMode', 'itemLevel', 'String');

		if ($shippingMode == "orderLeval")
		{
			PluginHelper::importPlugin('qtctax');
			$taxresults = $app->triggerEvent('onAddTax', array($dis_totalamt, $vars));

			if (!empty($taxresults[0]['charges']) && is_numeric($taxresults[0]['charges']))
			{
				$firstResult = $taxresults[0];
				$charges     = (float) $firstResult['charges'];
				$detail      = json_encode($firstResult);

				$taxChargesDetails                    = new stdclass;
				$taxChargesDetails->charges           = $charges;
				$taxChargesDetails->order_tax_details = $detail;

				return $taxChargesDetails;
			}
		}
		else
		{
			// GET BILLING AND SHIPPING ADDRESS
			$address = new stdClass;

			if (!empty($vars->billing_address))
			{
				$address->billing_address = $vars->billing_address;
			}

			if (!empty($vars->shipping_address))
			{
				$address->shipping_address = $vars->shipping_address;
			}

			$address->ship_chk = $vars->ship_chk;
			$totalTax          = 0;

			$taxobject                 = new stdClass;
			$taxobject->totalAmount    = $dis_totalamt;
			$taxobject->cartdetails    = $vars->cartItemDetail;
			$taxobject->addressDetails = $address;

			$taxHelper = new taxHelper;
			$taxresults = array();

			PluginHelper::importPlugin('tjtaxation');
			$taxresults = $app->triggerEvent('onTjCalculateTax', array($taxobject));

			$itemWiseTaxDetail = array();

			if (!empty($taxresults))
			{
				$itemWiseTaxDetail = $taxresults[0];
			}

			if (!empty($itemWiseTaxDetail))
			{
				// To set tax detail against order item row entry
				// $post->set('itemsTaxDetail', $itemWiseTaxDetail);

				// Get total of all item taxes
				foreach ($itemWiseTaxDetail as $prodTax)
				{
					if (!empty($prodTax))
					{
						foreach ($prodTax['taxdetails'] as $prodVariantTax)
						{
							if (!empty($prodVariantTax['amount']))
							{
								$totalTax += $prodVariantTax['amount'];
							}

							if (!empty($prodVariantTax['taxAmount']))
							{
								$totalTax += $prodVariantTax['taxAmount'];
							}
						}
					}
				}

				$taxChargesDetails                 = new stdclass;
				$taxChargesDetails->charges        = $totalTax;
				$taxChargesDetails->orderTax       = $totalTax;
				$taxChargesDetails->itemsTaxDetail = $itemWiseTaxDetail;

				return $taxChargesDetails;
			}
		}
	}

	/**
	 * This function updates coupon item
	 *
	 * @param   object           $coupon  coupon code
	 * @param   iterable|object  $cart_item  cart item
	 * @param   STRING           $called     called
	 * @param   INT              $order_id   order id
	 *
	 * @return price after discount
	 */
	public function updateCop_item($coupon, $cart_item, $called="cart", $order_id=0)
	{
		$db = Factory::getDbo();

		// $cart_item = coupon items;
		foreach ($cart_item as $cart_item_id)
		{
			if ($called == "cart")
			{
				// Called from cart
				$Quick2cartModelcart = new Quick2cartModelcart;
				$cartid = $Quick2cartModelcart->getCartId();
				$query = "Select cart_item_id as id ,item_id as item_id, product_final_price as tamt ,
						  product_quantity as qty
			 			  FROM #__kart_cartitems
			 			  WHERE item_id='$cart_item_id' AND cart_id =" . $cartid . " order by `store_id`";
			}
			else
			{
				// Called from  order
				$query = "Select order_item_id as id , item_id as item_id, product_final_price as tamt ,
				          product_quantity as qty
			              FROM #__kart_order_item
			              WHERE item_id='$cart_item_id' AND order_id =" . $order_id . " order by `store_id`";
			}

			$db->setQuery($query);
			$cartitems = $db->loadAssocList();

			foreach ($cartitems as $item)
			{
				if ($item['item_id'] == $cart_item_id)
				{
					if ($coupon[0]->val_type == 1)
					{
						$cval = ($coupon[0]->value / 100) * $item['tamt'];
					}
					else
					{
						$cval = $coupon[0]->value;

						/* Multiply cop disc with qty*/
						$cval = $cval * $item['qty'];
					}

					$camt = $item['tamt'] - $cval;

					if ($camt <= 0)
					{
						$camt = 0;
					}

					$dis_totalamt = ($camt)?$camt:$item['tamt'];

					$cart_item                      = new stdClass;
					$cart_item->item_id             = $cart_item_id;
					$cart_item->original_price      = $item['tamt'];
					$cart_item->product_final_price = $dis_totalamt;

					if ($called != "cart")
					{
						$cart_item->discount = ($cval) ? $cval : '0';
					}

					$comquick2cartHelper = new comquick2cartHelper;

					if ($called == "cart")
					{
						$q = "SELECT  `params` FROM  `#__kart_cartitems` WHERE `cart_item_id` =" . $item['id'];
						$cart_item->params = $comquick2cartHelper->appendExtraFieldData($coupon[0]->cop_code, $q, 'coupon_code');
						$cart_item->cart_item_id = $item['id'];
						$sql = $db->updateObject("#__kart_cartitems", $cart_item, "cart_item_id");
					}
					else
					{
						$q = "SELECT  `params` FROM  `#__kart_order_item` WHERE `order_item_id` =" . $item['id'];
						$cart_item->params = $comquick2cartHelper->appendExtraFieldData($coupon[0]->cop_code, $q, 'coupon_code');
						$cart_item->order_item_id = $item['id'];
						$sql = $db->updateObject("#__kart_order_item", $cart_item, "order_item_id");
					}

					try
					{
						$sql;
					}
					catch (\RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return -1;
					}
				}
			}
		}
	}

	/**
	 * This function returns price after applying discounts
	 *
	 * @param   null|bool|int|float|string   $totalamt  total amount
	 * @param   STRING  $c_code    coupon code
	 * @param   INT     $user_id   userid
	 * @param   STRING  $called    called
	 * @param   INT     $order_id  order id
	 *
	 * @return price after discount
	 */
	public function afterDiscountPrice($totalamt, $c_code, $user_id = "", $called = "cart", $order_id = 0)
	{
		$coupon       = $this->getcoupon($c_code, $user_id, $called, $order_id);
		$coupon       = $coupon ? $coupon : array();
		$dis_totalamt = $totalamt;

		// If user entered code is matched with dDb coupon code
		if (isset($coupon) && $coupon)
		{
			if (!empty($coupon[0]->item_id))
			{
				// Item specific coupon
				$coupon[0]->cop_code = $c_code;
				$this->updateCop_item($coupon, $coupon[0]->item_id, $called, $order_id);
			}
			else
			{
				/*
				 * [0] => stdClass Object
					(
						[value] => 10.00
						[val_type] => 1  // 1 for percentag,  0 for flat coupon
						[store_id] => 0
						[item_use_per_user] => ,0,0
					)
				 */

				/*$returnData = array();
				$returnData['coupon_code'] = $c_code;
				$returnData['value'] = $coupon[0]->val_type;
				*/

				if ($coupon[0]->val_type == 1)
				{
					// Percentage
					$cval = ($coupon[0]->value / 100) * $totalamt;
				}
				else
				{
					// Flat coupon
					$cval = $coupon[0]->value;

					// $returnData['coupon_type'] = 0;
				}

				$camt = $totalamt - $cval;

				if ($camt <= 0)
				{
					$camt = 0;
				}

				$dis_totalamt = ($camt >= 0) ? $camt : $totalamt;

				// $returnData['discount_amount'] = $totalamt - $dis_totalamt;
			}
		}

		// @TODO @ANKUSH - while doing coupon change, $returnData['discount_amount'] return in specific format

		return $dis_totalamt;
	}

	/**
	 * Gives applicable Shipping charges.
	 *
	 * @param   integer  $subtotal  cart subtotal (after discounted amount )
	 * @param   object   $vars      object with cartdetail,billing address and shipping adress details.
	 *
	 * @since   2.2
	 * @return   returns the applicatble shipping charges
	 */
	public function afterShipPrice($subtotal, $vars)
	{
		$app          = Factory::getApplication();
		$jinput       = $app->input;
		$post         = $jinput->post;
		$params       = ComponentHelper::getParams('com_quick2cart');
		$shippingMode = $params->get('shippingMode', 'itemLevel', 'String');

		if ($shippingMode == "orderLeval")
		{
			// Call the plugin and get the result
			// @TODO:need to check plugim type..
			PluginHelper::importPlugin('qtcshipping');
			$result      = $app->triggerEvent('onQ2cShipping', array($subtotal, $vars));
			$shipresults = array();

			if (!empty($result[0]))
			{
				$shipresults = $result[0];
			}

			if (isset($shipresults['allowToPlaceOrder']) && $shipresults['allowToPlaceOrder'] == 1)
			{
				$detail                                = json_encode($shipresults);
				$shipresults['order_shipping_details'] = $detail;

				return $shipresults;
			}
			else
			{
				return $shipresults;
			}
		}
		else
		{
			$shipChargesDetail                       = array();
			$shipChargesDetail['totCharges']         = 0;
			$shipChargesDetail['itemShipMethDetail'] = array();
			$qtcshiphelper                           = new qtcshiphelper;
			$allowToPlaceOrder                       = array();
			$ship_charge_Details_for_product         = array();

			foreach ($vars->cartItemDetail as $key => $itemDetail)
			{
				$item_id              = $itemDetail['item_id'];
				$shippingChargeDetail = array();

				if (property_exists($vars, 'selectedItemshipMeth'))
				{
					// If item has shipping methods
					foreach ($vars->selectedItemshipMeth as $j => $itemShippingMethod)
					{
						if (array_key_exists($item_id, $itemShippingMethod) && !empty($vars->selectedItemshipMeth[$j][$item_id]))
						{
							$shipMethIdForItem  = $vars->selectedItemshipMeth[$j][$item_id];
							$itemShipMethDetail = $vars->itemsShipMethRateDetail[$shipMethIdForItem];

							$shipMethod             = array();
							$shipMethod['client']   = $itemShipMethDetail['client'];
							$shipMethod['methodId'] = $itemShipMethDetail['methodId'];

							$address                   = new stdClass;
							$address->billing_address  = $vars->billing_address;
							$address->shipping_address = $vars->shipping_address;
							$address->ship_chk         = $vars->ship_chk;

							// (Recalculate) Get selected shipping method detail.
							$shippingChargeDetail = $qtcshiphelper->getItemsShipMethods($item_id, $address, $itemDetail, $shipMethod);

							if (!empty($shippingChargeDetail['totalShipCost']))
							{
								$vars->cartItemDetail[$item_id]['itemShipCharges'] = $shippingChargeDetail['totalShipCost'];
							}
							else
							{
								$vars->cartItemDetail[$item_id]['itemShipCharges'] = $itemShipMethDetail['totalShipCost'];
							}

							$shipChargesDetail['totCharges'] += $vars->cartItemDetail[$item_id]['itemShipCharges'];
						}
					}
				}

				if (!empty($shippingChargeDetail))
				{
					$ship_charge_Details_for_product[] = $shippingChargeDetail;
				}
			}

			$qtcOrderShipcharges = 0;

			if (!empty($shipChargesDetail))
			{
				// To add against item row entry

				// $post->set('itemShipMethDetail', $shipChargesDetail['itemShipMethDetail']);

				if ($shipChargesDetail['totCharges'])
				{
					$qtcOrderShipcharges = $shipChargesDetail['totCharges'];

					// $shipval = $comquick2cartHelper->calamt($shipval, $shipChargesDetail['totCharges']);
				}
			}

			// @TODO: change allowToPlaceOrder according to condition. Each plugin must return this parameter
			$shippingData                       = array();
			$shippingData['allowToPlaceOrder']  = 1;
			$shippingData['charges']            = $qtcOrderShipcharges;
			$shippingData['totalShipCost']      = $qtcOrderShipcharges;
			$shippingData['itemShipMethDetail'] = $ship_charge_Details_for_product;

			return $shippingData;
		}
	}

	/**
	 * function to add tax
	 *
	 * @param   INT  $dis_totalamt  dis_totalamt
	 * @param   INT  $tax           tax
	 *
	 * @return recalculated data
	 */
	public function addtax($dis_totalamt, $tax)
	{
		return $dis_totalamt + $tax;
	}

	/**
	 * function to get shipping price
	 *
	 * @param   STRING  $keyname   keyname
	 * @param   STRING  $keyvalue  keyvalue
	 *
	 * @return shipping price
	 */
	public function getShippingPrice($keyname, $keyvalue)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$shipid              = $comquick2cartHelper->getShippingManagerId($keyname, $keyvalue);

		if (!empty($shipid))
		{
			$shipval = $comquick2cartHelper->getShipCurrencyPrice($shipid);

			return $shipval;
		}

		return 0;
	}

	/**
	 * FUnctio to ccheck bill mail if exists
	 *
	 * @param   INT  $mail  mail
	 *
	 * @return status
	 */
	public function checkbillMailExists($mail)
	{
		$db        = Factory::getDbo();
		$mailexist = 0;

		$query     = "SELECT id FROM #__users where email  LIKE '" . $mail . "'";
		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result)
		{
			$mailexist = 1;
		}

		return $mailexist;
	}

	/**
	 * This function gives plugin name from plugin parameter
	 *
	 * @param   INT  $plgname  plugin name
	 *
	 * @return array
	 */
	public function getPluginName($plgname)
	{
		$plugin  = PluginHelper::getPlugin('payment',  $plgname);
		@$params = json_decode($plugin->params);

		return @$params->plugin_name;
	}

	/**
	 * Get details of checkout cart items
	 *
	 * @return array
	 */
	public function getCheckoutCartItemsDetails()
	{
		// GETTING CART ITEMS
		JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
		$cartmodel = new Quick2cartModelcart;
		$cart      = $cartmodel->getCartitems();

		if (!empty($cart))
		{
			foreach ( $cart as $key => $rec )
			{
				JLoader::import('product', JPATH_SITE . '/components/com_quick2cart/models');
				$quick2cartModelProduct    = new quick2cartModelProduct;
				$cart[$key]['item_images'] = $quick2cartModelProduct->getProdutImages($rec['item_id']);

				$productHelper = new productHelper;

				// Get cart items attribute details
				$cart[$key]['prodAttributeDetails'] = $productHelper->getItemCompleteAttrDetail($rec['item_id']);

				$product_attributes = rtrim($cart[$key]['product_attributes'], ",");

				if (!empty($product_attributes))
				{
					// Get Cart Item attribute seleted value
					if ($cart[$key]['product_attributes'])
					{
						$db    = Factory::getDbo();
						$query = $db->getQuery(true);
						$query->select("`cartitemattribute_id`, `itemattributeoption_id`, `cartitemattribute_name`");
						$query->from('#__kart_cartitemattributes');
						$query->where("itemattributeoption_id IN(" . $product_attributes . ")");
						$query->where(" cart_item_id = " . $cart[$key]['id']);
						$db->setQuery($query);
						$cart[$key]['product_attributes_values'] = $db->loadObjectList('itemattributeoption_id');
					}
				}
			}
		}

		return $cart;
	}

	/**
	 * Find applicable promotion discount. Substract the applicable discount from item's total amount.
	 *
	 * @param   Array  &$cart        cart detail
	 * @param   Array  $coupon_code  coupon code
	 *
	 * @return coupon
	 */
	public function afterPromotionDiscount(&$cart, $coupon_code = '')
	{
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/promotion.php';

		if (!class_exists('PromotionHelper'))
		{
			JLoader::register('PromotionHelper', $path);
			JLoader::load('PromotionHelper');
		}

		$PromotionHelper        = new PromotionHelper;
		$getAllApplicablePromos = $PromotionHelper->getPromotionDiscount($cart, $coupon_code);

		if (empty($getAllApplicablePromos))
		{
			return array();
		}

		$maximumDiscount = 0;
		$maxDisPromo     = new stdClass;

		// Get Maximum discount promotion
		if (!empty($getAllApplicablePromos))
		{
			foreach ($getAllApplicablePromos as $key => $promo)
			{
				// If coupon is not applied then apply cart promotion with max value else apply coupon promo
				if ($coupon_code == '')
				{
					if ($maximumDiscount < $promo->applicableMaxDiscount)
					{
						$maximumDiscount = $promo->applicableMaxDiscount;
						$maxDisPromo     = $promo;
					}
				}
				else
				{
					if ($coupon_code == $promo->coupon_code)
					{
						$maxDisPromo = $promo;
						$maximumDiscount = $promo->applicableMaxDiscount;
					}
				}
			}
		}

		// Now distribute amount in shares
		$totalPrice             = 0;
		$cartitemPriceArray     = array();
		$cartitemIdDiscoutArray = array();

		if (!empty($maxDisPromo->applicableItemDetail))
		{
			$promoDetail                = array();
			$promoDetail['id']          = $maxDisPromo->id;
			$promoDetail['name']        = $maxDisPromo->name;
			$promoDetail['coupon_code'] = ($maxDisPromo->coupon_required == 1 ) ? $maxDisPromo->coupon_code : '';

			/* Get applicable item's total price and item->price array {$cartItemId is unique identifier to identify the item row.}
			- Not need to
			*/
			foreach ($maxDisPromo->applicableItemDetail as $key => $citem)
			{
				$totalPrice = $totalPrice + $citem['tamt'];
				$cartitemPriceArray[$key] = $citem['tamt'];
			}

			// Sort by asc item price  and distribute share
			arsort($cartitemPriceArray);

			$HighestPriceCartItemId = -1;
			$itemDiscounts          = 0;

			// Find discount share except highest item priceone
			foreach ($cartitemPriceArray as $key => $pamout)
			{
				if ($HighestPriceCartItemId == -1)
				{
					// For highest item price item, we will add the share later
					$HighestPriceCartItemId = $key;
					continue;
				}

				if (empty($pamout))
				{
					$cartitemIdDiscoutArray[$key] = 0;
				}
				else
				{
					$cartitemIdDiscoutArray[$key] = round(($pamout / $totalPrice) * $maximumDiscount);
					$itemDiscounts                = $itemDiscounts + $cartitemIdDiscoutArray[$key];
				}
			}

			$cartitemIdDiscoutArray[$HighestPriceCartItemId] = $maximumDiscount - $itemDiscounts;
		}

		// Update the discount in cart variable
		if (!empty($cartitemIdDiscoutArray))
		{
			foreach ($cart as $key => $citem)
			{
				$cart_id = $citem['id'];

				if (!empty($cartitemIdDiscoutArray[$cart_id]))
				{
					$cart[$key]['tamt']            = $citem['tamt'] - $cartitemIdDiscoutArray[$cart_id];
					$cart[$key]['discount']        = $cartitemIdDiscoutArray[$cart_id];
					$cart[$key]['discount_detail'] = $promoDetail;
				}
			}
		}
	}

	/**
	 * Get item attribute id
	 *
	 * @param   Array  $attributeOptionId  attribute option id
	 *
	 * @return attribute id
	 */
	public function getAttributeId($attributeOptionId)
	{
		if (!empty($attributeOptionId))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName("itemattribute_id"));
			$query->from($db->quoteName('#__kart_itemattributeoptions'));
			$query->where($db->quoteName('itemattributeoption_id') . " = " . $attributeOptionId);
			$db->setQuery($query);
			$attributeId = $db->loadResult();
		}

		return $attributeId;
	}

	/**
	 * This function return order delivery from date and to date in UTC format for storing in db
	 *
	 * @param   Array  $orderDeliveryDetails  Array which contain delivery Date and time slot
	 *
	 * @return  Array  $res
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getitemDeliveryDateDetails($orderDeliveryDetails)
	{
		$res                = array();
		$params             = ComponentHelper::getParams('com_quick2cart');
		$deliveryTimeFormat = $params->get('delivery_time_format', 12, 'Integer');
		$minDeliveryTime    = $params->get('min_delivery_time', 30, 'Integer');
		$deliverytimeslots  = $params->get('deliverytimeslots');

		$deliveryDate = $orderDeliveryDetails['deliveryDate'];
		$deliveryTime = $orderDeliveryDetails['deliveryTime'];

		// Get date in required format Y-m-d
		$date = Factory::getDate($deliveryDate)->format('Y-m-d');

		/* Explode delivery time slot to take from and to time
		For e.g 04 PM - 06 PM */
		$getTime = explode('-', $deliveryTime);

		$fromDate = $date . ' ' . $getTime[0] . ':00:00';
		$todate   = $date . ' ' . $getTime[1] . ':00:00';

		// If selected delivery time slot is in 12 hours format, then convert it in 24 hour format
		if ($deliveryTimeFormat == 12)
		{
			$fromDate = $date . date(" H:i:s", strtotime($getTime[0]));
			$todate   = $date . date(" H:i:s", strtotime($getTime[1]));
		}

		if (!empty(Factory::getUser()->id))
		{
			$offset = Factory::getUser()->getParam('timezone', Factory::getConfig()->get('offset'));
		}
		else
		{
			$offset = Factory::getConfig()->get('offset');
		}

		// Convert date and time in UTC for storing in DB
		$res['deliveryfromdate'] = Factory::getDate($fromDate, $offset)->toSQL();
		$res['deliverytodate']   = Factory::getDate($todate, $offset)->toSQL();

		return $res;
	}

	/**
	 * Generates an OTP, saves it to the database, and sends it to the user's email.
	 *
	 * @param   int  $userId  The ID of the user for whom to generate the OTP.
	 *
	 * @return  array  Result of OTP generation with success status, OTP, and email (or error message).
	 */
	public function generateOtpForUser($userId)
	{
		$db = Factory::getDbo();
		$now = Factory::getDate();
		$expiresAt = clone $now;
		$expiresAt->modify('+10 minutes');
		$otp = rand(100000, 999999);

		// Fetch user email from _kart_customer_address
		$query = $db->getQuery(true)
			->select($db->quoteName('user_email'))
			->from($db->quoteName('#__kart_customer_address'))
			->where($db->quoteName('user_id') . ' = ' . (int)$userId);
		$db->setQuery($query);
		$userEmail = $db->loadResult();

		// Delete expired OTPs
		$deleteQuery = $db->getQuery(true)
			->delete($db->quoteName('#__order_otps'))
			->where($db->quoteName('expires_at') . ' < ' . $db->quote($now->toSql()));
		$db->setQuery($deleteQuery);
		$db->execute();

		$columns = ['user_id', 'otp', 'created_on', 'expires_at'];
		$values = [
			(int)$userId,
			$db->quote($otp),
			$db->quote($now->toSql()),
			$db->quote($expiresAt->toSql())
		];
		$query = $db->getQuery(true)
			->insert($db->quoteName('#__order_otps'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));
		$db->setQuery($query);

		try
		{
			$db->execute();
			JLoader::register('Quick2CartMailsHelper', JPATH_SITE . '/components/com_quick2cart/helpers/mails.php');
			$quick2CartMailsHelper = new Quick2CartMailsHelper;
			$quick2CartMailsHelper->sendOtpToUser($userEmail, $otp);

			return ['success' => true, 'otp' => $otp, 'email' => $userEmail];
		}
		catch (Exception $e)
		{
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}

	/**
	 * Verifies the OTP entered by the user.
	 *
	 * @param   int    $userId     The ID of the user to verify OTP for.
	 * @param   string $enteredOtp The OTP entered by the user.
	 *
	 * @return  array  Result of verification with success status and message.
	 */
	public function verifyOtpForUser($userId, $enteredOtp)
	{
		$db = Factory::getDbo();
		$now = Factory::getDate()->toSql();

		// Find OTP for this user, not expired
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__order_otps'))
			->where($db->quoteName('user_id') . ' = ' . (int)$userId)
			->where($db->quoteName('otp') . ' = ' . $db->quote($enteredOtp))
			->where($db->quoteName('expires_at') . ' > ' . $db->quote($now));
		$db->setQuery($query);
		$otpRow = $db->loadAssoc();

		if ($otpRow)
		{
			$deleteQuery = $db->getQuery(true)
				->delete($db->quoteName('#__order_otps'))
				->where($db->quoteName('user_id') . ' = ' . (int)$userId);
			$db->setQuery($deleteQuery);
			$db->execute();

			return ['success' => true, 'message' => 'OTP Verified Successfully..!'];
		}
		else
		{
			return ['success' => false, 'message' => 'Please enter valid OTP..!'];
		}
	}
}
