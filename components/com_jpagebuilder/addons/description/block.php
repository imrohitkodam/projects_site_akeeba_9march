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
class JpagebuilderAddonDescription extends JpagebuilderAddons {
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

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$meta_alignment = (isset ( $settings->meta_alignment ) && $settings->meta_alignment) ? $settings->meta_alignment : 'bottom-content';

		$layout = (isset ( $settings->layout ) && $settings->layout) ? $settings->layout : '';
		$width_cls = (isset ( $settings->width ) && $settings->width) ? $settings->width : '';

		$style = (isset ( $settings->style ) && $settings->style) ? ' uk-list-' . $settings->style : '';

		$style .= (isset ( $settings->list_marker ) && $settings->list_marker) ? ' uk-list-' . $settings->list_marker : '';
		$style .= (isset ( $settings->list_marker_color ) && $settings->list_marker_color) ? ' uk-list-' . $settings->list_marker_color : '';
		$style .= (isset ( $settings->list_size ) && $settings->list_size) ? ' uk-list-' . $settings->list_size : '';

		$list_column = (isset ( $settings->column ) && $settings->column) ? ' uk-column-' . $settings->column : '';
		$list_column_breakpoint = ($list_column) ? ((isset ( $settings->column_breakpoint ) && $settings->column_breakpoint) ? '@' . $settings->column_breakpoint : '') : '';
		$list_column_divider = ($list_column) ? ((isset ( $settings->column_divider ) && $settings->column_divider) ? ' uk-column-divider' : '') : '';

		$style .= $list_column . $list_column_breakpoint . $list_column_divider;

		$grid_cls = (isset ( $settings->width ) && $settings->width) ? ' uk-width-' . $settings->width : '';
		$grid_cls_bp = (isset ( $settings->description_breakpoint ) && $settings->description_breakpoint) ? '@' . $settings->description_breakpoint : '';

		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? ' uk-grid-column-' . $settings->grid_column_gap : '';
		$grid_row_gap = (isset ( $settings->grid_row_gap ) && $settings->grid_row_gap) ? ' uk-grid-row-' . $settings->grid_row_gap : '';
		$leader = (isset ( $settings->leader ) && $settings->leader) ? 1 : 0;

		$leader_init = ($grid_cls_bp) ? ' uk-leader="media: ' . $grid_cls_bp . '"' : ' uk-leader';

		$leader_cls = ($leader && $layout != 'grid-2' && $layout != 'stacked') ? $leader_init : '';

		// New style options.

		$title_element = (isset ( $settings->title_element ) && $settings->title_element) ? $settings->title_element : 'div';
		$title_style_cls = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-text-' . $settings->heading_style : '';
		$title_style_cls .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';

