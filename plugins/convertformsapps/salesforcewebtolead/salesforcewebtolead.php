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
use Joomla\CMS\Language\Text;
use NRFramework\Integrations\Salesforce;

class plgConvertFormsAppsSalesforceWebToLead extends App
{
	/**
	 * The Subscribe trigger
	 *
	 * @return void
	 */
	public function actionSubscribe()
	{
        $org_id = isset($this->options['org_id']) ? $this->options['org_id'] : '';
        $test_mode = isset($this->options['test_mode']) && $this->options['test_mode'] === '1';

        $api = $this->getApiOrDie($org_id, $test_mode);

        $params = [
            'email' => isset($this->options['email']) ? $this->options['email'] : '',
            'first_name' => isset($this->options['first_name']) ? $this->options['first_name'] : '',
            'last_name' => isset($this->options['last_name']) ? $this->options['last_name'] : '',
            'phone' => isset($this->options['phone']) ? $this->options['phone'] : '',
            'company' => isset($this->options['company']) ? $this->options['company'] : '',
            'title' => isset($this->options['title']) ? $this->options['title'] : '',
            'street' => isset($this->options['street']) ? $this->options['street'] : '',
            'city' => isset($this->options['city']) ? $this->options['city'] : '',
            'state' => isset($this->options['state']) ? $this->options['state'] : '',
            'zip' => isset($this->options['zip']) ? $this->options['zip'] : '',
            'description' => isset($this->options['description']) ? $this->options['description'] : ''
        ];

        // Set custom fields in proper format
        $customfields = isset($this->options['customfields']) ? $this->options['customfields'] : [];
        if ($customfields)
        {
            foreach ($customfields as $key => $value)
            {
                if (!isset($value['value']))
                {
                    continue;
                }
                
                $params[$value['key']] = is_array($value['value']) ? implode(';', $value['value']) : $value['value'];
            }
        }

        $api->subscribe(
            $this->options['email'],
            $params
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
        return [
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_SETUP_ACTION'),
                'fields' => [
                    $this->field('org_id', ['required' => true]),
                ]
            ],
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_MATCH_FIELDS'),
                'fields' => [
                    $this->commonField('email'),
                    $this->field('first_name', ['required' => false]),
                    $this->field('last_name', ['required' => false]),
                    $this->field('phone', ['required' => false]),
                    $this->field('company', ['required' => false]),
                    $this->field('title', ['required' => false]),
                    $this->field('street', ['required' => false]),
                    $this->field('city', ['required' => false]),
                    $this->field('state', ['required' => false]),
                    $this->field('zip', ['required' => false]),
                    $this->field('description', ['required' => false]),
                    $this->field('customfields', [
                        'type' => 'keyvalue',
                        'required' => false
                    ]),
                    $this->field('test_mode', [
                        'type'  => 'bool',
                        'value' => '0',
                        'includeSmartTags' => 'Fields'
                    ])
                ]
            ]
        ];
	}
    
    /**
     * Return the app's respective class 
     *
     * @param   string  $org_id
     * @param   bool    $test_mode
     * 
     * @return  object
     */
    private function getApiOrDie($org_id = null, $test_mode = false)
    {
        if (!$org_id)
        {
            throw new \Exception('Invalid Organization ID supplied.');
        }

        $options = [
            'api' => $org_id,
            'test_mode' => $test_mode
        ];
        
        return new Salesforce($options);
    }
}