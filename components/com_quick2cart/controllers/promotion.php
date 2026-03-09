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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

/**
 * Promotion controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerPromotion extends FormController
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'promotions';
		parent::__construct();
	}

	/**
	 * Function to delete promotion code
	 *
	 * @return true/false
	 *
	 * @since  2.5
	 *
	 * */
	public function qtc_delete_promotion_condition()
	{
		$promotionModel = $this->getModel('promotion');
		$input          = Factory::getApplication()->input;
		$conditionId    = $input->get('cid', '', 'int');

		if (!empty($conditionId))
		{
			$result = $promotionModel->qtc_delete_promotion_condition($conditionId);

			if ($result == false)
			{
				$message  = Text::_("COM_QUICK2CART_PROMOTION_CONDITION_REMOVE_ERROR");
				$status[] = array("error" => $message);
			}
			else
			{
				$status[] = array("success" => 'ok');
			}

			echo json_encode($status);
		}

		jexit();
	}

	/**
	 * Function to save promotion rule
	 *
	 * @param   INT     $key     key
	 * @param   STRING  $urlVar  url var
	 *
	 * @return true/false
	 *
	 * @since  2.8
	 *
	 * */
	public function save($key = null, $urlVar = null)
	{
		Session::checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
		$app          = Factory::getApplication();
		$model        = $this->getModel('Promotion', 'Quick2cartModel');
		$input        = $app->input;
		$data         = $input->get('jform', array(), 'array');
		$allJformData = $data;
		$form         = $model->getForm();

		// Redirect to the list screen.
		$link = 'index.php?option=com_quick2cart&view=promotions&layout=default';

		$comquick2cartHelper = new comquick2cartHelper;
		$promotionsItemId   = $comquick2cartHelper->getitemid($link);
		$link .= $link . "&Itemid=" . $promotionsItemId;

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		$validData   = $model->validate($form, $data);

		if ($validData === false)
		{
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
					$app->enqueueMessage($errors[$i], 'error');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_quick2cart.edit.promotion.data', $allJformData);

			// Tweak *important
			$app->setUserState('com_quick2cart.edit.promotion.id', $allJformData['id']);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_quick2cart.edit.promotion.id');

			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=promotion&layout=default&id=' . $id . '&Itemid=' . $promotionsItemId, false));

			return false;
		}

		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_quick2cart.edit.promotion.data', $allJformData);

			$id = $app->getUserState('com_quick2cart.edit.promotion.data.id');
			$this->setMessage(Text::_('COM_QUICK2CART_SOMETHING_WENT_WRONG'), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=promotion&layout=default&id=' . $id . '&Itemid=' . $promotionsItemId, false));

			return false;
		}

		// Clear the profile id from the session.
		$app->setUserState('com_quick2cart.edit.promotion.id', null);

		// Check this function
		$msg      = Text::_('COM_QUICK2CARET_MSG_SUCCESS_SAVE_PROMOTION');
		$isAdmin = $app->isClient('administrator');

		if ($isAdmin)
		{
			$task = $input->get('task');
			if ($task == 'save')
			{
				$redirect = Route::_('index.php?option=com_quick2cart&view=promotions&layout=default', false);
			}
			else 
			{
				$id         = (int) $model->getState($model->getName() . '.id');
				$redirect = Route::_('index.php?option=com_quick2cart&view=promotion&layout=default&id=' . $id, false);
			}
			$app->enqueueMessage($msg, 'success');
			$app->redirect($redirect);
		}
		else
		{
			$redirect = Route::_($link);
			$this->setRedirect($redirect, $msg);
		}

		// Flush the data from the session.
		$app->setUserState('com_quick2cart.edit.promotion.data', null);
	}

	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return void
	 *
	 * @since    2.9
	 */
	public function add()
	{
		$input = Factory::getApplication()->input;
		$input->set('view', 'promotion');
		$input->set('layout', 'default');

		parent::add();
	}

	/**
	 * Method Cancel.
	 *
	 * @return	void
	 *
	 * @since	1.6
	 */
	public function cancel($key = null)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$itemId              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=promotions');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=promotions&Itemid=' . $itemId, false);
		$this->setRedirect($redirect);
	}

	/**
	 * Find the auto suggestion according the db. @TODO - remove this when jform is used
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the primary key of the URL variable.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function edit($key = null, $urlVar = null)
	{
		$input = Factory::getApplication()->input;

		// Get some variables from the request
		$cid = $input->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);

		$comquick2cartHelper = new comquick2cartHelper;
		$itemId              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=promotions');

		if (!count($cid))
		{
			$id   = $input->get('id', '', 'INT');
			$link = 'index.php?option=com_quick2cart&view=promotion&id=' . $id;
		}
		else
		{
			$link = 'index.php?option=com_quick2cart&view=promotion&id=' . $cid[0];
		}

		$link = Route::_($link . '&Itemid=' . $itemId, false);

		$this->setRedirect($link);
	}
}
