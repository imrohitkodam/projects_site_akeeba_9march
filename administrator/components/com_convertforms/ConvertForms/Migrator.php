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

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;
use \NRFramework\Countries;
use Joomla\CMS\Factory;
use ConvertForms\Tasks\ModelTasks;

/**
 *  Convert Forms Migrator
 */
class Migrator
{
    /**
     * The database class
     *
     * @var object
     */
    private $db;

    /**
     * Indicates the current installed version of the extension
     *
     * @var string
     */
    private $installedVersion;

    /**
     * Class constructor
     *
     * @param string $installedVersion  The current extension version
     */
    public function __construct($installedVersion)
    {
        $this->installedVersion = $installedVersion;
        $this->db = Factory::getDbo();
    }

	/**
	 *  Start migration process
	 *
	 *  @return  void
	 */
	public function start()
	{
        $this->set_sib_campaigns_default_v2_version();
        
        if (!$items = $this->getForms())
        {
            return;
        }
        
		foreach ($items as $item)
        {
            $item->params = new Registry($item->params);

            $this->fixContentAppArticleTextOption($item);

            if (version_compare($this->installedVersion, '4.4.11', '<='))
            {
                $this->disableMinimumTimeToSubmit($item);
            }

            if (version_compare($this->installedVersion, '4.4.4', '<='))
            {
                $this->fixNewEditorValueInTasks($item);
            }

            if (version_compare($this->installedVersion, '4.0.2', '<')) 
            {
                $this->fixInputMask($item);
            }

            if (version_compare($this->installedVersion, '2.7.3', '<=')) 
            {
				$this->fixUploadFolder($item);
            }
           
            
            if (version_compare($this->installedVersion, '3.2.8', '<=')) 
            {
                $this->updateSubmissionCountryNamesToCodes($item->id, $item);
                $this->updateFormConditionsCountryNamesToCodes($item);
            }
            

            // Update item using id as the primary key.
            $item->params = json_encode($item->params);
            $this->db->updateObject('#__convertforms', $item, 'id');
        }
	}

    /**
     * In version 4.4.10 we introduced the "Minimum Time to Submit" restriction which is enabled by default to 2 seconds for new forms. 
     * To prevent issues with existing forms, we will disable this option by default.
     *
     * @param  object $item
     * 
     * @return void
     */
    private function disableMinimumTimeToSubmit(&$item)
    {
        // Skip if the option is already set
        if (!is_null($item->params->get('enable_min_time_to_submit')))
        {
            return;
        }

        // To prevent issues with existing forms, we will disable this option by default
        $item->params->set('enable_min_time_to_submit', '0');
    }

    private function fixContentAppArticleTextOption($item)
    {
        if (!$tasks = ModelTasks::getItems($item->id))
        {
            return;
        }

        if (!is_array($tasks) || !count($tasks))
        {
            return;
        }

        foreach ($tasks as $task)
        {
            if ($task['app'] !== 'content')
            {
                continue;
            }

            // If text_source is already set, move on. 
            if (isset($task['app']['options']['text_source']))
            {
                continue;
            }

            // Set text_source to custom
            $task['options']['text_source'] = 'custom';

            ModelTasks::save($task);
        }
    }

    private function fixNewEditorValueInTasks(&$item)
    {
        if (!$tasks = \ConvertForms\Tasks\ModelTasks::getItems($item->id))
        {
            return;
        }

        if (!$tasks || !is_array($tasks) || !count($tasks))
        {
            return;
        }
        
        $allowedApps = [
            
            'content',
            
            'email'
        ];

        foreach ($tasks as $ordering => $_task)
        {
            if (!in_array($_task['app'], $allowedApps))
            {
                continue;
            }

            
            if ($_task['app'] === 'content')
            {
                $_task['options']['fulltext'] = str_replace(["\r\n", "\r", "\n"], '<br>', $_task['options']['fulltext']);
            }
            

            if ($_task['app'] === 'email')
            {
                $_task['options']['body'] = str_replace(["\r\n", "\r", "\n"], '<br>', $_task['options']['body']);
            }

            // Update task
            $_task['ordering'] = $ordering;
            $_task['form_id'] = $item->id;
            \ConvertForms\Tasks\ModelTasks::save($_task);
        }
    }

    private function fixInputMask(&$item)
    {
        if (!$fields = $item->params->get('fields'))
        {
            return;
        }

        foreach ($fields as &$field)
        {
            if (!isset($field->inputmask) || empty($field->inputmask) || !is_scalar($field->inputmask))
            {
                continue;
            }

            $field->inputmask = [
                'options' => 'custom',
                'custom'  => $field->inputmask
            ];
        }
    }

	private function fixUploadFolder(&$item)
	{
        if (!$fields = $item->params->get('fields'))
        {
            return;
        }

        foreach ($fields as &$field)
        {
            if ($field->type != 'fileupload')
            {
                continue;
            }

            if (isset($field->upload_folder_type))
            {
                continue;
            }

            $field->upload_folder_type = 'custom';
        }
    }
    
