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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Shipmanager Model.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelShipmanager extends BaseDatabaseModel
{
	/**
	 * Method getCountry.
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function getCountry()
	{
		$db    = Factory::getDBO();
		$query = "select country from #__kart_country";
		$db->setQuery($query);
		$rows = $db->loadColumn();

		return $rows;
	}

	/**
	 * Method getStatelist.
	 *
	 * @param   Integer  $country  country
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function getStatelist($country)
	{
		$db    = Factory::getDBO();
		$query = "SELECT r.region FROM #__kart_region AS r LEFT JOIN #__kart_country as c
		ON r.country_code = c.country_code where c.country = \"" . $country . "\"";
		$db->setQuery($query);
		$rows = $db->loadColumn();

		return $rows;
	}

	/**
	 * Method getCity.
	 *
	 * @param   Integer  $country  country
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function getCity($country)
	{
		$db    = Factory::getDBO();
		$query = "SELECT r.region FROM #__kart_region AS r LEFT JOIN #__kart_country as c
		ON r.country_code = c.country_code where c.country = \"" . $country . "\"";
		$query = "SELECT r.city FROM  `#__kart_city` AS r
		LEFT JOIN `#__kart_country` AS c ON r.country_code = c.country_code
		WHERE c.country =\"" . $country . "\"";
		$db->setQuery($query);
		$rows = $db->loadColumn();

		return $rows;
	}

	/**
	 * Method storeShipData.
	 *
	 * @param   Array  $data  Data
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function storeShipData($data)
	{
		$params      = ComponentHelper::getParams('com_quick2cart');
		$multi_curr  = $params->get('addcurrency');
		$multi_currs = explode(",", $multi_curr);
		$keytype     = "";

		$ship_unit = isset($data['geo_type']) ? $data['geo_type'] : "everywhere";

		switch ($ship_unit)
		{
			case "everywhere":
				$indexname = $keytype = "country";
				break;
			case "byregion":
				$keytype   = "region";
				$indexname = "region";
				break;
			case "bycity":
				$keytype = $indexname = "city";
				break;
			case "qtc_forallcountry":
				$keytype   = "qtc_forallcountry";
				$indexname = "country";
				break;
		}

		if (!empty($data['geo'][$indexname]))
		{
			$add = 1;

			foreach ($multi_currs as $cur)
			{
				$key = "";
				$key = $keytype . '_' . $cur;

				if (empty($data[$key]))
				{
					// DONT ADD when price is not entered
					$add = -1;
				}
				else
				{
					$add = 1;
					break;
				}
			}

			if ($add == 1)
			{
				$countylist = explode('|', $data['geo'][$indexname]);
				$countylist = array_filter($countylist, "trim");

				foreach ($countylist as $value)
				{
					$shipid = 0;
					$shipid = $this->addShipManagerEntry($keytype, $value);

					$this->shipManagerCurrency($keytype, $shipid, $data, $multi_currs);
				}
			}
		}
	}

	/**
	 * Method addShipManagerEntry.
	 *
	 * @param   Integer  $key    key contain one of state,county,city
	 * @param   Array    $value  contain key's value
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function addShipManagerEntry($key, $value)
	{
		$key   = ($key == "qtc_forallcountry") ? "country" : $key;
		$db    = Factory::getDBO();
		$query = "select id from `#__kart_ship_manager` where `value`='$value' AND `key`='$key' ";
		$db->setQuery($query);
		$id = $db->loadResult();

		if ($id)
		{
			return $id;
		}
		else
		{
			$row        = new stdClass;
			$row->key   = $key;
			$row->value = $value;

			try
			{
				$db->insertObject('#__kart_ship_manager', $row, 'cart_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

			return $db->insertid();
		}
	}

	/**
	 * Method shipManagerCurrency.
	 *
	 * @param   String   $name       name
	 * @param   Integer  $shipid     Ship Id
	 * @param   Array    $data       Data
	 * @param   Integer  $multicurr  Multicurrency
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function shipManagerCurrency($name, $shipid, $data, $multicurr)
	{
		$db = Factory::getDBO();

		foreach ($multicurr as $curr)
		{
			$key = "";
			$key = $name . "_" . $curr;

			if (!empty($data[$key]))
			{
				$present = $this->getshipManagerCurrencyId($shipid, $curr);

				// Present then update
				if (!empty($present))
				{
					$row                  = new stdClass;
					$row->id              = $present;
					$row->ship_manager_id = $shipid;
					$row->shipvalue       = $data[$key];
					$row->currency        = $curr;

					try
					{
						$db->updateObject('#__kart_ship_manager_currency', $row, 'id');
					}
					catch (\RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return false;
					}
				}
				else
				{
					// Not present then add
					$row                  = new stdClass;
					$row->ship_manager_id = $shipid;
					$row->shipvalue       = $data[$key];
					$row->currency        = $curr;

					try
					{
						$db->insertObject('#__kart_ship_manager_currency', $row, 'id');
					}
					catch (\RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return false;
					}
				}
			}
		}
	}

	/**
	 * Method getshipManagerCurrencyId.
	 *
	 * @param   Integer  $shipid  Ship Id
	 * @param   Integer  $curr    Currency
	 *
	 * @return	data
	 *
	 * @since	1.6
	 */
	public function getshipManagerCurrencyId($shipid, $curr)
	{
		$db    = Factory::getDBO();
		$query = "select id from `#__kart_ship_manager_currency` where `ship_manager_id`='$shipid' AND `currency`='$curr' ";
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Method getshippinglist.
	 *
	 * @return	id
	 *
	 * @since	1.6
	 */
	public function getshippinglist()
	{
		$db    = Factory::getDBO();
		$query = "select * from #__kart_ship_manager ";
		$db->setQuery($query);
		$id = $db->loadobjectlist();

		foreach ($id as $key)
		{
			$query = "SELECT CONCAT(`shipvalue`,`currency`) as shipprice FROM `#__kart_ship_manager_currency` WHERE ship_manager_id=$key->id";
			$db->setQuery($query);
			$sid              = $db->loadobjectlist();
			$key->shipcharges = $sid;
		}

		return $id;
	}

	/**
	 * Method deletshiplist.
	 *
	 * @param   null|array  $shpid  Shipid
	 *
	 * @return	boolean
	 *
	 * @since	1.6
	 */
	public function deletshiplist($shpid)
	{
		$odid_str = implode(',', $shpid);
		$query    = "DELETE FROM #__kart_ship_manager_currency where ship_manager_id IN (" . $odid_str . ") ";
		$this->_db->setQuery($query);
		$a = $this->_db->execute();

		$query = "DELETE FROM #__kart_ship_manager where id IN (" . $odid_str . ")";
		$this->_db->setQuery($query);
		$a = $this->_db->execute();

		return true;
	}
}
