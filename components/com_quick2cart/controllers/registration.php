<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Registration Controller class.
 *
 * @package  Quick2cart
 * @since    2.7
 */
class Quick2cartControllerregistration extends BaseController
{
	/**
	 * Save
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function save()
	{
		$jinput  = Factory::getApplication()->input;
		$model   = $this->getModel('registration');
		$session = Factory::getSession();

		// Get data from request
		$post             = $jinput->get('post');
		$socialadsbackurl = $session->get('socialadsbackurl');

		// Let the model save it
		$result           = $model->store($post);

		if ($result)
		{
			$message = Text::_('REGIS_USER_CREATE_MSG');
			$itemid  = $jinput->get('Itemid');
			$user    = Factory::getuser();
			$cart    = $session->get('cart_temp');
			$session->set('cart' . $user->id, $cart);
			$session->clear('cart_temp');
			$this->setRedirect($socialadsbackurl, $message);
		}
		else
		{
			$message = $jinput->get('message', '', 'STRING');
			$itemid  = $jinput->get('Itemid');
			$this->setRedirect('index.php?option=com_quick2cart&view=registration&Itemid=' . $itemid, $message);
		}
	}

	/**
	 * Cancel
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function cancel()
	{
		$msg    = Text::_('Operation Cancelled');
		$this->setRedirect('index.php', $msg);
	}

	/**
	 * Login
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function login()
	{
		$app      = Factory::getApplication();
		$jinput   = $app->input;
		$pass     = $jinput->get('qtc_password');
		$username = $jinput->get('login_user_name');
		$itemId   = $jinput->get('Itemid');
		$status   = $app->login(
				array(
					'username' => $username,
					'password' => $pass
				),
				array(
					'silent' => true
				)
			);

		if ($status)
		{
			$app->redirect(Route::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemId, false));
		}
		else
		{
			$massage = Text::_('Q2C_LOGIN_FAIL');
			$app->redirect(Route::_('index.php?option=com_quick2cart&view=registration&Itemid=' . $itemId, $massage, 'alert'));
		}
	}

	/**
	 * Login
	 *
	 * @param   array    $credentials  Login detail array
	 * @param   boolean  $remember     whether to remember or not
	 * @param   string   $return       Return URL
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function userLogin($credentials, $remember = true, $return = '')
	{
		$app = Factory::getApplication();

		if (strpos($return, 'http') !== false && strpos($return, Uri::base()) !== 0)
		{
			$return = '';
		}

		$options             = array();
		$options['remember'] = (boolean) $remember;
		$success             = $app->login($credentials);

		if ($return)
		{
			$app->redirect($return);
		}

		return $success;
	}

	/**
	 * Guest_checkout
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function guest_checkout()
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$itemid = $jinput->get('Itemid');	
		$app->redirect(Route::_('index.php?option=com_quick2cart&view=cartcheckout&guestckout=1&Itemid=' . $itemid, false));
	}

	/**
	 * For one page checkout As it is copied from sagar file
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function login_validate()
	{
		$app          = Factory::getApplication();
		$input        = $app->input;
		$user         = Factory::getUser();
		$itemId       = $input->get('Itemid');
		$redirect_url = Route::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemId, false);
		$json         = array();

		if ($user->id)
		{
			$json['redirect'] = $redirect_url;
		}

		if (!$json)
		{
			$userLoginDetail['username'] = $input->get('email', '', 'STRING');
			$userLoginDetail['password'] = $input->get('password', '', 'STRING');
			$status                      = $app->login($userLoginDetail, array('silent' => true));

			// Now login the user
			if (empty($status))
			{
				// If not logged in then show error msg.
				$json['error']['warning'] = Text::_('COM_QUICK2CART_ERROR_LOGIN');
			}
		}

		$json['redirect'] = $redirect_url;

		echo json_encode($json);
		$app->close();
	}

	/**
	 * Get New User data.
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function newUser()
	{
		$app    = Factory::getApplication();
		$input  = $app->input;
		$post   = $input->post->getArray();
		$model  = $this->getModel('registration');
		$result = $model->newUser($post);
		echo json_encode($result);
		jexit();
	}
}
