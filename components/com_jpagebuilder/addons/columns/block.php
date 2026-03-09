<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonColumns extends JpagebuilderAddons {
	public function render() {
		return '';
	}
	public function css() {
		$settings = $this->addon->settings;

		return '';
	}
	public static function getFrontendEditor() {
		return '';
	}
}
