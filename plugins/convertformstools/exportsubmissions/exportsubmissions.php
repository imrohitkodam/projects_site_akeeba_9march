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

use ConvertForms\Export;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Log\Log;
USE Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class PlgConvertFormsToolsExportSubmissions extends CMSPlugin
{
    /**
     * The Joomla application object
     *
     * @var object
     */
    protected $app;

    /**
     * Remove old uploaded files based on the preferences set in each File Upload Field.
     *
     * @return void
     */
    public function onConvertFormsCronTask($task, $options = array())
    {
		if ($task !== 'exportsubmissions')
		{
			return;
        }

        $this->log('Start: Export Form Submissions - Time Limit: ' . $options['time_limit'] . 's');

        $input = $this->app->input;
        
        if (!$formID = $input->getInt('form_id'))
        {
            $this->die('Form ID is missing');
        }

        $filenameParts = [
            'convertforms',
            'submissions', 
            $formID,
            HtmlHelper::date('now', 'Ymd'),   // Current date
            bin2hex(Crypt::genRandomBytes(2)) // Append a random 4-character hexadecimal string to increase uniqueness and security
        ];

        $export_filename = implode('_', $filenameParts) . '.' . $input->get('export_type', 'csv');
        $export_path = $input->get('export_path', null, 'RAW');

        $filter_timezone = trim($input->get('filter_timezone', '', 'string'));
        // Add "+" when adding a positive timezone as this gets removed
        if (!empty($filter_timezone) && strpos($filter_timezone, ':') !== false && $filter_timezone[0] !== '-')
        {
            $filter_timezone = '+' . $filter_timezone;
        }

        $options = [
            // Accepts an integer representing the ID of the form.
            'filter_form_id'       => $formID,

            // Accepts any search term.
            'filter_filter_search' => $input->get('filter_search'),

            // Accepts: today, yesterday, this_week, this_month, this_year, last_week, last_month, last_year, daterange
            'filter_period'        => $input->get('filter_period'),
            'filter_created_from'  => $input->get('filter_created_from'),
            'filter_created_to'    => $input->get('filter_created_to'),

            // Accepts: empty (site timezone) or utc
            'filter_timezone'      => $filter_timezone,

            // Accepts: csv, json
            'export_type'          => $input->get('export_type', 'csv'),

            // Accepts: true or false
            'export_append'        => $input->get('export_append', false),
            'export_path'          => $export_path,
            'filename'             => $export_filename
        ];

        try 
        {
            $this->log(print_r($options, true));
            $result = Export::export($options);

            if ($input->get('download'))
            {
                \NRFramework\File::download($export_filename, $export_path);
            }

            if ($input->get('email'))
            {
                $form = \ConvertForms\Form::load($formID);
                $formName = $form['name'];
                $emailTo = $input->getHtml('emailRecipients', $this->app->get('mailfrom'));
                $attachment = $result['filename'];

                $mailer = new NRFramework\Email([
                    'recipient'   => $emailTo,
                    'from_email'  => $this->app->get('mailfrom'),
                    'from_name'   => $this->app->get('fromname'),
                    'subject'     => Text::sprintf('Convert Forms - Form Submissions Report – %s', $formName),
                    'body'        => Text::sprintf('Hello,<br><br>Please find attached the form submissions report for the form <b>%s</b>.', $formName),
                    'attachments' => $attachment,
                    'reply_to_name' => '',
                ]);

                if (!$mailer->send())
                {
                    $this->die($mailer->error);
                }

                // Don't leave any clues on the server. Delete the file.
		        Joomla\Filesystem\File::delete($attachment);
            }

        } catch (\Throwable $th)
        {
            $this->die($th->getMessage());
        }

        $this->log('End: Export Form Submissions');
    }
    
    /**
     * Log debug message to lg file
     *
     * @param  strng $msg
     *
     * @return void
     */
    private function log($msg)
    {
        try
        {
            Log::add($msg, Log::DEBUG, 'convertforms.cron.exportsubmissions');
        } catch (\Throwable $th)
        {
        }
    }

    private function die($msg)
    {
        $this->log($msg, Log::ERROR);
        jexit($msg);
    }
}