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

	// Load assets
	comquick2cartHelper::loadQuicartAssetFiles();

	// LOAD LANGUAGE FILES
	$doc  = Factory::getDocument();
	$lang = Factory::getLanguage();
	$lang->load('mod_qtc_categorypin', JPATH_SITE);
	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

	require ModuleHelper::getLayoutPath('mod_qtc_categorypin', $params->get('layout', 'default'));
}
