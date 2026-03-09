<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/helpers/loader.php');
JpagebuilderLoader::setup();
JpagebuilderLoader::register('JpagebuilderHelperSite', JPATH_SITE . '/components/com_jpagebuilder/helpers/helper.php');
require_once JPATH_ROOT . '/components/com_jpagebuilder/editor/addonparser.php';

if (! class_exists ( 'JpagebuilderAddon' )) {
	require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/addon.php';
}
$app = Factory::getApplication ();
$doc = $app->getDocument ();
$input = $app->getInput ();
$component_params = ComponentHelper::getParams ( 'com_jpagebuilder' );
$wa = $doc->getWebAssetManager ();

if ($params->get ( 'fontawesome', 1 )) {
	$wa->registerAndUseStyle('jpagebuilder.faw5', 'components/com_jpagebuilder/assets/css/font-awesome-5.min.css');
	$wa->registerAndUseStyle('jpagebuilder.faw4shim', 'components/com_jpagebuilder/assets/css/font-awesome-v4-shims.css');
}

if (! $params->get ( 'disableanimatecss', 0 )) {
	$wa->registerAndUseStyle('jpagebuilder.animate', 'components/com_jpagebuilder/assets/css/animate.min.css');
	
}

if (! $params->get ( 'disablecss', 0 )) {
	$wa->registerAndUseStyle('jpagebuilder.pagebuildersite', 'components/com_jpagebuilder/assets/css/jpagebuilder.css');
	JpagebuilderHelperSite::addContainerMaxWidth ();
}

$wa->useScript('jquery');
$wa->registerAndUseScript('jpagebuilder.jqueryparallax', 'components/com_jpagebuilder/assets/js/jquery.parallax.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], [], ['jquery']);
$wa->registerAndUseScript('jpagebuilder.pagebuilder', 'components/com_jpagebuilder/assets/js/jpagebuilder.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], ['defer' => true], ['jpagebuilder.jqueryparallax']);
?>
<div class="mod-jpagebuilder <?php echo $moduleclass_sfx ?> jpagebuilder" data-module_id="<?php echo $module->id; ?>">
	<div class="page-content">
		<?php echo JpagebuilderAddonParser::viewAddons(json_decode($data), 0, 'module'); ?>
	</div>
</div>