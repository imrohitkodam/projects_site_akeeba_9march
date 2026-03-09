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
class JpagebuilderAddonParticles extends JpagebuilderAddons {
	public function render() {
	}
	public function css() {
		$css = '';
		$css .= ' canvas.particles-js-canvas-el {';
		$css .= 'position: absolute;';
		$css .= 'top: 0;';
		$css .= 'bottom: 0;';
		$css .= '}';
		return $css;
	}
	public function js() {
		$settings = $this->addon->settings;
		$particles = (isset ( $settings->ui_particle_list_items ) && $settings->ui_particle_list_items) ? $settings->ui_particle_list_items : array ();
		$js = '';
		if (count ( ( array ) $particles )) {
			foreach ( $particles as $key => $id ) {
				$type = (isset ( $id->type ) && $id->type) ? $id->type : '';
				$title = (isset ( $id->title ) && $id->title) ? strtolower ( str_replace ( ' ', '-', $id->title ) ) : '';
				$value = (isset ( $id->value ) && $id->value) ? $id->value : '80';
				$color = (isset ( $id->color ) && $id->color) ? $id->color : '#ffffff';
				$shape = (isset ( $id->shape ) && $id->shape) ? $id->shape : 'circle';
				$size = (isset ( $id->size ) && $id->size) ? $id->size : '3';
				$line_linked = (isset ( $id->line_linked ) && $id->line_linked) ? $id->line_linked : '#ffffff';
				$speed = (isset ( $id->speed ) && $id->speed) ? $id->speed : '6';
				$outmode = (isset ( $id->outmode ) && $id->outmode) ? $id->outmode : 'out';
				$direction = (isset ( $id->direction ) && $id->direction) ? $id->direction : '';
				$direction_cls = '';
				if (empty ( $direction )) {
					$direction_cls = 'none';
				} else {
					$direction_cls = $direction;
				}
				$id_text = '';

				$id_text = $title;

				if (empty ( $type )) {
					$js .= '
jQuery(function($){
particlesJS("' . $id_text . '",{"particles":{"number":{"value":' . $value . ',"density":{"enable":true,"value_area":800}},"color":{"value":"' . $color . '"},"shape":{"type":"' . $shape . '","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":' . $size . ',"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":true,"distance":150,"color":"' . $line_linked . '","opacity":0.4,"width":1},"move":{"enable":true,"speed":' . $speed . ',"direction":"' . $direction_cls . '","random":false,"straight":false,"out_mode":"' . $outmode . '","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
});';
				}
				if ($type == 'nasa') {
					$js .= '
jQuery(function($){
particlesJS("' . $id_text . '",{"particles":{"number":{"value":' . $value . ',"density":{"enable":true,"value_area":800}},"color":{"value":"' . $color . '"},"shape":{"type":"' . $shape . '","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":1,"random":true,"anim":{"enable":true,"speed":1,"opacity_min":0,"sync":false}},"size":{"value":' . $size . ',"random":true,"anim":{"enable":false,"speed":4,"size_min":0.3,"sync":false}},"line_linked":{"enable":false,"distance":150,"color":"' . $line_linked . '","opacity":0.4,"width":1},"move":{"enable":true,"speed":' . $speed . ',"direction":"' . $direction_cls . '","random":true,"straight":false,"out_mode":"' . $outmode . '","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":600}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":250,"size":0,"duration":2,"opacity":0,"speed":3},"repulse":{"distance":400,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
});';
				}
				if ($type == 'bubble') {
					$js .= '
jQuery(function($){
particlesJS("' . $id_text . '",{"particles":{"number":{"value":' . $value . ',"density":{"enable":true,"value_area":800}},"color":{"value":"' . $color . '"},"shape":{"type":"' . $shape . '","stroke":{"width":0,"color":"#000"},"polygon":{"nb_sides":6},"image":{"src":"img/github.svg","width":100 ,"height":100}},"opacity":{"value":0.3,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":' . $size . ',"random":false,"anim":{"enable":true,"speed":10,"size_min":40,"sync":false}},"line_linked":{"enable":false,"distance":200,"color":"' . $line_linked . '","opacity":1,"width":2},"move":{"enable":true,"speed":' . $speed . ',"direction":"' . $direction_cls . '","random":false,"straight":false,"out_mode":"' . $outmode . '","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":false,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
});';
				}
				if ($type == 'snow') {
					$js .= '
jQuery(function($){
particlesJS("' . $id_text . '",{"particles":{"number":{"value":' . $value . ',"density":{"enable":true,"value_area":800}},"color":{"value":"' . $color . '"},"shape":{"type":"' . $shape . '","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":' . $size . ',"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":500,"color":"' . $line_linked . '","opacity":0.4,"width":2},"move":{"enable":true,"speed":' . $speed . ',"direction":"' . $direction_cls . '","random":false,"straight":false,"out_mode":"' . $outmode . '","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":0.5}},"bubble":{"distance":400,"size":4,"duration":0.3,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
});';
				}
				if ($type == 'nyancat2') {
					$js .= '
jQuery(function($){
particlesJS("' . $id_text . '",{"particles":{"number":{"value":' . $value . ',"density":{"enable":false,"value_area":800}},"color":{"value":"' . $color . '"},"shape":{"type":"' . $shape . '","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"http://wiki.lexisnexis.com/academic/images/f/fb/Itunes_podcast_icon_300.jpg","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":' . $size . ',"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":150,"color":"' . $line_linked . '","opacity":0.4,"width":1},"move":{"enable":true,"speed":' . $speed . ',"direction":"' . $direction_cls . '","random":false,"straight":true,"out_mode":"' . $outmode . '","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":200,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});
});';
				}
			}
		}
		$js .= "\n";
		return $js;
	}
	public function scripts() {
		return array (
				'components/com_jpagebuilder/addons/particles/assets/particles.min.js'
		);
	}
}
