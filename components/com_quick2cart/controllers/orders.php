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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

JLoader::import('createorder', JPATH_SITE . '/components/com_quick2cart/helpers');

/**
 * Order controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerOrders extends quick2cartController
{
	/**
	 * Constructor
	 *
	 * @since    1.6
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->siteMainHelper   = new comquick2cartHelper;
		$this->vdashboardItemid = $this->siteMainHelper->getitemid('index.php?option=com_quick2cart&view=vendor');
		$this->myOrdersItemId   = $this->siteMainHelper->getitemid('index.php?option=com_quick2cart&view=orders');
	}

	/**
	 * Method to save/update the order status.
	 *
	 * @return  void
	 *
	 * @since    1.0
	 */
	public function save()
	{
		// Check for CSRF token
		Session::checkToken() or die('Invalid Token');

		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		$model    = $this->getModel('orders');
		$jinput   = Factory::getApplication()->input;
		$layout   = $jinput->get('layout', '', "STRING");
		$orderid  = $jinput->get('orderid');
		$post     = $jinput->post;
		$store_id = $post->get('store_id');

		if (empty($store_id))
		{
			// For order detail view
			$store_id = $jinput->get('store_id', '', "INTEGER");
		}

		// Check if user is store owner
		$storeHelper = new storeHelper;
		$storeOwner  = $storeHelper->getStoreOwner($store_id);
		$user        = Factory::getUser();

		if ($user->id != $storeOwner)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$model->setState('request', $post);
		$result = $model->store($store_id);
		$msg    = Text::_('QTC_FIELD_ERROR_SAVING_MSG');

		if ($result == 1)
		{
			$msg = Text::_('QTC_FIELD_SAVING_MSG');
		}
		elseif ($result == 3)
		{
			$msg = Text::_('QTC_REFUND_SAVING_MSG');
		}

		$link = 'index.php?option=com_quick2cart&view=orders';

		if ($layout == "storeorder")
		{
			$link = 'index.php?option=com_quick2cart&view=orders&layout=storeorder';
		}
		elseif ($layout == "customerdetails")
		{
			$link = 'index.php?option=com_quick2cart&view=orders&layout=customerdetails&orderid=' . $orderid . '&store_id=' . $store_id;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Method to update the order status and comment from **order detail page**.
	 *
	 * @return  void
	 *
	 * @since    1.0
	 */
	public function updateStoreItemStatus()
	{
		$jinput   = Factory::getApplication()->input;
		$layout   = $jinput->get('layout', '', "STRING");
		$post     = $jinput->post;
		$store_id = $jinput->get('store_id', '', "INTEGER");
		$orderid  = $jinput->get("orderid", '', "INTEGER");

		$add_note_chk = $post->get('add_note_chk');
		$note         = '';
		$note         = $post->get('order_note', '', "STRING");
		$status       = $jinput->get('status', '', "STRING");
		$notify_chk   = $post->get('notify_chk');

		if (!empty($notify_chk))
		{
			$notify_chk = 1;
		}
		else
		{
			$notify_chk = 0;
		}

		if ($orderid && $store_id)
		{
			// Update item status
			$this->siteMainHelper->updatestatus($orderid, $status, $note, $notify_chk, $store_id);

			// Save order history
			$orderItemsStr = $post->get("orderItemsStr", '', "STRING");
			$orderItems = explode("||", $orderItemsStr);

			foreach ($orderItems as $oitemId)
			{
				// Save order item status history
				$this->siteMainHelper->saveOrderStatusHistory($orderid, $oitemId, $status, $note, $notify_chk);
			}
		}

		// $layout == "order"
		$rLink = "index.php?option=com_quick2cart&view=orders&layout=order&orderid=";
		$link = Route::_($rLink . $orderid . '&store_id=' . $store_id . '&calledStoreview=1&Itemid' . $this->vdashboardItemid, false);

		$this->setRedirect($link);
	}

	/**
	 * Called on cancel button
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	public function cancel()
	{
		$msg = Text::_('CCK_FIELD_CANCEL_MSG', true);
		$this->setRedirect('index.php?option=com_quick2cart', $msg);
	}

	/**
	 * Function for Resending invoice to buyer
	 *
	 * @since   2.2.2
	 * @return   null.
	 */
	public function resendInvoice()
	{
		(Session::checkToken() or Session::checkToken('get')) or jexit(Text::_('JINVALID_TOKEN'));

		$params             = ComponentHelper::getParams('com_quick2cart');
		$multivendor_enable = $params->get('multivendor');
		$app                = Factory::getApplication();
		$jinput             = $app->input;
		$orderid            = $jinput->get('orderid', '', 'INT');
		$store_id           = $jinput->get('store_id', '', 'INT');
		$comquick2cartHelper = new comquick2cartHelper;

		if (empty($multivendor_enable))
		{
			$order    = $comquick2cartHelper->getorderinfo($orderid);
			$store_id = $order['items'][0]->store_id;
			$jinput->set('store_id', $store_id);
		}

		$model  = $this->getModel('Orders');
		$result = $model->resendInvoice();

		$msg = '';

		if ($result)
		{
			$msg = Text::_("COM_QUICK2CART_INVOICE_SEND");
		}

		echo $msg;

		jexit();

		// IF not multi-vendor then redirect to my order list layout

		/*if (empty($multivendor_enable))
		{
			$redirectUrl = Route::_('index.php?option=com_quick2cart&view=orders&layout=default&Itemid=' . $this->myOrdersItemId, false);
		}
		else
		{
			$calledStoreview = $jinput->get('calledStoreview', '', 'INT');

			 MD5	EMAIL
			$email = $jinput->get('email', '', 'RAW');
			$streLinkPrarm = "";

			if (!empty($calledStoreview))
			{
				$streLinkPrarm = "&calledStoreview=1";
			}

			if (!empty($email))
			{
				$streLinkPrarm = "&email=" . $email;
			}

			$redirectUrl =  Uri::base() .
			"index.php?option=com_quick2cart&view=orders&layout=order&orderid=" . $orderid . "&store_id=" . $store_id . $streLinkPrarm;
			For multivendor ON, redirect to myorders->detail page or store's->order detial page

			$this->setRedirect($redirectUrl, $msg);
		}*/
	}

	/**
	 * This function is to generate, store wise invoice PDF
	 *
	 * @since   2.5
	 * @return   json.
	 */
	public function generateInvoicePDF()
	{
		// Check for CSRF token
		Session::checkToken() or die('Invalid Token');

		$params              = ComponentHelper::getParams('com_quick2cart');
		$multivendor_enable  = $params->get('multivendor');
		$app                 = Factory::getApplication();
		$jinput              = $app->input;
		$user                = Factory::getUser();
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;

		// Get order id and store id
		$orderid  = $jinput->get('orderid', 0, 'INT');
		$store_id = $jinput->get('store_id', 0, 'INT');

		$storeOwner = $storeHelper->getStoreOwner($store_id);
		$isOwner    = $comquick2cartHelper->checkOwnership($storeOwner);

		$createOrderHelper = new CreateOrderHelper;
		$orderDetails      = $createOrderHelper->getOrderDetails($orderid);

		if ($user->id == $orderDetails->user_info_id || $isOwner === true)
		{
			$lang = Factory::getLanguage();
			$lang->load('com_quick2cart', JPATH_SITE);
			$storeHelper = new storeHelper;
			$storeHelper->generateInvoicePDF($orderid, $store_id);

			// IF not multi-vendor then redirect to my order list layout
			if (empty($multivendor_enable))
			{
				$redirectUrl = Route::_('index.php?option=com_quick2cart&view=orders&layout=defaul&Itemid=' . $this->myOrdersItemId, false);
			}
			else
			{
				$redirectUrl = Uri::base() . "index.php?option=com_quick2cart&view=orders&layout=order&orderid=" . $orderid . "&Itemid=" . $this->myOrdersItemId;
			}

			$this->setRedirect($redirectUrl);
		}
		else
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		jexit();
	}

	/**
	 * This function is to send email to add feedback to user after buying a product
	 *
	 * @since   5.0.2
	 * @return   json.
	 */
	public function sendFeedbackReminderEmail()
	{

		$params    				= ComponentHelper::getParams('com_quick2cart');
		$input                  = Factory::getApplication()->input;
		$private_keyinurl       = $input->get('pkey', '', 'STRING');
		$pkey_for_feedback_email = $params->get("pkey_for_feedback_email");
		if ($pkey_for_feedback_email != $private_keyinurl) {
			echo Text::_('COM_QUICK2CART_SEND_FEEDBACK_EMAIL_AUTHORIZATION_ERROR');
			return;
		}

		//get the interval days from global config
		$intervalDays = $params->get('interval_days', 1);

		// Get the database object
		$db = Factory::getDbo();

		// Get users who made orders interval days ago and haven't received an email
		$query = $db->getQuery(true)
			->select('o.id AS order_id')
			->from($db->quoteName('#__kart_orders', 'o'))
			->where('o.status = "C"')
			->where('DATE(o.cdate) = DATE_SUB(CURDATE(), INTERVAL ' . $intervalDays . ' DAY)')
			->where('o.feedbackemail_sent = 0'); // Ensure email hasn't been sent

		$db->setQuery($query);
		$users = $db->loadObjectList();


		if (!$users) {
			echo Text::_('COM_QUICK2CART_SEND_FEEDBACK_EMAIL_NOUSERSFOUND_ERROR');
			return;
		}

		$path = JPATH_SITE . '/components/com_quick2cart/helpers/mails.php';

		if (!class_exists('Quick2CartMailsHelper')) {
			JLoader::register('Quick2CartMailsHelper', $path);
			JLoader::load('Quick2CartMailsHelper');
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$quick2CartMailsHelper = new Quick2CartMailsHelper;
		foreach ($users as $user) {
			$orderIitemInfo = $comquick2cartHelper->getorderinfo($user->order_id);
			$quick2CartMailsHelper->onAfterOrderStatusUpdated($orderIitemInfo);
			$updateQuery = $db->getQuery(true)
				->update($db->quoteName('#__kart_orders'))
				->set($db->quoteName('feedbackemail_sent') . ' = 1')
				->where($db->quoteName('id') . ' = ' . (int) $user->order_id);
			$db->setQuery($updateQuery);
			$db->execute();
		}
	}

	/**
	 * Method to delete records.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function deleteorders()
	{
		$model  = $this->getModel('Orders');
		$jinput = Factory::getApplication()->input;
		$post   = $jinput->post;
		$cid    = $post->get('cid', array(), 'ARRAY');
		$msg    = Text::_('ERR_ORDER_DELETED');
		$errorType = 'error';

		if ($model->delete($cid))
		{
			$errorType = 'success';
			$msg = Text::_('ORDER_DELETED');
		}

		$layout   = $jinput->get('layout', 'default', "STRING");
		$myorderItemid = $this->siteMainHelper->getitemid('index.php?option=com_quick2cart&view=orders&layout='. $layout);

		$link = Route::_("index.php?option=com_quick2cart&view=orders&Itemid=" . $myorderItemid, false);

		$this->setMessage($msg, $errorType);
		$this->setRedirect($link, $msg);
	}
}
