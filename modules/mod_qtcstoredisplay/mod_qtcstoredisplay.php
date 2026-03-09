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

if (File::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
{
	$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

	if (!class_exists('comquick2cartHelper'))
	{
		JLoader::register('comquick2cartHelper', $path);
		JLoader::load('comquick2cartHelper');
	}

	$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

	if (!class_exists('ProductHelper'))
	{
		JLoader::register('ProductHelper', $path);
		JLoader::load('ProductHelper');
	}

	$path = JPATH_SITE . '/components/com_quick2cart/helpers/storeHelper.php';

	if (!class_exists('StoreHelper'))
	{
		JLoader::register('StoreHelper', $path);
		JLoader::load('StoreHelper');
	}

	// Load assets
	comquick2cartHelper::loadQuicartAssetFiles();

	// LOAD LANGUAGE FILES
	$doc  = Factory::getDocument();
	$lang = Factory::getLanguage();
	$lang->load('mod_qtcstoredisplay', JPATH_SITE);

	// GETTING MODULE PARAMS
	$prodLimit = $params->get('limit');

	// Allow to display all stores
	if ($prodLimit == -1)
	{
		$prodLimit = '';
	}

	$productHelper         = new ProductHelper;
	$module_mode           = $params->get('module_mode', 'qtc_latestStore');
	$qtc_modViewType       = $params->get('qtc_modViewType', 'qtc_blockView');
	$qtc_mod_scroll_height = $params->get('scroll_height');

	if (!empty($module_mode))
	{
		switch ($module_mode)
		{
			case 'qtc_latestStore';
				$target_data = $productHelper->getLatestStore(1, $prodLimit);
				break;

			case 'qtc_bestSellerStore';
				$target_data = $productHelper->getTopSellerStore($prodLimit);
				break;

			case 'qtc_storeList';
				// LOAD ALL STORE
				$storeHelper = new storeHelper;
				$target_data = $storeHelper->getStoreList(1, $prodLimit);
				break;
		}
	}

	if (empty($target_data))
	{
		return false;
	}

	require ModuleHelper::getLayoutPath('mod_qtcstoredisplay', $params->get('layout', 'default'));
}
