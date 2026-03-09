<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Component\ComponentHelper;

require_once JPATH_COMPONENT . '/builder/classes/base.php';
require_once JPATH_COMPONENT . '/builder/classes/config.php';
require_once JPATH_COMPONENT . '/builder/classes/addon.php';
require_once (JPATH_ROOT . '/components/com_jpagebuilder/helpers/route.php');

$doc = Factory::getApplication ()->getDocument ();
$app = Factory::getApplication ();
$wa = $doc->getWebAssetManager();
$params = ComponentHelper::getParams ( 'com_jpagebuilder' );

if (! $params->get ( 'enable_frontend_editing', 1 )) {
	die ( "The frontend editing is disabled." );
}

$wa->addInlineScript ( 'var disableGoogleFonts = ' . $params->get ( 'disable_google_fonts', 0 ) . ';' );
$wa->addInlineScript ( 'var addItemsFirst = ' . $params->get ( 'add_items_first', 0 ) . ';' );
$wa->addInlineScript ( 'var builderDarkMode = ' . $params->get ( 'dark_mode', 1 ) . ';' );

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

$wa->registerAndUseStyle('jpagebuilder.editor', 'components/com_jpagebuilder/assets/css/editor.css');

if ( $params->get ( 'dark_mode', 1 )) {
	$wa->registerAndUseStyle('jpagebuilder.canvasdark', 'components/com_jpagebuilder/assets/css/main-dark.css');
}

$wa->useScript('jquery');
$wa->addInlineScript ( 'var pagebuilder_base="' . Uri::root () . '";' );

