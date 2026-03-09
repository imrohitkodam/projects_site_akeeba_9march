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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Uri\Uri;

/**
 * Attributeset controller class.
 *
 * @since  2.5
 */
class Quick2cartControllerAttributeSetMapping extends FormController
{
	/**
	 * Method to save mapping
	 *
	 * @param   STRING  $key     key
	 *
	 * @param   STRING  $urlVar  url var
	 *
	 * @return null
	 *
	 * @since  2.5
	 */
	public function save($key = null, $urlVar = null)
	{
		$input = Factory::getApplication()->input;
		$task = $input->get('task', '', 'string');
		$model = $this->getModel('attributesetmapping');
		$model->save();

		if ($task == 'apply')
		{
			$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=attributesetmapping");
		}
		else
		{
			$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=dashboard");
		}
	}

	/**
	 * Method to cancel changes
	 *
	 * @param   STRING  $key  key
	 *
	 * @return null
	 *
	 * @since  2.5
	 */
	public function cancel($key = null)
	{
		$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=dashboard");
	}
}
