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
class JpagebuilderAddonCardslider extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		// $image_padding = ( isset( $settings->image_padding ) && $settings->image_padding ) ? 1 : 0;

		$positions = (isset ( $settings->card_alignment ) && $settings->card_alignment) ? $settings->card_alignment : '';

		// Alignment and Margin for left/right.

		$grid_cls = (isset ( $settings->grid_width ) && $settings->grid_width) ? 'uk-width-' . $settings->grid_width : '';
		$grid_cls_bp = (isset ( $settings->grid_breakpoint ) && $settings->grid_breakpoint) ? '@' . $settings->grid_breakpoint : '';
		$cls_class = ($positions == 'right') ? ' uk-flex-last' . $grid_cls_bp . '' : '';

		$img_class = ($positions == 'left' || $positions == 'right') ? 'uk-card-media-' . $positions . '' : '';

		$vertical_alignment = (isset ( $settings->vertical_alignment ) && $settings->vertical_alignment) ? 1 : 0;
		$vertical_alignment_cls = ($vertical_alignment) ? ' uk-flex-middle' : '';

		$image_grid_column_gap = (isset ( $settings->image_grid_column_gap ) && $settings->image_grid_column_gap) ? $settings->image_grid_column_gap : '';
		$image_grid_row_gap = (isset ( $settings->image_grid_row_gap ) && $settings->image_grid_row_gap) ? $settings->image_grid_row_gap : '';

		$image_grid_cr_gap = '';
		if ($image_grid_column_gap == $image_grid_row_gap) {
			$image_grid_cr_gap .= (! empty ( $image_grid_column_gap ) && ! empty ( $image_grid_row_gap )) ? ' uk-grid-' . $image_grid_column_gap : '';
		} else {
			$image_grid_cr_gap .= ! empty ( $image_grid_column_gap ) ? ' uk-grid-column-' . $image_grid_column_gap : '';
			$image_grid_cr_gap .= ! empty ( $image_grid_row_gap ) ? ' uk-grid-row-' . $image_grid_row_gap : '';
		}

		$velocity = (isset ( $settings->velocity ) && $settings->velocity) ? (( int ) $settings->velocity / 100) : '';
		$velocity_init = (! empty ( $velocity )) ? 'velocity: ' . $velocity . ';' : '';
		$finite_slide = (isset ( $settings->finite_slide ) && $settings->finite_slide) ? 'finite: 1;' : '';

		$grid = (isset ( $settings->cs_phone_portrait ) && $settings->cs_phone_portrait) ? ' uk-width-' . $settings->cs_phone_portrait : '';
		$grid .= (isset ( $settings->cs_phone_landscape ) && $settings->cs_phone_landscape) ? ' uk-width-' . $settings->cs_phone_landscape . '@s' : '';
		$grid .= (isset ( $settings->cs_tablet_landscape ) && $settings->cs_tablet_landscape) ? ' uk-width-' . $settings->cs_tablet_landscape . '@m' : '';
		$grid .= (isset ( $settings->cs_desktop ) && $settings->cs_desktop) ? ' uk-width-' . $settings->cs_desktop . '@l' : '';
		$grid .= (isset ( $settings->cs_large_screens ) && $settings->cs_large_screens) ? ' uk-width-' . $settings->cs_large_screens . '@xl' : '';

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

		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';
		$grid_gutter = ($grid_column_gap) ? ' uk-grid-column-' . $grid_column_gap : '';
		$divider = ($grid_column_gap != 'collapse') ? (isset ( $settings->grid_divider ) && $settings->grid_divider) ? 1 : 0 : '';
		$grid_divider = ($divider) ? ' uk-grid-divider' : '';

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

		$heading_style_cls = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style_cls_init = (empty ( $heading_style_cls )) ? ' uk-card-title' : '';

		// Meta.
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

		// Content.
		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_dropcap = (isset ( $settings->content_dropcap ) && $settings->content_dropcap) ? 1 : 0;
		$content_style .= ($content_dropcap) ? ' uk-dropcap' : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';
		$content_column = (isset ( $settings->content_column ) && $settings->content_column) ? ' uk-column-' . $settings->content_column : '';
		$content_column_breakpoint = ($content_column) ? ((isset ( $settings->content_column_breakpoint ) && $settings->content_column_breakpoint) ? '@' . $settings->content_column_breakpoint : '') : '';
		$content_column_divider = ($content_column) ? ((isset ( $settings->content_column_divider ) && $settings->content_column_divider) ? ' uk-column-divider' : false) : '';

		$content_style .= $content_column . $content_column_breakpoint . $content_column_divider;

		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$attribs = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? ' target="' . $settings->link_new_tab . '"' : '';
		$btn_styles = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? $settings->link_button_style : '';
		$link_button_size = (isset ( $settings->link_button_size ) && $settings->link_button_size) ? ' ' . $settings->link_button_size : '';

		$button_style_cls = '';
		if ($btn_styles == 'primary' || $btn_styles == 'secondary' || $btn_styles == 'danger' || $btn_styles == 'text' || $btn_styles == 'custom') {
			$button_style_cls .= 'uk-button uk-button-' . $btn_styles . '';
		} elseif ($btn_styles == 'link' || $btn_styles == 'link-muted' || $btn_styles == 'link-text') {
			$button_style_cls .= 'uk-' . $btn_styles . '';
		} else {
			$button_style_cls .= 'uk-button uk-button-default';
		}

		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';
		$all_button_title = (isset ( $settings->all_button_title ) && $settings->all_button_title) ? $settings->all_button_title : 'Learn more';

		$image_margin_top = (isset ( $settings->image_margin_top ) && $settings->image_margin_top) ? ' uk-margin-' . $settings->image_margin_top . '-top' : ' uk-margin-top';

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
		$animation_delay = ($delay_element_animations) ? ' delay: 200' : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="' . $scrollspy_target . 'cls: uk-animation-' . $animation . ';' . $animation_repeat . $animation_delay . '"';
		}

		$card = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_width = (isset ( $settings->card_width ) && $settings->card_width) ? ' uk-margin-auto uk-width-' . $settings->card_width : '';
		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? ' ' . $settings->card_size : '';

		$panel_image_padding = (isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0;
		$image_padding = ($card && $positions != 'between') ? ((isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0) : '';

		$panel_content_padding = (isset ( $settings->card_content_padding ) && $settings->card_content_padding) ? $settings->card_content_padding : '';

		$card_content_padding = ($panel_content_padding && empty ( $card )) ? 'uk-padding' . (($panel_content_padding == 'default') ? ' ' : '-' . $panel_content_padding . ' ') : '';

		$title_align = (isset ( $settings->title_align ) && $settings->title_align) ? $settings->title_align : '';
		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$link_title_hover = (isset ( $settings->title_hover_style ) && $settings->title_hover_style) ? ' class="uk-link-' . $settings->title_hover_style . '"' : '';
		$panel_link = (isset ( $settings->panel_link ) && $settings->panel_link) ? 1 : 0;

		$title_grid_width = (isset ( $settings->title_grid_width ) && $settings->title_grid_width) ? 'uk-width-' . $settings->title_grid_width : '';
		$title_breakpoint = (isset ( $settings->title_breakpoint ) && $settings->title_breakpoint) ? '@' . $settings->title_breakpoint : '';

		$title_grid_column_gap = (isset ( $settings->title_grid_column_gap ) && $settings->title_grid_column_gap) ? $settings->title_grid_column_gap : '';
		$title_grid_row_gap = (isset ( $settings->title_grid_row_gap ) && $settings->title_grid_row_gap) ? $settings->title_grid_row_gap : '';

		$title_grid_cr = '';
		if ($title_grid_column_gap == $title_grid_row_gap) {
			$title_grid_cr .= (! empty ( $title_grid_column_gap ) && ! empty ( $title_grid_row_gap )) ? ' uk-grid-' . $title_grid_column_gap : '';
		} else {
			$title_grid_cr .= ! empty ( $title_grid_column_gap ) ? ' uk-grid-column-' . $title_grid_column_gap : '';
			$title_grid_cr .= ! empty ( $title_grid_row_gap ) ? ' uk-grid-row-' . $title_grid_row_gap : '';
		}

		$image_link = (isset ( $settings->image_link ) && $settings->image_link) ? 1 : 0;
		$image_border = (! empty ( $card ) && $image_padding) ? false : ((isset ( $settings->image_border ) && $settings->image_border) ? ' ' . $settings->image_border : '');
		$image_box_shadow = (! empty ( $card )) ? false : ((isset ( $settings->image_box_shadow ) && $settings->image_box_shadow) ? ' uk-box-shadow-' . $settings->image_box_shadow : '');
		$image_transition = ($image_link || $panel_link) ? ((isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . ' uk-transition-opaque' : '') : false;

		$image_hover_box_shadow = (($image_link || $panel_link) && empty ( $card )) ? ((isset ( $settings->image_hover_box_shadow ) && $settings->image_hover_box_shadow) ? ' uk-box-shadow-hover-' . $settings->image_hover_box_shadow : '') : false;

		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';
		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;
		$cover_init = (! empty ( $card ) && $image_padding) ? ' uk-cover' : '';
		$link_cover = ($image_padding && empty ( $vertical_alignment )) ? ' class="uk-position-cover"' : '';

		$toggle_transition = ($panel_link) ? ' uk-transition-toggle' : '';
		$link_transition = ($panel_link && $title_link) ? ' uk-display-block uk-link-toggle' : '';

		$panel_cls = ($card) ? 'uk-card uk-card-' . $card . $card_size . $card_width : 'uk-panel' . $card_width;
		$panel_cls .= ($card && $card != 'hover' && $panel_link) ? ' uk-card-hover' : '';
		$panel_cls .= (($card && $panel_image_padding == false) || ($card && $positions == 'between' && $panel_image_padding)) ? ' uk-card-body uk-margin-remove-first-child' : '';
		$panel_cls .= (empty ( $card ) && empty ( $panel_content_padding )) ? ' uk-margin-remove-first-child' : '';

		$output = '';

		$output .= '<div class="ui-slider' . $text_alignment . $general . $max_width_cfg . '"' . $animation . ' uk-slider="' . $autoplay . $slide_sets . $center_items . $finite_slide . $velocity_init . '">';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div class="ui-slider-wrapper">';
		$output .= ($slidenav_on_hover) ? '<div class="uk-position-relative uk-visible-toggle" tabindex="-1">' : '<div class="uk-position-relative">';

		$output .= ($slidenav_position == 'outside') ? '<div class="uk-slider-container">' : '';

		$output .= '<ul class="uk-slider-items uk-grid-match uk-grid' . $grid_gutter . $grid_divider . '">';

		if (isset ( $settings->ui_cs_item ) && count ( ( array ) $settings->ui_cs_item )) {
			foreach ( $settings->ui_cs_item as $key => $value ) {
				$image = (isset ( $value->image ) && $value->image) ? $value->image : '';
				$image_src = isset ( $image->src ) ? $image->src : $image;
				if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
					$image_src = $image_src;
				} elseif ($image_src) {
					$image_src = Uri::base ( true ) . '/' . $image_src;
				}

				$card_meta = (isset ( $value->meta ) && $value->meta) ? $value->meta : '';
				$card_content = (isset ( $value->card_content ) && $value->card_content) ? $value->card_content : '';
				$card_title = (isset ( $value->title ) && $value->title) ? $value->title : '';

				$button_title = (isset ( $value->button_title ) && $value->button_title) ? $value->button_title : '';

				if (empty ( $button_title )) {
					$button_title .= $all_button_title;
				}

				$title_link = (isset ( $value->title_link ) && $value->title_link) ? $value->title_link : '';

				$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';

				$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

				$image_alt = (isset ( $value->image->alt ) && $value->image->alt) ? $value->image->alt : '';
				$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';
				$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';

				$output .= '<li class="ui-item' . $grid . '">';

				if ($panel_link && $title_link) {
					$output .= '<a class="' . $panel_cls . $link_transition . $toggle_transition . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . $scrollspy_cls . '>';
				} else {
					$output .= '<div class="' . $panel_cls . '"' . $scrollspy_cls . '>';
				}

				if (($positions == 'left') || ($positions == 'right')) {

					if (! empty ( $card )) {
						$output .= ($image_padding) ? '<div class="uk-child-width-expand uk-grid-collapse uk-grid-match' . $vertical_alignment_cls . '" uk-grid>' : '<div class="uk-child-width-expand' . $image_grid_cr_gap . $vertical_alignment_cls . '" uk-grid>';
					} else {
						$output .= '<div class="uk-child-width-expand' . $image_grid_cr_gap . $vertical_alignment_cls . '" uk-grid>';
					}

					$output .= '<div class="' . $grid_cls . $grid_cls_bp . $cls_class . '">';

					$output .= ($image_padding) ? '<div class="' . $img_class . ' uk-cover-container">' : '';

					if ($image_src) {

						if ($image_link && $title_link && $panel_link == false) {
							$output .= '<a href="' . $title_link . '"' . $attribs . $link_cover . $render_linkscroll . '>';
							$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle' . $image_border . $image_box_shadow . $image_hover_box_shadow . '">' : '';
						}

						$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '<div class="uk-inline-clip' . $image_border . $image_box_shadow . '">' : '';

						$output .= '<img class="ui-img' . $image_svg_color . ($image_link || $panel_link ? $image_transition : $image_border . $image_box_shadow) . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . $cover_init . '>';
						$output .= ($image_padding && ! empty ( $card )) ? '<img class="uk-invisible uk-display-inline-block' . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>' : '';

						$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '</div>' : '';

						if ($image_link && $title_link && $panel_link == false) {
							$output .= ($image_transition) ? '</div>' : '';
							$output .= '</a>';
						}
					}

					$output .= ($image_padding) ? '</div>' : '';

					$output .= '</div>';
				}
				// end grid 1st colum.

				if ($positions == 'top' && $image_src) {

					$output .= ($image_padding) ? '<div class="uk-card-media-top">' : '';

					if ($image_link && $title_link && $panel_link == false) {
						$output .= '<a href="' . $title_link . '"' . $attribs . $render_linkscroll . '>';
						$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle' . $image_border . $image_box_shadow . $image_hover_box_shadow . '">' : '';
					}

					$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '<div class="uk-inline-clip' . $image_border . $image_box_shadow . '">' : '';
					$output .= '<img class="ui-img' . $image_svg_color . ($image_link || $panel_link ? $image_transition : $image_border . $image_box_shadow) . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
					$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '</div>' : '';

					if ($image_link && $title_link && $panel_link == false) {
						$output .= ($image_transition) ? '</div>' : '';
						$output .= '</a>';
					}

					$output .= ($image_padding) ? '</div>' : '';
				}

				if (($positions == 'left') || ($positions == 'right')) {
					$output .= empty ( $card ) && ! empty ( $card_content_padding ) || $card && $image_padding ? '<div>' : '';
					$output .= ($image_padding) ? '<div class="uk-card-body uk-margin-remove-first-child">' : '<div class="' . $card_content_padding . 'uk-margin-remove-first-child">';
				} else {
					$output .= ($image_padding) ? '<div class="uk-card-body uk-margin-remove-first-child">' : '';
					$output .= ($card_content_padding) ? '<div class="' . $card_content_padding . 'uk-margin-remove-first-child">' : '';
				}

				if ($title_align == 'left') {

					$output .= '<div class="uk-child-width-expand uk-margin-top' . $title_grid_cr . '" uk-grid>';
					$output .= '<div class="' . $title_grid_width . $title_breakpoint . ' uk-margin-remove-first-child">';
				}

				if ($meta_alignment == 'top' && $card_meta) {
					$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
					$output .= $card_meta;
					$output .= '</' . $meta_element . '>';
				}

				if ($card_title) {
					$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $heading_style_cls_init . $title_decoration . '">';
					$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';

					if ($link_title && $title_link && $panel_link == false) {
						$output .= '<a' . $link_title_hover . ' href="' . $title_link . '"' . $attribs . $render_linkscroll . '>';
					}
					$output .= $card_title;
					if ($link_title && $title_link && $panel_link == false) {
						$output .= '</a>';
					}

					$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
					$output .= '</' . $heading_selector . '>';
				}

				if (empty ( $meta_alignment ) && $card_meta) {
					$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
					$output .= $card_meta;
					$output .= '</' . $meta_element . '>';
				}

				if ($title_align == 'left') {
					$output .= '</div>  ';
					$output .= '<div class="uk-margin-remove-first-child">';
				}

				if ($positions == 'between' && $image_src) {

					if ($image_link && $title_link && $panel_link == false) {
						$output .= '<a href="' . $title_link . '"' . $attribs . $render_linkscroll . '>';
						$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle' . $image_border . $image_box_shadow . $image_hover_box_shadow . $image_margin_top . '">' : '';
					}
					$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '<div class="uk-inline-clip' . $image_border . $image_box_shadow . $image_margin_top . '">' : '';
					$output .= '<img class="ui-img' . ($image_transition ? '' : $image_margin_top) . $image_svg_color . ($image_link || $panel_link ? $image_transition : $image_border . $image_box_shadow) . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
					$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '</div>' : '';
					if ($image_link && $title_link && $panel_link == false) {
						$output .= ($image_transition) ? '</div>' : '';
						$output .= '</a>';
					}
				}

				if ($meta_alignment == 'above' && $card_meta) {
					$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
					$output .= $card_meta;
					$output .= '</' . $meta_element . '>';
				}

				if ($card_content) {
					$output .= '<div class="ui-content uk-panel' . $content_style . '">';
					$output .= $card_content;
					$output .= '</div>';
				}

				if ($meta_alignment == 'content' && $card_meta) {
					$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
					$output .= $card_meta;
					$output .= '</' . $meta_element . '>';
				}

				if ($button_title && $title_link) {
					$output .= '<div class="' . $btn_margin_top . '">';
					if ($panel_link) {
						$output .= '<div class="' . $button_style_cls . '">' . $button_title . '</div>';
					} else {
						$output .= '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a>';
					}
					$output .= '</div>';
				}

				if ($title_align == 'left') {
					$output .= '</div>';
					$output .= '</div>';
				}

				if (($positions == 'left') || ($positions == 'right')) {
					$output .= '</div>';
					$output .= empty ( $card ) && ! empty ( $card_content_padding ) || $card && $image_padding ? '</div>' : '';
				} else {
					$output .= ($image_padding) ? '</div>' : '';
					$output .= ($card_content_padding) ? '</div>' : '';
				}

				if ($positions == 'bottom' && $image_src) {

					$output .= ($image_padding) ? '<div class="uk-card-media-bottom">' : '';

					if ($image_link && $title_link && $panel_link == false) {
						$output .= '<a href="' . $title_link . '"' . $attribs . $render_linkscroll . '>';
						$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle' . $image_border . $image_box_shadow . $image_hover_box_shadow . $image_margin_top . '">' : '';
					}

					$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '<div class="uk-inline-clip' . $image_border . $image_box_shadow . $image_margin_top . '">' : '';
					$output .= '<img class="ui-img' . ($image_transition || $image_padding ? '' : $image_margin_top) . $image_svg_color . ($image_link || $panel_link ? $image_transition : $image_border . $image_box_shadow) . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
					$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '</div>' : '';

					if ($image_link && $title_link && $panel_link == false) {
						$output .= ($image_transition) ? '</div>' : '';
						$output .= '</a>';
					}

					$output .= ($image_padding) ? '</div>' : '';
				}

				if ($panel_link && $title_link) {
					$output .= '</a>';
				} else {
					$output .= '</div>';
				}

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

		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';

		$link_button_style = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? $settings->link_button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$card_style = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_background = (isset ( $settings->card_background ) && $settings->card_background) ? 'background-color: ' . $settings->card_background . ';' : '';

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
		if ($card_style == 'custom' && $card_background) {
			$css .= $addon_id . ' .uk-card-custom {' . $card_background . '}';
		}
		if ($link_button_style == 'custom') {
			if ($button_background || $button_color) {
				$css .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
			}
			if ($button_background_hover || $button_hover_color) {
				$css .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active {' . $button_background_hover . $button_hover_color . '}';
			}
		}
		return $css;
	}
}
