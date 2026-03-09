<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\NoteField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use NRFramework\Functions;
use NRFramework\HTML;

class JFormFieldSPFreeDisplayConditions extends NoteField
{
    public function getInput()
    {
        Functions::loadLanguage('com_smilepack');
        
        ob_start();
        echo HTML::renderProButton(Text::_('NR_PUBLISHING_ASSIGNMENTS'), '');
        $proBtn = ob_get_clean();

        $html = '<div style="display: inline-flex; position: relative;">';
        $html .= '<div style="position: absolute;left: 0;top: 0;width: 100%;height: 100%;display: inline-flex; flex-direction: column; gap: 10px; justify-content: center;text-align: center;backdrop-filter: blur(1px);background: rgba(255, 255, 255, .5);align-items: center;">';
        $html .= '<h2 style="margin: 0;">' . Text::_('NR_PUBLISHING_ASSIGNMENTS') . '</h2>';
        $html .= '<p style="margin: 0; max-width: 400px;">' . Text::_('COM_SMILEPACK_DISPLAY_CONDITIONS_FREE_OVERLAY_DESC') . '</p>';
        $html .= $proBtn;
        $html .= '</div>';
        $html .= '<img src="' . Uri::root() . 'media/com_smilepack/img/module-display-conditions-preview.png' . '" alt="' . Text::_('COM_SMILEPACK_DISPLAY_CONDITIONS_PREVIEW_IMG') . '" />';
        $html .= '</div>';

        return $html;
    }
}