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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Payout form controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerPayout extends FormController
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->view_list = 'payouts';
	}

	// @TODO - remove this when jform is used
	/**
	 * function to add
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function add()
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=payout&layout=edit');
	}

	// @TODO - remove this when jform is used
	/**
	 * function to cancel.
	 *
	 * @param   STRING  $key  key
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function cancel($key = null)
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=payouts');
	}

	// @TODO - remove this when jform is used
	/**
	 * function to edit.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function edit($key = null, $urlVar = null)
	{
		$input = Factory::getApplication()->input;
		$cid   = $input->get('cid', array(), 'array');

		ArrayHelper::toInteger($cid);

		$link = 'index.php?option=com_quick2cart&view=payout&layout=edit&id=' . $cid[0];

		if (!count($cid))
		{
			$id   = $input->get('id', '', 'INT');
			$link = 'index.php?option=com_quick2cart&view=payout&layout=edit&id=' . $id;
		}

		$this->setRedirect($link);
	}

	/**
	 * Overrides parent save method.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   3.2
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$task = $this->getTask();

		// Initialise variables.
		$app   = Factory::getApplication();
		$model = $this->getModel('Payout', 'Quick2cartModel');

		// Get the user data.
		$data = $app->input->get->post;

		// Attempt to save the data.
		$return = $model->save($data);
		$id     = $return;

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_quick2cart.edit.payout.data', $data);

			// Tweak *important.
			$app->setUserState('com_quick2cart.edit.payout.id', $data['id']);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_quick2cart.edit.payout.id');
			$this->setMessage(Text::sprintf('COM_QUICK2CART_SAVE_MSG_ERROR', $model->getError()), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&&view=payout&layout=edit&id=' . $id, false));

			return false;
		}

		// Tweak *important.
		$app->setUserState('com_quick2cart.edit.payout.id', $data->get('id', '', 'INT'));

		if ($task === 'apply')
		{
			if (!$id)
			{
				$id = (int) $app->getUserState('com_quick2cart.edit.payout.id');
			}

			$redirect = 'index.php?option=com_quick2cart&task=payout.edit&id=' . $id;
		}
		else
		{
			// Clear the profile id from the session.
			$app->setUserState('com_quick2cart.edit.payout.id', null);

			// Flush the data from the session.
			$app->setUserState('com_quick2cart.edit.payout.data', null);

			// Redirect to the list screen.
			$redirect = Route::_('index.php?option=com_quick2cart&view=payouts', false);
		}

		$msg = Text::_('COM_QUICK2CART_SAVE_SUCCESS');
		$this->setRedirect($redirect, $msg);
	}
}
