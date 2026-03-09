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
 * Taxprofiles list controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerTaxprofiles extends Quick2cartController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   String  $name    Name
	 * @param   String  $prefix  Prefix
	 *
	 * @since	1.6
	 *
	 * @return  void
	 */
	public function &getModel($name = 'Taxprofiles', $prefix = 'Quick2cartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method use to delete taxprofile.
	 *
	 * @since	1.6
	 *
	 * @return  void
	 */
	public function delete()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);
		$model        = $this->getModel('taxprofiles');
		$successCount = $model->delete($cid);

		if (!empty($successCount))
		{
			$msg = Text::sprintf('COM_QUICK2CART_S_TAXPROFILE_DELETED_SUCCESSFULLY');
		}
		else
		{
			$msg = Text::_('COM_QUICK2CART_S_TAXPROFILE_ERROR_DELETE') . '</br>' . $model->getError();
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=taxprofiles&Itemid=' . $itemid, false);

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
		$input = Factory::getApplication()->input;
		$cid   = $input->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('taxprofiles');

		if ($model->setItemState($cid, 1))
		{
			$msg = Text::sprintf('COM_QUICK2CART_S_TAXPROFILES_PUBLISH_SUCCESSFULLY', count($cid));
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=taxprofiles&Itemid=' . $itemid, false);
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
		$input = Factory::getApplication()->input;
		$cid   = $input->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('taxprofiles');

		if ($model->setItemState($cid, 0))
		{
			$msg = Text::sprintf(Text::_('COM_QUICK2CART_S_TAXPROFILES_UNPUBLISH_SUCCESSFULLY'), count($cid));
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemId              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect            = Route::_('index.php?option=com_quick2cart&view=taxprofiles&Itemid=' . $itemId, false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}
}
