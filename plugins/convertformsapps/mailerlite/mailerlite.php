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
use NRFramework\Integrations\MailerLite;

class plgConvertFormsAppsMailerLite extends App
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

        // Calculate merge tags
        $keysToRemove = [
            'groups',
            'email',
            'update_existing_subscriber',
            'subscriber_status'
        ];

        $merge_tags = array_diff_key($this->options, array_flip($keysToRemove));

        // Set fields in proper format
        $custom_fields = isset($merge_tags['fields']) ? $merge_tags['fields'] : [];

        if ($custom_fields)
        {
            // Get new fields format
            $new_fields = [];
            foreach ($custom_fields as $key => $value)
            {
                if (!isset($value['value']))
                {
                    continue;
                }
                
                $new_fields[$value['key']] = $value['value'];
            }

            // Add new fields to the merge tags
            $custom_fields = $new_fields;
        }

        $api->subscribe(
            $this->options['email'],
            $custom_fields,
            $this->options['groups'],
            $this->options['subscriber_status'],
            $this->options['update_existing_subscriber'] === '1'
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
            $this->field('fields', [
                'type' => 'keyvalue',
                'required' => false,
                'keyField' => [
                    'type' => 'select',
                    'options' => $this->getFields(),
                    'placeholder' => $this->lang('SELECT_FIELD')
                ]
            ]),
        ];

        $fields = [
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_SETUP_ACTION'),
                'fields' => [
                    $this->field('groups', [
                        'required' => false,
                        'loadOptions' => $this->getAjaxEndpoint('getGroups'),
                        'includeSmartTags' => 'Fields',
                        'multiple' => true
                    ]),
                    $this->field('subscriber_status', [
                        'required' => false,
                        'value' => 'active',
                        'options' => [
                            [
                                'value' => 'active',
                                'label' => $this->lang('ACTIVE')
                            ],
                            [
                                'value' => 'unsubscribed',
                                'label' => $this->lang('UNSUBSCRIBED')
                            ],
                            [
                                'value' => 'unconfirmed',
                                'label' => $this->lang('UNCONFIRMED')
                            ],
                            [
                                'value' => 'bounced',
                                'label' => $this->lang('BOUNCED')
                            ],
                            [
                                'value' => 'junk',
                                'label' => $this->lang('JUNK')
                            ],
                        ]
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
     * Get all groups.
     * 
     * @return  array
     */
    public function getGroups()
    {
        $api = $this->getApiOrDie();
        
        if (!$lists = $api->getGroups())
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

        $api->get('subscribers');

        if (!$api->success())
		{
            $this->setError($api->getLastError());
            return false;
        }

        return true;
	}

    /**
     * Return all account's attributes required by the Attributes repeater field.
     *
     * @return array
     */
    private function getFields()
    {
        $api = $this->getApiOrDie();
        $response = $api->get('fields');

        $props = [];

        if (!isset($response['data']) || !is_array($response['data']))
        {
            return $props;
        }

        foreach ($response['data'] as $field)
        {
            $props[] = [
                'value' => $field['key'],
                'label' => $field['name'],
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

        return new MailerLite($api_key);
    }
}