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
 * Attributeset controller class.
 *
 * @since  2.5
 */
class Quick2cartControllerAttributeset extends FormController
{
	/**
	 * Constructor.
	 *
	 * @since   2.5
	 */
	public function __construct()
	{
		$this->view_list = 'attributesets';
		parent::__construct();
	}

	/**
	 * Method to add attribute.
	 *
	 * @return null
	 *
	 * @since  2.5
	 */
	public function addAttribute()
	{
		$attributeSetModel = $this->getModel('attributeset');
		$attributeSetModel->addAttribute();
	}

	/**
	 * Method to remove attribute
	 *
	 * @return null
	 *
	 * @since  2.5
	 */
	public function removeAttribute()
	{
		$attributeSetModel = $this->getModel('attributeset');
		$count = $attributeSetModel->removeAttribute();

		if (!empty($count))
		{
			$message = sprintf(Text::_("COM_QUICK2CART_ATTRIBUTE_REMOVE_ERROR"), implode(',', $count));
			$c[] = array("error" => $message);
		}
		else
		{
			$c[] = array("success" => 'ok');
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
		$attributeSetModel = $this->getModel('attributeset');
		$input = Factory::getApplication()->input;
		$app          = Factory::getApplication();
		$attId = $input->get('id', '', 'int');
		$attributeData = $input->get('attributes', '', 'array');
		$data         = $input->get('jform', array(), 'array');
		$form         = $attributeSetModel->getForm();

		$validData   = $attributeSetModel->validate($form, $data);

		if ($validData === false)
		{
			$errors = $attributeSetModel->getErrors();

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

			$this->setRedirect('index.php?option=com_quick2cart&view=attributeset&layout=edit&id=' . $attId);

			return false;
		}

		if (!empty($attributeData))
		{
			$result = $attributeSetModel->saveOrdering($attributeData);
		}

		if ($result === false)
		{
			$this->setRedirect('index.php?option=com_quick2cart&view=attributeset&layout=edit&id=' . $attId);

			return false;
		}

		parent::save($key = null, $urlVar = null);
	}
}
