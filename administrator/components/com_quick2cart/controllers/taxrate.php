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
use Joomla\CMS\MVC\Controller\FormController;

/**
 * Taxrate form controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerTaxrate extends FormController
{
	/**
	 * Class constructor.
	 *
	 * @since   1.6
	 */
	public function __construct()
	{
		$this->view_list = 'taxrates';
		parent::__construct();
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
	 * @since 4.0.1
	 *
	 * */
	public function save($key = null, $urlVar = null)
	{
		$taxRateModel = $this->getModel('taxrate');
		$input = Factory::getApplication()->input;
		$app          = Factory::getApplication();
		$attId = $input->get('id', '', 'int');
		$data         = $input->get('jform', array(), 'array');
		$form = $taxRateModel->getForm();

		$validData   = $taxRateModel->validate($form, $data);

		if ($validData === false)
		{
			$errors = $taxRateModel->getErrors();

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

			$this->setRedirect('index.php?option=com_quick2cart&view=taxrate&layout=edit&id=' . $attId);

			return false;
		}

		parent::save($key = null, $urlVar = null);
	}
}
