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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Utilities\ArrayHelper;

/**
 * Table class
 *
 * @package     Joomla.Administrator
 * @subpackage  com_quick2cart
 * @since       1.5
 */
class Quick2cartTableShipmethods extends Table
{
	/**
	 * Constructor
	 *
	 * @param   Joomla\Database\DatabaseDriver  $db  A database connector object
	 */
	public function __construct($db)
	{
		parent::__construct('#__kart_shipProfileMethods', 'id', $db);
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array    $pks     Named array
	 *
	 * @param   boolean  $state   state
	 *  
	 * @param   integer  $userId  user id
	 * 
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 *
	 * @see		JTable:bind
	 * 
	 * @since	1.5
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		// Sanitize input.
		$k = $this->_tbl_key;
		ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(Text::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
			'UPDATE `' . $this->_tbl . '`'
			. ' SET `state` = ' . (int) $state
			. ' WHERE (' . $where . ')' . $checkin
		);

		// Check for a database error.
		try
		{
			$this->_db->execute();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		$this->setError('');

		return true;
	}

	/**
	 * Overloaded delete function 
	 *
	 * @param   array  $pk  Named array
	 *
	 * @return	boolean 
	 */
	public function delete($pk = null)
	{
		if ($pk)
		{
			$this->load($pk);
			$result = parent::delete($pk);

			return $result;
		}

		return false;
	}
}
