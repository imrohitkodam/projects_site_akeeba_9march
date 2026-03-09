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
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Cartcheckout controller class.
 *
 * @since  1.0.0
 */
class Quick2cartControllercartcheckout extends Quick2cartController
{
	/**
	 * Function used to set cookie
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function setCookieCur()
	{
		$jinput     = Factory::getApplication()->input;
		$data       = $jinput->post;
		$calledFrom = $jinput->get("view", "", "STRING");
		$multi_curr = $data->get('multi_curr', '', 'RAW');
		$expire     = time() + 60 * 60 * 24 * 7;
		setcookie("qtc_currency", $multi_curr, $expire, "/");
		$qtc_current_url = $data->get('qtc_current_url', '', 'RAW');

		if (!empty($qtc_current_url))
		{
			$link = $qtc_current_url;
		}
		else
		{
			// Get Item ID
			$comquick2cartHelper = new comquick2cartHelper;
			$iId                 = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=' . $calledFrom);

			if ($calledFrom == "cart")
			{
				$link = Uri::root()
				. substr(Route::_('index.php?option=com_quick2cart&view=cart&tmpl=component&Itemid=' . $iId, false), strlen(Uri::base(true)) + 1);
			}
			else
			{
				$link = Uri::root() . substr(Route::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $iId, false), strlen(Uri::base(true)) + 1);
			}
		}

		$this->setRedirect($link);
	}

	/**
	 * Function used to clear coupon
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function clearcop()
	{
		$jinput  = Factory::getApplication()->input;
		$session = Factory::getSession();
		$cops    = $session->get('coupon');
		$session->clear('coupon');
	}

	/**
	 * Function used to get coupon
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function isExistPromoCode()
	{
		$jinput               = Factory::getApplication()->input;
		$session              = Factory::getSession();
		$coupon_code          = $jinput->get('coupon_code', '', 'STRING');
		$model                = $this->getModel('cartcheckout');
		$applicablePromotions = $model->getPromoCoupon($coupon_code);

		if (!empty($applicablePromotions))
		{
			$couponList[0] = array("code" => $coupon_code);
			$session->set('coupon', $couponList);
			$session->set('one_pg_ckout_tab_state', 'qtc_cart');

			echo json_encode($couponList);
		}
		else
		{
			echo 0;
		}

		jexit();
	}

	/**
	 * Function used to get coupon
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function getcoupon()
	{
		$jinput  = Factory::getApplication()->input;
		$session = Factory::getSession();
		$c_code  = $jinput->get('coupon_code', '', 'STRING');
		$count   = '';
		$model   = $this->getModel('cartcheckout');
		$count   = $model->getcoupon($c_code);

		if (!empty($count))
		{
			$copItems = isset($count[0]->item_id) ? $count[0]->item_id : '';
			$c[]      = array(
				"code" => $c_code,
				"value" => $count[0]->value,
				"val_type" => $count[0]->val_type,
				"item_id" => $copItems
			);

			$cop                 = $session->get('coupon');
			$Quick2cartModelcart = new Quick2cartModelcart;
			$cart_itemIds        = $Quick2cartModelcart->getCartItemIds();

			if (!empty($cop))
			{
				foreach ($cop as $key => $copn)
				{
					// Avoid duplicate coupon
					if ($copn['code'] == $c[0]['code'])
					{
						// $cop_flag= 1;
						// Unset first coupon and keep it as latest (change order of coupon code)
						unset($cop[$key]);
						break;
					}

					// Apply only latest coupon on single item
					if (!empty($copn['item_id']))
					{
						$copitemsCart = array_intersect($copn['item_id'], $cart_itemIds);

						foreach ($copitemsCart as $item)
						{
							if (in_array($item, $c[0]['item_id']))
							{
								// If($copn['value'] < $c[0]['value'] )
								unset($cop[$key]);
							}
						}
					}
				}
			}

			// If($cop_flag== 0)
			$cop[0] = $c[0];
			$session->set('coupon', $cop);

			// For setting current tab status one page chkout::
			$session = Factory::getSession();
			$session->set('one_pg_ckout_tab_state', 'qtc_cart');

			echo json_encode($c);
		}
		else
		{
			echo 0;
		}

		jexit();
	}

	/**
	 * Function used to save
	 *
	 * @return  Array
	 *
	 * @since  1.0.0
	 */
	public function save()
	{
		$model       = $this->getModel('cartcheckout');
		$jinput      = Factory::getApplication()->input;
		$post        = $jinput->post->getArray();
		$orderDetail = array();
		$session     = Factory::getSession();

		if ($post['order_id'] == $session->get('order_id'))
		{
			// Update payment gateway for the order
			$orderDetail['order_id'] = $post['order_id'];
		}
		else
		{
			$orderDetail = $model->store();
		}

		$orderid             = $orderDetail['order_id'];
		$comquick2cartHelper = new comquick2cartHelper;
		$itemid              = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cartcheckout');

		if ($orderid > 0)
		{
			// Vm: SESSION ORDERID USED in payment process
			$this->setOrderINsession($orderid);

			// Vm: REMOVING SESSIION VARIABLE WHICH IS USED FOR 1-PAGE-CKOUT TAB
			$session = Factory::getSession();
			$session->set('one_pg_ckout_tab_state', '');
			$session->set('one_pg_ckoutMethod', '');

			$orderDetail['msg']      = Text::_('CONFIG_SAV');
			$orderDetail['success']  = 1;
			$orderDetail['order_id'] = $orderid;

			JLoader::import('components.com_quick2cart.models.cart', JPATH_SITE . '/components/com_quick2cart/models');
			$quick2cartModelcart = new Quick2cartModelcart;
			$quick2cartModelcart->empty_cart();
		}
		else
		{
			if (isset($orderDetail['success_msg']) && $orderDetail['success_msg'])
			{
				$orderDetail['msg'] = $orderDetail['success_msg'];
			}
			else
			{
				$orderDetail['msg'] = Text::_('COM_QUICK2CRT_ERROR_WHILE_PLACING_ORDER');
			}

			$orderDetail['success_msg']  = $orderDetail['message'];
			$orderDetail['success']      = 0;

			$orderDetail['redirect_uri'] = 'index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemid;
		}

		echo json_encode($orderDetail);
		jexit();
	}

