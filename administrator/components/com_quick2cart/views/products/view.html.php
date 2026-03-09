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
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for list view of products.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewProducts extends HtmlView
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
		$this->params              = ComponentHelper::getParams('com_quick2cart');
		$this->comquick2cartHelper = new comquick2cartHelper;
		$this->productHelper       = new productHelper;

		$this->product_types    = array();
		$this->product_types[1] = HTMLHelper::_('select.option', 1, Text::_('QTC_PROD_TYPE_SIMPLE'));
		$this->product_types[2] = HTMLHelper::_('select.option', 2, Text::_('QTC_PROD_TYPE_VARIABLE'));

		$this->products      = $this->items = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Creating status filter.
		$sstatus = array();

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->products as &$products)
		{
			$this->ordering[$products->parent_id][] = $products->item_id;
		}

		// Create clients array
		$clients = array();

		// Get all stores.
		$this->store_details = $this->comquick2cartHelper->getAllStoreDetails();

		$this->addToolbar();

		if (JVERSION < '4.0.0')
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
		$user      = Factory::getUser();
		$canDelete = $user->authorise('core.delete', 'com_quick2cart');
		$canCreate = $user->authorise('core.create', 'com_quick2cart');
		$bar       = ToolBar::getInstance('toolbar');

		ToolBarHelper::title(Text::_('COM_QUICK2CART_PRODUCTS'), 'cart');

		if ($canCreate)
		{
			ToolBarHelper::addNew('product.addnew', 'QTC_NEW');
		}

		if (JVERSION >= '4.0.0')
		{
			$dropdown = $bar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
		}

		if (JVERSION < '4.0.0')
		{
			if (isset($this->items[0]))
			{
				ToolBarHelper::editList('products.edit', 'JTOOLBAR_EDIT');
				ToolBarHelper::divider();
				ToolBarHelper::custom('products.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				ToolBarHelper::custom('products.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				ToolBarHelper::custom('products.featured', 'featured', '', 'COM_QUICK2CART_FEATURE_TOOLBAR');
				ToolBarHelper::custom('products.unfeatured', 'star-empty', '', 'COM_QUICK2CART_UNFEATURE_TOOLBAR');

				if ($canDelete)
				{
					ToolBarHelper::deleteList('', 'products.delete', 'JTOOLBAR_DELETE');
				}
			}
		}
		else
		{
			$childBar->publish('products.publish')->listCheck(true);
			$childBar->unpublish('products.unpublish')->listCheck(true);
			$childBar->standardButton('featured')->text('JFEATURE')->task('products.featured')->listCheck(true);
			$childBar->standardButton('featured')->text('JUNFEATURED')->task('products.unfeatured')->listCheck(true);

			if ($canDelete)
			{
				$childBar->delete('products.delete')->listCheck(true);
			}
		}

		if (isset($this->items[0]))
		{
			ToolBarHelper::custom('products.csvExport', 'download', 'download', 'COM_QUICK2CART_SALES_CSV_EXPORT', false);
		}

		if (JVERSION < '4.0.0')
		{
			$importProductsButton = '<a href="#import_products" class="btn ImportButton modal" data-toggle="modal" data-target="#import_products"><span class="icon-upload icon-white"></span>' . Text::_('COM_QUICK2CART_PRODUCTS_IMPORT_CSV') . '</a>';
			$bar->appendButton('Custom', $importProductsButton);
		}
		else
		{
			$bar->appendButton(
				'Custom', '&nbsp;&nbsp;<a
				class="btn btn-primary"
				data-bs-target="#import_products" data-bs-toggle="modal"
				href="javascript:void(0);"><span class="icon-upload icon-white"></span> ' . Text::_('COM_QUICK2CART_PRODUCTS_IMPORT_CSV') . '</a>'
			);
		}

		HTMLHelper::_('bootstrap.modal', 'collapseModal');

		// Adding option btn
		ToolbarHelper::preferences('com_quick2cart');
	}
}
