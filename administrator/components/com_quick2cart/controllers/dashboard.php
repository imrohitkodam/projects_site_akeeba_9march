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
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

JLoader::register('TjControllerHouseKeeping', JPATH_SITE . "/libraries/techjoomla/controller/houseKeeping.php");

/**
 * Dashboard form controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerDashboard extends FormController
{
	use TjControllerHouseKeeping;

	/**
	 * Function to get version
	 *
	 * @return  null
	 *
	 * @since   1.6
	 */
	public function getVersion()
	{
		if (!class_exists('comquick2cartHelper'))
		{
			$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$helperobj = new comquick2cartHelper;
		echo $latestversion = $helperobj->getVersion();
		jexit();
	}

	/**
	 * Function to set session for graph
	 *
	 * @return  null
	 *
	 * @since   1.6
	 */
	public function SetsessionForGraph()
	{
		$periodicorderscount = '';
		$fromDate            = $_GET['fromDate'];
		$toDate              = $_GET['toDate'];
		$periodicorderscount = 0;

		$session = Factory::getSession();
		$session->set('qtc_graph_from_date', $fromDate);
		$session->set('socialads_end_date', $toDate);

		$model       = $this->getModel('dashboard');
		$statsforpie = $model->statsforpie();

		// $ignorecnt = $model->getignoreCount();
		$periodicorderscount = $model->getperiodicorderscount();
		$session->set('statsforpie', $statsforpie);

		// $session->set('ignorecnt', $ignorecnt);
		$session->set('periodicorderscount', $periodicorderscount);

		header('Content-type: application/json');
		echo json_encode(array("statsforpie" => $statsforpie /*,"ignorecnt" => $ignorecnt*/));
		jexit();
	}

	/**
	 * Function to make chart
	 *
	 * @return  null
	 *
	 * @since   1.6
	 */
	public function makechart()
	{
		$month_array_name = array(
			Text::_('SA_JAN'),
			Text::_('SA_FEB'),
			Text::_('SA_MAR'),
			Text::_('SA_APR'),
			Text::_('SA_MAY'),
			Text::_('SA_JUN'),
			Text::_('SA_JUL'),
			Text::_('SA_AUG'),
			Text::_('SA_SEP'),
			Text::_('SA_OCT'),
			Text::_('SA_NOV'),
			Text::_('SA_DEC')
		);

		$session             = Factory::getSession();
		$qtc_graph_from_date = '';
		$socialads_end_date  = '';
		$qtc_graph_from_date = $session->get('fromDate', '');
		$socialads_end_date  = $session->get('socialads_end_date', '');
		$total_days          = (strtotime($socialads_end_date) - strtotime($qtc_graph_from_date)) / (60 * 60 * 24);
		$total_days++;

		$statsforpie         = $session->get('statsforpie', '');
		$model               = $this->getModel('dashboard');
		$statsforpie         = $model->statsforpie();
		$ignorecnt           = $session->get('ignorecnt', '');
		$periodicorderscount = $session->get('periodicorderscount');
		$imprs             = 0;
		$clicks            = 0;
		$max_invite        = 100;
		$cmax_invite       = 100;
		$yscale            = "";
		$titlebar          = "";
		$daystring         = "";
		$finalstats_date   = array();
		$finalstats_clicks = array();
		$finalstats_imprs  = array();
		$day_str_final     = '';
		$emptylinechart    = 0;
		$barchart          = '';
		$fromDate          = $session->get('qtc_graph_from_date', '');
		$toDate            = $session->get('socialads_end_date', '');
		$dateMonthYearArr  = array();
		$fromDateSTR       = strtotime($fromDate);
		$toDateSTR         = strtotime($toDate);
		$pending_orders    = $confirmed_orders = $shiped_orders = $refund_orders = 0;

		if (empty($statsforpie[0]) && empty($statsforpie[1]) && empty($statsforpie[2]))
		{
			$barchart = Text::_('NO_STATS');
			$emptylinechart = 1;
		}
		else
		{
			if (!empty($statsforpie[0]))
			{
				$pending_orders = $statsforpie[0][0]->orders;
			}

			if (!empty($statsforpie[1]))
			{
				$confirmed_orders = $statsforpie[1][0]->orders;
				$shiped_orders = $statsforpie[3][0]->orders;
			}

			if (!empty($statsforpie[1]))
			{
				$refund_orders = $statsforpie[2][0]->orders;
			}
		}

		/*$barchart='<img src="http://chart.apis.google.com/chart?cht=lc&chtt=+'
		.$titlebar.'|'
		* .JText::_('NUMICHITSMON').'  	+&chco=0000ff,ff0000&chs=900x310&chbh=a,25&chm='.$chm_str.'&chd=t:'.$imprs.'|'.$clicks
		* .'&chxt=x,y&chxr=0,0,200&chds=0,'.$max_invite.',0,'.$cmax_invite.'&chxl=1:|'.$yscale.'|0:|'. $daystring.'|" />';*/

		header('Content-type: application/json');
		echo json_encode(
				array(
					"pending_orders" => $pending_orders,
					"confirmed_orders" => $confirmed_orders,
					"shiped_orders" => $shiped_orders,
					"refund_orders" => $refund_orders,
					"periodicorderscount" => $periodicorderscount,
					"emptylinechart" => $emptylinechart
				)
			);
		jexit();
	}

	/**
	 * Manual Setup related chages: For now - 1. for overring the bs-2 view
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function setup()
	{
		$jinput     = Factory::getApplication()->input;
		$takeBackUp = $jinput->get("takeBackUp", 1);

		$comquick2cartHelper = new comquick2cartHelper;
		$defTemplate         = $comquick2cartHelper->getSiteDefaultTemplate(0);
		$templatePath        = JPATH_SITE . '/templates/' . $defTemplate . '/html/';

		$statusMsg = array();
		$statusMsg["component"] = array();

		// 1. Override component view
		$siteBs2views = JPATH_ROOT . "/components/com_quick2cart/views_bs2/site";

		// Check for com_quick2cart folder in template override location
		$compOverrideFolder  = $templatePath . "com_quick2cart";

		if (Folder::exists($compOverrideFolder))
		{
			if ($takeBackUp)
			{
				// Rename
				$backupPath = $compOverrideFolder . '_' . date("Ymd_H_i_s");
				Folder::move($compOverrideFolder, $backupPath);
				$statusMsg["component"][] = Text::_('COM_QUICK2CART_TAKEN_BACKUP_OF_OVERRIDE_FOLDER') . $backupPath;
			}
			else
			{
				Folder::delete($compOverrideFolder);
			}
		}

		// Copy
		Folder::copy($siteBs2views, $compOverrideFolder);
		$statusMsg["component"][] = Text::_('COM_QUICK2CART_OVERRIDE_DONE') . $compOverrideFolder;

		// 2. Create Override plugins folder if not exist
		$pluginsPath = JPATH_ROOT . "/components/com_quick2cart/views_bs2/plugins/";

		// Check for com_quick2cart folder in template override location
		$pluginsOverrideFolder = $templatePath . "plugins";
		$createFolderStatus    = Folder::create($pluginsOverrideFolder);

		if ($createFolderStatus)
		{
			$statusMsg["plugins"][] = Text::_('COM_QUICK2CART_CREATE_PLUGINS_FOLDER_STATUS');

			// Check for override tjshipping plugin
			$newtjshipping = $pluginsPath . "/tjshipping";
			$tjshippingOverrideFolder = $pluginsOverrideFolder . "/tjshipping";

			if (Folder::exists($tjshippingOverrideFolder))
			{
				if ($takeBackUp)
				{
					// Rename
					$backupPath = $tjshippingOverrideFolder . '_' . date("Ymd_H_i_s");
					Folder::move($tjshippingOverrideFolder, $backupPath);

					$statusMsg["plugins"][] = Text::sprintf('COM_QUICK2CART_TAKEN_OF_PLUGIN_ND_BACKUP_PATH', 'tjshipping', $backupPath);
				}
				else
				{
					Folder::delete($tjshippingOverrideFolder);
				}
			}

			// Copy
			Folder::copy($newtjshipping, $tjshippingOverrideFolder);
			$statusMsg["plugins"][] = Text::sprintf('COM_QUICK2CART_COMPLETED_PLUGINS_OVERRIDE', "<b> tjshipping</b>");
		}
		else
		{
			$statusMsg["plugins"][] = Text::_('COM_QUICK2CART_CREATE_PLUGINS_FOLDER_FAILED');
		}

		// 3. Modules override
		$modules = Folder::folders(JPATH_ROOT . "/components/com_quick2cart/views_bs2/modules/");
		$statusMsg["modules"] = array();

		foreach ($modules as $modName)
		{
			$this->overrideModule($templatePath, $modName, $statusMsg, $takeBackUp);
		}

		$this->displaySetup($statusMsg);
		exit;
	}

	/**
	 * Override the Modules
	 *
	 * @param   string  $templatePath  templatePath eg JPATH_SITE . '/templates/protostar/html/'
	 * @param   string  $modName       Module name
	 * @param   array   &$statusMsg    The array of config values.
	 * @param   array   $takeBackUp    flag
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function overrideModule($templatePath, $modName, &$statusMsg, $takeBackUp)
	{
		$bs2ModulePath          = JPATH_ROOT . "/components/com_quick2cart/views_bs2/modules/" . $modName;
		$overrideBs2ModulePath  = $templatePath . $modName;
		$statusMsg["modules"][] = Text::sprintf('COM_QUICK2CART_OVERRIDING_THE_MODULE', $modName);

		if (Folder::exists($overrideBs2ModulePath))
		{
			if ($takeBackUp)
			{
				// Rename
				$backupPath = $overrideBs2ModulePath . '_' . date("Ymd_H_i_s");
				Folder::move($overrideBs2ModulePath, $backupPath);

				$statusMsg["modules"][] = Text::sprintf('COM_QUICK2CART_TAKEN_OF_MODULE_ND_BACKUP_PATH',  $modName, $backupPath);
			}
			else
			{
				Folder::delete($overrideBs2ModulePath);
			}
		}

		// Copy
		Folder::copy($bs2ModulePath, $overrideBs2ModulePath);
		$statusMsg["modules"][] = Text::sprintf('COM_QUICK2CART_COMPLETED_MODULE_OVERRIDE', "<b>" . $modName . "</b>");
	}

	/**
	 * Override the Modules
	 *
	 * @param   array  $statusMsg  The array of config values.
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function displaySetup($statusMsg)
	{
		echo "<br/> =================================================================================";
		echo "<br/> " . Text::_("COM_QUICK2CART_BS2_OVERRIDE_PROCESS_START");
		echo "<br/> =================================================================================";

		foreach ($statusMsg as $key => $extStatus)
		{
			echo "<br/> <br/><br/>*****************  " . Text::_("COM_QUICK2CART_BS2_OVERRIDING_FOR")
			. " <strong>" . $key . "</strong> ****************<br/>";

			foreach ($extStatus as $k => $status)
			{
				$index = $k + 1;
				echo $index . ") " . $status . "<br/> ";
			}
		}

		echo "<br/> " . Text::_("COM_QUICK2CART_BS2_OVERRIDING_DONE");
	}
}