	/**
	 * Function used to redirect on cancel
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function cancel()
	{
		$msg    = Text::_('Operation Cancelled');
		$this->setRedirect('index.php', $msg);
	}

	/**
	 * Function used to load states
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function loadState()
	{
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$country   = $jinput->get('country', '', 'STRING');
		$model     = $this->getModel('cartcheckout');
		$stateList = $model->getuserState($country);
		echo json_encode($stateList);
		jexit();
	}

	/**
	 *Function used to set currency session
	 *
	 * @param   STRING  $cur  Currency
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function setCurrencySession($cur = null)
	{
		$jinput = Factory::getApplication()->input;

		if ($cur == null || !$cur)
		{
			$curr = $jinput->get('currency');
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$oldcurrency         = $comquick2cartHelper->getCurrencySession();
		$model               = $this->getModel('cart');
		$return              = $model->syncCartCurrency($oldcurrency, $curr);
		echo $return;
		jexit();
	}

	/**
	 * Function used to calculate final price
	 *
	 * @param   Obj  $ipdata  idata should in formats tdClass Object ( [totalprice] => 174.375 [country] => Bangladesh [region] => Dhaka [city] => punt )
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function calFinalShipPrice($ipdata = '')
	{
		$jinput = Factory::getApplication()->input;

		// If called from Ajax
		if (empty($ipdata))
		{
			$postdata = $jinput->post();
			$jsondata = $postdata->get('data', '', 'STRING');
			$data     = json_decode($jsondata);
		}
		else
		{
			$data = $ipdata;
		}

		// Call model
		$model         = $this->getModel('cartcheckout');
		$finalshipdata = $model->getFinalShipPrice($data);

		// If not called from Ajax
		if (!empty($ipdata))
		{
			return $finalshipdata;
		}

		$comquick2cartHelper       = new comquick2cartHelper;
		$finalshipdata['charges']  = $comquick2cartHelper->getFromattedPrice(number_format($finalshipdata['charges'], 2));
		$finalshipdata['totalamt'] = $comquick2cartHelper->getFromattedPrice(number_format($finalshipdata['totalamt'], 2));
		echo json_encode($finalshipdata);
		jexit();
	}

	/**
	 * Function used to check bill mail
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function chkbillmail()
	{
		$jinput = Factory::getApplication()->input;
		$email  = $jinput->get('email', '', 'STRING');
		$model  = $this->getModel('cartcheckout');
		$status = $model->checkbillMailExists($email);
		$e[]    = $status;

		if ($status == 1)
		{
			$e[] = Text::_('QTC_BILLMAIL_EXISTS');
		}

		echo json_encode($e);
		jexit();
	}

	/**
	 *Function used to get Oder HTML
	 *
	 * @param   ARRAY  $inputArray  Order data
	 *
	 * @return  Array
	 *
	 * @since  1.0.0
	 */
	public function getOrderSummaryHTML($inputArray)
	{
		JLoader::import('cartcheckout', JPATH_SITE . '/components/com_quick2cart/models');
		$cartCheckoutModel = new Quick2cartModelcartcheckout;

		// Get cart details
		$cart = $cartCheckoutModel->getCheckoutCartitemsDetails();

		// Paymodel is uesed in included layout
		JLoader::import('components.com_quick2cart.models.payment', JPATH_SITE);
		$paymodel = new Quick2cartModelpayment;

		JLoader::import('components.com_quick2cart.models.cart', JPATH_SITE);
		$quick2cartModelcart = new Quick2cartModelcart;

		JLoader::import('components.com_quick2cart.helpers.product', JPATH_SITE);
		$productHelper = new productHelper;

		JLoader::import('components.com_quick2cart.models.cartcheckout', JPATH_SITE);
		$quick2cartModelCartCheckout = new Quick2cartModelCartCheckout;

		JLoader::import('components.com_quick2cart.helpers.promotion', JPATH_SITE);
		$promotionHelper = new promotionHelper;

		$cartItemsData = $quick2cartModelcart->getCartitems();
		$productsData  = array();

		foreach ($cartItemsData as $item)
		{
			$productDetail = $quick2cartModelcart->getItemRec($item['item_id']);
			$attributes    = $productHelper->getItemCompleteAttrDetail($item['item_id']);

			if (!empty($attributes))
			{
				$productDetail->itemAttributes = $attributes;
			}

			$productInStock                 = $productHelper->isInStockProduct($productDetail);
			$itemDetail                     = array();
			$itemDetail['store_id']         = $item['store_id'];
			$itemDetail['product_id']       = $item['item_id'];
			$itemDetail['product_quantity'] = abs($item['qty']);

			$cartDetail = Factory::getApplication()->input->get("cartDetail", '', "ARRAY");

			if (!empty($item['product_attributes']))
			{
				$attributes = explode(",", $item['product_attributes']);
				$attributes = array_filter($attributes);
				$att_option = array();

				foreach ($attributes as $attributeOption)
				{
					$attInputOption = array();
					$attributeId    = $quick2cartModelCartCheckout->getAttributeId($attributeOption);

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

							$att_option[$attributeId] = $attInputOption;
						}
						else
						{
							$attributeId              = $quick2cartModelCartCheckout->getAttributeId($attributeOption);
							$att_option[$attributeId] = $attributeOption;
						}
					}
				}

				$itemDetail['att_option'] = $att_option;
			}

