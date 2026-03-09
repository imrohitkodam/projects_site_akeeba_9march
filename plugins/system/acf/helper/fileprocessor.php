<?php

/**
 * @package         Advanced Custom Fields
 * @version         3.1.3 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2025 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use NRFramework\Functions;
use Joomla\CMS\Factory;

/**
 * Abstract helper class for processing files in ACF fields within subforms.
 * 
 * This class handles the common logic for processing files in both acfupload and acfgallery fields
 * when they are nested within subform fields, including deeply nested structures.
 */
abstract class ACFFileProcessor
{
	/**
	 * The field type this processor handles (e.g., 'acfupload', 'acfgallery')
	 * 
	 * @var string
	 */
	protected $fieldType;

	/**
	 * Static array to track which subforms have been processed
	 * to prevent multiple processing of the same subform
	 */
	private static $processedSubformIds = [];

	/**
	 * Constructor
	 * 
	 * @param string $fieldType The field type this processor handles
	 */
	public function __construct($fieldType)
	{
		$this->fieldType = $fieldType;
	}

	/**
	 * Creates a processor instance for the given field type.
	 * 
	 * @param string $fieldType The field type
	 * @return ACFFileProcessor|null The processor instance or null if not available
	 */
	private function createProcessorForFieldType($fieldType)
	{
		// Convert field type to class name using naming convention
		$fieldTypeParts = explode('acf', $fieldType);
		if (count($fieldTypeParts) === 2 && $fieldTypeParts[0] === '')
		{
			$className = 'ACF' . ucfirst($fieldTypeParts[1]) . 'Processor';
			if (class_exists($className))
			{
				return new $className();
			}
		}
		return null;
	}

	/**
	 * Processes the files for all fields.
	 * 
	 * Either duplicates the files or uploads them to final directory.
	 * 
	 * @param   array   $fields      The fields to process
	 * @param   array   $fieldsData  The submitted field data
	 * @param   object  $item        The item being saved
	 * @param   array   &$data       Reference to original data array to update field values
	 * 
	 * @return  void
	 */
	public function processFiles($fields = [], $fieldsData = [], $item = [], &$data = null)
	{
		if (!$fields || !$fieldsData || !$item)
		{
			return;
		}

		// Whether we should clean up the temp folder at the end of this process
		$should_clean = false;

		// Get the Fields Model
		$model = Factory::getApplication()->bootComponent('com_fields')->getMVCFactory()->createModel('Field', 'Administrator', ['ignore_request' => true]);

		// Cache subform fields
		$subform_fields = [];

		// Loop over the fields
		foreach ($fields as $field)
		{
			$field_type = $field->type;
			
			/**
			 * Check whether fields are used within the Subform field (including nested subforms).
			 */
			if ($field_type === 'subform')
			{
				$this->processSubformField($field, $fieldsData, $model, $item, $subform_fields, $should_clean, $data);
			}
			elseif ($field_type === $this->fieldType)
			{
				// Process direct fields of our type
				$this->processDirectField($field, $fieldsData, $model, $item, $should_clean, $data);
			}
		}

		if ($should_clean)
		{
			// Clean old files from temp folder
			$this->cleanTempFiles();
		}
	}

