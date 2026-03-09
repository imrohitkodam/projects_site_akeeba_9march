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
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use Joomla\CMS\Factory;

class Articles
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
	 * @param   object  $field
	 * @param   object  $source
	 * 
	 * @return  array
	 */
	public static function getYooType($field = [], $source = [])
	{
		$max_articles = (int) $field->fieldparams->get('max_articles', 0);
		$multiple = $max_articles === 0 || $max_articles > 1;
		return $multiple ? ['listOf' => 'Article'] : 'Article';
	}

	/**
	 * Transforms the field value to an appropriate value that YooTheme can understand.
	 * 
	 * @return  array
	 */
	public static function yooResolve($item, $args)
	{
		$field = isset($item->id)
            ? FieldsType::getField($args['field'], $item, $args['context'])
            : FieldsType::getSubfield($args['id'] ?? 0, $args['context']);
		
		if (!$field)
		{
            return null;
        }

        $fieldValue = $field->rawvalue ?? ($item["field{$args['id']}"] ?? null);

        if (!$fieldValue)
		{
            return null;
        }

        $ids = $fieldValue;

		$max_articles = 0;
		$multiple = true;

		// Handle Linked Articles
		$field_type = $field->fieldparams->get('articles_type', 'default');
		if ($field_type === 'linked')
		{
			
		}
		else
		{
			$max_articles = (int) $field->fieldparams->get('max_articles', 0);
			$multiple = $max_articles === 0 || $max_articles > 1;
		}

		if (is_scalar($ids))
		{
			$ids = [(int) $ids];
		}

        if (!is_array($ids))
        {
            return;
        }

        require_once JPATH_PLUGINS . '/fields/acfarticles/fields/acfarticlesfilters.php';

		$_order = \ACFArticlesFilters::getOrder($field->fieldparams->get('order', null));

		$order = [];
		foreach ($_order as $_order)
		{
			$split = explode(' ', $_order);
			$order[$split[0]] = $split[1] ?? 'ASC';
		}

		$articles = ArticleHelper::get($ids, [
			'order' => $order
		]);

		return !$multiple ? array_first($articles) : $articles;

	}

	
}