		$meta_style_cls = (isset ( $settings->meta_style ) && $settings->meta_style) ? $settings->meta_style : '';

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->meta_color ) && $settings->meta_color) ? ' uk-text-' . $settings->meta_color : '';

		// Remove margin for heading element

		if ($meta_style_cls && $meta_style_cls != 'text-meta') {
			$meta_style .= ' uk-margin-remove';
		}

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$link_style = (isset ( $settings->link_style ) && $settings->link_style) ? ' uk-link-' . $settings->link_style : '';

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

		$flex_cls = ($layout == 'grid-2-m' && $leader) ? ' uk-flex-bottom' : ' uk-flex-middle';
		$flex_init = ($layout == 'grid-2-m' && empty ( $leader )) ? ' uk-flex-middle' : '';

		$output = '';

		$output .= '<div class="ui-description' . $text_alignment . $general . $max_width_cfg . '"' . $animation . '>';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';
			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';
			$output .= nl2br ( $title_addon );
			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';
			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<ul class="uk-list' . $style . '">';

		if (isset ( $settings->ui_description_item ) && count ( ( array ) $settings->ui_description_item )) {
			foreach ( $settings->ui_description_item as $key => $item ) {
				$link = isset ( $item->link ) ? $item->link : '';
				$target = (isset ( $item->target ) && $item->target) ? ' target="' . $item->target . '"' : '';
				$title = (isset ( $item->title ) && $item->title) ? $item->title : '';
				$content = (isset ( $item->content ) && $item->content) ? $item->content : '';
				$meta = (isset ( $item->meta ) && $item->meta) ? $item->meta : '';

				$render_linkscroll = (empty ( $target ) && strpos ( $link, '#' ) === 0) ? ' uk-scroll' : '';

				$output .= '<li class="ui-item"' . $scrollspy_cls . '>';

				if ($meta_alignment == 'top-title' && $layout == 'stacked') {
					$output .= '<div class="tm-meta' . $meta_style . '">';
					$output .= $meta;
					$output .= '</div>';
				}

				if ($layout == 'grid-2' || $layout == 'grid-2-m') {
					if ($width_cls == 'expand') {
						$output .= '<div class="uk-child-width-auto' . $grid_cls_bp . $grid_column_gap . $grid_row_gap . $flex_cls . '" uk-grid>';
					} else {
						$output .= '<div class="uk-child-width-expand' . $grid_cls_bp . $grid_column_gap . $grid_row_gap . $flex_init . '" uk-grid>';
					}

					$output .= '<div class="' . $grid_cls . $grid_cls_bp . '">';
				}

				if ($title) {
					$output .= '<' . $title_element . ' class="tm-title uk-margin-remove' . $title_style_cls . '"' . $leader_cls . '>';
					$output .= $title;
					$output .= '</' . $title_element . '>';
				}

				if ($layout == 'grid-2' || $layout == 'grid-2-m') {
					$output .= '</div><div>';
				}

				if ($meta_alignment == 'bottom-title' && $layout == 'stacked') {
					$output .= '<div class="tm-meta' . $meta_style . '">';
					$output .= $meta;
					$output .= '</div>';
				}

				if ($layout == 'stacked') {
					$output .= '<div class="tm-content">';
					if ($meta_alignment == 'top-content') {
						$output .= '<div class="tm-meta uk-text-meta' . $meta_style . '">';
						$output .= $meta;
						$output .= '</div>';
					}
					$output .= ($link) ? '<a class="ui-link' . $link_style . '" href="' . $link . '"' . $target . $render_linkscroll . '>' : '';
					$output .= $content;
					$output .= ($link) ? '</a>' : '';

					if ($meta_alignment == 'bottom-content') {
						$output .= '<div class="tm-meta' . $meta_style . '">';
						$output .= $meta;
						$output .= '</div>';
					}
					$output .= '</div>';
				}

				if ($layout == 'grid-2' || $layout == 'grid-2-m') {
					if ($layout == 'grid-2') {
						$output .= '<div class="tm-content' . $content_style . '">';

						if ($meta_alignment == 'top-content') {
							$output .= '<div class="tm-meta' . $meta_style . '">';
							$output .= $meta;
							$output .= '</div>';
						}

						$output .= ($link) ? '<a class="ui-link' . $link_style . '" href="' . $link . '"' . $target . $render_linkscroll . '>' : '';
						$output .= $content;
						$output .= ($link) ? '</a>' : '';

						if ($meta_alignment == 'bottom-content') {
							$output .= '<div class="tm-meta' . $meta_style . '">';
							$output .= $meta;
							$output .= '</div>';
						}

						$output .= '</div>';
					} else {
						$output .= '<div class="tm-meta' . $meta_style . '">';
						$output .= $meta;
						$output .= '</div>';
					}

					$output .= '</div>';
					$output .= '</div>';
				}

				if ($layout == 'grid-2-m') {
					$output .= '<div class="tm-content' . $content_style . '">';
					$output .= ($link) ? '<a class="ui-link' . $link_style . '" href="' . $link . '"' . $target . $render_linkscroll . '>' : '';
					$output .= $content;
					$output .= ($link) ? '</a>' : '';
					$output .= '</div>';
				}

				$output .= '</li>';
			}
		}

		$output .= '</ul>';

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

		$leader = (isset ( $settings->leader ) && $settings->leader) ? 1 : 0;
		$layout = (isset ( $settings->layout ) && $settings->layout) ? $settings->layout : '';
		$leader_color = (isset ( $settings->leader_color ) && $settings->leader_color) ? 'color: ' . $settings->leader_color . ';' : '';
		$css = '';

		if ($layout == 'grid-2-m' && $leader) {
			if ($leader_color) {
				$css .= $addon_id . ' .uk-leader-fill::after {' . $leader_color . '}';
			}
		}
		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .tm-title {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$css .= $addon_id . ' .tm-meta {' . $custom_meta_color . '}';
		}
		if ($content_color) {
			$css .= $addon_id . ' .tm-content, ' . $addon_id . ' .tm-content a {' . $content_color . '}';
		}

		return $css;
	}
}
