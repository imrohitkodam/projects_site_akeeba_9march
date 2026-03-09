<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.defined('_JEXEC') or die();
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Helper\ModuleHelper;

$input = Factory::getApplication()->input;

// Check if component is installed
if (ComponentHelper::isEnabled('com_quick2cart', true) )
{
	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
	$tjStrapperPath = JPATH_SITE . '/media/techjoomla_strapper/tjstrapper.php';

	if (File::exists($tjStrapperPath))
	{
		require_once $tjStrapperPath;
		TjStrapper::loadTjAssets('com_quick2cart');
	}

	require ModuleHelper::getLayoutPath('mod_q2c_autosearch', $params->get('layout', 'default'));
}
