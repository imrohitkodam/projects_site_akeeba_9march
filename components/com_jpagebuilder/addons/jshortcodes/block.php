<?php
/**
 * @package JSHORTCODES::JPAGEBUILDER
 * @subpackage plugins
 * @author Joomla! Extensions Store
 * @copyright (C)2024 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct accees
defined ( '_JEXEC' ) or die ( 'Restricted Aceess' );
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class JpagebuilderAddonJshortcodes extends JpagebuilderAddons {
	private static $searchCounter = 0;
	public function render() {
		$class = (isset ( $this->addon->settings->class ) && $this->addon->settings->class) ? ' ' . $this->addon->settings->class : '';
		$shortcode = (isset ( $this->addon->settings->shortcode ) && $this->addon->settings->shortcode) ? $this->addon->settings->shortcode : '';

		require_once JSHORTCODES_SYSTEM_PLUGIN_ROOT . '/helper/assets.php';
		require_once JSHORTCODES_SYSTEM_PLUGIN_ROOT . '/config/inc/tools.php';
		require_once JSHORTCODES_SYSTEM_PLUGIN_ROOT . '/config/inc/utilities.php';
		require_once JSHORTCODES_SYSTEM_PLUGIN_ROOT . '/helper/shortcodes.php';
		require_once JSHORTCODES_SYSTEM_PLUGIN_ROOT . '/helper/shortcodesbase.php';

		// Init only on the first iteration
		if (static::$searchCounter == 0) {
			$app = Factory::getApplication ();
			$doc = $app->getDocument ();
			$wa = $doc->getWebAssetManager ();
			$wa->useScript ( 'jquery' );

			$lang = $app->getLanguage ();
			$lang->load ( 'plg_content_jshortcodes', JPATH_ADMINISTRATOR );

			$option = $app->getInput ()->getCmd ( 'option' );
			$current_tmpl = $app->getTemplate ();
			$current_conf_tmpl = JShortcodesAssets::currentTmpl ();

			// Loading common css wide
			$wa->registerAndUseStyle ( 'jshortcodes.maincss', JSHORTCODES_SYSTEM_PLUGIN_MEDIA_URI . '/css/main.css' );

			// If found any shortcode.css override in the template directory then loads it
			$css = JPATH_ROOT . '/media/templates/site/' . $current_tmpl . '/css/shortcodes.css';
			if (file_exists ( $css )) {
				$wa->registerAndUseStyle ( 'jshortcodes.shortcodescss', 'media/templates/site/' . $current_tmpl . '/css/shortcodes.css' );
			}

			// RTL css file for RTL supported language styling
			if ($lang->isRTL ()) {
				$wa->registerAndUseStyle ( 'jshortcodes.mainrtlcss', JSHORTCODES_SYSTEM_PLUGIN_MEDIA_URI . '/css/rtl.css' );
			}

			// All shortcodes register here for both frontend and backend
			JShortcodesShortcodesbase::registerShortcodes ();
		}

		$shortcode = JShortcodesFunctions::autoParagraph ( $shortcode );
		$shortcode = JShortcodesFunctions::shortcode_unautop ( $shortcode );
		$shortcode = JShortcodesFunctions::do_shortcode ( $shortcode );

		$shortcode = '<div class="' . $this->addon->settings->class . '">' . $shortcode . '</div>';

		return $shortcode;
	}
	public function css() {
		$addon_id = '#jpb-addon-' . $this->addon->id;

		$style = '';

		$style .= (isset ( $this->addon->settings->addon_margin ) && $this->addon->settings->addon_margin) ? JpagebuilderHelperSite::getPaddingMargin ( $this->addon->settings->addon_margin, 'margin' ) : '';
		$style .= (isset ( $this->addon->settings->addon_padding ) && $this->addon->settings->addon_padding) ? JpagebuilderHelperSite::getPaddingMargin ( $this->addon->settings->addon_padding, 'padding' ) : '';

		$css = '';
		if ($style) {
			$css .= $addon_id . ' {' . $style . '}';
		}

		return $css;
	}
	/**
	 * Generate the lodash template string for the frontend editor.
	 *
	 * @return string The lodash template string.
	 * @since 1.0.0
	 */
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( '#jpb-addon-{{ data.id }}' );
		$base = Uri::base(true);
		$output = '<style type="text/css">';
		$output .= 'div.jshortcodes-block{min-height:40px;border:1px solid #CCC;display: flex;align-items: center;padding: 0 10px}img.jshortcode-icon{width:20px;margin-right:10px}'; 
		$output .= '</style>';
		
		$output .= '<div class="jshortcodes-block">' .
					'<span data-addon-icon="true">' .
						'<img class="jshortcode-icon" src="' . $base . '/components/com_jpagebuilder/addons/jshortcodes/assets/images/icon.svg" alt="jshortcodes"></span>' .
						'JShortcodes:
					<# if ( !_.isEmpty( data.admin_label ) ) { #>
						{{ data.admin_label }}
					<# } else { #>
						{{ data.shortcode }}
					<# } #>
				  </div>';
		return $output;
	}
}
