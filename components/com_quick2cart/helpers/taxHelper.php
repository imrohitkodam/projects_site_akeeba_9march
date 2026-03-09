<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * TaxHelper
 *
 * @package     Com_Quick2cart
 * @subpackage  site
 * @since       2.2
 */
class TaxHelper
{
	/**
	 * Method to get the record form.
	 *
	 * @param   string  $taxrule_id  taxrule id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getTaxProfileId($taxrule_id)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('taxprofile_id')))->from('#__kart_taxrules')->where('taxrule_id=' . $taxrule_id);
		$db->setQuery((string) $query);

		return $db->loadResult();
	}

	/**
	 * Method to get the profile id of product.
	 *
	 * @param   integer  $item_id  item_id id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getProductProfileId($item_id)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('taxprofile_id')))->from('#__kart_items')->where('item_id=' . $item_id);
		$db->setQuery((string) $query);

		return $db->loadResult();
	}

	/**
	 * Method to get the store list for tax profile having tax rates against it.
	 *
	 * @param   integer  $user_id  user id.
	 *
	 * @since   2.2
	 * @return   Array
	 */
	public function getStoreListForTaxprofile($user_id = 0)
	{
		if (empty($user_id))
		{
			$user    = Factory::getUser();
			$user_id = $user->id;
		}

		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('s.id AS store_id,s.title');
		$query->from('#__kart_store AS s');
		$query->join('INNER', '#__kart_zone AS z ON s.id = z.store_id');
		$query->join('INNER', '#__kart_taxrates AS tr ON z.id = tr.zone_id');
		$query->where('s.owner=' . $user_id);
		$query->group('s.id');
		$db->setQuery((string) $query);

		return $db->loadAssocList();
	}

