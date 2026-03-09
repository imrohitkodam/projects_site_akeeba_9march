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
class JpagebuilderAddonCard extends JpagebuilderAddons {
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

		$card = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';

		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? ' ' . $settings->card_size : '';
		$card_title = (isset ( $settings->card_title ) && $settings->card_title) ? $settings->card_title : '';
		$card_meta = (isset ( $settings->meta ) && $settings->meta) ? $settings->meta : '';
		$positions = (isset ( $settings->card_alignment ) && $settings->card_alignment) ? $settings->card_alignment : '';

		$panel_image_padding = (isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0;
		$image_padding = ($card && $positions != 'between') ? ((isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0) : '';

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

		$card_content = (isset ( $settings->card_content ) && $settings->card_content) ? $settings->card_content : '';

		// Image
		$image = (isset ( $settings->image ) && $settings->image) ? $settings->image : '';

		$image_src = isset ( $image->src ) ? $image->src : $image;

		if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
			$image_src = $image_src;
		} elseif ($image_src) {
			$image_src = Uri::base ( true ) . '/' . $image_src;
		}

		$image_alt = (isset ( $settings->alt_text ) && $settings->alt_text) ? $settings->alt_text : '';

		$image_margin_top = (isset ( $settings->image_margin_top ) && $settings->image_margin_top) ? ' uk-margin-' . $settings->image_margin_top . '-top' : ' uk-margin-top';

		$title_alt_text = (isset ( $settings->card_title ) && $settings->card_title) ? $settings->card_title : '';

		$link_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? ' target="' . $settings->link_new_tab . '"' : '';
		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_size = (isset ( $settings->link_button_size ) && $settings->link_button_size) ? ' ' . $settings->link_button_size : '';

		$button_style_cls = '';
		if (empty ( $button_style )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size;
		} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
			$button_style_cls .= 'uk-' . $button_style;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $button_style . $button_size;
		}

		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';

		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';
		$title_link = (isset ( $settings->title_link ) && $settings->title_link) ? $settings->title_link : '';

		$label_text = (isset ( $settings->label_text ) && $settings->label_text) ? $settings->label_text : '';
		$label_styles = (isset ( $settings->label_styles ) && $settings->label_styles) ? ' ' . $settings->label_styles : '';

		// Alignment and Margin for left/right.

		$grid_cls = (isset ( $settings->grid_width ) && $settings->grid_width) ? 'uk-width-' . $settings->grid_width : '';
		$grid_cls_bp = (isset ( $settings->grid_breakpoint ) && $settings->grid_breakpoint) ? '@' . $settings->grid_breakpoint : '';

		$cls_class = ($positions == 'right') ? ' uk-flex-last' . $grid_cls_bp : '';

		$img_class = ($positions == 'left' || $positions == 'right') ? 'uk-card-media-' . $positions : '';

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

		// New options.

		$panel_content_padding = (isset ( $settings->card_content_padding ) && $settings->card_content_padding) ? $settings->card_content_padding : '';

		$card_content_padding = ($panel_content_padding && empty ( $card )) ? 'uk-padding' . (($panel_content_padding == 'default') ? ' ' : '-' . $panel_content_padding . ' ') : '';

		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$link_title_hover = (isset ( $settings->title_hover_style ) && $settings->title_hover_style) ? ' class="uk-link-' . $settings->title_hover_style . '"' : '';
		$panel_link = (isset ( $settings->panel_link ) && $settings->panel_link) ? 1 : 0;

		$title_align = (isset ( $settings->title_align ) && $settings->title_align) ? $settings->title_align : '';

		$title_grid_width = (isset ( $settings->title_grid_width ) && $settings->title_grid_width) ? 'uk-width-' . $settings->title_grid_width : '';
		$title_grid_width .= (isset ( $settings->title_breakpoint ) && $settings->title_breakpoint) ? '@' . $settings->title_breakpoint : '';

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

		// Cls init.

		$cover_init = (! empty ( $card ) && $image_padding) ? ' uk-cover' : '';
		$link_cover = ($image_padding && empty ( $vertical_alignment )) ? ' class="uk-position-cover"' : '';
		$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';

		$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';
		$toggle_transition = ($panel_link) ? ' uk-transition-toggle' : '';
		$link_transition = ($panel_link && $title_link) ? ' uk-display-block uk-link-toggle' : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$panel_cls = ($card) ? 'uk-card uk-card-' . $card . $card_size : 'uk-panel';
		$panel_cls .= ($card && $card != 'hover' && $panel_link) ? ' uk-card-hover' : '';
		$panel_cls .= (($card && $panel_image_padding == false) || ($card && $positions == 'between' && $panel_image_padding)) ? ' uk-card-body uk-margin-remove-first-child' : '';
		$panel_cls .= (empty ( $card ) && empty ( $panel_content_padding )) ? ' uk-margin-remove-first-child' : '';

		$output = '';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		if ($panel_link && $title_link) {
			$output .= '<a class="' . $panel_cls . $link_transition . $toggle_transition . $text_alignment . $general . $max_width_cfg . '" href="' . $title_link . '"' . $link_target . $render_linkscroll . $animation . '>';
		} else {
			$output .= '<div class="' . $panel_cls . $text_alignment . $general . $max_width_cfg . '"' . $animation . '>';
		}

