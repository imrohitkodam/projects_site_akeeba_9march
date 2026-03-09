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
use NRFramework\Integrations\CampaignMonitor;

class plgConvertFormsAppsCampaignMonitor extends App
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
        $custom_fields_saved = isset($this->options['customfields']) ? $this->options['customfields'] : [];
        foreach ($custom_fields_saved as $custom_field)
        {
            $custom_fields[$custom_field['key']] = $custom_field['value'];
        }

        $api->subscribe(
            $this->options['email'],
            $this->options['name'],
            $this->options['list'],
            $custom_fields
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

        $api->get('systemdate.json');

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

        return new CampaignMonitor($api_key);
    }
}