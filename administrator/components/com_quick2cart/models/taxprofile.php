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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Quick2cart model.
 *
 * @since  2.2
 */
class Quick2cartModelTaxprofile extends AdminModel
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
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
	public function getTable($type = 'Taxprofile', $prefix = 'Quick2cartTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 *
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return	JForm	A JForm object on success, false on failure
	 *
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.taxprofile', 'taxprofile', array('control' => 'jform', 'load_data' => $loadData));

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
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.taxprofile.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
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
				$db->setQuery('SELECT MAX(ordering) FROM #__kart_taxprofiles');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Method to get the users tax rule select box.
	 *
	 * @param   INT     $store_id     default value of select box.
	 * @param   string  $default_val  default value of select box.
	 *
	 * @since   2.2
	 * @return   null object of tax rule select box.
	 */
	public function getTaxRateListSelect($store_id,$default_val='')
	{
		// Get tax rate list
		$zoneHelper        = new zoneHelper;
		$taxrates          = $zoneHelper->getUserTaxRateList($store_id);
		$taxrate_options   = array();
		$taxrate_options[] = HTMLHelper::_('select.option', '', Text::_('COM_QUICK2CART_SELECT_TAXRATE'));

		foreach ($taxrates as $item)
		{
			$name              = $item->name . ' (' . floatval($item->percentage) . "%)";
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
	 * @param   INT  $update  update flag
	 *
	 * @since   2.2
	 * @return   null object of address select box.
	 */
	public function saveTaxRule($update = 0)
	{
		$app = Factory::getApplication();
		$data = $app->input->post->get('jform', array(), 'array');

		$db		= Factory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__kart_taxrules AS r');

		if ($update == 1)
		{
			// Getting profile id of tax rule id.
			$taxHelper = new taxHelper;
			$taxprofile_id = $taxHelper->getTaxProfileId($data['taxrule_id']);
			$data['taxprofile_id'] = $taxprofile_id;
			$query->where('r.taxrule_id !=' . $db->escape($data['taxrule_id']));
		}

		$query->where('r.taxprofile_id=' . $db->escape($data['taxprofile_id']));
		$query->where('r.taxrate_id=' . $db->escape($data['taxrate_id']));
		$query->where('r.address=' . $db->Quote($db->escape($data['address'])));

		$db->setQuery($query);
		$result = $db->loadResult();

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

	/**
	 * Method to get profiles tax rule(s) detail.
	 *
	 * @param   string  $taxprofile_id  Tax profile id.
	 * @param   string  $taxRule_id     Tax rule id.
	 *
	 * @since   2.2
	 * @return   null object.
	 */
	public function getTaxRules($taxprofile_id='', $taxRule_id='')
	{
		$zoneHelper = new zoneHelper;

		return  $zoneHelper->getTaxRules($taxprofile_id, $taxRule_id);
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
		$app   = Factory::getApplication();

		// On before tax profile save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$isNew      = empty($data['id']) ? true : false;
		$app->triggerEvent('onBeforeQ2cSaveTaxProfile', array($data, $isNew));

		// Store the data.
		if (!parent::save($data))
		{
			return false;
		}

		$data['id'] = (int) $this->getState($this->getName() . '.id');

		// On after tax profile save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$app->triggerEvent('onAfterQ2cSaveTaxProfile', array($data, $isNew));

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
		$app   = Factory::getApplication();

		// Check if I am a Super Admin
		$iAmSuperAdmin = $user->authorise('core.admin');

		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;

		// Load tables
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				$storeOwner = $storeHelper->getStoreOwner($table->store_id);
				$canDelete  = $comquick2cartHelper->checkOwnership($storeOwner);

				// Super user or else store owner can delete the tax profiles
				$allow = ($iAmSuperAdmin || $canDelete) ? true : false;

				if ($allow)
				{
					$data = $table->getProperties();

					// On before tax profile delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onBeforeQ2cDeleteTaxProfile", array($data));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
					else
					{
						// On after tax profile delete
						PluginHelper::importPlugin("actionlog");
						$app->triggerEvent("onAfterQ2cDeleteTaxProfile", array($data));
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$app->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'), 'warning');
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
