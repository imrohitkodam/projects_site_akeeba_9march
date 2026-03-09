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

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

use \NRFramework\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class Signature extends \ConvertForms\Field
{
	/**
	 * Remove common fields from the form rendering
	 *
	 * @var  mixed
	 */
	protected $excludeFields = [
		'placeholder',
		'browserautocomplete',
		'inputcssclass',
		'value'
	];

	/**
	 * While we capture the base64 image data on front-end, we should not save this format in the database
	 * for storage capacity reasons.
	 * 
	 * Instead, we upload the image on /media/com_convertforms/signatures and save only
	 * the relative path of the signature file in the database.
	 * 
	 * @param   string  $value    The base64 image data
	 * 
	 * @return  void
	 */
	public function validate(&$value)
	{
		// Abort if the field is not required and the value is empty
		if (!$this->field->get('required') && empty($value))
		{
			return;
		}

		// A required field should not be empty.
		if ($this->field->get('required') && empty($value))
		{
			$this->throwError(Text::_('COM_CONVERTFORMS_FIELD_REQUIRED'));
		}

		$ds = DIRECTORY_SEPARATOR;
		
		// Get the field name
		$field_name = $this->field->get('name');

		// Decoded the signature
		$decoded_image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $value));

		// Path of the temporary signature
		$tmp_path = implode($ds, [File::getTempFolder(), 'signature_' . $field_name . '_' . uniqid() . '.png']);

		// Upload temporary signature to temp folder
		file_put_contents($tmp_path, $decoded_image_data);

		$this->app->registerEvent('onConvertFormsSubmissionBeforeSave', function(&$data) use ($value, $field_name, $tmp_path, $ds)
		{
			try
			{
				// Get the data
				$tmpData = $data->getArgument('0');

				// Destination folder. We are not using /media/ folder here as the 'media' Joomla field supports only relative paths to the top level /images/ folder only :(
				$destination_folder = implode($ds, ['images', 'convertforms', 'signatures']);
				
				// Move the signature to the destination folder
				$destination = implode($ds, [JPATH_SITE, $destination_folder, $field_name . '_' . uniqid() . '.png']);

				$destination = File::move($tmp_path, $destination);

				// Allow to modify the signature file name via event
				\Joomla\CMS\Factory::getApplication()->triggerEvent('onConvertFormsFieldSignatureStore', [&$destination, $tmpData]);
				
				// Set the new Signature value
				$tmpData['params'][$field_name] = implode($ds, [$destination_folder, basename($destination)]);

				// Set back the new value to $data object
				$data->setArgument(0, $tmpData);
			} catch (\Throwable $th)
			{
				$this->throwError($th->getMessage());
			}
		});
	}

	/**
	 * Prepare value to be displayed to the user as HTML/text
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function prepareValueHTML($value)
	{
		if (!$value)
		{
			return;
		}

		return '<img src="' . Uri::root() . $value . '" alt="Signature" />';
	}
}