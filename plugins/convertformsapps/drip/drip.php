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
use NRFramework\Integrations\Drip;

class plgConvertFormsAppsDrip extends App
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
            'list',
            'email',
            'name',
            'update_existing_subscriber',
            'double_optin',
        ];

        $merge_tags = array_diff_key($this->options, array_flip($keysToRemove));

        // Set custom fields in proper format
        $custom_fields = isset($merge_tags['customfields']) ? $merge_tags['customfields'] : [];

        if ($custom_fields)
        {
            // Get new custom fields format
            $new_custom_fields = [];
            foreach ($custom_fields as $key => $value)
            {
                if (!isset($value['value']))
                {
                    continue;
                }
                
                $new_custom_fields[$value['key']] = $value['value'];
            }

            // Delete old custom fields value
            unset($merge_tags['customfields']);
            
            // Add new custom fields to the merge tags
            $merge_tags = array_merge($merge_tags, $new_custom_fields);
        }

        $tags = isset($merge_tags['tags']) ? $merge_tags['tags'] : [];
        if ($tags)
        {
            $tags = array_column($tags, 'value');
            unset($merge_tags['tags']);
        }

        $api->subscribe(
            $this->options['email'],
            $this->options['list'],
            $this->options['name'],
            $merge_tags,
            $tags,
            $this->options['update_existing_subscriber'] === '1',
            $this->options['double_optin'] === '1'
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
            $this->field('name', ['required' => false]),
            $this->field('customfields', [
                'type' => 'keyvalue',
                'required' => false,
                'keyField' => [
                    'type' => 'select',
                    'options' => $this->getCustomFields(),
                    'placeholder' => $this->lang('SELECT_CUSTOM_FIELD')
                ]
            ]),
            $this->field('tags', [
                'type'  => 'repeat-select',
                'options' => $this->getTags(),
                'label' => Text::_('COM_CONVERTFORMS_APP_TAGS'),
                'hint'  => Text::_('COM_CONVERTFORMS_APP_TAGS_DESC')
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
                    $this->commonField('update_existing_subscriber'),
                    $this->commonField('double_optin'),
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
		$api->setEndpoint('https://api.getdrip.com/v2/');
        
        $api->get('accounts/' . $connection_options['account_id']);

        if (!$api->success())
		{
            $this->setError($api->getLastError());
            return false;
        }

        return true;
	}

    /**
     * Return all account's custom fields.
     *
     * @return array
     */
    private function getCustomFields()
    {
        $api = $this->getApiOrDie();
        $fields = $api->get('custom_field_identifiers');

        $customfields = [];

        // Populate with default fields
        $default = [
            'address1',
            'address2',
            'city',
            'state',
            'zip',
            'country',
            'phone'
        ];
        foreach ($default as $property)
        {
            $customfields[] = [
                'value' => $property,
                'label' => $property,
                'desc'  => ''
            ];
        }

        if (!isset($fields['custom_field_identifiers']))
        {
            return $customfields;
        }

        foreach ($fields['custom_field_identifiers'] as $property)
        {
            $customfields[] = [
                'value' => $property,
                'label' => $property,
                'desc'  => ''
            ];
        }

        return $customfields;
    }

    /**
     * Return all account's tags.
     *
     * @return array
     */
    private function getTags()
    {
        $api = $this->getApiOrDie();
        $fields = $api->get('tags');

        $data = [];

        if (!isset($fields['tags']))
        {
            return $data;
        }

        foreach ($fields['tags'] as $property)
        {
            $data[] = [
                'value' => $property,
                'label' => $property,
                'desc'  => ''
            ];
        }

        return $data;
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
            [
                'name'     => 'api_key',
                'label'    => Text::sprintf('COM_CONVERTFORMS_APP_API_KEY', $this->lang('ALIAS')),
                'hint'     => Text::sprintf('COM_CONVERTFORMS_APP_API_KEY_DESC', $this->lang('ALIAS')),
                'labelBtn' => Text::_('COM_CONVERTFORMS_APP_API_KEY_FIND'),
                'labelBtnLink' => $this->getDocsURL() . '#api_key',
                'required' => true
            ],
            [
                'name'     => 'account_id',
                'label'    => Text::_('COM_CONVERTFORMS_APPS_DRIP_ACCOUNT_ID'),
                'hint'     => Text::_('COM_CONVERTFORMS_APPS_DRIP_ACCOUNT_ID_DESC'),
                'labelBtn' => Text::_('COM_CONVERTFORMS_APPS_DRIP_ACCOUNT_ID_FIND'),
                'labelBtnLink' => $this->getDocsURL() . '#account_id',
                'required' => true
            ],
        ];
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

        if (!$account_id = $this->connection->get('account_id'))
        {
            throw new \Exception('Invalid Account ID supplied.');
        }

        $options = [
            'api' => $api_key,
            'account_id' => $account_id
        ];

        return new Drip($options);
    }
}