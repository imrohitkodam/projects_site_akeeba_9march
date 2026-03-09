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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Zone controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerZone extends Quick2cartController
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	public function edit()
	{
		$app = Factory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_quick2cart.edit.zone.id');
		$editId     = $app->input->getInt('id', null, 'array');

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_quick2cart.edit.zone.id', $editId);

		// Get the model.
		$model = $this->getModel('Zone', 'quick2cartModel');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId && $previousId !== $editId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=zoneform&layout=default&id='. $editId, false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return    void
	 *
	 * @since    1.6
	 */
	public function publish()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = Factory::getApplication();
		$model = $this->getModel('Zone', 'quick2cartModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// Attempt to save the data.
		$return = $model->publish($data['id'], $data['state']);

		// Check for errors.
		if ($return === false)
		{
			$this->setMessage(Text::sprintf('Save failed', $model->getError()), 'warning');
		}
		else
		{
			// Check in the profile.
			if ($return)
			{
				$model->checkin($return);
			}

			// Clear the profile id from the session.
			$app->setUserState('com_entrusters.edit.bid.id', null);

			// Redirect to the list screen.
			$this->setMessage(Text::_('COM_QUICK2CART_ZONE_DELETED'));
		}

		// Clear the profile id from the session.
		$app->setUserState('com_quick2cart.edit.zone.id', null);

		// Flush the data from the session.
		$app->setUserState('com_quick2cart.edit.zone.data', null);

		// Redirect to the list screen.
		$this->setMessage(Text::_('COM_QUICK2CART_ZONE_DELETED'));
		$menu = $app->getMenu();
		$item = $menu->getActive();

		if (empty($item))
		{
			$item->link = 'index.php?option=com_quick2cart&view=zones';
		}

		$this->setRedirect(Route::_($item->link, false));
	}

	/**
	 * Function used to remove zone
	 *
	 * @return  void
	 *
	 * @since  1.6
	 */
	public function remove()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = Factory::getApplication();
		$model = $this->getModel('Zone', 'quick2cartModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// Attempt to save the data.
		$return = $model->delete($data['id']);

		// Check for errors.
		if ($return === false)
		{
			$this->setMessage(Text::sprintf('Delete failed', $model->getError()), 'warning');
		}
		else
		{
			// Check in the profile.
			if ($return)
			{
				$model->checkin($return);
			}

			// Clear the profile id from the session.
			$app->setUserState('com_quick2cart.edit.zone.id', null);

			// Flush the data from the session.
			$app->setUserState('com_quick2cart.edit.zone.data', null);

			$this->setMessage(Text::_('COM_QUICK2CART_ZONE_DELETED'));
		}

		// Redirect to the list screen.
		$menu = $app->getMenu();
		$item = $menu->getActive();

		// Code  added by sanjivani
		if (empty($item))
		{
			$item->link = 'index.php?option=com_quick2cart&view=zones';
		}

		$this->setRedirect(Route::_($item->link, false));
	}
}
