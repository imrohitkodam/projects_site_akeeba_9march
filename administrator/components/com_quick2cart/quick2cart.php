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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

// Load backend language file for shared views in FE/BE
$app  = Factory::getApplication();
$lang = $app->getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

if (!class_exists('comquick2cartHelper'))
{
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_quick2cart'))
{
	$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

	return;
}

comquick2cartHelper::defineIcons("ADMIN");

// Define wrapper class
define('Q2C_WRAPPER_CLASS', "q2c-wrapper");

if (JVERSION < '4.0.0')
{
    // Tabstate
    HTMLHelper::_('behavior.tabstate');
}

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_quick2cart/helpers/products.php';
require_once JPATH_SITE . '/components/com_quick2cart/helper.php';
require_once JPATH_SITE . '/components/com_quick2cart/helpers/storeHelper.php';
require_once JPATH_SITE . '/components/com_quick2cart/helpers/product.php';
require_once JPATH_SITE . '/components/com_quick2cart/helpers/zoneHelper.php';
require_once JPATH_SITE . '/components/com_quick2cart/helpers/taxHelper.php';
require_once JPATH_SITE . '/components/com_quick2cart/helpers/qtcshiphelper.php';

$jinput    = $app->input;
$params    = ComponentHelper::getParams('com_quick2cart');
$bsVersion = $params->get('bootstrap_version', '', 'STRING');

if (empty($bsVersion))
{
	$bsVersion = (JVERSION > '4.0.0') ? 'bs5' : 'bs3';
}

define('QUICK2CART_LOAD_BOOTSTRAP_VERSION', $bsVersion);

// Load assets
comquick2cartHelper::loadQuicartAssetFiles();

// When All products menu not present.
$multivendor_enable = $params->get('multivendor');

if (!empty($multivendor_enable))
{
	$link  = 'index.php?option=com_quick2cart&view=category';
	$db    = Factory::getDBO();
	$query = "SELECT id FROM #__menu WHERE link LIKE '%" . $link . "%' AND published = 1 LIMIT 1";
	$db->setQuery($query);
	$items = $db->loadResult();

	if (empty($items))
	{
		$link    = Text::_('COM_QUICK2CART_ALLPRODUCTSMENU');
		$not_msg = Text::sprintf('VANITY_REQ_MENU_WARNING', $link);

		// Get messages in queue
		$messages = $app->getMessageQueue();

		// Flag for duplicate message
		$mgsExists = 0;

		// If we have messages
		if (is_array($messages) && count($messages))
		{
			// Check each message for the one we want
			foreach ($messages as $message)
			{
				if ($message['message'] == $not_msg)
				{
					$mgsExists = 1;
				}
			}
		}

		// Enqueu message only if message is not present
		if ($mgsExists == 0)
		{
			$app->enqueueMessage($not_msg, 'error');
		}
	}
}

HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/artificiers.min.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/quick2cart.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/quick2cart.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/q2c-tables.css');

// Include dependancies
$controller = BaseController::getInstance('Quick2cart', $config = array());
$controller->execute($app->input->get('task'));
$controller->redirect();
