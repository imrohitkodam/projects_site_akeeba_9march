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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;

/**
 * Attributesets list controller class.
 *
 * @since  2.5
 */
class Quick2cartControllerAttributesets extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   STRING  $name    class name
	 *
	 * @param   STRING  $prefix  model prefix
	 *
	 * @param   STRING  $config  config
	 *
	 * @return  model object
	 *
	 * @since  2.5
	 */
	public function getModel($name = 'attributeset', $prefix = 'Quick2cartModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$app   = Factory::getApplication();
		$input = $app->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		$app->close();
	}

	/**
	 * Function to delete attribute sets
	 *
	 * @return  null
	 *
	 * @since  2.5
	 *
	 * */
	public function delete()
	{
		$attributeSetsModel = $this->getmodel('attributesets');

		if ($attributeSetsModel->delete())
		{
			parent::delete();
		}
	}
}
