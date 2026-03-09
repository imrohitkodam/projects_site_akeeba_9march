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
class JpagebuilderAddonSlider extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;

		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$slider_width = (isset ( $settings->width_mode ) && $settings->width_mode) ? $settings->width_mode : '';

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

		// Navigation settings.
		$navigation_control = (isset ( $settings->navigation ) && $settings->navigation) ? $settings->navigation : '';
		$navigation_inverse = (isset ( $settings->navigation_color ) && $settings->navigation_color) ? $settings->navigation_color : '';
		$navigation_breakpoint = (isset ( $settings->navigation_breakpoint ) && $settings->navigation_breakpoint) ? $settings->navigation_breakpoint : '';
		$navigation_breakpoint_cls = ($navigation_breakpoint) ? ' uk-visible@' . $navigation_breakpoint . '' : '';

		$navigation = (isset ( $settings->navigation_position ) && $settings->navigation_position) ? ' uk-flex-' . $settings->navigation_position : '';
		$navigation_margin = (isset ( $settings->navigation_margin ) && $settings->navigation_margin) ? ' ' . $settings->navigation_margin : ' uk-margin-top';

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

		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';
		$divider = (isset ( $settings->divider ) && $settings->divider) ? 1 : 0;

		$grid_slider = '';

		$grid_slider .= $grid_column_gap ? ' uk-grid-column-' . $grid_column_gap : '';
		$grid_slider .= ($grid_column_gap != 'collapse' && $divider) ? ' uk-grid-divider' : '';

		$grid = (isset ( $settings->phone_portrait ) && $settings->phone_portrait) ? ' uk-width-' . $settings->phone_portrait : '';
		$grid .= (isset ( $settings->phone_landscape ) && $settings->phone_landscape) ? ' uk-width-' . $settings->phone_landscape . '@s' : '';
		$grid .= (isset ( $settings->tablet_landscape ) && $settings->tablet_landscape) ? ' uk-width-' . $settings->tablet_landscape . '@m' : '';
		$grid .= (isset ( $settings->desktop ) && $settings->desktop) ? ' uk-width-' . $settings->desktop . '@l' : '';
		$grid .= (isset ( $settings->large_screens ) && $settings->large_screens) ? ' uk-width-' . $settings->large_screens . '@xl' : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

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
		$delay_element_animations = (isset ( $settings->delay_element_animations ) && $settings->delay_element_animations) ? $settings->delay_element_animations : '';
		$scrollspy_cls = ($delay_element_animations) ? ' uk-scrollspy-class' : '';
		$scrollspy_target = ($delay_element_animations) ? 'target: [uk-scrollspy-class]; ' : '';
		$animation_delay = ($delay_element_animations) ? ' delay: 200;' : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="' . $scrollspy_target . 'cls: uk-animation-' . $animation . ';' . $animation_repeat . $animation_delay . '"';
		}

		$overlay_mode = (isset ( $settings->overlay_mode ) && $settings->overlay_mode) ? $settings->overlay_mode : 'cover';

		$overlay_on_hover = (isset ( $settings->overlay_on_hover ) && $settings->overlay_on_hover) ? $settings->overlay_on_hover : 0;
		$overlay_transition_background = (isset ( $settings->overlay_transition_background ) && $settings->overlay_transition_background) ? $settings->overlay_transition_background : 0;
		$check_animate_bg = $overlay_mode == 'cover' && $overlay_on_hover && $overlay_transition_background;
		$title_transition = ($overlay_on_hover) ? ((isset ( $settings->title_transition ) && $settings->title_transition) ? ' uk-transition-' . $settings->title_transition . '' : '') : false;

		$content_transition = ($overlay_on_hover) ? ((isset ( $settings->content_transition ) && $settings->content_transition) ? ' uk-transition-' . $settings->content_transition . '' : '') : false;

		$meta_transition = ($overlay_on_hover) ? ((isset ( $settings->meta_transition ) && $settings->meta_transition) ? ' uk-transition-' . $settings->meta_transition . '' : '') : false;

		$image_transition = (isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . ' uk-transition-opaque' : '';

		// $overlay_positions = ( isset( $settings->overlay_positions ) && $settings->overlay_positions ) ? 'uk-position-' . $settings->overlay_positions : '';
		$overlay_positions = (isset ( $settings->overlay_positions ) && $settings->overlay_positions) ? $settings->overlay_positions : '';
		$overlay_transition = ($overlay_on_hover) ? ((isset ( $settings->overlay_transition ) && $settings->overlay_transition) ? ' uk-transition-' . $settings->overlay_transition : '') : false;

		$tab_transition = ($overlay_on_hover || ! empty ( $image_transition )) ? ' tabindex="0"' : '';
		$toggle_transition = ($overlay_on_hover || ! empty ( $image_transition )) ? ' uk-transition-toggle' : '';

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? ' uk-' . $settings->overlay_styles : '';

		$overlay_styles_int = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? 'uk-overlay' : 'uk-panel';

		$overlay_padding_init = '';
		$overlay_padding = (isset ( $settings->overlay_padding ) && $settings->overlay_padding) ? $settings->overlay_padding : '';

		if (empty ( $overlay_styles ) && empty ( $overlay_padding )) {
			$overlay_padding_init = ' uk-padding';
		} elseif (empty ( $overlay_styles ) && $overlay_padding == 'remove') {
			$overlay_padding_init = '';
		} elseif (! empty ( $overlay_padding )) {
			$overlay_padding_init = ' uk-padding-' . $overlay_padding;
		}

		$overlay_cover = ! empty ( $overlay_styles ) && $overlay_mode == 'cover';
		$overlay_margin = ($overlay_styles) ? ((isset ( $settings->overlay_margin ) && $settings->overlay_margin) ? ' uk-position-' . $settings->overlay_margin : '') : '';
		// Inverse text color on hover
		$inverse_text_color = ($overlay_mode == 'cover' && $overlay_on_hover && $overlay_transition_background);
		$overlay_text_color = (isset ( $settings->overlay_text_color ) && $settings->overlay_text_color) ? $settings->overlay_text_color : '';

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

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

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';
		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$min_height = (isset ( $settings->min_height ) && $settings->min_height) ? 'min-height: ' . $settings->min_height . ';' : '300';

		$velocity = (isset ( $settings->velocity ) && $settings->velocity) ? (( int ) $settings->velocity / 100) : '';
		$velocity_init = (! empty ( $velocity )) ? 'velocity: ' . $velocity . ';' : '';

		$finite_slide = (isset ( $settings->finite_slide ) && $settings->finite_slide) ? 'finite: 1;' : '';

		$height = (isset ( $settings->height ) && $settings->height) ? $settings->height : '';
		$height_cls = '';
		if ($height == 'full') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . '"';
		} elseif ($height == 'percent') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . 'offset-bottom: 20"';
		} elseif ($height == 'section') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . 'offset-bottom: !.jpb-section +"';
		}
		$grid_match = (! empty ( $height )) ? ' uk-grid-match' : '';

		// New options.

		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$link_title_hover = (isset ( $settings->title_hover_style ) && $settings->title_hover_style) ? ' class="uk-link-' . $settings->title_hover_style . '"' : '';
		$overlay_link = (isset ( $settings->overlay_link ) && $settings->overlay_link) ? 1 : 0;
		$overlay_maxwidth = (isset ( $settings->overlay_maxwidth ) && $settings->overlay_maxwidth) ? ' uk-width-' . $settings->overlay_maxwidth : '';

		$link_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? 'target="_blank"' : '';
		$button_margin = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';

		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';
		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_size = (isset ( $settings->button_size ) && $settings->button_size) ? ' uk-button-' . $settings->button_size : '';
		$button_style_cls = '';

		if (empty ( $button_style )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size;
		} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
			$button_style_cls .= 'uk-' . $button_style;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $button_style . $button_size;
		}

		$button_transition = ($overlay_on_hover) ? ((isset ( $settings->button_transition ) && $settings->button_transition) ? ' uk-transition-' . $settings->button_transition : '') : false;

		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$text_color_hover = ($overlay_on_hover) ? ((isset ( $settings->text_color_hover ) && $settings->text_color_hover) ? 1 : 0) : false;
		// Helper
		$helper_color = empty ( $overlay_styles ) || $overlay_mode == 'cover';
		$helper_toggle = $text_color_hover && $overlay_cover && $overlay_transition_background;
		$helper = $helper_color || $helper_toggle;

		$output = '';

		$output .= '<div class="ui-slider' . $general . $max_width_cfg . '"' . $animation . ' uk-slider="' . $autoplay . $slide_sets . $center_items . $finite_slide . $velocity_init . '">';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div class="ui-slider-wrapper' . $text_alignment . '">';
		$output .= ($slidenav_on_hover) ? '<div class="uk-position-relative uk-visible-toggle" tabindex="-1">' : '<div class="uk-position-relative">';

		if ($slidenav_position == 'outside') {
			$output .= '<div class="uk-slider-container">';
		}

		$output .= ($slider_width) ? '<ul class="uk-slider-items uk-grid' . $grid_match . $grid_slider . '"' . $height_cls . '>' : '<ul class="uk-slider-items uk-grid' . $grid_slider . '">';

		if (isset ( $settings->ui_slider_items ) && count ( ( array ) $settings->ui_slider_items )) {
			foreach ( $settings->ui_slider_items as $key => $value ) {
				$title = (isset ( $value->title ) && $value->title) ? $value->title : '';
				$meta = (isset ( $value->meta ) && $value->meta) ? $value->meta : '';
				$content = (isset ( $value->content ) && $value->content) ? $value->content : '';

				$image_panel = (isset ( $value->image_panel ) && $value->image_panel) ? 1 : 0;
				$media_background = ($image_panel) ? ((isset ( $value->media_background ) && $value->media_background) ? ' style="background-color: ' . $value->media_background . ';"' : '') : '';
				$media_blend_mode = ($image_panel && $media_background) ? ((isset ( $value->media_blend_mode ) && $value->media_blend_mode) ? ' uk-blend-' . $value->media_blend_mode : '') : false;
				$media_overlay = ($image_panel) ? ((isset ( $value->media_overlay ) && $value->media_overlay) ? '<div class="uk-position-cover" style="background-color: ' . $value->media_overlay . '"></div>' : '') : '';

				$media_item = (isset ( $value->media_item ) && $value->media_item) ? $value->media_item : '';
				$image_src = isset ( $media_item->src ) ? $media_item->src : $media_item;
				if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
					$image_src = $image_src;
				} elseif ($image_src) {
					$image_src = Uri::base ( true ) . '/' . $image_src;
				}

				$image_alt = (isset ( $value->image_alt ) && $value->image_alt) ? $value->image_alt : '';
				$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';

				$title_link = (isset ( $value->title_link->src ) && $value->title_link->src) ? $value->title_link->src : '';

				$image_alt_init = '';

				if (empty ( $image_alt )) {
					$image_alt_init .= 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"';
				} else {
					$image_alt_init .= 'alt="' . str_replace ( '"', '', $image_alt ) . '"';
				}

				$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';

				$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

				$item_color = (isset ( $value->item_color ) && $value->item_color) ? 'uk-' . $value->item_color : '';
				if (empty ( $item_color && $overlay_text_color )) {
					$item_color .= $overlay_text_color;
				}

				$output .= ($slider_width) ? '<li class="ui-item' . $grid . '">' : '<li class="ui-item">';

				$output .= ($helper) ? '<div' . ($helper_color && $item_color || $slider_width && ! empty ( $height ) ? ' class="' . ($slider_width && ! empty ( $height ) ? 'uk-grid-item-match' . ($item_color ? ' ' : '') : '') . $item_color . '"' : '') . ($helper_toggle ? ' uk-toggle="cls: uk-light uk-dark; mode: hover"' : '') . '>' : '';

				if ($overlay_link && $title_link) {
					$output .= '<a class="uk-cover-container' . $toggle_transition . ' uk-display-block uk-link-toggle" href="' . $title_link . '"' . $link_target . $render_linkscroll . $tab_transition . '>';
				} else {
					$output .= '<div class="uk-cover-container' . $toggle_transition . '"' . $tab_transition . '>';
				}
				$output .= ($slider_width && ! empty ( $height ) && $image_transition) ? '<div class="uk-position-cover' . $image_transition . '">' : '';

				$output .= '<img class="ui-image' . $media_blend_mode . ($slider_width && ! empty ( $height ) ? '' : $image_transition) . '" src="' . $image_src . '" ' . $image_alt_init . ($slider_width && ! empty ( $height ) ? ' uk-cover' : '') . '>';

				$output .= ($slider_width && ! empty ( $height ) && $image_transition) ? '</div>' : '';

				$output .= $media_overlay;

				if ($overlay_cover) {
					$output .= '<div class="uk-position-cover' . $overlay_margin . $overlay_styles . $overlay_transition . '"></div>';
				}

				if (in_array ( $overlay_positions, array (
						'center',
						'center-left',
						'center-right',
						'top-center',
						'bottom-center'
				) )) {
					$output .= '<div class="uk-position-' . $overlay_positions . $overlay_margin . '">';
				}

				if ($title || $meta || $content || $title_link) {

					$output .= '<div class="' . $overlay_styles_int . $overlay_padding_init . $overlay_maxwidth . (! in_array ( $overlay_positions, array (
							'center',
							'center-left',
							'center-right',
							'top-center',
							'bottom-center'
					) ) ? ' uk-position-' . $overlay_positions . $overlay_margin : '') . (empty ( $overlay_styles ) || $check_animate_bg == false ? $overlay_transition : '') . (! empty ( $overlay_styles ) && $overlay_mode == 'caption' ? $overlay_styles : '') . ' uk-margin-remove-first-child">';

					if ($meta_alignment == 'top' && $meta) {
						$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . $meta_transition . '">';
						$output .= $meta;
						$output .= '</' . $meta_element . '>';
					}

					if ($title) {
						$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $title_transition . $font_weight . '">';
						$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
						if ($link_title && $title_link && $overlay_link == false) {
							$output .= '<a' . $link_title_hover . ' href="' . $title_link . '"' . $link_target . $render_linkscroll . '>';
						}
						$output .= $title;
						if ($link_title && $title_link && $overlay_link == false) {
							$output .= '</a>';
						}
						$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
						$output .= '</' . $heading_selector . '>';
					}

					if (empty ( $meta_alignment ) && $meta) {
						$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . $meta_transition . '">';
						$output .= $meta;
						$output .= '</' . $meta_element . '>';
					}

					if ($content) {
						$output .= '<div class="ui-content uk-panel' . $content_style . $content_transition . '">';
						$output .= $content;
						$output .= '</div>';
					}

					if ($meta_alignment == 'content' && $meta) {
						$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . $meta_transition . '">';
						$output .= $meta;
						$output .= '</' . $meta_element . '>';
					}

					if ($title_link && $button_title) {
						$output .= '<div class="' . $button_margin . '">';
						if ($overlay_link) {
							$output .= '<div class="' . $button_style_cls . $button_transition . '">' . $button_title . '</div>';
						} else {
							$output .= '<a class="' . $button_style_cls . $button_transition . '" href="' . $title_link . '"' . $link_target . $render_linkscroll . '>' . $button_title . '</a>';
						}
						$output .= '</div>';
					}

					$output .= '</div>';
				}

				if (in_array ( $overlay_positions, array (
						'center',
						'center-left',
						'center-right',
						'top-center',
						'bottom-center'
				) )) {
					$output .= '</div>';
				}

				$output .= ($overlay_link && $title_link) ? '</a>' : '</div>';

				$output .= ($helper) ? '</div>' : '';
				$output .= '</li>';
			}
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

		if ($slidenav_position == 'outside') {
			$output .= '</div>';
		}

		$output .= '</div>';
		if ($navigation_control != '') {
			$output .= ($navigation_inverse) ? '<div class="uk-' . $navigation_inverse . '">' : '';
			$output .= '<ul class="uk-slider-nav uk-dotnav' . $navigation . $navigation_margin . $navigation_breakpoint_cls . '"></ul>';
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
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';
		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? $settings->overlay_styles : '';
		$overlay_background = (isset ( $settings->overlay_background ) && $settings->overlay_background) ? 'background-color: ' . $settings->overlay_background . ';' : '';

		$css = '';

		if ($overlay_styles == 'overlay-custom' && $overlay_background) {
			$css .= $addon_id . ' .uk-overlay-custom {' . $overlay_background . '}';
		}

		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$css .= $addon_id . ' .ui-meta {' . $custom_meta_color . '}';
		}
		if ($content_color) {
			$css .= $addon_id . ' .ui-content {' . $content_color . '}';
		}

		if ($button_title && $button_style == 'custom') {
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
