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

$addon = $displayData ['addon'];
$addonName = str_replace ( '_', '-', $addon->name );

$view_mode = Factory::getApplication ()->getInput()->get ( 'view' );
$custom_class = '';
$responsive_class = '';

$allowed_responsive_view_modes = [ 
		'page',
		'article',
		'item',
		'category',
		'featured',
		'products'
];

if (in_array ( $view_mode, $allowed_responsive_view_modes )) {
	$responsive_class .= (isset ( $addon->settings->hidden_xl ) && filter_var ( $addon->settings->hidden_xl, FILTER_VALIDATE_BOOLEAN )) ? ' jpb-hidden-xl ' : '';
	$responsive_class .= (isset ( $addon->settings->hidden_lg ) && filter_var ( $addon->settings->hidden_lg, FILTER_VALIDATE_BOOLEAN )) ? ' jpb-hidden-lg ' : '';
	$responsive_class .= (isset ( $addon->settings->hidden_md ) && filter_var ( $addon->settings->hidden_md, FILTER_VALIDATE_BOOLEAN )) ? ' jpb-hidden-md ' : '';
	$responsive_class .= (isset ( $addon->settings->hidden_sm ) && filter_var ( $addon->settings->hidden_sm, FILTER_VALIDATE_BOOLEAN )) ? ' jpb-hidden-sm ' : '';
	$responsive_class .= (isset ( $addon->settings->hidden_xs ) && filter_var ( $addon->settings->hidden_xs, FILTER_VALIDATE_BOOLEAN )) ? ' jpb-hidden-xs ' : '';
}

$global_section_z_index = (isset ( $addon->settings->global_section_z_index ) && $addon->settings->global_section_z_index) ? $addon->settings->global_section_z_index : '';
$global_addon_z_index = (isset ( $addon->settings->global_addon_z_index ) && $addon->settings->global_addon_z_index) ? $addon->settings->global_addon_z_index : '';
$global_custom_position = (isset ( $addon->settings->global_custom_position ) && $addon->settings->global_custom_position) ? $addon->settings->global_custom_position : '';
$global_seclect_position = (isset ( $addon->settings->global_seclect_position ) && $addon->settings->global_seclect_position) ? $addon->settings->global_seclect_position : '';
$rowId = (isset ( $addon->settings->row_id ) && $addon->settings->row_id) ? $addon->settings->row_id : '';
$colId = (isset ( $addon->settings->column_id ) && $addon->settings->column_id) ? $addon->settings->column_id : '';

// Image lazy loading
$config = ComponentHelper::getParams ( 'com_jpagebuilder' );
$lazyLoad = $config->get ( 'lazyloadimg', '0' );
$global_background_image = (isset ( $addon->settings->global_background_image ) && $addon->settings->global_background_image) ? $addon->settings->global_background_image : '';
$global_background_image_src = isset ( $global_background_image->src ) ? $global_background_image->src : $global_background_image;

if ($lazyLoad && $global_background_image_src) {
	if (isset ( $addon->settings->global_background_type ) && $addon->settings->global_background_type === 'image') {
		$custom_class .= 'jpb-element-lazy ';
	}
}

// Animation
$addon_attr = '';

if (isset ( $addon->settings->global_use_animation ) && $addon->settings->global_use_animation) {
	if (isset ( $addon->settings->global_animation ) && $addon->settings->global_animation) {
		$custom_class .= ' jpb-wow ' . $addon->settings->global_animation . ' ';

		if (isset ( $addon->settings->global_animationduration ) && $addon->settings->global_animationduration) {
			$addon_attr .= ' data-jpb-wow-duration="' . $addon->settings->global_animationduration . 'ms" ';
		}

		if (isset ( $addon->settings->global_animationdelay ) && $addon->settings->global_animationdelay) {
			$addon_attr .= 'data-jpb-wow-delay="' . $addon->settings->global_animationdelay . 'ms" ';
		}
	}
}

$html = '<div id="jpb-addon-wrapper-' . $addon->id . '" class="jpb-addon-wrapper ' . $responsive_class . ' addon-root-' . $addonName . '">';
$html .= '<div id="jpb-addon-' . $addon->id . '" class="clearfix ' . $custom_class . ' ' . ($global_custom_position && $global_seclect_position != '' ? ' jpb-positioned-addon ' : '') . '" ' . $addon_attr . ' ' . ($global_section_z_index ? 'data-zindex="' . $global_section_z_index . '"' : '') . ' ' . ($global_addon_z_index ? 'data-col-zindex="' . $global_addon_z_index . '"' : '') . ' ' . ($global_custom_position && $rowId ? 'data-rowid="' . $rowId . '"' : '') . ' ' . ($global_custom_position && $colId ? 'data-colid="' . $colId . '"' : '') . '>';

if (isset ( $addon->settings->global_use_overlay ) && $addon->settings->global_use_overlay) {
	$html .= '<div class="jpb-addon-overlayer"></div>';
}

echo $html;
