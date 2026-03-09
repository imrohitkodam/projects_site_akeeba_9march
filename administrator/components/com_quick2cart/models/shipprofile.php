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
defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

/**
 * Shipprofile Model.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelShipprofile extends AdminModel
{
	// Changed by Deepa
	/*protected $_item = null;*/
	protected $item = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$app = Factory::getApplication('com_quick2cart');

		// Load state from the request userState on edit or from the passed variable on default
		if ($app->input->get('layout') == 'edit')
		{
			$id = $app->getUserState('com_quick2cart.edit.shipprofile.id');

			if (!isset($id))
			{
				$id = $app->input->get('id');
			}
		}
		else
		{
			$id = $app->input->get('id');
			$app->setUserState('com_quick2cart.edit.shipprofile.id', $id);
		}

		$this->setState('shipprofile.id', $id);

		// Load the parameters.
		$params       = ComponentHelper::getParams('com_quick2cart');
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('shipprofile.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param   Integer  $id  Id
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		if ($this->item === null)
		{
			$this->item = false;

			if (empty($id))
			{
				$id = $this->getState('shipprofile.id');
			}
			// Get a level row instance.
			$table = $this->getTable();
			/*$this->getTable('shipprofile');*/
			// Attempt to load the row.
			if ($table->load($id))
			{
				$user = Factory::getUser();
				$id   = $table->id;

				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published)
					{
						return $this->item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->item = ArrayHelper::toObject($properties, 'JObject');
			}
			elseif ($error = $table->getError())
			{
				$this->setError($error);
			}
		}

		return $this->item;
	}

	/**
	 * Method Get table.
	 *
	 * @param   String  $type    Type
	 * @param   String  $prefix  Prefix
	 * @param   String  $config  Config
	 *
	 * @return	Object
	 */
	public function getTable($type = 'shipprofile', $prefix = 'Quick2cartTable', $config = array())
	{
		$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   Integer  $id  Id
	 *
	 * @return	boolean		True on success, false on failure.
	 *
	 * @since	1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('shipprofile.id');

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
	 * Method to check out an item.
	 *
	 * @param   Integer  $id  Id
	 *
	 * @return	boolean		True on success, false on failure.
	 *
	 * @since	1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('shipprofile.id');

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
	 * Method to get the profile form The base form is loaded from XML.
	 *
	 * @param   Array    $data      An optional array of data for the form to interogate.
	 * @param   Boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return	JForm	A JForm object on success, false on failure
	 *
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.shipprofile', 'shipprofile', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.shipprofile.data', array());

		if (empty($data))
		{
			$data = $this->getData();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   Array  $data  Data
	 *
	 * @since	1.6
	 *
	 * @return id
	 */
	public function save($data)
	{
		$app   = Factory::getApplication();
		$table = $this->getTable('shipprofile');
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__kart_shipprofile');

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
			$this->setError(Text::_("COM_QUICK2CART_SHIPPROFILE_ALREADY_EXISTS"));

			return false;
		}


		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// On before shipping profile save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$isNew      = empty($data['id']) ? true : false;
		$app->triggerEvent('onBeforeQ2cSaveShippingProfile', array($data, $isNew));

		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		$data['id'] = (int) $table->id;

		// On after shipping profile save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$app->triggerEvent('onAfterQ2cSaveShippingProfile', array($data, $isNew));
		$app->input->set('shipprofileId', $table->id);

		return $table->id;
	}

	/**
	 * Method to get ship profiles method(s) detail.
	 *
	 * @param   string  $shipprofile_id  ship profile id id.
	 * @param   string  $methodId        Tax rule id.
	 *
	 * @since   2.2
	 * @return   object list.
	 */
	public function getShipMethods($shipprofile_id = '', $methodId = '')
	{
		$qtcshiphelper = new qtcshiphelper;

		return $qtcshiphelper->getShipMethods($shipprofile_id, $methodId);
	}

	/**
	 * Method to get shipping plugin select box.
	 *
	 * @param   string  $default_val  default value of select box.
	 *
	 * @since   2.2
	 * @return   null object of shipping plugin select box.
	 */
	public function getShipPluginListSelect($default_val = '')
	{
		// Get tax rate list
		$plugins          = qtcshiphelper::getPlugins();
		$plugin_options   = array();
		$plugin_options[] = HTMLHelper::_('select.option', '', Text::_("COM_QUICK2CART_SELECT_SHIPPLUGIN"));

		foreach ($plugins as $item)
		{
			$plugin_options[] = HTMLHelper::_('select.option', $item->extension_id, $item->name);
		}

		$plugin_list = HTMLHelper::_(
		'select.genericlist', $plugin_options, 'qtcShipPlugin', 'class="form-select" aria-invalid="false" size="1"
		autocomplete="off" data-chosen="qtc" onchange=\'qtcLoadPlgMethods()\'',
		'value', 'text', $default_val
		);

		return $plugin_list;
	}

	/**
	 * Method to add shipping method.
	 *
	 * @param   Integer  $update  Update
	 *
	 * @since   2.2
	 * @return   null object of shipping method select box.
	 */
	public function addShippingPlgMeth($update = 0)
	{
		$app  = Factory::getApplication();
		$data = $app->input->post->get('jform', array(), 'array');

		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__kart_shipProfileMethods AS sm');

		if ($update == 1)
		{
			$data['id'] = $data['qtc_shipProfileMethodId'];
			$query->where('sm.id !=' . $db->escape($data['qtc_shipProfileMethodId']));
		}

		$qtcshiphelper  = new qtcshiphelper;
		$data['client'] = $qtcshiphelper->getPluginDetail($data['qtcShipPluginId']);
		$query->where('sm.shipprofile_id=' . $db->escape($data['shipprofile_id']));
		$query->where('sm.client=' . $db->Quote($db->escape($data['client'])));
		$query->where('sm.methodId=' . $db->Quote($db->escape($data['methodId'])));
		$db->setQuery($query);
		$result = $db->loadResult();

		if (!empty($result))
		{
			$this->setError(Text::_("COM_QUICK2CART_SHIPMETHOD_ALREADY_EXISTS"));

			return false;
		}

		$table = $this->getTable('Shipmethods');

		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		$app->input->set('shipMethodId', $table->id);

		return true;
	}

	/**
	 * Method Delete
	 *
	 * @param   String  &$items  Reference Address of items
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function delete(&$items)
	{
		$user  = Factory::getUser();
		$db    = Factory::getDBO();
		$count = 0;
		$ids   = '';
		$app   = Factory::getApplication();

		if (is_array($items) && !empty($items))
		{
			foreach ($items as $id)
			{
				$table  = $this->getTable();
				$table->load($id);
				$data = $table->getProperties();

				try
				{
					// On before shipping profile delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onBeforeQ2cDeleteShippingProfile", array($data));

					$status = $table->delete($id);
				}
				catch (RuntimeException $e)
				{
					$this->setError($e->getMessage());

					return 0;
				}

				if ($status)
				{
					// On after shipping profile delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onAfterQ2cDeleteShippingProfile", array($data));

					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * This function manage items published or unpublished state
	 *
	 * @param   Array   $items  Items
	 * @param   String  $state  1 for publish and 0 for unpublish.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function setItemState($items, $state)
	{
		$db = Factory::getDBO();

		if (is_array($items))
		{
			$taxreate_ids = implode(',', $items);
			$db           = Factory::getDbo();
			$query        = $db->getQuery(true);

			// Fields to update.
			$fields = array($db->quoteName('state') . ' =' . $state);

			// Conditions for which records should be updated.
			$conditions = array($db->quoteName('id') . '  IN (' . $taxreate_ids . ')');

			$query->update($db->quoteName('#__kart_shipprofile'))->set($fields)->where($conditions);
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
		}

		return true;
	}
}
