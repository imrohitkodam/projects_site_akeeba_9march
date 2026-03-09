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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

class DateTime extends \ConvertForms\Field
{
	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
		'browserautocomplete',
	);

	/**
	 *  Prepares the field's input layout data in order to support PHP date relative formats such as
	 *  first day of this month, next week, +5 day e.t.c
	 *
	 *  http://php.net/manual/en/datetime.formats.relative.php
	 *  
	 *  @return  array
	 */
	protected function getInputData()
	{
		$data = parent::getInputData();

		$properties = [
			'value',
			'placeholder',
			'mindate',
			'maxdate',
		];
		
		// Make sure we have a valid dateformat
		$dateFormat = $data['field']->dateformat ?: 'd/m/Y';
		$siteTimezone = new \DateTimeZone(Factory::getApplication()->getCfg('offset', 'GMT'));

		foreach ($properties as $property)
		{
			if (!isset($data['field']->$property) || empty($data['field']->$property))
			{
				continue;
			}

			$value = $data['field']->$property;

			// Try to format the date property.
			try
			{
				$date = Factory::getDate($value, $siteTimezone);

				// In case of maxDate, if there's no time set, set the time to 23:59:59 to prevent issues with flatpickr.
				if ($property == 'maxdate' && $date->format('H:i:s', true, false) == '00:00:00')
				{
					$date->setTime(23, 59, 59);
				}

				// Format the date in local time.
				$newDate = $date->format($dateFormat, true, false);

				$data['field']->$property = $newDate;

			} catch (\Exception $e)
			{
			}
		}

		return $data;
	}

	/**
	 * Convert value(s) to date time object
	 *
	 * @param  string  $value	 The date time string as stored in the database
	 * 
	 * @return mixed
	 */
	public function prepareValueAsObject($value)
	{
		switch ($this->field->get('mode'))
		{
			case 'multiple':
				return is_array($value) ? $value : array_map('trim', explode(',', $value));
				
			case 'range':
				return is_array($value) ? $value : array_map('trim', explode('to', $value));
			
			default:
				if (is_object($value))
				{
					return $value;
				}

				$tz = new \DateTimeZone(Factory::getUser()->getParam('timezone', Factory::getConfig()->get('offset')));
				return date_create_from_format($this->field->get('dateformat'), $value, $tz);
		}
	}
}