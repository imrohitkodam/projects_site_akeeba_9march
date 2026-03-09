<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Access\Rules;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
class JpagebuilderTableEditor extends Table {
	/**
	 * Summary of __construct
	 *
	 * @param DatabaseDriver $db
	 *        	DatabaseDriver object.
	 */
	function __construct($db) {
		parent::__construct ( '#__jpagebuilder', 'id', $db );
	}
	public function store($updateNulls = false) {
		return parent::store ( $updateNulls );
	}
	public function bind($array, $ignore = '') {
		/** @var CMSApplication $app */
		$app = Factory::getApplication ();

		if (isset ( $array ['id'] )) {
			$date = Factory::getDate ();
			$user = $app->getIdentity ();
			if ($array ['id']) {
				$array ['modified'] = $date->toSql ();
				$array ['modified_by'] = $user->id;
			} else {
				if (! ( int ) $array ['created_on']) {
					$array ['created_on'] = $date->toSql ();
				}

				if (empty ( $array ['created_by'] )) {
					$array ['created_by'] = $user->id;
				}
			}
		}

		// Bind the rules.
		if (isset ( $array ['rules'] ) && is_array ( $array ['rules'] )) {
			$rules = new Rules ( $array ['rules'] );
			$this->setRules ( $rules );
		}

		return parent::bind ( $array, $ignore );
	}
	protected function _getAssetTitle() {
		return $this->title;
	}

	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;

		return 'com_jpagebuilder.page.' . ( int ) $this->$k;
	}

	/**
	 * We provide our global ACL as parent
	 *
	 * @see Table::_getAssetParentId()
	 */
	protected function _getAssetParentId(?Table $table = null, $id = null) {
		/** @var Joomla\CMS\Table */
		$db = Factory::getContainer ()->get ( 'DatabaseDriver' );
		$asset = new Joomla\CMS\Table\Asset ( $db );
		$asset->loadByName ( 'com_jpagebuilder' );

		return $asset->id;
	}
}
