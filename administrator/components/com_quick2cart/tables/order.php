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
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
 * Order Table class
 *
 * @since  1.1
 */
class Quick2CartTableOrder extends Table
{
	/**
	 * Constructor
	 *
	 * @param   Joomla\Database\DatabaseDriver  &$db  A database connector object
	 */
	public function __construct($db)
	{
		parent::__construct('#__kart_orders', 'id', $db);
	}
}
