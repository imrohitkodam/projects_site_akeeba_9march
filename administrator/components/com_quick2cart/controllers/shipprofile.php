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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Taxprofile controller class.
 *
 * @since  2.2
 */
class Quick2cartControllerShipprofile extends FormController
{
	/**
	 * This function unpublishes ship profile.
	 *
	 * @param   ARRAY  $config  config
	 *
	 * @since   2.2
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);
	}

	/**
	 * This function unpublishes ship profile.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function unpublish()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('shipprofile');

		if ($model->setItemState($cid, 0))
		{
			$msg = Text::sprintf(Text::_('COM_QUICK2CART_S_SHIPPROFILE_UNPUBLISH_SUCCESSFULLY'), count($cid));
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=shipprofiles&Itemid=' . $itemid, false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}

	/**
	 * This function publishes ship profile.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function publish()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('shipprofile');

		if ($model->setItemState($cid, 1))
		{
			$msg = Text::sprintf('COM_QUICK2CART_S_SHIPPROFILE_PUBLISH_SUCCESSFULLY', count($cid));
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect = Route::_('index.php?option=com_quick2cart&view=shipprofiles&Itemid=' . $itemid, false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}

	/**
	 * Method use to delete shipprofile.
	 *
	 * @since	2.2
	 * @return  null
	 */
	public function delete()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('shipprofile');
		$successCount = $model->delete($cid);

		if ($successCount)
		{
			$msg = Text::sprintf('COM_QUICK2CART_S_SHIPPROFILE_DELETED_SUCCESSFULLY');
			$app->enqueueMessage($msg);
		}
		else
		{
			$msg = Text::_('COM_QUICK2CART_S_SHIPPROFILE_ERROR_DELETE') . '</br>' . $model->getError();
			$app->enqueueMessage($msg, 'error');
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect = Route::_('index.php?option=com_quick2cart&view=shipprofiles&Itemid=' . $itemid, false);
		$this->setRedirect($redirect);
	}

	/**
	 * Function to add shipping profile
	 *
	 * @since	1.6
	 *
	 * @return  null
	 */
	public function add()
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Set the user id for the user.
		$app->setUserState('com_quick2cart.edit.shipprofile.id', null);

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=shipprofile&layout=edit', false));
	}

	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @param   STRING  $key     key
	 * @param   STRING  $urlVar  url var
	 *
	 * @since	1.6
	 *
	 * @return  null
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app    = Factory::getApplication();
		$jinput = Factory::getApplication()->input;

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_quick2cart.edit.shipprofile.id');
		$editId     = $jinput->getInt('id', null);

		if (empty($editId))
		{
			$cids = $jinput->post->get('cid', array(), 'ARRAY');

			if (!empty($cids[0]))
			{
				$editId = $cids[0];
			}
		}

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_quick2cart.edit.shipprofile.id', $editId);

		// Get the model.
		$model = $this->getModel('shipprofile', 'Quick2cartModel');

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
		$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=shipprofile&layout=edit&id=' . $editId, false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @param   STRING  $key     key
	 * @param   STRING  $urlVar  url var
	 *
	 * @return	void
	 *
	 * @since	1.6
	 */
	public function  save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Initialise variables.		
		$editId = $app->input->getInt('id', null, 'array');
		$task   = $app->input->get('task', null, '');
		$model  = $this->getModel('shipprofile', 'Quick2cartModel');

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
			$app->setUserState('com_quick2cart.edit.shipprofile.data', $jform, array());

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_quick2cart.edit.shipprofile.id');
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=shipprofile&layout=edit&id=' . $id, false));

			return false;
		}

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_quick2cart.edit.shipprofile.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_quick2cart.edit.shipprofile.id');
			$this->setMessage($model->getError(), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=shipprofile&layout=edit&id=' . $id, false));

			return false;
		}

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_quick2cart.edit.shipprofile.id', null);

		// Redirect to the list screen.
		$this->setMessage(Text::_('COM_QUICK2CART_ITEM_SAVED_SUCCESSFULLY'));

		if ($task == "save")
		{
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=shipprofile&id=' . $return, false));
		}
		else
		{
			$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=shipprofiles&id=' . $return, false));
		}

		// Flush the data from the session.
		$app->setUserState('com_quick2cart.edit.shipprofile.data', null);
	}

	/**
	 * Function to save and close
	 *
	 * @since   2.2
	 * @return   null Json response object.
	 */
	public function saveAndClose()
	{
		$this->save();
	}

	/**
	 * Method to add shipping method.
	 *
	 * @since   2.2
	 * @return   null Json response object.
	 */
	public function addShipMethod()
	{
		$app   = Factory::getApplication();
		$model = $this->getModel('shipprofile', 'Quick2cartModel');

		$response = array();
		$response['error'] = 0;

		if (!$model->addShippingPlgMeth())
		{
			$response['error'] = 1;
			$response['errorMessage'] = $model->getError();
		}

		if ($response['error'] == 0)
		{
			$response['shipProfileMethodId'] = $app->input->get('shipMethodId');
		}

		echo json_encode($response);

		$app->close();
	}

	/**
	 * This function delete the tax rule form perticular profile.
	 *
	 * @since	2.2
	 *
	 * @return  null
	 */
	public function deleteShipProfileMethod()
	{
		$app       = Factory::getApplication();
		$data      = $app->input->post->get('jform', array(), 'array');
		$model     = $this->getModel('shipprofile', 'Quick2cartModel');
		$ruleTable = $model->getTable('Shipmethods');
		$response  = array();
		$response['error'] = 0;

		if (!$ruleTable->delete($data['shipMethodId']))
		{
			$response['error'] = 1;
			$response['errorMessage'] = $ruleTable->getError();
		}

		echo json_encode($response);
		$app->close();
	}

	/**
	 * This function Update shipping profile method associated with shipprofile.
	 *
	 * @since	2.2
	 *
	 * @return null
	 */
	public function updateShipMethod()
	{
		$app      = Factory::getApplication();
		$data     = $app->input->post->get('jform', array(), 'array');
		$model    = $this->getModel('shipprofile', 'Quick2cartModel');
		$response = array();
		$response['error'] = 0;

		if (!$model->addShippingPlgMeth(1))
		{
			$response['error'] = 1;
			$response['errorMessage'] = $model->getError();
		}

		if ($response['error'] == 0)
		{
			$response['shipProfileMethodId'] = $app->input->get('shipMethodId');
		}

		echo json_encode($response);
		$app->close();
	}

	/**
	 * Method to give Plugins shipping method.
	 *
	 * @since   2.2
	 * @return   Json Plugin shipping methods list.
	 */
	public function qtcLoadPlgMethods()
	{
		$app           = Factory::getApplication();
		$extension_id  = $app->input->post->get('qtcShipPluginId', 0, "INTEGER");
		$store_id      = $app->input->post->get('store_id', 0, "INTEGER");
		$qtcshiphelper = new qtcshiphelper;
		$response      = $qtcshiphelper->qtcLoadShipPlgMethods($extension_id, $store_id);
		echo json_encode($response);

		$app->close();
	}
}
