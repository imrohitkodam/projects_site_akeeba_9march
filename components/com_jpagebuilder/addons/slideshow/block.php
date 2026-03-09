<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'restricted access' );

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
class JpagebuilderAddonSlideShow extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;

		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$box_shadow = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$attrs_slideshow [] = '';
		$attrs_slideshow [] = (isset ( $settings->ratio ) && $settings->ratio) ? 'ratio: ' . $settings->ratio : '';
		$attrs_slideshow [] = (isset ( $settings->min_height ) && $settings->min_height) ? 'minHeight: ' . $settings->min_height : 'minHeight: 300';
		$attrs_slideshow [] = (isset ( $settings->max_height ) && $settings->max_height) ? 'maxHeight: ' . $settings->max_height : '';
		$attrs_slideshow [] = (isset ( $settings->autoplay ) && $settings->autoplay) ? 'autoplay: 1' : '';
		$attrs_slideshow [] = (isset ( $settings->autoplay ) && $settings->autoplay) && ! (isset ( $settings->pause ) && $settings->pause) ? 'pauseOnHover: false' : '';
		$attrs_slideshow [] = (isset ( $settings->autoplay ) && $settings->autoplay) && (isset ( $settings->autoplay_interval ) && $settings->autoplay_interval) ? 'autoplayInterval: ' . (( int ) $settings->autoplay_interval * 1000) : '';
		$attrs_slideshow [] = (isset ( $settings->slideshow_transition ) && $settings->slideshow_transition) ? 'animation: ' . $settings->slideshow_transition : '';
		$attrs_slideshow [] = (isset ( $settings->velocity ) && $settings->velocity) ? 'velocity: ' . ( int ) $settings->velocity / 100 : '';
		$attrs_slideshow = ' uk-slideshow="' . implode ( '; ', array_filter ( $attrs_slideshow ) ) . '"';

		$kenburns_transition = (isset ( $settings->kenburns_transition ) && $settings->kenburns_transition) ? ' uk-transform-origin-' . $settings->kenburns_transition : '';

		$kenburns_duration = (isset ( $settings->kenburns_duration ) && $settings->kenburns_duration) ? $settings->kenburns_duration : '';
		if ($kenburns_duration) {
			$kenburns_duration = ' style="-webkit-animation-duration: ' . $kenburns_duration . 's; animation-duration: ' . $kenburns_duration . 's;"';
		}

		$min_height = (isset ( $settings->min_height ) && $settings->min_height) ? 'minHeight: ' . $settings->min_height . ';' : 'minHeight: 300;';
		$height = (isset ( $settings->height ) && $settings->height) ? $settings->height : '';
		$height_cls = '';
		if ($height == 'full') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . '"';
		} elseif ($height == 'percent') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . 'offset-bottom: 20"';
		} elseif ($height == 'section') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . 'offset-bottom: !.jpb-section +"';
		}

		// Navigation settings.
		$navigation_control = (isset ( $settings->navigation ) && $settings->navigation) ? $settings->navigation : '';
		$navigation_breakpoint = (isset ( $settings->navigation_breakpoint ) && $settings->navigation_breakpoint) ? $settings->navigation_breakpoint : '';
		$navigation_breakpoint_cls = '';
		$navigation_breakpoint_cls .= ($navigation_breakpoint) ? ' uk-visible@' . $navigation_breakpoint . '' : '';

		$navigation_margin = (isset ( $settings->navigation_margin ) && $settings->navigation_margin) ? ' uk-position-' . $settings->navigation_margin : '';

		$navigation = (isset ( $settings->navigation_position ) && $settings->navigation_position) ? ' uk-position-' . $settings->navigation_position : '';

		$navigation_cls = ($navigation == ' uk-position-bottom-center') ? ' uk-flex-center' : '';
		$navigation_cls .= ($navigation == ' uk-position-bottom-right' || $navigation == ' uk-position-center-right' || $navigation == ' uk-position-top-right') ? ' uk-flex-right' : '';

		$navigation_below = (isset ( $settings->navigation_below ) && $settings->navigation_below) ? 1 : 0;

		$navigation_below_cls = ($navigation_below) ? ((isset ( $settings->navigation_below_position ) && $settings->navigation_below_position) ? ' uk-flex-' . $settings->navigation_below_position : '') : false;
		$navigation_below_margin_cls = ($navigation_below) ? ((isset ( $settings->navigation_below_margin ) && $settings->navigation_below_margin) ? ' uk-margin-' . $settings->navigation_below_margin : '') : false;
		$navigation_below_color_cls = ($navigation_below) ? ((isset ( $settings->navigation_color ) && $settings->navigation_color) ? ' uk-' . $settings->navigation_color : '') : false;

		$navigation_vertical = (! $navigation_below) ? ((isset ( $settings->navigation_vertical ) && $settings->navigation_vertical) ? ' uk-dotnav-vertical' : '') : '';
		$navigation_vertical_thumb = (! $navigation_below) ? ((isset ( $settings->navigation_vertical ) && $settings->navigation_vertical) ? ' uk-thumbnav-vertical' : '') : '';

		$thumbnav_wrap = (isset ( $settings->thumbnav_wrap ) && $settings->thumbnav_wrap) ? 1 : 0;
		$thumbnav_wrap_cls = ($thumbnav_wrap) ? ((isset ( $settings->thumbnav_wrap ) && $settings->thumbnav_wrap) ? ' uk-flex-nowrap' : '') : false;

		// Sidenav Settings.
		$slidenav_position = (isset ( $settings->slidenav_position ) && $settings->slidenav_position) ? $settings->slidenav_position : '';
		$slidenav_position_cls = (! empty ( $slidenav_position ) || ($slidenav_position != 'default')) ? ' uk-position-' . $slidenav_position . '' : '';

		$slidenav_margin = (isset ( $settings->slidenav_margin ) && $settings->slidenav_margin) ? ' uk-position-' . $settings->slidenav_margin . '' : '';

		$slidenav_on_hover = (isset ( $settings->slidenav_on_hover ) && $settings->slidenav_on_hover) ? 1 : 0;
		$slidenav_breakpoint = (isset ( $settings->slidenav_breakpoint ) && $settings->slidenav_breakpoint) ? $settings->slidenav_breakpoint : '';
		$slidenav_breakpoint_cls = ($slidenav_breakpoint) ? ' uk-visible@' . $slidenav_breakpoint . '' : '';

		$slidenav_outside_breakpoint = (isset ( $settings->slidenav_outside_breakpoint ) && $settings->slidenav_outside_breakpoint) ? ' @' . $settings->slidenav_outside_breakpoint : 'xl';

		$slidenav_outside_color = (isset ( $settings->slidenav_outside_color ) && $settings->slidenav_outside_color) ? ' uk-' . $settings->slidenav_outside_color : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$larger_style = (isset ( $settings->larger_style ) && $settings->larger_style) ? $settings->larger_style : '';
		$larger_style_init = ($larger_style) ? ' uk-slidenav-large' : '';

		$overlay_positions = (isset ( $settings->overlay_positions ) && $settings->overlay_positions) ? $settings->overlay_positions : '';
		$overlay_pos_int = ($overlay_positions == 'top' || $overlay_positions == 'bottom') ? ' uk-flex-1' : '';
		if (($overlay_positions == 'top') || ($overlay_positions == 'left') || ($overlay_positions == 'bottom') || ($overlay_positions == 'right')) {
			$overlay_positions = ' uk-flex-' . $overlay_positions;
		} elseif ($overlay_positions == 'top-left') {
			$overlay_positions = ' uk-flex-top uk-flex-left';
		} elseif ($overlay_positions == 'top-right') {
			$overlay_positions = ' uk-flex-top uk-flex-right';
		} elseif ($overlay_positions == 'top-center') {
			$overlay_positions = ' uk-flex-top uk-flex-center';
		} elseif ($overlay_positions == 'center-left') {
			$overlay_positions = ' uk-flex-left uk-flex-middle';
		} elseif ($overlay_positions == 'center-right') {
			$overlay_positions = ' uk-flex-right uk-flex-middle';
		} elseif ($overlay_positions == 'center') {
			$overlay_positions = ' uk-flex-center uk-flex-middle';
		} elseif ($overlay_positions == 'bottom-left') {
			$overlay_positions = ' uk-flex-bottom uk-flex-left';
		} elseif ($overlay_positions == 'bottom-center') {
			$overlay_positions = ' uk-flex-bottom uk-flex-center';
		} elseif ($overlay_positions == 'bottom-right') {
			$overlay_positions = ' uk-flex-bottom uk-flex-right';
		}

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? ' uk-' . $settings->overlay_styles . '' : '';
		$overlay_styles_int = ($overlay_styles) ? 'uk-overlay' : 'uk-panel';

		$overlay_container = (isset ( $settings->overlay_container ) && $settings->overlay_container) ? $settings->overlay_container : '';

		$overlay_container_cls = ($overlay_container) ? ' ' . (($overlay_container == 'default') ? 'container' : 'uk-container uk-container-' . $overlay_container) : '';

		$overlay_container_padding = ($overlay_container) ? ((isset ( $settings->overlay_container_padding ) && $settings->overlay_container_padding) ? ' uk-section-' . $settings->overlay_container_padding : '') : '';

		$overlay_padding = ($overlay_styles) ? ((isset ( $settings->overlay_padding ) && $settings->overlay_padding) ? ' uk-padding-' . $settings->overlay_padding : '') : '';

		$overlay_position_check = (isset ( $settings->overlay_positions ) && $settings->overlay_positions) ? $settings->overlay_positions : '';

		$overlay_width = '';

		if ($overlay_position_check != 'top' && $overlay_position_check != 'bottom') {
			$overlay_width = (isset ( $settings->overlay_width ) && $settings->overlay_width) ? ' uk-width-' . $settings->overlay_width : '';
		}

		$overlay_margin = (isset ( $settings->overlay_margin ) && $settings->overlay_margin) ? $settings->overlay_margin : '';

		$overlay_margin_cls = (empty ( $overlay_container ) && ! empty ( $overlay_margin )) ? (($overlay_margin == 'none') ? '' : ' uk-padding-' . $overlay_margin) : '';

		$overlay_margin_cls .= (empty ( $overlay_container ) && empty ( $overlay_margin )) ? ' uk-padding' : '';

		$thumbnail_width = (isset ( $settings->thumbnail_width ) && $settings->thumbnail_width) ? $settings->thumbnail_width : '100';
		$thumbnail_width_cls = ($thumbnail_width) ? ' width="' . $thumbnail_width . '"' : '';

		$thumbnail_height = (isset ( $settings->thumbnail_height ) && $settings->thumbnail_height) ? $settings->thumbnail_height : '';
		$thumbnail_height_cls = ($thumbnail_height) ? ' height="' . $thumbnail_height . '"' : '';

		$item_color = (isset ( $settings->item_color ) && $settings->item_color) ? ' uk-' . $settings->item_color : '';

		$overlay_transition = (isset ( $settings->overlay_transition ) && $settings->overlay_transition) ? ' uk-transition-' . $settings->overlay_transition : '';

		$overlay_horizontal_start = (isset ( $settings->overlay_horizontal_start ) && $settings->overlay_horizontal_start) ? $settings->overlay_horizontal_start : '0';
		$overlay_horizontal_end = (isset ( $settings->overlay_horizontal_end ) && $settings->overlay_horizontal_end) ? $settings->overlay_horizontal_end : '0';

		$overlay_horizontal = (! empty ( $overlay_horizontal_start ) || ! empty ( $overlay_horizontal_end )) ? 'x: ' . $overlay_horizontal_start . ',0,' . $overlay_horizontal_end . ';' : '';

		$overlay_vertical_start = (isset ( $settings->overlay_vertical_start ) && $settings->overlay_vertical_start) ? $settings->overlay_vertical_start : '0';
		$overlay_vertical_end = (isset ( $settings->overlay_vertical_end ) && $settings->overlay_vertical_end) ? $settings->overlay_vertical_end : '0';

		$overlay_vertical = (! empty ( $overlay_vertical_start ) || ! empty ( $overlay_vertical_end )) ? 'y: ' . $overlay_vertical_start . ',0,' . $overlay_vertical_end . ';' : '';

		$overlay_scale_start = (isset ( $settings->overlay_scale_start ) && $settings->overlay_scale_start) ? (( int ) $settings->overlay_scale_start / 100) : '';
		$overlay_scale_end = (isset ( $settings->overlay_scale_end ) && $settings->overlay_scale_end) ? (( int ) $settings->overlay_scale_end / 100) : '';
		$overlay_scale = '';

		if (! empty ( $overlay_scale_start ) && empty ( $overlay_scale_end )) {
			$overlay_scale .= 'scale: ' . $overlay_scale_start . ',1,1;';
		} elseif (empty ( $overlay_scale_start ) && ! empty ( $overlay_scale_end )) {
			$overlay_scale .= 'scale: 1,1,' . $overlay_scale_end . ';';
		} elseif (empty ( $overlay_scale_start ) && empty ( $overlay_scale_end )) {
			$overlay_scale .= '';
		} else {
			$overlay_scale .= 'scale: ' . $overlay_scale_start . ',1,' . $overlay_scale_end . ';';
		}

		$overlay_rotate_start = (isset ( $settings->overlay_rotate_start ) && $settings->overlay_rotate_start) ? $settings->overlay_rotate_start : '0';
		$overlay_rotate_end = (isset ( $settings->overlay_rotate_end ) && $settings->overlay_rotate_end) ? $settings->overlay_rotate_end : '0';
		$overlay_rotate = (! empty ( $overlay_rotate_start ) || ! empty ( $overlay_rotate_end )) ? 'rotate: ' . $overlay_rotate_start . ',0,' . $overlay_rotate_end . ';' : '';

		$overlay_opacity_start = (isset ( $settings->overlay_opacity_start ) && $settings->overlay_opacity_start) ? (( int ) $settings->overlay_opacity_start / 100) : '';
		$overlay_opacity_end = (isset ( $settings->overlay_opacity_end ) && $settings->overlay_opacity_end) ? (( int ) $settings->overlay_opacity_end / 100) : '';
		$overlay_opacity = '';

		if (! empty ( $overlay_opacity_start ) && empty ( $overlay_opacity_end )) {
			$overlay_opacity .= 'opacity: ' . $overlay_opacity_start . ',1,1;';
		} elseif (empty ( $overlay_opacity_start ) && ! empty ( $overlay_opacity_end )) {
			$overlay_opacity .= 'opacity: 1,1,' . $overlay_opacity_end . ';';
		} elseif (empty ( $overlay_opacity_start ) && empty ( $overlay_opacity_end )) {
			$overlay_opacity .= '';
		} else {
			$overlay_opacity .= 'opacity: ' . $overlay_opacity_start . ',1,' . $overlay_opacity_end . ';';
		}
		$overlay_parallax_cls = '';

		if (! empty ( $overlay_horizontal ) || ! empty ( $overlay_vertical ) || ! empty ( $overlay_scale ) || ! empty ( $overlay_rotate ) || ! empty ( $overlay_opacity )) {
			$overlay_parallax_cls .= ' uk-slideshow-parallax="' . $overlay_horizontal . $overlay_vertical . $overlay_scale . $overlay_rotate . $overlay_opacity . '"';
		}

		// Title Parallax.
		$title_horizontal_start = (isset ( $settings->title_horizontal_start ) && $settings->title_horizontal_start) ? $settings->title_horizontal_start : '0';
		$title_horizontal_end = (isset ( $settings->title_horizontal_end ) && $settings->title_horizontal_end) ? $settings->title_horizontal_end : '0';
		$title_horizontal = (! empty ( $title_horizontal_start ) || ! empty ( $title_horizontal_end )) ? 'x: ' . $title_horizontal_start . ',0,' . $title_horizontal_end . ';' : '';

		$title_vertical_start = (isset ( $settings->title_vertical_start ) && $settings->title_vertical_start) ? $settings->title_vertical_start : '0';
		$title_vertical_end = (isset ( $settings->title_vertical_end ) && $settings->title_vertical_end) ? $settings->title_vertical_end : '0';
		$title_vertical = (! empty ( $title_vertical_start ) || ! empty ( $title_vertical_end )) ? 'y: ' . $title_vertical_start . ',0,' . $title_vertical_end . ';' : '';

		$title_scale_start = (isset ( $settings->title_scale_start ) && $settings->title_scale_start) ? (( int ) $settings->title_scale_start / 100) : '';
		$title_scale_end = (isset ( $settings->title_scale_end ) && $settings->title_scale_end) ? (( int ) $settings->title_scale_end / 100) : '';
		$title_scale = '';

		if (! empty ( $title_scale_start ) && empty ( $title_scale_end )) {
			$title_scale .= 'scale: ' . $title_scale_start . ',1,1;';
		} elseif (empty ( $title_scale_start ) && ! empty ( $title_scale_end )) {
			$title_scale .= 'scale: 1,1,' . $title_scale_end . ';';
		} elseif (empty ( $title_scale_start ) && empty ( $title_scale_end )) {
			$title_scale .= '';
		} else {
			$title_scale .= 'scale: ' . $title_scale_start . ',1,' . $title_scale_end . ';';
		}

		$title_rotate_start = (isset ( $settings->title_rotate_start ) && $settings->title_rotate_start) ? $settings->title_rotate_start : '0';
		$title_rotate_end = (isset ( $settings->title_rotate_end ) && $settings->title_rotate_end) ? $settings->title_rotate_end : '0';
		$title_rotate = (! empty ( $title_rotate_start ) || ! empty ( $title_rotate_end )) ? 'rotate: ' . $title_rotate_start . ',0,' . $title_rotate_end . ';' : '';

		$title_opacity_start = (isset ( $settings->title_opacity_start ) && $settings->title_opacity_start) ? (( int ) $settings->title_opacity_start / 100) : '';
		$title_opacity_end = (isset ( $settings->title_opacity_end ) && $settings->title_opacity_end) ? (( int ) $settings->title_opacity_end / 100) : '';
		$title_opacity = '';

		if (! empty ( $title_opacity_start ) && empty ( $title_opacity_end )) {
			$title_opacity .= 'opacity: ' . $title_opacity_start . ',1,1;';
		} elseif (empty ( $title_opacity_start ) && ! empty ( $title_opacity_end )) {
			$title_opacity .= 'opacity: 1,1,' . $title_opacity_end . ';';
		} elseif (empty ( $title_opacity_start ) && empty ( $title_opacity_end )) {
			$title_opacity .= '';
		} else {
			$title_opacity .= 'opacity: ' . $title_opacity_start . ',1,' . $title_opacity_end . ';';
		}

		$use_title_parallax = (isset ( $settings->use_title_parallax ) && $settings->use_title_parallax) ? 1 : 0;
		$title_transition = '';
		if (empty ( $overlay_transition ) && $use_title_parallax) {
			if (! empty ( $title_horizontal ) || ! empty ( $title_vertical ) || ! empty ( $title_scale ) || ! empty ( $title_rotate ) || ! empty ( $title_opacity )) {
				$title_transition .= ' uk-slideshow-parallax="' . $title_horizontal . $title_vertical . $title_scale . $title_rotate . $title_opacity . '"';
			}
		}

		// Meta Parallax.
		$meta_horizontal_start = (isset ( $settings->meta_horizontal_start ) && $settings->meta_horizontal_start) ? $settings->meta_horizontal_start : '0';
		$meta_horizontal_end = (isset ( $settings->meta_horizontal_end ) && $settings->meta_horizontal_end) ? $settings->meta_horizontal_end : '0';
		$meta_horizontal = (! empty ( $meta_horizontal_start ) || ! empty ( $meta_horizontal_end )) ? 'x: ' . $meta_horizontal_start . ',0,' . $meta_horizontal_end . ';' : '';

		$meta_vertical_start = (isset ( $settings->meta_vertical_start ) && $settings->meta_vertical_start) ? $settings->meta_vertical_start : '0';
		$meta_vertical_end = (isset ( $settings->meta_vertical_end ) && $settings->meta_vertical_end) ? $settings->meta_vertical_end : '0';
		$meta_vertical = (! empty ( $meta_vertical_start ) || ! empty ( $meta_vertical_end )) ? 'y: ' . $meta_vertical_start . ',0,' . $meta_vertical_end . ';' : '';

		$meta_scale_start = (isset ( $settings->meta_scale_start ) && $settings->meta_scale_start) ? (( int ) $settings->meta_scale_start / 100) : '';
		$meta_scale_end = (isset ( $settings->meta_scale_end ) && $settings->meta_scale_end) ? (( int ) $settings->meta_scale_end / 100) : '';
		$meta_scale = '';

		if (! empty ( $meta_scale_start ) && empty ( $meta_scale_end )) {
			$meta_scale .= 'scale: ' . $meta_scale_start . ',1,1;';
		} elseif (empty ( $meta_scale_start ) && ! empty ( $meta_scale_end )) {
			$meta_scale .= 'scale: 1,1,' . $meta_scale_end . ';';
		} elseif (empty ( $meta_scale_start ) && empty ( $meta_scale_end )) {
			$meta_scale .= '';
		} else {
			$meta_scale .= 'scale: ' . $meta_scale_start . ',1,' . $meta_scale_end . ';';
		}

		$meta_rotate_start = (isset ( $settings->meta_rotate_start ) && $settings->meta_rotate_start) ? $settings->meta_rotate_start : '0';
		$meta_rotate_end = (isset ( $settings->meta_rotate_end ) && $settings->meta_rotate_end) ? $settings->meta_rotate_end : '0';
		$meta_rotate = (! empty ( $meta_rotate_start ) || ! empty ( $meta_rotate_end )) ? 'rotate: ' . $meta_rotate_start . ',0,' . $meta_rotate_end . ';' : '';

		$meta_opacity_start = (isset ( $settings->meta_opacity_start ) && $settings->meta_opacity_start) ? (( int ) $settings->meta_opacity_start / 100) : '';
		$meta_opacity_end = (isset ( $settings->meta_opacity_end ) && $settings->meta_opacity_end) ? (( int ) $settings->meta_opacity_end / 100) : '';
		$meta_opacity = '';

		if (! empty ( $meta_opacity_start ) && empty ( $meta_opacity_end )) {
			$meta_opacity .= 'opacity: ' . $meta_opacity_start . ',1,1;';
		} elseif (empty ( $meta_opacity_start ) && ! empty ( $meta_opacity_end )) {
			$meta_opacity .= 'opacity: 1,1,' . $meta_opacity_end . ';';
		} elseif (empty ( $meta_opacity_start ) && empty ( $meta_opacity_end )) {
			$meta_opacity .= '';
		} else {
			$meta_opacity .= 'opacity: ' . $meta_opacity_start . ',1,' . $meta_opacity_end . ';';
		}

		$use_meta_parallax = (isset ( $settings->use_meta_parallax ) && $settings->use_meta_parallax) ? 1 : 0;

		$meta_transition = '';
		if (empty ( $overlay_transition ) && $use_meta_parallax) {
			if (! empty ( $meta_horizontal ) || ! empty ( $meta_vertical ) || ! empty ( $meta_scale ) || ! empty ( $meta_rotate ) || ! empty ( $meta_opacity )) {
				$meta_transition .= ' uk-slideshow-parallax="' . $meta_horizontal . $meta_vertical . $meta_scale . $meta_rotate . $meta_opacity . '"';
			}
		}

		// Content Parallax.
		$content_horizontal_start = (isset ( $settings->content_horizontal_start ) && $settings->content_horizontal_start) ? $settings->content_horizontal_start : '0';
		$content_horizontal_end = (isset ( $settings->content_horizontal_end ) && $settings->content_horizontal_end) ? $settings->content_horizontal_end : '0';
		$content_horizontal = (! empty ( $content_horizontal_start ) || ! empty ( $content_horizontal_end )) ? 'x: ' . $content_horizontal_start . ',0,' . $content_horizontal_end . ';' : '';

		$content_vertical_start = (isset ( $settings->content_vertical_start ) && $settings->content_vertical_start) ? $settings->content_vertical_start : '0';
		$content_vertical_end = (isset ( $settings->content_vertical_end ) && $settings->content_vertical_end) ? $settings->content_vertical_end : '0';
		$content_vertical = (! empty ( $content_vertical_start ) || ! empty ( $content_vertical_end )) ? 'y: ' . $content_vertical_start . ',0,' . $content_vertical_end . ';' : '';

		$content_scale_start = (isset ( $settings->content_scale_start ) && $settings->content_scale_start) ? (( int ) $settings->content_scale_start / 100) : '';
		$content_scale_end = (isset ( $settings->content_scale_end ) && $settings->content_scale_end) ? (( int ) $settings->content_scale_end / 100) : '';
		$content_scale = '';

		if (! empty ( $content_scale_start ) && empty ( $content_scale_end )) {
			$content_scale .= 'scale: ' . $content_scale_start . ',1,1;';
		} elseif (empty ( $content_scale_start ) && ! empty ( $content_scale_end )) {
			$content_scale .= 'scale: 1,1,' . $content_scale_end . ';';
		} elseif (empty ( $content_scale_start ) && empty ( $content_scale_end )) {
			$content_scale .= '';
		} else {
			$content_scale .= 'scale: ' . $content_scale_start . ',1,' . $content_scale_end . ';';
		}

		$content_rotate_start = (isset ( $settings->content_rotate_start ) && $settings->content_rotate_start) ? $settings->content_rotate_start : '0';
		$content_rotate_end = (isset ( $settings->content_rotate_end ) && $settings->content_rotate_end) ? $settings->content_rotate_end : '0';
		$content_rotate = (! empty ( $content_rotate_start ) || ! empty ( $content_rotate_end )) ? 'rotate: ' . $content_rotate_start . ',0,' . $content_rotate_end . ';' : '';

		$content_opacity_start = (isset ( $settings->content_opacity_start ) && $settings->content_opacity_start) ? (( int ) $settings->content_opacity_start / 100) : '';
		$content_opacity_end = (isset ( $settings->content_opacity_end ) && $settings->content_opacity_end) ? (( int ) $settings->content_opacity_end / 100) : '';
		$content_opacity = '';

		if (! empty ( $content_opacity_start ) && empty ( $content_opacity_end )) {
			$content_opacity .= 'opacity: ' . $content_opacity_start . ',1,1;';
		} elseif (empty ( $content_opacity_start ) && ! empty ( $content_opacity_end )) {
			$content_opacity .= 'opacity: 1,1,' . $content_opacity_end . ';';
		} elseif (empty ( $content_opacity_start ) && empty ( $content_opacity_end )) {
			$content_opacity .= '';
		} else {
			$content_opacity .= 'opacity: ' . $content_opacity_start . ',1,' . $content_opacity_end . ';';
		}

		$use_content_parallax = (isset ( $settings->use_content_parallax ) && $settings->use_content_parallax) ? 1 : 0;

		$content_transition = '';
		if (empty ( $overlay_transition ) && $use_content_parallax) {
			if (! empty ( $content_horizontal ) || ! empty ( $content_vertical ) || ! empty ( $content_scale ) || ! empty ( $content_rotate ) || ! empty ( $content_opacity )) {
				$content_transition .= ' uk-slideshow-parallax="' . $content_horizontal . $content_vertical . $content_scale . $content_rotate . $content_opacity . '"';
			}
		}

		// Button Parallax.
		$button_horizontal_start = (isset ( $settings->button_horizontal_start ) && $settings->button_horizontal_start) ? $settings->button_horizontal_start : '0';
		$button_horizontal_end = (isset ( $settings->button_horizontal_end ) && $settings->button_horizontal_end) ? $settings->button_horizontal_end : '0';
		$button_horizontal = (! empty ( $button_horizontal_start ) || ! empty ( $button_horizontal_end )) ? 'x: ' . $button_horizontal_start . ',0,' . $button_horizontal_end . ';' : '';

		$button_vertical_start = (isset ( $settings->button_vertical_start ) && $settings->button_vertical_start) ? $settings->button_vertical_start : '0';
		$button_vertical_end = (isset ( $settings->button_vertical_end ) && $settings->button_vertical_end) ? $settings->button_vertical_end : '0';
		$button_vertical = (! empty ( $button_vertical_start ) || ! empty ( $button_vertical_end )) ? 'y: ' . $button_vertical_start . ',0,' . $button_vertical_end . ';' : '';

		$button_scale_start = (isset ( $settings->button_scale_start ) && $settings->button_scale_start) ? (( int ) $settings->button_scale_start / 100) : '';
		$button_scale_end = (isset ( $settings->button_scale_end ) && $settings->button_scale_end) ? (( int ) $settings->button_scale_end / 100) : '';
		$button_scale = '';

		if (! empty ( $button_scale_start ) && empty ( $button_scale_end )) {
			$button_scale .= 'scale: ' . $button_scale_start . ',1,1;';
		} elseif (empty ( $button_scale_start ) && ! empty ( $button_scale_end )) {
			$button_scale .= 'scale: 1,1,' . $button_scale_end . ';';
		} elseif (empty ( $button_scale_start ) && empty ( $button_scale_end )) {
			$button_scale .= '';
		} else {
			$button_scale .= 'scale: ' . $button_scale_start . ',1,' . $button_scale_end . ';';
		}

		$button_rotate_start = (isset ( $settings->button_rotate_start ) && $settings->button_rotate_start) ? $settings->button_rotate_start : '0';
		$button_rotate_end = (isset ( $settings->button_rotate_end ) && $settings->button_rotate_end) ? $settings->button_rotate_end : '0';
		$button_rotate = (! empty ( $button_rotate_start ) || ! empty ( $button_rotate_end )) ? 'rotate: ' . $button_rotate_start . ',0,' . $button_rotate_end . ';' : '';

		$button_opacity_start = (isset ( $settings->button_opacity_start ) && $settings->button_opacity_start) ? (( int ) $settings->button_opacity_start / 100) : '';
		$button_opacity_end = (isset ( $settings->button_opacity_end ) && $settings->button_opacity_end) ? (( int ) $settings->button_opacity_end / 100) : '';
		$button_opacity = '';

		if (! empty ( $button_opacity_start ) && empty ( $button_opacity_end )) {
			$button_opacity .= 'opacity: ' . $button_opacity_start . ',1,1;';
		} elseif (empty ( $button_opacity_start ) && ! empty ( $button_opacity_end )) {
			$button_opacity .= 'opacity: 1,1,' . $button_opacity_end . ';';
		} elseif (empty ( $button_opacity_start ) && empty ( $button_opacity_end )) {
			$button_opacity .= '';
		} else {
			$button_opacity .= 'opacity: ' . $button_opacity_start . ',1,' . $button_opacity_end . ';';
		}
		$use_button_parallax = (isset ( $settings->use_button_parallax ) && $settings->use_button_parallax) ? 1 : 0;
		$button_transition = '';
		if (empty ( $overlay_transition ) && $use_button_parallax) {
			if (! empty ( $button_horizontal ) || ! empty ( $button_vertical ) || ! empty ( $button_scale ) || ! empty ( $button_rotate ) || ! empty ( $button_opacity )) {
				$button_transition .= ' uk-slideshow-parallax="' . $button_horizontal . $button_vertical . $button_scale . $button_rotate . $button_opacity . '"';
			}
		}

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';
		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$meta_element = (isset ( $settings->meta_element ) && $settings->meta_element) ? $settings->meta_element : 'div';
		$meta_style_cls = (isset ( $settings->meta_style ) && $settings->meta_style) ? $settings->meta_style : '';

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->meta_color ) && $settings->meta_color) ? ' uk-text-' . $settings->meta_color : '';
		$meta_style .= (isset ( $settings->meta_text_transform ) && $settings->meta_text_transform) ? ' uk-text-' . $settings->meta_text_transform : '';
		$meta_style .= (isset ( $settings->meta_margin_top ) && $settings->meta_margin_top) ? ' uk-margin-' . $settings->meta_margin_top . '-top' : ' uk-margin-top';

		// Remove margin for heading element
		if ($meta_element != 'div' || ($meta_style_cls && $meta_style_cls != 'text-meta')) {
			$meta_style .= ' uk-margin-remove-bottom';
		}

		$meta_alignment = (isset ( $settings->meta_alignment ) && $settings->meta_alignment) ? $settings->meta_alignment : '';

		$attribs = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? ' target="' . $settings->link_new_tab . '"' : '';
		$btn_styles = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? '' . $settings->link_button_style : '';
		$link_button_size = (isset ( $settings->link_button_size ) && $settings->link_button_size) ? ' ' . $settings->link_button_size : '';

		$button_style_cls = '';
		if (empty ( $btn_styles )) {
			$button_style_cls .= 'uk-button uk-button-default' . $link_button_size;
		} elseif ($btn_styles == 'link' || $btn_styles == 'link-muted' || $btn_styles == 'link-text') {
			$button_style_cls .= 'uk-' . $btn_styles;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $btn_styles . $link_button_size;
		}

		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';
		$all_button_title = (isset ( $settings->all_button_title ) && $settings->all_button_title) ? $settings->all_button_title : 'Learn more';

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

		// Default Animation.

		$animation = (isset ( $settings->animation ) && $settings->animation) ? $settings->animation : '';
		$animation_repeat = ($animation) ? ((isset ( $settings->animation_repeat ) && $settings->animation_repeat) ? ' repeat: true;' : '') : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}

		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';

		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;

		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';
		$output = '';

		$output .= '<div class="tm-slideshow-wrapper' . $text_alignment . $general . $max_width_cfg . '"' . $animation . '>';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div class="tm-slideshow"' . $attrs_slideshow . '>';

		$output .= ($slidenav_on_hover) ? '<div class="uk-position-relative uk-visible-toggle" tabindex="-1">' : '<div class="uk-position-relative">';

		$output .= '<ul class="uk-slideshow-items' . $box_shadow . '"' . $height_cls . '>';

		if (isset ( $settings->ui_slideshow_items ) && count ( ( array ) $settings->ui_slideshow_items )) {
			foreach ( $settings->ui_slideshow_items as $key => $value ) {
				$media_item = (isset ( $value->media_item ) && $value->media_item) ? $value->media_item : '';
				$image_src = isset ( $media_item->src ) ? $media_item->src : $media_item;
				if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
					$image_src = $image_src;
				} elseif ($image_src) {
					$image_src = Uri::base ( true ) . '/' . $image_src;
				}

				$text_item_color = (isset ( $value->text_item_color ) && $value->text_item_color) ? ' uk-' . $value->text_item_color : '';
				$item_title = (isset ( $value->title ) && $value->title) ? $value->title : '';
				$item_meta = (isset ( $value->meta ) && $value->meta) ? $value->meta : '';
				$item_content = (isset ( $value->content ) && $value->content) ? $value->content : '';

				$image_panel = (isset ( $value->image_panel ) && $value->image_panel) ? 1 : 0;
				$media_background = ($image_panel) ? ((isset ( $value->media_background ) && $value->media_background) ? ' style="background-color: ' . $value->media_background . ';"' : '') : '';
				$media_blend_mode = ($image_panel && $media_background) ? ((isset ( $value->media_blend_mode ) && $value->media_blend_mode) ? ' uk-blend-' . $value->media_blend_mode : '') : false;
				$media_overlay = ($image_panel) ? ((isset ( $value->media_overlay ) && $value->media_overlay) ? '<div class="uk-position-cover" style="background-color: ' . $value->media_overlay . '"></div>' : '') : '';

				$image_alt = (isset ( $value->image_alt ) && $value->image_alt) ? $value->image_alt : '';
				$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';

				$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';

				$text_item_color_cls = '';

				if (empty ( $overlay_styles ) || 'overlay-custom' == $overlay_styles) {
					if (empty ( $text_item_color )) {
						$text_item_color_cls .= $item_color;
					} else {
						$text_item_color_cls .= $text_item_color;
					}
				}

				$title_link = (isset ( $value->title_link->src ) && $value->title_link->src) ? $value->title_link->src : '';
				$button_title = (isset ( $value->button_title ) && $value->button_title) ? $value->button_title : '';

				if (empty ( $button_title )) {
					$button_title .= $all_button_title;
				}

				$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';

				$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

				$output .= '<li class="el-item item-' . $key . '"' . $media_background . '>';
				$output .= ($kenburns_transition) ? '<div class="uk-position-cover uk-animation-kenburns uk-animation-reverse' . $kenburns_transition . $media_blend_mode . '"' . $kenburns_duration . '>' : '';

				$output .= '<img class="ui-image' . ($kenburns_transition ? '' : $media_blend_mode) . '" src="' . $image_src . '" ' . $image_alt_init . ' uk-cover>';

				$output .= ($kenburns_transition) ? '</div>' : '';

				$output .= $media_overlay;

				$output .= '<div class="uk-position-cover uk-flex' . $overlay_positions . $overlay_container_cls . $overlay_container_padding . $overlay_margin_cls . '">';

				$output .= '<div class="' . $overlay_styles_int . $overlay_pos_int . $overlay_width . $overlay_transition . $text_item_color_cls . $overlay_styles . $overlay_padding . (! empty ( $overlay_transition ) ? $overlay_transition : '') . ' uk-margin-remove-first-child"' . (empty ( $overlay_transition ) ? $overlay_parallax_cls : '') . '>';

				if ($meta_alignment == 'top' && $item_meta) {
					$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '"' . $meta_transition . '>';
					$output .= $item_meta;
					$output .= '</' . $meta_element . '>';
				}

				if ($item_title) {
					$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $font_weight . '"' . $title_transition . '>';
					$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
					$output .= $item_title;
					$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
					$output .= '</' . $heading_selector . '>';
				}

				if (empty ( $meta_alignment ) && $item_meta) {
					$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '"' . $meta_transition . '>';
					$output .= $item_meta;
					$output .= '</' . $meta_element . '>';
				}

				if ($item_content) {
					$output .= '<div class="ui-content uk-panel' . $content_style . '"' . $content_transition . '>';
					$output .= $item_content;
					$output .= '</div>';
				}

				if ($meta_alignment == 'content' && $item_meta) {
					$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '"' . $meta_transition . '>';
					$output .= $item_meta;
					$output .= '</' . $meta_element . '>';
				}

				if (! empty ( $button_title ) && $title_link) {
					$output .= '<div class="' . $btn_margin_top . '">';
					$output .= '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . $button_transition . '>' . $button_title . '</a>';
					$output .= '</div>';
				}

				$output .= '</div>';

				$output .= '</div>';

				$output .= '</li>';
			}
		}

		$output .= '</ul>';

		if ($slidenav_position == 'default') {
			$output .= ($slidenav_on_hover) ? '<div class="uk-hidden-hover uk-hidden-touch' . $slidenav_breakpoint_cls . $item_color . '">' : '<div class="tm-sidenav' . $slidenav_breakpoint_cls . $item_color . '">';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-left" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-right" href="#" uk-slidenav-next uk-slideshow-item="next"></a>';
			$output .= '</div> ';
		} elseif ($slidenav_position == 'outside') {
			$output .= ($slidenav_on_hover) ? '<div class="ui-sidenav-outside uk-hidden-hover uk-hidden-touch' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">' : '<div class="ui-sidenav-outside' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-left-out" href="#" uk-slidenav-previous uk-slideshow-item="previous" uk-toggle="cls: uk-position-center-left-out uk-position-center-left; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-right-out" href="#" uk-slidenav-next uk-slideshow-item="next" uk-toggle="cls: uk-position-center-right-out uk-position-center-right; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
			$output .= '</div> ';
		} elseif ($slidenav_position != '') {
			$output .= ($slidenav_on_hover) ? '<div class="uk-slidenav-container uk-hidden-hover uk-hidden-touch' . $slidenav_position_cls . $slidenav_margin . $slidenav_breakpoint_cls . $item_color . '">' : '<div class="uk-slidenav-container' . $slidenav_position_cls . $slidenav_margin . $slidenav_breakpoint_cls . $item_color . '">';
			$output .= '<a class="ui-slidenav' . $larger_style_init . '" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>';
			$output .= '<a class="ui-slidenav' . $larger_style_init . '" href="#" uk-slidenav-next uk-slideshow-item="next"></a>';
			$output .= '</div>';
		}

		if ($navigation_below) {
			$output .= '</div>';
		}

		if ($navigation_control == 'dotnav') {
			if ($navigation_below) {
				$output .= ($navigation_below_color_cls) ? '<div class="ui-nav-control' . $navigation_below_margin_cls . $navigation_breakpoint_cls . $navigation_below_color_cls . '">' : '';
				$output .= ($navigation_below_color_cls) ? '<ul class="uk-slideshow-nav uk-dotnav' . $navigation_below_cls . '"></ul>' : '<ul class="uk-slideshow-nav uk-dotnav' . $navigation_below_cls . $navigation_below_margin_cls . $navigation_breakpoint_cls . '"></ul>';
				$output .= ($navigation_below_color_cls) ? '</div>' : '';
			} else {
				$output .= '<div class="ui-nav-control' . $navigation_margin . $navigation . $navigation_breakpoint_cls . $item_color . '"> ';
				$output .= '<ul class="uk-slideshow-nav uk-dotnav' . $navigation_vertical . $navigation_cls . '"></ul>';
				$output .= '</div> ';
			}
		} elseif ($navigation_control == 'thumbnav') {
			if ($navigation_below) {
				$output .= ($navigation_below_color_cls) ? '<div class="ui-nav-control' . $navigation_below_margin_cls . $navigation_breakpoint_cls . $navigation_below_color_cls . '">' : '';
				$output .= ($navigation_below_color_cls) ? '<ul class="uk-thumbnav' . $thumbnav_wrap_cls . '">' : '<ul class="uk-thumbnav' . $thumbnav_wrap_cls . $navigation_below_cls . $navigation_below_margin_cls . $navigation_breakpoint_cls . '">';
			} else {
				$output .= '<div class="ui-nav-control' . $navigation_margin . $navigation . $navigation_breakpoint_cls . '"> ';
				$output .= '<ul class="uk-thumbnav' . $navigation_vertical_thumb . $thumbnav_wrap_cls . $navigation_cls . '">';
			}

			if (isset ( $settings->ui_slideshow_items ) && count ( ( array ) $settings->ui_slideshow_items )) {
				foreach ( $settings->ui_slideshow_items as $key => $value ) {
					$media_item = (isset ( $value->media_item ) && $value->media_item) ? $value->media_item : '';
					$image_src = isset ( $media_item->src ) ? $media_item->src : $media_item;

					if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
						$image_src = $image_src;
					} elseif ($image_src) {
						$image_src = Uri::base ( true ) . '/' . $image_src;
					}
					$nav_image = (isset ( $value->navigation_image_item ) && $value->navigation_image_item) ? $value->navigation_image_item : '';
					$nav_image_src = isset ( $nav_image->src ) ? $nav_image->src : $nav_image;
					if (strpos ( $nav_image_src, 'http://' ) !== false || strpos ( $nav_image_src, 'https://' ) !== false) {
						$nav_image_src = $nav_image_src;
					} elseif ($nav_image_src) {
						$nav_image_src = Uri::base ( true ) . '/' . $nav_image_src;
					}

					$image_alt = (isset ( $value->image_alt ) && $value->image_alt) ? $value->image_alt : '';
					$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';
					$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';

					$output .= '<li uk-slideshow-item="' . $key . '">';
					if ($nav_image_src) {
						$output .= '<a href="#"><img class="img-thumb' . $image_svg_color . '" src="' . $nav_image_src . '" ' . $thumbnail_width_cls . $thumbnail_height_cls . $image_alt . $image_svg_inline_cls . '></a>';
					} else {
						$output .= '<a href="#"><img class="img-thumb' . $image_svg_color . '" src="' . $image_src . '" ' . $thumbnail_width_cls . $thumbnail_height_cls . $image_alt . $image_svg_inline_cls . '></a>';
					}

					$output .= '</li>';
				}
			}
			if ($navigation_below) {
				$output .= '</ul>';
				$output .= ($navigation_below_color_cls) ? '</div>' : '';
			} else {
				$output .= '</ul>';
				$output .= '</div> ';
			}
		}

		if (! $navigation_below) {
			$output .= '</div>';
		}
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
	public function scripts() {
		HTMLHelper::_ ( 'script', 'components/com_jpagebuilder/assets/js/uitheme.js', [ ], [
				'defer' => true
		] );
		HTMLHelper::_ ( 'script', 'components/com_jpagebuilder/assets/js/uitheme-icons.js', [ ], [
				'defer' => true
		] );
	}
	public function stylesheets() {
		$style_sheet = [
				'components/com_jpagebuilder/assets/css/uitheme.css'
		];
		
		return $style_sheet;
	}
	public function css() {
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$button_style = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? $settings->link_button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? $settings->overlay_styles : '';
		$overlay_background = (isset ( $settings->overlay_background ) && $settings->overlay_background) ? 'background-color: ' . $settings->overlay_background . ';' : '';

		$css = '';
		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$css .= $addon_id . ' .ui-meta {' . $custom_meta_color . '}';
		}
		if ($content_color) {
			$css .= $addon_id . ' .ui-content {' . $content_color . '}';
		}
		if ($overlay_styles == 'overlay-custom' && $overlay_background) {
			$css .= $addon_id . ' .uk-overlay-custom {' . $overlay_background . '}';
		}
		if ($button_style == 'custom') {
			if ($button_background || $button_color) {
				$css .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
			}
			if ($button_background_hover || $button_hover_color) {
				$css .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active, ' . $addon_id . ' .uk-button-custom.uk-active {' . $button_background_hover . $button_hover_color . '}';
			}
		}

		return $css;
	}
}
