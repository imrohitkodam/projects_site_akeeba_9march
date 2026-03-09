<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct access
defined ('_JEXEC') or die ('Restricted access');

$options = $displayData['options'];

$fluid_row = (isset($options->fullscreen) && $options->fullscreen) ? $options->fullscreen : 0;

$html = '</div>';

if(!$fluid_row){
	$html .= '</div>';
	$html .= '</section>';
} else {
	$html .= '</div>';
	$html .= '</div>';
}

echo $html;
