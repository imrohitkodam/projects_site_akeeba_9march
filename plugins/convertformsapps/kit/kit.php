<?php

/**
 * @package         Convert Forms
 * @version         5.1.2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Tasks\App;
use ConvertForms\Tasks\Helper;
use ConvertForms\Tasks\AppConnection;
use Joomla\CMS\Language\Text;
use NRFramework\Integrations\Kit;

class plgConvertFormsAppsKit extends App
{   
    // This app requires connection with a 3rd party API
    use AppConnection;

	/**
	 * The Subscribe trigger
	 *
	 * @return void
	 */
	public function actionSubscribe()
	{
        $api = $this->getApiOrDie();

        $first_name = isset($this->options['first_name']) ? $this->options['first_name'] : '';

        $tags = isset($this->options['tags']) ? $this->options['tags'] : [];
        $new_tags = [];
        // prepare tags
        if ($tags)
        {
            foreach ($tags as $tag)
            {
                $value = isset($tag['value']) ? $tag['value'] : [];
                if (!$value)
                {
                    continue;
                }

                $value = explode(',', $value);
                
                $new_tags = array_merge($new_tags, $value);
            }

            $new_tags = array_filter($new_tags);
        }

        // Set custom fields in proper format
        $customfields = isset($this->options['customfields']) ? $this->options['customfields'] : [];
        $new_customfields = [];
        if ($customfields)
        {
            foreach ($customfields as $key => $value)
            {
                if (!isset($value['key']))
                {
                    continue;
                }
                
                $new_customfields[$value['key']] = $value['value'];
            }
        }

        $api->subscribe(
            $this->options['email'],
            $first_name,
            $new_tags,
            $new_customfields
        );

		if (!$api->success())
        {
            $this->setError($api->getLastError());
        }
	}

    /**
     * Get a list with the fields needed to setup the app's event.
     *
     * @return array
     */
	public function getActionSubscribeSetupFields()
	{
        $matchFields = [
            $this->commonField('email'),
            $this->field('first_name', ['required' => false]),
            $this->field('customfields', [
                'type' => 'keyvalue',
                'required' => false,
                'keyField' => [
                    'type' => 'select',
                    'options' => $this->getCustomFields(),
                    'placeholder' => $this->lang('SELECT_FIELD')
                ]
            ]),
            $this->field('tags', [
                'type'  => 'repeat-select',
                'label' => Text::_('COM_CONVERTFORMS_APP_TAGS'),
                'hint'  => Text::_('COM_CONVERTFORMS_APP_TAGS_DESC'),
                'required' => false
            ]),
        ];

        $fields = [
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_MATCH_FIELDS'),
                'fields' => $matchFields
            ]
        ];

        return $fields;
	}
    
    /**
     * Test an app's connection with the given credentials
     *
     * @return void
     */
	public function testConnection($connection_options = null)
    {
        if ($connection_options)
        {
            $this->setConnection($connection_options);
        }

        $api = $this->getApiOrDie();

        $api->get('account');

        if (!$api->success())
		{
            $this->setError($api->getLastError());
            return false;
        }

        return true;
	}

    /**
     * Return all custom fields.
     *
     * @return array
     */
    private function getCustomFields()
    {
        $api = $this->getApiOrDie();
        $fields = $api->get('custom_fields');

        $props = [];

        if (!isset($fields['custom_fields']))
        {
            return $props;
        }

        if (!count($fields['custom_fields']))
        {
            return $props;
        }

        foreach ($fields['custom_fields'] as $field)
        {
            $props[] = [
                'value' => $field['key'],
                'label' => $field['label'],
                'desc'  => ''
            ];
        }

        return $props;
    }

    /**
     * Return the app's respective class 
     *
     * @return object
     */
    private function getApiOrDie()
    {
        if (!isset($this->connection) || !$this->connection)
        {
            throw new \Exception('Invalid connection');
        }

        if (!$api_key = $this->connection->get('api_key'))
        {
            throw new \Exception('Invalid API Key supplied.');
        }

        return new Kit($api_key);
    }
}