		$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';

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
					$output .= '<a href="' . $title_link . '"' . $link_target . $render_linkscroll . $link_cover . '>';
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

			// end 1st column.

			$output .= empty ( $card ) && ! empty ( $card_content_padding ) || $card && $image_padding ? '<div>' : '';

			$output .= ($image_padding) ? '<div class="uk-card-body uk-margin-remove-first-child">' : '<div class="' . $card_content_padding . 'uk-margin-remove-first-child">';

			$output .= ($label_text) ? '<div class="uk-card-badge uk-label' . $label_styles . '">' . $label_text . '</div>' : '';

			if ($title_align == 'left') {

				$output .= '<div class="uk-child-width-expand uk-margin-top' . $title_grid_cr . '" uk-grid>';
				$output .= '<div class="' . $title_grid_width . ' uk-margin-remove-first-child">';
			}

			if ($meta_alignment == 'top' && $card_meta) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $card_meta;
				$output .= '</' . $meta_element . '>';
			}

			if ($card_title) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $heading_style_cls_init . $title_decoration . $font_weight . '">';
				$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';

				if ($link_title && $title_link && $panel_link == false) {
					$output .= '<a' . $link_title_hover . ' href="' . $title_link . '"' . $link_target . $render_linkscroll . '>';
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
				$output .= '</div>';
				$output .= '<div class="uk-margin-remove-first-child">';
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
					$output .= '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $link_target . $render_linkscroll . '>' . $button_title . '</a>';
				}

				$output .= '</div>';
			}

			if ($title_align == 'left') {
				$output .= '</div>';
				$output .= '</div>';
			}

			$output .= '</div>';
			$output .= empty ( $card ) && ! empty ( $card_content_padding ) || $card && $image_padding ? '</div>' : '';

			$output .= '</div>';
		} else {

			if ($positions == 'top' && $image_src) {

				$output .= ($image_padding) ? '<div class="uk-card-media-top">' : '';

				if ($image_link && $title_link && $panel_link == false) {
					$output .= '<a href="' . $title_link . '"' . $link_target . $render_linkscroll . '>';
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

			$output .= ($image_padding) ? '<div class="uk-card-body uk-margin-remove-first-child">' : '';
			$output .= ($card_content_padding) ? '<div class="' . $card_content_padding . 'uk-margin-remove-first-child">' : '';

			$output .= ($label_text) ? '<div class="uk-card-badge uk-label' . $label_styles . '">' . $label_text . '</div>' : '';

			if ($title_align == 'left') {

				$output .= '<div class="uk-child-width-expand uk-margin-top' . $title_grid_cr . '" uk-grid>';
				$output .= '<div class="' . $title_grid_width . ' uk-margin-remove-first-child">';
			}
			if ($meta_alignment == 'top' && $card_meta) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $card_meta;
				$output .= '</' . $meta_element . '>';
			}

			if ($card_title) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $heading_style_cls_init . $title_decoration . $font_weight . '">';
				$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';

				if ($link_title && $title_link && $panel_link == false) {
					$output .= '<a' . $link_title_hover . ' href="' . $title_link . '"' . $link_target . $render_linkscroll . '>';
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
				$output .= '</div>';
				$output .= '<div class="uk-margin-remove-first-child">';
			}

			if ($positions == 'between' && $image_src) {

				if ($image_link && $title_link && $panel_link == false) {
					$output .= '<a href="' . $title_link . '"' . $link_target . $render_linkscroll . '>';
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
				if ($panel_link == false) {
					$output .= '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $link_target . $render_linkscroll . '>' . $button_title . '</a>';
				} else {
					$output .= '<div class="' . $button_style_cls . '">' . $button_title . '</div>';
				}
				$output .= '</div>';
			}

			if ($title_align == 'left') {
				$output .= '</div>';
				$output .= '</div>';
			}

			$output .= ($image_padding) ? '</div>' : '';
			$output .= ($card_content_padding) ? '</div>' : '';

			if ($positions == 'bottom' && $image_src) {
				$output .= ($image_padding) ? '<div class="uk-card-media-bottom">' : '';
				if ($image_link && $title_link && $panel_link == false) {
					$output .= '<a href="' . $title_link . '"' . $link_target . $render_linkscroll . '>';
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
		}

		if ($panel_link && $title_link) {
			$output .= '</a>';
		} else {
			$output .= '</div>';
		}

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
		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$panel_link = (isset ( $settings->panel_link ) && $settings->panel_link) ? 1 : 0;
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

		$card_style = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_background = (isset ( $settings->card_background ) && $settings->card_background) ? 'background-color: ' . $settings->card_background . ';' : '';
		$card_color = (isset ( $settings->card_color ) && $settings->card_color) ? 'color: ' . $settings->card_color . ';' : '';
		$css = '';

		if ($card_style == 'custom' && $card_background) {
			$css .= $addon_id . ' .uk-card-custom {' . $card_background . '}';
		}

		if ($card_style == 'custom' && $card_color) {
			$css .= $addon_id . ' .uk-card-custom.uk-card-body, ' . $addon_id . ' .uk-card-custom>:not([class*=uk-card-media]) {' . $card_color . '}';
		}

		if (empty ( $title_color ) && $custom_title_color) {
			if ($link_title && $panel_link == false) {
				$css .= $addon_id . ' .ui-title a {' . $custom_title_color . '}';
			} else {
				$css .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
			}
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
