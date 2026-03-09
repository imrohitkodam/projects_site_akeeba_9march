<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Uri\Uri;
class JpagebuilderAddonMap extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$heading_addon_margin = (isset ( $settings->heading_addon_margin ) && $settings->heading_addon_margin) ? $settings->heading_addon_margin : '';
		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= ($heading_addon_margin) ? ' uk-margin' . (($heading_addon_margin == 'default') ? '' : '-' . $heading_addon_margin) : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';

		$block_align = (isset ( $settings->block_align ) && $settings->block_align) ? $settings->block_align : '';
		$block_align_breakpoint = (isset ( $settings->block_align_breakpoint ) && $settings->block_align_breakpoint) ? '@' . $settings->block_align_breakpoint : '';
		$block_align_fallback = (isset ( $settings->block_align_fallback ) && $settings->block_align_fallback) ? $settings->block_align_fallback : '';

		// Block Alignment CLS.
		$block_cls [] = '';

		if (empty ( $block_align )) {
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = ' uk-margin-auto-right' . $block_align_breakpoint;
				$block_cls [] = 'uk-margin-remove-left' . $block_align_breakpoint . ($block_align_fallback == 'center' ? ' uk-margin-auto' : ' uk-margin-auto-left');
			}
		}

		if ($block_align == 'center') {
			$block_cls [] = ' uk-margin-auto' . $block_align_breakpoint;
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = 'uk-margin-auto' . ($block_align_fallback == 'right' ? '-left' : '');
			}
		}

		if ($block_align == 'right') {
			$block_cls [] = ' uk-margin-auto-left' . $block_align_breakpoint;
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = $block_align_fallback == 'center' ? 'uk-margin-remove-right' . $block_align_breakpoint . ' uk-margin-auto' : 'uk-margin-auto-left';
			}
		}

		$block_cls = implode ( ' ', array_filter ( $block_cls ) );

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' uk-text-' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';

		$max_width_cfg .= $addon_max_width_breakpoint . ($max_width_cfg ? $block_cls : '');

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' ' . $settings->box_shadow : '';
		$general .= (isset ( $settings->hover_box_shadow ) && $settings->hover_box_shadow) ? ' ' . $settings->hover_box_shadow : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';
		$general .= $text_alignment . $text_breakpoint . $text_alignment_fallback . $max_width_cfg;

		$zoom = (isset ( $settings->map_zoom ) && $settings->map_zoom) ? $settings->map_zoom : '12';
		$leaflet_providers = (isset ( $settings->leaflet_providers ) && $settings->leaflet_providers) ? $settings->leaflet_providers : 'CartoDB.Voyager';

		$popup = (isset ( $settings->popup ) && $settings->popup) ? $settings->popup : '';

		// Parallax Animation.
		$horizontal_start = (isset ( $settings->horizontal_start ) && $settings->horizontal_start) ? $settings->horizontal_start : '0';
		$horizontal_end = (isset ( $settings->horizontal_end ) && $settings->horizontal_end) ? $settings->horizontal_end : '0';
		$horizontal = (! empty ( $horizontal_start ) || ! empty ( $horizontal_end )) ? 'x: ' . $horizontal_start . ',' . $horizontal_end . ';' : '';

		$vertical_start = (isset ( $settings->vertical_start ) && $settings->vertical_start) ? $settings->vertical_start : '0';
		$vertical_end = (isset ( $settings->vertical_end ) && $settings->vertical_end) ? $settings->vertical_end : '0';
		$vertical = (! empty ( $vertical_start ) || ! empty ( $vertical_end )) ? 'y: ' . $vertical_start . ',' . $vertical_end . ';' : '';

		$scale_start = (isset ( $settings->scale_start ) && $settings->scale_start) ? (( int ) $settings->scale_start / 100) : 1;
		$scale_end = (isset ( $settings->scale_end ) && $settings->scale_end) ? (( int ) $settings->scale_end / 100) : 1;
		$scale = (! empty ( $scale_start ) || ! empty ( $scale_end )) ? 'scale: ' . $scale_start . ',' . $scale_end . ';' : '';

		$rotate_start = (isset ( $settings->rotate_start ) && $settings->rotate_start) ? $settings->rotate_start : '0';
		$rotate_end = (isset ( $settings->rotate_end ) && $settings->rotate_end) ? $settings->rotate_end : '0';
		$rotate = (! empty ( $rotate_start ) || ! empty ( $rotate_end )) ? 'rotate: ' . $rotate_start . ',' . $rotate_end . ';' : '';

		$opacity_start = (isset ( $settings->opacity_start ) && $settings->opacity_start) ? (( int ) $settings->opacity_start / 100) : 1;
		$opacity_end = (isset ( $settings->opacity_end ) && $settings->opacity_end) ? (( int ) $settings->opacity_end / 100) : 1;
		$opacity = (! empty ( $opacity_start ) || ! empty ( $opacity_end )) ? 'opacity: ' . $opacity_start . ',' . $opacity_end . ';' : '';

		$easing = (isset ( $settings->easing ) && $settings->easing) ? (( int ) $settings->easing / 100) : '';
		$easing_cls = (! empty ( $easing )) ? 'easing:' . $easing . ';' : '';

		$breakpoint = (isset ( $settings->breakpoint ) && $settings->breakpoint) ? $settings->breakpoint : '';
		$breakpoint_cls = (! empty ( $breakpoint )) ? 'media: @' . $breakpoint . ';' : '';

		$viewport = (isset ( $settings->viewport ) && $settings->viewport) ? (( int ) $settings->viewport / 100) : '';
		$viewport_cls = (! empty ( $viewport )) ? 'viewport:' . $viewport . ';' : '';

		$parallax_target = (isset ( $settings->parallax_target ) && $settings->parallax_target) ? $settings->parallax_target : false;
		$target_cls = ($parallax_target) ? ' target: !.jpb-section;' : '';

		// Default Animation.

		$animation = (isset ( $settings->animation ) && $settings->animation) ? $settings->animation : '';
		$parallax_zindex = (isset ( $settings->parallax_zindex ) && $settings->parallax_zindex) ? $settings->parallax_zindex : false;
		$zindex_cls = ($parallax_zindex && $animation == 'parallax') ? ' uk-position-z-index uk-position-relative' : '';

		$animation_repeat = ($animation) ? ((isset ( $settings->animation_repeat ) && $settings->animation_repeat) ? ' repeat: true;' : '') : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . $target_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}

		$output = '';

		$output .= '<div class="ui-map' . $zindex_cls . $general . '"' . $animation . '>';
		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div id="mapid-' . $this->addon->id . '"></div>';

		$output .= '<script>';
		if (isset ( $settings->ui_map_item ) && count ( ( array ) $settings->ui_map_item )) {
			foreach ( $settings->ui_map_item as $key => $item ) {
				$latlong = (isset ( $item->latlong ) && $item->latlong) ? $item->latlong : '';
			}
			$output .= 'var mymap = L.map(\'mapid-' . $this->addon->id . '\',{scrollWheelZoom:false}).setView([' . $latlong . '], ' . $zoom . ');';
		}

		$base_path = Uri::base ( true ) . '/components/com_jpagebuilder/addons/map/assets';
		$output .= 'var LeafIcon = L.Icon.extend({
		options: {
			shadowUrl: \'' . $base_path . '/marker-shadow.png\',
			iconAnchor: [12, 41],
			popupAnchor: [1, -41],
			shadowSize: [41, 41]
				}
		});';

		if (isset ( $settings->ui_map_item ) && count ( ( array ) $settings->ui_map_item )) {
			foreach ( $settings->ui_map_item as $key => $item ) {

				$pop_content = (isset ( $item->pop_content ) && $item->pop_content) ? $item->pop_content : '';
				$marker_icon = (isset ( $item->marker ) && $item->marker) ? $item->marker : '';
				$marker_icon_src = isset ( $marker_icon->src ) ? $marker_icon->src : $marker_icon;

				if (strpos ( $marker_icon_src, 'http://' ) !== false || strpos ( $marker_icon_src, 'https://' ) !== false) {
					$marker_icon_src = $marker_icon_src;
				} elseif ($marker_icon_src) {
					$marker_icon_src = Uri::base ( true ) . '/' . $marker_icon_src;
				}

				if ($marker_icon_src) {
					$output .= 'var mkIcon = new LeafIcon({iconUrl: \'' . $marker_icon_src . '\'});';
				}

				if ((isset ( $item->latlong ) && $item->latlong)) {
					$output .= 'var marker = L.marker([' . $item->latlong . ']' . ($marker_icon_src ? ', {icon: mkIcon}' : '') . ').addTo(mymap)' . ($pop_content ? '.bindPopup("' . str_replace ( '"', '', $item->pop_content ) . '")' : '') . ';';
					if (! empty ( $popup )) {
						$output .= 'marker.on(\'mouseover\', function (e) {';
						$output .= 'this.openPopup();';
						$output .= '});';
						$output .= 'marker.on(\'mouseout\', function (e) {';
						$output .= 'this.closePopup();';
						$output .= '});';
					}
				}
			}
		}

		$output .= 'L.tileLayer.provider(\'' . $leaflet_providers . '\').addTo(mymap);';
		$output .= '</script>';
		
		$output .= '<style>div.jpagebuilder-addons #mapid-' . $this->addon->id . '{background-image:url(' . Uri::base ( false ) . 'components/com_jpagebuilder/addons/map/assets/placeholder.jpg);background-repeat: no-repeat;background-size: cover}</style>';

		$output .= '</div>';

		return $output;
	}
	public function scripts() {
		return array (
				'components/com_jpagebuilder/assets/js/uitheme.js',
				'components/com_jpagebuilder/assets/js/uitheme-icons.js',
				'components/com_jpagebuilder/addons/map/assets/leaflet.js',
				'components/com_jpagebuilder/addons/map/assets/leaflet-providers.js'
		);
	}
	public function stylesheets() {
		$style_sheet = [ 
				'components/com_jpagebuilder/assets/css/uitheme.css',
				'components/com_jpagebuilder/addons/map/assets/leaflet.css'
		];

		return $style_sheet;
	}
	public function css() {
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$height = (isset ( $settings->map_height ) && $settings->map_height) ? 'height: ' . $settings->map_height . 'px;' : '';
		$css = '';
		$css .= $addon_id . ' #mapid-' . $this->addon->id . ' {';
		$css .= $height;
		$css .= "\n" . '}' . "\n";
		$css .= '.leaflet-popup-content-wrapper {border-radius: 2px;}';
		return $css;
	}
}
