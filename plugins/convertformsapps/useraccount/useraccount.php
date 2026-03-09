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

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Tasks\App;
use ConvertForms\Tasks\Helper;
use NRFramework\Functions;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Mail\MailTemplate;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Access\Access;
use Joomla\Utilities\ArrayHelper;

class plgConvertFormsAppsUserAccount extends App
{
	/**
	 * The trigger Subscribe
	 *
	 * @return void
	 */
	public function actionRegister()
	{
        $data = $this->options;

        $groups = Helper::readRepeatSelect($data['groups']);

        // Make sure we have a 1-level array. This is crucial in case of the User Groups property is mapped to a Checkboxes field which returns an array.
        $groups = ArrayHelper::flatten($groups);

        // Make sure we have a unique list of numbers
        $groups = array_unique(array_map('intval', array_values($groups)));

        // Get all user groups
        $allGroups = UserGroupsHelper::getInstance()->getAll();

        // Remove groups with Super User or Administrator privileges
        $groups = array_filter($groups, function ($groupId) use ($allGroups)
        {
            foreach ($allGroups as $group)
            {
                if ($group->id == $groupId)
                {
                    // Use the new method to check if the group should be excluded
                    if ($this->isGroupExcluded($group))
                    {
                        return false;
                    }
                }
            }
    
            return true;
        });

        $data['groups'] = $groups;

        return $this->register($data);
	}

    /**
     * Get a list with the fields needed to setup the app's event.
     *
     * @return array
     */
	public function getActionRegisterSetupFields()
	{
        Factory::getLanguage()->load('com_users');

        // Get Users component configuration options
        $userSettings = ComponentHelper::getParams('com_users');

		$globalUserActivation = (int) $userSettings->get('useractivation');

        switch ($globalUserActivation)
        {
            case 0:
                $globalUserActivation = 'JNONE';
                break;

            case 1:
                $globalUserActivation = 'COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_SELFACTIVATION';
                break;

            case 2:
                $globalUserActivation = 'COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION';
        }

		$globalNewUserGroup = (int) $userSettings->get('new_usertype');

        $fields = [
            $this->field('email'),
            $this->field('name'),
            $this->field('username'),
            $this->field('password', [
                'options' => [
                    [
                        'label' => $this->lang('GENERATE_RANDOM_PASSWORD'),
                        'value' => '{randomid}'
                    ]
                ]
            ]),
            $this->field('require_reset', [
                'type'  => 'bool',
                'value' => '0',
                'required' => false,
                'label' => $this->lang('REQUIRE_PASSWORD_RESET'),
                'hint'  => $this->lang('REQUIRE_PASSWORD_RESET_DESC'),
                'includeSmartTags' => false
            ]),
            [
                'name'  => 'groups',
                'type'  => 'repeat-select',
                'value' => [
                    [
                        'value' => $globalNewUserGroup
                    ]
                ],
                'label' => $this->lang('GROUPS'),
                'hint'  => $this->lang('GROUPS_DESC'),
                'options' => $this->getGroups()
            ],
            [
                'name'  => 'activation',
                'value' => 'use_global',
                'label' => Text::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_LABEL'),
                'hint'  => $this->lang('ACTIVATION_DESC'),
                'options' => [
                    [
                        'label' => Text::sprintf('JGLOBAL_USE_GLOBAL_VALUE', Text::_($globalUserActivation)),
                        'value' => 'use_global'
                    ],
                    [
                        'label' => Text::_('JNONE'),
                        'value' => '0'
                    ],
                    [
                        'label' => Text::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_SELFACTIVATION'),
                        'value' => '1'
                    ],
                    [
                        'label' => Text::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION'),
                        'value' => '2'
                    ],
                ]
            ]
        ];

        if ($customFields = $this->getCustomFields())
        {
            $fields[] = $this->field('custom_fields', [
                'type' => 'keyvalue',
                'required' => false,
                'keyField' => [
                    'type' => 'select',
                    'options' => $customFields,
                    'placeholder' => $this->lang('SELECT_CUSTOM_FIELD')
                ]
            ]);
        }
        

        return [
            [
                'name' => Text::_('COM_CONVERTFORMS_APP_SETUP_ACTION'),
                'fields' => $fields
            ]
        ];
	}

    /**
     * Get a list with all allowed custom fields.
     * 
     * @return  array
     */
    private function getCustomFields()
    {
        $allowedFieldTypes = ConvertForms\Tasks\Helper::getAllowedCustomFieldsTypesInRepeater();
        $allowedFieldTypes = array_map(function($value) {
            return '"' . $value . '"';
        }, $allowedFieldTypes);

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select(
                [
                    $db->quoteName('a.id', 'value'),
                    $db->quoteName('a.type', 'type'),
                    $db->quoteName('a.name', 'name'),
                    $db->quoteName('a.title', 'label')
                ]
            )
            ->from($db->quoteName('#__fields', 'a'))
            ->where($db->quoteName('a.context') . ' = ' . $db->quote('com_users.user'))
            ->where($db->quoteName('a.state') . ' = 1')
            ->where($db->quoteName('a.type') . ' IN (' . implode(',', $allowedFieldTypes) . ')');

        $db->setQuery($query);

        $data = $db->loadObjectList();

        $fields = [];

        foreach ($data as $field)
        {
            $fields[] = [
                'value' => $field->value,
                'label' => $field->label,
                'type' => $field->type,
                'desc' => $field->name
            ];
        }

        return $fields;
    }

