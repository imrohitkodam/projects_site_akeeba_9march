<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

/**
 * No direct access.
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
 * Autoload the required classes in a required scope.
 *
 * @since 4.0.0
 */
class JpagebuilderAutoload {
	/**
	 * Load the required classes for the application.
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public static function loadClasses() {
		require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/route.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/helper.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/base.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/config.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/editor/basehelper.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/editor/lodashlib.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/editor/csshelper.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/editor/addonutils.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/addon.php';
		require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/helper.php';
	}
	public static function loadHelperClasses() {
		if (! class_exists ( 'JpagebuilderAddonsHelper' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/helpers/AddonsHelper.php';
		}

		if (! class_exists ( 'JpagebuilderApplicationHelper' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/helpers/ApplicationHelper.php';
		}

		if (! class_exists ( 'JpagebuilderSecurityHelper' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/helpers/SecurityHelper.php';
		}

		if (! class_exists ( 'JpagebuilderEditorUtils' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/helpers/EditorUtils.php';
		}

		if (! class_exists ( 'JpagebuilderIconHelper' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/helpers/IconHelper.php';
		}

		if (! class_exists ( 'JpagebuilderLanguageHelper' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/builder/helpers/LanguageHelper.php';
		}

		if (! class_exists ( 'JpagebuilderHelper' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/helpers/jpagebuilder.php';
		}

		if (! class_exists ( 'BuilderMediaHelper' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/helpers/media-helper.php';
		}
	}

	/**
	 * Load the global assets to the whole application.
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public static function loadGlobalAssets() {
		$doc = Factory::getApplication ()->getDocument ();
		$wa = $doc->getWebAssetManager();
		$wa->useScript('core');
		$wa->registerAndUseScript('jpagebuilder.common', 'components/com_jpagebuilder/assets/js/common.js', [], [], ['core']);
	}
}