<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonEmpty_space extends JpagebuilderAddons {
	public function render() {
		$class = (isset ( $this->addon->settings->class ) && $this->addon->settings->class) ? $this->addon->settings->class : '';

		return '<div class="jpb-empty-space ' . $class . ' clearfix"></div>';
	}
	public function css() {
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;
		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );
		$css = '';
		$gapSettings = $cssHelper->generateStyle ( '.jpb-empty-space', $settings, [ 
				'gap' => 'height'
		] );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-empty-space', $settings, 'transform' );

		$css .= $gapSettings;
		$css .= $transformCss;

		return $css;
	}
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( '#jpb-addon-{{ data.id }}' );
		$output = '<style type="text/css">';
		$output .= $lodash->unit ( 'height', '.jpb-empty-space', 'data.gap', 'px' );
		$output .= $lodash->generateTransformCss ( '.jpb-empty-space', 'data.transform' );
		$output .= '
		</style>

		<div class="jpb-empty-space jpb-empty-space-edit {{ data.class }} clearfix"></div>';
		return $output;
	}
}
