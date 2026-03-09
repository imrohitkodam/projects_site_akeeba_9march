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
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

/**
 * Item Model for a zone.
 *
 * @since  2.2
 */
class Quick2cartModelZoneForm extends FormModel
{
	/*$_item = null;*/

	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_QUICK2CART';

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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$comQuick2cartParam = Factory::getApplication('com_quick2cart');
		$app                = Factory::getApplication();

		// Load state from the request userState on edit or from the passed variable on default
		if ($app->input->get('layout') == 'edit')
		{
			$id = $app->getUserState('com_quick2cart.edit.zone.id');
		}
		else
		{
			$id = $app->input->get('id');
			$app->setUserState('com_quick2cart.edit.zone.id', $id);
		}

		$this->setState('zone.id', $id);

		// Load the parameters.
		$params       = $comQuick2cartParam->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('zone.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		if (empty($this->_item) || $this->_item === null)
		{
			$app         = Factory::getApplication();
			$this->_item = false;
			$id          = $app->input->get('id');

			if (empty($id))
			{
				$id = $this->getState('zone.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
				$user    = Factory::getUser();
				$id      = $table->id;
				$canEdit = $user->authorise('core.edit', 'com_quick2cart') || $user->authorise('core.create', 'com_quick2cart');

				if (!$canEdit && $user->authorise('core.edit.own', 'com_quick2cart'))
				{
					$canEdit = $user->id == $table->created_by;
				}

				if (!$canEdit)
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
				}

				if (!empty($table->id))
				{
					// Check if user is authorized to edit/see the zone
					JLoader::import('helpers.storeHelper', JPATH_SITE . '/components/com_quick2cart/');
					JLoader::import('helper', JPATH_SITE . '/components/com_quick2cart/');

					$storeHelper         = new StoreHelper;
					$comquick2cartHelper = new comquick2cartHelper;

					$storeOwner = $storeHelper->getStoreOwner($table->store_id);
					$isOwner    = $comquick2cartHelper->checkOwnership($storeOwner);

					if (!$isOwner)
					{
						throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
					}
				}

				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published)
					{
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->_item = ArrayHelper::toObject($properties, 'JObject');
			}
			elseif ($error = $table->getError())
			{
				$this->setError($error);
			}
		}

		return $this->_item;
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Zone', $prefix = 'quick2cartTable', $config = array())
	{
		$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to checkin a row.
	 *
	 * @param   integer  $id  The numeric id of the primary key.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 * @since   12.2
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('zone.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					$this->setError($table->getError());

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to check-out a row for editing.
	 *
	 * @param   integer  $id  The numeric id of the primary key.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 * @since   12.2
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('zone.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = Factory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					$this->setError($table->getError());

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   2.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.zone', 'zoneform', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.zone.data', array());

		if (empty($data))
		{
			$data = $this->getData();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
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

		$id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('zone.id');
		$state = (!empty($data['state'])) ? 1 : 0;
		$user  = Factory::getUser();

		if ($id)
		{
			// Check the user can edit this item
			$authorised = $user->authorise('core.edit.own', 'com_quick2cart');
		}
		else
		{
			// Check the user can create new items in this section
			$authorised = $user->authorise('core.create', 'com_quick2cart');
		}

		// $authorised = true;

		if ($authorised !== true)
		{
			Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

			return false;
		}

		$table = $this->getTable();

		if ($table->save($data) === true)
		{
			return $table->id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $data  An array of record id to be deleted
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($data)
	{
		$id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('zone.id');

		if (Factory::getUser()->authorise('core.delete', 'com_quick2cart') !== true)
		{
			Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'),'error');

			return false;
		}

		$table = $this->getTable();

		if ($table->delete($data['id']) === true)
		{
			return $id;
		}
		else
		{
			return false;
		}

		return true;
	}

	/**
	 * Gives country list.
	 *
	 * @return  array
	 *
	 * @since   2.2
	 */
	public function getCountry()
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
		$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');
		$rows        = (array) $tjGeoHelper->getCountryList('com_quick2cart');

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
		$app     = Factory::getApplication();
		$zone_id = $app->input->get('id', 0);

		if ($zone_id == 0)
		{
			$zone_id = (int) $app->getUserState('com_quick2cart.edit.zone.id');
		}

		if (!empty($zone_id))
		{
			$db    = Factory::getDbo();
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
	 * Get Zone Rule Details
	 *
	 * @param   int  $rule_id  Rule id
	 *
	 * @return  object
	 */
	public function getZoneRuleDetail($rule_id)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('zr.zonerule_id', 'id'));
		$query->select($db->quoteName('c.country', 'country'));
		$query->select($db->quoteName('c.id', 'country_id'));
		$query->select($db->quoteName('reg.region'));
		$query->select($db->quoteName('reg.id', 'region_id'));
		$query->from($db->quoteName('#__kart_zonerules', 'zr'));
		$query->join('LEFT', $db->quoteName('#__tj_country', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('zr.country_id'));
		$query->join('LEFT', $db->quoteName('#__tj_region', 'reg') . ' ON ' . $db->quoteName('reg.id') . ' = ' . $db->quoteName('zr.region_id'));
		$query->where($db->quoteName('zr.zonerule_id') . ' = ' . (int) $rule_id);
		$query->order($db->quoteName('zr.ordering'));
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get list of regions from given country
	 *
	 * @param   int  $country_id  Country id
	 *
	 * @return  array
	 */
	public function getRegionList($country_id)
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
		$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');
		$rows = $tjGeoHelper->getRegionList($country_id, 'com_quick2cart');

		return $rows;
	}

	/**
	 * This function save country and region/state aginst zone.
	 *
	 * @param   int  $update  Update
	 *
	 * @return   true or false.
	 *
	 * @since	2.2
	 **/
	public function saveZoneRule($update = 0)
	{
		$app   = Factory::getApplication();
		$data  = $app->input->post->get('jform', array(), 'array');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__kart_zonerules AS gr');

		$filter              = InputFilter::getInstance();
		$data['zonerule_id'] = $filter->clean($data['zonerule_id'], 'INT');
		$data['country_id']  = $filter->clean($data['country_id'], 'INT');
		$data['region_id']   = $filter->clean($data['region_id'], 'INT');

		if ($update === 1)
		{
			// Getting zone id from rule id.
			$data['zone_id'] = $this->getZoneId($data['zonerule_id']);
		}

		$data['zone_id'] = $filter->clean($data['zone_id'], 'INT');

		// Check if user is authorized to add regions to the zone
		JLoader::import('helpers.storeHelper', JPATH_SITE . '/components/com_quick2cart');
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');
		$zoneTable = Table::getInstance('zone', 'Quick2cartTable', array('dbo', $db));
		$zoneTable->load(array('id' => $data['zone_id']));

		$comquick2cartHelper = new comquick2cartHelper;
		$storeHelper         = new StoreHelper;

		$storeOwner = $storeHelper->getStoreOwner($zoneTable->store_id);
		$isOwner    = $comquick2cartHelper->checkOwnership($storeOwner);

		if ($isOwner === false)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$query->where('gr.zone_id=' . $db->escape($data['zone_id']));
		$query->where('gr.country_id=' . $db->quote($data['country_id']));
		$query->where('gr.region_id=' . $db->escape($data['region_id']));
		$db->setQuery($query);

		try
		{
			$result = $db->loadResult();
		}
		catch (Exception $e)
		{
			throw new Exception(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 500);
		}

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

		if (!$ZoneRule->store($data))
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
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('zone_id');
		$query->from($db->qn('#__kart_zonerules', 'zr'));
		$query->where($db->qn('zr.zonerule_id') . ' = ' . (int) $ruleId);
		$db->setQuery($query);

		return $db->loadResult();
	}
}
