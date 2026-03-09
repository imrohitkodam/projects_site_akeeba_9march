<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Item Model for an Store.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelStore extends AdminModel
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_QUICK2CART';

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Store', $prefix = 'Quick2cartTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional ordering field.
	 * @param   boolean  $loadData  An optional direction (asc|desc).
	 *
	 * @return  JForm    $form      A JForm object on success, false on failure
	 *
	 * @since   2.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.store', 'store', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	$data  The data for the form.
	 *
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.store.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  $item  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__kart_store');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Method to delete rows.
	 *
	 * @param   array  &$pks  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   2.9.14
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
				$storeOwner = $storeHelper->getStoreOwner($pk);
				$canDelete  = $comquick2cartHelper->checkOwnership($storeOwner);

				// Super user or else store owner can delete the stores
				$allow = ($iAmSuperAdmin || $canDelete) ? true : false;

				if ($allow)
				{
					$storeTable = Table::getInstance('Store', 'Quick2cartTable', array('dbo', $db));
					$storeTable->load(array('id' => $pk));

					$data = $storeTable->getProperties();

					// On before store delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onBeforeQ2cDeleteStore", array($data));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
					else
					{
						// On after store delete
						PluginHelper::importPlugin("actionlog");
						$app->triggerEvent("onAfterQ2cDeleteStore", array($data));
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
