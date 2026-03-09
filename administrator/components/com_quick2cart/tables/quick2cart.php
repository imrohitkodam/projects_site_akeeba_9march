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
* Cart Table class
* 
* 
*/
class TableCart extends Table
{
	/**
	* Constructor
	*
	* @param Joomla\Database\DatabaseDriver Database connector object
	* 
	* 
	*/
	public function __construct($db)
	{
		parent::__construct('#__kart_cart', 'id', $db);
	}
}

/**
* Cart Items Table class
*/
class TableCartitems extends Table
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 * 
	 * 
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kart_cartitems', 'id', $db);
	}
}
