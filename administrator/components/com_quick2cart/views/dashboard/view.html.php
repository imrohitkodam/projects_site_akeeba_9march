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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for dashboard.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewDashboard extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get download id
		$params           = ComponentHelper::getParams('com_quick2cart');
		$model            = $this->getModel('dashboard');
		$this->downloadid = $params->get('downloadid');

		// Get installed version from xml file
		$this->formXml = simplexml_load_file(JPATH_ADMINISTRATOR . "/components/com_quick2cart/quick2cart.xml");
		$xml           = (array) $this->formXml->version;
		$this->version = (string) $xml[0];

		// Refresh update site
		$model->refreshUpdateSite();

		// Get new version
		$this->latestVersion = $model->getLatestVersion();
		$this->addToolbar();

		// Get data from the model
		$allincome                     = $this->get('AllOrderIncome');
		$MonthIncome                   = $this->get('MonthIncome');
		$AllMonthName                  = $this->get('Allmonths');
		$this->tot_periodicorderscount = $this->get('periodicorderscount');

		$this->statsforpie  = $model->statsforpie();
		$this->allincome    = $allincome;
		$this->MonthIncome  = $MonthIncome;
		$this->AllMonthName = $AllMonthName;

		// Get box stats
		$this->productsCount = $this->get('ProductsCount');
		$this->ordersCount   = $this->get('OrdersCount');
		$this->storesCount   = $this->get('StoresCount');

		// Getting  not shipped prod /order
		$this->multivendor_enable = $multivendor_enable = $params->get('multivendor');
		$this->notShippedDetails  = $model->notShippedDetails();

		if (!empty($multivendor_enable))
		{
			$this->getpendingPayouts = $model->getpendingPayouts();
		}

		if (JVERSION < '4.0.0')
		{
		    $this->sidebar = JHtmlSidebar::render();
		}

		$this->showEasySocialMsg = $this->showEasySocialMsg();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		// Get the toolbar object instance
		$bar = ToolBar::getInstance('toolbar');
		ToolbarHelper::title(Text::_('QTC_DASHBOARD'), 'dashboard');

		$bar->appendButton(
		'Custom', '<a id="tjHouseKeepingFixDatabasebutton" class="btn btn-default hidden"><span class="icon-refresh"></span>'
		. Text::_('COM_QUICK2CART_FIX_DATABASE') . '</a>');

		// Adding option btn
		ToolbarHelper::preferences('com_quick2cart');
	}

	/**
	 * Function to show easysocial msg.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function showEasySocialMsg()
	{
		if ($this->Checkifinstalled('com_easysocial'))
		{
			$quick2cartproducts = Folder::exists(JPATH_SITE . '/media/com_easysocial/apps/user/quick2cartproducts');
			$quick2cartstores   = Folder::exists(JPATH_SITE . '/media/com_easysocial/apps/user/quick2cartstores');

			if (!$quick2cartproducts || !$quick2cartstores)
			{
				// IF any of app not present then show INTEGRATION link ON dashboard
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Function to check if component is installed.
	 *
	 * @param   string  $folder  component name
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function Checkifinstalled($folder)
	{
		$path = JPATH_SITE . '/' . 'components' . '/' . $folder;

		return (Folder::exists($path)) ? true : false;
	}
}
