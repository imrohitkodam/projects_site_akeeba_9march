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
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Shipprofiles list controller class
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerShipprofiles extends Quick2cartController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   String  $name    Name
	 * @param   String  $prefix  Prefix
	 *
	 * @see     JController
	 * @since   1.6
	 *
	 * @return void
	 */
	public function &getModel($name = 'Shipprofiles', $prefix = 'Quick2cartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method use to delete shipprofile.
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function delete()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);
		$model        = $this->getModel('shipprofiles');
		$successCount = $model->delete($cid);
		$msg          = Text::_('COM_QUICK2CART_S_SHIPPROFILE_ERROR_DELETE') . '</br>' . $model->getError();

		if ($successCount)
		{
			$msg = Text::sprintf('COM_QUICK2CART_S_SHIPPROFILE_DELETED_SUCCESSFULLY');
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemId              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=shipprofiles&Itemid=' . $itemId, false);

		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}

	/**
	 * This function publishes taxrate.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function publish ()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('shipprofiles');

		if ($model->setItemState($cid, 1))
		{
			$msg = Text::sprintf('COM_QUICK2CART_S_SHIPPROFILE_PUBLISH_SUCCESSFULLY', count($cid));
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemId = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect = Route::_('index.php?option=com_quick2cart&view=shipprofiles&Itemid=' . $itemId, false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}

	/**
	 * This function unpublishes taxrate.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function unpublish ()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('shipprofiles');

		if ($model->setItemState($cid, 0))
		{
			$msg = Text::sprintf(Text::_('COM_QUICK2CART_S_SHIPPROFILE_UNPUBLISH_SUCCESSFULLY'), count($cid));
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemId              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=shipprofiles&Itemid=' . $itemId, false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}
}
