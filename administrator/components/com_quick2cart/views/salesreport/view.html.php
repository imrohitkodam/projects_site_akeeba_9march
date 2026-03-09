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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\String\StringHelper;

/**
 * Class for a sales report view
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewSalesreport extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$app                 = Factory::getApplication();
		$jinput              = $app->input;
		$option              = $jinput->get('option');

		// Default layout is default
		$layout = $jinput->get('layout', 'default');
		$this->setLayout($layout);

		// SEARCH TEXT BOX VALUE
		$search = $app->getUserStateFromRequest($option . 'filter_search', 'filter_search', '', 'string');
		$search = StringHelper::strtolower($search);

		if ($search == null)
		{
			$search = '';
		}

		$filter_order_Dir = $app->getUserStateFromRequest('com_quick2cart.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$filter_type      = $app->getUserStateFromRequest('com_quick2cart.filter_order', 'filter_order', 'saleqty', 'string');

		// GET STORE DETAIL FOR FILTER
		$this->store_details = $comquick2cartHelper->getAllStoreDetails();

		// STORE FILTER search_store
		$search_store        = $app->getUserStateFromRequest($option . 'search_store', 'search_store', 0, 'INTEGER');

		$model       = $this->getModel('salesreport');
		$this->items = $model->getSalesReport();

		// GET STORE DETAIL FOR FILTER
		$this->store_details = $comquick2cartHelper->getAllStoreDetails();

		$total       = $this->get('Total');
		$this->total = $total;

		$pagination       = $this->get('Pagination');
		$this->pagination = $pagination;

		// From date FILTER
		$fromDate = $app->getUserStateFromRequest($option . 'salesfromDate', 'salesfromDate', '', 'RAW');

		// To date FILTER
		$toDate   = $app->getUserStateFromRequest($option . 'salestoDate', 'salestoDate', '', 'RAW');

		$lists['salesfromDate'] = $fromDate;
		$lists['salestoDate']   = $toDate;
		$lists['order_Dir']     = $filter_order_Dir;
		$lists['order']         = $filter_type;
		$lists['search_store']  = $search_store;
		$lists['search']        = $search;
		$this->lists            = $lists;

		$payee_name = $app->getUserStateFromRequest('com_quick2cart', 'payee_name', '', 'string');
		$this->addToolbar();

		if (JVERSION < '4.0.0')
		{
		    // FOR DISPLAY SIDE FILTER
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
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$bar    = ToolBar::getInstance('toolbar');

		require_once JPATH_COMPONENT . '/helpers/quick2cart.php';
		$canDo = Quick2cartHelper::getActions();

		ToolBarHelper::title(Text::_('COM_QUICK2CART_SALES_REPORT'), 'list');

		// FILTER FOR J3.0
		JHtmlSidebar::setAction('index.php?option=com_quick2cart');

		// CSV EXPORT
		if (!empty($this->items))
		{
			ToolBarHelper::custom('salesreport.csvexport', 'download', 'download', 'COM_QUICK2CART_SALES_CSV_EXPORT', false);
		}

		ToolBarHelper::back('QTC_HOME', 'index.php?option=com_quick2cart');

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_quick2cart');
		}
	}
}
