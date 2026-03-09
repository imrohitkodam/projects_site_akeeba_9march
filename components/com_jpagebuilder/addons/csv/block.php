<?php
/**
 * @package JPageBuilder
 * @author Joomla!
 * @license GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\Path;

class JpagebuilderAddonCsv extends JpagebuilderAddons {
	
	public function render() {
		$settings = $this->addon->settings;
		
		$csv_file       = isset($settings->csv_file->src) ? trim($settings->csv_file->src) : ($settings->csv_file ? $settings->csv_file : '');
		$delimiter      = isset($settings->delimiter) && $settings->delimiter !== '' ? $settings->delimiter : ',';
		$enclosure      = isset($settings->csv_enclosure) && $settings->csv_enclosure !== '' ? $settings->csv_enclosure : '"';
		$table_class    = isset($settings->table_class) && $settings->table_class !== '' ? $settings->table_class : 'uk-table uk-table-striped';
		$strip_headers  = isset($settings->strip_headers) && $settings->strip_headers;
		$limit_rows     = isset($settings->limit_rows) && is_numeric($settings->limit_rows) ? (int)$settings->limit_rows : 0;
		
		$output = '<div class="jpb-addon-csvtable">';
		$full_path = Path::clean(JPATH_ROOT . '/' . ltrim($csv_file, '/'));
		
		if ($csv_file && file_exists($full_path) && is_readable($full_path)) {
			if (($handle = fopen($full_path, 'r')) !== false) {
				$rows = [];
				while (($row = fgetcsv($handle, 0, $delimiter, $enclosure)) !== false) {
					$rows[] = $row;
				}
				fclose($handle);
				
				if (!empty($rows)) {
					$output .= '<table class="' . htmlspecialchars($table_class) . '">';
					
					if (!$strip_headers) {
						$header = array_shift($rows);
						$output .= '<thead><tr>';
						foreach ($header as $head) {
							$output .= '<th>' . htmlspecialchars($head) . '</th>';
						}
						$output .= '</tr></thead>';
					} else {
						$header = array_shift($rows); // remove first row even if not used
					}
					
					// Limit rows if set
					if ($limit_rows > 0) {
						$rows = array_slice($rows, 0, $limit_rows);
					}
					
					$output .= '<tbody>';
					foreach ($rows as $row) {
						$output .= '<tr>';
						foreach ($row as $col) {
							$output .= '<td>' . htmlspecialchars($col) . '</td>';
						}
						$output .= '</tr>';
					}
					$output .= '</tbody></table>';
				} else {
					$output .= '<p>No data found in CSV file.</p>';
				}
			} else {
				$output .= '<p>Unable to open CSV file.</p>';
			}
		} else {
			$output .= '<p>CSV file not found or unreadable: <code>' . htmlspecialchars($csv_file) . '</code></p>';
		}
		
		$output .= '</div>';
		
		return $output;
	}
	
	public function scripts() {
		HTMLHelper::_('script', 'components/com_jpagebuilder/assets/js/uitheme.js', [], ['defer' => true]);
		HTMLHelper::_('script', 'components/com_jpagebuilder/assets/js/uitheme-icons.js', [], ['defer' => true]);
	}
	
	public function stylesheets() {
		return [
				'components/com_jpagebuilder/assets/css/uitheme.css'
		];
	}
	
	public function css() {
		return '';
	}
}