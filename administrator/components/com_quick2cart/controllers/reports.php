<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

jimport('joomla.application.component.controller');

/**
 * reports controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerReports extends quick2cartController
{
	/**
	 * Function to save reports
	 *
	 * @return  null
	 *
	 * @since 1.5
	 * */
	public function save()
	{
		Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

		// Get model
		$model    = $this->getModel('reports');
		$result   = $model->savePayout();
		$redirect = Route::_('index.php?option=com_quick2cart&view=reports&layout=payouts', false);
		$msg      = Text::_('COM_QUICK2CART_PAYOUT_ERROR_SAVING');

		if ($result)
		{
			$msg = Text::_('COM_QUICK2CART_PAYOUT_SAVED');
		}

		$this->setRedirect($redirect, $msg);
	}

	/**
	 * Function to edit pay
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function edit_pay()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$model    = $this->getModel('reports');
		$result   = $model->editPayout();
		$redirect = Route::_('index.php?option=com_quick2cart&view=reports&layout=payouts', false);
		$msg      = Text::_('COM_QUICK2CART_PAYOUT_ERROR_SAVING');

		if ($result)
		{
			$msg = Text::_('COM_QUICK2CART_PAYOUT_SAVED');
		}

		$this->setRedirect($redirect, $msg);
	}

	/**
	 * Function for CSV Export
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function  csvexport()
	{
		$model    = $this->getModel("reports");
		$CSVData  = $model->getCsvexportData();
		$filename = "StoreOwnerPayouts_" . date("Y-m-d");
		$csvData  = null;

		// $csvData.= "Item_id;Product Name;Store Name;Store Id;Sales Count;Amount;Created By;";
		$headColumn = array();
		$headColumn[0] = Text::_('COM_QUICK2CART_PAYOUTS_ID');
		$headColumn[1] = Text::_('COM_QUICK2CART_PAYOUTS_NAME');
		$headColumn[2] = Text::_('COM_QUICK2CART_PAYOUTS_EMAIL');
		$headColumn[3] = Text::_('COM_QUICK2CART_PAYOUTS_TRANS_ID');
		$headColumn[4] = Text::_('COM_QUICK2CART_PAYOUTS_DATE');
		$headColumn[5] = Text::_('COM_QUICK2CART_PAYOUTS_STATUS');
		$headColumn[6] = Text::_('COM_QUICK2CART_PAYOUTS_AMOUNT');

		$csvData .= implode(";", $headColumn);
		$csvData .= "\n";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=" . $filename . ".csv");

		if (!empty($CSVData))
		{
			$storeHelper = new storeHelper;

			foreach ($CSVData as $data)
			{
				$csvrow = array();
				$csvrow[0] = '"' . $data['id'] . '"';
				$csvrow[1] = '"' . $data['payee_name'] . '"';
				$csvrow[2] = '"' . $data['email_id'] . '"';
				$csvrow[3] = '"' . $data['transaction_id'] . '"';

				$date = HTMLHelper::_('date', $data['date'], "Y-m-d");

				$csvrow[4] = '"' . $date . '"';

				if ($data['status'] == 1)
				{
					$status = Text::_('COM_QUICK2CART_PAID');
				}
				else
				{
					$status = Text::_('COM_QUICK2CART_NOT_PAID');
				}

				$csvrow[5] = '"' . $status . '"';
				$csvrow[6] = '"' . $data['amount'] . '"';
				$csvData .= implode(";", $csvrow);
				$csvData .= "\n";
			}
		}

		ob_clean();
		echo $csvData . "\n";
		jexit();

		$link = Uri::base() . substr(Route::_('index.php?option=com_quick2cart&view=reports&layout=payouts', false), strlen(Uri::base(true)) + 1);
		$this->setRedirect($link);
	}
}
