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
 * TaxprofileForm Model
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class Quick2cartModelTaxprofileForm extends FormModel
{
	protected $item = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();

		// Load state from the request userState on edit or from the passed variable on default
		if ($app->input->get('layout') == 'edit')
		{
			$id = $app->getUserState('com_quick2cart.edit.taxprofile.id');
		}
		else
		{
			$id = $app->input->get('id');
			$app->setUserState('com_quick2cart.edit.taxprofile.id', $id);
		}

		$this->setState('taxprofile.id', $id);

		// Load the parameters.
		$params       = Factory::getApplication('com_quick2cart')->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('taxprofile.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param   integer  $id  Id
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
				$id = $this->getState('taxprofile.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
				$user = Factory::getUser();
				$id = $table->id;

				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published)
					{
						return $this->item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
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
	 * Method to get table.
	 *
	 * @param   integer  $type    type
	 * @param   integer  $prefix  prefix
	 * @param   integer  $config  config
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getTable($type = 'taxprofile', $prefix = 'Quick2cartTable', $config = array())
	{
		$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer  $id  ID
	 *
	 * @return	boolean		True on success, false on failure.
	 *
	 * @since	1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('taxprofile.id');

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
	 * @return	boolean  True on success, false on failure.
	 *
	 * @since	1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('taxprofile.id');

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
		$form = $this->loadForm('com_quick2cart.taxprofile', 'taxprofileform', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.taxprofile.data', array());

		if (empty($data))
		{
			$data = $this->getData();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  Data
	 *
	 * @return  mixed  The user id on success, false on failure.
	 *
	 * @since	1.6
	 */
	public function save($data)
	{
		$id    = (!empty($data['id'])) ? $data['id'] : '';
		$state = (!empty($data['state'])) ? 1 : 0;
		$db    = Factory::getDBO();

		$obj           = new stdClass;
		$obj->id       = $id;
		$obj->state    = $state;
		$obj->name     = $data['name'];
		$obj->store_id = $data['store_id'];
		$action        = (!empty($obj->id)) ? 'updateObject' : 'insertObject';

		try
		{
			$db->$action('#__kart_taxprofiles', $obj,'id');
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return $obj->id;
	}

	/**
	 * Method to get profiles tax rule(s) detail.
	 *
	 * @param   string  $taxprofile_id  Tax Profile Id.
	 * @param   string  $taxRule_id     Tax rule id.
	 *
	 * @since   2.2
	 *
	 * @return   null object.
	 */
	public function getTaxRules($taxprofile_id='', $taxRule_id='')
	{
		// Load Zone helper.
		$path = JPATH_SITE . "/components/com_quick2cart/helpers/zoneHelper.php";

		if (!class_exists('zoneHelper'))
		{
			JLoader::register('zoneHelper', $path);
			JLoader::load('zoneHelper');
		}

		$zoneHelper = new zoneHelper;

		return  $zoneHelper->getTaxRules($taxprofile_id, $taxRule_id);
	}

	/**
	 * Method to get the users tax rule select box.
	 *
	 * @param   string  $store_id     Store ID
	 * @param   string  $default_val  default value of select box.
	 *
	 * @since   2.2
	 * @return   null object of tax rule select box.
	 */
	public function getTaxRateListSelect($store_id, $default_val='')
	{
		$zoneHelper = new zoneHelper;

		// Get tax rate list
		$taxrates          = $zoneHelper->getUserTaxRateList($store_id);
		$taxrate_options   = array();
		$taxrate_options[] = HTMLHelper::_('select.option', '', Text::_('COM_QUICK2CART_SELECT_TAXRATE'));

		foreach ($taxrates as $item)
		{
			$name              = $item->name . ' (' . floatval($item->percentage) . '%)';
			$taxrate_options[] = HTMLHelper::_('select.option', $item->id, $name);
		}

		$taxrate_list = HTMLHelper::_('select.genericlist', $taxrate_options, 'jform[taxrate_id]', 'class="form-select"', 'value', 'text', $default_val);

		return $taxrate_list;
	}

	/**
	 * Method to get address list to be consider while appling the tax.
	 *
	 * @param   string  $default_val  default value of select box.
	 *
	 * @since   2.2
	 * @return   null object of address select box.
	 */
	public function getAddressList($default_val='')
	{
		$address_options   = array();
		$address_options[] = HTMLHelper::_('select.option', '', Text::_('COM_QUICK2CART_SELECT_ADDRESS'));
		$address_options[] = HTMLHelper::_('select.option', 'shipping', Text::_('COM_QUICK2CART_SHIPPING_ADDRESS'));
		$address_options[] = HTMLHelper::_('select.option', 'billing', Text::_('COM_QUICK2CART_BILLING_ADDRESS'));
		$address_list      = HTMLHelper::_('select.genericlist', $address_options, 'jform[address]', 'class="form-select"', 'value', 'text', $default_val);

		return $address_list;
	}

	/**
	 * Method to add tax rule against tax profile.
	 *
	 * @param   Integer  $update  Update
	 *
	 * @since   2.2
	 * @return   null object of address select box.
	 */
	public function saveTaxRule($update = 0)
	{
		$app  = Factory::getApplication();
		$data = $app->input->post->get('jform', array(), 'array');

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__kart_taxrules AS r');

		// Sanitize data
		$filter             = InputFilter::getInstance();
		$data['taxrule_id'] = $filter->clean($data['taxrule_id'], 'INT');
		$data['taxrate_id'] = $filter->clean($data['taxrate_id'], 'INT');
		$data['address']    = $filter->clean($data['address'], 'STRING');

		if ($update == 1)
		{
			// Getting profile id of tax rule id.
			$taxHelper             = new taxHelper;
			$taxprofile_id         = $taxHelper->getTaxProfileId($data['taxrule_id']);
			$data['taxprofile_id'] = $taxprofile_id;
			$query->where('r.taxrule_id !=' . $db->escape($data['taxrule_id']));
		}

		$data['taxprofile_id'] = $filter->clean($data['taxprofile_id'], 'INT');

		// Check if user is authorized to add regions to the zone
		JLoader::import('helpers.storeHelper', JPATH_SITE . '/components/com_quick2cart');
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');
		$taxProfileTable = Table::getInstance('taxprofile', 'Quick2cartTable', array('dbo', $db));
		$taxProfileTable->load(array('id' => $data['taxprofile_id']));

		$comquick2cartHelper = new comquick2cartHelper;
		$storeHelper         = new StoreHelper;

		$storeOwner = $storeHelper->getStoreOwner($taxProfileTable->store_id);
		$isOwner    = $comquick2cartHelper->checkOwnership($storeOwner);

		if ($isOwner === false)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$query->where('r.taxprofile_id=' . $db->escape($data['taxprofile_id']));
		$query->where('r.taxrate_id=' . $db->escape($data['taxrate_id']));
		$query->where('r.address=' . $db->Quote($db->escape($data['address'])));

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
			$this->setError(Text::_('COM_QUICK2CART_TAXRULE_ALREADY_EXISTS'));

			return false;
		}

		$taxRule = $this->getTable('Taxrules');

		if (!$taxRule->bind($data))
		{
			$this->setError($taxRule->getError());

			return false;
		}

		if (!$taxRule->check())
		{
			$this->setError($taxRule->getError());

			return false;
		}

		if (!$taxRule->store())
		{
			$this->setError($taxRule->getError());

			return false;
		}

		$app->input->set('taxrule_id', $taxRule->taxrule_id);

		return true;
	}
}
