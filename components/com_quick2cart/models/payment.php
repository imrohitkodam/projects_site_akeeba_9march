<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die(';)');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

/**
 * Methods Payment Model.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelpayment extends BaseDatabaseModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $pg_plugin  Plugin Name
	 * @param   string  $oid        Order ID.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function confirmpayment($pg_plugin, $oid)
	{
		$app  = Factory::getApplication();
		$post = Factory::getApplication()->input->get('post');
		$oid  = $this->extract_prefix($oid);
		$vars = $this->getPaymentVars($pg_plugin, $oid);

		if (!empty($post) && !empty($vars))
		{
			PluginHelper::importPlugin('payment', $pg_plugin);
			$app->triggerEvent('onTP_ProcessSubmit', array($post, $vars));
		}
		else
		{
			$app->enqueueMessage(Text::_('SOME_ERROR_OCCURRED'), 'error');
		}
	}

	/**
	 * Method getPaymentVars.
	 *
	 * @param   string  $pg_plugin  Plugin Name
	 * @param   string  $orderid    Order ID.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function getPaymentVars($pg_plugin, $orderid)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$params              = ComponentHelper::getParams('com_quick2cart');
		$chkoutItemid        = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cartcheckout');
		$pass_data           = $this->getdetails($orderid);
		$vars                = new stdClass;

		// Append prefix and order_id
		$vars->order_id      = $pass_data->prefix . $orderid;
		$vars->user_id       = $pass_data->user_id;

		$billDetails                 = array();
		$billDetails['firstname']    = $vars->user_firstname = $pass_data->firstname;
		$billDetails['lastname']     = $vars->user_lastname = $pass_data->lastname;
		$billDetails['add_line1']    = $vars->user_address = $pass_data->address;
		$billDetails['email']        = $vars->user_email = $pass_data->user_email;
		$billDetails['city']         = $vars->user_city = $pass_data->city;
		$billDetails['country_code'] = $vars->user_country = $pass_data->country_code;
		$billDetails['state_code']   = $vars->user_state = $pass_data->state_code;
		$billDetails['zipcode']      = $vars->user_zip = $pass_data->zipcode;
		$billDetails['phone']        = $vars->phone = $pass_data->phone;

		// Remove new line
		$remove_character = array(
			"\n",
			"\r\n",
			"\r"
		);

		if (!empty($billDetails['add_line1']))
		{
			$billDetails['add_line1'] = str_replace($remove_character, ' ', $billDetails['add_line1']);
		}

		/*$billDetails['add_line2'] = str_replace($remove_character ,' ', $billDetails['add_line2']);*/

		$guest_email = '';

		if (!$pass_data->user_id && $params->get('guest'))
		{
			$guest_email = "&email=" . md5($pass_data->user_email);
		}

		$vars->item_name        = $pass_data->order_item_name;
		$vars->submiturl        = Route::_(
		"index.php?option=com_quick2cart&task=payment.confirmpayment&orderid=" .
		($orderid) . "&processor={$pg_plugin}"
		);

		$ItemId = $comquick2cartHelper->getitemid(
		"index.php?option=com_quick2cart&view=orders&layout=order"
		. $guest_email . "&orderid=" . ($orderid) . "&processor={$pg_plugin}"
		);

		$vars->return  = Uri::root() . substr(
		Route::_("index.php?option=com_quick2cart&view=orders&layout=order" .
			$guest_email . "&orderid=" . ($orderid) . "&processor={$pg_plugin}" . "&Itemid=" . $ItemId
		), strlen(Uri::base(true)) + 1
		);

		$vars->cancel_return    = Uri::root() . substr(
		Route::_(
		"index.php?option=com_quick2cart&view=cartcheckout&layout=cancel&processor={$pg_plugin}&Itemid=" . $chkoutItemid
		), strlen(Uri::base(true)) + 1
		);
		$vars->url = $vars->notify_url = Route::_(
		Uri::root() . "index.php?option=com_quick2cart&task=payment.processpayment&orderid=" .
		($orderid) . $guest_email . "&processor=" . $pg_plugin
		);
		$vars->currency_code    = $pass_data->currency;
		$vars->comment          = $pass_data->customer_note;
		$vars->amount           = $pass_data->order_amt;
		$vars->bootstrapVersion = $params->get("bootstrap_version");

		if ($pg_plugin == 'paypal')
		{
			$send_payments_to_owner = $params->get('send_payments_to_store_owner', 0);
			$singleStoreCkout       = $params->get('singleStoreCkout', 0);
			$commission             = $params->get('commission', 0);

			// Lets set the paypal email if admin is not handling transactions
			if ($send_payments_to_owner && $singleStoreCkout == 1 && $commission == 0)
			{
				$vars->business = $this->getStorePaypalId($orderid);
			}
		} else if ($pg_plugin == 'razorpay')
		{
			$vars->order_id = $orderid;
		}

		$adaptiveDetails            = array();
		$Quick2cartModelpayment     = new Quick2cartModelpayment;
		$vars->adaptiveReceiverList = $this->getReceiverList($vars, $pg_plugin, $orderid);

		// For pre fill user \info in payment plug
		$vars->userInfo = $billDetails;
		$vars->client   = "com_quick2cart";

		return $vars;
	}

	/**
	 * Method getReceiverList.
	 *
	 * @param   Object  $vars       Bar
	 * @param   string  $pg_plugin  Plugin Name
	 * @param   string  $orderid    Order ID.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function getReceiverList($vars, $pg_plugin, $orderid)
	{
		// GET BUSINESS EMAIL
		$plugin           = PluginHelper::getPlugin('payment', $pg_plugin);
		$pluginParams     = json_decode($plugin->params);
		$businessPayEmial = "";

		if (property_exists($pluginParams, 'business'))
		{
			$businessPayEmial = trim($pluginParams->business);
		}
		else
		{
			return array();
		}

		$params                 = ComponentHelper::getParams('com_quick2cart');
		$send_payments_to_owner = $params->get('send_payments_to_owner', 0);

		if ($pg_plugin == 'adaptive_paypal' || $pg_plugin == 'stripe')
		{
			// Lets set the paypal email if admin is not handling transactions
			{
				$storeHelper = new storeHelper;

				// Get payee detail having only paypal mode
				$adaptiveDetails = $storeHelper->getorderItemsStoreInfo($orderid);

				$params     = ComponentHelper::getParams('com_quick2cart');
				$commission = $params->get("commission", 0);

				// GET BUSINESS EMAIL
				$plugin           = PluginHelper::getPlugin('payment', $pg_plugin);
				$pluginParams     = json_decode($plugin->params);
				$businessPayEmial = "";

				if (property_exists($pluginParams, 'business'))
				{
					$businessPayEmial = trim($pluginParams->business);
				}

				$receiverList                = array();
				$receiverList[0]             = array();

				// Used to add ship and tax amount to admin
				$tamount                     = 0;

				// Admin has his own products
				$receiverList[0]['receiver'] = $businessPayEmial;
				$receiverList[0]['amount']   = $vars->amount;
				$receiverList[0]['primary']  = true;

				if (!empty($adaptiveDetails[$businessPayEmial]))
				{
					unset($adaptiveDetails[$businessPayEmial]);
				}

				// Add other receivers
				$index = 1;

				if ($pg_plugin == 'adaptive_paypal' && !empty($orderid))
				{
					foreach ($adaptiveDetails as $payDetail => $detail)
					{
						$receiverList[$index]['receiver']  = $payDetail;
						$receiverList[$index]['amount']    = $detail['commissonCutPrice'];
						$receiverList[$index]['primary']   = false;
						$receiverList[$index]['vendor_id'] = $detail['vendor_id'];
						$receiverList[$index]['store_id']  = $detail['store_id'];
						$index++;
					}
				}

				if ($pg_plugin == 'stripe' && !empty($orderid) && $pluginParams->enableconnect == 1)
				{
					foreach ($adaptiveDetails as $payDetail => $detail)
					{
						$receiverList[$index]['receiver']  = $payDetail;
						$receiverList[$index]['amount']    = $detail['commissonCutPrice'];
						$receiverList[$index]['primary']   = false;
						$receiverList[$index]['vendor_id'] = $detail['vendor_id'];
						$receiverList[$index]['store_id']  = $detail['store_id'];

						$index++;
					}
				}

				return $receiverList;
			}
		}
	}

	/**
	 * Methosd getHTML
	 *
	 * @param   String   $pg_plugin  Plugin Name
	 * @param   Integer  $tid        Tid
	 *
	 * @since   2.2
	 * @return  list.
	 */
	public function getHTML($pg_plugin, $tid)
	{
		$app  = Factory::getApplication();
		$vars = $this->getPaymentVars($pg_plugin, $tid);

		PluginHelper::importPlugin('system');
		$result      = $app->triggerEvent('onBeforeQ2cPaymentFormPrepare', array($vars));

		if (!empty($result))
		{
			$vars = $result[0];
		}

		// Depricated
		$result      = $app->triggerEvent('onBeforeQ2cPay', array($vars));

		if (!empty($result))
		{
			$vars = $result[0];
		}

		PluginHelper::importPlugin('payment', $pg_plugin);
		$html                  = $app->triggerEvent('onTP_GetHTML', array($vars));
		/*$enabledPaymentPlugins = PluginHelper::getPlugin('payment');
		$configuredPluginIndex = 0;

		// Loop through all the enabled payment gateways and return the HTML for selected payment gateway
		foreach ($enabledPaymentPlugins as $k => $enabledPaymentPlugin)
		{
			if ($enabledPaymentPlugin->name == $pg_plugin)
			{
				$configuredPluginIndex = $k;
				break;
			}
		}*/

		$paymentPluginHtml   = array();
		//~ $paymentPluginHtml[] = $html[$configuredPluginIndex];
		$paymentPluginHtml[] = $html[0];

		return $paymentPluginHtml;
	}

	/**
	 * Methosd getdetails
	 *
	 * @param   Integer  $tid  Tid
	 *
	 * @since   2.2
	 * @return  list.
	 */
	public function getdetails($tid)
	{
		$query = "SELECT user_id,firstname,lastname,address,user_email,city,country_code,state_code,zipcode,phone
				FROM #__kart_users as ou
				where ou.order_id=" . (INT) $tid . " AND ou.address_type='BT'";
		$this->_db->setQuery($query);
		$orderdetails = $this->_db->loadObjectlist();
		$query        = "SELECT oi.order_item_name
				FROM #__kart_order_item  as oi
				where oi.order_id=" . $tid;
		$this->_db->setQuery($query);
		$orderitems                         = $this->_db->loadResult();
		$orderdetails['0']->order_item_name = $orderitems;
		$query                              = "SELECT o.amount,o.currency,o.customer_note,o.prefix
				FROM #__kart_orders  as o
				where o.id=" . $tid;
		$this->_db->setQuery($query);
		$orderamt                         = $this->_db->loadObjectlist();
		$orderdetails['0']->prefix        = $orderamt[0]->prefix;
		$orderdetails['0']->order_amt     = $orderamt[0]->amount;
		$orderdetails['0']->currency      = $orderamt[0]->currency;
		$orderdetails['0']->customer_note = preg_replace('/\<br(\s*)?\/?\>/i', " ", $orderamt[0]->customer_note);

		return $orderdetails['0'];
	}

	/**
	 * Methosd processpayment
	 *
	 * @param   Array    $post       Post Array
	 * @param   string   $pg_plugin  Plugin Name
	 * @param   Integer  $order_id   Order Id
	 *
	 * @since   2.2
	 * @return  list.
	 */
	public function processpayment($post, $pg_plugin, $order_id)
	{
		$app                 = Factory::getApplication();
		$comquick2cartHelper = new comquick2cartHelper;
		$jinput              = $app->input;
		$jinput->set('remote', 1);

		$sacontroller = new quick2cartController;
		$sacontroller->execute('clearcart');

		$chkoutItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cartcheckout');
		$return_resp  = array();

		// Authorise Post Data
		if (!empty($post['plugin_payment_method']) && $post['plugin_payment_method'] == 'onsite')
		{
			$plugin_payment_method = $post['plugin_payment_method'];
		}

		$order_id = $this->extract_prefix($order_id);
		$vars     = $this->getPaymentVars($pg_plugin, $order_id);

		// Payment trigger
		PluginHelper::importPlugin('payment', $pg_plugin);
		$processPaymentData = $app->triggerEvent('onTP_Processpayment', array($post, $vars));
		$processPaymentData = $processPaymentData[0];

		$res = @$this->storelog($pg_plugin, $processPaymentData);

		// Get order id
		if (empty($order_id))
		{
			$order_id = $processPaymentData['order_id'];
		}

		$order_id = $this->extract_prefix($order_id);
		/*start for guest checkout*/
		$query    = "SELECT ou.user_id,ou.user_email
			FROM #__kart_users as ou
			WHERE ou.address_type='BT' AND ou.order_id = " . $order_id;
		$this->_db->setQuery($query);
		$user_detail = $this->_db->loadObject();
		$params      = ComponentHelper::getParams('com_quick2cart');
		$guest_email = "";

		if (!$user_detail->user_id && $params->get('guest'))
		{
			$guest_email = "&email=" . md5($user_detail->user_email);
		}
		/*end for guest checkout*/

		$processPaymentData['processor'] = $pg_plugin;
		$processPaymentData['status']    = trim($processPaymentData['status']);
		$query             = "SELECT o.amount
				FROM #__kart_orders  as o
				where o.id=" . $order_id;
		$this->_db->setQuery($query);
		$order_amount          = $this->_db->loadResult();
		$return_resp['status'] = '0';

		$epsilon  = 0.00;
		$epsilon2 = 0.01;

		$return_resp['msg'] = Text::_('COM_QUICK2CART_ORDER_THNX');

		if ($processPaymentData['status'] == 'C' && ($processPaymentData['total_paid_amt'] - $order_amount) >= $epsilon)
		{
			// Received amount is greater or equal to order amount
			$processPaymentData['status']        = 'C';
			$return_resp['status'] = '1';
			$return_resp['msg']    = Text::_('COM_QUICK2CART_ORDER_THNX_CONFIRM');
		}
		elseif (($order_amount - $processPaymentData['total_paid_amt']) > $epsilon2)
		{
			// Received amount les
			$processPaymentData['status']        = 'E';
			$return_resp['status'] = '0';
			$comp_pay              = "&paybuttonstatus=1";
			$return_resp['msg']    = Text::_('COM_QUICK2CART_ORDER_THNX_ERROR');
		}
		elseif (empty($processPaymentData['status']))
		{
			$processPaymentData['status']        = 'P';
			$return_resp['status'] = '0';
			$comp_pay              = "&paybuttonstatus=1";
			$return_resp['msg']    = Text::_('COM_QUICK2CART_ORDER_THNX');
		}

		if ($processPaymentData['status'] != 'C' && !empty($processPaymentData['error']))
		{
			$return_resp['msg'] = $processPaymentData['error']['code'] . " " . $processPaymentData['error']['desc'];
			$comp_pay           = "&paybuttonstatus=1";
			$link               = '<a href="#complete-order">' . Text::_('COM_QUICK2CART_ORDER_PROCESS_AGAIN_TEXT') . '</a>';
			$comp_pay_msg       = '</br>' . Text::sprintf('COM_QUICK2CART_ORDER_PROCESS_AGAIN', $link);
			$return_resp['msg'] .= $comp_pay_msg;
		}

		$this->updateOrder($processPaymentData, $pg_plugin);
		$comquick2cartHelper->updatestatus($order_id, $processPaymentData['status'], '', 1);

		$ItemId = $comquick2cartHelper->getitemid(
		"index.php?option=com_quick2cart&view=orders&layout=order" . $guest_email . "&orderid=" .
		($order_id) . "&processor={$pg_plugin}" . $comp_pay
		);

		$return_resp['return'] = Uri::root() . substr(
		Route::_(
		"index.php?option=com_quick2cart&view=orders&layout=order" . $guest_email . "&orderid=" .
		($order_id) . "&processor={$pg_plugin}" . $comp_pay . "&Itemid=" . $ItemId, false
		),
		strlen(Uri::base(true)) + 1
		);

		// Save/update comment
		$comment_present = array_key_exists('comment', $post);

		if ($comment_present)
		{
			$this->savePaymentComment($order_id, $post['comment']);
		}

		return $return_resp;
	}

	/**
	 * Save the payment form comment/note field detail in db
	 *
	 * @param   integer  $order_id  order id.
	 * @param   string   $comment   comment/note field detail from payment gateway form details .
	 *
	 * @since   2.2
	 * @return  list.
	 */
	private function savePaymentComment($order_id, $comment)
	{
		if ($order_id)
		{
			$obj = new stdClass;
			$db  = Factory::getDBO();

			$obj->id           = $order_id;
			$obj->payment_note = $comment;

			if ($obj->id)
			{
				try
				{
					$db->updateObject('#__kart_orders', $obj, 'id');
				}
				catch(\RuntimeException $e)
				{
					$this->setError($e->getMessage());

					return false;
				}
			}
		}
	}

	/**
	 * Method addPayoutEntry
	 *
	 * @param   Array  $name  Name
	 * @param   Array  $data  Data
	 *
	 * @return  prefix
	 *
	 * @since   1.6
	 */
	public function storelog($name, $data)
	{
		$data1              = array();
		$data1['raw_data']  = $data['raw_data'];
		$data1['JT_CLIENT'] = "com_quick2cart";
		PluginHelper::importPlugin('payment', $name);
		$data = Factory::getApplication()->triggerEvent('onTP_Storelog', array($data1)
		);
	}

	/**
	 * Method addPayoutEntry
	 *
	 * @param   Array   $data       Transaction id
	 * @param   String  $pg_plugin  Transaction id
	 *
	 * @return  prefix
	 *
	 * @since   1.6
	 */
	public function updateOrder($data, $pg_plugin)
	{
		$db                  = Factory::getDBO();
		$res                 = new stdClass;
		$eoid                = $this->extract_prefix($data['order_id']);
		$res->id             = $eoid;
		$res->mdate          = date("Y-m-d H:i:s");
		$res->transaction_id = $data['transaction_id'];
		$res->processor      = $data['processor'];

		// Appending raw data to orders's extra field data
		$comquick2cartHelper = new comquick2cartHelper;
		$q                   = "SELECT  `extra` FROM  `#__kart_orders` WHERE `id` =" . $eoid;
		$res->extra          = $comquick2cartHelper->appendExtraFieldData($data['raw_data'], $q);

		if (!$db->updateObject('#__kart_orders', $res, 'id'))
		{
			/*return false;*/
		}

		// Add payout entry
		$payout_id = $this->addPayoutEntry($eoid, $res->transaction_id, $data['status'], $pg_plugin);
	}

	/**
	 * Method addPayoutEntry
	 *
	 * @param   Integer  $order_id   Transaction id
	 * @param   Integer  $txnid      Transaction id
	 * @param   Integer  $status     User id
	 * @param   Integer  $pg_plugin  User id
	 *
	 * @return  prefix
	 *
	 * @since   1.6
	 */
	public function addPayoutEntry($order_id, $txnid, $status, $pg_plugin)
	{
		// GET BUSINESS EMAIL
		$plugin           = PluginHelper::getPlugin('payment', $pg_plugin);
		$pluginParams     = json_decode($plugin->params);
		$businessPayEmial = "";

		if (property_exists($pluginParams, 'business'))
		{
			$businessPayEmial = trim($pluginParams->business);
		}
		else
		{
			return array();
		}

		$params                 = ComponentHelper::getParams('com_quick2cart');
		$send_payments_to_owner = $params->get('send_payments_to_owner', 0);

		if ($pg_plugin == 'adaptive_paypal')
		{
			$comquick2cartHelper    = new comquick2cartHelper;
			$storeHelper            = new storeHelper;
			$adaptiveDetails        = $storeHelper->getorderItemsStoreInfo($order_id);
			$Quick2cartModelReports = $comquick2cartHelper->loadqtcClass(
			JPATH_SITE . "/components/com_quick2cart/models/reports.php", 'Quick2cartModelReports');

			$reportStatus = ($status == 'C') ? 1 : 0;

			foreach ($adaptiveDetails as $userReport)
			{
				$Quick2cartModelpayment = new Quick2cartModelpayment;
				$payDetail              = $Quick2cartModelpayment->getPayoutId($txnid, $userReport['owner']);

				if (!empty($payDetail) && $payDetail['status'] == $reportStatus)
				{
					/* payout already present mean $payDetail will not empty AND STATUS is same then dont process.
					for new payout,thisl will not process*/
					break;
				}

				$post                    = array();
				$post['id']              = empty($payDetail['id']) ? '' : $payDetail['id'];
				$post['user_id']         = $userReport['owner'];
				$post['payee_name']      = $comquick2cartHelper->getUserName($post['user_id']);
				$post['paypal_email']    = $userReport['store_email'];
				$post['transaction_id']  = $txnid;
				$post['payment_amount']  = $userReport['commissonCutPrice'];
				$post['payout_date']     = date('Y-m-d');
				$post['status']          = $reportStatus;
				$post['payment_comment'] = "adaptive pay";
				$Quick2cartModelReports->savePayout($post);
			}
		}
	}

	/**
	 * Method getPayoutId
	 *
	 * @param   Integer  $transactionID  Transaction id
	 * @param   Integer  $userid         User id
	 *
	 * @return  prefix
	 *
	 * @since   1.6
	 */
	public function getPayoutId($transactionID, $userid)
	{
		$db    = Factory::getDBO();
		$query = "SELECT `id`,`status`
		FROM `#__kart_payouts`
		WHERE `transaction_id`='" . $transactionID . "' AND `user_id`=" . $userid;
		$db->setQuery($query);

		return $db->loadAssoc();
	}

	/**
	 * Method extract_prefix
	 *
	 * @param   string  $prefix_orderid  Prefix Order id
	 *
	 * @return  prefix
	 *
	 * @since   1.6
	 */
	public function extract_prefix($prefix_orderid)
	{
		$params       = ComponentHelper::getParams('com_quick2cart');
		$separator    = (string) $params->get('separator');
		$prefix_array = explode($separator, $prefix_orderid);

		if (count($prefix_array) == 1)
		{
			return $prefix_array[0];
		}
		else
		{
			$use_random_orderid = (int) $params->get('random_orderid');
			$order_id = ($use_random_orderid) ? $prefix_array[2] : $prefix_array[1];
			$order_id = ltrim($order_id, "0");

			/* @TODO trim the padded zero's from order id*/
			return $order_id;
		}
	}

	/**
	 * Method generate_prefix
	 *
	 * @param   String  $oid  Order ID
	 *
	 * @return  prefix
	 *
	 * @since   1.6
	 */
	public function generate_prefix($oid)
	{
		$params             = ComponentHelper::getParams('com_quick2cart');
		/*##############################################################*/
		// Lets make a random char for this order
		// Take order prefix set by admin
		$order_prefix       = (string) $params->get('order_prefix');

		// String length should not be more than 5
		$order_prefix       = substr($order_prefix, 0, 5);

		// Take separator set by admin
		$separator          = (string) $params->get('separator');
		$prefix             = $order_prefix . $separator;

		// Check if we have to add random number to order id
		$use_random_orderid = (int) $params->get('random_orderid');

		if ($use_random_orderid)
		{
			$random_numer = $this->_random(5);
			$prefix .= $random_numer . $separator;
			/*this length shud be such that it matches the column lenth of primary key
			it is used to add pading
			order_id_column_field_length - prefix_length - no_of_underscores - length_of_random number*/
			$len = (23 - 5 - 2 - 5);
		}
		else
		{
			/*this length shud be such that it matches the column lenth of primary key
			it is used to add pading
			order_id_column_field_length - prefix_length - no_of_underscores*/
			$len = (23 - 5 - 2);
		}
		/*##############################################################*/

		$maxlen        = 23 - strlen($prefix) - strlen($oid);
		$padding_count = (int) $params->get('padding_count');

		// Use padding length set by admin only if it is les than allowed(calculate) length
		if ($padding_count > $maxlen)
		{
			$padding_count = $maxlen;
		}

		$append = '';

		if (strlen((string) $oid) <= $len)
		{
			for ($z = 0; $z < $padding_count; $z++)
			{
				$append .= '0';
			}

			/*$append=$append.$oid;*/
		}

		$prefix .= $append;

		return $prefix;
	}

	/**
	 * Method _random
	 *
	 * @param   Integer  $length  Length
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function _random($length = 5)
	{
		$salt   = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len    = strlen($salt);
		$random = '';

		$stat = @stat(__FILE__);

		if (empty($stat) || !is_array($stat))
		{
			$stat = array(php_uname());
		}

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i++)
		{
			$random .= $salt[mt_rand(0, $len - 1)];
		}

		return $random;
	}

	/**
	 * Method This function update order gateway on change of gateway.
	 *
	 * @param   string  $selectedGateway  Select Gateway.
	 * @param   string  $order_id         Order ID.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function updateOrderGateway($selectedGateway, $order_id)
	{
		$db             = Factory::getDBO();
		$row            = new stdClass;
		$row->id        = $order_id;
		$row->processor = $selectedGateway;

		try
		{
			$this->_db->updateObject('#__kart_orders', $row, 'id');
		}
		catch(\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return 0;
		}

		return 1;
	}

	/**
	 * Method gives first stores paypal email .
	 *
	 * @param   integer  $order_id  order id .
	 *
	 * @since    2.2
	 * @return   string|boolean paypal email, false if fails
	 */
	public function getStorePaypalId($order_id)
	{
		if ($order_id)
		{
			$db    = Factory::getDBO();
			$query = $db->getQuery(true);

			// Check in tax related table
			$query->select('s.`vendor_id`');
			$query->from("#__kart_order_item AS i");
			$query->join('LEFT', '`#__kart_orders` AS o ON i.order_id=o.id');
			$query->join('LEFT', '`#__kart_store` AS s ON s.id = i.store_id');
			$query->where('o.id=' . $order_id);

			try
			{
				$db->setQuery($query);
				$vendorId = $db->loadResult();
				JLoader::import('vendorclientxref', JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');
				$vendorClientXrefTable = Table::getInstance('vendorclientxref', 'TjvendorsTable', array());
				$vendorClientXrefTable->load(array('vendor_id' => $vendorId, 'client' => 'com_quick2cart'));

				if ($vendorClientXrefTable->params != null || !empty($vendorClientXrefTable->params))
				{
					$paymentDetails = json_decode($vendorClientXrefTable->params)->payment_gateway;
				}

				if (!empty($paymentDetails))
				{
					foreach ($paymentDetails as $paymentDetail)
					{
						if ($paymentDetail->payment_gateways == 'paypal')
						{
							return $paymentDetail->payment_email_id;
						}
					}
				}
			}
			catch (Exception $e)
			{
				$this->setMessage(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 'error');

				return false;
			}
		}

		return '';
	}
}
