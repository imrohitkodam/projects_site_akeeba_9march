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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

// Load backend language file for shared views in FE/BE
$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

if (!class_exists('comquick2cartHelper'))
{
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

// Load assets
comquick2cartHelper::loadQuicartAssetFiles();
comquick2cartHelper::defineIcons('SITE');

comquick2cartHelper::getLanguageConstantForJs();

$path = JPATH_SITE . '/components/com_quick2cart/helpers/storeHelper.php';

if (!class_exists('storeHelper'))
{
	JLoader::register('storeHelper', $path);
	JLoader::load('storeHelper');
}

$path = JPATH_SITE . '/components/com_quick2cart/helpers/zoneHelper.php';

if (!class_exists('zoneHelper'))
{
	JLoader::register('zoneHelper', $path);
	JLoader::load('zoneHelper');
}

$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

if (!class_exists('productHelper'))
{
	JLoader::register('productHelper', $path);
	JLoader::load('productHelper');
}

$path = JPATH_SITE . '/components/com_quick2cart/helpers/taxHelper.php';

if (!class_exists('taxHelper'))
{
	JLoader::register('taxHelper', $path);
	JLoader::load('taxHelper');
}

// Load ship helper
$path = JPATH_SITE . '/components/com_quick2cart/helpers/qtcshiphelper.php';

if (!class_exists('qtcshiphelper'))
{
	JLoader::register('qtcshiphelper', $path);
	JLoader::load('qtcshiphelper');
}

require_once JPATH_COMPONENT . '/controller.php';
JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');

$input = Factory::getApplication()->input;

// Task for checking store releated view :checking authorization starts.
$view   = $input->get('view', 'category', 'STRING');
$input->set('view', $view);

$layout              = $input->get('layout', 'default');
$ck                  = $view . "_" . $layout;
$comquick2cartHelper = new comquick2cartHelper;
$path                = JPATH_SITE . '/components/com_quick2cart/authorizeviews.php';
$params              = ComponentHelper::getParams('com_quick2cart');

$bsVersion = $params->get('bootstrap_version', '', 'STRING');

if (empty($bsVersion))
{
	$bsVersion = (JVERSION > '4.0.0') ? 'bs5' : 'bs3';
}

define('QUICK2CART_LOAD_BOOTSTRAP_VERSION', $bsVersion);

include $path;

$store_releatedview = 0;

foreach ($rolearray as $arr)
{
	if (in_array($ck, $arr))
	{
		$store_releatedview = 1;
		break;
	}
}

if ($store_releatedview == 1)
{
	$user = Factory::getUser();

	if (empty($user->id))
	{
		?>
			<div class="techjoomla-bootstrap" >
				<div class="well well-small" >
					<div class="alert alert-error alert-danger">
						<span ><?php echo Text::_('QTC_LOGIN'); ?> </span>
					</div>
				</div>
			</div>
			<!-- eoc techjoomla-bootstrap -->
		<?php
			return false;
	}

	$comquick2cartHelper = new comquick2cartHelper;
	$authority = $comquick2cartHelper->store_authorize($ck);

	if (empty($authority))
	{
		?>
			<div class="techjoomla-bootstrap" >
				<div class="well well-small" >
					<div class="alert alert-error alert-danger">
						<span ><?php echo Text::_('QTC_VIOLATING_UR_ROLE'); ?> </span>
					</div>
				</div>
			</div>
			<!-- eoc techjoomla-bootstrap -->
		<?php
		return false;
	}
}

$result = $comquick2cartHelper->displaySocialToolbar();

if ($params['multivendor'] == 0)
{
	$result = $comquick2cartHelper->isAllowedToVisitView();

	if ($result == false)
	{
		return false;
	}
}

// Global icon constants.
HTMLHelper::_('bootstrap.tooltip');

if (JVERSION < '4.0.0')
{
    // Tabstate
    HTMLHelper::_('behavior.tabstate');
    HTMLHelper::_('behavior.framework');
}

$helperPath = JPATH_SITE . '/components/com_quick2cart/helpers/reports.php';

if (!class_exists('reportsHelper'))
{
	JLoader::register('reportsHelper', $helperPath);
	JLoader::load('reportsHelper');
}

$document = Factory::getDocument();

// Frontend css
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/artificiers.min.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/quick2cart.css');

// Responsive tables
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/q2c-tables.css');

// Include dependancies
$controller = BaseController::getInstance('Quick2cart');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
