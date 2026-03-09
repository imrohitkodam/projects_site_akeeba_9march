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
	JLoader::import('components.com_quick2cart.helper', JPATH_SITE);
	JLoader::import('components.com_quick2cart.helpers.product', JPATH_SITE);

	// Load assets
	comquick2cartHelper::loadQuicartAssetFiles();

	// LOAD LANGUAGE FILES
	$doc  = Factory::getDocument();
	$lang = Factory::getLanguage();
	$lang->load('mod_qtcproductdisplay', JPATH_SITE);

	// GETTING MODULE PARAMS
	$prodLimit     = $params->get('limit', 2);
	$module_mode   = $params->get('module_mode', 'qtc_featured');
	$productHelper = new productHelper;

	if (!empty($module_mode))
	{
		switch ($module_mode)
		{
			case 'qtc_featured';
				$target_data = $productHelper->getAllFeturedProducts('', '', $prodLimit);
				break;

			case 'qtc_recentlyAdded';
				$target_data = $productHelper->getNewlyAdded_products($prodLimit);
				break;

			case 'qtc_recentlyBought';
				$target_data = $productHelper->getRecentlyBoughtproducts($prodLimit);
				break;

			case 'qtc_topSeller';
				$target_data = $productHelper->getTopSellerProducts('', '', $prodLimit);
				break;
		}
	}

	if (empty($target_data))
	{
		return false;
	}

	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

	require ModuleHelper::getLayoutPath('mod_qtcproductdisplay', $params->get('layout', 'default', 'string'));
}
