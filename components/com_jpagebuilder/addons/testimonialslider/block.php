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
use Joomla\CMS\HTML\HTMLHelper;
class JpagebuilderAddonTestimonialSlider extends JpagebuilderAddons {
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

		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$autoplay = (isset ( $settings->autoplay ) && $settings->autoplay) ? 'autoplay: 1; ' : '';
		$pause = ($autoplay) ? ((isset ( $settings->pause ) && $settings->pause) ? '' : ' pauseOnHover: false;') : '';
		$interval = ($autoplay) ? ((isset ( $settings->autoplay_interval ) && $settings->autoplay_interval) ? 'autoplayInterval: ' . (( int ) $settings->autoplay_interval * 1000) . ';' : '') : '';
		$autoplay .= $interval . $pause;

		$slide_sets = (isset ( $settings->slidesets ) && $settings->slidesets) ? 'sets: 1; ' : '';
		$center_items = (isset ( $settings->center_slide ) && $settings->center_slide) ? 'center: 1;' : '';

		$velocity = (isset ( $settings->velocity ) && $settings->velocity) ? (( int ) $settings->velocity / 100) : '';
		$velocity_init = (! empty ( $velocity )) ? 'velocity: ' . $velocity . ';' : '';
		$finite_slide = (isset ( $settings->finite_slide ) && $settings->finite_slide) ? 'finite: 1;' : '';

		// Navigation settings.
		$navigation_control = (isset ( $settings->navigation ) && $settings->navigation) ? $settings->navigation : '';
		$navigation_inverse = (isset ( $settings->navigation_color ) && $settings->navigation_color) ? $settings->navigation_color : '';
		$navigation_breakpoint = (isset ( $settings->navigation_breakpoint ) && $settings->navigation_breakpoint) ? $settings->navigation_breakpoint : '';
		$navigation_breakpoint_cls = ($navigation_breakpoint) ? ' uk-visible@' . $navigation_breakpoint . '' : '';

		$navigation = (isset ( $settings->navigation_margin ) && $settings->navigation_margin) ? ' ' . $settings->navigation_margin : '';
		$navigation .= (isset ( $settings->navigation_position ) && $settings->navigation_position) ? ' uk-flex-' . $settings->navigation_position : '';
		$larger_style = (isset ( $settings->larger_style ) && $settings->larger_style) ? $settings->larger_style : '';
		$larger_style_init = ($larger_style) ? ' uk-slidenav-large' : '';

		$slidenav_on_hover = (isset ( $settings->slidenav_on_hover ) && $settings->slidenav_on_hover) ? 1 : 0;

		// Sidenav Settings.
		$slidenav_position = (isset ( $settings->slidenav_position ) && $settings->slidenav_position) ? $settings->slidenav_position : '';
		$slidenav_position_cls = (! empty ( $slidenav_position ) || ($slidenav_position != 'default')) ? ' uk-position-' . $slidenav_position . '' : '';

		$slidenav_margin = (isset ( $settings->slidenav_margin ) && $settings->slidenav_margin) ? ' uk-position-' . $settings->slidenav_margin : '';

		$slidenav_breakpoint = (isset ( $settings->slidenav_breakpoint ) && $settings->slidenav_breakpoint) ? $settings->slidenav_breakpoint : '';
		$slidenav_breakpoint_cls = ($slidenav_breakpoint) ? ' uk-visible@' . $slidenav_breakpoint . '' : '';

		$slidenav_color = (isset ( $settings->slidenav_color ) && $settings->slidenav_color) ? $settings->slidenav_color : '';
		$slidenav_color_cls = ($slidenav_color) ? ' uk-' . $slidenav_color . '' : '';

