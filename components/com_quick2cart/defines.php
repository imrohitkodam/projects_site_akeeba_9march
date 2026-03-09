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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * This file define the icon set for admin view
 * */

$document = Factory::getDocument();

if (JVERSION < '5.0.0')
{
	HTMLHelper::stylesheet('media/techjoomla_strapper/vendors/font-awesome/css/font-awesome.min.css', $options);
}
else 
{
	HTMLHelper::stylesheet('media/techjoomla_strapper/vendors/font-awesome/css/font-awesome-6-5-1.min.css', $options);
}

$qtcParams      = ComponentHelper::getParams('com_quick2cart');
$currentBSViews = $qtcParams->get('bootstrap_version', "bs3");
$icon_color     = " icon-white ";

// Check if icon set is already defined or not
if (!defined('Q2C_ICON_IS_DEFINED_CEHCK'))
{
	define('Q2C_ICON_TRASH', " fa fa-trash ");
	define('Q2C_ICON_ENVELOPE', " fa fa-envelope ");
	define('Q2C_ICON_ARROW_RIGHT', " fa fa-arrow-right ");
	define('Q2C_ICON_ARROW_CHEVRON_RIGH', " fa fa-chevron-right ");
	define('Q2C_ICON_ARROW_CHEVRON_LEFT', " fa fa-chevron-left ");
	define('QTC_ICON_SEARCH', " fa fa-search ");
	define('Q2C_TOOLBAR_ICON_SETTINGS', " fa fa-cog ");
	define('QTC_ICON_PUBLISH', " fa fa-check ");
	define('QTC_ICON_REFRESH', " fa fa-refresh ");
	define('QTC_ICON_USER', " fa fa-user ");
	define('QTC_ICON_UNPUBLISH', " fa fa-minus ");
	define('QTC_ICON_INFO', " fa fa-info ");
	define('Q2C_ICON_HOME', " fa fa-home ");
	define('QTC_ICON_CHECKMARK', " fa fa-check ");
	define('QTC_ICON_MINUS', " fa fa-minus ");
	define('QTC_ICON_PLUS', " fa fa-plus ");
	define('QTC_ICON_EDIT', " fa fa-edit ");
	define('QTC_ICON_CART', " fa fa-shopping-cart ");
	define('QTC_ICON_BACK', " fa fa-arrow-left ");
	define('QTC_ICON_REMOVE', " fa fa-remove ");
	define('QTC_ICON_LIST', " fa fa-list ");
	define('Q2C_TOOLBAR_ICON_CART', " fa fa-shopping-cart ");
	define('Q2C_ICON_RIGHT_HAND', " fa fa-hand-o-right ");
	define('QTC_ICON_CALENDER', " fa fa-calendar ");
	define('Q2C_TOOLBAR_ICON_HOME', Q2C_ICON_HOME);
	define('Q2C_TOOLBAR_ICON_LIST', QTC_ICON_LIST);
	define('Q2C_TOOLBAR_ICON_PLUS', QTC_ICON_PLUS);
	define('Q2C_TOOLBAR_ICON_USERS', " fa fa-user ");
	define('Q2C_TOOLBAR_ICON_COUPONS', " fa fa-gift ");
	define('Q2C_TOOLBAR_ICON_PAYOUTS', " fa fa-briefcase ");
	define('Q2C_ICON_WHITECOLOR', "");
}
