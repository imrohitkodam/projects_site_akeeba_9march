<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Model for category attribute set mapping
 *
 * @since  2.5
 *
 */
class Quick2cartModelAttributesetMapping extends ListModel
{
	/**
	 * Function to get attribute sets
	 *
	 * @return array of attribute sets
	 *
	 * @since  2.5
	 *
	 **/
	public function getattributesets()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('global_attribute_set_name'));
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__kart_global_attribute_set'));
		$db->setQuery($query);
		$attributesets = $db->loadObjectlist();

		$select                            = new stdclass;
		$select->id                        = '0';
		$select->global_attribute_set_name = Text::_('QTC_PROD_SEL_ATTRIBUTE');

		$attributesetslist = array();
		array_push($attributesetslist, $select);

		foreach ($attributesets as $attr)
		{
			array_push($attributesetslist, $attr);
		}

		return $attributesetslist;
	}

	/**
	 * Function to get attribute set for mapped category id
	 *
	 * @param   INT  $categoryId  attribute set id
	 *
	 * @return category id
	 *
	 * @since  2.5
	 *
	 **/
	public function getAttributeSet($categoryId)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('attribute_set_id'));
		$query->from($db->quoteName('#__kart_category_attribute_set'));
		$query->where($db->quoteName('category_id') . ' = ' . $categoryId);
		$db->setQuery($query);
		$attributeSetId = $db->loadResult();

		return $attributeSetId;
	}

	/**
	 * Function to get category id for mapped attribute set
	 *
	 * @param   INT  $categoryId  attribute set id
	 *
	 * @return category id
	 *
	 * @since  2.5
	 *
	 **/
	public function getAttributeSetId($categoryId)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('attribute_set_id'));
		$query->from($db->quoteName('#__kart_category_attribute_set'));
		$query->where($db->quoteName('category_id') . ' = ' . $categoryId);
		$db->setQuery($query);
		$attributeSetId = $db->loadResult();

		return $attributeSetId;
	}

	/**
	 * Function to save attribute set and category mapping
	 *
	 * @return null
	 *
	 * @since  2.5
	 *
	 **/
	public function save()
	{
		$app          = Factory::getApplication();
		$input        = $app->input;
		$category_map = array();
		$category_map = $input->get('cat', '', 'array');

		// Get all mapped category set list
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('category_id'));
		$query->from($db->quoteName('#__kart_category_attribute_set'));
		$db->setQuery($query);
		$categoryList = $db->loadColumn();

		foreach ($category_map as $key => $category)
		{
			$record = new stdclass;
			$record->attribute_set_id = $category[0];
			$record->category_id = $key;

			// If attribute set id already maped then update record else add new record
			if (in_array($record->category_id, $categoryList))
			{
				if ($record->attribute_set_id != 0)
				{
					$db->updateObject('#__kart_category_attribute_set', $record, 'category_id', true);
				}
				else
				{
					$query = $db->getQuery(true);
					$conditions = array(
						$db->quoteName('category_id') . ' = ' . $record->category_id
					);
					$query->delete($db->quoteName('#__kart_category_attribute_set'));
					$query->where($conditions);
					$db->setQuery($query);
					$db->execute();
				}
			}
			else
			{
				if ($record->attribute_set_id != 0)
				{
					$db->insertObject('#__kart_category_attribute_set', $record, 'category_id', true);
				}
			}
		}

		$app->enqueueMessage(Text::_('COM_QUICK2CART_MAPPING_SAVED'));
	}

	/**
	 * Function to check if there are products in mapped category
	 *
	 * @param   INT  $categoryId  category id
	 *
	 * @return integer
	 *
	 * @since  2.5
	 *
	 **/
	public function checkForProductsInCategory($categoryId)
	{
		// Fetch mapped attribute set from category id
		$attributeSetId    = $this->getAttributeSet($categoryId);
		$attributeSetModel = BaseDatabaseModel::getInstance('Attributeset', 'Quick2cartModel');

		if (!empty($attributeSetId))
		{
			$attributeList = $attributeSetModel->getAttributeListInAttributeSet($attributeSetId);
		}

		$count = 0;
		$globalAttributeList = array();

		if (!empty($attributeList))
		{
			foreach ($attributeList as $attribute)
			{
				if ($attribute['id'] != 0)
				{
					$globalAttributeList[] = $attribute['id'];
				}
			}
		}

		if (!empty($globalAttributeList) && (!empty($categoryId)))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(a.item_id)');
			$query->from($db->quoteName('#__kart_items', 'a'));
			$query->join('INNER', $db->quoteName('#__kart_itemattributes', 'ia') . 'ON' . $db->quoteName('ia.item_id') . '=' . $db->quoteName('a.item_id'));
			$query->where($db->quoteName('ia.global_attribute_id') . ' IN (' . implode(',', $globalAttributeList) . ')');
			$query->where($db->quoteName('a.category') . '=' . $categoryId);
			$db->setQuery($query);
			$count = $db->loadResult();
		}

		return $count;
	}
}
