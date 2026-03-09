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
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Zone Model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelZone extends AdminModel
{
	protected $text_prefix = 'COM_QUICK2CART';

	/**
	 * Render view.
	 *
	 * @param   string  $type    An optional associative array of configuration settings.
	 * @param   string  $prefix  An optional associative array of configuration settings.
	 * @param   array   $config  An optional associative array of configuration settings.
	 *
	 * @since   2.2
	 * @return   Object
	 */

	public function getTable($type = 'Zone', $prefix = 'Quick2cartTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.zone', 'zone', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the record form.
	 *
	 * @since   2.2
	 * @return   null
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.zone.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   string  $pk  Private key.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   object  $table  Table object.
	 *
	 * @since   2.2
	 * @return   null
	 */
	protected function prepareTable($table)
	{
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__kart_zone');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
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
		$rows = (array) $tjGeoHelper->getCountryList('com_quick2cart');

		return $rows;
	}

	/**
	 * Gives zone rules list.
	 *
	 * @since   2.2
	 * @return   rulelist.
	 */
	public function getZoneRules ()
	{
		$app = Factory::getApplication();
		$zone_id = $app->input->get('id', 0, 'INT');

		if (!empty($zone_id))
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('zr.zonerule_id as id, c.country as country, reg.region,reg.id AS region_id ');
			$query->from('#__kart_zonerules AS zr');
			$query->join('LEFT', '#__tj_country AS c ON c.id=zr.country_id');
			$query->join('LEFT', '#__tj_region AS reg ON reg.id=zr.region_id');
			$query->where('zr.zone_id=' . $zone_id);
			$query->order('zr.ordering');
			$db->setQuery((string) $query);

			return $db->loadObjectList();
		}
	}

	/**
	 * This function Fetch Zone rule Details.
	 *
	 * @param   Integer  $rule_id  Rule Id
	 *
	 * @since	2.2
	 *
	 * @return   data.
	 */
	public function getZoneRuleDetail($rule_id)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('zr.zonerule_id as id, c.country as country,c.id AS country_id, reg.region,reg.id AS region_id');
		$query->from('#__kart_zonerules AS zr');
		$query->join('LEFT', '#__tj_country AS c ON c.id=zr.country_id');
		$query->join('LEFT', '#__tj_region AS reg ON reg.id=zr.region_id');
		$query->where('zr.zonerule_id=' . $rule_id);
		$query->order('zr.ordering');
		$db->setQuery((string) $query);

		return $db->loadObject();
	}

	/**
	 * This function Fetch Region List.
	 *
	 * @param   Integer  $country_id  Country Id
	 *
	 * @since	2.2
	 *
	 * @return   data.
	 */
	public function getRegionList($country_id)
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
		$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');
		$rows        = $tjGeoHelper->getRegionList($country_id, 'com_quick2cart');

		return $rows;
	}

	/**
	 * This function save country and region/state aginst zone.
	 *
	 * @param   Integer  $update  Update
	 *
	 * @since	2.2
	 * @return   true or false.
	 */
	public function saveZoneRule($update = 0)
	{
		$app = Factory::getApplication();
		$data = $app->input->post->get('jform', array(), 'array');

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from($db->qn('#__kart_zonerules', 'gr'));

		if ($update === 1)
		{
			// Getting zone id from rule id.
			$zone_id = $this->getZoneId($data['zonerule_id']);
			$data['zone_id'] = $zone_id;
			$query->where($db->qn('gr.zonerule_id') . '!=' . (int) $data['zonerule_id']);
		}

		$query->where($db->qn('gr.zone_id') . '=' . (int) $data['zone_id']);
		$query->where($db->qn('gr.country_id') . '=' . (int) $data['country_id']);
		$query->where($db->qn('gr.region_id') . '=' . (int) $data['region_id']);
		$db->setQuery($query);

		$result = $db->loadResult();

		if ($result == 1)
		{
			$this->setError(Text::_('COM_QUICK2CART_ZONERULE_ALREADY_EXISTS'));

			return false;
		}

		$ZoneRule = $this->getTable('Zonerule');

		if (!$ZoneRule->bind($data))
		{
			$this->setError($ZoneRule->getError());

			return false;
		}

		if (!$ZoneRule->check())
		{
			$this->setError($ZoneRule->getError());

			return false;
		}

		if (!$ZoneRule->store())
		{
			$this->setError($ZoneRule->getError());

			return false;
		}

		$app->input->set('zonerule_id', $ZoneRule->zonerule_id);

		return true;
	}

	/**
	 * This function save country and region/state aginst zone.
	 *
	 * @param   object  $ruleId  zone rule id.
	 *
	 * @since	2.2
	 * @return   true or false.
	 */
	public function getZoneId($ruleId)
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('zone_id');
		$query->from('#__kart_zonerules AS zr');
		$query->where('zr.zonerule_id=' . $db->escape($ruleId));
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.9.13
	 */
	public function save($data)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__kart_zone');

		if ($data['id'])
		{
			$query->where($db->quoteName('id') . ' != ' . $data['id']);
		}
		else
		{
			$query->where($db->quoteName('store_id') . ' = ' . $data['store_id']);
		}

		$query->where($db->quoteName('name') . ' = ' . $db->Quote($db->escape($data['name'])));
		$db->setQuery($query);
		$result = $db->loadResult();

		if (!empty($result))
		{
			$this->setError(Text::_("COM_QUICK2CART_ZONE_ALREADY_EXISTS"));

			return false;
		}

		// On before zone save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$isNew      = empty($data['id']) ? true : false;
		$result     = Factory::getApplication()->triggerEvent('onBeforeQ2cSaveZone', array($data, $isNew));

		// Store the data.
		if (!parent::save($data))
		{
			return false;
		}

		$data['id'] = (int) $this->getState($this->getName() . '.id');

		// On after zone save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$plgresult = Factory::getApplication()->triggerEvent('onAfterQ2cSaveZone', array($data, $isNew));

		return true;
	}

	/**
	 * Method to delete rows.
	 *
	 * @param   array  &$pks  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function delete(&$pks)
	{
		$user  = Factory::getUser();
		$table = $this->getTable();
		$pks   = (array) $pks;

		// Check if I am a Super Admin
		$iAmSuperAdmin       = $user->authorise('core.admin');
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				$storeOwner = $storeHelper->getStoreOwner($table->store_id);
				$canDelete  = $comquick2cartHelper->checkOwnership($storeOwner);

				// Super user or else store owner can delete the zones
				$allow = ($iAmSuperAdmin || $canDelete) ? true : false;

				if ($allow)
				{
					$data = $table->getProperties();

					// On before zone delete
					PluginHelper::importPlugin("actionlog");
					Factory::getApplication()->triggerEvent("onBeforeQ2cDeleteZone", array($data));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
					else
					{
						// On after zone delete
						PluginHelper::importPlugin("actionlog");
						Factory::getApplication()->triggerEvent("onAfterQ2cDeleteZone", array($data));
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					Factory::getApplication()->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'), 'warning');
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}
}
