<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldRenderer extends \JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'renderer';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   11.4
	 */
	public function getOptions()
	{
		$path = JPATH_ROOT . '/components/com_quick2cart/layouts/globalattribute/renderer/*.php';
		$files = glob($path);
		$replace = JPATH_ROOT . '/components/com_quick2cart/layouts/globalattribute/renderer/';
		$options = array();

		foreach ($files as $file)
		{
			$option        = new stdclass;
			$file          = str_replace($replace, '', $file);
			$option->value = $file;
			$option->text  = $file;
			$options[]     = $option;
		}

		return $options;
	}
}
