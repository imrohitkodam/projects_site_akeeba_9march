<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\Filesystem\Folder;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait for managing purging cached css.
 *
 * @version 4.1.0
 */
trait JPageBuilderFrameworkPurgeCss {
	private function purgeCachedCss() {
		$cssFolderPath = JPATH_ROOT . '/media/com_jpagebuilder/css';

		if (is_dir ( $cssFolderPath )) {
			Folder::delete ( $cssFolderPath );
		}

		$this->sendResponse ( true );
	}
	public function purgeCss() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'PUT',
				'DELETE',
				'PATCH'
		], $method );

		$this->purgeCachedCss ();
	}
}
