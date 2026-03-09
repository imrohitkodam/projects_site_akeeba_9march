<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace ACF\Helpers\Fields;

defined('_JEXEC') or die;

use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Str;

class Country
{
	/**
	* Returns the YooTheme type.
	*
	* @param object $field
	* @param object $source
	*
	* @return array
	*/
	public static function getYooType($field = [], $source = [])
	{
		$multiple_selection = $field->fieldparams->get('multiple_selection', '0') === '1';
		if (!$multiple_selection)
		{
			return null;
		}

		$fields = [
			'code' => [
				'type' => 'String',
				'name' => 'code',
				'metadata' => [
					'label' => 'Country Code'
				],
			],
			'name' => [
				'type' => 'String',
				'name' => 'name',
				'metadata' => [
					'label' => 'Country Name'
				],
			]
		];
		$name = Str::camelCase(['Field', $field->name], true);
		$source->objectType($name, compact('fields'));

		return ['listOf' => $name];
	}

	/**
	 * Transforms the field value to an appropriate value that YooTheme can understand.
	 * 
	 * @return  array
	 */
	public static function yooResolve($item, $args, $ctx, $info)
	{
		$name = str_replace('String', '', strtr($info->fieldName, '_', '-'));

		// Check if it's a subform field
		$subfield = FieldsType::getSubfield($args['field_id'], $args['context']);

		// When we have a subform field, the $item is an array of values
		if (!$subfield || !is_array($item))
		{
			if (!isset($item->id) || !($field = FieldsType::getField($name, $item, $args['context'])))
			{
				return;
			}
		}
		else
		{
			// Set rawvalue
			$subfield->rawvalue = isset($item["field{$args['field_id']}"]) ? $item["field{$args['field_id']}"] : '';

			// Use the subform field
			$field = $subfield;
		}

        $value = $field->rawvalue;

		$multiple_selection = $field->fieldparams->get('multiple_selection', '0') === '1';

		if ($multiple_selection)
		{
			if (!is_array($value))
			{
				$value = [$value];
			}

			foreach ($value as &$v)
			{
				$country = \NRFramework\Countries::getCountry($v);

				$v = [
					'code' => $v,
					'name' => isset($country['name']) ? $country['name'] : $v
				];
			}

			return $value;
		}
		else
		{
			// If it's an array, try to grab the first item.
			if (is_array($value))
			{
				$value = array_values($value);
				$value = isset($value[0]) ? $value[0] : $value;
			}
			
			if (!is_string($value))
			{
				return;
			}

			$display_mode = $field->fieldparams->get('countrydisplay', 'name');

			if ($display_mode === 'code')
			{
				return $value;
			}
	
			if (!$country = \NRFramework\Countries::getCountry($value))
			{
				return $value;
			}
		
			return $country['name'];
		}
	}
}