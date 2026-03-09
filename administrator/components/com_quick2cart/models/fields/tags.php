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
use Joomla\CMS\Form\Field\TagField;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of tags
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldTags extends \JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var      string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'tags';

	/**
	 * Method to return a list of tag options for a list input.
	 *
	 * @return   Array  $options   An array of JHtml options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		require_once JPATH_SITE . '/components/com_quick2cart/helper.php';
		$comquick2cartHelper = new comquick2cartHelper;
		$path                = JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/attributes.php';
		$attributesModel     = $comquick2cartHelper->loadqtcClass($path, "Quick2cartModelAttributes");
		$tags                = $attributesModel->getTags();
		$options             = array();

		if (!empty($tags))
		{
			foreach ($tags as $key => $tag)
			{
				$options[] = HTMLHelper::_('select.option', $tag['id'], $tag['title']);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
