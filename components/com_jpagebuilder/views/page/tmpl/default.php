<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/** @var CMSApplication */
$app = Factory::getApplication ();
$doc = Factory::getApplication ()->getDocument ();
$user = Factory::getApplication()->getIdentity();
$wa = $doc->getWebAssetManager();

$params = ComponentHelper::getParams ( 'com_jpagebuilder' );

if ($params->get ( 'fontawesome', 1 )) {
	$wa->registerAndUseStyle('jpagebuilder.faw5', 'components/com_jpagebuilder/assets/css/font-awesome-5.min.css');
	$wa->registerAndUseStyle('jpagebuilder.faw4shim', 'components/com_jpagebuilder/assets/css/font-awesome-v4-shims.css');
}

if (! $params->get ( 'disableanimatecss', 0 )) {
	$wa->registerAndUseStyle('jpagebuilder.animate', 'components/com_jpagebuilder/assets/css/animate.min.css');
	
}

if (! $params->get ( 'disablecss', 0 )) {
	$wa->registerAndUseStyle('jpagebuilder.pagebuildersite', 'components/com_jpagebuilder/assets/css/jpagebuilder.css');
	$wa->registerAndUseStyle('jpagebuilder.animate', 'components/com_jpagebuilder/assets/css/animate.min.css');
	JpagebuilderHelperSite::addContainerMaxWidth ();
}

$wa->useScript('jquery');
$wa->registerAndUseScript('jpagebuilder.jqueryparallax', 'components/com_jpagebuilder/assets/js/jquery.parallax.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], [], ['jquery']);
$wa->registerAndUseScript('jpagebuilder.pagebuilder', 'components/com_jpagebuilder/assets/js/jpagebuilder.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], ['defer' => true], ['jpagebuilder.jqueryparallax']);

$menus = $app->getMenu ();
$menu = $menus->getActive ();
$menuClassPrefix = '';
$showPageHeading = 0;

// check active menu item
if ($menu) {
	$menuClassPrefix = $menu->getParams ()->get ( 'pageclass_sfx' );
	$showPageHeading = $menu->getParams ()->get ( 'show_page_heading' );
	$menuHeading = $menu->getParams ()->get ( 'page_heading' );
}

$page = $this->item;

require_once JPATH_ROOT . '/components/com_jpagebuilder/editor/addonparser.php';
require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/addon.php';

$content = $page->text;

// Add page css
if (isset ( $page->css ) && $page->css) {
	$wa->addInlineStyle( $page->css );
}
?>

<div id="jpagebuilder" class="jpagebuilder <?php echo $menuClassPrefix; ?> page-<?php echo $page->id; ?>">

	<?php if ($showPageHeading) : ?>
		<div class="page-header">
			<h1 itemprop="name">
				<?php echo $menuHeading ? $menuHeading : $page->title; ?>
			</h1>
		</div>
	<?php endif; ?>

	<div class="page-content">
		<?php $pageName = 'page-' . $page->id; ?>
		<?php echo JpagebuilderAddonParser::viewAddons($content, 0, $pageName, ...$this->additionalAttributes); ?>

		<?php if ($this->canEdit) : ?>
			<a class="jpagebuilder-page-edit" href="<?php echo $this->checked_out ? $this->item->formLink : $this->item->link . '#'; ?>">
				<?php if (!$this->checked_out) : ?>
					<span class="fas fa-lock" area-hidden="true"></span> <?php echo Text::_('COM_JPAGEBUILDER_PAGE_CHECKED_OUT'); ?>
				<?php else : ?>
					<span class="fas fa-edit" area-hidden="true"></span> <?php echo Text::_('COM_JPAGEBUILDER_PAGE_EDIT'); ?>
				<?php endif; ?>
			</a>
		<?php endif; ?>
	</div>
</div>