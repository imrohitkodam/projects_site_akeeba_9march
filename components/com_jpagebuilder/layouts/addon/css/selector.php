<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$addon_id = $displayData ['addon_id'];
$selector = (isset ( $displayData ['selector'] ) && $displayData ['selector']) ? $displayData ['selector'] : '';
$options = $displayData ['options'];

$output = '';

if (! empty ( $options )) {
	foreach ( $options as $option ) {
		if (is_array ( $option )) {
			// Font
			if (isset ( $option ['type'] ) && $option ['type']) {
				$type = $option ['type'];

				if ($type === 'font') {
					$font = $option ['font'];

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
						$google_font = '//fonts.googleapis.com/css?family=' . str_replace ( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic&display=swap';
						$disableGoogleFonts = ComponentHelper::getParams ( "com_jpagebuilder" )->get ( 'google_fonts', 0 );
						if ($disableGoogleFonts != 1) {
							$wa = Factory::getApplication ()->getDocument ()->getWebAssetManager();
							$wa->registerAndUseStyle('jpagebuilder.font-' . str_replace ( ' ', '-', $font ), $google_font);
						}
					}

					$output .= $selector ? $addon_id . ' ' . $selector . $option ['css'] : $addon_id . ' ' . $option ['css'];

					$output .= "\n";
				}
			}
		}
	}
}

echo $output;