    /**
     * Method to create a Joomla user account
     *
     * @param array $temp  The user data
     * 
	 * @return  mixed  The user id on success, false on failure.
     */
    private function register($temp)
    {
        Factory::getLanguage()->load('com_users');

        $data = [
            'name'   	    => $temp['name'],
            'username'	    => $temp['username'],
            'password'	    => $temp['password'],
            'password2'	    => $temp['password'],
            'email'		    => PunycodeHelper::emailToPunycode($temp['email']),
            'groups'	    => $temp['groups'],
            'requireReset'  => $temp['require_reset']
        ];

        // Get Users component configuration options
		$params = ComponentHelper::getParams('com_users');

        $useractivation = (int) ($temp['activation'] == 'use_global' ? $params->get('useractivation') : $temp['activation']);
		$sendpassword = $params->get('sendpassword', 1);

		// Check if the user needs to activate their account.
		if ($useractivation == 1 || $useractivation == 2)
		{
			$data['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
			$data['block'] = 1;
		}
        
        // Load the users plugin group.
        $user = new User;

        // Bind the data.
        if (!$user->bind($data))
        {
            $this->setError($user->getError());
            return false;
        }
        
        // Load the users plugin group.
		PluginHelper::importPlugin('user');

        if (!$user->save())
        {
            $this->setError(Text::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
            return false;
        }

        // Populate custom fields
        $customFields = isset($temp['custom_fields']) && is_array($temp['custom_fields']) && count($temp['custom_fields']) ? $temp['custom_fields'] : [];
        $this->populateCustomFields($user->id, $customFields);

        // Send confirmation emails
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

        // Compile the notification mail values.
        $data = $user->getProperties();
        $data['fromname'] = $app->get('fromname');
        $data['mailfrom'] = $app->get('mailfrom');
        $data['sitename'] = $app->get('sitename');
        $data['siteurl'] = Uri::root();

        // Handle account activation/confirmation emails.
        if ($useractivation == 2)
        {
            // Set the link to confirm the user email.
            $linkMode = $app->get('force_ssl', 0) == 2 ? Route::TLS_FORCE : Route::TLS_IGNORE;

            $data['activate'] = Route::link(
                'site',
                'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
                false,
                $linkMode,
                true
            );

            $mailtemplate = 'com_users.registration.user.admin_activation';
        }
        elseif ($useractivation == 1)
        {
            // Set the link to activate the user account.
            $linkMode = $app->get('force_ssl', 0) == 2 ? Route::TLS_FORCE : Route::TLS_IGNORE;

            $data['activate'] = Route::link(
                'site',
                'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
                false,
                $linkMode,
                true
            );

            $mailtemplate = 'com_users.registration.user.self_activation';
        }
        else
        {
            $mailtemplate = 'com_users.registration.user.registration_mail';
        }

        if ($sendpassword)
        {
            $mailtemplate .= '_w_pw';
        }

        // Try to send the registration email.
        try
        {
            $mailer = new MailTemplate($mailtemplate, $app->getLanguage()->getTag());
            $mailer->addTemplateData($data);
            $mailer->addRecipient($data['email']);
            $mailer->send();
        }
        catch (\Exception $exception)
        {
            $this->setError(Text::_($exception->getMessage()));
            return false;
        }
        
        return $user;
    }

    /**
     * Populate custom fields.
     * 
     * @param int    $id            The user ID
     * @param array  $customFields  The user custom fields
     * 
     * @return void
     */
    private function populateCustomFields($id, $customFields)
    {
        if (!$customFields)
        {
            return;
        }

        // Get all custom fields
        if (!$fields = $this->getCustomFields())
        {
            return;
        }

        // Multi-value fields defined here
        $multiValueFields = ['checkboxes', 'list', 'acfarticles', 'imagelist', 'usergrouplist'];

        foreach ($customFields as $field)
        {
            if (!isset($field['key']) || !isset($field['value']))
            {
                continue;
            }
            
            // Find the custom field
            $fieldIndex = array_search($field['key'], array_column($fields, 'value'));
            $customField = isset($fields[$fieldIndex]) ? $fields[$fieldIndex] : null;

            // If the custom field is multi-value, explode the value
            if ($customField && in_array($customField['type'], $multiValueFields) && is_string($field['value']))
            {
                $field['value'] = array_map('trim', array_filter(explode(',', $field['value'])));
            }

            // Multi-value field
            if (is_array($field['value']))
            {
                foreach ($field['value'] as $value)
                {
                    $_value = (object) [
                        'item_id'  => $id,
                        'field_id' => $field['key'],
                        'value'    => $value
                    ];

                    Factory::getDbo()->insertObject('#__fields_values', $_value);
                }
            }
            // Single value field
            else
            {
                $value = (object) [
                    'item_id'  => $id,
                    'field_id' => $field['key'],
                    'value'    => $field['value']
                ];

                Factory::getDbo()->insertObject('#__fields_values', $value);
            }
        }
    }

    /**
     * API endpoint that returns a list of all user groups of the site
     *
     * @return array
     */
    private function getGroups()
    {
        $groups = UserGroupsHelper::getInstance()->getAll();
        $grps = [];

        foreach ($groups as $group)
        {
            // Use the new method to check if the group should be excluded
            if ($this->isGroupExcluded($group))
            {
                continue;
            }

            $grps[] = [
                'value' => $group->id,
                'label' => str_repeat('- ', $group->level) . $group->title
            ];
        }

        return $grps;
    }

    /**
     * Check if a user group should be excluded based on its permissions.
     *
     * This method excludes user groups that have administrative privileges, 
     * such as Super Users, Administrators, or any user group that has access 
     * to the admin interface (e.g., core.admin or core.manage permissions).
     *
     * @param   object  $group  The user group object.
     *
     * @return  bool  True if the group should be excluded, false otherwise.
     */
    private function isGroupExcluded($group)
    {
        return Access::checkGroup($group->id, 'core.admin') || Access::checkGroup($group->id, 'core.manage');
    }
}