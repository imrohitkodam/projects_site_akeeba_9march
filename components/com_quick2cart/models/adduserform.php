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
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;

/**
 * Quick2cart model.
 *
 * @since  1.6
 */
class Quick2cartModelAddUserForm extends FormModel
{
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.adduserform', 'adduserform', array('control'   => 'jform','load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function save($data)
	{
		$db     = Factory::getDbo();
		$object = (object) $data;

		// Update their details in the users table using id as the primary key.
		if (!empty($object->id))
		{
			$result = $db->updateObject('#__kart_customer_address', $object, 'id');

			if ($result == 1)
			{
				$msg = Text::_("COM_QUICK2CART_CUSTOMER_ADDRESS_UPDATE_MSG");
			}
		}
		else
		{
			$result     = $db->insertObject('#__kart_customer_address', $object);
			$object->id = $db->insertid();

			if ($result == 1)
			{
				$msg = Text::_("COM_QUICK2CART_CUSTOMER_ADDRESS_ADD_MSG");
			}
		}

		$fieldHtml = $this->getAddress($object->id);

		return $fieldHtml;
	}
}