		$slidenav_outside_breakpoint = (isset ( $settings->slidenav_outside_breakpoint ) && $settings->slidenav_outside_breakpoint) ? ' @' . $settings->slidenav_outside_breakpoint : 'xl';
		$slidenav_outside_color = (isset ( $settings->slidenav_outside_color ) && $settings->slidenav_outside_color) ? $settings->slidenav_outside_color : '';
		$slidenav_outside_color_cls = ($slidenav_outside_color) ? ' uk-' . $slidenav_outside_color . '' : '';

		$avatar_shape = (isset ( $settings->avatar_shape ) && $settings->avatar_shape) ? ' ' . $settings->avatar_shape : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->text_alignment ) && $settings->text_alignment) ? ' uk-text-' . $settings->text_alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$head_alignment = (isset ( $settings->text_alignment ) && $settings->text_alignment) ? ' uk-flex-' . $settings->text_alignment : '';
		$head_breakpoint = ($head_alignment) ? ((isset ( $settings->head_breakpoint ) && $settings->head_breakpoint) ? '@' . $settings->head_breakpoint : '') : '';
		$head_alignment_fallback = ($head_alignment && $head_breakpoint) ? ((isset ( $settings->head_alignment_fallback ) && $settings->head_alignment_fallback) ? ' uk-flex-' . $settings->head_alignment_fallback : '') : '';
		$head_alignment .= $head_breakpoint . $head_alignment_fallback;

		$vertical_alignment = (isset ( $settings->vertical_alignment ) && $settings->vertical_alignment) ? 1 : 0;

		$vertical_alignment_cls = ($vertical_alignment) ? ' uk-flex-middle' : '';

		$image_grid_column_gap = (isset ( $settings->image_grid_column_gap ) && $settings->image_grid_column_gap) ? ' uk-grid-column-' . $settings->image_grid_column_gap : '';

		$header_alignment = (isset ( $settings->header_alignment ) && $settings->header_alignment) ? $settings->header_alignment : '';

		$grid_gutter = '';
		$divider = (isset ( $settings->divider ) && $settings->divider) ? 1 : 0;
		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';

		$grid_gutter .= $grid_column_gap ? ' uk-grid-column-' . $grid_column_gap : '';
		$grid_gutter .= ($grid_column_gap != 'collapse' && $divider) ? ' uk-grid-divider' : '';

		$grid_ts = (isset ( $settings->ts_phone_portrait ) && $settings->ts_phone_portrait) ? ' uk-width-' . $settings->ts_phone_portrait : '';
		$grid_ts .= (isset ( $settings->ts_phone_landscape ) && $settings->ts_phone_landscape) ? ' uk-width-' . $settings->ts_phone_landscape . '@s' : '';
		$grid_ts .= (isset ( $settings->ts_tablet_landscape ) && $settings->ts_tablet_landscape) ? ' uk-width-' . $settings->ts_tablet_landscape . '@m' : '';
		$grid_ts .= (isset ( $settings->ts_desktop ) && $settings->ts_desktop) ? ' uk-width-' . $settings->ts_desktop . '@l' : '';
		$grid_ts .= (isset ( $settings->ts_large_screens ) && $settings->ts_large_screens) ? ' uk-width-' . $settings->ts_large_screens . '@xl' : '';

		$link_target = (isset ( $settings->link_target ) && $settings->link_target) ? ' target="' . $settings->link_target . '"' : '';

		$card = (isset ( $settings->card_styles ) && $settings->card_styles) ? $settings->card_styles : '';
		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? ' ' . $settings->card_size : '';
		$panel_content_padding = (isset ( $settings->card_content_padding ) && $settings->card_content_padding) ? $settings->card_content_padding : '';

		$card_content_padding = ($panel_content_padding && empty ( $card )) ? 'uk-padding' . (($panel_content_padding == 'default') ? ' uk-margin-remove-first-child' : '-' . $panel_content_padding . ' uk-margin-remove-first-child') : '';

		$heading_style = (isset ( $settings->title_style ) && $settings->title_style) ? ' uk-' . $settings->title_style : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_text_color ) && $settings->title_text_color) ? ' uk-text-' . $settings->title_text_color : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';

		$heading_style_cls = (isset ( $settings->title_style ) && $settings->title_style) ? ' uk-' . $settings->title_style : '';
		$heading_style_cls_init = (empty ( $heading_style_cls )) ? ' uk-card-title' : '';

		$meta_element = (isset ( $settings->meta_element ) && $settings->meta_element) ? $settings->meta_element : 'div';
		$meta_style_cls = (isset ( $settings->meta_style ) && $settings->meta_style) ? $settings->meta_style : '';

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->meta_text_color ) && $settings->meta_text_color) ? ' uk-text-' . $settings->meta_text_color : '';
		$meta_style .= (isset ( $settings->meta_text_transform ) && $settings->meta_text_transform) ? ' uk-text-' . $settings->meta_text_transform : '';
		$meta_style .= (isset ( $settings->meta_margin_top ) && $settings->meta_margin_top) ? ' uk-margin-' . $settings->meta_margin_top . '-top' : ' uk-margin-top';

		// Remove margin for heading element
		if ($meta_element != 'div' || ($meta_style_cls && $meta_style_cls != 'text-meta')) {
			$meta_style .= ' uk-margin-remove-bottom';
		}

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_dropcap = (isset ( $settings->content_dropcap ) && $settings->content_dropcap) ? 1 : 0;
		$content_style .= ($content_dropcap) ? ' uk-dropcap' : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';
		$content_column = (isset ( $settings->content_column ) && $settings->content_column) ? ' uk-column-' . $settings->content_column : '';
		$content_column_breakpoint = ($content_column) ? ((isset ( $settings->content_column_breakpoint ) && $settings->content_column_breakpoint) ? '@' . $settings->content_column_breakpoint : '') : '';
		$content_column_divider = ($content_column) ? ((isset ( $settings->content_column_divider ) && $settings->content_column_divider) ? ' uk-column-divider' : false) : '';

		$content_style .= $content_column . $content_column_breakpoint . $content_column_divider;
		$content_style .= empty ( $header_alignment ) ? ((isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top') : '';

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

		$header_margin_top = (isset ( $settings->header_margin_top ) && $settings->header_margin_top) ? ' uk-margin-' . $settings->header_margin_top : ' uk-margin';
		$image_position = (isset ( $settings->image_position ) && $settings->image_position) ? $settings->image_position : '';
		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';
		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;

		// New options.

		$card_width = (isset ( $settings->card_width ) && $settings->card_width) ? ' uk-margin-auto uk-width-' . $settings->card_width : '';

		$image_grid_cls = (isset ( $settings->image_grid_width ) && $settings->image_grid_width) ? 'uk-width-' . $settings->image_grid_width : '';
		$image_grid_cls_bp = (isset ( $settings->image_grid_breakpoint ) && $settings->image_grid_breakpoint) ? '@' . $settings->image_grid_breakpoint : '';
		$cls_class = ($image_position == 'right') ? ' uk-flex-last' . $image_grid_cls_bp . '' : '';

		$image_margin_top = (isset ( $settings->image_margin_top ) && $settings->image_margin_top) ? ' uk-margin-' . $settings->image_margin_top . '-top' : ' uk-margin-top';
		$rating_alignment = (isset ( $settings->rating_alignment ) && $settings->rating_alignment) ? $settings->rating_alignment : '';
		$center_cls = ($text_alignment == 'center') ? ' uk-text-center' : '';

		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';
		$image_link = (isset ( $settings->image_link ) && $settings->image_link) ? 1 : 0;
		$panel_link = (isset ( $settings->panel_link ) && $settings->panel_link) ? 1 : 0;
		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$link_title_hover = (isset ( $settings->title_hover_style ) && $settings->title_hover_style) ? ' class="uk-link-' . $settings->title_hover_style . '"' : '';

		$panel_cls = ($card) ? 'uk-card uk-card-' . $card . $card_size : 'uk-panel';
		$panel_cls .= ($card && $card != 'hover' && $panel_link) ? ' uk-card-hover' : '';
		$panel_cls .= ($card) ? ' uk-card-body uk-margin-remove-first-child' : '';

		$panel_cls .= (empty ( $card ) && empty ( $panel_content_padding )) ? ' uk-margin-remove-first-child' : '';

		$icon_rating = (isset ( $settings->icon_rating ) && $settings->icon_rating) ? $settings->icon_rating : '';

		$output = '';

		$output .= '<div class="ui-testimonial-slider' . $general . $max_width_cfg . '"' . $animation . ' uk-slider="' . $autoplay . $slide_sets . $center_items . $finite_slide . $velocity_init . '">';
		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div class="ui-slider-wrapper' . $text_alignment . '">';

		$output .= ($slidenav_on_hover) ? '<div class="uk-position-relative uk-visible-toggle" tabindex="-1">' : '<div class="uk-position-relative">';

		$output .= ($slidenav_position == 'outside') ? '<div class="uk-slider-container">' : '';

		$output .= '<ul class="uk-slider-items uk-grid uk-grid-match' . $grid_gutter . '">';

		foreach ( $settings->ui_testimonialslider_item as $key => $value ) {
			$name = (isset ( $value->title ) && $value->title) ? $value->title : '';
			$company = (isset ( $value->company ) && $value->company) ? $value->company : '';
			$message = (isset ( $value->message ) && $value->message) ? $value->message : '';
			$client_review = (isset ( $value->client_review ) && $value->client_review) ? $value->client_review : '';
			$avatar = (isset ( $value->avatar ) && $value->avatar) ? $value->avatar : '';
			$image_src = isset ( $avatar->src ) ? $avatar->src : $avatar;
			if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
				$image_src = $image_src;
			} elseif ($image_src) {
				$image_src = Uri::base ( true ) . '/' . $image_src;
			}

			$alt_text = (isset ( $value->alt_text ) && $value->alt_text) ? $value->alt_text : '';
			$image_width = (isset ( $settings->avatar_width ) && $settings->avatar_width) ? ' width="' . $settings->avatar_width . '"' : '';
			$link = (isset ( $value->link ) && $value->link) ? $value->link : '';

			$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';
			$render_linkscroll = (empty ( $check_target ) && strpos ( $link, '#' ) === 0) ? ' uk-scroll' : '';

			$client_rating = '';
			if ($client_review == 1) {
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
			} elseif ($client_review == 2) {
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
			} elseif ($client_review == 3) {
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
			} elseif ($client_review == 4) {
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="far fa-star" aria-hidden="true"></i>' : '<span class="el-icon" uk-icon="icon: star;"></span>';
			} elseif ($client_review == 5) {
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
			}

			$output .= '<li class="ui-testimonial' . $grid_ts . '">';

			$output .= ($panel_link && $link) ? '<a class="' . $panel_cls . '" href="' . $link . '"' . $link_target . $render_linkscroll . '>' : '<div class="' . $panel_cls . '">';

			$output .= ($card_content_padding) ? '<div class="' . $card_content_padding . '">' : '';

			if ($header_alignment == 'bottom' && $message) {
				$output .= '<div class="ui-content uk-panel' . $content_style . $header_margin_top . '">';
				$output .= $message;
				$output .= '</div>';
			}

			if ($image_position == 'top' && $image_src) {
				$output .= '<div class="ui-item">';
				if ($image_link && $link && $panel_link == false) {
					$output .= '<a class="uk-link-reset" href="' . $link . '"' . $link_target . $render_linkscroll . '>';
				}
				$output .= '<img' . $image_width . ' class="uk-display-inline-block' . $avatar_shape . $image_svg_color . '" src="' . $image_src . '" alt="' . str_replace ( '"', '', $alt_text ) . '"' . $image_svg_inline_cls . '>';
				if ($image_link && $link && $panel_link == false) {
					$output .= '</a>';
				}
				if (! empty ( $client_review ) && $rating_alignment == 'image') {
					$output .= '<div class="ui-review uk-margin-small-top">';
					$output .= $client_rating;
					$output .= '</div>';
				}
				$output .= '</div>';
			}

			if (empty ( $image_position ) || $image_position == 'right') {
				$output .= '<div class="uk-child-width-expand' . $head_alignment . $vertical_alignment_cls . $image_grid_column_gap . '" uk-grid>';
			}

			if ((empty ( $image_position ) || $image_position == 'right') && $image_src) {
				$output .= '<div class="ui-item ' . $image_grid_cls . $image_grid_cls_bp . $cls_class . '">';
				if ($image_link && $link && $panel_link == false) {
					$output .= '<a class="uk-link-reset" href="' . $link . '"' . $link_target . $render_linkscroll . '>';
				}
				$output .= '<img' . $image_width . ' class="uk-display-inline-block' . $avatar_shape . $image_svg_color . '" src="' . $image_src . '" alt="' . str_replace ( '"', '', $alt_text ) . '"' . $image_svg_inline_cls . '>';
				if ($image_link && $link && $panel_link == false) {
					$output .= '</a>';
				}
				if (! empty ( $client_review ) && $rating_alignment == 'image') {
					$output .= '<div class="ui-review uk-margin-small-top">';
					$output .= $client_rating;
					$output .= '</div>';
				}
				$output .= '</div>';
			}

			$output .= '<div class="ui-item' . $center_cls . '">';

			if ($name) {
				$output .= '<' . $heading_selector . ' class="ui-author uk-margin-remove-bottom' . $heading_style . $heading_style_cls_init . $font_weight . '">';
				if ($link_title && $link && $panel_link == false) {
					$output .= '<a' . $link_title_hover . ' href="' . $link . '"' . $link_target . $render_linkscroll . '>';
				}
				$output .= $name;
				if ($link_title && $link && $panel_link == false) {
					$output .= '</a>';
				}
				$output .= '</' . $heading_selector . '>';
			}

			if ($company) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $company;
				$output .= '</' . $meta_element . '>';
			}

			if (empty ( $image_position ) || $image_position == 'right') {
				$output .= '</div>';
			}

			if (! empty ( $client_review ) && $rating_alignment != 'image') {
				$output .= '<div class="ui-review uk-text-right@m">';
				$output .= $client_rating;
				$output .= '</div>';
			}

			$output .= '</div>';

			if ($image_position == 'bottom' && $image_src) {
				$output .= '<div class="ui-item">';
				if ($image_link && $link && $panel_link == false) {
					$output .= '<a class="uk-link-reset" href="' . $link . '"' . $link_target . $render_linkscroll . '>';
				}
				$output .= '<img' . $image_width . ' class="uk-display-inline-block' . $avatar_shape . $image_svg_color . $image_margin_top . '" src="' . $image_src . '" alt="' . str_replace ( '"', '', $alt_text ) . '"' . $image_svg_inline_cls . '>';
				if ($image_link && $link && $panel_link == false) {
					$output .= '</a>';
				}
				if (! empty ( $client_review ) && $rating_alignment == 'image') {
					$output .= '<div class="ui-review uk-margin-small-top">';
					$output .= $client_rating;
					$output .= '</div>';
				}
				$output .= '</div>';
			}

			if (empty ( $header_alignment ) && $message) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $message;
				$output .= '</div>';
			}

			$output .= ($card_content_padding) ? '</div>' : '';

			$output .= ($panel_link && $link) ? '</a>' : '</div>';

			$output .= '</li>';
		}
		$output .= '</ul>';

		if ($slidenav_position == 'default') {
			$output .= ($slidenav_on_hover) ? '<div class="uk-hidden-hover uk-hidden-touch' . $slidenav_breakpoint_cls . $slidenav_color_cls . '">' : '<div class="' . $slidenav_breakpoint_cls . $slidenav_color_cls . '">';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . ' uk-position-center-left' . $larger_style_init . '" href="#" uk-slidenav-previous uk-slider-item="previous"></a>';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . ' uk-position-center-right' . $larger_style_init . '" href="#" uk-slidenav-next uk-slider-item="next"></a>';
			$output .= '</div> ';
		} elseif ($slidenav_position == 'outside') {
			$output .= ($slidenav_on_hover) ? '<div class="ui-sidenav-outsite uk-hidden-hover uk-hidden-touch' . $slidenav_breakpoint_cls . $slidenav_outside_color_cls . '">' : '<div class="ui-sidenav-outsite' . $slidenav_breakpoint_cls . $slidenav_outside_color_cls . '">';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-left-out" href="#" uk-slidenav-previous uk-slider-item="previous" uk-toggle="cls: uk-position-center-left-out uk-position-center-left; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-right-out" href="#" uk-slidenav-next uk-slider-item="next" uk-toggle="cls: uk-position-center-right-out uk-position-center-right; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
			$output .= '</div> ';
		} elseif ($slidenav_position != '') {
			$output .= ($slidenav_on_hover) ? '<div class="uk-slidenav-container uk-hidden-hover uk-hidden-touch' . $slidenav_position_cls . $slidenav_margin . $slidenav_breakpoint_cls . $slidenav_color_cls . '">' : '<div class="uk-slidenav-container' . $slidenav_position_cls . $slidenav_margin . $slidenav_breakpoint_cls . $slidenav_color_cls . '">';
			$output .= '<a class="ui-slidenav' . $larger_style_init . '" href="#" uk-slidenav-previous uk-slider-item="previous"></a>';
			$output .= '<a class="ui-slidenav' . $larger_style_init . '" href="#" uk-slidenav-next uk-slider-item="next"></a>';
			$output .= '</div>';
		}

		$output .= '</div>';
		$output .= ($slidenav_position == 'outside') ? '</div>' : '';

		$output .= '</div>';
		if ($navigation_control != '') {
			$output .= ($navigation_inverse) ? '<div class="uk-' . $navigation_inverse . '">' : '';
			$output .= '<ul class="uk-slider-nav uk-dotnav' . $navigation . $navigation_breakpoint_cls . '"></ul>';
			$output .= ($navigation_inverse) ? '</div>' : '';
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
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;
		$css = '';
		$icon_style = '';
		$card_style = (isset ( $settings->card_styles ) && $settings->card_styles) ? $settings->card_styles : '';
		$card_background = (isset ( $settings->card_background ) && $settings->card_background) ? 'background-color: ' . $settings->card_background . ';' : '';
		$card_color = (isset ( $settings->card_color ) && $settings->card_color) ? 'color: ' . $settings->card_color . ';' : '';
		$title_color = (isset ( $settings->title_text_color ) && $settings->title_text_color) ? $settings->title_text_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';

		$icon_style .= (isset ( $settings->icon_color ) && $settings->icon_color) ? 'color: ' . $settings->icon_color . ';' : '';

		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-author {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$css .= $addon_id . ' .ui-meta {' . $custom_meta_color . '}';
		}
		if ($content_color) {
			$css .= $addon_id . ' .ui-content, ' . $addon_id . ' .ui-content blockquote {' . $content_color . '}';
		}

		if ($card_style == 'custom' && $card_background) {
			$css .= $addon_id . ' .uk-card-custom {' . $card_background . '}';
		}
		if ($card_style == 'custom' && $card_color) {
			$css .= $addon_id . ' .uk-card-custom.uk-card-body, ' . $addon_id . ' .uk-card-custom .ui-author, ' . $addon_id . ' .uk-card-custom .ui-meta {' . $card_color . '}';
		}
		if ($icon_style) {
			$css .= $addon_id . ' .ui-review .voted { ' . $icon_style . ' }';
		}
		return $css;
	}
}