	/**
	 * Method to get the store list for tax profile having tax rates against it.
	 *
	 * @param   string  $profileId  profileId id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getTaxprofileDetail($profileId)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select("tp.`name` , tp.id, s.`title` , s.`id` AS store_id");
		$query->from('#__kart_store AS s');
		$query->join('INNER', '#__kart_taxprofiles AS tp ON s.id = tp.store_id');
		$query->where('tp.id=' . $profileId);
		$db->setQuery((string) $query);

		return $db->loadAssoc();
	}

	/**
	 * Method to get the store list for tax profile having tax rates against it.
	 *
	 * @param   string  $user_id  user id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getUsersTaxprofiles($user_id = 0)
	{
		if (empty($user_id))
		{
			$user    = Factory::getUser();
			$user_id = $user->id;
		}

		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select("tp.`name` , tp.id, s.`title` , s.`id` AS store_id");
		$query->from('#__kart_store AS s');
		$query->join('INNER', '#__kart_taxprofiles AS tp ON s.id = tp.store_id');
		$query->join('INNER', '#__kart_taxrules AS trule ON trule.taxprofile_id = tp.id');
		$query->where('s.owner=' . $user_id);
		$query->group('tp.id');
		$db->setQuery((string) $query);
		$res = $db->loadAssocList();

		return $db->loadAssocList();
	}

	/**
	 * Method to get the store id from tax profile id.
	 *
	 * @param   integer  $taxPid  taxprofile id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getTaxProfileStoreId($taxPid)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('store_id')));
		$query->from('#__kart_taxprofiles AS p');
		$query->where('p.id=' . $taxPid);
		$db->setQuery((string) $query);

		return $db->loadResult();
	}

	/**
	 * Method to get the store list from tax rule.
	 *
	 * @param   string  $ruleId  taxrule id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getStoreIdFromTaxrule($ruleId)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('store_id')));
		$query->from('#__kart_taxprofiles AS tp');
		$query->join('LEFT', '#__kart_taxrules AS r ON r.taxprofile_id=tp.id');
		$query->where('taxrule_id=' . $ruleId);
		$db->setQuery((string) $query);

		return $db->loadResult();
	}

	/**
	 * Method provides item tax details. pass the product price and the product price and get the tax,
	 * nternally gets the taxprofile id and get the tax
	 *
	 * @param   float    $product_price  product price.
	 * @param   integer  $item_id        item id.
	 * @param   array    $address        Adress detail object
	 * @param   integer  $taxprofile_id  Tax profile id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getItemTax($product_price, $item_id, $address, $taxprofile_id = '')
	{
		$taxHelper         = new taxHelper;
		$amount            = 0;
		$ret['taxdetails'] = array();

		if (empty($taxprofile_id))
		{
			// Get tax profile id
			$taxprofile_id = $taxHelper->getProductProfileId($item_id);
		}

		// Get Application tax rate details
		$ItemTaxes = $taxHelper->getApplicableTaxRates($taxprofile_id, $address);

		// Get tax rate wise commulative total
		$taxRateWiseTotal = $taxHelper->getTaxRateWiseTotal($product_price, $ItemTaxes);

		foreach ($taxRateWiseTotal as $tax_rate)
		{
			$amount += $tax_rate['amount'];
		}

		$ret['item_id']  = $item_id;
		$ret['taxAmount']  = $amount;
		$ret['taxdetails'] = $taxRateWiseTotal;

		return $ret;
	}

	/**
	 * Method provides item wise tax details.
	 *
	 * @param   integer  $taxprofile_id  taxProfile id.
	 * @param   object    $address        Adress detail object
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getApplicableTaxRates($taxprofile_id, $address)
	{
		$tax_rates = array();
		$db        = Factory::getDBO();
		$shipping     = 'shipping';

		if (isset($address->shipping_address))
		{
			$regionCode = (int) isset($address->shipping_address['state']) ? $address->shipping_address['state'] : 0;

			$query = $db->getQuery(true);

			$query->select($db->qn('tr2.id', 'taxrate_id'));
			$query->select($db->qn('tr2.name', 'name'));
			$query->select($db->qn('tr2.percentage', 'rate'));
			$query->select($db->qn('tr1.address'));
			$query->from($db->qn('#__kart_taxrules', 'tr1'));
			$query->join('LEFT', $db->qn('#__kart_taxrates', 'tr2') . 'ON' . $db->qn('tr1.taxrate_id') . '=' . $db->qn('tr2.id'));
			$query->join('LEFT', $db->qn('#__kart_zonerules', 'zr') . 'ON' . $db->qn('tr2.zone_id') . '=' . $db->qn('zr.zone_id'));
			$query->join('LEFT', $db->qn('#__kart_zone', 'z') . 'ON' . $db->qn('tr2.zone_id') . '=' . $db->qn('z.id'));
			$query->where($db->qn('tr1.taxprofile_id') . '=' . (int) $taxprofile_id);
			$query->where($db->qn('tr1.address') . '= ' . $db->quote($shipping));
			$query->where($db->qn('zr.country_id') . '=' . (int) $address->shipping_address['country']);
			$query->where('(' . $db->qn('zr.region_id') . '=' . 0 .' OR '. $db->qn('zr.region_id') . '=' . (int) $regionCode . ')');
			$query->order($db->qn('tr1.ordering') . ' ASC');
			$query->group($db->qn('tr1.taxrate_id'));

			$db->setQuery($query);
			$taxrates_items = $db->loadObjectList();

			// Get all taxrates
			if (isset($taxrates_items))
			{
				foreach ($taxrates_items as $trate)
				{
					$tax_rates[] = array(
						'taxrate_id' => $trate->taxrate_id,
						'name' => $trate->name,
						'rate' => $trate->rate,
						'address' => $trate->address
					);
				}
			}
		}

		if (isset($address->billing_address))
		{
			$regionCode = (int) isset($address->billing_address['state']) ? $address->billing_address['state'] : 0;
			$billing     = 'billing';

			$query = $db->getQuery(true);

			$query->select($db->qn('tr2.id', 'taxrate_id'));
			$query->select($db->qn('tr2.name', 'name'));
			$query->select($db->qn('tr2.percentage', 'rate'));
			$query->select($db->qn('tr1.address'));
			$query->from($db->qn('#__kart_taxrules', 'tr1'));
			$query->join('LEFT', $db->qn('#__kart_taxrates', 'tr2') . 'ON' . $db->qn('tr1.taxrate_id') . '=' . $db->qn('tr2.id'));
			$query->join('LEFT', $db->qn('#__kart_zonerules', 'zr') . 'ON' . $db->qn('tr2.zone_id') . '=' . $db->qn('zr.zone_id'));
			$query->join('LEFT', $db->qn('#__kart_zone', 'z') . 'ON' . $db->qn('tr2.zone_id') . '=' . $db->qn('z.id'));
			$query->where($db->qn('tr1.taxprofile_id') . '=' . (int) $taxprofile_id);
			$query->where($db->qn('tr1.address') . '= ' . $db->quote($billing));
			$query->where($db->qn('zr.country_id') . '=' . (int) $address->shipping_address['country']);
			$query->where('(' . $db->qn('zr.region_id') . '=' . 0 .' OR '. $db->qn('zr.region_id') . '=' . (int) $regionCode . ')');
			$query->order($db->qn('tr1.ordering') . ' ASC');
			$query->group($db->qn('tr1.taxrate_id'));

			$db->setQuery($query);
			$taxrates_items = $db->loadObjectList();

			if (isset($taxrates_items))
			{
				foreach ($taxrates_items as $trate)
				{
					$tax_rates[] = array(
						'taxrate_id' => $trate->taxrate_id,
						'name' => $trate->name,
						'rate' => $trate->rate,
						'address' => $trate->address
					);
				}
			}
		}

		return $tax_rates;
	}

	/**
	 * Method provides item wise tax details.
	 *
	 * @param   float  $item_price  product price.
	 * @param   array  $ItemTaxes   applicable product rates.
	 *
	 * @since   2.2
	 * @return   iterable|object
	 */
	public function getTaxRateWiseTotal($item_price, $ItemTaxes)
	{
		$item_tax_data = array();

		if (!empty($ItemTaxes))
		{
			foreach ($ItemTaxes as $tax_rate)
			{
				if (isset($item_tax_data[$tax_rate['taxrate_id']]))
				{
					$amount = $item_tax_data[$tax_rate['taxrate_id']]['amount'];
				}
				else
				{
					$amount = 0;
				}

				$amount += ($item_price / 100 * $tax_rate['rate']);

				$item_tax_data[$tax_rate['taxrate_id']] = array(
					'taxrate_id' => $tax_rate['taxrate_id'],
					'name' => $tax_rate['name'],
					'rate' => $tax_rate['rate'],
					'amount' => $amount
				);
			}
		}

		return $item_tax_data;
	}

