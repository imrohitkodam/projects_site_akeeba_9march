<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;

$doc = Factory::getApplication ()->getDocument ();

if (! class_exists ( 'JpagebuilderHelperSite' )) {
	require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/helper.php';
}

$wa = $doc->getWebAssetManager();
$wa->registerAndUseScript('jpagebuilder.bundle', 'administrator/components/com_jpagebuilder/assets/js/editor/jpagebuilder.min.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], ['defer'=>true]);
$wa->registerAndUseScript('jpagebuilder.vendor', 'administrator/components/com_jpagebuilder/assets/js/editor/framework.min.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], ['defer'=>true]);
$wa->addInlineScript('Joomla.pagebuilderBase = "' . Uri::root () . '"');

$wa->registerAndUseStyle('jpagebuilder.main', 'administrator/components/com_jpagebuilder/assets/css/main.css');

$params = ComponentHelper::getParams ( 'com_jpagebuilder' );
if ( $params->get ( 'dark_mode', 1 )) {
	$wa->addInlineScript ( 'var builderDarkMode = 1;' );
	$wa->registerAndUseStyle('jpagebuilder.canvasdark', 'administrator/components/com_jpagebuilder/assets/css/main-dark.css');
}

if (! \class_exists ( 'JpagebuilderHelper' )) {
	require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/helpers/jpagebuilder.php';
}

?>

<div id="pagebuilder-backend-editor"></div>