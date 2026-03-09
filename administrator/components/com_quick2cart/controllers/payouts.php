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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

// Load Quick2cart Controller for list views
require_once __DIR__ . '/q2clist.php';

/**
 * Payouts list controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerPayouts extends Quick2cartControllerQ2clist
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  The array of config values.
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Payout', $prefix = 'Quick2cartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Function for CSV export
	 *
	 * @return  null
	 *
	 * @since  1.5
	 *
	 * */
	public function csvexport()
	{
		$payoutsModel = BaseDatabaseModel::getInstance('Payouts', 'Quick2cartModel', array('ignore_request' => true));
		$CSVData = $payoutsModel->getCsvexportData();

		$filename = "StoreOwnerPayouts_" . date("Y-m-d");
		$csvData = null;

		$headColumn = array();
		$headColumn[0] = Text::_('COM_QUICK2CART_PAYOUT_ID');
		$headColumn[1] = Text::_('COM_QUICK2CART_PAYEE_NAME');
		$headColumn[2] = Text::_('COM_QUICK2CART_PAYPAL_EMAIL');
		$headColumn[3] = Text::_('COM_QUICK2CART_TRANSACTION_ID');
		$headColumn[4] = Text::_('COM_QUICK2CART_PAYOUT_DATE');
		$headColumn[5] = Text::_('COM_QUICK2CART_STATUS');
		$headColumn[6] = Text::_('COM_QUICK2CART_CASHBACK_AMOUNT');

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
	}
}
