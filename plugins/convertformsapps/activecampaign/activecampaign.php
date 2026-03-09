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
use NRFramework\Integrations\ActiveCampaign;

class plgConvertFormsAppsActiveCampaign extends App
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

        $custom_fields = [];

        // Add phone to custom fields
        if (isset($this->options['phone']) && !empty($this->options['phone']))
        {
            $custom_fields['phone'] = $this->options['phone'];
        }

        // Set custom fields in proper format
        $customfields_ = isset($this->options['customfields']) ? $this->options['customfields'] : [];
        if ($customfields_)
        {
            // Get new custom fields format
            $new_customfields = [];
            
            foreach ($customfields_ as $key => $value)
            {
                if (!isset($value['value']))
                {
                    continue;
                }
                
                $new_customfields[$value['key']] = $value['value'];
            }

            // Add new custom fields to the merge tags
            $custom_fields = array_merge($custom_fields, $new_customfields);
        }

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

        $api->subscribe(
            $this->options['email'],
            trim("{$this->options['firstName']} {$this->options['lastName']}"),
            $this->options['list'],
            $new_tags,
            $custom_fields,
            $this->options['update_existing_subscriber'] === '1'
        );

		if (!$api->success())
        {
            $this->setError($api->getLastError());
        }
	}

    /**
     * Get a list with the authorization fields needed to create a new App Connection.
     *
     * @return array
     */
	protected function getConnectionFormFields()
    {
        return
        [
            $this->field('api_url', [
                'type' => 'text',
                'label' => Text::sprintf('COM_CONVERTFORMS_APP_API_URL', $this->lang('ALIAS')),
                'hint' => Text::sprintf('COM_CONVERTFORMS_APP_API_URL_DESC', $this->lang('ALIAS')),
                'labelBtn' => Text::_('COM_CONVERTFORMS_APP_API_KEY_FIND'),
                'labelBtnLink' => $this->getDocsURL() . '#api_url',
                'required' => true
            ]),
            [
                'name'     => 'api_key',
                'label'    => Text::sprintf('COM_CONVERTFORMS_APP_API_KEY', $this->lang('ALIAS')),
                'hint'     => Text::sprintf('COM_CONVERTFORMS_APP_API_KEY_DESC', $this->lang('ALIAS')),
                'labelBtn' => Text::_('COM_CONVERTFORMS_APP_API_KEY_FIND'),
                'labelBtnLink' => $this->getDocsURL() . '#api_key',
                'required' => true
            ]
        ];
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
            $this->field('firstName', ['required' => false]),
            $this->field('lastName', ['required' => false]),
            $this->field('phone', ['required' => false]),
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
                'name' => Text::_('COM_CONVERTFORMS_APP_SETUP_ACTION'),
                'fields' => [
                    $this->field('list', [
                        'required' => false,
                        'loadOptions' => $this->getAjaxEndpoint('getLists'),
                        'includeSmartTags' => 'Fields'
                    ]),
                    $this->commonField('update_existing_subscriber')
                ]
            ],
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_MATCH_FIELDS'),
                'fields' => $matchFields
            ]
        ];

        return $fields;
	}
    
    /**
     * Get all lists.
     * 
     * @return  array
     */
    public function getLists()
    {
        $api = $this->getApiOrDie();
        
        if (!$lists = $api->getLists())
        {
            return [];
        }

        return array_map(function($list)
        {
            return [
                'value' => $list['id'],
                'label' => $list['name'],
                'desc'  => $list['id'],
            ];
        }, $lists);
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

        $api->get('users/me');

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
        $fields = $api->getAllCustomFields();

        $props = [];

        if (!$fields)
        {
            return $props;
        }

        foreach ($fields as $key => $value)
        {
            $props[] = [
                'value' => $key,
                'label' => $value['title'],
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

        if (!$api_url = $this->connection->get('api_url'))
        {
            throw new \Exception('Invalid API URL supplied.');
        }

        if (!$api_key = $this->connection->get('api_key'))
        {
            throw new \Exception('Invalid API Key supplied.');
        }

        $options = [
            'endpoint' => $api_url,
            'api' => $api_key
        ];
        
        return new ActiveCampaign($options);
    }
}