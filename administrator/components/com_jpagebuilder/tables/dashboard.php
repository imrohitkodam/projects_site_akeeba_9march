<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Table\Table;
class JpagebuilderTableDashboard extends Table {
	function __construct($db) {
		parent::__construct ( '#__jpagebuilder', 'id', $db );
	}
	public function store($updateNulls = false) {
		return parent::store ( $updateNulls );
	}
}
