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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Quick2cart component helper.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2CartHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   String  $vName  The name of the active view.
	 * @param   String  $queue  Queue.
	 *
	 * @return  void
	 *
	 * @since  1.6
	 */
	public static function addSubmenu($vName = '', $queue = '')
	{
		if (JVERSION < '4.0.0')
		{
			$params             = ComponentHelper::getParams('com_quick2cart');
			$multivendor_enable = $params->get('multivendor');

			\JHtmlSidebar::addEntry(Text::_('QTC_DASHBOARD'), 'index.php?option=com_quick2cart&view=dashboard', $vName == 'dashboard');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_STORES'), 'index.php?option=com_quick2cart&view=stores', $vName == 'stores');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_VENDOR'), 'index.php?option=com_tjvendors&view=vendors&client=com_quick2cart', $vName == 'vendors');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_quick2cart', $vName == 'categories');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_GLOBAL_ATTRIBUTES'), 'index.php?option=com_quick2cart&view=globalattributes', $vName == 'globalattributes');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_GLOBAL_ATTRIBUTE_SET'), 'index.php?option=com_quick2cart&view=attributesets', $vName == 'attributesets');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_GLOBAL_ATTRIBUTE_SET_MAPPING'), 'index.php?option=com_quick2cart&view=attributesetmapping', $vName == 'attributesetmapping');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_PRODUCTS'), 'index.php?option=com_quick2cart&view=products', $vName == 'products');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_ADMIN_PROMOTIONS'), 'index.php?option=com_quick2cart&view=promotions', $vName == 'promotions');
			\JHtmlSidebar::addEntry(Text::_('QTC_ORDERS'), 'index.php?option=com_quick2cart&view=orders', $vName == 'orders');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_SALES_REPORT'), 'index.php?option=com_quick2cart&view=salesreport', $vName == 'salesreport');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_COUNTRIES'), 'index.php?option=com_tjfields&view=countries&client=com_quick2cart', $vName == 'countries');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_REGIONS'), 'index.php?option=com_tjfields&view=regions&client=com_quick2cart', $vName == 'regions');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_ZONES'), 'index.php?option=com_quick2cart&view=zones', $vName == 'zones');

			if ($params->get('enableTaxtion', 0))
			{
				\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_TAXTRATES'), 'index.php?option=com_quick2cart&view=taxrates', $vName == 'taxrates');
				\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_ADMIN_TITLE_TAXPROFILES'), 'index.php?option=com_quick2cart&view=taxprofiles', $vName == 'taxprofiles');
			}

			if ($params->get('shipping', 0))
			{
				\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_SHIPPING'), 'index.php?option=com_quick2cart&view=shipping', $vName == 'shipping');
				\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_SHIPPING_PROFILES'), 'index.php?option=com_quick2cart&view=shipprofiles', $vName == 'shipprofiles');
			}

			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_LENGTHS'), 'index.php?option=com_quick2cart&view=lengths', $vName == 'lengths');
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_WEIGHTS'), 'index.php?option=com_quick2cart&view=weights', $vName == 'weights');

			$group_link = "index.php?option=com_tjfields&view=groups&client=com_quick2cart.product";
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_FORM_GROUP'), $group_link, $vName == 'group');

			$fields_link = "index.php?option=com_tjfields&view=fields&client=com_quick2cart.product";
			\JHtmlSidebar::addEntry(Text::_('COM_QUICK2CART_TITLE_FORM_FIELDS'), $fields_link, $vName == 'fields');

			if (!empty($multivendor_enable))
			{
				\JHtmlSidebar::addEntry(Text::_('REPORTS'), 'index.php?option=com_quick2cart&view=payouts', $vName == 'payouts');
			}

			if ($vName == 'categories')
			{
				ToolbarHelper::title(Text::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', Text::_('COM_QUICK2CART')), 'Quick2Cart-categories');
			}
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return JObject
	 *
	 * @since 1.6
	 */
	public static function getActions()
	{
		$user   = Factory::getUser();
		$result = new CMSObject();

		$assetName = 'com_quick2cart';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
