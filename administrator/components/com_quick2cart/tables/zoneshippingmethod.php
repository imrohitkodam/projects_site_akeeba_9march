<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
 * Zone shipping method Table class
 *
 * @since  2.9.14
 */
class Quick2cartTableZoneShippingMethod extends Table
{
	/**
	 * Constructor
	 *
	 * @param   Joomla\Database\DatabaseDriver  $db  A database connector object
	 */
	public function __construct($db)
	{
		parent::__construct('#__kart_zoneShipMethods', 'id', $db);
	}
}
