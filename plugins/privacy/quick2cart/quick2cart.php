<?php
/**
 * @package     Quick2Cart
 * @subpackage  Plg_Privacy_Quick2Cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\User;

JLoader::register('PrivacyPlugin', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/plugin.php');
JLoader::register('PrivacyRemovalStatus', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/removal/status.php');

/**
 * Quick2Cart Privacy Plugin.
 *
 * @since  2.9.14
 */
class PlgPrivacyQuick2Cart extends PrivacyPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 *
	 * @since  2.9.14
	 */
	protected $autoloadLanguage = true;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  2.9.14
	 */
	protected $db;

	/**
	 * Reports the privacy related capabilities for this plugin to site administrators.
	 *
	 * @return  array
	 *
	 * @since   2.9.14
	 */
	public function onPrivacyCollectAdminCapabilities()
	{
		$this->loadLanguage();

		return array(
			Text::_('PLG_PRIVACY_QUICK2CART') => array(
				Text::_('PLG_PRIVACY_QUICK2CART_PRIVACY_CAPABILITY_USER_STORES_DETAIL'),
				Text::_('PLG_PRIVACY_QUICK2CART_PRIVACY_CAPABILITY_USER_ORDERS_DETAIL'),
				Text::_('PLG_PRIVACY_QUICK2CART_PRIVACY_CAPABILITY_COOKIES_DETAIL')
			)
		);
	}

	/**
	 * Processes an export request for Quick2Cart user data
	 *
	 * This event will collect data for the following tables:
	 *
	 * - #__kart_role
	 * - #__kart_cart
	 * - #__kart_orders
	 * - #__kart_payouts
	 * - #__kart_store
	 * - #__kart_users
	 * - #__kart_users_backup
	 * - #__kart_customer_address
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  PrivacyExportDomain[]
	 *
	 * @since   2.9.14
	 */
	public function onPrivacyExportRequest(PrivacyTableRequest $request, User $user = null)
	{
		if (!$user)
		{
			return array();
		}

		/** @var JTableUser $user */
		$userTable = User::getTable();
		$userTable->load($user->id);

		$domains = array();
		$domains[] = $this->createQuick2CartRole($userTable);
		$domains[] = $this->createQuick2CartCart($userTable);
		$domains[] = $this->createQuick2CartOrders($userTable);
		$domains[] = $this->createQuick2CartPayouts($userTable);
		$domains[] = $this->createQuick2CartStores($userTable);
		$domains[] = $this->createQuick2CartUsers($userTable);
		$domains[] = $this->createQuick2CartUsersBackup($userTable);
		$domains[] = $this->createQuick2CartCustomerAddress($userTable);

		return $domains;
	}

	/**
	 * Create the domain for the Quick2Cart user role
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   2.9.14
	 */
	private function createQuick2CartRole(User $user)
	{
		$domain = $this->createDomain('User Role', 'Role of user in Quick2Cart');

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName(array('id', 'store_id', 'user_id', 'role')))
			->from($this->db->quoteName('#__kart_role'))
			->where($this->db->quoteName('user_id') . '=' . $user->id);

		$roles = $this->db->setQuery($query)->loadAssocList();

		if (!empty($roles))
		{
			foreach ($roles as $role)
			{
				$domain->addItem($this->createItemFromArray($role, $role['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for the Quick2Cart user's cart
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   2.9.14
	 */
	private function createQuick2CartCart(User $user)
	{
		$domain = $this->createDomain('Users Cart', 'Users cart in Quick2Cart');

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName(array('cart_id', 'user_id', 'last_updated')))
			->from($this->db->quoteName('#__kart_cart'))
			->where($this->db->quoteName('user_id') . '=' . $user->id);

		$cart = $this->db->setQuery($query)->loadAssoc();

		if (!empty($cart))
		{
			$domain->addItem($this->createItemFromArray($cart, $cart['cart_id']));
		}

		return $domain;
	}

	/**
	 * Create the domain for users orders
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   2.9.14
	 */
	private function createQuick2CartOrders(User $user)
	{
		$domain = $this->createDomain('Users Orders', 'Orders placed by an user');

		$query = $this->db->getQuery(true);
		$query->select($this->db->qn(array('id', 'prefix', 'user_info_id', 'created_by', 'name', 'email', 'cdate', 'mdate', 'transaction_id', 'payee_id')));
		$query->select($this->db->qn(array('original_amount', 'amount', 'coupon_code', 'order_tax', 'order_tax_details', 'order_shipping')));
		$query->select($this->db->qn(array('order_shipping_details', 'fee', 'customer_note', 'payment_note', 'status', 'processor', 'ip_address')));
		$query->select($this->db->qn(array('currency', 'extra', 'couponDetails', 'coupon_discount')));

		$query->from($this->db->quoteName('#__kart_orders'));
		$query->where('(' . $this->db->quoteName('user_info_id') . '=' . $user->id . ' OR ' . $this->db->quoteName('created_by') . '=' . $user->id . ')');

		$orders = $this->db->setQuery($query)->loadAssocList();

		if (!empty($orders))
		{
			foreach ($orders as $order)
			{
				$domain->addItem($this->createItemFromArray($order, $order['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for payouts
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   2.9.14
	 */
	private function createQuick2CartPayouts(User $user)
	{
		$domain = $this->createDomain('Users Payout', 'Payouts given to the store owner');

		$query = $this->db->getQuery(true);
		$query->select($this->db->qn(array('id', 'user_id', 'payee_name', 'ad_id', 'transaction_id', 'date', 'email_id', 'amount', 'status', 'comment')));
		$query->from($this->db->quoteName('#__kart_payouts'));
		$query->where($this->db->quoteName('user_id') . '=' . $user->id);

		$payouts = $this->db->setQuery($query)->loadAssocList();

		if (!empty($payouts))
		{
			foreach ($payouts as $payout)
			{
				$domain->addItem($this->createItemFromArray($payout, $payout['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for the stores owned by user
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   2.9.14
	 */
	private function createQuick2CartStores(User $user)
	{
		$domain = $this->createDomain('Users Stores', 'Details of stores owned by a user');

		$query = $this->db->getQuery(true);
		$query->select($this->db->qn(array('id', 'owner', 'title', 'description', 'address', 'city', 'region', 'country', 'land_mark', 'pincode')));
		$query->select($this->db->qn(array('phone', 'store_email', 'store_avatar', 'live', 'cdate', 'mdate', 'extra', 'company_name', 'payment_mode')));
		$query->select($this->db->qn(array('pay_detail', 'vanityurl', 'header', 'length_id', 'weight_id', 'taxprofile_id', 'shipprofile_id')));
		$query->from($this->db->quoteName('#__kart_store'));
		$query->where($this->db->quoteName('owner') . '=' . $user->id);

		$stores = $this->db->setQuery($query)->loadAssocList();

		if (!empty($stores))
		{
			foreach ($stores as $store)
			{
				$domain->addItem($this->createItemFromArray($store, $store['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for the Quick2Cart users details related to an order
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   2.9.14
	 */
	private function createQuick2CartUsers(User $user)
	{
		$domain = $this->createDomain('Users order details', 'Users shipping/biling address for an order');

		$query = $this->db->getQuery(true);
		$query->select($this->db->qn(array('id', 'user_id', 'order_id', 'user_email', 'address_type', 'firstname', 'lastname', 'vat_number', 'city')));
		$query->select($this->db->qn(array('country_code', 'address', 'state_code', 'zipcode', 'phone', 'approved', 'middlename', 'land_mark')));

		$query->from($this->db->quoteName('#__kart_users'));
		$query->where($this->db->quoteName('user_id') . '=' . $user->id);

		$orderDetails = $this->db->setQuery($query)->loadAssocList();

		if (!empty($orderDetails))
		{
			foreach ($orderDetails as $orderDetail)
			{
				$domain->addItem($this->createItemFromArray($orderDetail, $orderDetail['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for the backup data of users
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   2.9.14
	 */
	private function createQuick2CartUsersBackup(User $user)
	{
		$domain = $this->createDomain('Backup - Users order details', 'Backup of users shipping/biling address for an order');

		if ($this->tableExists('kart_users_backup'))
		{
			$query = $this->db->getQuery(true);
			$query->select($this->db->qn(array('id', 'user_id', 'order_id', 'user_email', 'address_type', 'firstname', 'vat_number', 'tax_exempt')));
			$query->select($this->db->qn(array('lastname', 'country_code', 'address', 'city', 'state_code', 'zipcode', 'phone', 'approved', 'middlename')));
			$query->from($this->db->quoteName('#__kart_users_backup'));
			$query->where($this->db->quoteName('user_id') . '=' . $user->id);

			$orderDetails = $this->db->setQuery($query)->loadAssocList();
		}

		if (!empty($orderDetails))
		{
			foreach ($orderDetails as $orderDetail)
			{
				$domain->addItem($this->createItemFromArray($orderDetail, $orderDetail['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for users stored addresses
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   2.9.14
	 */
	private function createQuick2CartCustomerAddress(User $user)
	{
		$domain = $this->createDomain('Users Stored Addresses', 'Users stored addresses');

		$query = $this->db->getQuery(true);
		$query->select($this->db->qn(array('id', 'user_id', 'firstname', 'middlename', 'lastname', 'vat_number', 'phone', 'address_title')));
		$query->select($this->db->qn(array('user_email', 'address', 'land_mark', 'zipcode', 'country_code', 'state_code', 'city')));
		$query->select($this->db->qn(array('last_used_for_billing', 'last_used_for_shipping')));
		$query->from($this->db->quoteName('#__kart_customer_address'));
		$query->where($this->db->quoteName('user_id') . '=' . $user->id);

		$addresses = $this->db->setQuery($query)->loadAssocList();

		if (!empty($addresses))
		{
			foreach ($addresses as $address)
			{
				$domain->addItem($this->createItemFromArray($address, $address['id']));
			}
		}

		return $domain;
	}

	/**
	 * Performs validation to determine if the data associated with a remove information request can be processed
	 *
	 * This event will not allow a super user account to be removed
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  PrivacyRemovalStatus
	 *
	 * @since   2.9.14
	 */
	public function onPrivacyCanRemoveData(PrivacyTableRequest $request, User $user = null)
	{
		$status = new PrivacyRemovalStatus;

		if (!$user->id)
		{
			return $status;
		}

		// Check if user is store owner
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__kart_store'));
		$query->where($db->quoteName('owner') . '=' . (int) $user->id);
		$db->setQuery($query);
		$stores = $db->loadColumn();

		if (!empty($stores))
		{
			$allProducts = array();
			$allOrders = array();

			foreach ($stores as $storeId)
			{
				// Get store products
				$query = $db->getQuery(true);
				$query->select($db->quoteName('item_id'));
				$query->from($db->quoteName('#__kart_items'));
				$query->where($db->quoteName('store_id') . '=' . (int) $storeId);
				$db->setQuery($query);
				$products = $db->loadColumn();

				if (!empty($products))
				{
					$allProducts = array_merge($allProducts, $products);
				}
			}

			// Check if user has any orders against him
			if (!empty($allProducts))
			{
				foreach ($allProducts as $productId)
				{
					$query = $db->getQuery(true);
					$query->select($db->quoteName('order_id'));
					$query->from($db->quoteName('#__kart_order_item'));
					$query->where($db->quoteName('item_id') . '=' . $productId);
					$db->setQuery($query);
					$orders = $db->loadColumn();

					if (!empty($orders))
					{
						$allOrders = array_merge($allOrders, $orders);
					}
				}
			}

			// Restrict user deletion if there are orders associated with the stores owned by the user
			if (!empty($allOrders))
			{
				$status->canRemove = false;
				$ordersList = 'ID: ' . implode(', ', $allOrders);
				$status->reason    = Text::sprintf('PLG_PRIVACY_QUICK2CART_ERROR_PRODUCTS_WITH_ORDERS', $ordersList);

				return $status;
			}

			// Restrict user deletion if there are products associated with the stores owned by the user
			if (!empty($allProducts))
			{
				$status->canRemove = false;
				$productsList = 'ID: ' . implode(', ', $allProducts);
				$status->reason    = Text::sprintf('PLG_PRIVACY_QUICK2CART_ERROR_PRODUCTS_IN_STORES', $productsList);

				return $status;
			}

			// Restrict user deletion if there are stores associated the user
			if (!empty($stores))
			{
				$status->canRemove = false;
				$storesList = 'ID: ' . implode(', ', $stores);
				$status->reason    = Text::sprintf('PLG_PRIVACY_QUICK2CART_ERROR_STORE_OWNER', $storesList);

				return $status;
			}

			// Restrict user deletion if there are any pending payouts against the user
			BaseDatabaseModel::addIncludePath(JPATH_SITE . '/administrator/components/com_quick2cart/models');
			$quick2cartpayoutModel = BaseDatabaseModel::getInstance('Payout', 'Quick2cartModel');

			$payouts = $quick2cartpayoutModel->getPayoutFormData();

			foreach ($payouts as $payout)
			{
				if ($payout->user_id == $user->id)
				{
					$amount = (float) $payout->total_amount - $payout->fee;

					if ($amount != 0)
					{
						$status->canRemove = false;
						$status->reason    = Text::sprintf('PLG_PRIVACY_QUICK2CART_ERROR_PENDING_PAYOUT', $amount);

						return $status;
					}
				}
			}
		}

		// Restrict user deletion if there are any orders against the user
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__kart_orders'));
		$query->where($db->quoteName('user_info_id') . '=' . $user->id);
		$db->setQuery($query);
		$orders = $db->loadColumn();

		if (!empty($orders))
		{
			$status->canRemove = false;
			$ordersList = 'ID: ' . implode(', ', $orders);
			$status->reason    = Text::sprintf('PLG_PRIVACY_QUICK2CART_ERROR_USER_ORDERS', $ordersList);
		}

		return $status;
	}

	/**
	 * Removes the data associated with a remove information request
	 *
	 * This event will pseudoanonymise the user account
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onPrivacyRemoveData(PrivacyTableRequest $request, User $user = null)
	{
		// This plugin only processes data for registered user accounts
		if (!$user)
		{
			return;
		}

		// If there was an error loading the user do nothing here
		if ($user->guest)
		{
			return;
		}

		$db = $this->db;

		// 1. Delete data from #__kart_customer_address
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__kart_customer_address'))
			->where($db->quoteName('user_id') . '=' . $user->id);

		$db->setQuery($query);
		$db->execute();

		// 2. Delete data from #__kart_users_backup
		if ($this->tableExists('kart_users_backup'))
		{
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__kart_users_backup'))
				->where($db->quoteName('user_id') . '=' . $user->id);

			$db->setQuery($query);
			$db->execute();
		}

		// 3. Delete data from __kart_cart

		// Get users cart id
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('cart_id'));
		$query->from($db->quoteName('#__kart_cart'));
		$query->where($db->quoteName('user_id') . '=' . (int) $user->id);
		$db->setQuery($query);
		$cartId = $db->loadResult();

		// Get items from cart
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('cart_item_id'));
		$query->from($db->quoteName('#__kart_cartitems'));
		$query->where($db->quoteName('cart_id') . '=' . (int) $cartId);
		$db->setQuery($query);
		$cartItems = $db->loadColumn();

		// Delete cart items attributes
		foreach ($cartItems as $cartItem)
		{
			$query = $db->getQuery(true);
			$conditions = array($db->quoteName('cart_item_id') . '=' . (int) $cartItem);
			$query->delete($db->quoteName('#__kart_cartitemattributes'));
			$query->where($conditions);
			$db->setQuery($query);
			$db->execute();
		}

		// Delete cart items from cart
		$query = $db->getQuery(true);
		$conditions = array($db->quoteName('cart_id') . '=' . (int) $c_id);
		$query->delete($db->quoteName('#__kart_cartitems'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();

		// Delete users cart reference
		$query = $db->getQuery(true);
		$conditions = array($db->quoteName('user_id') . '=' . $user->id);
		$query->delete($db->quoteName('#__kart_cart'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Function to check if table exists
	 *
	 * @param   STRING  $tableName  Table name without database prefix
	 *
	 * @return  BOOLEAN
	 *
	 * @since   2.9.14
	 */
	public function tableExists($tableName)
	{
		// Check if table exists
		$db        = Factory::getDbo();
		$dbPrefix  = $db->getPrefix();
		$alltables = $db->getTableList();

		if (in_array($dbPrefix . $tableName, $alltables))
		{
			return true;
		}

		return false;
	}
}
