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
class JpagebuilderAddonSwitcher extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;

		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$title_show = (isset ( $settings->title_show ) && $settings->title_show) ? 1 : 0;

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->text_alignment ) && $settings->text_alignment) ? ' ' . $settings->text_alignment : '';
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

		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';

		$navigation = (isset ( $settings->navigation ) && $settings->navigation) ? $settings->navigation : '';

		$positions = (isset ( $settings->positions ) && $settings->positions) ? $settings->positions : '';

		$primary_navigation = (isset ( $settings->primary_navigation ) && $settings->primary_navigation) ? 1 : 0;
		$primary_navigation_init = '';
		if (($positions == 'left' || $positions == 'right') && $navigation != 'tab' || $navigation != 'thumbnav') {
			$primary_navigation_init .= ($primary_navigation) ? ' uk-nav-primary' : ' uk-nav-default';
		}

		// Alignment and Margin for left/right.

		$switcher_margin = (isset ( $settings->switcher_margin ) && $settings->switcher_margin) ? ' uk-margin-' . $settings->switcher_margin : ' uk-margin';

		$nav_alignment = (isset ( $settings->switcher_align ) && $settings->switcher_align) ? $settings->switcher_align : '';
		$nav_alignment_cls = ($nav_alignment == 'justify') ? ' uk-child-width-expand' : ' uk-flex-' . $nav_alignment;

		$grid_cls = (isset ( $settings->grid_width ) && $settings->grid_width) ? ' uk-width-' . $settings->grid_width : '';
		$grid_cls_bp = (isset ( $settings->grid_breakpoint ) && $settings->grid_breakpoint) ? '@' . $settings->grid_breakpoint : '';
		$cls_class = ($positions == 'right') ? ' uk-flex-last' . $grid_cls_bp . '' : '';

		$vertical_alignment = (isset ( $settings->vertical_alignment ) && $settings->vertical_alignment) ? 1 : 0;

		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';
		$grid_row_gap = (isset ( $settings->grid_row_gap ) && $settings->grid_row_gap) ? $settings->grid_row_gap : '';
		$grid_init = '';

		if ($grid_column_gap == $grid_row_gap) {
			$grid_init .= (! empty ( $grid_column_gap ) && ! empty ( $grid_row_gap )) ? ' uk-grid-' . $grid_column_gap : '';
		} else {
			$grid_init .= ! empty ( $grid_column_gap ) ? ' uk-grid-column-' . $grid_column_gap : '';
			$grid_init .= ! empty ( $grid_row_gap ) ? ' uk-grid-row-' . $grid_row_gap : '';
		}

		$grid_init .= ($vertical_alignment) ? ' uk-flex-middle' : '';

		$match_height = (isset ( $settings->match_height ) && $settings->match_height) ? 1 : 0;
		$match_height_cls = ($match_height) ? ' uk-height-match="row: false"' : '';

		$thumbnail_width = (isset ( $settings->thumbnail_width ) && $settings->thumbnail_width) ? ' width="' . $settings->thumbnail_width . '"' : '';
		$thumbnail_height = (isset ( $settings->thumbnail_height ) && $settings->thumbnail_height) ? ' height="' . $settings->thumbnail_height . '"' : '';

		$switcher_animation = (isset ( $settings->switcher_animation ) && $settings->switcher_animation) ? ' animation: uk-animation-' . $settings->switcher_animation . ';' : '';

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

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

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_dropcap = (isset ( $settings->content_dropcap ) && $settings->content_dropcap) ? 1 : 0;
		$content_style .= ($content_dropcap) ? ' uk-dropcap' : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';

		$content_column = (isset ( $settings->content_column ) && $settings->content_column) ? ' uk-column-' . $settings->content_column : '';
		$content_column_breakpoint = ($content_column) ? ((isset ( $settings->content_column_breakpoint ) && $settings->content_column_breakpoint) ? '@' . $settings->content_column_breakpoint : '') : '';
		$content_column_divider = ($content_column) ? ((isset ( $settings->content_column_divider ) && $settings->content_column_divider) ? ' uk-column-divider' : false) : '';

		$content_style .= $content_column . $content_column_breakpoint . $content_column_divider;

		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$image_styles = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '';
		$image_styles .= (isset ( $settings->image_border ) && $settings->image_border) ? ' uk-border-' . $settings->image_border : '';

		$attribs = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? ' target="' . $settings->link_new_tab . '"' : '';
		$btn_styles = (isset ( $settings->button_style ) && $settings->button_style) ? '' . $settings->button_style : '';
		$button_size = (isset ( $settings->button_size ) && $settings->button_size) ? ' ' . $settings->button_size : '';
		$button_style_cls = '';
		if (empty ( $btn_styles )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size;
		} elseif ($btn_styles == 'link' || $btn_styles == 'link-muted' || $btn_styles == 'link-text') {
			$button_style_cls .= 'uk-' . $btn_styles;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $btn_styles . $button_size;
		}

		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';
		$all_button_title = (isset ( $settings->all_button_title ) && $settings->all_button_title) ? $settings->all_button_title : '';

		$thumbnav_wrap = (isset ( $settings->thumbnav_wrap ) && $settings->thumbnav_wrap) ? 1 : 0;
		$thumbnav_wrap_cls = ($thumbnav_wrap) ? ((isset ( $settings->thumbnav_wrap ) && $settings->thumbnav_wrap) ? ' uk-flex-nowrap' : '') : false;

		$image_positions = (isset ( $settings->image_positions ) && $settings->image_positions) ? $settings->image_positions : '';
		$image_margin_top = (isset ( $settings->image_margin_top ) && $settings->image_margin_top) ? ' uk-margin-' . $settings->image_margin_top . '-top' : ' uk-margin-top';

		$image_grid_cls = (isset ( $settings->image_grid_width ) && $settings->image_grid_width) ? ' uk-width-' . $settings->image_grid_width : '';
		$image_grid_cls_bp = (isset ( $settings->image_grid_breakpoint ) && $settings->image_grid_breakpoint) ? '@' . $settings->image_grid_breakpoint : '';
		$image_vertical_alignment = (isset ( $settings->image_vertical_alignment ) && $settings->image_vertical_alignment) ? 1 : 0;

		$image_grid_column_gap = (isset ( $settings->image_grid_column_gap ) && $settings->image_grid_column_gap) ? $settings->image_grid_column_gap : '';
		$image_grid_row_gap = (isset ( $settings->image_grid_row_gap ) && $settings->image_grid_row_gap) ? $settings->image_grid_row_gap : '';

		$image_grid_cr_gap = '';
		if ($image_grid_column_gap == $image_grid_row_gap) {
			$image_grid_cr_gap .= (! empty ( $image_grid_column_gap ) && ! empty ( $image_grid_row_gap )) ? ' uk-grid-' . $image_grid_column_gap : '';
		} else {
			$image_grid_cr_gap .= ! empty ( $image_grid_column_gap ) ? ' uk-grid-column-' . $image_grid_column_gap : '';
			$image_grid_cr_gap .= ! empty ( $image_grid_row_gap ) ? ' uk-grid-row-' . $image_grid_row_gap : '';
		}

		$image_grid_cr_gap .= ($image_vertical_alignment) ? ' uk-flex-middle' : '';

		$image_cls_class = ($image_positions == 'right') ? ' uk-flex-last' . $image_grid_cls_bp . '' : '';

		$nav_init = ($navigation != 'subnav' || $navigation != 'tab' || $navigation != 'thumbnav') ? ' uk-' . $navigation . '' : '';

		$thumbnail_svg_inline = (isset ( $settings->thumbnail_svg_inline ) && $settings->thumbnail_svg_inline) ? $settings->thumbnail_svg_inline : false;
		$thumbnail_svg_inline_cls = ($thumbnail_svg_inline) ? ' uk-svg' : '';
		$thumbnail_svg_color = ($thumbnail_svg_inline) ? ((isset ( $settings->thumbnail_svg_color ) && $settings->thumbnail_svg_color) ? ' uk-text-' . $settings->thumbnail_svg_color : '') : false;

		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';
		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;

		$title_navigation = (isset ( $settings->title_navigation ) && $settings->title_navigation) ? 1 : 0;
		$title_align = (isset ( $settings->title_align ) && $settings->title_align) ? $settings->title_align : '';
		$title_grid_width = (isset ( $settings->title_grid_width ) && $settings->title_grid_width) ? 'uk-width-' . $settings->title_grid_width : '';
		$title_grid_width .= (isset ( $settings->title_breakpoint ) && $settings->title_breakpoint) ? '@' . $settings->title_breakpoint : '';

		$title_grid_column_gap = (isset ( $settings->title_grid_column_gap ) && $settings->title_grid_column_gap) ? $settings->title_grid_column_gap : '';
		$title_grid_row_gap = (isset ( $settings->title_grid_row_gap ) && $settings->title_grid_row_gap) ? $settings->title_grid_row_gap : '';

		if ($title_grid_column_gap == $title_grid_row_gap) {
			$title_grid_width .= (! empty ( $title_grid_column_gap ) && ! empty ( $title_grid_row_gap )) ? ' uk-grid-' . $title_grid_column_gap : '';
		} else {
			$title_grid_width .= ! empty ( $title_grid_column_gap ) ? ' uk-grid-column-' . $title_grid_column_gap : '';
			$title_grid_width .= ! empty ( $title_grid_row_gap ) ? ' uk-grid-row-' . $title_grid_row_gap : '';
		}

		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$output = '';

		$output .= '<div class="ui-switcher' . $general . $text_alignment . $max_width_cfg . '"' . $animation . '>';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		if (($positions == 'left') || ($positions == 'right')) {

			$output .= '<div class="uk-child-width-expand' . $grid_init . '" uk-grid>';
			$output .= '<div class="' . $grid_cls . $grid_cls_bp . $cls_class . '">';
		}
		if ($positions == 'left' || $positions == 'right' || $positions == 'top') {

			// Filter Navigation.
			if ($navigation == 'thumbnav') {

				$output .= '<ul uk-switcher="connect: #sc-' . $this->addon->id . ';' . $switcher_animation . '" class="uk-thumbnav' . $thumbnav_wrap_cls . ($positions == 'left' || $positions == 'right' ? '' : $nav_alignment_cls . $switcher_margin) . '"' . ($positions == 'left' || $positions == 'right' ? ' uk-toggle="cls: uk-thumbnav-vertical; mode: media; media: ' . $grid_cls_bp . '"' : '') . '>';
				foreach ( $settings->ui_switcher_item as $key => $tab ) {
					$image = (isset ( $tab->image ) && $tab->image) ? $tab->image : '';
					$image_src = isset ( $image->src ) ? $image->src : $image;
					if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
						$image_src = $image_src;
					} elseif ($image_src) {
						$image_src = Uri::base ( true ) . '/' . $image_src;
					}

					$navigation_image = (isset ( $tab->navigation_image ) && $tab->navigation_image) ? $tab->navigation_image : '';
					$nav_image_src = isset ( $navigation_image->src ) ? $navigation_image->src : $navigation_image;
					if (strpos ( $nav_image_src, 'http://' ) !== false || strpos ( $nav_image_src, 'https://' ) !== false) {
						$nav_image_src = $nav_image_src;
					} elseif ($nav_image_src) {
						$nav_image_src = Uri::base ( true ) . '/' . $nav_image_src;
					}

					$title_image_alt = (isset ( $tab->title ) && $tab->title) ? $tab->title : '';
					$navigation_image_alt = (isset ( $tab->navigation_image_alt ) && $tab->navigation_image_alt) ? $tab->navigation_image_alt : '';
					$navigation_image_alt_init = (empty ( $navigation_image_alt )) ? 'alt="' . str_replace ( '"', '', $title_image_alt ) . '"' : 'alt="' . str_replace ( '"', '', $navigation_image_alt ) . '"';

					$output .= '<li class="thumb-nav-item-' . $key . '">';

					if ($nav_image_src) {
						$output .= '<a href="#"><img class="thumb-nav-image' . $thumbnail_svg_color . '" src="' . $nav_image_src . '" ' . $thumbnail_width . $thumbnail_height . $navigation_image_alt_init . $thumbnail_svg_inline_cls . '></a>';
					} else {
						$output .= '<a href="#"><img class="thumb-nav-image' . $thumbnail_svg_color . '" src="' . $image_src . '" ' . $thumbnail_width . $thumbnail_height . $navigation_image_alt_init . $thumbnail_svg_inline_cls . '></a>';
					}

					$output .= '</li>';
				}

				$output .= '</ul>';
			} else {
				if ($navigation == 'tab') {
					$output .= '<ul uk-tab="connect: #sc-' . $this->addon->id . ';' . $switcher_animation . ' media: ' . $grid_cls_bp . '" class="' . ($positions == 'left' || $positions == 'right' ? 'uk-tab uk-tab-' . $positions . '' : 'uk-tab' . $nav_alignment_cls . $switcher_margin . '') . '">';
				} else {
					$output .= '<ul uk-switcher="connect: #sc-' . $this->addon->id . ';' . $switcher_animation . '" class="' . ($positions == 'left' || $positions == 'right' ? 'uk-nav' . $primary_navigation_init . '' : 'uk-subnav' . $nav_init . $nav_alignment_cls . $switcher_margin . '') . '"' . ($positions == 'left' || $positions == 'right' ? ' uk-toggle="cls: uk-nav' . $primary_navigation_init . ' uk-subnav' . $nav_init . '; mode: media; media: ' . $grid_cls_bp . '"' : '') . '>';
				}
				foreach ( $settings->ui_switcher_item as $key => $tab ) {
					$title = (isset ( $tab->title ) && $tab->title) ? $tab->title : '';
					$nav_title = (isset ( $tab->navigation_image_alt ) && $tab->navigation_image_alt) ? $tab->navigation_image_alt : '';
					$output .= ($title_navigation && $nav_title) ? '<li><a href="#">' . $nav_title . '</a></li>' : '<li><a href="#">' . $title . '</a></li>';
				}
				$output .= '</ul>';
			}
		}

		if (($positions == 'left') || ($positions == 'right')) {
			$output .= '</div>';
			$output .= '<div>';
		}

		// Tab Content.
		$output .= '<ul id="sc-' . $this->addon->id . '" class="uk-switcher"' . $match_height_cls . '>';

		foreach ( $settings->ui_switcher_item as $key => $tab ) {
			$content = (isset ( $tab->content ) && $tab->content) ? $tab->content : '';
			$title = (isset ( $tab->title ) && $tab->title) ? $tab->title : '';
			$meta = (isset ( $tab->meta ) && $tab->meta) ? $tab->meta : '';
			$image = (isset ( $tab->image ) && $tab->image) ? $tab->image : '';
			$image_src = isset ( $image->src ) ? $image->src : $image;
			if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
				$image_src = $image_src;
			} elseif ($image_src) {
				$image_src = Uri::base ( true ) . '/' . $image_src;
			}
			$image_alt = (isset ( $tab->image_alt ) && $tab->image_alt) ? $tab->image_alt : '';
			$title_alt_text = (isset ( $tab->title ) && $tab->title) ? $tab->title : '';
			$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';

			$button_title = (isset ( $tab->button_title ) && $tab->button_title) ? $tab->button_title : '';
			if (empty ( $button_title )) {
				$button_title .= $all_button_title;
			}

			$title_link = (isset ( $tab->link ) && $tab->link) ? $tab->link : '';

			$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';
			$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

			$output .= '<li class="ui-item-' . $key . ' uk-margin-remove-first-child">';

			if ($image_positions == 'left' || $image_positions == 'right' && $image_src) {
				$output .= '<div class="uk-child-width-expand' . $image_grid_cr_gap . '" uk-grid>';
				$output .= '<div class="' . $image_grid_cls . $image_grid_cls_bp . ' ' . $image_cls_class . '">';

				$output .= '<img class="sc-img' . $image_styles . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
			}

			if ($image_positions == 'top' && $image_src) {
				$output .= '<img class="sc-img' . $image_styles . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
			}

			if ($image_positions == 'left' || $image_positions == 'right' && $image_src) {
				$output .= '</div>';
				$output .= '<div class="uk-margin-remove-first-child">';
			}

			if ($title_align == 'left') {

				$output .= '<div class="uk-child-width-expand uk-margin-top" uk-grid>';
				$output .= '<div class="' . $title_grid_width . ' uk-margin-remove-first-child">';
			}

			if ($meta_alignment == 'top' && $meta) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $meta;
				$output .= '</' . $meta_element . '>';
			}

			if ($title_show && $title) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $font_weight . '">';
				$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
				$output .= $title;
				$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
				$output .= '</' . $heading_selector . '>';
			}

			if (empty ( $meta_alignment ) && $meta) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $meta;
				$output .= '</' . $meta_element . '>';
			}

			if ($title_align == 'left') {
				$output .= '</div>';
				$output .= '<div class="uk-margin-remove-first-child">';
			}

			if ($meta_alignment == 'above' && $meta) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $meta;
				$output .= '</' . $meta_element . '>';
			}
			if ($content) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $content;
				$output .= '</div>';
			}

			if ($meta_alignment == 'content' && $meta) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $meta;
				$output .= '</' . $meta_element . '>';
			}
			if ($title_link) {
				$output .= '<div class="' . $btn_margin_top . '">';
				$output .= '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>';
				$output .= $button_title;
				$output .= '</a>';
				$output .= '</div>';
			}

			if ($title_align == 'left') {
				$output .= '</div>';
				$output .= '</div>';
			}

			if ($image_positions == 'left' || $image_positions == 'right' && $image_src) {
				$output .= '</div>';
				$output .= '</div>';
			}

			if ($image_positions == 'bottom' && $image_src) {
				$output .= '<img class="sc-img' . $image_styles . $image_margin_top . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
			}

			$output .= '</li>';
		}

		$output .= '</ul>';

		if (($positions == 'left') || ($positions == 'right')) {
			$output .= '</div>';
			$output .= '</div>';
		}

		if ($positions == 'bottom') {

			if ($navigation == 'thumbnav') {
				$output .= '<ul uk-switcher="connect: #sc-' . $this->addon->id . ';' . $switcher_animation . '" class="uk-thumbnav' . $thumbnav_wrap_cls . $nav_alignment_cls . $switcher_margin . '">';
				foreach ( $settings->ui_switcher_item as $key => $tab ) {
					$image = (isset ( $tab->image ) && $tab->image) ? $tab->image : '';
					$image_src = isset ( $image->src ) ? $image->src : $image;
					if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
						$image_src = $image_src;
					} elseif ($image_src) {
						$image_src = Uri::base ( true ) . '/' . $image_src;
					}

					$navigation_image = (isset ( $tab->navigation_image ) && $tab->navigation_image) ? $tab->navigation_image : '';
					$nav_image_src = isset ( $navigation_image->src ) ? $navigation_image->src : $navigation_image;
					if (strpos ( $nav_image_src, 'http://' ) !== false || strpos ( $nav_image_src, 'https://' ) !== false) {
						$nav_image_src = $image_nav_src;
					} elseif ($nav_image_src) {
						$nav_image_src = Uri::base ( true ) . '/' . $nav_image_src;
					}

					$navigation_image_alt = (isset ( $tab->navigation_image_alt ) && $tab->navigation_image_alt) ? $tab->navigation_image_alt : '';
					$title_navigation_image_alt = (isset ( $tab->title ) && $tab->title) ? $tab->title : '';
					$navigation_image_alt_init = (empty ( $navigation_image_alt )) ? 'alt="' . str_replace ( '"', '', $title_navigation_image_alt ) . '"' : 'alt="' . str_replace ( '"', '', $navigation_image_alt ) . '"';

					$output .= '<li class="thumb-nav-item-' . $key . '">';

					if ($nav_image_src) {
						$output .= '<a href="#"><img class="s thumb-nav-image' . $thumbnail_svg_color . '" src="' . $nav_image_src . '" ' . $thumbnail_width . $thumbnail_height . $navigation_image_alt_init . $thumbnail_svg_inline_cls . '></a>';
					} else {
						$output .= '<a href="#"><img class="thumb-nav-image' . $thumbnail_svg_color . '" src="' . $image_src . '" ' . $thumbnail_width . $thumbnail_height . $navigation_image_alt_init . $thumbnail_svg_inline_cls . '></a>';
					}

					$output .= '</li>';
				}

				$output .= '</ul>';
			} else {
				if ($navigation == 'tab') {
					$output .= '<ul uk-tab="connect: #sc-' . $this->addon->id . ';' . $switcher_animation . '" class="uk-tab' . $nav_alignment_cls . $switcher_margin . '">';
				} else {
					$output .= '<ul uk-switcher="connect: #sc-' . $this->addon->id . ';' . $switcher_animation . '" class="uk-subnav' . $nav_init . $nav_alignment_cls . $switcher_margin . '">';
				}
				foreach ( $settings->ui_switcher_item as $key => $tab ) {
					$title = (isset ( $tab->title ) && $tab->title) ? $tab->title : '';
					$nav_title = (isset ( $tab->navigation_image_alt ) && $tab->navigation_image_alt) ? $tab->navigation_image_alt : '';
					$output .= ($title_navigation && $nav_title) ? '<li><a href="#">' . $nav_title . '</a></li>' : '<li><a href="#">' . $title . '</a></li>';
				}
				$output .= '</ul>';
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
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';
		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

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
