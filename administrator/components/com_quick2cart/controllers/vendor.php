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
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

/**
 * Vendor form controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerVendor extends FormController
{
	// @TODO - remove this when jform is used
	/**
	 * Function to add new vendor
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function addNew()
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=vendor&layout=createstore');
	}

	// @TODO - remove this when jform is used
	/**
	 * Function to cancel
	 *
	 * @param   STRING  $key  key
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function cancel($key = null)
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=stores');
	}

	// @TODO - remove this when jform is used
	/**
	 * Function to edit vendor data
	 *
	 * @param   STRING  $key     key
	 * @param   STRING  $urlVar  urlVar
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function edit($key = null, $urlVar = null)
	{
		$input = Factory::getApplication()->input;

		// Get some variables from the request
		$cid = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);

		$link = 'index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . $cid[0] . '';
		$this->setRedirect($link);
	}

	/**
	 * Function to save vendor data
	 *
	 * @param   STRING  $key     key
	 * @param   STRING  $urlVar  urlVar
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function save($key = null, $urlVar = null)
	{
		$app                 = Factory::getApplication();
		$jinput              = $app->input;
		$post                = $jinput->post;
		$model               = $this->getModel('vendor');
		$storeHelper         = new storeHelper;
		$storeOwner          = $post->get('store_creator_id');
		$store_id            = $post->get('id');
		$allowToCreateStore  = $storeHelper->isAllowedToCreateNewStore($storeOwner);
		$task                = $jinput->get('task');
		$comquick2cartHelper = new comquick2cartHelper;
		$createStoreViewLink = 'index.php?option=com_quick2cart&view=vendor&layout=createstore';
		$itemid              = $comquick2cartHelper->getItemId($createStoreViewLink);
		$link                = Route::_($createStoreViewLink . '&store_id=' . $store_id . '&Itemid=' . $itemid, false);

		if (!is_numeric($post->get('phone')))
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_INVALID_CONTACT_NO'), 'error');
			$this->setRedirect($link);

			return false;
		}

		if ($allowToCreateStore == 0 && empty( $store_id ))
		{
			$userStoreCount = $storeHelper->getUserStoreCount($storeOwner);
			$msg            = Text::sprintf('QTC_ALREADY_YOU_HAVE_STORES', $userStoreCount);
			$this->setRedirect('index.php?option=com_quick2cart&view=stores' . $result['store_id'], $msg, 'error');
		}
		else
		{
			$comquick2cartHelper = new comquick2cartHelper;
			$storeHelper         = new storeHelper;
			$storeOwner          = $post->get('store_creator_id');
			$result              = $storeHelper->saveVendorDetails($post, $storeOwner);
			$msg                 = $result['msg'];
			$task                = $jinput->get('task');
			$btnAction           = $post->get('btnAction');

			if ($btnAction == 'vendor.saveAndClose')
			{
				$link = Uri::base() . "index.php?option=com_quick2cart&view=stores";
				$this->setRedirect($link, $msg);
			}
			else
			{
				switch ($task)
				{
					case 'save':
					$this->setRedirect('index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . $result['store_id'], $msg);
					break;
				}
			}
		}
	}

	/**
	 * Function to export CSV
	 *
	 * @return  null
	 *
	 * @since  1.5
	 *
	 */
	public function csvexport()
	{
		$model    = $this->getModel("vendor");
		$CSVData  = $model->getCsvexportData();
		$filename = "SalesPerSellerReport_" . date("Y-m-d");
		$csvData  = null;

		$headColumn    = array();
		$headColumn[0] = Text::_('COM_QUICK2CART_SALESPERSELLER_STORENAME');
		$headColumn[1] = Text::_('COM_QUICK2CART_SALESPERSELLER_VENDORNAME');
		$headColumn[2] = Text::_('COM_QUICK2CART_SALESPERSELLER_STATUS');
		$headColumn[3] = Text::_('COM_QUICK2CART_SALESPERSELLER_EMAIL');
		$headColumn[4] = Text::_('COM_QUICK2CART_SALESPERSELLER_PHONE');
		$headColumn[5] = Text::_('COM_QUICK2CART_SALESPERSELLER_SALE');
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
				$csvrow    = array();
				$csvrow[0] = '"' . $data['title'] . '"';
				$csvrow[1] = '"' . $data['username'] . '"';
				$status    = Text::_('COM_QUICK2CART_UNPUBLISH');

				if ($data['published'] == 1)
				{
					$status = Text::_('COM_QUICK2CART_PUBLISH');
				}

				$csvrow[2] = '"' . $status . '"';
				$csvrow[3] = '"' . $data['store_email'] . '"';
				$csvrow[4] = '"' . $data['phone'] . '"';
				$storeHelper = new storeHelper;
				$comquick2cartHelper = new comquick2cartHelper;
				$total_sale = $storeHelper->getTotalSalePerStore($data['id']);

				if ($total_sale)
				{
					$sale = $comquick2cartHelper->getFromattedPrice($total_sale);
				}

				$csvrow[5] = '"' . $sale . '"';
				$csvData .= implode(";", $csvrow);
				$csvData .= "\n";
			}
		}

		ob_clean();
		echo $csvData . "\n";
		jexit();

		$link = Uri::base() . substr(Route::_('index.php?option=com_quick2cart&view=vendor&layout=salespervendor', false), strlen(Uri::base(true)) + 1);

		$this->setRedirect($link);
	}

	/**
	 * Function to get region
	 *
	 * @return  null
	 *
	 * @since  1.5
	 * */
	public function getRegions()
	{
		$app                 = Factory::getApplication();
		$input               = $app->input;
		$country_id          = $input->get('country_id', '0', 'int');
		$Quick2cartModelZone = $this->getModel('zone');
		$Quick2cartModelZone = new Quick2cartModelZone;

		if (!empty($country_id))
		{
			$stateList = $Quick2cartModelZone->getRegionList($country_id);

			$options   = array();
			$options[] = HTMLHelper::_('select.option', 0, Text::_('COM_QUICK2CART_ZONE_ALL_STATES'));

			if ($stateList)
			{
				foreach ($stateList as $state)
				{
					// This is only to generate the <option> tag inside select tag
					$options[] = HTMLHelper::_('select.option', $state['id'], $state['region']);
				}
			}

			// Now generate the select list and echo that
			if (JVERSION < '4.0.0')
			{
			    $stateList = HTMLHelper::_('select.genericlist', $options, 'qtcstorestate', ' class="qtc_store_state"', 'value', 'text');
			}
			else
			{
				$stateList = HTMLHelper::_('select.genericlist', $options, 'qtcstorestate', ' class="form-select"', 'value', 'text');
			}

			echo $stateList;
		}

		$app->close();
	}
}
