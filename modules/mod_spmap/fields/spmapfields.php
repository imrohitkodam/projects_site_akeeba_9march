<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldSPMapFields extends ListField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return  array
     */
    protected function getOptions() 
    {
        $fields = $this->getFields();

        if (!count($fields))
        {
            return [HTMLHelper::_('select.option', '', Text::_('SPMAP_NO_ACF_FIELDS_FOUND'), 'value', 'text', true)];
        }

        $options = array();

        foreach ($fields as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, $option->title);
        }

        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }

    private function getFields()
    {
        $context = isset($this->element['context']) ? (string) $this->element['context'] : 'com_content.article';
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title');
        $query->from('#__fields');
        $query->where('context = ' . $db->quote($context));
        $query->where('state = 1');
        $query->where('type = ' . $db->quote('acfmap'));
        $query->order('ordering ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }
}