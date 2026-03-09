<?php

/**
 * @package         Convert Forms
 * @version         5.1.2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2025 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

class Confirm extends \ConvertForms\Field
{
	protected $inheritInputLayout = 'text';

	public function setField($field)
	{
        parent::setField($field);

		$this->inheritInputLayout = $field['confirm_type'];

		return $this;
	}

	/**
	 *  Prepares the field's input layout data
	 *
	 *  @return  array
	 */
	protected function getInputData()
	{
		$this->field->type = isset($this->field->confirm_type) ? $this->field->confirm_type : 'text';
		return parent::getInputData();
	}

	/**
	 *  Validate field value
	 *
	 *  @param   mixed  $value           The field's value to validate
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value)
	{
		if (!$this->field->get('required'))
		{
			return true;
		}

		$confirmField = $this->field->get('confirmfield');

		if (empty($confirmField) || !isset($this->data['cf'][$confirmField]))
		{
			$this->throwError('COM_CONVERTFORMS_FIELD_CONFIRM_INVALID');
		}

		$confirmFieldValue = $this->data['cf'][$confirmField];

		if ($confirmFieldValue !== $value)
		{
			$this->throwError('COM_CONVERTFORMS_FIELD_CONFIRM_NOT_MATCH');
		}
	}

	/**
	 * Prepare value to be displayed to the user as HTML/text
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function prepareValueHTML($value)
	{
		if ($this->field->get('confirm_type') == 'password')
		{
			$passwordField = new Password();
			return $passwordField->prepareValueHTML($value);
		}

		return $value;
	}
}