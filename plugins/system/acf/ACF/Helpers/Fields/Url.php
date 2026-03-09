<?php

/**
* @author Tassos Marinos <info@tassos.gr>
* @link http://www.tassos.gr
* @copyright Copyright © 2021 Tassos Marinos All Rights Reserved
* @license GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ACF\Helpers\Fields;

defined('_JEXEC') or die;

use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Str;

class Url
{
	/**
	* Returns the YooTheme type.
	*
	* If this accepts one image:
	* - Tells YooTheme to use the default type for the dropdown mapping option.
	*
	* If this accepts multiple images:
	* - Tells YooTheme to only return the value of this field in the dropdown mapping option.
	*
	* @param object $field
	* @param object $source
	*
	* @return array
	*/
	public static function getYooType($field = [], $source = [])
	{
		$fields = [
			'url' => [
				'type' => 'String',
				'name' => 'url',
				'metadata' => [
					'label' => 'URL'
				],
			],
			'text' => [
				'type' => 'String',
				'name' => 'text',
				'metadata' => [
					'label' => 'Label'
				],
			],
			'target' => [
				'type' => 'String',
				'name' => 'target',
				'metadata' => [
					'label' => 'Target'
				],
			]
		];
		$name = Str::camelCase(['Field', $field->name], true);
		$source->objectType($name, compact('fields'));

		return $name;
	}

	/**
	* Transforms the field value to an appropriate value that YooTheme can understand.
	*
	* @return array
	*/
	public static function yooResolve($item, $args, $ctx, $info)
	{
		// var_dump($info->rootValue['parent']->children[0]);
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

		if (is_string($value))
		{
			if (!$value = json_decode($value, true))
			{
				return;
			}
		}

		// Fix "target" value for new tab
		if (isset($value['target']))
		{
			switch ($value['target'])
			{
				case 'same_tab':
					$value['target'] = '_self';
					break;
				
				case 'new_tab':
					$value['target'] = '_blank';
					break;

				default:
					$value['target'] = '';
					break;
			}
		}

		return $value;
	}
}