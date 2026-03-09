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

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Plugin\PluginHelper;

if (File::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
{
	$app           = Factory::getApplication();
	$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

	if (!class_exists('comquick2cartHelper'))
	{
		JLoader::register('comquick2cartHelper', $path);
		JLoader::load('comquick2cartHelper');
	}

	// Load assets
	comquick2cartHelper::loadQuicartAssetFiles();

	$doc = Factory::getDocument();
	$lang = Factory::getLanguage();
	$lang->load('mod_quick2cart', JPATH_SITE);

	JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
	$model = new Quick2cartModelcart;
	$cart  = $model->getCartitems();

	// Check for promotions
	$path = JPATH_SITE . '/components/com_quick2cart/helpers/promotion.php';

	if (!class_exists('PromotionHelper'))
	{
		JLoader::register('PromotionHelper', $path);
		JLoader::load('PromotionHelper');
	}

	$beforecartmodule = '';
	$aftercartdisplay = '';
	$aftercartdisplay = '';

	$promotionHelper = new PromotionHelper;
	$coupon          = $promotionHelper->getSessionCoupon();
	$promotions      = $promotionHelper->getCartPromotionDetail($cart, $coupon);

	// Trigger onBeforeCartModule
	PluginHelper::importPlugin('system');
	$result = $app->triggerEvent('onBeforeQ2cCartModuleDisplay');

	if (!empty($result))
	{
		$beforecartmodule .= $result[0];
	}

	// Depricated start
	$result = $app->triggerEvent('onBeforeQ2cCartModule');

	if (!empty($result))
	{
		$beforecartmodule .= $result[0];
	}
	// Depricated End

	// Trigger onAfterQ2cCartModule
	PluginHelper::importPlugin('system');
	$result = $app->triggerEvent('onAfterQ2cCartModuleDisplay');

	if (!empty($result))
	{
		$aftercartdisplay .= $result[0];
	}

	// Depricated start
	$result = $app->triggerEvent('onAfterQ2cCartModule');

	if (!empty($result))
	{
		$aftercartdisplay .= $result[0];
	}
	// Depricated End

	// Define wrapper class
	if (!defined('Q2C_WRAPPER_CLASS'))
	{
		define('Q2C_WRAPPER_CLASS', "q2c-wrapper");
	}

	// Bootstrap tooltip and chosen js
	HTMLHelper::_('bootstrap.tooltip');
	HTMLHelper::_('behavior.multiselect');

	$moduleParams    = $params;
	$hideOnCartEmpty = $moduleParams->get('hideOnCartEmpty', 0);
	$ckout_text      = $moduleParams->get('checkout_text', '');
	$ckout_text      = trim($ckout_text);
	$moduleclass_sfx = $moduleParams->get('moduleclass_sfx');
	require ModuleHelper::getLayoutPath('mod_quick2cart', $params->get('layout', 'default'));
}
