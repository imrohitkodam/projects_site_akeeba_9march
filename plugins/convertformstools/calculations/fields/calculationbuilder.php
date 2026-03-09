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

use Joomla\CMS\Form\Field\TextareaField;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldCalculationBuilder extends TextareaField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        // Disable form rendering on textarea change
        $this->class .= ' norender';

        $layout = new FileLayout('calculationbuilder_tmpl', __DIR__);
        $html = $layout->render();

        HTMLHelper::script('plg_convertformstools_calculations/calculation_builder.js', ['relative' => true, 'version' => 'auto']);

        return '<div class="calculation-builder">' . $html . parent::getInput() . '</div>';
    }
}