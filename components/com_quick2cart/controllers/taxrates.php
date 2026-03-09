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
 * Quick2cartControllerTaxrates Taxrates list controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerTaxrates extends quick2cartController
{
	/**
	 * Method Delete.
	 *
	 * @param   String  $name    Name
	 * @param   String  $prefix  Prefix
	 *
	 * @since   2.2
	 * @return   void
	 */
	public function &getModel($name = 'Taxrates', $prefix = 'Quick2cartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method Add.
	 *
	 * @since   2.2
	 * @return   void
	 */
	public function add()
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$itemid              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=taxrateform&Itemid=' . $itemid, false));
	}

	/**
	 * Method Delete.
	 *
	 * @since   2.2
	 * @return   void
	 */
	public function delete()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$cid   = $input->get('cid', '', 'array');
		$model = $this->getModel('taxrates');
		$comquick2cartHelper = new comquick2cartHelper;

		// Delete the items.
		try
		{
			ArrayHelper::toInteger($cid);
			$successCount = $model->delete($cid);
			$msg = Text::_('COM_QUICK2CART_S_TAXRATES_ERROR_DELETE') . '</br>' . $model->getError();

			if ($successCount)
			{
				$msg = Text::plural('COM_QUICK2CART_S_TAXRATES_DELETED_SUCCESSFULLY', $successCount);
			}

			$this->setMessage($msg);
		}
		catch (Exception $e)
		{
			$this->setMessage(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 'error');
		}

		$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect = Route::_('index.php?option=com_quick2cart&view=taxrates&Itemid=' . $itemid, false);

		$this->setMessage($msg);
		$this->setRedirect($redirect, $msg);
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
		$data  = array('publish' => 1, 'unpublish' => 0);
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('taxrates');
		$comquick2cartHelper = new comquick2cartHelper;

		try
		{
			$successCount = $model->setItemState($cid, $value);

			if ($successCount)
			{
				if ($value === 1)
				{
					$ntext = 'COM_QUICK2CART_N_TAXRATES_PUBLISHED';
				}
				elseif ($value === 0)
				{
					$ntext = 'COM_QUICK2CART_N_TAXRATES_UNPUBLISHED';
				}

				$this->setMessage(Text::plural($ntext, count($cid)));
			}
		}
		catch (Exception $e)
		{
			$this->setMessage($e->getMessage(), 'error');
		}

		$itemId   = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect = Route::_('index.php?option=com_quick2cart&view=taxrates&Itemid=' . $itemId, false);
		$this->setRedirect($redirect);
	}

	/**
	 * Method to unpublish records.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function unpublish()
	{
		$this->publish();
	}
}
