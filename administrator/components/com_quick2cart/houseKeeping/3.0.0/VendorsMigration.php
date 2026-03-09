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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

/**
 * Migration file for quick2cart vendors migration in TJ-Vendors
 *
 * @since  3.0
 */
class TjHouseKeepingVendorsMigration extends TjModelHouseKeeping
{
	public $title = "Vendor Migration";

	public $description = 'Create vendors for old stores and products';

	/**
	 * Vendor migration script
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function migrate()
	{
		$result = array();

		try
		{
			// Load TJ-Vendors front end helper
			JLoader::import('fronthelper', JPATH_SITE . '/components/com_tjvendors/helpers');

			// Load quick2cart backend models
			BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_quick2cart/models');

			// Load quick2cart store table
			Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');
			Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');

			$db                  = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__kart_store'));
			$db->setQuery($query);
			$stores = $db->loadObjectList();

			$tjvendorFrontHelper = new TjvendorFrontHelper;
			$params = ComponentHelper::getParams('com_quick2cart');
			$directPaymentToStoreOwner = $params->get('send_payments_to_store_owner');

			// Loop through the old stores data and add vendors in TJ-Vendors
			foreach ($stores as $store)
			{
				$vendorId = $tjvendorFrontHelper->checkVendor($store->owner, 'com_quick2cart');

				// Create vendor if not exists
				if (empty($vendorId))
				{
					$vendorId = $this->addVendor($store->owner);
				}

				// Check if the store has paypal email attached to it
				if ($store->payment_mode == 0)
				{
					if (!empty($store->pay_detail))
					{
						if ($directPaymentToStoreOwner == 1)
						{
							$payment_gateway = "paypal";
						}
						else
						{
							$payment_gateway = "adaptive_paypal";
						}

						$gatewayDetails = array("payment_gateway" => array("payment_gateway0" => array("payment_gateways" => $payment_gateway, "payment_email_id" => $store->pay_detail)));

						$vendorXrefTable = Table::getInstance('VendorClientXref', 'TjvendorsTable', array('dbo', $db));
						$vendorXrefTable->load(array('vendor_id' => $vendorId, 'client' => 'com_quick2cart'));
						$vendorXrefTable->params = json_encode($gatewayDetails);

						if (!empty($vendorXrefTable->id))
						{
							$vendorXrefTable->store();
						}
					}
				}

				// Update store with respective vendor Id
				$storeTable = Table::getInstance('Store', 'Quick2cartTable', array('dbo', $db));
				$storeTable->load(array('id' => $store->id));
				$storeTable->vendor_id = $vendorId;
				$storeTable->store();

				// Update vendor id in kart_role table
				$query = $db->getQuery(true);
				$query->update($db->qn('#__kart_role'))->set($db->qn('vendor_id') . ' = ' . $vendorId)->where($db->qn('store_id') . ' = ' . $store->id);
				$db->setQuery($query);
				$db->execute();
			}

			$result['status']  = true;
			$result['message'] = "Vendors created successfully.";
		}
		catch (Exception $e)
		{
			$result['err_code'] = '';
			$result['status']   = false;
			$result['message']  = $e->getMessage();
		}

		return $result;
	}

	/**
	 * Function to add a user as vendor
	 *
	 * @param   string  $userId  The user id.
	 *
	 * @return int
	 *
	 * @since 3.0
	 */
	private function addVendor($userId)
	{
		$tjvendorFrontHelper = new TjvendorFrontHelper;

		$vendorData = array();
		$vendorData['userName']        = Factory::getUser($userId)->name;
		$vendorData['vendor_client']   = "com_quick2cart";
		$vendorData['user_id']         = $userId;
		$vendorData['vendor_title']    = $vendorData['userName'];
		$vendorData['state']           = "1";
		$vendorData['notify_vendor']   = 0;

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'vendor');
		$TjvendorsModelVendors = JModelLegacy::getInstance('Vendor', 'TjvendorsModel');
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables', 'vendor');
		$TjvendorsModelVendors->save($vendorData);
		$vendorId = $tjvendorFrontHelper->checkVendor($userId, 'com_quick2cart');

		return $vendorId;
	}
}