    /**
     * Finds all SendInBlue campaigns and sets the version to v2 if not set.
     * 
     * @since   2.8.4
     * 
     * @return  void
     */
    private function set_sib_campaigns_default_v2_version()
    {
        if (version_compare($this->installedVersion, '2.8.4', '>=')) 
        {
            return;
        }

        if (!$campaigns = $this->getCampaigns('sendinblue'))
        {
            return;
        }
        
		foreach ($campaigns as $item)
        {
            $item->params = new Registry($item->params);

            // version exists, move on
            if ($version = $item->params->get('version'))
            {
                continue;
            }
            
            // if no version is found, set it to v2
            $item->params->set('version', '2');

            // Also set update existing to default value, true
            $item->params->set('updateexisting', '1');
            
            // Update item using id as the primary key.
            $item->params = json_encode($item->params);
            $this->db->updateObject('#__convertforms_campaigns', $item, 'id');
        }
    }

    
    /**
     * Updates a submission's country names to country codes.
     * 
     * @param   int     $form_id
     * @param   object  $item
     * 
     * @since   3.2.7
     * 
     * @return  void
     */
    private function updateSubmissionCountryNamesToCodes(&$form_id, $item)
    {
        $fields = (array) $item->params->get('fields');

        // Return all country field names
        $formCountryFields = array_filter(array_map(function($field) {
            return $field->type === 'country' ? $field->name : false;
        }, $fields));

        // Skip this form if it does not have a country field
        if (!$formCountryFields)
        {
            return;
        }

        // Get form submissions
        if (!$submissions = $this->getFormSubmissions($form_id))
        {
            return;
        }

        foreach ($formCountryFields as $key => $field_name)
        {
            // Loop each submissions
            foreach ($submissions as $submission)
            {
                // Get submission data
                if (!$submission_data = json_decode($submission->params, true))
                {
                    continue;
                }
        
                // Sanity check
                if (!count($submission_data))
                {
                    continue;
                }
        
                // Ensure the submission has country field related value
                if (!isset($submission_data[$field_name]))
                {
                    continue;
                }
    
                // Update country name to code
                $submission_data[$field_name] = Countries::toCountryCode($submission_data[$field_name]);
    
                // Update submission 
                $submission->params = json_encode($submission_data);
                $this->db->updateObject('#__convertforms_conversions', $submission, 'id');
            }
        }
    }

    /**
     * Updates Conditional Logic of the form to transform
     * Country Names to Country Codes.
     * 
     * @param   object  $item
     * 
     * @since   3.2.7
     * 
     * @return  void
     */
    private function updateFormConditionsCountryNamesToCodes(&$item)
    {
        $fields = (array) $item->params->get('fields');

        // Return all country field IDs
        $formCountryFields = array_values(array_filter(array_map(function($field) {
            return $field->type === 'country' ? $field->key : false;
        }, $fields)));

        // Ensure the form has country fields
        if (!$formCountryFields)
        {
            return;
        }
        
        // Ensure the form has conditional logic rules set up
        if (!$item->params->get('conditionallogic.rules'))
        {
            return;
        }

        // Ensure we can parse them
        if (!$rules = json_decode($item->params->get('conditionallogic.rules'), true))
        {
            return;
        }

        foreach ($rules as $key => &$value)
        {
            // Fix "When"
            if (isset($value['rules']))
            {
                foreach ($value['rules'] as $whenKey => &$whenValue)
                {
                    foreach ($whenValue as $whenRuleKey => &$whenRuleValue)
                    {
                        if (!in_array($whenRuleValue['field'], $formCountryFields))
                        {
                            continue;
                        }
                        
                        if ($newValue = Countries::toCountryCode($whenRuleValue['arg']))
                        {
                            $whenRuleValue['arg'] = $newValue;
                        }
                    }
                }
            }
            
            // Fix "Do"
            if (isset($value['actions']))
            {
                foreach ($value['actions'] as $doKey => &$doValue)
                {
                    if (!in_array($doValue['field'], $formCountryFields))
                    {
                        continue;
                    }
        
                    if ($newValue = Countries::toCountryCode($doValue['arg']))
                    {
                        $doValue['arg'] = $newValue;
                    }
                }
            }

            // Fix "If the condition is not met"
            if (isset($value['else']))
            {
                foreach ($value['else'] as $elseKey => &$elseValue)
                {
                    if (!in_array($elseValue['field'], $formCountryFields))
                    {
                        continue;
                    }

                    if ($newValue = Countries::toCountryCode($elseValue['arg']))
                    {
                        $elseValue['arg'] = $newValue;
                    }
                }
            }
        }

        // Set rules
        $item->params->set('conditionallogic.rules', json_encode($rules));
    }
    

	/**
	 *  Get Forms List
	 *
	 *  @return  object
	 */
	public function getForms()
	{
		$db = $this->db;
		$query = $db->getQuery(true);

		$query
			->select('*')
			->from('#__convertforms');

		$db->setQuery($query);

		return $db->loadObjectList();
	}
    
	/**
	 * Get form submissions.
	 *
	 * @return  object
	 */
	public function getFormSubmissions($form_id = null)
	{
        if (!$form_id)
        {
            return;
        }
        
		$db = $this->db;
		$query = $db->getQuery(true);

		$query
			->select('*')
			->from('#__convertforms_conversions')
			->where($this->db->quoteName('form_id') . ' = ' . $this->db->quote($form_id));

		$db->setQuery($query);

		return $db->loadObjectList();
	}
    

	/**
	 *  Get Campaigns List
	 *
     *  @param   string  $service
     * 
	 *  @return  object
	 */
	public function getCampaigns($service = null)
	{
		$db = $this->db;
		$query = $db->getQuery(true);

		$query
			->select('*')
            ->from('#__convertforms_campaigns');
        
        if ($service)
        {
			$query->where($this->db->quoteName('service') . ' = ' . $this->db->quote($service));
        }

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}