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

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;

class JFormFieldPDFSubmission extends TextField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        JLoader::register('PDFHelper', __DIR__ . '/helper/pdfhelper.php');
        
        $this->disabled = true;
        $this->class = 'span12';
        
        $id = $this->form->getData()->get('id');
        $modelSubmission = BaseDatabaseModel::getInstance('Conversion', 'ConvertFormsModel', ['ignore_request' => true]);
        $submission = $modelSubmission->getItem($id);

        // if no value is given, hide the field
        if (!$this->value = PDFHelper::getSubmissionPDF($submission))
        {
            $this->hidden = true;
            return;
        }

        return parent::getInput() . $this->getHelperButtons($this->value);
    }

    /**
     * Renders buttons to view/download the PDF
     * 
     * @param   string  $pdf_url
     * 
     * @return  string
     */
    private function getHelperButtons($pdf_url)
    {
        return '<div style="margin-top: 10px;">' .
                    '<a href="' . $pdf_url . '" target="_blank" class="btn btn-secondary">' . Text::_('PLG_CONVERTFORMSTOOLS_PDF_SUBMISSION_VIEW_BTN') . '</a>&nbsp;&nbsp;' .
                    '<a href="' . $pdf_url . '" class="btn btn-secondary" download>' . Text::_('PLG_CONVERTFORMSTOOLS_PDF_SUBMISSION_DOWNLOAD_BTN') . '</a>' .
                '</div>';
    }
}