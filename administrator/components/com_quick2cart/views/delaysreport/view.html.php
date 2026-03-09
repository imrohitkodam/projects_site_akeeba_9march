<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;

/**
 * View to edit
 *
 * @since  2.5
 */
class Quick2cartViewDelaysreport extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   STRING  $tpl  template name
	 *
	 * @return  null
	 *
	 * @since  2.5
	 */
	public function display($tpl = null)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$app           = Factory::getApplication();
		$option        = $app->input->get('option');

		// Default layout is default
		$layout = $app->input->get('layout','','default');
		$this->setLayout($layout);
		$model = $this->getModel('delaysreport');

		$sstatus   = array();
		$sstatus[] = HTMLHelper::_('select.option', 'C', Text::_('QTC_CONFR'));
		$sstatus[] = HTMLHelper::_('select.option', 'S', Text::_('QTC_SHIP'));

		// $sstatus[] = JHtml::_('select.option','P',  JText::_('QTC_PENDIN'));
		$sstatus[] = HTMLHelper::_('select.option', 'E', Text::_('QTC_ERR'));
		$this->sstatus = $sstatus;

		$delay   = array();
		$delay[] = HTMLHelper::_('select.option', 1, Text::_('QTC_ONE'));
		$delay[] = HTMLHelper::_('select.option', 2, Text::_('QTC_TWO'));
		$delay[] = HTMLHelper::_('select.option', 5, Text::_('QTC_FIVE'));
		$delay[] = HTMLHelper::_('select.option', 10, Text::_('QTC_TEN'));
		$delay[] = HTMLHelper::_('select.option', 25, Text::_('QTC_TWENTYFIVE'));

		// $delay[]=JHtml::_('select.option',50, JText::_('QTC_FIFTY'));
		$this->delay = $delay;

		if ($layout == 'default')
		{
			// SEARCH TEXT BOX VALUE
			$search = $app->getUserStateFromRequest($option . 'search', 'search', '', 'string');
			$search = StringHelper::strtolower($search);

			if ($search == null)
			{
				$search = '';
			}

			$filter_order_Dir = $app->getUserStateFromRequest('com_quick2cart.filter_order_Dir', 'filter_order_Dir', '', 'word');
			$filter_type      = $app->getUserStateFromRequest('com_quick2cart.filter_order', 'filter_order', '', 'string');
			$search_store     = $app->getUserStateFromRequest($option . 'search_store', 'search_store', 0, 'INTEGER');
			$status           = $app->getUserStateFromRequest($option . 'search_select', 'search_select', '', 'string');
			$delayday         = $app->getUserStateFromRequest($option . 'search_select_delay', 'search_select_delay', '', 'INTEGER');

			$model                 = $this->getModel('delaysreport');
			$this->getDelaysReport = $getDelaysReport = $model->getDelaysReport();
			$this->getDelaysReport = $getDelaysReport;

			$total       = $this->get('Total');
			$this->total = $total;

			$pagination       = $this->get('Pagination');
			$this->pagination = $pagination;

			// From date FILTER
			$fromDate = $app->getUserStateFromRequest($option . 'salesfromDate', 'salesfromDate', '', 'RAW');

			// To date FILTER
			$toDate   = $app->getUserStateFromRequest($option . 'salestoDate', 'salestoDate', '', 'RAW');

			$lists['salesfromDate']       = $fromDate;
			$lists['salestoDate']         = $toDate;
			$lists['order_Dir']           = $filter_order_Dir;
			$lists['order']               = $filter_type;

			// $lists['search']      = $search;
			$lists['search_select']       = $status;
			$lists['search_select_delay'] = $delayday;
			$lists['search_list']         = $search;
			$this->lists                  = $lists;
		}

		$payee_name = $app->getUserStateFromRequest('com_quick2cart', 'payee_name', '', 'string');

		//  $lists['payee_name']=$payee_name;

		$this->_setToolBar();

		// FOR DISPLAY SIDE FILTER

		if (JVERSION < '4.0.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  null
	 */
	public function _setToolBar()
	{
		$jinput = Factory::getApplication()->input;
		$document = Factory::getDocument();
		HTMLHelper::_('stylesheet','components/com_quick2cart/css/quick2cart.css');
		$bar = ToolBar::getInstance('toolbar');
		ToolBarHelper::title(Text::_('QTC_DELAY_ORDERS_REPORT'), 'icon-48-quick2cart.png');
		$layout = $jinput->get('layout','','default');

		if ($layout == "default")
		{
			// JToolBarHelper::cancel( 'cancel', 'Close' );
			// FILTER FOR J3.0
			// JHtmlSidebar class to render a list view sidebar //setAction::Set value for the action attribute of the filter form
			JHtmlSidebar::setAction('index.php?option=com_quick2cart');
			$serSel = HTMLHelper::_('select.options', $this->sstatus, "value", "text", $this->lists['search_select'], true);
			JHtmlSidebar::addFilter(Text::_('QTC_SELONE'), 'search_select', $serSel);

			$days = HTMLHelper::_('select.options', $this->delay, "value", "text", $this->lists['search_select_delay'], true);
			JHtmlSidebar::addFilter(Text::_('QTC_DAYS'), 'search_select_delay', $days);

			// CSV EXPORT
			ToolBarHelper::custom('csvexport', 'icon-32-save.png', 'icon-32-save.png', Text::_("COM_QUICK2CART_SALES_CSV_EXPORT"), false);
		}
	}
}
