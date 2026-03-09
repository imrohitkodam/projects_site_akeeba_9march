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
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

/**
 * Methods Shipping Profile Form.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       1.0
 */
class Quick2cartModelShipprofileform extends FormModel
{
	protected $item = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 * @return  void
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();

		// Load state from the request userState on edit or from the passed variable on default
		if ($app->input->get('layout') == 'edit')
		{
			$id = $app->getUserState('com_quick2cart.edit.shipprofile.id');
		}
		else
		{
			$id = $app->input->get('id');
			$app->setUserState('com_quick2cart.edit.shipprofile.id', $id);
		}

		$this->setState('shipprofile.id', $id);

		// Load the parameters.
		$params       = Factory::getApplication('com_quick2cart')->getParams();
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
	 * @param   integer  $id  The id of the object to get.
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
	 * Method to get an Table.
	 *
	 * @param   String  $type    Type
	 * @param   String  $prefix  Prefix
	 * @param   String  $config  Config
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getTable($type = 'shipprofile', $prefix = 'Quick2cartTable', $config = array())
	{
		$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer  $id  The id of the row to check out.
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
	 * Method to check out an item for editing.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean		True on success, false on failure.
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
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return	JForm	A JForm object on success, false on failure
	 *
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.shipprofile', 'shipprofileform', array(
			'control' => 'jform',
			'load_data' => $loadData)
		);

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
	 * @param   array  $data  The form data.
	 *
	 * @return	mixed		The user id on success, false on failure.
	 *
	 * @since	1.6
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

		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

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
		'select.genericlist', $plugin_options, 'qtcShipPlugin',
		'class="form-select" aria-invalid="false" autocomplete="off" data-chosen="qtc" onchange=\'qtcLoadPlgMethods()\'',
		'value', 'text', $default_val
		);

		return $plugin_list;
	}

	/**
	 * Method to add shipping method.
	 *
	 * @param   Integer  $update  Update state
	 *
	 * @since   2.2
	 *
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

		// Sanitize data
		$filter                  = InputFilter::getInstance();
		$data['qtcShipPluginId'] = $filter->clean($data['qtcShipPluginId'], 'INT');
		$data['shipprofile_id']  = $filter->clean($data['shipprofile_id'], 'INT');
		$data['methodId']        = $filter->clean($data['methodId'], 'INT');

		$qtcshiphelper  = new qtcshiphelper;
		$data['client'] = $qtcshiphelper->getPluginDetail($data['qtcShipPluginId']);

		// Check if user is authorized to add regions to the zone
		JLoader::import('helpers.storeHelper', JPATH_SITE . '/components/com_quick2cart');
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');
		$shipProfileTable = Table::getInstance('shipprofile', 'Quick2cartTable', array('dbo', $db));
		$shipProfileTable->load(array('id' => $data['shipprofile_id']));

		$comquick2cartHelper = new comquick2cartHelper;
		$storeHelper         = new StoreHelper;

		$storeOwner = $storeHelper->getStoreOwner($shipProfileTable->store_id);
		$isOwner    = $comquick2cartHelper->checkOwnership($storeOwner);

		if ($isOwner === false)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$query->where('sm.shipprofile_id=' . $db->escape($data['shipprofile_id']));
		$query->where('sm.client=' . $db->Quote($db->escape($data['client'])));
		$query->where('sm.methodId=' . $db->Quote($db->escape($data['methodId'])));
		$db->setQuery($query);

		try
		{
			$result = $db->loadResult();
		}
		catch (Exception $e)
		{
			throw new Exception(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 500);
		}

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
}