			if($productInStock == 1)
			{
				$productsData[] = $itemDetail;
			}
		}

		JLoader::import('components.com_quick2cart.helpers.createorder', JPATH_SITE);
		$createOrderHelper = new CreateOrderHelper();
		$productsData      = $createOrderHelper->formatProductsData($productsData);
		$orderAmount       = $createOrderHelper->calculateOrderPrice($productsData);

		$quick2cartHelper = new comquick2cartHelper;
		$view             = $quick2cartHelper->getViewpath('cartcheckout', 'ordersummary_' . QUICK2CART_LOAD_BOOTSTRAP_VERSION);

		ob_start();
		include $view;
		$html = ob_get_contents();
		ob_end_clean();

		$data['success']   = 1;
		$data['orderHTML'] = $html;

		return $data;
	}

	/**
	 * Function used to set session
	 *
	 * @param   INT  $orderid  Order ID
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function setOrderINsession($orderid)
	{
		$session = Factory::getSession();
		$session->set('order_id', $orderid);
	}

	/**
	 * Function used to set check out method
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function setCheckoutMethod()
	{
		$jinput  = Factory::getApplication()->input;
		$regType = $jinput->get("regType", '', "RAW");

		// For setting current tab status one page chkout::
		$session = Factory::getSession();
		$session->set('one_pg_ckoutMethod', $regType);

		echo 1;
		jexit();
	}

	/**
	 * Function used to get Free irder HTML
	 *
	 * @param   INT  $order_id  Order ID
	 *
	 * @return  STRING
	 *
	 * @since  1.0.0
	 */
	public function getFreeOrderHtml($order_id)
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		// CHECK for view override
		$comquick2cartHelper = new comquick2cartHelper;
		$path                = $comquick2cartHelper->getViewpath('cartcheckout', 'freeorder', 'SITE', 'SITE');

		ob_start();
		include $path;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Function used to process free orders
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function processFreeOrder()
	{
		$jinput              = Factory::getApplication()->input;
		$comquick2cartHelper = new comquick2cartHelper;
		$db                  = Factory::getDBO();
		$user                = Factory::getUser();
		$post                = $jinput->post;
		$orderid             = $post->get('order_id', '', 'INT');
		$guest_email         = '';

		if (!empty($orderid))
		{
			$query = "SELECT `amount`,`email` FROM `#__kart_orders` where `id`=" . $orderid;
			$db->setQuery($query);
			$orderDetail = $db->loadAssoc();
			$orderPrice  = (int) $orderDetail['amount'];

			if (empty($orderPrice))
			{
				if (empty($user->id) && $orderDetail['email'])
				{
					$guest_email = "&email=" . md5($orderDetail['email']);
				}
				// CONFORM ONLY 0 PRICE ORDER
				$comquick2cartHelper->updatestatus($orderid, 'C', $comment = '', $send_mail = 1, $store_id = 0);
			}
		}

		$app = Factory::getApplication();

		$orderItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders');
		$orderLink   = 'index.php?option=com_quick2cart&view=orders&layout=order&orderid=' . $orderid . '&Itemid=' . $orderItemid . $guest_email;
		$link        = Uri::base() . substr(Route::_($orderLink, false), strlen(Uri::base(true)) + 1);

		$return       = new stdClass;
		$return->link = $link;

		echo json_encode($return);
		jexit();
	}

	/**
	 * This function save checkout data
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function qtc_autoSave()
	{
		$params                        = ComponentHelper::getParams('com_quick2cart');
		$isShippingEnabled             = $params->get('shipping', 0);
		$shippingMode                  = $params->get('shippingMode', 'itemLevel');
		$app                           = Factory::getApplication();
		$input                         = $app->input;
		$session                       = Factory::getSession();
		$post                          = $input->post;
		$model                         = $this->getModel('cartcheckout');
		$stepId                        = $input->get('stepId', '', 'STRING');
		$retdata                       = array();
		$retdata['stepId']             = $stepId;
		$retdata['payAndReviewHtml']   = '';
		$retdata['camp_id']            = '';
		$retdata['sa_sentApproveMail'] = '';
		$retdata['Itemid']             = '';
		$comquick2cartHelper           = new comquick2cartHelper;

		// Trigger: this trigger is called while changing the steps from checkout page
		PluginHelper::importPlugin("system");
		$app->triggerEvent("onAfterQ2cStepChange");

		$Quick2cartControllercartcheckout = new Quick2cartControllercartcheckout;
		$nextstep = '';

		switch ($stepId)
		{
			case "qtc_cartDetails":
				$nextstep = "fetchBillData";
			break;

			case "qtc_billing":

				$nextstep = "fetchPayNdReviewData";

				if ($isShippingEnabled == 1)
				{
					$nextstep = "fetchShipData";

					// If order level shippin mode then place order. (No ned to fetch ship detail)
					if ($shippingMode == "orderLeval")
					{
						$nextstep = "fetchPayNdReviewData";
					}
				}
			break;

			case "qtc_shippingStep":
				$nextstep = "fetchPayNdReviewData";
			break;
		}

		if ($nextstep == 'fetchBillData')
		{
			// Already fetched and rendered on form
		}

		// Clicked on billing
		if ($nextstep == 'fetchShipData')
		{
			$qtcshiphelper = new qtcshiphelper;
			$modelsPath    = JPATH_SITE . '/components/com_quick2cart/models/customer_addressform.php';
			$q2cCustomerAddressformModel = $comquick2cartHelper->loadqtcClass($modelsPath, "Quick2cartModelCustomer_AddressForm");
			$helperPath        = JPATH_SITE . '/components/com_quick2cart/helpers/createorder.php';
			$createOrderHelper = $comquick2cartHelper->loadqtcClass($helperPath, "CreateOrderHelper");

			$shippingDetails = new stdclass;
			$shipping        = $input->get('shipping_address', '', 'INT');
			$billing         = $input->get('billing_address', '', 'INT');

			if (!empty($shipping))
			{
				$shippingDetails->ship = $q2cCustomerAddressformModel->getAddress($shipping);
				$shippingDetails->ship = $createOrderHelper->mapUserAddress($shippingDetails->ship);
			}
			else
			{
				// For guest checkout
				$shippingDetails->ship = $post->get("ship", '', "ARRAY");
			}

			if (!empty($billing))
			{
				$shippingDetails->bill = $q2cCustomerAddressformModel->getAddress($billing);
				$shippingDetails->bill = $createOrderHelper->mapUserAddress($shippingDetails->bill);
			}
			else
			{
				// For guest checkout
				$shippingDetails->bill = $post->get("bill", '', "ARRAY");
			}

			$itemWiseShipDetail         = $qtcshiphelper->getCartItemsShiphDetail($shippingDetails);
			$shippingHtml               = $qtcshiphelper->getShipMethodHtml($itemWiseShipDetail);
			$retdata['shipMethoDetail'] = $shippingHtml;
		}

		// Save ad qtc_billing data
		if ($nextstep == 'fetchPayNdReviewData')
		{
			$isShippingEnabled = $params->get('shipping', 0);
			$shippingMode      = $params->get('shippingMode', 'itemLevel');

			if ($isShippingEnabled && $shippingMode == "orderLeval")
			{
				$q2cPinwiseShippingPlugin = PluginHelper::getPlugin('qtcshipping', 'qtc_pinwise_shipping');

				if (isset($q2cPinwiseShippingPlugin->id) && !empty($q2cPinwiseShippingPlugin->id))
				{
					// Get cart details
					JLoader::import('cartcheckout', JPATH_SITE . '/components/com_quick2cart/models');
					$cartCheckoutModel   = new Quick2cartModelcartcheckout;
					$cart                = $cartCheckoutModel->getCheckoutCartitemsDetails();
					$cartItemsStoreArray = array_column($cart, 'store_id');

					// Check if shipping is avaibale for the items in the cart
					PluginHelper::importPlugin('qtcshipping');
					$modelsPath                  = JPATH_SITE . '/components/com_quick2cart/models/customer_addressform.php';
					$q2cCustomerAddressformModel = $comquick2cartHelper->loadqtcClass($modelsPath, "Quick2cartModelCustomer_AddressForm");
					$shipping                    = $input->get('shipping_address', '', 'INT');

					if (!empty($shipping))
					{
						$shippingAddress = $q2cCustomerAddressformModel->getAddress($shipping);

						foreach ($cartItemsStoreArray as $cartItemStore)
						{
							if (!empty($cartItemStore) && !empty($shippingAddress->zipcode))
							{
								$validPinCodeWiseShippingcharges = $app->triggerEvent('onGetValidShippingCharges', array($cartItemStore, $shippingAddress->zipcode));
								$validPinCodeWiseShippingcharges = array_filter($validPinCodeWiseShippingcharges);

								if (count($validPinCodeWiseShippingcharges) == 0)
								{
									$retdata['shippingNotAvailable'] = Text::sprintf("PLG_QTCSHIPPING_QTC_PINWISE_PINCODE_IS_NOT_AVAILABEL", $shippingAddress->zipcode);
									$retdata['payAndReviewHtml']     = '';
									$retdata['order_id']             = 0;

									echo json_encode($retdata);
									jexit();
								}
							}
						}
					}
				}
			}

			$input      = $app->input;
			$inputArray = $input->getArray();
			$response   = $this->getOrderSummaryHTML($inputArray);

			if ($response['success'] == 0)
			{
				$retdata['shippingNotAvailable'] = $response['success_msg'];
			}
			else
			{
				$retdata['payAndReviewHtml'] = !empty($response['orderHTML']) ? $response['orderHTML'] : '';
				$retdata['order_id']         = !empty($response['order_id']) ? $response['order_id'] : 0;
			}
		}

		echo json_encode($retdata);
		jexit();
	}

	/**
	 * Function used to get gateway HTML
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function qtc_gatewayHtml()
	{
		$jinput          = Factory::getApplication()->input;
		$model           = $this->getModel('payment');
		$selectedGateway = $jinput->get('gateway', '');
		$order_id        = $jinput->get('order_id', '');
		$return          = '';

		if (!empty($selectedGateway) && !empty($order_id))
		{
			// Add selected payment gateway name against order
			$status = $model->updateOrderGateway($selectedGateway, $order_id);

			if ($status)
			{
				$payhtml = $model->getHTML($selectedGateway, $order_id);
				$return  = !empty($payhtml[0]) ? $payhtml[0] : '';
			}
		}

		echo $return;
		jexit();
	}

	/**
	 * Function used to get gateway HTML
	 *
	 * @param   STRING  $selectedGateway  selected gateway name
	 *
	 * @param   STRING  $order_id         order id
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function qtc_singleGatewayHtml($selectedGateway, $order_id)
	{
		$model  = $this->getModel('payment');
		$status = $model->updateOrderGateway($selectedGateway, $order_id);

		if ($status)
		{
			$payhtml = $model->getHTML($selectedGateway, $order_id);
			$return  = !empty ($payhtml[0]) ? $payhtml[0] : '';
		}

		return $return;
	}

	/**
	 * Function: updatecart updates the cart and also calculates the tax and shipping charges
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function update_cart_item()
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$input               = Factory::getApplication()->input;
		$post                = $input->post;
		$msg                 = array();
		$cart_item_id        = $post->get('cart_item_id', '', 'INT');
		$item_id             = $post->get('item_id', '', 'INT');

		// Get parsed form data
		parse_str($post->get('formData', '', 'STRING'), $formData);

		/* Load cart model
		$item = Array
					(
						[id] => 17
						[parent] => com_quick2cart
						[item_id] => 8 if item_id present then no need of id and parent fields
						[count] => 1
						[options] => 24,23,22,19
					)
					<pre>$userdata = Array
									(
										[23] => Array
											(
												[itemattributeoption_id] => 23
												[type] => Textbox
												[value] => qqq
											)

										[24] => Array
											(
												[itemattributeoption_id] => 24
												[type] => Textbox
												[value] => www
											)

									)
					* */

		$itemFromattedDet = array();
		$itemFromattedDet['item_id'] = $item_id;
		$userdata = array();

		if (!empty($formData['cartDetail']) && !empty($cart_item_id))
		{
			$newItemDetails = $formData['cartDetail'][$cart_item_id];
			$itemFromattedDet['count'] = abs($newItemDetails['cart_count']);
			$itemFromattedDet['options'] = '';

			if (empty($itemFromattedDet['count']))
			{
				$cartModel = $this->getModel('cart');
				$cartModel->remove_cartItem($cart_item_id);

				$msg['status']      = true;
				$msg['successCode'] = 1;

				echo json_encode($msg);
				jexit();
			}

			// If not empty attribute details
			if (!empty($newItemDetails['attrDetail']))
			{
				$attrDetail = $newItemDetails['attrDetail'];

				foreach ($attrDetail as $key => $attr)
				{
					if ($attr['type'] == 'Textbox' && !empty($attr['value']))
					{
						$userkey = $attr['itemattributeoption_id'];
						$userdata[$userkey] = $attr;
						$itemFromattedDet['options'] .= $attr['itemattributeoption_id'] . ',';
					}
					else
					{
						$itemFromattedDet['options'] .= $attr['value'] . ',';
					}
				}
			}
		}

		$path                = JPATH_SITE . "/components/com_quick2cart/models/cart.php";
		$comquick2cartHelper->loadqtcClass($path, 'Quick2cartModelcart');
		$Quick2cartModelcart = new Quick2cartModelcart;

		// Remove last comma
		if (!empty($itemFromattedDet['options']))
		{
			$tempArray                   = explode(',', $itemFromattedDet['options']);
			$tempArray                   = array_filter($tempArray, "strlen");
			$itemFromattedDet['options'] = implode(',', $tempArray);
		}

		// Get formated product details  (internal)
		$prod_details = $Quick2cartModelcart->getProd($itemFromattedDet);

		// If option present
		if (!empty($prod_details[1]))
		{
			// Add user field detail to options
			$AttrOptions     = $prod_details[1];
			$prod_details[1] = $comquick2cartHelper->AddUserFieldDetail($AttrOptions, $userdata);
		}

		// Update the cart
		$result = $Quick2cartModelcart->putCartitem('', $prod_details, $cart_item_id);

		// Validate Result. If added successfully.
		if (is_array($result) && $result["cart_item_id"] > 0)
		{
			$msg['status']      = true;
			$msg['successCode'] = 1;
		}
		else
		{
			$msg['status']      = false;
			$msg['successCode'] = 0;
			$msg['message']     = $result;
		}

		echo json_encode($msg);
		jexit();
	}

	/**
	 * Genrates the OTP for the user.
	 *
	 * @return void
	 */
	public function generateOtp()
	{
		$user  = Factory::getUser();
		$userId = $user->id ? $user->id : 0;

		$model = $this->getModel('cartcheckout');
		$result = $model->generateOtpForUser($userId);

		echo json_encode($result);
		Factory::getApplication()->close();
	}

	/**
	 * Verifies the OTP entered by the user.
	 *
	 * @return void
	 */
	public function verifyOtp()
	{
		$input = Factory::getApplication()->input;
		$user  = Factory::getUser();
		$userId = $user->id ? $user->id : 0;
		$enteredOtp = $input->getString('otp', '');

		$model = $this->getModel('cartcheckout');
		$result = $model->verifyOtpForUser($userId, $enteredOtp);

		echo json_encode($result);
		Factory::getApplication()->close();
	}
}
