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
 * Quick2cart model.
 *
 * @since  1.6
 */
class Quick2cartModelTaxrate extends AdminModel
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_QUICK2CART';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    An optional associative array of configuration settings.
	 *
	 * @param   string  $prefix  An optional associative array of configuration settings.
	 *
	 * @param   array   $config  An optional associative array of configuration settings.
	 *
	 * @since   2.2
	 * @return   Object
	 */
	public function getTable($type = 'Taxrate', $prefix = 'Quick2cartTable', $config = array())
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
		$form = $this->loadForm('com_quick2cart.taxrate', 'taxrate', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.taxrate.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   OBJECT  $table  The table data.
	 *
	 * @return  null
	 *
	 * @since   2.9.13
	 */
	protected function prepareTable($table)
	{
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__kart_taxrates');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
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
		// On before tax rate save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$isNew      = empty($data['id']) ? true : false;

		Factory::getApplication()->triggerEvent('onBeforeQ2cSaveTaxRate', array($data, $isNew));

		// Store the data.
		if (!parent::save($data))
		{
			return false;
		}

		$data['id'] = (int) $this->getState($this->getName() . '.id');

		// On after tax rate save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		Factory::getApplication()->triggerEvent('onAfterQ2cSaveTaxRate', array($data, $isNew));

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
		$app   = Factory::getApplication();
		$user  = Factory::getUser();
		$table = $this->getTable();
		$pks   = (array) $pks;
		$db    = Factory::getDbo();

		// Check if I am a Super Admin
		$iAmSuperAdmin       = $user->authorise('core.admin');
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;

		// Load tables
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				$zoneTable = Table::getInstance('zone', 'Quick2cartTable', array('dbo', $db));
				$zoneTable->load(array('id' => $table->zone_id));

				$storeOwner = $storeHelper->getStoreOwner($zoneTable->store_id);
				$canDelete  = $comquick2cartHelper->checkOwnership($storeOwner);

				// Super user or else store owner can delete the tax rates
				$allow = ($iAmSuperAdmin || $canDelete) ? true : false;

				if ($allow)
				{
					$data = $table->getProperties();

					// On before tax rate delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onBeforeQ2cDeleteTaxRate", array($data));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
					else
					{
						// On after tax rate delete
						PluginHelper::importPlugin("actionlog");
						$app->triggerEvent("onAfterQ2cDeleteTaxRate", array($data));
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

	/**
	 * Method to validate the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.9.13
	 */
	public function validate($form, $data, $group = null)
	{
		if ($data['percentage'] < 0)
		{
			$this->setError(Text::_('QTC_ENTER_POSITIVE_PERCENTAGE'));

			return false;
		}

		return parent::validate($form, $data, $group);
	}
}
