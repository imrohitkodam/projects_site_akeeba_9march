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
class JpagebuilderAddonCallToAction extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;

		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$content = (isset ( $settings->content ) && $settings->content) ? $settings->content : '';

		$positions = (isset ( $settings->button_alignment ) && $settings->button_alignment) ? $settings->button_alignment : '';

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= ($positions == 'top' || $positions == 'bottom') ? (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top' : '';
		$title_decoration = (isset ( $settings->decoration ) && $settings->decoration) ? ' uk-heading-' . $settings->decoration : '';

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_dropcap = (isset ( $settings->content_dropcap ) && $settings->content_dropcap) ? 1 : 0;
		$content_style .= ($content_dropcap) ? ' uk-dropcap' : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';
		$content_column = (isset ( $settings->content_column ) && $settings->content_column) ? ' uk-column-' . $settings->content_column : '';
		$content_column_breakpoint = ($content_column) ? ((isset ( $settings->content_column_breakpoint ) && $settings->content_column_breakpoint) ? '@' . $settings->content_column_breakpoint : '') : '';
		$content_column_divider = ($content_column) ? ((isset ( $settings->content_column_divider ) && $settings->content_column_divider) ? ' uk-column-divider' : false) : '';

		$content_style .= $content_column . $content_column_breakpoint . $content_column_divider;
		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';
		// Options.

		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';
		$title_link = (isset ( $settings->title_link ) && $settings->title_link) ? $settings->title_link : '';

		$attribs = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? ' target="' . $settings->link_new_tab . '"' : '';

		$button_style = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? $settings->link_button_style : '';
		$button_size = (isset ( $settings->link_button_size ) && $settings->link_button_size) ? ' ' . $settings->link_button_size : '';
		$button_width = (isset ( $settings->button_width ) && $settings->button_width) ? ' uk-width-1-1' : '';

		$button_style_cls = '';
		if (empty ( $button_style )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size;
		} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
			$button_style_cls .= 'uk-' . $button_style;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $button_style . $button_size;
		}

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$button_margin_top = (isset ( $settings->link_margin_top ) && $settings->link_margin_top) ? $settings->link_margin_top : '';

		if ($positions == 'top') {
			$btn_margin_top = ($button_margin_top) ? ' uk-margin-' . $settings->link_margin_top : ' uk-margin';
		} else {
			$btn_margin_top = ($button_margin_top) ? ' uk-margin-' . $settings->link_margin_top . '-top' : ' uk-margin-top';
		}

		$grid_cls = (isset ( $settings->grid_width ) && $settings->grid_width) ? 'uk-width-' . $settings->grid_width : '';
		$grid_cls_bp = (isset ( $settings->grid_breakpoint ) && $settings->grid_breakpoint) ? '@' . $settings->grid_breakpoint : '';

		$cls_class = ($positions == 'left') ? ' uk-flex-first' . $grid_cls_bp : '';
		$cls_right_class = ($positions == 'right') ? ' uk-text-right' . $grid_cls_bp : '';
		$vertical_alignment = (isset ( $settings->vertical_alignment ) && $settings->vertical_alignment) ? 1 : 0;
		$grid_gutter = (isset ( $settings->grid_gutter ) && $settings->grid_gutter) ? ' uk-grid-column-' . $settings->grid_gutter : '';

		$grid_column_gap = (isset ( $settings->grid_gutter ) && $settings->grid_gutter) ? $settings->grid_gutter : '';
		$divider = ($grid_column_gap != 'collapse') ? (isset ( $settings->grid_divider ) && $settings->grid_divider) ? 1 : 0 : '';

		$grid = '';
		$grid .= ($divider) ? ' uk-grid-divider' : '';
		$grid .= ($vertical_alignment) ? ' uk-grid-match uk-flex-middle' : '';

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

		$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';

		$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';
		$output = '';

		$output .= '<div class="ui-cta' . $text_alignment . $general . $max_width_cfg . '"' . $animation . '>';

		if (($positions == 'left') || ($positions == 'right')) {

			$output .= '<div class="uk-child-width-expand' . $grid . '" uk-grid>';

			$output .= '<div>';

			if ($title) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $font_weight . '">';
				$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
				$output .= $title;
				$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
				$output .= '</' . $heading_selector . '>';
			}

			if ($content) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $content;
				$output .= '</div>';
			}

			$output .= '</div>';

			$output .= '<div class="' . $grid_cls . $grid_cls_bp . $cls_class . $cls_right_class . '">';

			if (! empty ( $button_title )) {
				$output .= ($title_link) ? '<div class="tm-button' . ($vertical_alignment ? '' : $btn_margin_top) . '"><a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a></div>' : '';
			}

			$output .= '</div>';

			// end 1st colum.

			$output .= '</div>';
		} else {

			if ($positions == 'top') {
				if (! empty ( $button_title )) {
					$output .= ($title_link) ? '<div class="tm-button' . $btn_margin_top . '"><a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a></div>' : '';
				}
			}
			if ($title) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $font_weight . '">';
				$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
				$output .= $title;
				$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
				$output .= '</' . $heading_selector . '>';
			}

			if ($content) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $content;
				$output .= '</div>';
			}

			if ($positions == 'bottom') {
				if (! empty ( $button_title )) {
					$output .= ($title_link) ? '<div class="tm-button' . $btn_margin_top . '"><a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a></div>' : '';
				}
			}
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
		$content_colors = (isset ( $settings->content_colors ) && $settings->content_colors) ? $settings->content_colors : '';
		$custom_content_color = (isset ( $settings->custom_content_color ) && $settings->custom_content_color) ? 'color: ' . $settings->custom_content_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';

		$link_button_style = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? $settings->link_button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$css = '';
		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
		}
		if (empty ( $content_colors ) && $custom_content_color) {
			$css .= $addon_id . ' .ui-content {' . $custom_content_color . '}';
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
