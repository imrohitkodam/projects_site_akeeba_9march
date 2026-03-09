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
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * Quick2cart model.
 *
 * @since  2.5
 *
 */
class Quick2cartModelAttributeset extends AdminModel
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_QUICK2CART';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Config array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   2.5
	 */
	public function getTable($type = 'Attributeset', $prefix = 'Quick2cartTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm    A JForm object on success, false on failure
	 *
	 * @since   2.5
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.attributeset', 'attributeset', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @since	2.5
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.attributeset.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed	Object on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   table  $table  table object
	 *
	 * @return  null
	 *
	 * @since   2.5
	 */
	protected function prepareTable($table)
	{
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__kart_global_attribute_set');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Function to get attribute list in attribute set
	 *
	 * @param   integer  $attributesetid  Attribute set id
	 * @param   integer  $filters         flag for filters
	 *
	 * @return  Array
	 *
	 * @since   2.5
	 */
	public function getAttributeListInAttributeSet($attributesetid, $filters = 0)
	{
		if (empty($attributesetid))
		{
			return array();
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('global_attribute_ids'));
		$query->from($db->quoteName('#__kart_global_attribute_set'));
		$query->where($db->quoteName('id') . " = " . $attributesetid);

		if ($filters == 1)
		{
			$query->where($db->quoteName('state') . " = 1");
		}

		$db->setQuery($query);
		$attribute_ids       = $db->loadResult();
		$attributeidsinarray = json_decode($attribute_ids);
		$attributeDetails    = array();

		if (!empty($attributeidsinarray))
		{
			foreach ($attributeidsinarray as $attribute)
			{
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->select($db->quoteName('attribute_name'));
				$query->select($db->quoteName('display_name'));
				$query->select($db->quoteName('renderer'));
				$query->select($db->quoteName('state'));
				$query->from($db->quoteName('#__kart_global_attribute'));
				$query->where($db->quoteName('id') . " = " . $attribute);

				if ($filters == 1)
				{
					$query->where($db->quoteName('state') . " = 1");
				}

				$db->setQuery($query);
				$attribute_ids = $db->LoadAssoc();

				$attributeDetails[] = $attribute_ids;
			}
		}

		return $attributeDetails;
	}

	/**
	 * Function to get attribute list in attribute set
	 *
	 * @param   integer  $attributeId  Attribute set id
	 * @param   integer  $prod_cat     product category
	 *
	 * @return  Array
	 *
	 * @since   2.5
	 */
	public function getOptionsListInAttribute($attributeId, $prod_cat = 0)
	{
		$options = array();

		if (!empty($attributeId))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("DISTINCT " . $db->quoteName('gao.id'));
			$query->select($db->quoteName('gao.option_name'));
			$query->select($db->quoteName('gao.ordering'));
			$query->from('#__kart_global_attribute_option AS gao');
			$query->join("INNER", "#__kart_itemattributeoptions AS iao ON iao.global_option_id = gao.id");
			$query->where($db->quoteName('attribute_id') . " = " . $attributeId);
			$query->order('gao.ordering');

			if (!empty($prod_cat))
			{
				$query->join("INNER", "#__kart_itemattributes AS ia ON ia.itemattribute_id = iao.itemattribute_id");
				$query->join("INNER", "#__kart_items AS i ON i.item_id = ia.item_id");
				$query->where('i.category = ' . $prod_cat);
			}

			$db->setQuery($query);
			$options = $db->loadAssocList();
		}

		return $options;
	}

	/**
	 * Function to get categorys mapped with providede attribute set
	 *
	 * @param   INT  $attributeSet  attribute set
	 *
	 * @return  null
	 *
	 * @since  2.5
	 *
	 */
	public function getCategorysForAttributeSet($attributeSet)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('category_id'));
		$query->from($db->quoteName('#__kart_category_attribute_set'));
		$query->where($db->quoteName('attribute_set_id') . " = " . $attributeSet);
		$db->setQuery($query);
		$options = $db->loadColumn();

		return $options;
	}

	/**
	 * Method to delete attribute.
	 *
	 * @return null
	 *
	 * @since  2.5
	 */
	public function removeAttribute()
	{
		$input = Factory::getApplication()->input;
		$attId = $input->get('attributeid', '', 'int');
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('global_attribute_ids'));
		$query->from($db->quoteName('#__kart_global_attribute_set'));
		$query->where($db->quoteName('id') . " = " . $input->get('attributesetid'));
		$db->setQuery($query);
		$attribute_ids       = $db->loadResult();
		$attribute_ids_array = ($attribute_ids != null) ? json_decode($attribute_ids) : array();

		// If attribute id present in aray them remove the attribute id entry
		if (in_array($attId, $attribute_ids_array))
		{
			// Check if products are present which uses atribute which is to be deleted
			if (!empty($attId))
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/globalattributes.php';
				$Quick2cartModelGlobalattributes = new Quick2cartModelGlobalattributes;
				$count = $Quick2cartModelGlobalattributes->checkForProductsWithAttributeId($attId);

				if (!empty($count))
				{
					return $count;
				}
			}

			$key = array_search($attId, $attribute_ids_array);
			unset($attribute_ids_array[$key]);

			// $updated_array is used to reorder the array $attribute_ids_array after deleting the element
			$updated_array = array();

			foreach ($attribute_ids_array as $attribute_id)
			{
				$updated_array[] = $attribute_id;
			}

			$query      = $db->getQuery(true);
			$fields     = $db->quoteName('global_attribute_ids') . " = '" . json_encode($updated_array) . "'";
			$conditions = $db->quoteName('id') . " = " . $input->get('attributesetid');
			$query->update($db->quoteName('#__kart_global_attribute_set'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Method to add attribute.
	 *
	 * @return null
	 *
	 * @since  2.5
	 */
	public function addAttribute()
	{
		$input = Factory::getApplication()->input;
		$attId = $input->get('attributeid', '', 'int');
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('global_attribute_ids'));
		$query->from($db->quoteName('#__kart_global_attribute_set'));
		$query->where($db->quoteName('id') . " = " . $input->get('attributesetid'));
		$db->setQuery($query);
		$attribute_ids = $db->loadResult();

		$attribute_ids_array = json_decode($attribute_ids);

		// If atribute id already exists in attribute set then do not insert attribute id
		if(!empty($attribute_ids_array))
		{
			if (!(in_array($attId, $attribute_ids_array)))
			{
				$attribute_ids_array[count($attribute_ids_array)] = (int) $attId;
				$query      = $db->getQuery(true);
				$fields     = $db->quoteName('global_attribute_ids') . " = '" . json_encode($attribute_ids_array) . "'";
				$conditions = $db->quoteName('id') . " = " . $input->get('attributesetid');
				$query->update($db->quoteName('#__kart_global_attribute_set'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Function to save global_attribute_ids in order in table kart_global_attribute_set
	 *
	 * @param   ARRAY  $attributeData  attribute ids
	 *
	 * @return  null
	 *
	 * @since 2.5
	 *
	 * */
	public function saveOrdering($attributeData)
	{
		$app              = Factory::getApplication();
		$input            = $app->input;
		$order            = array();
		$orderedAttribute = array();

		foreach ($attributeData as $k => $orderig)
		{
			$order[$k] = $orderig['attribute_option'];
		}

		// Code to check duplicate ordering
		for ($i = 0;$i < count($order);$i++)
		{
			for ($j = $i + 1;$j < count($order);$j++)
			{
				if ($order[$i] == $order[$j])
				{
					$app->enqueueMessage(Text::_('COM_QUICK2CART_DUPLICATE_ORDERING_ERROR'), 'error');

					return false;
				}
			}
		}

		array_multisort($order, SORT_NUMERIC, $attributeData);

		foreach ($attributeData as $orderig)
		{
			$orderedAttribute[] = (int) $orderig['id'];
		}

		$db         = Factory::getDbo();
		$query      = $db->getQuery(true);
		$fields     = $db->quoteName('global_attribute_ids') . " = '" . json_encode($orderedAttribute) . "'";
		$conditions = $db->quoteName('id') . " = " . $input->get('id', '', 'INT');
		$query->update($db->quoteName('#__kart_global_attribute_set'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->execute();

		return true;
	}

	/**
	 * Method to validate the Organization contact form data from server side.
	 *
	 * @param   \JForm  $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  array|boolean  Array of filtered data if valid, false otherwise.
	 *
	 * @since   4.0.1
	 */
	public function validate($form, $data, $group = null)
	{
		$return = true;
		$app   = Factory::getApplication();
		$input = $app->input;

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('count(*)');
		$query->from('#__kart_global_attribute_set');
		$query->where('global_attribute_set_name' . ' = ' . $db->q($data['global_attribute_set_name']));

		if (isset($data['id']) && $data['id'])
		{
			$query->where('id' . ' != ' . (int) $data['id']);
		}

		$db->setQuery($query);
		$count = $db->loadResult();

		if ($count)
		{
			$this->setError(Text::_('COM_QUICK2CARET_MSG_ERROR_SAVE_ATTR_SET'));
			$return = false;
		}

		$data   = parent::validate($form, $data, $group);

		return ($return === true) ? $data: false;
	}
}
