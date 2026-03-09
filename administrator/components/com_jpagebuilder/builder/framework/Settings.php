<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Trait for managing app configs
 */
trait JPageBuilderFrameworkSettings {
	public function settings() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'PUT',
				'DELETE',
				'PATCH'
		], $method );

		$this->getSettings ();
	}
	private function getSettings() {
		if (! \class_exists ( 'JpagebuilderBase' )) {
			require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/base.php';
		}

		$sectionSettings = JpagebuilderBase::getRowGlobalSettings ();
		$columnSettings = JpagebuilderBase::getColumnGlobalSettings ();

		$sectionDefaults = JpagebuilderEditorUtils::getSectionSettingsDefaultValues ();
		$columnDefaults = JpagebuilderEditorUtils::getColumnSettingsDefaultValues ();

		$this->sendResponse ( [ 
				'section' => $sectionSettings,
				'column' => $columnSettings,
				'sectionDefaults' => $sectionDefaults,
				'columnDefaults' => $columnDefaults
		] );
	}
}
