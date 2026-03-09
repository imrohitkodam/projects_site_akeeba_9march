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
use NRFramework\Integrations\ElasticEmail;

class plgConvertFormsAppsElasticEmail extends App
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
            'update_existing_subscriber',
            'double_optin'
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
                
                $merge_tags['field'][$value['key']] = $value['value'];
            }

            // Delete custom fields
            unset($merge_tags['customfields']);
        }

        $api->subscribe(
            $this->options['email'],
            $this->options['list'],
            $merge_tags,
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
            $this->field('firstName', ['required' => false]),
            $this->field('lastName', ['required' => false]),
            $this->field('customfields', [
                'type' => 'keyvalue',
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
                    $this->commonField('update_existing_subscriber'),
                    $this->commonField('double_optin')
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

        $api->get('account/load?api_key=' . $this->connection->get('api_key'));

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

        if (!$api_key = $this->connection->get('api_key'))
        {
            throw new \Exception('Invalid API Key supplied.');
        }

        return new ElasticEmail($api_key);
    }
}