	/**
	 * Method cCheck whether Taxrate is allowed to delete or not.  If not the enqueue error message accordingly..
	 *
	 * @param   string  $id  taxrateid .
	 *
	 * @since   2.2
	 * @return   boolean true or false.
	 */
	public function isAllowedToDelTaxrate($id)
	{
		$app   = Factory::getApplication();
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Check in tax related table
		$query->select('count(*)');
		$query->from($db->quoteName('#__kart_taxrules', 'z'));
		$query->join('INNER', $db->quoteName('#__kart_taxprofiles', 'tp') . ' ON (' . $db->quoteName('z.taxprofile_id') .
		' = ' . $db->quoteName('tp.id') . ')');
		$query->where($db->quoteName('z.taxrate_id') . ' = ' . (int) $id);

		try
		{
			$db->setQuery($query);
			$taxEntry = $db->loadResult();

			if (!empty($taxEntry))
			{
				$errMsg = Text::sprintf('COM_QUICK2CART_TAXRATE_DEL_FOUND_AGAINST_TAXPROFILE', $id);
				$app->enqueueMessage($errMsg, 'error');

				return false;
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Method cCheck whether TaxProfile is allowed to delete or not.  If not the enqueue error message accordingly..
	 *
	 * @param   string  $id  taxprofileid .
	 *
	 * @since   2.2
	 * @return   boolean true or false.
	 */
	public function isAllowedToDelTaxProfile($id)
	{
		$app   = Factory::getApplication();
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);

		// Check in tax related table
		$query->select('count(*)');
		$query->from('#__kart_items AS z');
		$query->where('z.taxprofile_id=' . $id);

		try
		{
			$db->setQuery($query);
			$count = $db->loadResult();

			if (!empty($count))
			{
				$errMsg = Text::sprintf('COM_QUICK2CART_TAXPROFILE_DEL_FOUND_AGAINST_PRODUCT', $id);
				$app->enqueueMessage($errMsg, 'error');

				return false;
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 'error');

			return false;
		}

		// Check in shipping mthods
		try
		{
			// Check in shipping method's rate table
			$query = $db->getQuery(true);
			$query->select('z.id');
			$query->from('#__kart_zoneShipMethods AS z');
			$query->where('z.taxprofileId=' . $id);
			$db->setQuery($query);
			$count = $db->loadResult();

			if (!empty($count))
			{
				$errMsg = Text::sprintf('COM_QUICK2CART_TAXPROFILE_DEL_FOUND_AGAINST_SHIP_METHODS', $id);
				$app->enqueueMessage($errMsg, 'error');

				return false;
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 'error');

			return false;
		}

		return true;
	}
}
