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
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Quick2cartControllerTaxrates controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerTaxrateForm extends Quick2cartController
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @since	1.6
	 *
	 * @return  void
	 */
	public function edit()
	{
		$app = Factory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_quick2cart.edit.taxrateform.id');
		$editId     = $app->input->getInt('id', null, 'array');

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_quick2cart.edit.taxrateform.id', $editId);

		// Get the model.
		$model = $this->getModel('TaxrateForm', 'Quick2cartModel');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=taxrateform&id=' . $editId, false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return	void
	 *
	 * @since	1.6
	 */
	public function save()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app    = Factory::getApplication();
		$editId = $app->input->getInt('id', null, 'array');
		$task   = $app->input->get('task', null, '');

		// Set the user id for the user to edit in the session.
		$app->setUserState('taxrateform.id', $editId);
		$model = $this->getModel('TaxrateForm', 'Quick2cartModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// Add current id
		$data['id'] = $editId;

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$input = $app->input;
			$jform = $input->get('jform', array(), 'ARRAY');

			// Save the data in the session.
			$app->setUserState('com_quick2cart.edit.taxrate.data', $jform, array());

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_quick2cart.edit.taxrateform.id');
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=taxrateform&layout=edit&id=' . $id, false));

			return false;
		}

		$isNew = (empty($data['id'])) ? true : false;

		// On before tax rate create
		PluginHelper::importPlugin("system");
		PluginHelper::importPlugin("actionlog");
		$app->triggerEvent("onBeforeQ2cSaveTaxRate", array($data, $isNew));

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_quick2cart.edit.taxrate.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_quick2cart.edit.taxrateform.id');
			$this->setMessage(Text::sprintf(Text::_('COM_QUICK2CART_ITEM_SAVED_FAIL'), $model->getError()), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=taxrateform&id=' . $id, false));

			return false;
		}

		$data['id'] = $return;

		// On after tax rate create
		PluginHelper::importPlugin("system");
		PluginHelper::importPlugin("actionlog");
		$app->triggerEvent("onAfterQ2cSaveTaxRate", array($data, $isNew));

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_quick2cart.edit.taxrateform.id', null);

		// Redirect to the list screen.
		$this->setMessage(Text::_('COM_QUICK2CART_ITEM_SAVED_SUCCESSFULLY'));

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=cp');

		if ($task == "save")
		{
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=taxrateform&id=' . $return . '&Itemid=' . $itemid, false));
		}
		else
		{
				// Task= save and close
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=taxrates&Itemid=' . $itemid, false));
		}

		// Flush the data from the session.
		$app->setUserState('com_quick2cart.edit.taxrate.data', null);
	}

	/**
	 * Method to Save and Close.
	 *
	 * @return	void
	 *
	 * @since	1.6
	 */
	public function saveAndClose()
	{
		$this->save();
	}

	/**
	 * Method to Cancel.
	 *
	 * @return	void
	 *
	 * @since	1.6
	 */
	public function cancel()
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$itemid              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=taxrates&Itemid=' . $itemid, false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}

	/**
	 * Method to Remove.
	 *
	 * @return	boolean
	 *
	 * @since	1.6
	 */
	public function remove()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = Factory::getApplication();
		$model = $this->getModel('TaxrateForm', 'Quick2cartModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_quick2cart.edit.taxrate.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_quick2cart.edit.taxrateform.id');
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=taxrate&layout=edit&id=' . $id, false));

			return false;
		}

		// Attempt to save the data.
		$return = $model->delete($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_quick2cart.edit.taxrate.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_quick2cart.edit.taxrateform.id');
			$this->setMessage(Text::sprintf('Delete failed', $model->getError()), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=taxrate&layout=edit&id=' . $id, false));

			return false;
		}

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_quick2cart.edit.taxrateform.id', null);

		// Redirect to the list screen.
		$this->setMessage(Text::_('COM_QUICK2CART_ITEM_DELETED_SUCCESSFULLY'));
		$menu = $app->getMenu();
		$item = $menu->getActive();
		$this->setRedirect(Route::_($item->link, false));

		// Flush the data from the session.
		$app->setUserState('com_quick2cart.edit.taxrate.data', null);
	}
}
