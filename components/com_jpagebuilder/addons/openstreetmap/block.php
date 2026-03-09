<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Uri\Uri;
class JpagebuilderAddonOpenstreetmap extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$class = (isset ( $settings->class ) && $settings->class) ? $settings->class : '';

		// Options
		$map_style = (isset ( $settings->map_style ) && $settings->map_style) ? $settings->map_style : 'Wikimedia';
		$zoom = (isset ( $settings->zoom ) && $settings->zoom) ? $settings->zoom : 0;
		$mousescroll = (isset ( $settings->mousescroll ) && $settings->mousescroll) ? $settings->mousescroll : 0;
		$dragging = (isset ( $settings->dragging ) && $settings->dragging) ? $settings->dragging : 0;
		$zoomcontrol = (isset ( $settings->zoomcontrol ) && $settings->zoomcontrol) ? $settings->zoomcontrol : 0;
		$attribution = (isset ( $settings->attribution ) && $settings->attribution) ? $settings->attribution : 0;

		$total_location = [ ];
		if (( array ) $settings->multi_location_items) {
			foreach ( $settings->multi_location_items as $key => $item ) {
				$lat_long = (isset ( $item->location_item ) && $item->location_item) ? explode ( ',', $item->location_item ) : array (
						40.7970,
						- 73.9491
				);
				$address_text = (isset ( $item->location_popup_text ) && $item->location_popup_text) ? str_replace ( "'", "&#39;", $item->location_popup_text ) : '';
				$custom_icon = (isset ( $item->custom_icon ) && $item->custom_icon) ? $item->custom_icon : '';
				$custom_icon_src = isset ( $custom_icon->src ) ? $custom_icon->src : $custom_icon;
				if ($custom_icon_src) {
					if (strpos ( $custom_icon_src, "http://" ) !== false || strpos ( $custom_icon_src, "https://" ) !== false) {
						$custom_icon = $custom_icon_src;
					} else {
						$custom_icon = Uri::base ( true ) . '/' . $custom_icon_src;
					}
				}
				$total_location [] = array (
						'address' => $address_text,
						'latitude' => $lat_long [0],
						'longitude' => $lat_long [1],
						'custom_icon' => $custom_icon
				);
			}
		}
		$location_json = json_encode ( $total_location );

		$output = '';
		if ($location_json) {
			$output .= '<div class="jpb-addon-openstreetmap-wrapper">';
			$output .= '<div class="jpb-addon-content">';
			$output .= '<div id="jpb-addon-osm-' . $this->addon->id . '" class="jpb-addon-openstreetmap ' . $class . '" data-location=\'' . $location_json . '\' data-mapstyle="' . $map_style . '" data-mapzoom="' . $zoom . '" data-mousescroll="' . $mousescroll . '" data-dragging="' . $dragging . '" data-zoomcontrol="' . $zoomcontrol . '" data-attribution="' . $attribution . '"></div>';
			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}
	public function scripts() {
		return array (
				'components/com_jpagebuilder/assets/js/leaflet.js',
				'components/com_jpagebuilder/assets/js/leaflet.provider.js'
		);
	}
	public function stylesheets() {
		return array (
				'components/com_jpagebuilder/assets/css/leaflet.css'
		);
	}
	public function css() {
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;
		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );
		$css = '';
		$height = $cssHelper->generateStyle ( '.jpb-addon-openstreetmap', $settings, [ 
				'height' => 'height'
		] );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-openstreetmap-wrapper', $settings, 'transform' );

		$css .= $transformCss;
		$css .= $height;

		return $css;
	}
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( '#jpb-addon-{{ data.id }}' );
		$output = '
		<#

			let location_addr = [];
			if(_.isObject(data.multi_location_items) && data.multi_location_items){
				_.each(data.multi_location_items, function(item){
					let latLong = _.split(item.location_item, ",");

					var custom_icon = {}
					if (typeof item.custom_icon !== "undefined" && typeof item.custom_icon.src !== "undefined") {
						custom_icon = item.custom_icon
					} else {
						custom_icon = {src: item.custom_icon}
					}
					if(custom_icon.src){
						if(custom_icon.src.indexOf("http://") == 0 || custom_icon.src.indexOf("https://") == 0){
							custom_icon = custom_icon.src;
						} else {
							custom_icon = pagebuilder_base + "/" + custom_icon.src;
						}
					}
					let mainObj = [{
						address: item.location_popup_text,
						latitude: latLong[0] ||40.7970,
						longitude: latLong[1] || -73.9491,
						custom_icon: custom_icon
					}];
					location_addr = _.concat(location_addr, mainObj);
				})
			}

			let location_json = JSON.stringify(location_addr);
			
		#>
		<style type="text/css">';

		$output .= $lodash->unit ( 'height', '.jpb-addon-openstreetmap', 'data.height', 'px' );
		$output .= $lodash->generateTransformCss ( '.jpb-addon-openstreetmap-wrapper', 'data.transform' );
		$output .= '
		</style>
		<div class="jpb-addon-openstreetmap-wrapper edit-view">
			<div class="jpb-addon-content">
				<div id="jpb-addon-osm-{{ data.id }}" class="jpb-addon-openstreetmap {{data.class}}" data-location=\'{{location_json}}\' data-mapstyle="{{data.map_style}}" data-mapzoom="{{data.zoom}}" data-mousescroll="{{data.mousescroll}}" data-dragging="{{data.dragging}}" data-zoomcontrol="{{data.zoomcontrol}}" data-attribution="{{data.attribution}}"></div>
			</div>
		</div>
		';

		return $output;
	}
}