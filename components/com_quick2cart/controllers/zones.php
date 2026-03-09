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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Zones list controller class.
 *
 * @since  2.2
 */
class Quick2cartControllerZones extends quick2cartController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   STRING  $name    model name
	 * @param   STRING  $prefix  model prefix
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function &getModel($name = 'Zones', $prefix = 'quick2cartModel', $config = array('ignore_request' => true))
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
		$input = Factory::getApplication()->input;
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
		Factory::getApplication()->close();
	}

	/**
	 * Change state of an item.
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function publish()
	{
		$input = Factory::getApplication()->input;
		$cid   = $input->get('cid', '', 'array');

		ArrayHelper::toInteger($cid);
		$model = $this->getModel('zones');

		if ($model->setItemState($cid, 1))
		{
			$count = count($cid);

			if ($count > 1)
			{
				$msg = Text::sprintf(Text::_('COM_QUICK2CART_ZONE_PUBLISHED'), $count);
			}
			else
			{
				$msg = Text::sprintf(Text::_('COM_QUICK2CART_ZONE_PUBLISHED'), $count);
			}
		}
		else
		{
			$msg = $model->getError();
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid              = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=zones');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=zones&Itemid=' . $itemid, false);

		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}

	/**
	 * Change state of an item.
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function unpublish()
	{
		$input = Factory::getApplication()->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('zones');

		if ($model->setItemState($cid, 0))
		{
			$count = count($cid);
			$msg   = Text::sprintf(Text::_('COM_QUICK2CART_ZONE_UNPUBLISHED'), $count);

			if ($count > 1)
			{
				$msg = Text::sprintf(Text::_('COM_QUICK2CART_ZONE_UNPUBLISHED'), $count);
			}
		}
		else
		{
			$msg = $model->getError();
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemId              = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=zones');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=zones&Itemid=' . $itemId, false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}

	/**
	 * Method use when new zone create
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	public function delete()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);

		$model        = $this->getModel('zones');
		$successCount = $model->delete($cid);

		if ($successCount)
		{
			if ($successCount >= 1)
			{
				$msg = Text::sprintf(Text::_('COM_QUICK2CART_ZONE_DELETED'), $successCount);
			}
		}
		else
		{
			$msg = Text::_('COM_QUICK2CART_ZONE_ERROR_DELETE') . '</br>' . $model->getError();
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemId              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=zones&Itemid=' . $itemId, false);

		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}
}