	/**
	 * Processes a subform field that may contain our field type.
	 * 
	 * @param   object  $field          The subform field
	 * @param   array   $fieldsData     The submitted field data
	 * @param   object  $model          The Fields model
	 * @param   object  $item           The item being saved
	 * @param   array   &$subform_fields Cached subform fields (passed by reference)
	 * @param   bool    &$should_clean  Whether cleanup should run (passed by reference)
	 * @param   array   &$data          Reference to original data array (passed by reference)
	 * 
	 * @return  void
	 */
	private function processSubformField($field, $fieldsData, $model, $item, &$subform_fields, &$should_clean, &$data = null)
	{
		// Check if this subform has already been processed by another processor
		$subform_key = $field->id . '_' . $item->id;
		if (in_array($subform_key, self::$processedSubformIds))
		{
			return; // Skip if already processed
		}
		
		// Mark as processed
		self::$processedSubformIds[] = $subform_key;
		
		$submitted_subform_value = array_key_exists($field->name, $fieldsData) ? $fieldsData[$field->name] : null;

		// Ensure it has a value - use submitted data, not saved data
		if (!$submitted_subform_value)
		{
			// Update subform field
			$model->setFieldValue($field->id, $item->id, json_encode([]));
			return;
		}

		// Decode submitted value if it's a string
		if (is_string($submitted_subform_value))
		{
			$subform_value = json_decode($submitted_subform_value, true);
		}
		else
		{
			$subform_value = $submitted_subform_value;
		}

		if (!$subform_value)
		{
			// Update subform field
			$model->setFieldValue($field->id, $item->id, json_encode([]));
			return;
		}
		
		$update = false;
		$is_subform_non_repeatable = false;

		if (!isset($field->fieldparams) || !($field->fieldparams instanceof \Joomla\Registry\Registry))
		{
			$field->fieldparams = new \Joomla\Registry\Registry($field->fieldparams ?? null);
		}

		// Make non-repeatable subform fields a multi array so we can parse them
		$first_key = array_key_first($subform_value);
		if ($first_key && Functions::startsWith($first_key, 'field') && $field->fieldparams->get('repeat', '0') === '0')
		{
			$is_subform_non_repeatable = true;
			$subform_value = [$subform_value];
		}

		// Process subform recursively to handle nested subforms - process ALL ACF file field types in one pass
		$this->processAnyDepthFieldsAllTypes($subform_value, $subform_fields, $model, $item, $should_clean, $update, $data);

		if ($update)
		{
			if ($is_subform_non_repeatable)
			{
				$subform_value = reset($subform_value);
			}

			// Update subform field with processed data from all field types
			$model->setFieldValue($field->id, $item->id, json_encode($subform_value));
		}
	}

	/**
	 * Processes a direct field (not within a subform).
	 * 
	 * @param   object  $field         The field
	 * @param   array   $fieldsData    The submitted field data
	 * @param   object  $model         The Fields model
	 * @param   object  $item          The item being saved
	 * @param   bool    &$should_clean Whether cleanup should run (passed by reference)
	 * @param   array   &$data         Reference to original data array (passed by reference)
	 * 
	 * @return  void
	 */
	private function processDirectField($field, $fieldsData, $model, $item, &$should_clean, &$data = null)
	{
		// Determine the value if it is available from the data
		$value = array_key_exists($field->name, $fieldsData) ? $fieldsData[$field->name] : null;

		if (!$value)
		{
			return;
		}

		// Check if value can be json_decoded
		if (is_string($value))
		{
			if ($decoded = json_decode($value, true))
			{
				$value = $decoded;
			}
		}

		if (\ACF\Item::isCopying())
		{
			// Duplicate files
			$this->duplicateFiles($value);
		}
		else
		{
			// We should run our cleanup routine at the end
			$should_clean = true;

			// Move to final folder
			$value = $this->moveFilesToDestination($value, $field, $item);
		}

		// Setting the value for the field and the item
		$model->setFieldValue($field->id, $item->id, json_encode($value));

		// Also update the form data reference
		if ($data !== null && isset($data['com_fields']))
		{
			$data['com_fields'][$field->name] = json_encode($value);
		}
	}

