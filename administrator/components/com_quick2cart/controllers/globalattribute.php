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
 * Attribute controller class.
 *
 * @since  2.5
 */
class Quick2cartControllerGlobalAttribute extends FormController
{
	/**
	 * Constructor
	 *
	 * @since  2.5
	 */
	public function __construct()
	{
		$this->view_list = 'globalattributes';
		parent::__construct();
	}

	/**
	 * Function to dlete options
	 *
	 * @return  model object
	 *
	 * @since  2.5
	 */
	public function deleteoption()
	{
		$globalAttributeModel = $this->getmodel('globalattribute');
		$result               = $globalAttributeModel->deleteoption();
		$c[]                  = array("success" => 'ok');

		if ($result == false)
		{
			$message = Text::_("COM_QUICK2CART_ATTRIBUTE_OPTION_REMOVE_ERROR");
			$c[] = array("error" => $message);
		}

		echo json_encode($c);
		jexit();
	}

	/**
	 * Function to save global_attribute_ids in order in table kart_global_attribute_set
	 *
	 * @param   INT  $key     key
	 *
	 * @param   INT  $urlVar  url
	 *
	 * @return  null
	 *
	 * @since 2.5
	 *
	 * */
	public function save($key = null, $urlVar = null)
	{
		$attributeModel = $this->getModel('globalattribute');
		$input = Factory::getApplication()->input;
		$app          = Factory::getApplication();
		$attId = $input->get('id', '', 'int');
		$data         = $input->get('jform', array(), 'array');
		$form = $attributeModel->getForm();

		$validData   = $attributeModel->validate($form, $data);

		if ($validData === false)
		{
			$errors = $attributeModel->getErrors();

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

			$this->setRedirect('index.php?option=com_quick2cart&view=globalattribute&layout=edit&id=' . $attId);

			return false;
		}

		parent::save($key = null, $urlVar = null);
	}
}
