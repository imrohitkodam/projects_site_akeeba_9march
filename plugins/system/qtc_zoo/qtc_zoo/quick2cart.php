<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

HTMLHelper::_('bootstrap.renderModal', 'a.modal');
$html = '';
$client = "com_zoo";

$jinput = Factory::getApplication()->input;
$itemid = $jinput->get('cid', array(), "ARRAY");
$pid = $itemid[0];

// Load helper file if not exist

if (! class_exists('comquick2cartHelper'))
{
	// Require_once $path;
	$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

$comquick2cartHelper = new comquick2cartHelper;
$path = $comquick2cartHelper->getViewpath('attributes', '', "ADMIN", "SITE");
ob_start();

include $path;
$html = ob_get_contents();
ob_end_clean();

echo $html;
