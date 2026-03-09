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

/**
 * Cart controller class.
 *
 * @since  1.0.0
 */
class Quick2cartControllercart extends Quick2cartController
{
	/**
	 * Function used to get the stock limit
	 *
	 * @return  Array
	 *
	 * @since  1.0.0
	 */
	public function stocklimit()
	{
		$jinput = Factory::getApplication()->input;
		$pid    = $jinput->get('pid');
		$parent = $jinput->get('parent');
		$limit  = $jinput->get('limit');

		$model  = $this->getModel('cart');
		$return = $model->getStockLimit($pid, $parent, $limit);
		echo $return;
		jexit();
	}

	/**
	 * Function used to update
	 *
	 * @return  Array
	 *
	 * @since  1.0.0
	 */
	public function update_mod()
	{
		$lang = Factory::getLanguage();
		$lang->load('mod_quick2cart', JPATH_ROOT);

		$comquick2cartHelper = new comquick2cartHelper;
		$data = $comquick2cartHelper->get_module();

		echo $data;
		jexit();
	}

	/**
	 * Function used to migrate country related fields
	 *
	 * @return  boolean
	 *
	 * @since  1.0.0
	 */
	public function migrateCountryRelatedFields()
	{
		$db    = Factory::getDBO();
		$query = "Select `id`,`country_code`,`state_code` From #__kart_users";
		$db->setQuery($query);
		$billing_data = $db->loadObjectlist();

		// 1. Copy table structure.
		$workingTbCopy = "#__kart_users_bak_" . date("Ymd_H_i_s");
		$query         = "CREATE TABLE IF NOT EXISTS " . $workingTbCopy . " LIKE #__kart_users ";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// 2. Copy all data to new table
		$query = "INSERT INTO  " . $workingTbCopy . " SELECT * FROM #__kart_users";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		foreach ($billing_data as $data)
		{
			// Update country column
			if ($data->country_code)
			{
				$query = "Select id From #__tj_country WHERE country LIKE '" . $data->country_code . "'";
				$db->setQuery($query);
				$country_code = $db->loadResult();

				if ($country_code)
				{
					$country_object               = new stdClass;
					$country_object->id           = $data->id;
					$country_object->country_code = $country_code;

					if (!$db->updateObject($workingTbCopy, $country_object, 'id'))
					{
						JError::raiseError(500, $db->stderr());
						echo '-1';
						jexit();
					}
				}
			}

			// Update state column
			if ($data->state_code)
			{
				$query = "Select id From #__tj_region WHERE region LIKE '" . $data->state_code . "'";
				$db->setQuery($query);
				$region_code = $db->loadResult();

				if ($region_code)
				{
					$region_object             = new stdClass;
					$region_object->id         = $data->id;
					$region_object->state_code = $region_code;

					if (!$db->updateObject($workingTbCopy, $region_object, 'id'))
					{
						JError::raiseError(500, $db->stderr());
						echo '-1';
						jexit();
					}
				}
			}
		}

		// Now remane kart_user_table
		$rename_success = $this->renameTable('#__kart_users', '#__kart_users_backup', 0);

		if ($rename_success)
		{
			$rename_success = $this->renameTable($workingTbCopy, '#__kart_users', 0);
			echo 1;
			jexit();
		}

		echo '-1';
		jexit();
	}

	/**
	 * Function used to remane table
	 *
	 * @param   STRING  $table           Old table name
	 * @param   STRING  $newTable        New table name
	 * @param   STRING  $appendDateTime  Add date time
	 *
	 * @return  true/false
	 *
	 * @since  1.0.0
	 */
	public function renameTable($table, $newTable, $appendDateTime = 1)
	{
		$db    = Factory::getDBO();
		$query = "RENAME TABLE " . $table . " TO " . $newTable;

		if ($appendDateTime)
		{
			$query = $query . '_' . date("Ymd_H_i_s");
		}

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}
}
