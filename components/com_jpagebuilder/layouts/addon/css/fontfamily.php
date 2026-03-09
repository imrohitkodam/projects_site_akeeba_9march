<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

$font = $displayData ['font'];

$system = array (
		'Arial',
		'Tahoma',
		'Verdana',
		'Helvetica',
		'Times New Roman',
		'Trebuchet MS',
		'Georgia'
);

if (! in_array ( $font, $system )) {
	$google_font = 'https://fonts.googleapis.com/css?family=' . str_replace ( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic&display=swap';
	$disableGoogleFonts = ComponentHelper::getParams ( "com_jpagebuilder" )->get ( 'google_fonts', 0 );
	if ($disableGoogleFonts != 1) {
		$wa = Factory::getApplication ()->getDocument ()->getWebAssetManager();
		$wa->registerAndUseStyle('jpagebuilder.font-' . str_replace ( ' ', '-', $font ), $google_font);
	}
}
