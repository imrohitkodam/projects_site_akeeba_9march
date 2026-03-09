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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\String\StringHelper;

/**
 * Shipping View class for a list of Quick2cart.
 *
 * @package  Quick2cart
 * @since    1.8
 */
class Quick2cartViewShipping extends HtmlView
{
	/**
	 * Function dispaly
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function display($tpl = null)
	{
		$comquick2cartHelper = new comquick2cartHelper;

		$app = Factory::getApplication();
		$this->params = ComponentHelper::getParams('com_quick2cart');
		$zoneHelper = new zoneHelper;

		// Check whether view is accessible to user
		if (!$zoneHelper->isUserAccessible())
		{
			return;
		}

		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = 'com_quick2cart';
		$nameSpace = 'com_quick2cart.shipping';
		$task = $jinput->get('task');
		$view = $jinput->get('view', '');
		$layout = $jinput->get('layout', 'default');

		// Get other vars
		$bsVersion               = $this->params->get('bootstrap_version', 'bs3', 'STRING');

		if ($bsVersion == 'bs5')
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
		}
		else
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs3');
		}

		if ($layout == 'default' || $layout == 'default_bs3' || $layout == 'default_bs5')
		{
			// Display list of pluigns
			$filter_order = $app->getUserStateFromRequest($nameSpace . 'filter_order', 'filter_order', 'tbl.id', 'cmd');
			$filter_order_Dir = $app->getUserStateFromRequest($nameSpace . 'filter_order_Dir', 'filter_order_Dir', '', 'word');
			$filter_orderstate	= $app->getUserStateFromRequest($nameSpace . 'filter_orderstate', 'filter_orderstate', '', 'string');
			$filter_name = $app->getUserStateFromRequest($nameSpace . 'filter_name', 'filter_name',	'tbl.name',	'cmd');

			$search	= $app->getUserStateFromRequest($nameSpace . 'search', 'search', '', 'string');

			if (strpos($search, '"') !== false)
			{
				$search = str_replace(array('=', '<'), '', $search);
			}

			$search = StringHelper::strtolower($search);

			$model = $this->getModel('shipping');

			// Get data from the model
			$items = $this->get('Items');
			$total		= $model->getTotal();
			$pagination = $model->getPagination();

			if (count($items) == 1)
			{
				// If there is only one plug then redirct to that shipping view
				$extension_id = $items[0]->extension_id;
				$this->form = '';

				if ($extension_id)
				{
					$this->form = $this->getShipPluginForm($extension_id);

					$plugConfigLink = "index.php?option=com_quick2cart&task=shipping.getShipView&extension_id=" . $extension_id;
					$app->redirect($plugConfigLink);
				}
			}
			// Table ordering
			$lists['order_Dir'] = $filter_order_Dir;
			$lists['order'] = $filter_order;
			$lists['filter_name'] = $filter_name;

			// Search filter
			$lists['search'] = $search;

			$this->lists = $lists;
			$this->items = $items;
			$this->pagination = $pagination;
		}
		elseif ($layout == 'list')
		{
			$this->form = '';
			$extension_id = $jinput->get('extension_id');

			if ($extension_id)
			{
				$this->form = $this->getShipPluginForm($extension_id);
			}
		}

		/*JToolBarHelper::title(JText::_('COM_QUICK2CART_SHIPM_SHIPPING_METHODS','quick2cart-logo');
		JToolBarHelper::title(JText::_('COM_QUICK2CART_SHIPM_SHIPPING_METHODS'));*/

		parent::display($tpl);
	}

	/**
	 * Function Ship Plugin Form
	 *
	 * @param   Integer  $extension_id  Extension ID
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	protected function getShipPluginForm($extension_id)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$qtcshiphelper = new qtcshiphelper;
		$plugName = $qtcshiphelper->getPluginDetail($extension_id);
		$import = PluginHelper::importPlugin('tjshipping', $plugName);

		$result = $app->triggerEvent('onTjShip_shipBuildLayout', array($jinput));

		if (!empty($result[0]))
		{
			return $this->form = $result[0];
		}

		return '';
	}
}
