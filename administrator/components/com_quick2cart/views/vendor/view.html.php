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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

require_once JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/zone.php';
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjprivacy/tables');

JLoader::import('fronthelper', JPATH_SITE . '/components/com_tjvendors/helpers');
JLoader::import('vendorclientxref', JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');
include_once JPATH_SITE . '/components/com_tjvendors/includes/tjvendors.php';

/**
 * View class for vendor.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewVendor extends HtmlView
{
	protected $params;

	protected $directPaymentConfig;

	protected $orders_site;

	protected $allowToCreateStore;

	protected $editview;

	protected $storeinfo;

	protected $legthList;

	protected $weigthList;

	protected $OnBeforeCreateStore;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app          = Factory::getApplication();
		$this->params              = ComponentHelper::getParams('com_quick2cart');
		$this->directPaymentConfig = $this->params->get('send_payments_to_store_owner', 0, 'INTEGER');

		$Quick2cartModelZone = new Quick2cartModelZone;
		$comquick2cartHelper = new comquick2cartHelper;
		$storeHelper         = new storeHelper;
		$this->addToolbar();
		$app = Factory::getApplication();
		$input     = $app->input;

		$store_id          = $input->get('store_id', '0');
		$this->storeinfo = $storeinfo = $comquick2cartHelper->editstore($store_id);

		$option = $input->get('option');
		$layout = $input->get('layout', 'createstore');

		if ($layout == "createstore")
		{
			if (!empty($this->storeinfo))
			{
				$tjvendorFrontHelper       = new TjvendorFrontHelper;
				$this->vendorCheck         = $tjvendorFrontHelper->checkVendor($this->storeinfo[0]->owner, 'com_quick2cart');
				$this->checkGatewayDetails = $tjvendorFrontHelper->checkGatewayDetails($this->storeinfo[0]->owner, 'com_quick2cart');
			}

			$this->countrys    = $Quick2cartModelZone->getCountry();
			$this->orders_site = 1;

			// DEFAULT ALLOW TO CREAT STORE
			$this->allowToCreateStore = 1;

			// Means edit task
			if (!empty($store_id))
			{
				// $this->store_authorize=$comquick2cartHelper->store_authorize("vendor_createstore",$store_id);
				$this->editview  = 1;

				if (!empty($this->storeinfo))
				{
					// Get weight and length select box
					$this->legthList  = $storeHelper->getLengthClassSelectList(0, $this->storeinfo[0]->length_id);
					$this->weigthList = $storeHelper->getWeightClassSelectList(0, $this->storeinfo[0]->weight_id);
				}

				if (!empty($this->storeinfo[0]->id) && !empty($this->storeinfo[0]->owner))
				{
					$userPrivacyTable = Table::getInstance('tj_consent', 'TjprivacyTable', array());
					$userPrivacyData = $userPrivacyTable->load(
												array(
														'client' => 'com_quick2cart.store',
														'client_id' => $this->storeinfo[0]->id,
														'user_id' => $this->storeinfo[0]->owner
													)
											);

					if ($userPrivacyData == true)
					{
						$this->storeinfo[0]->privacy_terms_condition = 1;
					}
				}
			}
			else
			{
				// NEW STORE TASK:: CK FOR WHETHER WE HV TO ALLOW OR NOT
				$storeHelper = new storeHelper;

				// $this->allowToCreateStore=$storeHelper->isAllowedToCreateNewStore();
				// Get weight and length select box
				$this->legthList  = $storeHelper->getLengthClassSelectList();
				$this->weigthList = $storeHelper->getWeightClassSelectList();
			}

			// START Q2C Sample development
			PluginHelper::importPlugin('system');

			// Call the plugin and get the result // @DEPRICATED
			$result              = $app->triggerEvent('onBeforeQ2cEditStore', array($store_id));
			$beforecart          = '';
			$OnBeforeCreateStore = '';

			if (!empty($result))
			{
				$OnBeforeCreateStore = $result[0];
			}

			$result = $app->triggerEvent('onBeforeQ2cStoreEdit', array($store_id));

			if (!empty($result))
			{
				// If more than one plugin returns

				/* $OnBeforeCreateStore = $result[0];
				$OnBeforeCreateStore = join('', $result);*/
				$OnBeforeCreateStore .= trim(implode("\n", $result));
			}

			$this->OnBeforeCreateStore = $OnBeforeCreateStore;
		}

		// end of else

		// FOR DISPLAY SIDE FILTER
		if ($layout == 'salespervendor' && JVERSION < '4.0.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

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
		$bar    = ToolBar::getInstance('toolbar');
		$input  = Factory::getApplication()->input;
		$layout = $input->get('layout', 'createstore');

		if ($layout == "createstore")
		{
			Factory::getApplication()->input->set('hidemainmenu', true);

			$store_id = $input->get('store_id', '0');
			$isNew    = ($store_id == 0);

			if ($isNew)
			{
				$viewTitle = Text::_('AD_VENDER_TITLE');
			}
			else
			{
				$viewTitle = Text::_('COM_QUICK2CART_EDIT_STORE');
			}

			ToolBarHelper::title($viewTitle, 'pencil-2');

			ToolBarHelper::back('COM_QUICK2CART_BACK', 'index.php?option=com_quick2cart&view=stores');
		}
		elseif ($layout == "salespervendor")
		{
			ToolBarHelper::title(Text::_('SALES_PER_VENDER_TITLE'), 'icon-48-quick2cart.png');
			ToolBarHelper::back('QTC_HOME', 'index.php?option=com_quick2cart');

			// CSV EXPORT
			ToolBarHelper::custom('csvexport', 'icon-32-save.png', 'icon-32-save.png', 'COM_QUICK2CART_SALES_CSV_EXPORT', false);
		}
	}
}
