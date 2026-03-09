<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonRow extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$output = '';

		return $output;
	}
	public function css() {
		$settings = $this->addon->settings;
		$css = '';

		return $css;
	}
	public static function getFrontendEditor() {
		$output = '';
		return $output;
	}
}