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
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

$lang = Factory::getLanguage();
$lang->load('mod_q2ccart', JPATH_SITE);

if (File::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
{
	// Define wrapper class
	if (!defined('Q2C_WRAPPER_CLASS'))
	{
		define('Q2C_WRAPPER_CLASS', "q2c-wrapper techjoomla-bootstrap");
	}

	$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

	if (!class_exists('comquick2cartHelper'))
	{
		JLoader::register('comquick2cartHelper', $path);
		JLoader::load('comquick2cartHelper');
	}

	// Load assets
	comquick2cartHelper::loadQuicartAssetFiles();

	BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_quick2cart/models');
	$quick2CartCartModel = BaseDatabaseModel::getInstance('cart', 'Quick2cartModel');

	$cartItem        = $quick2CartCartModel->getCartitems();
	$cartCount       = isset($cartItem) ? count($cartItem) : 0;
	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

	require ModuleHelper::getLayoutPath('mod_q2ccart', $params->get('layout', 'default'));
}