	/**
	 * Processes all file field types at any depth in the data structure.
	 * This method handles both acfupload and acfgallery fields in one pass.
	 * 
	 * @param   mixed    &$data             The data to process (passed by reference)
	 * @param   array    &$subform_fields   Cached fields (passed by reference)
	 * @param   object   $model             The Fields model
	 * @param   object   $item              The item object
	 * @param   boolean  &$should_clean     Whether cleanup should run (passed by reference)
	 * @param   boolean  &$update           Whether anything was updated (passed by reference)
	 * @param   array    &$mainData         The main data array (passed by reference)
	 * 
	 * @return  void
	 */
	private function processAnyDepthFieldsAllTypes(&$data, &$subform_fields, $model, $item, &$should_clean, &$update, &$mainData = null)
	{
		if (!is_array($data))
		{
			return;
		}

		foreach ($data as $key => &$value)
		{
			// Check if this is a field key (starts with 'field')
			if (is_string($key) && Functions::startsWith($key, 'field'))
			{
				$field_id = str_replace('field', '', $key);
				
				if (is_numeric($field_id))
				{
					// Get the field
					$field = isset($subform_fields[$field_id]) ? $subform_fields[$field_id] : $model->getItem($field_id);
					
					// Cache field
					if (!isset($subform_fields[$field_id]))
					{
						$subform_fields[$field_id] = $field;
					}
					
					// Process ACF file field types (acfupload, acfgallery)
					if ($field && in_array($field->type, ['acfupload', 'acfgallery']))
					{
						// Decode JSON if needed
						if (is_string($value))
						{
							if ($decoded = json_decode($value, true))
							{
								$value = $decoded;
							}
						}

						// Process data at any level
						if (is_array($value))
						{
							// Create appropriate processor for this field type
							$processor = $this->createProcessorForFieldType($field->type);
							if ($processor)
							{
								$processor->processFieldData($value, $field, $item, $should_clean);
							}
							else
							{
								// Fallback to current processor if it matches the field type
								if ($field->type === $this->fieldType)
								{
									$this->processFieldData($value, $field, $item, $should_clean);
								}
							}
							$update = true;
						}
						
						continue;
					}
					// If it's a subform field, process it with subform logic
					elseif ($field && $field->type === 'subform')
					{
						if (is_array($value))
						{
							$nested_update = false;
							$is_subform_non_repeatable = false;

							if (!isset($field->fieldparams) || !($field->fieldparams instanceof \Joomla\Registry\Registry))
							{
								$field->fieldparams = new \Joomla\Registry\Registry($field->fieldparams ?? null);
							}

							// Make non-repeatable subform fields a multi array so we can parse them
							$first_key = array_key_first($value);
							if ($first_key && Functions::startsWith($first_key, 'field') && $field->fieldparams->get('repeat', '0') === '0')
							{
								$is_subform_non_repeatable = true;
								$value = [$value];
							}

							// Process nested subform recursively
							$this->processAnyDepthFieldsAllTypes($value, $subform_fields, $model, $item, $should_clean, $nested_update, $mainData);

							if ($nested_update)
							{
								if ($is_subform_non_repeatable)
								{
									$value = reset($value);
								}
								
								// Don't save here - let the parent level handle the database saving
								// The processed data will be updated in the $value reference
								$update = true;
							}
						}
						
						continue;
					}
				}
			}
			
			// Recursively process nested arrays for non-field data or other field types
			if (is_array($value))
			{
				$this->processAnyDepthFieldsAllTypes($value, $subform_fields, $model, $item, $should_clean, $update, $mainData);
			}
		}
	}

	/**
	 * Processes field data, handling both direct arrays and nested structures.
	 * 
	 * @param   array    &$data         The field data (passed by reference)
	 * @param   object   $field         The field object
	 * @param   object   $item          The item object
	 * @param   boolean  &$should_clean Whether cleanup should run (passed by reference)
	 * 
	 * @return  void
	 */
	public function processFieldData(&$data, $field, $item, &$should_clean)
	{
		if (\ACF\Item::isCopying())
		{
			// Duplicate files
			$this->duplicateFiles($data);
		}
		else
		{
			// We should run our cleanup routine at the end
			$should_clean = true;

			// Move to final folder
			$data = $this->moveFilesToDestination($data, $field, $item);
		}
	}

	/**
	 * Abstract method to duplicate files.
	 * Each field type should implement this method according to its needs.
	 * 
	 * @param   mixed   $data  The file data to duplicate
	 * 
	 * @return  void
	 */
	abstract protected function duplicateFiles(&$data);

	/**
	 * Abstract method to move files to destination.
	 * Each field type should implement this method according to its needs.
	 * 
	 * @param   mixed   $data   The file data to move
	 * @param   object  $field  The field object
	 * @param   object  $item   The item object
	 * 
	 * @return  mixed   The processed data
	 */
	abstract protected function moveFilesToDestination($data, $field, $item);

	/**
	 * Abstract method to clean temporary files.
	 * Each field type should implement this method according to its needs.
	 * 
	 * @return  void
	 */
	abstract protected function cleanTempFiles();
}