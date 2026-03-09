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

/**
 * Customer_address controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerCustomer_AddressForm extends FormController
{
	/**
	 * Save
	 *
	 * @param   Integer  $key     Key
	 * @param   String   $urlVar  URLVar
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function save($key = null, $urlVar = null)
	{
		$app     = Factory::getApplication();
		$jinput  = $app->input;
		$userId  = $jinput->get('userid', '', 'INT');
		$model   = $this->getModel('customer_addressform');
		$data    = $jinput->get('jform', '', '');
		$address = '';
		$form    = $model->getForm();
		$form->addRulePath(JPATH_COMPONENT . 'components/com_quick2cart/models/rules');

		if (!$form)
		{
			throw new Exception(Text::_('COM_QUICK2CART_ORDERSUMMERY_PLS_TRY_AGAIN_SOMETHING_WENT_WRONG'), 500);
		}

		// Validate the posted data.
		if (!empty($form))
		{
			$data = $model->validate($form, $data);
		}

		$errors = $model->getErrors();

		if (empty($errors))
		{
			$data['country_code'] = $jinput->get('country_code', '', '');
			$data['state_code']   = $jinput->get('state_code', '', '');
			$data['user_id']      = $userId;
			$address              = $model->save($data);
		}

		echo $address;

		jexit();
	}

	/**
	 * User Address List
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function getUserAddressList()
	{
		$jinput  = Factory::getApplication()->input;
		$uid     = $jinput->get('uid', '0', 'INT');
		$model   = $this->getModel('customer_addressform');
		$address = $model->getUserAddressList($uid);

		echo $address;

		jexit();
	}

	/**
	 * Delete
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function delete()
	{
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$addressId = $jinput->get('addressId', 0, 'INT');
		$model     = $this->getModel('customer_addressform');
		$result    = $model->delete($addressId);

		if ($result == 1)
		{
			echo 1;
		}
		else
		{
			echo 0;
		}

		jexit();
	}
}