$wa->registerAndUseScript('jpagebuilder.csslint', 'components/com_jpagebuilder/assets/js/csslint.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], []);
$wa->registerAndUseScript('jpagebuilder.actions', 'components/com_jpagebuilder/assets/js/actions.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], []);

$wa->registerAndUseScript ( 'jpagebuilder.framework', 'components/com_jpagebuilder/assets/js/framework.js', [
		'version' => JpagebuilderHelperSite::getVersion ( true )
], [
		'defer' => true
] );
$wa->registerAndUseScript ( 'jpagebuilder.editor', 'components/com_jpagebuilder/assets/js/editor.js', [
		'version' => JpagebuilderHelperSite::getVersion ( true )
], [
		'defer' => true
], [
		'jpagebuilder.framework'
] );

if ($this->item->extension === 'com_content' && $this->item->extension_view === 'article') {
	$extension_view = 'article';
} else {
	$extension_view = 'page';
}

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

JpagebuilderBase::loadAddons ();

$fa_icon_list = JpagebuilderBase::getIconList (); // Icon List
$accessLevels = JpagebuilderBase::getAccessLevelList (); // Access Levels
$article_cats = JpagebuilderBase::getArticleCategories (); // Article Categories
$moduleAttr = JpagebuilderBase::getModuleAttributes (); // Module Positions and Module List
$rowSettings = JpagebuilderBase::getRowGlobalSettings (); // Row Settings Attributes
$columnSettings = JpagebuilderBase::getColumnGlobalSettings (); // Column Settings Attributes
$global_attributes = JpagebuilderBase::addonOptions ();
$user = Factory::getApplication()->getIdentity();

$userPermissions = JpagebuilderBase::getUserPermissions ();

$addons_list = JpagebuilderConfig::$addons;

$globalDefaults = [ ];
$globalSettingsGroups = [ 
		'style',
		'advanced',
		'interaction'
];

foreach ( $globalSettingsGroups as $groupName ) {
	$globalDefaults = array_merge ( $globalDefaults, JpagebuilderEditorUtils::extractSettingsDefaultValues ( $global_attributes [$groupName] ) );
}

$addons_list = array_map ( function ($addon) use ($globalDefaults) {
	$modernAddon = JpagebuilderAddonsHelper::modernizeAddonStructure ( $addon );
	$addonDefaults = JpagebuilderEditorUtils::extractSettingsDefaultValues ( $modernAddon ['settings'] );
	$modernAddon ['default'] = array_merge ( $globalDefaults, $addonDefaults );

	return $modernAddon;
}, $addons_list );

foreach ( $addons_list as &$addon ) {
	if (! isset ( $addon ['category'] ) || empty ( $addon ['category'] )) {
		$addon ['category'] = 'General';
	}

	$addon_name = preg_replace ( '/^jp_/i', '', $addon ['addon_name'] );
	$class_name = JpagebuilderApplicationHelper::generateSiteClassName ( $addon_name );

	if (method_exists ( $class_name, 'getFrontendEditor' )) {
		$addon ['js_template'] = true;
	}
}

unset ( $addon );

$rowDefaultValue = JpagebuilderEditorUtils::getSectionSettingsDefaultValues ();
$rowSettings ['default'] = $rowDefaultValue;

$columnDefaultValue = JpagebuilderEditorUtils::getColumnSettingsDefaultValues ();
$columnSettings ['default'] = $columnDefaultValue;

JpagebuilderBase::loadAssets ( $addons_list );

$addon_cats = JpagebuilderBase::getAddonCategories ( $addons_list );

// Global Attributes
$wa->addInlineScript ( 	'var addonsJSON=' . json_encode ( $addons_list ) . ';' .
						'var addonsFromDB=' . json_encode ( JpagebuilderConfig::loadAddonList () ) . ';' .
						'var addonCats=' . json_encode ( $addon_cats ) . ';' .
						'var globalAttr=' . json_encode ( $global_attributes ) . ';' .
						'var faIconList=' . json_encode ( $fa_icon_list ) . ';' .
						'var accessLevels=' . json_encode ( $accessLevels ) . ';' .
						'var articleCats=' . json_encode ( $article_cats ) . ';' .
						'var moduleAttr=' . json_encode ( $moduleAttr ) . ';' .
						'var rowSettings=' . json_encode ( $rowSettings ) . ';' .
						'var colSettings=' . json_encode ( $columnSettings ) . ';' .
						'var jpbVersion="' . JpagebuilderHelperSite::getVersion () . '";' .
						'var userPermissions=Object.freeze(' . json_encode ( $userPermissions ) . ');' );

$app = Factory::getApplication ();
$cParams = $app->getParams ( 'com_jpagebuilder' );

// Media
$mediaParams = ComponentHelper::getParams ( 'com_media' );
$wa->addInlineScript ( 	'var jpbMediaPath=\'/' . $mediaParams->get ( 'file_path', 'files' ) . '\';' .
						'var jpbSvgShape=' . json_encode ( JpagebuilderHelperSite::getSvgShapes () ) . ';' .
						'var extensionView=\'' . $extension_view . '\';' .
						'var is_ai_enabled=' . $cParams->get ( 'enable_ai', 0, 'int' ) . ';' );

if (! $this->item->text) {
	$wa->addInlineScript ( 'var initialState=[];' );
} else {
	$wa->addInlineScript ( 'var initialState=' . json_encode ( $this->item->text ) . ';' );
}

$languageCode = '';

if (Multilanguage::isEnabled ()) {
	if ($this->item->language != '*') {
		$langCode = explode ( '-', $this->item->language );
		$languageCode = '&lang=' . $langCode [0];
	}
}

$previewUrl = JpagebuilderHelperRoute::getPageRoute($this->item->id, $this->item->language);
?>

<div id="jpagebuilder" class="jpagebuilder <?php echo $menuClassPrefix; ?> page-<?php echo $this->item->id; ?>" data-pageid="<?php echo $this->item->id; ?>" data-pageurl="<?php echo $previewUrl; ?>">
	<form action="<?php echo Route::_('index.php?option=com_jpagebuilder&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate page-builder-form" style="display: none;">
		<div id="page-options">
			<?php $fieldsets = $this->form->getFieldsets(); ?>

			<div class="jpagebuilder-form-group-toggler active">
				<span>Basic <span class="fa fa-chevron-right"></span></span>
				<div>
					<?php foreach ($this->form->getFieldset('basic') as $key => $field) : ?>
						<div class="jpagebuilder-form-group">
							<?php echo $field->label; ?>
							<?php echo $field->input; ?>
							<?php if ($field->getAttribute('desc')) : ?>
								<span class="jpagebuilder-form-help"><?php echo Text::_($field->getAttribute('desc')); ?></span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<input type="hidden" id="form_task" name="task" value="page.apply" />
		<?php
		$app = Factory::getApplication();
		$input = $app->getInput();
		$Itemid = $input->get('Itemid', 0, 'int');

		$extension = $input->get('extension', '', 'string');

		$url = Route::_('index.php?option=com_jpagebuilder&view=page&id=' . $this->item->id . '&Itemid=' . $Itemid);
		$root = Uri::base();
		$root = new Uri($root);
		$pageUrl = $root->getScheme() . '://' . $root->getHost() . $url;

		$iframeUrl = Uri::root() . 'index.php?option=com_jpagebuilder&view=form&id=' . $this->item->id . '&layout=edit-iframe&Itemid=' . $Itemid . $languageCode;

		if ($extension === 'mod_jpagebuilder')
		{
			$iframeUrl .= '&tmpl=component';
		}
		?>
		<input type="hidden" id="return_page" name="return_page" value="<?php echo base64_encode($pageUrl); ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>

	<iframe name="jpagebuilder-view" id="jpagebuilder-view" class="builder-iframe-laptop" data-url="<?php echo $iframeUrl; ?>"></iframe>

	<div id="jpagebuilder-main"></div>

	<!-- Never delete this element -->
	<div id="builder-dnd-provider-dom"></div>
</div>
<script>
	window.builderDefaultDevice = 'xl';
</script>

<?php
$mediaQueries = [
	'xl' => '@media ( max-width: 1399.98px )',
	'lg' => '@media ( max-width: 1199.98px )',
	'md' => '@media ( max-width: 991.98px )',
	'sm' => '@media ( max-width: 767.98px )',
	'xs' => '@media ( max-width: 575.98px )',
];
$queryString = '';

foreach ($mediaQueries as $size => $media)
{
	$queryString .= $media . ' {';
	$queryString .= '
	
	#{{ addonId }}{
		<# if(_.isObject(data.global_border_width)){ #>
			<# if (!_.isEmpty(data.global_border_width.' . $size . ')) { #>
			border-width: {{data.global_border_width.' . $size . '}}px;
			<# } #>
		<# } #>
		
		<# if(_.isObject(borderRadius)){ #>
			<# if (!_.isEmpty(borderRadius.' . $size . ')) { #>
			border-radius: {{ borderRadius.' . $size . ' }}px;
			<# } #>
		<# }
		if(_.isObject(padding)){
		#>
			{{{ padding.' . $size . ' }}}
		<# } #>

	}
	#jpagebuilder div#addon-wrap-{{ data.id }} {
		<# if(data.global_custom_position && data.global_seclect_position){ #>
			<# if(_.isObject(data.global_addon_position_top)) { #>
				top:{{data.global_addon_position_top.' . $size . '}}{{unitTop}};
			<# }

			if(_.isObject(data.global_addon_position_left)) {
			#>
				left:{{data.global_addon_position_left.' . $size . '}}{{unitLeft}};
			<# }
		}
		if(_.isObject(margin)){
		#>
			{{{ margin.' . $size . ' }}}
		<# }
		if(typeof data.use_global_width !== "undefined" && data.use_global_width && typeof data.global_width !== "undefined" && _.isObject(data.global_width)) {
		#>
			width: {{data.global_width.' . $size . '}}%;
		<# } #>
	}
	
	<# if (!_.isEmpty(data.title)){ #>
		#jpb-addon-{{ data.id }} .jpb-addon-title{
			<# if(_.isObject(data.title_fontsize)){ #>
				font-size: {{ data.title_fontsize.' . $size . ' }}px;
				line-height: {{ data.title_fontsize.' . $size . ' }}px;
			<# } #>
			<# if(_.isObject(data.title_lineheight)){ #>
				line-height: {{ data.title_lineheight.' . $size . ' }}px;
			<# } else { #>
				line-height: {{ data.title_lineheight }}px;
			<# } #>
			<# if(_.isObject(data.title_margin_top)){ #>
				margin-top: {{ data.title_margin_top.' . $size . ' }}px;
			<# } #>
			<# if(_.isObject(data.title_margin_bottom)){ #>
				margin-bottom: {{ data.title_margin_bottom.' . $size . ' }}px;
			<# } #>
		}
	<# } #>
	';

	$queryString .= '}';
}

foreach ( $addons_list as $addon ) {
	$addon_name = $addon ['addon_name'];
	$addon_name = preg_replace ( '/^jp_/i', '', $addon ['addon_name'] );
	// $class_name = 'JpagebuilderAddon' . ucfirst($addon_name);
	$class_name = JpagebuilderApplicationHelper::generateSiteClassName ( $addon_name );
	;

	if (method_exists ( $class_name, 'getFrontendEditor' )) {
?>
		<script id="jpb-tmpl-addon-<?php echo $addon_name; ?>" type="x-tmpl-lodash">
			<#
			var addonClass = 'clearfix';
			var addonAttr = '';
			var addonId = 'jpb-addon-'+data.id;
			var addonName = '<?php echo $addon_name; ?>';

			var textColor = data.global_text_color || '';
			var linkColor = data.global_link_color || '';
			var linkHoverColor = data.global_link_hover_color || '';
			var backgroundRepeat = data.global_background_repeat || '';
			var backgroundSize = data.global_background_size || '';
			var backgroundAttachment = data.global_background_attachment || '';
			var backgroundPosition = data.global_background_position || '';
			var modern_font_style = false;
			var title_font_style = data.title_fontstyle || "";

			var backgroundColor = '';
			if(data.global_background_color){
				backgroundColor = data.global_background_color;
			}

			var backgroundImage = '';
			var globalBgImg = {}
			if (typeof data.global_background_image !== "undefined" && typeof data.global_background_image.src !== "undefined") {
				globalBgImg = data.global_background_image
			} else {
				globalBgImg = {src: data.global_background_image}
			}

			if(globalBgImg.src && (globalBgImg.src.indexOf('http://') != -1 || globalBgImg.src.indexOf('https://') != -1)){
				backgroundImage = 'url('+globalBgImg.src+')';
			} else if(globalBgImg.src){
				backgroundImage = 'url('+pagebuilder_base+globalBgImg.src+')';
			}

			var borderWidth = '';

			if (data.global_user_border) {
				if (_.isObject(data.global_border_width)) {
					borderWidth = data.global_border_width[window.builderDefaultDevice]+'px';
				} else {
					borderWidth = data.global_border_width+'px';
				}
			} 
			
			var borderColor = '';
			if(data.global_user_border && data.global_border_color){
				borderColor = data.global_border_color;
			}

			var borderStyle = '';
			if(data.global_user_border && data.global_boder_style){
				borderStyle = data.global_boder_style;
			}

			var borderRadius = data.global_border_radius || '';

			var margin = window.getMarginPadding(data.global_margin, 'margin');
			var padding = window.getMarginPadding(data.global_padding, 'padding');

			if(data.global_use_animation && data.global_animation){
				addonClass += ' jpb-wow '+data.global_animation;

				if(data.global_animationduration){
					addonAttr = ` data-jpb-wow-duration="${data.global_animationduration}ms"`;
				}

				if(data.global_animationdelay){
					addonAttr += ` data-jpb-wow-delay="${data.global_animationdelay}ms"`;
				}
			}

			if(_.isObject(data.global_boxshadow) && !data.global_boxshadow.enabled) {
				boxShadow = '';
			} else if(_.isObject(data.global_boxshadow)){
				var ho = data.global_boxshadow.ho + 'px' || '0px',
					vo = data.global_boxshadow.vo + 'px' || '0px',
					blur = data.global_boxshadow.blur + 'px' || '0px',
					spread = data.global_boxshadow.spread + 'px' || '0px',
					color = data.global_boxshadow.color || '';

				boxShadow = ho + ' ' + vo + ' ' + blur + ' ' + spread  + ' ' + color;
			} else {
				boxShadow = data.global_boxshadow || '';
			}
			
		#>
		<div id="{{ (data.table_advanced_item || data.jp_tab_item || data.jp_accordion_item || addonName === 'div') ? '' : addonId }}" class="{{ addonClass }}" {{{ addonAttr }}} >
			<# if(data.global_use_overlay){ #>
				<div class="jpb-addon-overlayer"></div>
			<# } #>
			<style type="text/css">
				<#
				var unitTop = typeof data.global_addon_position_top !== "undefined" && typeof data.global_addon_position_top.unit !== "undefined" ? data.global_addon_position_top.unit : "px";
				var unitLeft = typeof data.global_addon_position_left !== "undefined" && typeof data.global_addon_position_left.unit !== "undefined" ? data.global_addon_position_left.unit : "px";

				if(data.global_seclect_position == "absolute" || data.global_seclect_position == "fixed"){
				#>
					#jpagebuilder div#jpb-addon-{{ data.id }} { 
						margin: 0;
					}
				<# } #>
				#jpagebuilder div#addon-wrap-{{ data.id }} { 
					<# if (addonName === 'empty_space') { #>
						position: static;
					<#}#>
					<# if(data.global_custom_position && data.global_seclect_position){ #>
						<# if(data.global_seclect_position == "absolute"){ #>
							position:absolute;
						<# } else if(data.global_seclect_position == "fixed"){ #>
							position:fixed;
						<# }
						if(_.isObject(data.global_addon_position_top)) {
						#>
							top:{{data.global_addon_position_top[window.builderDefaultDevice]}}{{unitTop}};
						<# } else { #>
							top:{{data.global_addon_position_top}}{{unitTop}};
						<# }
						if(_.isObject(data.global_addon_position_left)) {
						#>
							left:{{data.global_addon_position_left[window.builderDefaultDevice]}}{{unitLeft}};
						<# } else { #>
							left:{{data.global_addon_position_left}}{{unitLeft}};
						<# }
						if(data.global_addon_z_index) {
						#>
							z-index:{{data.global_addon_z_index}};
						<# }
					} #>
					<# if(_.isObject(margin)){ #>
						{{{ margin[window.builderDefaultDevice] }}}
					<# } else { #>
						{{{ margin }}}
					<# } #>
					<# 
					if(typeof data.use_global_width !== "undefined" && data.use_global_width && typeof data.global_width !== "undefined" && _.isObject(data.global_width)) {
					#>
						width: {{data.global_width[window.builderDefaultDevice]}}%;
					<# } #>
				}

				<# if (addonName === "button" || addonName === "button_group")  { #>
					#{{ addonId }} .jpb-btn {
						<# if (!_.isEmpty(boxShadow)) { #>
							box-shadow: {{ boxShadow }};
						<# } #>
					}
				<# } else {#>
					#{{ addonId }} {
						<# if (!_.isEmpty(boxShadow)) { #>
							box-shadow: {{ boxShadow }};
						<# } #>
					}
				<# } #>

				#{{ addonId }}{
					<# if(!_.isEmpty(textColor)) { #>
					color: {{ textColor }};
					<# } #>
					<# if(typeof data.global_background_type === "undefined" && backgroundColor){ #>
						background-color: {{ backgroundColor }};
					<# } else { #>
						<# if(data.global_background_type == "color" || data.global_background_type == "image" && backgroundColor){ #>
							background-color: {{ backgroundColor }};
						<# } #>
					<# } #>
					<# if(data.global_background_type == "gradient" && _.isObject(data.global_background_gradient)){ #>
						<# if(typeof data.global_background_gradient.type !== 'undefined' && data.global_background_gradient.type == 'radial'){ #>
							background-image: radial-gradient(at {{ data.global_background_gradient.radialPos || 'center center'}}, {{ data.global_background_gradient.color }} {{ data.global_background_gradient.pos || 0 }}%, {{ data.global_background_gradient.color2 }} {{ data.global_background_gradient.pos2 || 100 }}%);
						<# } else { #>
							background-image: linear-gradient({{ data.global_background_gradient.deg || 0}}deg, {{ data.global_background_gradient.color }} {{ data.global_background_gradient.pos || 0 }}%, {{ data.global_background_gradient.color2 }} {{ data.global_background_gradient.pos2 || 100 }}%);
						<# } #>
					<# } #>
					<# if(typeof data.global_background_type === "undefined" ){ #>
						<# if(data.global_use_background){ #>
							background-image: {{ backgroundImage }};
							background-repeat: {{ backgroundRepeat }};
							background-size: {{ backgroundSize }};
							background-attachment: {{ backgroundAttachment }};
							background-position: {{ backgroundPosition }};
						<# } #>
					<# } else { #>
						<# if(data.global_background_type == "image" && backgroundImage){ #>
							background-image: {{ backgroundImage }};
							background-repeat: {{ backgroundRepeat }};
							background-size: {{ backgroundSize }};
							background-attachment: {{ backgroundAttachment }};
							background-position: {{ backgroundPosition }};
						<# } #>
					<# } #>
					<# if(_.isObject(borderRadius)){ #>
						<# if (!_.isEmpty(borderRadius[window.builderDefaultDevice])) {#>
							border-radius: {{ borderRadius[window.builderDefaultDevice] }}px;
						<# } #>
					<# } else { #>
						<# if (!_.isEmpty(borderRadius)){ #>
							border-radius: {{ borderRadius }}px;
						<# } #>
					<# } #>
					<# if(_.isObject(padding)) { #>
						{{{ padding[window.builderDefaultDevice] }}}
					<# } else { #>
						{{{ padding }}}
					<# } #>
					<# if (!_.isEmpty(borderWidth)) { #>
						border-width: {{ borderWidth }};
					<# } #>
					<# if (!_.isEmpty(borderColor)) { #>
						border-color: {{ borderColor }};
					<# } #>
					<# if (!_.isEmpty(borderStyle)) { #>
						border-style: {{ borderStyle }};
					<# } #>
					
					<# if(data.global_use_overlay){ #>
						position: relative;
						overflow: hidden;
					<# } #>
				}
				<# if(data.global_use_overlay){ #>
					#{{ addonId }} .jpb-addon-overlayer{

						<# if(typeof data.global_overlay_type == "undefined"){
							data.global_overlay_type = "overlay_color";
						} #>
						<# if(data.global_overlay_type == "overlay_color") { #>
							background-color: {{ data.global_background_overlay }};
						<# }

						if(data.global_background_type == "image" && backgroundImage){
							if(data.global_overlay_type == "overlay_gradient" && _.isObject(data.global_gradient_overlay)){
								if(typeof data.global_gradient_overlay.type !== 'undefined' && data.global_gradient_overlay.type == 'radial'){
						#>
									background: radial-gradient(at {{ data.global_gradient_overlay.radialPos || 'center center'}}, {{ data.global_gradient_overlay.color }} {{ data.global_gradient_overlay.pos || 0 }}%, {{ data.global_gradient_overlay.color2 }} {{ data.global_gradient_overlay.pos2 || 100 }}%);
								<# } else { #>
									background: linear-gradient({{ data.global_gradient_overlay.deg || 0}}deg, {{ data.global_gradient_overlay.color }} {{ data.global_gradient_overlay.pos || 0 }}%, {{ data.global_gradient_overlay.color2 }} {{ data.global_gradient_overlay.pos2 || 100 }}%);
								<# }
							}
							let patternImgBg = {}
							if (typeof data.global_pattern_overlay !== "undefined" && typeof data.global_pattern_overlay.src !== "undefined") {
								patternImgBg = data.global_pattern_overlay
							} else {
								patternImgBg = {src: data.global_pattern_overlay}
							}
							if(patternImgBg.src && data.global_overlay_type == "overlay_pattern"){
								let patternImg = '';
								if(patternImgBg.src && (patternImgBg.src.indexOf('http://') != -1 || patternImgBg.src.indexOf('https://') != -1)){
									patternImg = 'url('+patternImgBg.src+')';
								} else if(patternImgBg.src){
									patternImg = 'url('+pagebuilder_base+patternImgBg.src+')';
								}
							#>
								background-image:{{patternImg}};
								background-attachment: scroll;
								<# if(!_.isEmpty(data.global_overlay_pattern_color)){ #>
									background-color:{{data.global_overlay_pattern_color}};
								<# }
							}
						} #>
						position: absolute;
						top: 0;
						left: 0;
						width: 100%;
						height: 100%;
						z-index: 0;
						<# if(data.global_background_type == "image" && backgroundImage){ #>
							<# if (data.blend_mode) { #>
								mix-blend-mode:{{data.blend_mode}};
							<# } #>
						<# } #>
					}

					#{{ addonId }} > .jpb-addon{
						position: relative;
					}
				<# } #>
				#{{ addonId }} a{
					color: {{ linkColor }};
				}
				#{{ addonId }} a:hover,
				#{{ addonId }} a:focus,
				#{{ addonId }} a:active{
					color: {{ linkHoverColor }};
				}

				<# if (!_.isEmpty(data.title)){ #>
					#jpb-addon-{{ data.id }} .jpb-addon-title{
						color: {{ data.title_text_color }};
					}
				<# } #>

				<?php echo $queryString; ?>
			</style>
			<?php echo $class_name::getFrontendEditor(); ?>
		</div>
		</script>
<?php
	}
}