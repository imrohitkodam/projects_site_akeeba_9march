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
class JpagebuilderAddonCounter extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;

		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$start_number = (isset ( $settings->start_number ) && $settings->start_number) ? $settings->start_number : 0;
		$ending_number = (isset ( $settings->ending_number ) && $settings->ending_number) ? $settings->ending_number : 0;

		$animation_duration = (isset ( $settings->animation_duration ) && $settings->animation_duration) ? $settings->animation_duration : 0;
		$counter_title = (isset ( $settings->counter_title ) && $settings->counter_title) ? $settings->counter_title : '';

		$title_style = (isset ( $settings->title_style ) && $settings->title_style) ? ' uk-' . $settings->title_style : '';
		$title_style .= (isset ( $settings->title_margin ) && $settings->title_margin) ? ' ' . $settings->title_margin : '';

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->title_style ) && $settings->title_style) ? ' uk-' . $settings->title_style : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

		$number_heading_selector = (isset ( $settings->number_heading_selector ) && $settings->number_heading_selector) ? $settings->number_heading_selector : 'h3';

		$number_text_style = (isset ( $settings->number_text_style ) && $settings->number_text_style) ? ' uk-' . $settings->number_text_style : '';
		$number_margin_top = (isset ( $settings->number_margin_top ) && $settings->number_margin_top) ? 'uk-margin-' . $settings->number_margin_top . '-top' : 'uk-margin-top';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$plus_text = (isset ( $settings->plus_text ) && $settings->plus_text) ? $settings->plus_text : '';

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

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}

		$positions = (isset ( $settings->icon_alignment ) && $settings->icon_alignment) ? $settings->icon_alignment : '';

		// Alignment and Margin for left/right.

		$grid_cls = (isset ( $settings->icon_grid_width ) && $settings->icon_grid_width) ? 'uk-width-' . $settings->icon_grid_width : '';
		$grid_cls_bp = (isset ( $settings->icon_grid_breakpoint ) && $settings->icon_grid_breakpoint) ? '@' . $settings->icon_grid_breakpoint : '';
		$cls_class = ($positions == 'right') ? ' uk-flex-last' . $grid_cls_bp . '' : '';
		$img_class = ($positions == 'left' || $positions == 'right') ? ' uk-card-media-' . $positions . '' : '';

		$vertical_alignment = (isset ( $settings->icon_vertical_alignment ) && $settings->icon_vertical_alignment) ? 1 : 0;
		$vertical_alignment_cls = ($vertical_alignment) ? ' uk-flex-middle' : '';

		$image_grid_column_gap = (isset ( $settings->image_grid_column_gap ) && $settings->image_grid_column_gap) ? $settings->image_grid_column_gap : '';
		$image_grid_cr_gap = ! empty ( $image_grid_column_gap ) ? ' uk-grid-column-' . $image_grid_column_gap : '';

		$title_align = (isset ( $settings->title_align ) && $settings->title_align) ? $settings->title_align : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$icon_type = (isset ( $settings->icon_type ) && $settings->icon_type) ? $settings->icon_type : '';

		$faw_icon = ($icon_type === 'fontawesome_icon') ? ((isset ( $settings->faw_icon ) && $settings->faw_icon) ? $settings->faw_icon : '') : false;
		$uk_icon = ($icon_type === 'uikit') ? ((isset ( $settings->uikit ) && $settings->uikit) ? $settings->uikit : '') : false;
		// Fallback old icon cls
		$fb_icon = (isset ( $settings->custom_icon ) && $settings->custom_icon) ? $settings->custom_icon : '';
		$custom_icon = ($icon_type === 'custom' && $fb_icon) ? (strpos ( $fb_icon, '<' ) === 0 ? '<div class="tm-custom-icon">' . $fb_icon . '</div>' : '<div class="tm-custom-icon"><span class="' . $fb_icon . '"></span></div>') : false;

		$image_margin_top = (isset ( $settings->image_margin_top ) && $settings->image_margin_top) ? ' uk-margin-' . $settings->image_margin_top . '-top' : ' uk-margin-top';
		$icon_size = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? '; width: ' . $settings->faw_icon_size . '' : '';

		$icon_arr = array_filter ( explode ( ' ', $faw_icon ) );
		if (count ( $icon_arr ) === 1) {
			$faw_icon = 'fa ' . $faw_icon;
		}

		if ($faw_icon) {
			$icon_render = '<i class="uk-icon-link' . ($positions == 'between' || $positions == 'bottom' ? $image_margin_top : '') . ' ' . $faw_icon . '" aria-hidden="true"></i>';
		} elseif ($uk_icon) {
			$icon_render = '<span class="uk-icon-link' . ($positions == 'between' || $positions == 'bottom' ? $image_margin_top : '') . '" uk-icon="icon: ' . $uk_icon . $icon_size . '"></span>';
		} else {
			$icon_render = $custom_icon;
		}

		$output = '';

		$output .= '<div class="ui-counter-number' . $text_alignment . $general . $max_width_cfg . '"' . $animation . '>';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		if (($positions == 'left') || ($positions == 'right')) {
			$output .= '<div class="uk-child-width-expand' . $image_grid_cr_gap . $vertical_alignment_cls . '" uk-grid>';
			$output .= '<div class="' . $grid_cls . $grid_cls_bp . $cls_class . '">';

			$output .= $icon_render;

			$output .= '</div>';
			// end 1st colum.

			$output .= '<div class="uk-margin-remove-first-child">';
		}

		if ($positions == 'top' && ! empty ( $icon_type )) {
			$output .= $icon_render;
		}

		$output .= '<div class="' . $number_margin_top . '">';

		$output .= '<' . $number_heading_selector . ' class="uk-margin-remove-bottom ui-counter-' . $this->addon->id . ' ui-counter' . $number_text_style . '" data-refresh-interval="50" data-speed="' . $animation_duration . '" data-from="' . $start_number . '" data-to="' . $ending_number . '" data-refresh-interval="50"></' . $number_heading_selector . '>';

		$output .= ($plus_text) ? '<' . $number_heading_selector . ' class="extra-text' . $number_text_style . '">' . $plus_text . '</' . $number_heading_selector . '>' : '';

		$output .= '</div>';

		if ($positions == 'between' && ! empty ( $icon_type )) {
			$output .= $icon_render;
		}

		if (empty ( $title_align ) && $counter_title) {
			$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $font_weight . '">';
			$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
			$output .= $counter_title;
			$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
			$output .= '</' . $heading_selector . '>';
		}

		if (($positions == 'left') || ($positions == 'right')) {
			$output .= '</div>';

			$output .= '</div>';
		}

		if ('content' == $title_align && $counter_title) {
			$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $font_weight . '">';
			$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
			$output .= $counter_title;
			$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
			$output .= '</' . $heading_selector . '>';
		}

		if ($positions == 'bottom' && ! empty ( $icon_type )) {
			$output .= $icon_render;
		}

		$output .= '</div>';

		return $output;
	}
	public function js() {
		$timer = '.ui-counter-' . $this->addon->id;
		$js = '
		jQuery(function($){function CounterNumberChanger () {var timer = $(\'' . $timer . '\');if(timer.length) {timer.appear();timer.countTo();}}CounterNumberChanger();})';
		$js .= "\n";
		return $js;
	}
	public function scripts() {
		return array (
				'components/com_jpagebuilder/assets/js/jquery.appear.js',
				'components/com_jpagebuilder/assets/js/jquery.countTo.js'
		);
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
		$number_style = (isset ( $settings->color ) && $settings->color) ? 'color: ' . $settings->color . ';' : '';
		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? 'color: ' . $settings->title_color . ";\n" : '';
		$plus_color = (isset ( $settings->plus_color ) && $settings->plus_color) ? 'color: ' . $settings->plus_color . ";\n" : '';
		$plus_text = (isset ( $settings->plus_text ) && $settings->plus_text) ? $settings->plus_text : '';

		$icon_type = (isset ( $settings->icon_type ) && $settings->icon_type) ? $settings->icon_type : '';
		$icon_color = (isset ( $this->addon->settings->icon_color ) && $this->addon->settings->icon_color) ? $this->addon->settings->icon_color : '';
		$icon_size = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? $settings->faw_icon_size : '';
		$font_size = (isset ( $icon_size ) && $icon_size) ? 'font-size:' . $icon_size . 'px;width:' . $icon_size . 'px;height:' . $icon_size . 'px;line-height:' . $icon_size . 'px;' : '';

		$css = '';

		$style = ($icon_color) ? 'color:' . $icon_color . ';' : '';
		if ($style) {
			$css .= $addon_id . ' .uk-icon-link {';
			$css .= $style;
			$css .= '}';
		}
		if (($icon_type == 'fontawesome_icon') && $font_size) {
			$css .= $addon_id . ' .tm-custom-icon span,' . $addon_id . ' .uk-icon-link {';
			$css .= $font_size;
			$css .= '}';
		}

		if (($icon_type == 'custom') && $font_size) {
			$css .= $addon_id . ' .tm-custom-icon span,' . $addon_id . ' .tm-custom-icon i {';
			$css .= $font_size;
			$css .= '}';
		}

		if ($number_style) {
			$css .= $addon_id . ' .ui-counter-' . $this->addon->id . ', .extra-text {';
			$css .= $number_style;
			$css .= '}';
		}

		if ($title_color) {
			$css .= $addon_id . ' .ui-title {';
			$css .= $title_color;
			$css .= '}';
		}
		if ($plus_color) {
			$css .= $addon_id . ' .extra-text {';
			$css .= $plus_color;
			$css .= '}';
		}

		$css .= $addon_id . ' .ui-counter-' . $this->addon->id . ',  .extra-text {';
		$css .= 'display: inline';
		$css .= '}';

		return $css;
	}
}
