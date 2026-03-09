<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/** @var CMSApplication */
$app = Factory::getApplication ();
$doc = Factory::getApplication ()->getDocument ();
$params = ComponentHelper::getParams ( 'com_jpagebuilder' );
$wa = $doc->getWebAssetManager();

$wa->addInlineScript ( 'var disableGoogleFonts = ' . $params->get ( 'disable_google_fonts', 0 ) . ';' );

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

$wa->registerAndUseStyle('jpagebuilder.canvas', 'components/com_jpagebuilder/assets/css/main.css');

if ( $params->get ( 'dark_mode', 1 )) {
	$wa->registerAndUseStyle('jpagebuilder.canvasdark', 'components/com_jpagebuilder/assets/css/main-dark.css');
}

$wa->useScript('jquery');
$wa->addInlineScript ( 'var pagebuilder_base="' . Uri::root () . '";' );
$wa->registerAndUseScript('jpagebuilder.jqueryparallax', 'components/com_jpagebuilder/assets/js/jquery.parallax.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], [], ['jquery']);
$wa->registerAndUseScript('jpagebuilder.pagebuilder', 'components/com_jpagebuilder/assets/js/jpagebuilder.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], [], ['jpagebuilder.jqueryparallax']);

$menus = $app->getMenu ();
$menu = $menus->getActive ();
$menuClassPrefix = '';
$showPageHeading = 0;

// check active menu item
if ($menu) {
	$menuClassPrefix = $menu->getParams ()->get ( 'pageclass_sfx' );
	$showPageHeading = $menu->getParams ()->get ( 'show_page_heading' );
	$menuheading = $menu->getParams ()->get ( 'page_heading' );
}

require_once JPATH_COMPONENT . '/builder/classes/base.php';
require_once JPATH_COMPONENT . '/builder/classes/config.php';
require_once JPATH_COMPONENT . '/builder/classes/addon.php';

$this->item = JpagebuilderApplicationHelper::preparePageData ( $this->item );

JpagebuilderBase::loadAddons ();
$addons_list = JpagebuilderConfig::$addons;

$addons_list = array_map ( function ($addon) {
	return JpagebuilderAddonsHelper::modernizeAddonStructure ( $addon );
}, $addons_list );

JpagebuilderBase::loadAssets ( $addons_list );
$addon_cats = JpagebuilderBase::getAddonCategories ( $addons_list );
$wa->addInlineScript ( 	'var addonsJSON=' . json_encode ( $addons_list ) . ';'  .
						'var addonsFromDB=' . json_encode ( JpagebuilderConfig::loadAddonList () ) . ';'  .
						'var addonCats=' . json_encode ( $addon_cats ) . ';'  .
						'var jpbVersion="' . JpagebuilderHelperSite::getVersion () . '";' );

if (! $this->item->text) {
	$wa->addInlineScript ( 'var initialState=[];' );
} else {
	$wa->addInlineScript ( 'var initialState=' . json_encode ( $this->item->text ) . ';' );
}

?>

<div id="jpagebuilder" class="jpagebuilder <?php echo $menuClassPrefix; ?> page-<?php echo $this->item->id; ?>">
	<div id="jpagebuilder-container"></div>
</div>

<style id="jpagebuilder-css" type="text/css">
	<?php echo $this->item->css; ?>
</style>

<?php
$wa->addInlineScript ('jQuery(function($) {
	$(document).on("click", "a", function(e){
		e.preventDefault();
	});

	$(document).on("focus", ".jp-editable-content, .jp-inline-editable-element", function(e){
		e.preventDefault();

		const strippedText = e.target?.getAttribute("data-stripped-text") || "";
		const maxWords = Number(e.target?.getAttribute("data-max-words")) || strippedText?.length;

		if (!maxWords || maxWords === 0) return;

		const addonName = e.target?.getAttribute("data-addon") || null;

		if (addonName !== "text-block") return;
		
		const isTruncated = e.target?.getAttribute("data-is-truncated") || "false";
		const fullText = e.target?.getAttribute("data-full-text") || "";

		const isShowBtn = e.target?.querySelector(".jpb-btn-container");
		
		if (isTruncated === "false") return;

		if (!isShowBtn) return;

		if (fullText === e.target.innerHTML) return;
		
		if (isTruncated === "true") {
			const fullText = e.target?.getAttribute("data-full-text") || "";
			e.target.innerHTML = fullText;
		}
	});

	$(document).on("blur", ".jp-editable-content, .jp-inline-editable-element", function(e){
		e.preventDefault();

		const addonName = e.target?.getAttribute("data-addon") || null;

		if (addonName !== "text-block") return;
		
		const isTruncated = e.target?.getAttribute("data-is-truncated") || "false";
		const isShowBtn = e.target?.querySelector(".jpb-btn-container");
		
		if (isTruncated === "false") return;

		if (isShowBtn) return;
		
		if (isTruncated === "true") {
			const strippedText = e.target?.getAttribute("data-stripped-text") || "";
			const maxWords = Number(e.target?.getAttribute("data-max-words")) || strippedText?.length;
			const truncatedText = strippedText.split(" ").slice(0, maxWords).join(" ");
			const actionText = e.target?.getAttribute("data-action-text") || "";

			if (!maxWords || maxWords === 0) return;

			if (maxWords >= strippedText.split(" ").length) return;

			e.target.innerHTML = `
			${truncatedText}
			<div class="jpb-btn-container jpb-content-truncation-show"><div role="button" class="jpb-btn-show-more">${actionText}</div></div>
			`
		}
	});

	$(document).on("click", ".jp-editable-content, .jp-inline-editable-element", function(e){
		e.preventDefault();
		var ids = $(this).attr("id");
		$(this).attr("contenteditable", true);
		$(this).focus();
	});

	$(document).find(".jpb-addon-countdown .jpb-countdown-timer").each((function() {
 			var e = $(this), i = e.data("date") + " " + e.data("time");
			e.countdown(i, (function(i) {
			$(this).html(i.strftime(\'<div class="jpb-countdown-days jpb-col-xs-6 jpb-col-sm-3 jpb-text-center"><span class="jpb-countdown-number">%-D</span><span class="jpb-countdown-text">%!D: \' + Joomla.JText._("COM_JPAGEBUILDER_DAY") + "," + Joomla.JText._("COM_JPAGEBUILDER_DAYS") + \';</span></div><div class="jpb-countdown-hours jpb-col-xs-6 jpb-col-sm-3 jpb-text-center"><span class="jpb-countdown-number">%H</span><span class="jpb-countdown-text">%!H: \' + Joomla.JText._("COM_JPAGEBUILDER_HOUR") + "," + Joomla.JText._("COM_JPAGEBUILDER_HOURS") + \';</span></div><div class="jpb-countdown-minutes jpb-col-xs-6 jpb-col-sm-3 jpb-text-center"><span class="jpb-countdown-number">%M</span><span class="jpb-countdown-text">%!M:\' + Joomla.JText._("COM_JPAGEBUILDER_MINUTE") + "," + Joomla.JText._("COM_JPAGEBUILDER_MINUTES") + \';</span></div><div class="jpb-countdown-seconds jpb-col-xs-6 jpb-col-sm-3 jpb-text-center"><span class="jpb-countdown-number">%S</span><span class="jpb-countdown-text">%!S:\' + Joomla.JText._("COM_JPAGEBUILDER_SECOND") + "," + Joomla.JText._("COM_JPAGEBUILDER_SECONDS") + ";</span></div>")).on("finish.countdown", (function() {
			$(this).html(\'<div class="jpb-countdown-finishedtext-wrap jpb-col-xs-12 jpb-col-sm-12 jpb-text-center"><h3 class="jpb-countdown-finishedtext">\' + e.data("finish-text") + "</h3></div>")
					}
				))
			}
		))
	}));
});');
