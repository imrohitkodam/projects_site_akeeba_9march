<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('restricted access');

use Joomla\CMS\Factory;

class ModJPagebuilderHelper
{
	public static function getData($id, $params)
	{
		$data = self::pageBuilderData($id);
		$data = JpagebuilderApplicationHelper::preparePageData($data);

		if (isset($data->text) && $data->text)
		{
			$data->text = !is_string($data->text) ? json_encode($data->text) : $data->text;
			return $data->text;
		}
		else
		{
			$content = $params->get('content', '[]');

			if (!self::isJson($content))
			{
				$content = '[]';
			}
		}

		return $content;
	}

	private static function pageBuilderData($id)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('*');
		$query->from($db->quoteName('#__jpagebuilder'));
		$query->where($db->quoteName('extension') . ' = ' . $db->quote('mod_jpagebuilder'));
		$query->where($db->quoteName('extension_view') . ' = ' . $db->quote('module'));
		$query->where($db->quoteName('view_id') . ' = ' . $db->quote($id));
		$db->setQuery($query);
		$item = $db->loadObject();

		return $item;
	}

	private static function isJson($string)
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
}
