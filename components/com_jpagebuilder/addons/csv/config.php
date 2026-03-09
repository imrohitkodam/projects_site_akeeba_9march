<?php
/**
 * @package JPageBuilder
 * @author Joomla!
 * @license GNU/GPLv2 or later
 */
use Joomla\CMS\Language\Text;

defined ( '_JEXEC' ) or die ( 'Restricted access' );

JpagebuilderConfig::addonConfig ( [ 
		'type' => 'content',
		'addon_name' => 'csv',
		'title' => Text::_ ( 'CSV Table' ),
		'desc' => Text::_ ( 'Displays a table of data loaded from a CSV file' ),
		'icon'       => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.27 4.27A4.333 4.333 0 014.332 3h23.334A4.333 4.333 0 0132 7.333v17.334A4.333 4.333 0 0127.667 29H4.333A4.333 4.333 0 010 24.667V7.333C0 6.184.457 5.082 1.27 4.27zM2 13.666v5.666h13v-5.666H2zm15 0v5.666h13v-5.666H17zm13-2V7.333A2.333 2.333 0 0027.667 5H4.333A2.333 2.333 0 002 7.333v4.334h28zm0 9.666H17V27L27.667 27A2.333 2.333 0 0030 24.667v-3.334zM15 27v-5.666H2v3.334A2.333 2.333 0 004.333 27H15z" fill="currentColor"/></svg>',
		'category' => 'Media',
		'settings' => [ 
				'general' => [ 
						'title' => Text::_ ( 'CSV Table Settings' ),
						'fields' => [ 
								'csv_file' => array (
										'type' => 'media',
										'format' => 'attachment',
										'title' => Text::_ ( 'CSV File Path' ),
										'desc' => Text::_ ( 'Relative path to the CSV file from Joomla root (e.g. media/jpagebuilder/csv/data.csv)' ),
										'placeholder' => 'media/jpagebuilder/csv/data.csv',
										'std' => '',
										'hide_preview' => true,
										'hide_alt_text' => true
								),
								'delimiter' => [ 
										'type' => 'text',
										'title' => Text::_ ( 'Delimiter' ),
										'desc' => Text::_ ( 'Character used to separate fields in the CSV (default is comma)' ),
										'std' => ',',
										'inline' => true
								],
								'csv_enclosure' => [
										'type' => 'text',
										'title' => Text::_('Enclosure'),
										'desc' => Text::_('Character used to enclose fields in the CSV (default is double quote ")'),
										'std' => '"',
										'inline' => true
								],
								'table_class' => [
										'type' => 'text',
										'title' => Text::_('Table CSS Class'),
										'desc' => Text::_('Additional CSS classes for the <table> element (e.g. uk-table-striped uk-table-hover)'),
										'std' => 'uk-table uk-table-striped uk-table-hover'
								],
								'limit_rows' => [
										'type' => 'number',
										'title' => Text::_('Limit Rows'),
										'desc' => Text::_('Maximum number of rows to display (0 or empty = no limit)'),
										'std' => ''
								],
								'strip_headers' => [ 
										'type' => 'checkbox',
										'title' => Text::_ ( 'Remove header row' ),
										'desc' => Text::_ ( 'If enabled, the first row of the CSV will not be shown as table headers' ),
										'values' => [ 
												1 => Text::_ ( 'JYES' ),
												0 => Text::_ ( 'JNO' )
										],
										'std' => 0
								]
						]
				]
		]
] );
