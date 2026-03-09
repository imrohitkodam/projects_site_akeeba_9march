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
class JpagebuilderAddonContentalign extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$box_shadow = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$text = (isset ( $settings->text ) && $settings->text) ? $settings->text : '';

		// New style options.
		$card_title = (isset ( $settings->card_title ) && $settings->card_title) ? $settings->card_title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

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

		$kenburns_transition = (isset ( $settings->kenburns_transition ) && $settings->kenburns_transition) ? ' uk-transform-origin-' . $settings->kenburns_transition : '';
		$kenburns_duration = (isset ( $settings->kenburns_duration ) && $settings->kenburns_duration) ? $settings->kenburns_duration : '';
		if ($kenburns_duration) {
			$kenburns_duration = ' style="-webkit-animation-duration: ' . $kenburns_duration . 's; animation-duration: ' . $kenburns_duration . 's;"';
		}

		$min_height = (isset ( $settings->min_height ) && $settings->min_height) ? 'minHeight: ' . $settings->min_height . ';' : 'minHeight: 300;';
		$max_height = (isset ( $settings->max_height ) && $settings->max_height) ? ' maxHeight: ' . $settings->max_height . ';' : '';

		$slideshow_transition_cls = (isset ( $settings->slideshow_transition ) && $settings->slideshow_transition) ? ' animation: ' . $settings->slideshow_transition . ';' : '';

		$velocity = (isset ( $settings->velocity ) && $settings->velocity) ? (( int ) $settings->velocity / 100) : '';
		$velocity_init = (! empty ( $velocity )) ? 'velocity: ' . $velocity . ';' : '';

		$autoplay = (isset ( $settings->autoplay ) && $settings->autoplay) ? 'autoplay: 1; ' : '';
		$pause = ($autoplay) ? ((isset ( $settings->pause ) && $settings->pause) ? '' : ' pauseOnHover: false;') : '';
		$interval = ($autoplay) ? ((isset ( $settings->autoplay_interval ) && $settings->autoplay_interval) ? 'autoplayInterval: ' . (( int ) $settings->autoplay_interval * 1000) . ';' : '') : '';
		$autoplay .= $interval . $pause;

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$link_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? ' target="' . $settings->link_new_tab . '"' : '';
		$button_style = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? '' . $settings->link_button_style : '';

		$button_style_cls = '';
		if ($button_style == 'primary' || $button_style == 'secondary' || $button_style == 'danger' || $button_style == 'text' || $button_style == 'custom') {
			$button_style_cls .= 'uk-button uk-button-' . $button_style . '';
		} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
			$button_style_cls .= 'uk-' . $button_style . '';
		} else {
			$button_style_cls .= 'uk-button uk-button-default';
		}

		$button_size = (isset ( $settings->link_button_size ) && $settings->link_button_size) ? ' ' . $settings->link_button_size : '';
		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';

		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';
		$title_link = (isset ( $settings->title_link ) && $settings->title_link) ? $settings->title_link : '';

		$positions = (isset ( $settings->image_alignment ) && $settings->image_alignment) ? $settings->image_alignment : '';

		// Alignment and Margin for left/right.

		$grid_cls = (isset ( $settings->grid_width ) && $settings->grid_width) ? 'uk-width-' . $settings->grid_width : '';
		$grid_cls_bp = (isset ( $settings->grid_breakpoint ) && $settings->grid_breakpoint) ? '@' . $settings->grid_breakpoint : '';

		$cls_class = ($positions == 'right') ? ' uk-flex-last' . $grid_cls_bp . '' : '';
		$vertical_alignment = (isset ( $settings->vertical_alignment ) && $settings->vertical_alignment) ? 1 : 0;
		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? ' uk-grid-column-' . $settings->grid_column_gap : '';
		$grid_row_gap = (isset ( $settings->grid_row_gap ) && $settings->grid_row_gap) ? ' uk-grid-row-' . $settings->grid_row_gap : '';
		$vertical_alignment_cls = ($vertical_alignment) ? ' uk-flex-middle' : '';

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

		$navigation_color = (isset ( $settings->navigation_color ) && $settings->navigation_color) ? ' uk-' . $settings->navigation_color : '';

		// Sidenav Settings.
		$slidenav_position = (isset ( $settings->slidenav_position ) && $settings->slidenav_position) ? $settings->slidenav_position : '';
		$slidenav_position_cls = (! empty ( $slidenav_position ) || ($slidenav_position != 'default')) ? ' uk-position-' . $slidenav_position . '' : '';
		$slidenav_margin = (isset ( $settings->slidenav_margin ) && $settings->slidenav_margin) ? ' uk-position-' . $settings->slidenav_margin . '' : '';

		$slidenav_on_hover = (isset ( $settings->slidenav_on_hover ) && $settings->slidenav_on_hover) ? 1 : 0;
		$slidenav_breakpoint = (isset ( $settings->slidenav_breakpoint ) && $settings->slidenav_breakpoint) ? $settings->slidenav_breakpoint : '';
		$slidenav_breakpoint_cls = ($slidenav_breakpoint) ? ' uk-visible@' . $slidenav_breakpoint . '' : '';
		$slidenav_outside_breakpoint = (isset ( $settings->slidenav_outside_breakpoint ) && $settings->slidenav_outside_breakpoint) ? ' @' . $settings->slidenav_outside_breakpoint : 'xl';

		$slidenav_outside_color = (isset ( $settings->slidenav_outside_color ) && $settings->slidenav_outside_color) ? ' uk-' . $settings->slidenav_outside_color : '';

		$larger_style = (isset ( $settings->larger_style ) && $settings->larger_style) ? $settings->larger_style : '';
		$larger_style_init = ($larger_style) ? ' uk-slidenav-large' : '';
		$image_margin_top = ($positions == 'bottom') ? ((isset ( $settings->image_margin_top ) && $settings->image_margin_top) ? ' uk-margin-' . $settings->image_margin_top . '-top' : ' uk-margin-top') : '';

		$thumbnail_width = (isset ( $settings->thumbnail_width ) && $settings->thumbnail_width) ? $settings->thumbnail_width : '100';
		$thumbnail_width_cls = ($thumbnail_width) ? ' width="' . $thumbnail_width . '"' : '';
		$thumbnail_height = (isset ( $settings->thumbnail_height ) && $settings->thumbnail_height) ? $settings->thumbnail_height : '';
		$thumbnail_height_cls = ($thumbnail_height) ? ' height="' . $thumbnail_height . '"' : '';
		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';
		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;
		$navigation_vertical_thumb = (! $navigation_below) ? ((isset ( $settings->navigation_vertical ) && $settings->navigation_vertical) ? ' uk-thumbnav-vertical' : '') : '';
		$thumbnav_wrap = (isset ( $settings->thumbnav_wrap ) && $settings->thumbnav_wrap) ? 1 : 0;
		$thumbnav_wrap_cls = ($thumbnav_wrap) ? ((isset ( $settings->thumbnav_wrap ) && $settings->thumbnav_wrap) ? ' uk-flex-nowrap' : '') : false;

		$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';
		$check_render_link = '';
		if (empty ( $settings->title_link ) || strpos ( $settings->title_link, '#' ) === 0) {
			if ($check_target != '_blank') {
				$check_render_link .= ' uk-scroll';
			}
		}

		$output = '';

		$output .= '<div class="ui-content-align' . $general . $max_width_cfg . '">';

		if (($positions == 'left') || ($positions == 'right')) {

			$output .= '<div class="uk-child-width-expand' . $grid_column_gap . $grid_row_gap . $vertical_alignment_cls . '" uk-grid>';

			$output .= '<div class="' . $grid_cls . $grid_cls_bp . $cls_class . '">';

			$output .= '<div class="tm-slideshow' . $box_shadow . '" uk-slideshow="' . $min_height . $max_height . $velocity_init . $slideshow_transition_cls . $autoplay . '">';

			$output .= ($slidenav_on_hover) ? '<div class="uk-position-relative uk-visible-toggle" tabindex="-1">' : '<div class="uk-position-relative">';

			$output .= '<ul class="uk-slideshow-items">';
			if (isset ( $settings->ui_slideshow_items ) && count ( ( array ) $settings->ui_slideshow_items )) {
				foreach ( $settings->ui_slideshow_items as $key => $value ) {
					$media_item = (isset ( $value->media_item ) && $value->media_item) ? $value->media_item : '';
					$image_src = isset ( $media_item->src ) ? $media_item->src : $media_item;

					if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
						$image_src = $image_src;
					} elseif ($image_src) {
						$image_src = Uri::base ( true ) . '/' . $image_src;
					}

					$image_alt = (isset ( $value->image_alt ) && $value->image_alt) ? $value->image_alt : '';
					$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';
					$image_alt_init = '';

					if (empty ( $image_alt )) {
						$image_alt_init .= 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"';
					} else {
						$image_alt_init .= 'alt="' . str_replace ( '"', '', $image_alt ) . '"';
					}

					$output .= '<li class="el-item item-' . $key . '">';

					$output .= ($kenburns_transition != '') ? '<div class="uk-position-cover uk-animation-kenburns uk-animation-reverse' . $kenburns_transition . '"' . $kenburns_duration . '>' : '';

					$output .= '<img class="ui-image" src="' . $image_src . '" ' . $image_alt_init . ' uk-cover>';

					$output .= ($kenburns_transition != '') ? '</div>' : '';

					$output .= '</li>';
				}
			}
			$output .= '</ul>';

			if ($slidenav_position == 'default') {
				$output .= ($slidenav_on_hover) ? '<div class="uk-hidden-hover uk-hidden-touch' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">' : '<div class="tm-sidenav' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">';
				$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-left" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>';
				$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-right" href="#" uk-slidenav-next uk-slideshow-item="next"></a>';
				$output .= '</div> ';
			} elseif ($slidenav_position == 'outside') {
				$output .= ($slidenav_on_hover) ? '<div class="tm-outsite uk-hidden-hover uk-hidden-touch' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">' : '<div class="tm-outsite' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">';
				$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-left-out" href="#" uk-slidenav-previous uk-slideshow-item="previous" uk-toggle="cls: uk-position-center-left-out uk-position-center-left; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
				$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-right-out" href="#" uk-slidenav-next uk-slideshow-item="next" uk-toggle="cls: uk-position-center-right-out uk-position-center-right; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
				$output .= '</div> ';
			} elseif ($slidenav_position != '') {
				$output .= ($slidenav_on_hover) ? '<div class="uk-slidenav-container uk-hidden-hover uk-hidden-touch' . $slidenav_position_cls . $slidenav_margin . $slidenav_breakpoint_cls . $slidenav_outside_color . '">' : '<div class="uk-slidenav-container' . $slidenav_position_cls . $slidenav_margin . $slidenav_breakpoint_cls . $slidenav_outside_color . '">';
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
					$output .= '<div class="ui-nav-control' . $navigation_margin . $navigation . $navigation_breakpoint_cls . $navigation_color . '"> ';
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

						$image_alt_init = '';
						if (empty ( $image_alt )) {
							$image_alt_init .= 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"';
						} else {
							$image_alt_init .= 'alt="' . str_replace ( '"', '', $image_alt ) . '"';
						}
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
		}

		if (($positions == 'left') || ($positions == 'right')) {
			// end 1st colum.

			$output .= '<div class="uk-margin-remove-first-child">';

			if ($card_title) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . '">';
				if ($title_decoration == ' uk-heading-line') {
					$output .= '<span>';
					$output .= $card_title;
					$output .= '</span>';
				} else {
					$output .= $card_title;
				}
				$output .= '</' . $heading_selector . '>';
			}

			if ($text) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $text;
				$output .= '</div>';
			}
			$output .= ($title_link) ? '<div class="' . $btn_margin_top . '"><a class="' . $button_style_cls . $button_size . '" href="' . $title_link . '"' . $link_target . $check_render_link . '>' . $button_title . '</a></div>' : '';
		}

		if (($positions == 'left') || ($positions == 'right')) {
			$output .= '</div>';

			$output .= '</div>';
		}

		if ($positions == 'bottom') {
			if ($card_title) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . '">';
				if ($title_decoration == ' uk-heading-line') {
					$output .= '<span>';
					$output .= $card_title;
					$output .= '</span>';
				} else {
					$output .= $card_title;
				}
				$output .= '</' . $heading_selector . '>';
			}

			if ($text) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $text;
				$output .= '</div>';
			}
			$output .= ($title_link) ? '<div class="' . $btn_margin_top . '"><a class="' . $button_style_cls . $button_size . '" href="' . $title_link . '"' . $link_target . $check_render_link . '>' . $button_title . '</a></div>' : '';
		}

		if (($positions == 'top') || ($positions == 'bottom')) {

			$output .= '<div class="tm-slideshow' . $box_shadow . $image_margin_top . '" uk-slideshow="' . $min_height . $max_height . $velocity_init . $slideshow_transition_cls . $autoplay . '">';

			$output .= ($slidenav_on_hover) ? '<div class="uk-position-relative uk-visible-toggle" tabindex="-1">' : '<div class="uk-position-relative">';

			$output .= '<ul class="uk-slideshow-items">';
			if (isset ( $settings->ui_slideshow_items ) && count ( ( array ) $settings->ui_slideshow_items )) {
				foreach ( $settings->ui_slideshow_items as $key => $value ) {
					$media_item = (isset ( $value->media_item ) && $value->media_item) ? $value->media_item : '';
					$image_src = isset ( $media_item->src ) ? $media_item->src : $media_item;
					if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
						$image_src = $image_src;
					} elseif ($image_src) {
						$image_src = Uri::base ( true ) . '/' . $image_src;
					}

					$image_alt = (isset ( $value->image_alt ) && $value->image_alt) ? $value->image_alt : '';
					$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';
					$image_alt_init = '';
					if (empty ( $image_alt )) {
						$image_alt_init .= 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"';
					} else {
						$image_alt_init .= 'alt="' . str_replace ( '"', '', $image_alt ) . '"';
					}

					$output .= '<li class="el-item item-' . $key . '">';

					$output .= ($kenburns_transition != '') ? '<div class="uk-position-cover uk-animation-kenburns uk-animation-reverse' . $kenburns_transition . '"' . $kenburns_duration . '>' : '';

					$output .= '<img class="ui-image" src="' . $image_src . '" ' . $image_alt_init . ' uk-cover>';

					$output .= ($kenburns_transition != '') ? '</div>' : '';

					$output .= '</li>';
				}
			}
			$output .= '</ul>';

			if ($slidenav_position == 'default') {
				$output .= ($slidenav_on_hover) ? '<div class="uk-hidden-hover uk-hidden-touch' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">' : '<div class="tm-sidenav' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">';
				$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-left" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>';
				$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-right" href="#" uk-slidenav-next uk-slideshow-item="next"></a>';
				$output .= '</div> ';
			} elseif ($slidenav_position == 'outside') {
				$output .= ($slidenav_on_hover) ? '<div class="tm-outsite uk-hidden-hover uk-hidden-touch' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">' : '<div class="tm-outsite' . $slidenav_breakpoint_cls . $slidenav_outside_color . '">';
				$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-left-out" href="#" uk-slidenav-previous uk-slideshow-item="previous" uk-toggle="cls: uk-position-center-left-out uk-position-center-left; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
				$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-right-out" href="#" uk-slidenav-next uk-slideshow-item="next" uk-toggle="cls: uk-position-center-right-out uk-position-center-right; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
				$output .= '</div> ';
			} elseif ($slidenav_position != '') {
				$output .= ($slidenav_on_hover) ? '<div class="uk-slidenav-container uk-hidden-hover uk-hidden-touch' . $slidenav_position_cls . $slidenav_margin . $slidenav_breakpoint_cls . $slidenav_outside_color . '">' : '<div class="uk-slidenav-container' . $slidenav_position_cls . $slidenav_margin . $slidenav_breakpoint_cls . $slidenav_outside_color . '">';
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
					$output .= '<div class="ui-nav-control' . $navigation_margin . $navigation . $navigation_breakpoint_cls . $navigation_color . '"> ';
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

						$image_alt_init = '';
						if (empty ( $image_alt )) {
							$image_alt_init .= 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"';
						} else {
							$image_alt_init .= 'alt="' . str_replace ( '"', '', $image_alt ) . '"';
						}
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
		}

		if ($positions == 'top') {
			if ($card_title) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . '">';
				if ($title_decoration == ' uk-heading-line') {
					$output .= '<span>';
					$output .= $card_title;
					$output .= '</span>';
				} else {
					$output .= $card_title;
				}
				$output .= '</' . $heading_selector . '>';
			}

			if ($text) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $text;
				$output .= '</div>';
			}
			$output .= ($title_link) ? '<div class="' . $btn_margin_top . '"><a class="' . $button_style_cls . $button_size . '" href="' . $title_link . '"' . $link_target . $check_render_link . '>' . $button_title . '</a></div>' : '';
		}

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
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$button_style = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? $settings->link_button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$css = '';
		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
		}

		if ($content_color) {
			$css .= $addon_id . ' .ui-content {' . $content_color . '}';
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
