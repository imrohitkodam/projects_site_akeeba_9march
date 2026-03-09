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
defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\String\StringHelper;

/**
 * View class for a Shipping.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewShipping extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse;
	 *                        automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->comquick2cartHelper = new comquick2cartHelper;
		$this->storeHelper         = new storeHelper;
		$this->zoneHelper          = new zoneHelper;
		$this->params              = ComponentHelper::getParams('com_quick2cart');
		$this->isShippingEnabled   = $this->params->get('shipping', 0);

		$app = Factory::getApplication();

		// Check whether view is accessible to user
		if (!$this->zoneHelper->isUserAccessible())
		{
			return;
		}

		$app = Factory::getApplication();
		$jinput    = $app->input;
		$option    = 'com_quick2cart';
		$nameSpace = 'com_quick2cart.shipping';
		$task      = $jinput->get('task');
		$view      = $jinput->get('view', '');
		$layout    = $jinput->get('layout', 'default', 'string');
		$tmpl = $jinput->get('tmpl', '', 'string');

		if ($layout == 'default')
		{
			// Display list of pluigns
			$filter_order      = $app->getUserStateFromRequest($nameSpace . 'filter_order', 'filter_order', 'tbl.id', 'cmd');
			$filter_order_Dir  = $app->getUserStateFromRequest($nameSpace . 'filter_order_Dir', 'filter_order_Dir', '', 'word');
			$filter_orderstate = $app->getUserStateFromRequest($nameSpace . 'filter_orderstate', 'filter_orderstate', '', 'string');
			$filter_name       = $app->getUserStateFromRequest($nameSpace . 'filter_name', 'filter_name', 'tbl.name', 'cmd');

			$search = $app->getUserStateFromRequest($nameSpace . 'search', 'search', '', 'string');

			if (strpos($search, '"') !== false)
			{
				$search = str_replace(array('=', '<'), '', $search);
			}

			$search = StringHelper::strtolower($search);

			$model = $this->getModel('shipping');

			// Get data from the model
			$items      = $this->get('Items');
			$total      = $model->getTotal();
			$pagination = $model->getPagination();

			if (count($items) == 1)
			{
				// If there is only one plug then redirct to that shipping view
				$extension_id = $items[0]->extension_id;
				$this->form   = '';

				if ($extension_id)
				{
					$this->form = $this->getShipPluginForm($extension_id);

					$plugConfigLink = "index.php?option=com_quick2cart&task=shipping.getShipView&extension_id=" . $extension_id;

					$CpItemid = $this->comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
					$redirect = Route::_($plugConfigLink . '&Itemid=' . $itemid, false);
					$app->redirect($redirect);
				}
			}

			// Table ordering
			$lists['order_Dir']   = $filter_order_Dir;
			$lists['order']       = $filter_order;
			$lists['filter_name'] = $filter_name;

			// Search filter
			$lists['search'] = $search;

			$this->lists      = $lists;
			$this->items      = $items;
			$this->pagination = $pagination;
		}
		elseif ($layout == 'list')
		{
			$this->form   = '';
			$extension_id = $jinput->get('extension_id');

			if ($extension_id)
			{
				$this->form = $this->getShipPluginForm($extension_id);
			}

			if ($tmpl == 'component')
			{
				if (!empty($this->form))
				{
					$this->form = str_replace( "form-select", "form-select-sm", $this->form);
				}
			}
		}

		$this->addToolbar();

		if (JVERSION < '4.0.0')
		{
			if (!isset($tmpl) || empty($tmpl))
			{
				$this->sidebar = JHtmlSidebar::render();
			}
		}

		parent::display($tpl);
	}

	/**
	 * Method getShipPluginForm.
	 *
	 * @param   Integer  $extension_id  Extension ID
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function getShipPluginForm($extension_id)
	{
		$app           = Factory::getApplication();
		$jinput        = $app->input;
		$qtcshiphelper = new qtcshiphelper;
		$plugName      = $qtcshiphelper->getPluginDetail($extension_id);
		$import        = PluginHelper::importPlugin('tjshipping', $plugName);
		$result        = $app->triggerEvent('onTjShip_shipBuildLayout', array($jinput));

		if (!empty($result[0]))
		{
			return $this->form = $result[0];
		}

		return '';
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
		require_once JPATH_COMPONENT . '/helpers/quick2cart.php';

		$state = $this->get('State');
		$canDo = Quick2cartHelper::getActions();

		ToolBarHelper::title(Text::_('COM_QUICK2CART_SHIPPING'), 'list');

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_quick2cart');
		}

		$this->extra_sidebar = '';
	}
}
