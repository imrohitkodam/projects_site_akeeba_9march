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
use NRFramework\Integrations\IContact;

class plgConvertFormsAppsIContact extends App
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
            'email'
        ];

        $merge_tags = array_diff_key($this->options, array_flip($keysToRemove));

        // Set custom_fields in proper format
        $custom_fields = isset($merge_tags['customfields']) ? $merge_tags['customfields'] : [];
        if ($custom_fields)
        {
            foreach ($custom_fields as $key => $value)
            {
                if (!isset($value['value']))
                {
                    continue;
                }
                
                $merge_tags[$value['key']] = $value['value'];
            }

            // Delete custom fields
            unset($merge_tags['customfields']);
        }

        $api->subscribe(
            $this->options['email'],
            $merge_tags,
            $this->options['list'],
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
            $this->field('prefix', ['required' => false]),
            $this->field('firstName', ['required' => false]),
            $this->field('lastName', ['required' => false]),
            $this->field('suffix', ['required' => false]),
            $this->field('street', ['required' => false]),
            $this->field('street2', ['required' => false]),
            $this->field('city', ['required' => false]),
            $this->field('state', ['required' => false]),
            $this->field('postalCode', ['required' => false]),
            $this->field('phone', ['required' => false]),
            $this->field('fax', ['required' => false]),
            $this->field('business', ['required' => false]),
            $this->field('customfields', [
                'type' => 'keyvalue',
                'required' => false,
                'keyField' => [
                    'type' => 'select',
                    'options' => $this->getCustomFields(),
                    'placeholder' => $this->lang('SELECT_PROPERTY')
                ]
            ])
        ];

        $fields = [
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_SETUP_ACTION'),
                'fields' => [
                    $this->field('list', [
                        'required' => false,
                        'loadOptions' => $this->getAjaxEndpoint('getLists'),
                        'includeSmartTags' => 'Fields'
                    ])
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
     * Get a list with the authorization fields needed to create a new App Connection.
     *
     * @return array
     */
	protected function getConnectionFormFields()
    {
        return
        [
            $this->field('api_app_id', [
                'type' => 'text',
                'label' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_ID'),
                'hint' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_ID_DESC'),
                'labelBtn' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_ID_FIND'),
                'labelBtnLink' => $this->getDocsURL() . '#app-credentials',
                'required' => true
            ]),
            [
                'name'     => 'api_username',
                'label' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_USERNAME'),
                'hint' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_USERNAME_DESC'),
                'labelBtn' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_USERNAME_FIND'),
                'labelBtnLink' => $this->getDocsURL() . '#app-credentials',
                'required' => true
            ],
            [
                'name'     => 'api_password',
                'label' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_PASSWORD'),
                'hint' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_PASSWORD_DESC'),
                'labelBtn' => Text::_('PLG_CONVERTFORMSAPPS_ICONTACT_APP_PASSWORD_FIND'),
                'labelBtnLink' => $this->getDocsURL() . '#app-credentials',
                'required' => true
            ],
        ];
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
     * Get all custom fields.
     * 
     * @return  array
     */
    public function getCustomFields()
    {
        $api = $this->getApiOrDie();
        
        $custom_fields = $api->get($api->accountID .'/c/' . $api->clientFolderID . '/customfields');
        
        if (!isset($custom_fields['customfields']) || !is_array($custom_fields['customfields']))
        {
            return [];
        }

        return array_map(function($field)
        {
            return [
                'value' => $field['customFieldId'],
                'label' => $field['publicName'],
                'desc'  => $field['customFieldId'],
            ];
        }, $custom_fields['customfields']);
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

        $api->get('');

        if (!$api->success())
		{
            $this->setError($api->getLastError());
            return false;
        }

        return true;
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

        if (!$api_app_id = $this->connection->get('api_app_id'))
        {
            throw new \Exception('Invalid API App ID supplied.');
        }

        if (!$api_username = $this->connection->get('api_username'))
        {
            throw new \Exception('Invalid API Username supplied.');
        }

        if (!$api_password = $this->connection->get('api_password'))
        {
            throw new \Exception('Invalid API Password supplied.');
        }

        return new IContact([
            'appID' => $api_app_id,
            'username' => $api_username,
            'appPassword' => $api_password
        ]);
    }
}