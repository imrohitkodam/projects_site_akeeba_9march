<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

define('_JEXEC', 1);
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Initialize the joomla instance
 *
 * @return  void
 *
 * @since   2.6
 */
function initializeJOOMLA()
{
	// $siteDir = dirname(dirname(__FILE__));
	$siteDir = dirname(__FILE__);

	if (file_exists($siteDir . '/defines.php'))
	{
		include_once $siteDir . '/defines.php';
	}

	if (!defined('_JDEFINES'))
	{
		define('JPATH_BASE', $siteDir);
		require_once JPATH_BASE . '/includes/defines.php';
	}

	require_once JPATH_BASE . '/includes/framework.php';
	$app = Factory::getApplication('site');
	$app->initialise();
}

// Initialize Joomla
initializeJOOMLA();
$app    = Factory::getApplication();
$jinput = $app->input;

/* Call Quick2cart trigger
require_once  JPATH_SITE . '/components/com_quick2cart/helper.php';
$qtcmainHelper = new comquick2cartHelper;
*/


// Call the plugin and get the result
PluginHelper::importPlugin('system', "qtcamazon_easycheckout");
$results = $app->triggerEvent('onATP_cancelRedirect');
