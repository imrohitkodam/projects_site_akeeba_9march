<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Language\Text;

/**
 * No direct access
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class JpagebuilderLanguageHelper {
	public static function registerLanguageKeys() {
		$languageKeysPath = JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/data/translations.json';
		$languageKeys = \file_get_contents ( $languageKeysPath );

		if (! empty ( $languageKeys )) {
			$languageKeys = \json_decode ( $languageKeys );

			foreach ( $languageKeys as $key => $_ ) {
				Text::script ( $key );
			}
		}
	}
}
