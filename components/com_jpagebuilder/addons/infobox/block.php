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
class JpagebuilderAddoninfobox extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$heading_addon_margin = (isset ( $settings->heading_addon_margin ) && $settings->heading_addon_margin) ? $settings->heading_addon_margin : '';
		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= ($heading_addon_margin) ? ' uk-margin' . (($heading_addon_margin == 'default') ? '' : '-' . $heading_addon_margin) : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$card = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_width = (isset ( $settings->card_width ) && $settings->card_width) ? ' uk-margin-auto uk-width-' . $settings->card_width : '';
		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? ' ' . $settings->card_size : '';
		$panel_link = (isset ( $settings->panel_link ) && $settings->panel_link) ? 1 : 0;

		$positions = (isset ( $settings->card_alignment ) && $settings->card_alignment) ? $settings->card_alignment : '';

		$panel_card_image = (isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0;
		$image_padding = ($card && $positions != 'between') ? ((isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0) : '';

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

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';

		$block_align = (isset ( $settings->block_align ) && $settings->block_align) ? $settings->block_align : '';
		$block_align_breakpoint = (isset ( $settings->block_align_breakpoint ) && $settings->block_align_breakpoint) ? '@' . $settings->block_align_breakpoint : '';
		$block_align_fallback = (isset ( $settings->block_align_fallback ) && $settings->block_align_fallback) ? $settings->block_align_fallback : '';

		// Block Alignment CLS.
		$block_cls [] = '';

		if (empty ( $block_align )) {
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = ' uk-margin-auto-right' . $block_align_breakpoint;
				$block_cls [] = 'uk-margin-remove-left' . $block_align_breakpoint . ($block_align_fallback == 'center' ? ' uk-margin-auto' : ' uk-margin-auto-left');
			}
		}

		if ($block_align == 'center') {
			$block_cls [] = ' uk-margin-auto' . $block_align_breakpoint;
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = 'uk-margin-auto' . ($block_align_fallback == 'right' ? '-left' : '');
			}
		}

		if ($block_align == 'right') {
			$block_cls [] = ' uk-margin-auto-left' . $block_align_breakpoint;
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = $block_align_fallback == 'center' ? 'uk-margin-remove-right' . $block_align_breakpoint . ' uk-margin-auto' : 'uk-margin-auto-left';
			}
		}

		$block_cls = implode ( ' ', array_filter ( $block_cls ) );

		$max_width_cfg .= $addon_max_width_breakpoint . ($max_width_cfg ? $block_cls : '');

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$general .= $max_width_cfg;

		$grid_parallax = (isset ( $settings->grid_parallax ) && $settings->grid_parallax) ? $settings->grid_parallax : '';
		$grid_parallax_init = ($grid_parallax) ? ' parallax: ' . $grid_parallax . ';' : '';

		$justify_columns = (isset ( $settings->justify_columns ) && $settings->justify_columns) ? 1 : 0;
		$justify_columns_cls = ($justify_columns) ? ' parallax-justify: true;' : '';
		$grid_parallax_start = (isset ( $settings->grid_parallax_start ) && $settings->grid_parallax_start) ? $settings->grid_parallax_start : '';
		$grid_parallax_end = (isset ( $settings->grid_parallax_end ) && $settings->grid_parallax_end) ? $settings->grid_parallax_end : '';

		$grid_parallax_start_init = ($grid_parallax_start && ($grid_parallax || $justify_columns)) ? ' parallax-start: ' . $grid_parallax_start . ';' : '';
		$grid_parallax_end_init = ($grid_parallax_end && ($grid_parallax || $justify_columns)) ? ' parallax-end: ' . $grid_parallax_end . ';' : '';

		$masonry = (isset ( $settings->masonry ) && $settings->masonry) ? 1 : 0;
		$masonry_layout = (isset ( $settings->masonry_layout ) && $settings->masonry_layout) ? $settings->masonry_layout : 'pack';
		$masonry_cls = ($masonry) ? ' masonry: ' . $masonry_layout . ';' : '';

		$column_align = (isset ( $settings->grid_column_align ) && $settings->grid_column_align) ? 1 : 0;
		$row_align = (isset ( $settings->grid_row_align ) && $settings->grid_row_align) ? 1 : 0;

		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';
		$grid_row_gap = (isset ( $settings->grid_row_gap ) && $settings->grid_row_gap) ? $settings->grid_row_gap : '';
		$divider = (isset ( $settings->grid_divider ) && $settings->grid_divider) ? 1 : 0;

		$phone_portrait = (isset ( $settings->phone_portrait ) && $settings->phone_portrait) ? $settings->phone_portrait : '';
		$phone_landscape = (isset ( $settings->phone_landscape ) && $settings->phone_landscape) ? $settings->phone_landscape : '';
		$tablet_landscape = (isset ( $settings->tablet_landscape ) && $settings->tablet_landscape) ? $settings->tablet_landscape : '';
		$desktop = (isset ( $settings->desktop ) && $settings->desktop) ? $settings->desktop : '';
		$large_screens = (isset ( $settings->large_screens ) && $settings->large_screens) ? $settings->large_screens : '';

		$grid = '';

		$grid .= ($phone_portrait) ? ' uk-child-width-' . (($phone_portrait == 'auto') ? '' : '1-') . $phone_portrait : '';
		$grid .= ($phone_landscape) ? ' uk-child-width-' . (($phone_landscape == 'auto') ? '' : '1-') . $phone_landscape . '@s' : '';
		$grid .= ($tablet_landscape) ? ' uk-child-width-' . (($tablet_landscape == 'auto') ? '' : '1-') . $tablet_landscape . '@m' : '';
		$grid .= ($desktop) ? ' uk-child-width-' . (($desktop == 'auto') ? '' : '1-') . '' . $desktop . '@l' : '';
		$grid .= ($large_screens) ? ' uk-child-width-' . (($large_screens == 'auto') ? '' : '1-') . $large_screens . '@xl' : '';

		$grid .= ($divider && $grid_column_gap != 'collapse' && $grid_row_gap != 'collapse') ? ' uk-grid-divider' : '';
		$grid .= ($column_align) ? ' uk-flex-center' : '';
		$grid .= ($row_align) ? ' uk-flex-middle' : '';

		if ($grid_column_gap == $grid_row_gap) {
			$grid .= (! empty ( $grid_column_gap ) && ! empty ( $grid_row_gap )) ? ' uk-grid-' . $grid_column_gap : '';
		} else {
			$grid .= ! empty ( $grid_column_gap ) ? ' uk-grid-column-' . $grid_column_gap : '';
			$grid .= ! empty ( $grid_row_gap ) ? ' uk-grid-row-' . $grid_row_gap : '';
		}

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

		$btn_styles = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$btn_size = (isset ( $settings->link_button_size ) && $settings->link_button_size) ? ' ' . $settings->link_button_size : '';

		$button_style_cls = '';

		if (empty ( $btn_styles )) {
			$button_style_cls .= 'uk-button uk-button-default' . $btn_size;
		} elseif ($btn_styles == 'link' || $btn_styles == 'link-muted' || $btn_styles == 'link-text') {
			$button_style_cls .= 'uk-' . $btn_styles;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $btn_styles . $btn_size;
		}

		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';

		$all_button_title = (isset ( $settings->all_button_title ) && $settings->all_button_title) ? $settings->all_button_title : '';

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

		$parallax_target = (isset ( $settings->parallax_target ) && $settings->parallax_target) ? $settings->parallax_target : false;
		$target_cls = ($parallax_target) ? ' target: !.jpb-section;' : '';

		// Default Animation.

		$animation = (isset ( $settings->animation ) && $settings->animation) ? $settings->animation : '';
		$parallax_zindex = (isset ( $settings->parallax_zindex ) && $settings->parallax_zindex) ? $settings->parallax_zindex : false;
		$zindex_cls = ($parallax_zindex && $animation == 'parallax') ? ' uk-position-z-index uk-position-relative' : '';

		$animation_repeat = ($animation) ? ((isset ( $settings->animation_repeat ) && $settings->animation_repeat) ? ' repeat: true;' : '') : '';
		$delay_element_animations = (isset ( $settings->delay_element_animations ) && $settings->delay_element_animations) ? $settings->delay_element_animations : '';
		$scrollspy_cls = ($delay_element_animations) ? ' uk-scrollspy-class' : '';
		$scrollspy_target = ($delay_element_animations) ? 'target: [uk-scrollspy-class]; ' : '';
		$animation_delay = ($delay_element_animations) ? ' delay: 200' : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . $target_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="' . $scrollspy_target . 'cls: uk-animation-' . $animation . ';' . $animation_repeat . $animation_delay . '"';
		}

		$image_link = (isset ( $settings->image_link ) && $settings->image_link) ? 1 : 0;
		$image_border = (! empty ( $card ) && $image_padding) ? false : ((isset ( $settings->image_border ) && $settings->image_border) ? ' ' . $settings->image_border : '');
		$image_box_shadow = (! empty ( $card )) ? false : ((isset ( $settings->image_box_shadow ) && $settings->image_box_shadow) ? ' uk-box-shadow-' . $settings->image_box_shadow : '');

		$image_transition = ($image_link || $panel_link) ? ((isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . ' uk-transition-opaque' : '') : false;
		$image_hover_box_shadow = (($image_link || $panel_link) && empty ( $card )) ? ((isset ( $settings->image_hover_box_shadow ) && $settings->image_hover_box_shadow) ? ' uk-box-shadow-hover-' . $settings->image_hover_box_shadow : '') : false;

		$panel_content_padding = (isset ( $settings->card_content_padding ) && $settings->card_content_padding) ? $settings->card_content_padding : '';
		$card_content_padding = ($panel_content_padding && empty ( $card )) ? 'uk-padding' . (($panel_content_padding == 'default') ? ' ' : '-' . $panel_content_padding . ' ') : '';

		// New options.
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

		$image_width = (isset ( $settings->img_width ) && $settings->img_width) ? ' width="' . $settings->img_width . '"' : '';
		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';
		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;
		$cover_init = (! empty ( $card ) && $image_padding) ? ' uk-cover' : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$link_title_hover = (isset ( $settings->title_hover_style ) && $settings->title_hover_style) ? ' class="uk-link-' . $settings->title_hover_style . '"' : '';

		$icon_width = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? '; width: ' . $settings->faw_icon_size . '' : '';
		$icon_height = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? '; height: ' . $settings->faw_icon_size . ';' : '';
		$card_inverse = $card == 'primary' || $card == 'secondary' ? ' uk-light' : '';

		$panel_cls = ($card) ? 'uk-card uk-card-' . $card . $card_size . $card_inverse . $card_width : 'uk-panel' . $card_width;
		$panel_cls .= ($card && $card != 'hover' && $panel_link) ? ' uk-card-hover' : '';
		$panel_cls .= (($card && $panel_card_image == false) || ($card && $positions == 'between' && $panel_card_image)) ? ' uk-card-body uk-margin-remove-first-child' : '';
		$panel_cls .= (empty ( $card ) && empty ( $panel_content_padding )) ? ' uk-margin-remove-first-child' : '';

		$toggle_transition = ($panel_link) ? ' uk-transition-toggle' : '';

		$output = '';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div class="ui-info-box' . ($masonry && $grid_parallax ? ' masonry-init' : '') . ' uk-grid-match ' . $text_alignment . $zindex_cls . $general . $grid . '" uk-grid="' . trim ( $masonry_cls . $grid_parallax_init . $justify_columns_cls . $grid_parallax_start_init . $grid_parallax_end_init ) . '"' . $animation . '>';

		if (isset ( $settings->ui_grid_item ) && count ( ( array ) $settings->ui_grid_item )) {
			foreach ( $settings->ui_grid_item as $key => $value ) {

				$media_type = (isset ( $value->media_type ) && $value->media_type) ? $value->media_type : '';
				$image = (empty ( $media_type )) ? ((isset ( $value->image ) && $value->image) ? $value->image : '') : false;
				$image_src = isset ( $image->src ) ? $image->src : $image;
				if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
					$image_src = $image_src;
				} elseif ($image_src) {
					$image_src = Uri::base ( true ) . '/' . $image_src;
				}

				$card_meta = (isset ( $value->meta ) && $value->meta) ? $value->meta : '';
				$label_text = (isset ( $value->label_text ) && $value->label_text) ? $value->label_text : '';
				$label_styles = (isset ( $value->label_styles ) && $value->label_styles) ? $value->label_styles : '';
				$card_content = (isset ( $value->card_content ) && $value->card_content) ? $value->card_content : '';
				$card_title = (isset ( $value->title ) && $value->title) ? $value->title : '';
				$ariaLabel = ! empty ( $value->link_aria_label ) ? ' aria-label="' . $value->link_aria_label . '"' : '';

				$button_title = (isset ( $value->button_title ) && $value->button_title) ? $value->button_title : '';
				if (empty ( $button_title )) {
					$button_title .= $all_button_title;
				}

				$icon = ($media_type === 'fontawesome_icon') ? ((isset ( $value->faw_icon ) && $value->faw_icon) ? $value->faw_icon : '') : false;
				$uk_icon = ($media_type === 'uikit_icon') ? ((isset ( $value->uikit ) && $value->uikit) ? $value->uikit : '') : false;

				// Fallback old icon cls
				$fb_icon = (isset ( $value->custom_icon ) && $value->custom_icon) ? $value->custom_icon : '';
				$custom_icon = ($media_type === 'custom' && $fb_icon) ? (strpos ( $fb_icon, '<' ) === 0 ? '<div class="tm-custom-icon">' . $fb_icon . '</div>' : '<div class="tm-custom-icon"><span class="uk-icon-link ' . $fb_icon . '"></span></div>') : false;

				list ( $title_link, $link_target ) = JpagebuilderAddonHelper::parseLink ( $value, 'title_link', [ 
						'new_tab' => 'target',
						'url' => 'url'
				] );

				$render_linkscroll = (empty ( $link_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

				$icon_arr = array_filter ( explode ( ' ', $icon ) );
				if (count ( $icon_arr ) === 1) {
					$icon = 'fa ' . $icon;
				}

				if ($icon) {
					$icon_render = '<i class="uk-icon-link' . ($positions == 'between' || $positions == 'bottom' ? $image_margin_top : '') . ' ' . $icon . '" aria-hidden="true"></i>';
				} elseif ($uk_icon) {
					$icon_render = '<span class="uk-icon-link' . ($positions == 'between' || $positions == 'bottom' ? $image_margin_top : '') . '" uk-icon="icon: ' . $uk_icon . $icon_width . $icon_height . '"></span>';
				} else {
					$icon_render = $custom_icon;
				}

				$image_alt = (isset ( $value->alt_text ) && $value->alt_text) ? $value->alt_text : '';
				$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';
				$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';
				$link_transition = ($panel_link && $title_link) ? ' uk-display-block uk-link-toggle' : '';

				$output .= '<div class="ui-item">';

				if ($panel_link && $title_link) {
					$output .= '<a href="' . $title_link . '" class="' . $panel_cls . $link_transition . $toggle_transition . '"' . $link_target . $ariaLabel . $render_linkscroll . $scrollspy_cls . '>';
				} else {
					$output .= '<div class="' . $panel_cls . '"' . $scrollspy_cls . '>';
				}

				if (($positions == 'left' || $positions == 'right') && ($image_src || $icon || $uk_icon || $custom_icon)) {

					if (! empty ( $card )) {
						$output .= ($image_padding) ? '<div class="uk-child-width-expand uk-grid-collapse uk-grid-match' . $vertical_alignment_cls . '" uk-grid>' : '<div class="uk-child-width-expand' . $image_grid_cr_gap . $vertical_alignment_cls . '" uk-grid>';
					} else {
						$output .= '<div class="uk-child-width-expand' . $image_grid_cr_gap . $vertical_alignment_cls . '" uk-grid>';
					}

					$output .= '<div class="' . $grid_cls . $grid_cls_bp . $cls_class . '">';

					$output .= ($image_padding) ? '<div class="' . $img_class . ' uk-cover-container">' : '';

					if ($image_src || $icon || $uk_icon || $custom_icon) {

						if ($image_link && $title_link && $panel_link == false) {
							$output .= ($title_link) ? '<a href="' . $title_link . '"' . $link_target . $ariaLabel . $render_linkscroll . '>' : '';
							$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle' . $image_border . $image_box_shadow . $image_hover_box_shadow . '">' : '';
						}

						$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '<div class="uk-inline-clip' . $image_border . $image_box_shadow . '">' : '';

						if ($image_src) {
							$output .= '<img' . $image_width . ' class="ui-img' . ($image_link || $panel_link ? $image_transition : $image_border . $image_box_shadow) . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . $cover_init . '>';
							$output .= ($image_padding && ! empty ( $card )) ? '<img class="uk-invisible uk-display-inline-block' . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>' : '';
						} else {
							$output .= $icon_render;
						}

						$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '</div>' : '';

						if ($image_link && $title_link && $panel_link == false) {
							$output .= ($image_transition) ? '</div>' : '';
							$output .= ($title_link) ? '</a>' : '';
						}
					}

					$output .= ($image_padding) ? '</div>' : '';

					$output .= '</div>';

					// end 1st column.

					$output .= empty ( $card ) && ! empty ( $card_content_padding ) || $card && $image_padding ? '<div>' : '';

					$output .= ($image_padding) ? '<div class="uk-card-body uk-margin-remove-first-child">' : '<div class="' . $card_content_padding . 'uk-margin-remove-first-child">';

					$output .= ($label_text) ? '<div class="uk-card-badge uk-label ' . $label_styles . '">' . $label_text . '</div>' : '';

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
							$output .= '<a href="' . $title_link . '"' . $link_title_hover . $link_target . $ariaLabel . $render_linkscroll . '>';
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
							$output .= '<a href="' . $title_link . '" class="' . $button_style_cls . '"' . $link_target . $ariaLabel . $render_linkscroll . '>' . $button_title . '</a>';
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

					if ($positions == 'top') {

						$output .= ($image_padding) ? '<div class="uk-card-media-top">' : '';

						if ($image_src || $icon || $uk_icon || $custom_icon) {

							if ($image_link && $title_link && $panel_link == false) {
								$output .= ($title_link) ? '<a href="' . $title_link . '"' . $link_target . $ariaLabel . $render_linkscroll . '>' : '';
								$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle' . $image_border . $image_box_shadow . $image_hover_box_shadow . '">' : '';
							}

							$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '<div class="uk-inline-clip' . $image_border . $image_box_shadow . '">' : '';

							if ($image_src) {
								$output .= '<img' . $image_width . ' class="ui-img' . ($image_link || $panel_link ? $image_transition : $image_border . $image_box_shadow) . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
							} else {
								$output .= $icon_render;
							}

							$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '</div>' : '';

							if ($image_link && $title_link && $panel_link == false) {
								$output .= ($image_transition) ? '</div>' : '';
								$output .= ($title_link) ? '</a>' : '';
							}
						}

						$output .= ($image_padding) ? '</div>' : '';
					}

					$output .= ($image_padding) ? '<div class="uk-card-body uk-margin-remove-first-child">' : '';
					$output .= ($card_content_padding) ? '<div class="' . $card_content_padding . 'uk-margin-remove-first-child">' : '';

					$output .= ($label_text) ? '<div class="uk-card-badge uk-label ' . $label_styles . '">' . $label_text . '</div>' : '';

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
							$output .= '<a href="' . $title_link . '"' . $link_title_hover . $link_target . $ariaLabel . $render_linkscroll . '>';
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

					if ($positions == 'between' && ($image_src || $icon || $uk_icon || $custom_icon)) {

						if ($image_link && $title_link && $panel_link == false) {
							$output .= ($title_link) ? '<a href="' . $title_link . '"' . $link_target . $ariaLabel . $render_linkscroll . '>' : '';
							$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle' . $image_border . $image_box_shadow . $image_hover_box_shadow . $image_margin_top . '">' : '';
						}

						$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '<div class="uk-inline-clip' . $image_border . $image_box_shadow . $image_margin_top . '">' : '';

						if ($image_src) {

							$output .= '<img' . $image_width . ' class="ui-img' . ($image_transition ? '' : $image_margin_top) . ($image_link || $panel_link ? $image_transition : $image_border . $image_box_shadow) . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
						} else {
							$output .= $icon_render;
						}

						$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '</div>' : '';

						if ($image_link && $title_link && $panel_link == false) {
							$output .= ($image_transition) ? '</div>' : '';
							$output .= ($title_link) ? '</a>' : '';
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
							$output .= '<a href="' . $title_link . '" class="' . $button_style_cls . '"' . $link_target . $ariaLabel . $render_linkscroll . '>' . $button_title . '</a>';
						}
						$output .= '</div>';
					}

					if ($title_align == 'left') {

						$output .= '</div>';
						$output .= '</div>';
					}

					$output .= ($image_padding) ? '</div>' : '';
					$output .= ($card_content_padding) ? '</div>' : '';

					if ($positions == 'bottom' && ($image_src || $icon || $uk_icon || $custom_icon)) {

						$output .= ($image_padding) ? '<div class="uk-card-media-bottom">' : '';

						if ($image_link && $title_link && $panel_link == false) {
							$output .= ($title_link) ? '<a href="' . $title_link . '"' . $link_target . $ariaLabel . $render_linkscroll . '>' : '';
							$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle' . $image_border . $image_box_shadow . $image_hover_box_shadow . $image_margin_top . '">' : '';
						}

						$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '<div class="uk-inline-clip' . $image_border . $image_box_shadow . $image_margin_top . '">' : '';

						if ($image_src) {

							$output .= '<img' . $image_width . ' class="ui-img' . ($image_transition || $image_padding ? '' : $image_margin_top) . ($image_link || $panel_link ? $image_transition : $image_border . $image_box_shadow) . $image_svg_color . '" src="' . $image_src . '" ' . $image_alt_init . $image_svg_inline_cls . '>';
						} else {
							$output .= $icon_render;
						}

						$output .= ($panel_link && ($image_transition || $image_border || $image_box_shadow)) ? '</div>' : '';

						if ($image_link && $title_link && $panel_link == false) {
							$output .= ($image_transition) ? '</div>' : '';
							$output .= ($title_link) ? '</a>' : '';
						}

						$output .= ($image_padding) ? '</div>' : '';
					}
				}

				$output .= ($panel_link && $title_link) ? '</a>' : '</div>';

				$output .= '</div>';
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

		$icon_color = (isset ( $settings->color ) && $settings->color) ? 'color: ' . $settings->color . ';' : '';
		$icon_size = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? $settings->faw_icon_size : '';

		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';

		$link_button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';
		$decoration_color = '';
		$decoration_color .= (isset ( $settings->decoration_color ) && $settings->decoration_color) ? ' border-color: ' . $settings->decoration_color . ';' : '';
		$decoration_color .= (isset ( $settings->decoration_width ) && $settings->decoration_width) ? ' border-width: ' . $settings->decoration_width . 'px;' : '';

		$masonry = (isset ( $settings->masonry ) && $settings->masonry) ? 1 : 0;
		$parallax = (isset ( $settings->grid_parallax ) && $settings->grid_parallax) ? $settings->grid_parallax : "";

		$styles = array ();
		foreach ( $settings->ui_grid_item as $key => $value ) {
			$key ++;
			$media_type = (isset ( $value->media_type ) && $value->media_type) ? $value->media_type : '';
			$icon_size = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? $settings->faw_icon_size : '';
			$css = '';
			if ($icon_size && $media_type != 'uikit_icon') {
				$css .= $addon_id . ' .tm-custom-icon span,' . $addon_id . ' .tm-custom-icon i,' . $addon_id . ' .uk-icon-link {';
				$css .= 'font-size:' . $icon_size . 'px;';
				$css .= '}';
			}
			$styles [$key] = $css;
		}

		$styles_explode = implode ( "\n", $styles );

		$css_makeup = '';
		// fix box resizing
		if ($masonry && $parallax) {
			$css_makeup .= $addon_id . ' .masonry-init {box-sizing: content-box;}';
		}
		if (empty ( $title_color ) && $custom_title_color) {
			$css_makeup .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$css_makeup .= $addon_id . ' .ui-meta {' . $custom_meta_color . '}';
		}
		if ($content_color) {
			$css_makeup .= $addon_id . ' .ui-content {' . $content_color . '}';
		}

		if ($link_button_style == 'custom') {
			if ($button_background || $button_color) {
				$css_makeup .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
			}
			if ($button_background_hover || $button_hover_color) {
				$css_makeup .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active, ' . $addon_id . ' .uk-button-custom.uk-active {' . $button_background_hover . $button_hover_color . '}';
			}
		}

		if ($icon_color) {
			$css_makeup .= $addon_id . ' .uk-icon-link, ' . $addon_id . ' .tm-custom-icon span {' . $icon_color . '}';
		}

		if (! empty ( $title_decoration ) && $decoration_color) {
			$css .= "\n";
			$css .= $addon_id . ' .uk-heading-bullet::before {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-line>::after {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-line>::before {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-divider {' . $decoration_color . '}';
			$css .= "\n";
		}

		$styles_explode .= $css_makeup;

		return $styles_explode;
	}
	public static function getFrontendEditor() {
		$output = '
		<style type="text/css">
		
		<# if (data.masonry && data.grid_parallax) { #>
			#jpb-addon-{{data.id}} .ui-info-box .uk-grid-match {
				box-sizing: initial;
			}
		<# } #>
		
		<# if(data.decoration_color && !_.isEmpty(data.title_decoration)) { #>
			#jpb-addon-{{ data.id }} .uk-heading-bullet::before {
				border-color: {{data.decoration_color}};
				border-width: {{data.decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-line>::after {
				border-color: {{data.decoration_color}};
				border-width: {{data.decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-line>::before {
				border-color: {{data.decoration_color}};
				border-width: {{data.decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-divider {
				border-color: {{data.decoration_color}};
				border-width: {{data.decoration_width}}px;
			}
		<# } #>
		<# if(data.faw_icon_size || data.color) { #>
			#jpb-addon-{{ data.id }} .uk-icon-link,
			#jpb-addon-{{ data.id }} .tm-custom-icon span,
			#jpb-addon-{{ data.id }} .tm-custom-icon i {
				<# if(data.color) { #>
					color: {{ data.color }};
				<# } #>
				<# if(data.faw_icon_size) { #>
					font-size: {{ data.faw_icon_size }}px;
				<# } #>
			}
		<# } #>

		<# if(_.isEmpty(data.title_color) && data.custom_title_color) { #>
			<# if(data.link_title && data.panel_link == false) { #>
				#jpb-addon-{{ data.id }} .ui-title a {
			<# } else { #>
				#jpb-addon-{{ data.id }} .ui-title {
			<# } #>	
				color: {{ data.custom_title_color }}
			}
		<# } #>

		<# if(_.isEmpty(data.meta_color) && data.custom_meta_color) { #>
			#jpb-addon-{{ data.id }} .ui-meta {
				color: {{ data.custom_meta_color }}
			}
		<# } #>

		<# if(data.content_color) { #>
			#jpb-addon-{{ data.id }} .ui-content {
				color: {{ data.content_color }}
			}
		<# } #>

		<# if(data.button_style == "custom") { #>
			#jpb-addon-{{ data.id }} .uk-button-custom {
				<# if(data.button_background) { #>
					background-color: {{ data.button_background }};
				<# } #>
				<# if(data.button_color) { #>
				color: {{ data.button_color }};
				<# } #>
			}
			#jpb-addon-{{ data.id }} .uk-button-custom:hover {
				<# if(data.button_background_hover) { #>
					background-color: {{ data.button_background_hover }};
				<# } #>
				<# if(data.button_hover_color) { #>
				color: {{ data.button_hover_color }};
				<# } #>
			}
		<# } #>

		</style>
		<#
		let heading_addon_margin = data.heading_addon_margin || "";
		let title_addon              = ( data.title_addon ) ? data.title_addon : "";
		var title_style = "";
		title_style = data.title_heading_style ? " uk-"+data.title_heading_style : "";
		title_style += data.title_heading_color ? " uk-"+data.title_heading_color : "";
		title_style += ( heading_addon_margin ) ? " uk-margin" + (( heading_addon_margin == "default" ) ? "" : "-" + heading_addon_margin ) : "";
		
		let title_heading_selector = data.title_heading_selector || "h3";
		var title_heading_decoration = data.title_heading_decoration ? " "+data.title_heading_decoration : "";

		let addon_margin = data.addon_margin || "";

		var general = "";
		
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";

		let card       = data.card_style ? data.card_style : "";
		let card_width = ( data.card_width ) ? " uk-margin-auto uk-width-"+data.card_width : "";
		let card_size  = data.card_size ? " " + data.card_size : "";
		let panel_link = ( data.panel_link ) ? 1 : "";
		let positions  = data.card_alignment ? data.card_alignment : "top";

		let panel_card_image = ( data.image_padding ) ? 1 : "";
		let image_padding       = ( card && positions != "between" ) ? ( ( data.image_padding ) ? 1 : "" ) : "";

		let grid_cls    = ( data.grid_width ) ? "uk-width-" + data.grid_width : "";
		let grid_cls_bp = ( data.grid_breakpoint ) ? "@" + data.grid_breakpoint : "";

		let cls_class = ( positions == "right" ) ? " uk-flex-last" + grid_cls_bp : "";

		let img_class = ( positions == "left" || positions == "right" ) ? "uk-card-media-" + positions : "";

		let vertical_alignment = ( data.vertical_alignment ) ? 1 : "";

		let vertical_alignment_cls = ( vertical_alignment ) ? " uk-flex-middle" : "";

		let image_grid_column_gap = ( data.image_grid_column_gap ) ? data.image_grid_column_gap : "";
		let image_grid_row_gap    = ( data.image_grid_row_gap ) ? data.image_grid_row_gap : "";

		var image_grid_cr_gap = ""
		if (image_grid_column_gap == image_grid_row_gap ) {
			image_grid_cr_gap   += (!_.isEmpty(image_grid_column_gap) && !_.isEmpty(image_grid_row_gap) ) ? " uk-grid-" + image_grid_column_gap : "";
		} else {
			image_grid_cr_gap   += !_.isEmpty(image_grid_column_gap) ? " uk-grid-column-" + image_grid_column_gap : "";
			image_grid_cr_gap   += !_.isEmpty(image_grid_row_gap) ? " uk-grid-row-" + image_grid_row_gap : "";
		}

		let max_width_cfg = ( data.addon_max_width ) ? " uk-width-" + data.addon_max_width : "";
		let addon_max_width_breakpoint = (!_.isEmpty( data.addon_max_width) && data.addon_max_width_breakpoint ) ? "@" + data.addon_max_width_breakpoint : "";

		let block_align = ( data.block_align ) ? data.block_align : "";
		let block_align_breakpoint = ( data.block_align_breakpoint ) ? "@" + data.block_align_breakpoint : "";
		let block_align_fallback = ( data.block_align_fallback ) ? data.block_align_fallback : "";

		var block_cls = "";

		if ( _.isEmpty( block_align ) ) {
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += " uk-margin-auto-right" + block_align_breakpoint;
				block_cls += " uk-margin-remove-left" + block_align_breakpoint + ( block_align_fallback == "center" ? " uk-margin-auto" : " uk-margin-auto-left" );
			}
		}

		if ( block_align == "center" ) {
			block_cls += " uk-margin-auto" + block_align_breakpoint;
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += " uk-margin-auto" + ( block_align_fallback == "right" ? "-left" : "" );
			}
		}

		if ( block_align == "right" ) {
			 block_cls += " uk-margin-auto-left" + block_align_breakpoint;
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += block_align_fallback == "center" ? " uk-margin-remove-right" + block_align_breakpoint + " uk-margin-auto" : "uk-margin-auto-left";
			}
		}

		max_width_cfg += addon_max_width_breakpoint + ( max_width_cfg ? block_cls : "" );
		general += max_width_cfg;
		
		let text_alignment = data.alignment ? " " + data.alignment : "";
		let text_breakpoint = (data.alignment && data.text_breakpoint) ? "@" + data.text_breakpoint : "";
		let text_alignment_fallback = (data.alignment && data.text_breakpoint && data.text_alignment_fallback) ? " uk-text-" + data.text_alignment_fallback : "";
		text_alignment         += text_breakpoint + text_alignment_fallback;

		var grid_parallax    = ( data.grid_parallax ) ? data.grid_parallax : "";
		var justify_columns     = ( data.justify_columns ) ? 1 : "";
		let justify_columns_cls = ( justify_columns ) ? " parallax-justify: true;" : "";
		var grid_parallax_start    = ( data.grid_parallax_start ) ? data.grid_parallax_start : "";
		var grid_parallax_end    = ( data.grid_parallax_end ) ? data.grid_parallax_end : "";
		let grid_parallax_start_init = ( grid_parallax_start && (grid_parallax || justify_columns) ) ? " parallax-start: " + grid_parallax_start + ";" : "";
		let grid_parallax_end_init = ( grid_parallax_end && (grid_parallax || justify_columns) ) ? " parallax-end: " + grid_parallax_end + ";" : "";

		let grid_parallax_init = ( grid_parallax ) ? "parallax: " + grid_parallax + ";" : "";
		var masonry          = ( data.masonry ) ? 1 : "";
		var masonry_layout    = ( data.masonry_layout ) ? data.masonry_layout : "pack";
		let masonry_cls      = ( masonry ) ? "masonry: " + masonry_layout + ";" : "";

		let column_align = ( data.grid_column_align ) ? 1 : "";
		let row_align    = ( data.grid_row_align ) ? 1 : "";

		let grid_column_gap = ( data.grid_column_gap ) ? data.grid_column_gap : "";
		let grid_row_gap    = ( data.grid_row_gap ) ? data.grid_row_gap : "";

		let divider = ( data.grid_divider ) ? 1 : "";

		let phone_portrait   = ( data.phone_portrait ) ? data.phone_portrait : "";
		let phone_landscape  = ( data.phone_landscape ) ? data.phone_landscape : "";
		let tablet_landscape = ( data.tablet_landscape ) ? data.tablet_landscape : "";
		let desktop          = ( data.desktop ) ? data.desktop : "";
		let large_screens    = ( data.large_screens ) ? data.large_screens : "";

		var grid = "";

		grid += ( phone_portrait ) ? " uk-child-width-" + ( ( phone_portrait == "auto" ) ? "" : "1-" ) + phone_portrait : "";
		grid += ( phone_landscape ) ? " uk-child-width-" + ( ( phone_landscape == "auto" ) ? "" : "1-" ) + phone_landscape + "@s" : "";
		grid += ( tablet_landscape ) ? " uk-child-width-" + ( ( tablet_landscape == "auto" ) ? "" : "1-" ) + tablet_landscape + "@m" : "";
		grid += ( desktop ) ? " uk-child-width-" + ( ( desktop == "auto" ) ? "" : "1-" ) + "" + desktop + "@l" : "";
		grid += ( large_screens ) ? " uk-child-width-" + ( ( large_screens == "auto" ) ? "" : "1-" ) + large_screens + "@xl" : "";

		grid += ( divider && grid_column_gap != "collapse" && grid_row_gap != "collapse" ) ? " uk-grid-divider" : "";
		grid += ( column_align ) ? " uk-flex-center" : "";
		grid += ( row_align ) ? " uk-flex-middle" : "";

		if ( grid_column_gap == grid_row_gap ) {
			grid += ( !_.isEmpty( grid_column_gap ) && !_.isEmpty( grid_row_gap ) ) ? " uk-grid-" + grid_column_gap : "";
		} else {
			grid += !_.isEmpty( grid_column_gap ) ? " uk-grid-column-" + grid_column_gap : "";
			grid += !_.isEmpty( grid_row_gap ) ? " uk-grid-row-" + grid_row_gap : "";
		}

		let heading_selector = data.heading_selector || "h3";
		 
		var heading_style    = ( data.heading_style ) ? " uk-" + data.heading_style : "";
		heading_style   += ( data.title_color ) ? " uk-text-" + data.title_color : "";
		heading_style   += ( data.title_text_transform ) ? " uk-text-" + data.title_text_transform : "";
		heading_style   += ( data.title_margin_top ) ? " uk-margin-" + data.title_margin_top +"-top" : " uk-margin-top";
		let title_decoration = data.title_decoration ? " " + data.title_decoration : "";

		let heading_style_cls      = data.heading_style ? " uk-" + data.heading_style : "";
		let heading_style_cls_init = ( _.isEmpty( heading_style_cls ) ) ? " uk-card-title" : "";

		// Meta.
		let meta_element = data.meta_element || "div";
		let meta_style_cls = data.meta_style ? data.meta_style : "";

		let meta_style  = data.meta_style ? " uk-" + data.meta_style : "";
		meta_style += data.meta_color ? " uk-text-" + data.meta_color : "";
		meta_style += data.meta_text_transform ? " uk-text-" + data.meta_text_transform : "";
		meta_style += data.meta_margin_top ? " uk-margin-" + data.meta_margin_top + "-top" : " uk-margin-top";

		// Remove margin for heading element
		if ( meta_element !== "div" || ( meta_style_cls && meta_style_cls !== "text-meta" ) ) {
			meta_style += " uk-margin-remove-bottom";
		}

		meta_alignment = data.meta_alignment ? data.meta_alignment : "";

		// Content.
		let content_style  = data.content_style ? " uk-" + data.content_style : "";

		let content_dropcap = data.content_dropcap ? 1 : 0;
		content_style	+= content_dropcap ? " uk-dropcap" : "";

		content_style += data.content_text_transform ? " uk-text-" + data.content_text_transform : "";

		let content_column = data.content_column ? " uk-column-"+data.content_column : "";
		let content_column_breakpoint = (data.content_column && data.content_column_breakpoint) ? "@"+data.content_column_breakpoint : "";
		let content_column_divider = (data.content_column && data.content_column_divider) ? " uk-column-divider" : "";
		
		content_style	+= content_column + content_column_breakpoint + content_column_divider;
		content_style	+= data.content_margin_top ? " uk-margin-" + data.content_margin_top + "-top" : " uk-margin-top";

		let btn_styles = ( data.button_style ) ? data.button_style : "";
		let btn_size    = ( data.link_button_size ) ? " " + data.link_button_size : "";
		var button_style_cls = "";

		if (_.isEmpty(btn_styles)) {
			button_style_cls += "uk-button uk-button-default" + btn_size;
		} else if ( btn_styles == "link" || btn_styles == "link-muted" || btn_styles == "link-text") {
			button_style_cls += "uk-" + btn_styles;
		} else {
			button_style_cls += "uk-button uk-button-" + btn_styles + btn_size;
		}
		let btn_margin_top = ( data.button_margin_top ) ? "uk-margin-" + data.button_margin_top + "-top" : "uk-margin-top";
		let all_button_title = ( data.all_button_title ) ? data.all_button_title : "";
		let image_margin_top = data.image_margin_top ? " uk-margin-" + data.image_margin_top + "-top" : " uk-margin-top";

		// Animation
		let horizontal_start = (!_.isEmpty(data.horizontal_start) && data.horizontal_start) ? data.horizontal_start : "0";
		let horizontal_end = (!_.isEmpty(data.horizontal_end) && data.horizontal_end) ? data.horizontal_end : "0";
		let horizontal = (!_.isEmpty(data.horizontal_start) || !_.isEmpty(data.horizontal_end)) ? \'x:\'+horizontal_start+\',\'+ horizontal_end +\';\' : "";

		let vertical_start = (!_.isEmpty(data.vertical_start) && data.vertical_start) ? data.vertical_start : "0";
		let vertical_end = (!_.isEmpty(data.vertical_end) && data.vertical_end) ? data.vertical_end : "0";
		let vertical = (!_.isEmpty(data.vertical_start) || !_.isEmpty(data.vertical_end)) ? \'y:\'+vertical_start+\',\'+ vertical_end +\';\' : "";

		let scale_start = (!_.isEmpty(data.scale_start) && data.scale_start) ? data.scale_start / 100 : "1";
		let scale_end = (!_.isEmpty(data.scale_end) && data.scale_end) ? data.scale_end / 100 : "1";
		let scale = (!_.isEmpty(data.scale_start) || !_.isEmpty(data.scale_end)) ? \'scale:\'+scale_start+\',\'+ scale_end +\';\' : "";

		let rotate_start = (!_.isEmpty(data.rotate_start) && data.rotate_start) ? data.rotate_start : "0";
		let rotate_end = (!_.isEmpty(data.rotate_end) && data.rotate_end) ? data.rotate_end : "0";
		let rotate = (!_.isEmpty(data.rotate_start) || !_.isEmpty(data.rotate_end)) ? \'rotate:\'+rotate_start+\',\'+ rotate_end +\';\' : "";

		let opacity_start = (!_.isEmpty(data.opacity_start) && data.opacity_start) ? parseInt(data.opacity_start) / 100 : "1";
		let opacity_end = (!_.isEmpty(data.opacity_end) && data.opacity_end) ? parseInt(data.opacity_end) / 100 : "1";
		let opacity = (!_.isEmpty(data.opacity_start) || !_.isEmpty(data.opacity_end)) ? \'opacity:\'+opacity_start+\',\'+ opacity_end +\';\' : "";

		let easing = (!_.isEmpty(data.easing) && data.easing) ? parseInt(data.easing) / 100 : "";
		let easing_cls = (!_.isEmpty(data.easing)) ? \'easing:\'+easing+\';\' : "";

		let breakpoint = (!_.isEmpty(data.breakpoint) && data.breakpoint) ? data.breakpoint : "";
		let breakpoint_cls = (!_.isEmpty(data.breakpoint)) ? \'media:@\'+breakpoint+\';\' : "";

		let viewport = (!_.isEmpty(data.viewport) && data.viewport) ? parseInt(data.viewport) / 100 : "";
		let viewport_cls = (!_.isEmpty(data.viewport)) ? \'viewport:\'+viewport+\';\' : "";
		
		let target_cls = data.parallax_target ? \'target:!.jpb-section;\' : "";

		let animation = data.animation ? data.animation : "";
		let parallax_zindex = ( data.parallax_zindex ) ? data.parallax_zindex : "";
		let zindex_cls = (animation == "parallax" && parallax_zindex) ? " uk-position-z-index uk-position-relative" : "";	

		let animation_repeat = (animation && data.animation_repeat) ? " repeat: true;" : "";
		let delay_element_animations = ( data.delay_element_animations ) ? data.delay_element_animations : "";
		
		let scrollspy_cls            = ( delay_element_animations ) ? " uk-scrollspy-class" : "";
		var scrollspy_target         = ( delay_element_animations ) ? \'target: [uk-scrollspy-class]; \' : "";
		let animation_delay          = ( delay_element_animations ) ? " delay: 200" : "";

		if (animation == "parallax") {
			animation = ` uk-parallax=${horizontal}${vertical}${scale}${rotate}${opacity}${easing_cls}${target_cls}${breakpoint_cls}${viewport_cls}`;
		} else if ( !_.isEmpty( animation ) ) {
			animation = ` uk-scrollspy="${scrollspy_target}cls: uk-animation-${animation};${animation_repeat}${animation_delay}"`;
		}
		let image_link       = ( data.image_link ) ? 1 : "";
		let image_border         = ( !_.isEmpty( card ) && image_padding ) ? "" : ( ( data.image_border ) ? " " + data.image_border : "" );
		let image_box_shadow     = ( !_.isEmpty( card ) ) ? "" : ( ( data.image_box_shadow ) ? " uk-box-shadow-" + data.image_box_shadow : "" );
		let image_transition     = ( image_link || panel_link ) ?  ( ( data.image_transition ) ? " uk-transition-" + data.image_transition + " uk-transition-opaque" : "" ) : "";
		let image_hover_box_shadow     = ( (image_link || panel_link) && _.isEmpty( card ) ) ?  ( ( data.image_hover_box_shadow ) ? " uk-box-shadow-hover-" + data.image_hover_box_shadow : "" ) : "";

		let panel_content_padding = ( data.card_content_padding ) ? data.card_content_padding : "";
		let card_content_padding   = ( panel_content_padding && _.isEmpty(card) ) ? "uk-padding"+((panel_content_padding == "default") ? " " : "-" + panel_content_padding + " ") : "";
		let title_align    = ( data.title_align ) ? data.title_align : "";

		var title_grid_width = ( data.title_grid_width ) ? "uk-width-" + data.title_grid_width : "";
		title_grid_width += ( data.title_breakpoint ) ? "@" + data.title_breakpoint : "";

		let title_grid_column_gap = ( data.title_grid_column_gap ) ? data.title_grid_column_gap : "";
		let title_grid_row_gap    = ( data.title_grid_row_gap ) ? data.title_grid_row_gap : "";

		var title_grid_cr = "";
		if (title_grid_column_gap == title_grid_row_gap ) {
			title_grid_cr    += (!_.isEmpty(title_grid_column_gap) && !_.isEmpty(title_grid_row_gap) ) ? " uk-grid-" + title_grid_column_gap : "";
		} else {
			title_grid_cr    += (!_.isEmpty(title_grid_column_gap)) ? " uk-grid-column-" + title_grid_column_gap : "";
			title_grid_cr    += (!_.isEmpty(title_grid_row_gap)) ? " uk-grid-row-" + title_grid_row_gap : "";
		}

		let image_width          = ( data.img_width ) ? \' width="\' + data.img_width + \'"\' : "";
		let image_svg_inline     = ( data.image_svg_inline ) ? 1 : "";
		let image_svg_inline_cls = ( image_svg_inline ) ? " uk-svg" : "";
		let image_svg_color      = ( image_svg_inline ) ? ( (data.image_svg_color ) ? " uk-text-" + data.image_svg_color : "" ) : "";

		let cover_init        = ( !_.isEmpty( card ) && image_padding ) ? " uk-cover" : "";
		let font_weight      = ( data.font_weight ) ? " uk-text-"+data.font_weight : "";

		let link_title       = ( data.link_title ) ? 1 : "";
		var link_title_hover = ( data.title_hover_style ) ? \' class="uk-link-\' + data.title_hover_style + \'" \' : "";
		let icon_width        = ( data.faw_icon_size ) ? "; width: " + data.faw_icon_size : "";
		let icon_height        = ( data.faw_icon_size ) ? "; height: " + data.faw_icon_size + ";" : "";

		let card_inverse = data.card_style == "primary" || data.card_style == "secondary" ? " uk-light" : "";

		var panel_cls = (card) ? "uk-card uk-card-" + card + card_size + card_inverse + card_width : "uk-panel"+card_width;
		panel_cls += (card && card != "hover" && panel_link) ? " uk-card-hover" : "";
		panel_cls += ((card && panel_card_image == false) || (card && positions == "between" && panel_card_image) ) ? " uk-card-body uk-margin-remove-first-child" : "";
		panel_cls += (_.isEmpty( card ) && _.isEmpty(panel_content_padding)) ? " uk-margin-remove-first-child" : "";

		let toggle_transition = ( panel_link ) ? " uk-transition-toggle" : "";
		let check_img_transition = (image_link || panel_link) && data.image_transition ;
		#>

		<div class="ui-info-box{{ zindex_cls }}{{ general }}"{{{ animation }}}>

		<# if( !_.isEmpty( data.title_addon ) ){ #>
			<{{ title_heading_selector }} class="tm-addon-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span> <# } #>
					{{{ data.title_addon }}}
			 	<# if (title_heading_decoration == " uk-heading-line") { #></span> <# } #>
			</{{ title_heading_selector }}>
		<# } #>

		<div class="uk-grid-match {{ text_alignment }}{{ grid }}" uk-grid="{{ masonry_cls }}{{ grid_parallax_init }}{{ justify_columns_cls }}{{ grid_parallax_start_init }}{{ grid_parallax_end_init }}">

		<#
		if(_.isObject(data.ui_grid_item) && data.ui_grid_item){
			_.each(data.ui_grid_item, function(value){
				
				let media_type = ( value.media_type ) ? value.media_type : "";
				var image     = {}
				if (typeof value.image !== "undefined" && typeof value.image.src !== "undefined") {
					image = value.image
				} else {
					image = {src: value.image}
				}	

				let icon_name    = ( value.icon_name ) ? value.icon_name : "";
				let card_meta    = ( value.meta ) ? value.meta : "";
				let label_text   = ( value.label_text ) ? value.label_text : "";
				let label_styles = ( value.label_styles ) ? value.label_styles : "";
				let card_content = ( value.card_content ) ? value.card_content : "";
				let card_title   = ( value.card_title ) ? value.card_title : "";

				const isUrlObj = _.isObject(value?.title_link) && (!!value?.title_link?.url || !!value?.title_link?.page || !!value?.title_link?.menu);
				const isUrlString = _.isString(value?.title_link) && value?.title_link !== "";
				
				const isTarget = value?.link_open_new_window ? "_blank" : "";
				const urlObj = value?.title_link?.url ? value?.title_link : window.getSiteUrl(value?.title_link, isTarget);
				const {url, new_tab, nofollow, type, } = urlObj;
				const target = new_tab ? "_blank" : "";
				
				const rel = nofollow ? "noopener noreferrer" : "";
				var buttonUrl = (type === "url" && url) || (type === "menu" && urlObj.menu) || ((type === "page" && !!urlObj.page) && "index.php?option=com_jpagebuilder&view=page&id=" + urlObj.page) || "";

				let button_title = ( value.button_title ) ? value.button_title : "";
				
				if ( _.isEmpty( button_title ) ) {
					button_title += all_button_title;
				}
				let ariaLabel = ( value.link_aria_label ) ? \' aria-label="\' + value.link_aria_label + \'" \' : "";
				let icon    = ( media_type === "fontawesome_icon" ) ? ( ( value.faw_icon ) ? value.faw_icon : "" ) : "";
				let uk_icon = ( media_type === "uikit_icon" ) ? ( ( value.uikit ) ? value.uikit : "" ) : "";

				// Fallback old icon cls
				let fb_icon = ( value.custom_icon ) ? value.custom_icon : "";

				let custom_icon = ( media_type === "custom" && fb_icon ) ? ( fb_icon.indexOf("<") > -1 ? \'<div class="tm-custom-icon">\' + fb_icon + \'</div>\' : \'<div class="tm-custom-icon"><span class="\'+ fb_icon + \'"></span></div>\' ) : "";

				let icon_arr = (typeof icon !== "undefined" && icon) ? icon.split(" ") : "";
				icon = icon_arr.length === 1 ? "fa "+icon : icon;
				var icon_render = "";
				if ( icon ) {
					icon_render = \'<i class="uk-icon-link\'+ ( positions == "between" || positions == "bottom" ? image_margin_top : "" ) + " " + icon + \'" aria-hidden="true"></i>\'
				} else if ( uk_icon ) {
					icon_render = \'<span class="uk-icon-link\'+ ( positions == "between" || positions == "bottom" ? image_margin_top : "" ) + \'" uk-icon="icon: \' + uk_icon + icon_width + icon_height + \'"></span>\'
				} else {
					icon_render = custom_icon;
				}

				let image_alt = value.alt_text ? value.alt_text : "";
				let title_alt_text = value.card_title ? value.card_title : "";
		
				var image_alt_init = "";
				
				if ( _.isEmpty( image_alt ) ) {
					image_alt_init += `alt="${title_alt_text.replace(/"/g, "")}"`;
				} else {
					image_alt_init += `alt="${image_alt.replace(/"/g, "")}"`;
				}

				let link_transition = ( panel_link && buttonUrl ) ? " uk-display-block uk-link-toggle" : "";
				
				#>
				
				<div class="ui-item">

				<# if ( panel_link && buttonUrl ) { #>
					<a class="{{ panel_cls }}{{ link_transition }}{{ toggle_transition }}" href=\'{{ buttonUrl }}\' target=\'{{ target }}\'{{{ ariaLabel }}} rel=\'{{ rel }}\'{{ scrollspy_cls }}>
				<# } else { #>
					<div class="{{ panel_cls }}"{{ scrollspy_cls }}>
				<# } #>

				<# if ( ( positions == "left" || positions == "right" ) && ( image.src || icon || uk_icon || custom_icon ) ) { #>

					<# if ( !_.isEmpty( card ) ) { #>
						<# if (image_padding) { #>
							<div class="uk-child-width-expand uk-grid-collapse uk-grid-match{{ vertical_alignment_cls }}" uk-grid>
						<# } else { #>
							<div class="uk-child-width-expand{{ image_grid_cr_gap }}{{ vertical_alignment_cls }}" uk-grid>
						<# } #>
					<# } else { #>
						<div class="uk-child-width-expand{{ image_grid_cr_gap }}{{ vertical_alignment_cls }}" uk-grid>
					<# } #>

					<div class="{{ grid_cls }}{{ grid_cls_bp }}{{ cls_class }}">

					<# if(image_padding) { #>
						<div class="{{ img_class }} uk-cover-container">
					<# } #>

					<# if ( image.src || icon || uk_icon || custom_icon ) { #>
						<# if ( image_link && buttonUrl && panel_link == false ) { #>
							<# if (buttonUrl) { #>
								<a href=\'{{ buttonUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'{{{ ariaLabel }}}>
							<# } #>
						<# } #>
						<# if ( check_img_transition ) { #>
							<div class="uk-inline-clip<# if (panel_link == false) { #> uk-transition-toggle<# } #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}">
						<# } #>
						<# if ( _.isEmpty(media_type) && image.src ) { #>
							<# if(image.src.indexOf("http://") == -1 && image.src.indexOf("https://") == -1){ #>
								<img{{{ image_width }}} class="ui-img<# if(! check_img_transition ) { #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}<# } #><# } else { #>{{image_transition}}<# } #>{{ image_svg_color }}" src=\'{{ pagebuilder_base + image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}{{ cover_init }}>
							<# } else { #>
								<img{{{ image_width }}} class="ui-img<# if(! check_img_transition ) { #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}<# } #><# } else { #>{{image_transition}}<# } #>{{ image_svg_color }}" src=\'{{ image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}{{ cover_init }}>
							<# } #>
							<# if (image_padding && !_.isEmpty(card)) { #>
								<# if(image.src.indexOf("http://") == -1 && image.src.indexOf("https://") == -1){ #>
									<img class="uk-invisible uk-display-inline-block{{ image_svg_color }}" src=\'{{ pagebuilder_base + image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}>
								<# } else { #>
									<img class="uk-invisible uk-display-inline-block{{image_svg_color}}" src=\'{{ image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}>
								<# } #>
							<# } #>	
						<# } else { #>
							{{{ icon_render }}}
						<# } #>
						
						<# if ( check_img_transition ) { #>
							</div>
						<# } #>

					<# } #>

					<# if(image_padding) { #>
						</div>
					<# } #>

					</div>

					<# if (_.isEmpty(card) && !_.isEmpty( card_content_padding ) || card && image_padding ) { #>
						<div>
					<# } #>
					
					<# if(image_padding) { #>
						<div class="uk-card-body uk-margin-remove-first-child">
					<# } else { #>
						<div class="{{ card_content_padding }}uk-margin-remove-first-child">
					<# } #>

					<# if(label_text) { #>
						<div class="uk-card-badge uk-label {{ label_styles }}">{{{ label_text }}}</div>
					<# } #>

					<# if ( title_align == "left" ) { #>
						<div class="uk-child-width-expand uk-margin-top{{ title_grid_cr }}" uk-grid>
						<div class="{{ title_grid_width }} uk-margin-remove-first-child">
					<# } #>

					<# if ( meta_alignment == "top" && card_meta ) { #>
						<{{ meta_element }} class="ui-meta{{ meta_style }}">
							{{{ card_meta }}}
						</{{ meta_element }}>
					<# } #>

					<# if ( card_title ) { #>
						<{{ heading_selector }} class="ui-title uk-margin-remove-bottom{{ heading_style }}{{ heading_style_cls_init }}{{ title_decoration }}{{ font_weight }}">
						<# if (title_decoration == " uk-heading-line") { #><span><# } #>
		
						<# if ( link_title && buttonUrl && panel_link == false ) { #>
							<a{{{ link_title_hover }}} href=\'{{ buttonUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'{{{ ariaLabel }}}>
						<# } #>
		
						{{{ card_title }}}
		
						<# if ( link_title && buttonUrl && panel_link == false ) { #>
							</a>
						<# } #>
		
						<# if (title_decoration == " uk-heading-line") { #></span><# } #>
						</{{ heading_selector }}>
					<# } #>

					<# if ( _.isEmpty(meta_alignment) && card_meta ) { #>
						<{{ meta_element }} class="ui-meta{{ meta_style }}">
							{{{ card_meta }}}
						</{{ meta_element }}>
					<# } #>
		
					<# if ( title_align == "left" ) { #>
						</div>
						<div class="uk-margin-remove-first-child">
					<# } #>

					<# if ( meta_alignment == "above" && card_meta ) { #>
						<{{ meta_element }} class="ui-meta{{ meta_style }}">
							{{{ card_meta }}}
						</{{ meta_element }}>
					<# } #>
		
					<# if ( card_content ) { #>
						<div class="ui-content uk-panel{{ content_style }}">
							{{{ card_content }}}
						</div>
					<# } #>

					<# if ( meta_alignment == "content" && card_meta ) { #>
						<{{ meta_element }} class="ui-meta{{ meta_style }}">
							{{{ card_meta }}}
						</{{ meta_element }}>
					<# } #>

					<# if ( button_title && buttonUrl ) { #>
						<div class="{{ btn_margin_top }}">
						<# if (panel_link) { #>
							<div class="{{ button_style_cls }}">{{{ button_title }}}</div>
						<# } else { #>
							<a class="{{ button_style_cls }}" href=\'{{ buttonUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'{{{ ariaLabel }}}>{{{ button_title }}}</a>
						<# } #>
						</div>
					<# } #>

					<# if ( title_align == "left" ) { #>
						</div>
						</div>
					<# } #>
		
					</div>

					<# if (_.isEmpty(card) && !_.isEmpty(card_content_padding) || card && image_padding ) { #>
						</div>
					<# } #>

					</div>

				<# } else { #>

					<# if ( positions == "top" && ( image.src || icon || uk_icon || custom_icon ) ) { #>

						<# if(image_padding) { #>
							<div class="uk-card-media-top">
						<# } #>

						<# if ( image.src || icon || uk_icon || custom_icon ) { #>

							<# if ( image_link && buttonUrl && panel_link == false ) { #>
								<# if (buttonUrl) { #>
									<a href=\'{{ buttonUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'{{{ ariaLabel }}}>
								<# } #>
							<# } #>
						
							<# if ( check_img_transition ) { #>
								<div class="uk-inline-clip<# if (panel_link == false) { #> uk-transition-toggle<# } #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}">
							<# } #>
	
							<# if ( _.isEmpty(media_type) && image.src ) { #>
								<# if(image.src.indexOf("http://") == -1 && image.src.indexOf("https://") == -1){ #>
									<img{{{ image_width }}} class="ui-img<# if(! check_img_transition ) { #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}<# } #><# } else { #>{{image_transition}}<# } #>{{ image_svg_color }}" src=\'{{ pagebuilder_base + image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}>
								<# } else { #>
									<img{{{ image_width }}} class="ui-img<# if(! check_img_transition ) { #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}<# } #><# } else { #>{{image_transition}}<# } #>{{ image_svg_color }}" src=\'{{ image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}>
								<# } #>
							<# } else { #>
								{{{ icon_render }}}
							<# } #>
							
							<# if ( check_img_transition ) { #>
								</div>
							<# } #>
	
							<# if ( image_link && buttonUrl && panel_link == false ) { #>
								<# if (buttonUrl) { #>
									</a>
								<# } #>
							<# } #>

						<# } #>

						<# if(image_padding) { #>
							</div>
						<# } #>

					<# } #>

					<# if(image_padding) { #>
						<div class="uk-card-body uk-margin-remove-first-child">
					<# } #>
			
					<# if(card_content_padding) { #>
						<div class="{{ card_content_padding }}uk-margin-remove-first-child">
					<# } #>

					<# if(label_text) { #>
						<div class="uk-card-badge uk-label{{ label_styles }}">
							{{{ label_text }}}
						</div>
					<# } #>

					<# if ( title_align == "left" ) { #>
						<div class="uk-child-width-expand uk-margin-top{{ title_grid_cr }}" uk-grid>
						<div class="{{ title_grid_width }} uk-margin-remove-first-child">
					<# } #>

					<# if ( meta_alignment == "top" && card_meta ) { #>
						<{{ meta_element }} class="ui-meta{{ meta_style }}">
							{{{ card_meta }}}
						</{{ meta_element }}>
					<# } #>

					<# if ( card_title ) { #>
						<{{ heading_selector }} class="ui-title uk-margin-remove-bottom{{ heading_style }}{{ heading_style_cls_init }}{{ title_decoration }}{{ font_weight }}">
						<# if (title_decoration == " uk-heading-line") { #><span><# } #>
		
						<# if ( link_title && buttonUrl && panel_link == false ) { #>
							<a{{{ link_title_hover }}} href=\'{{ buttonUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'{{{ ariaLabel }}}>
						<# } #>
		
						{{{ card_title }}}
		
						<# if ( link_title && buttonUrl && panel_link == false ) { #>
							</a>
						<# } #>
		
						<# if (title_decoration == " uk-heading-line") { #></span><# } #>
						</{{ heading_selector }}>
					<# } #>

					<# if ( _.isEmpty(meta_alignment) && card_meta ) { #>
						<{{ meta_element }} class="ui-meta{{ meta_style }}">
							{{{ card_meta }}}
						</{{ meta_element }}>
					<# } #>
			
					<# if ( title_align == "left" ) { #>
						</div>
						<div class="uk-margin-remove-first-child">
					<# } #>

					<# if ( positions == "between" && ( image.src || icon || uk_icon || custom_icon ) ) { #>

						<# if ( image_link && buttonUrl && panel_link == false ) { #>
							<# if (buttonUrl) { #>
								<a href=\'{{ buttonUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'{{{ ariaLabel }}}>
							<# } #>
						<# } #>

						<# if ( check_img_transition ) { #>
							<div class="uk-inline-clip<# if (panel_link == false) { #> uk-transition-toggle<# } #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}{{ image_margin_top }}<# } #>">
						<# } #>

						<# if ( _.isEmpty(media_type) && image.src ) { #>
							<# if(image.src.indexOf("http://") == -1 && image.src.indexOf("https://") == -1){ #>
								<img{{{ image_width }}} class="ui-img<# if(! check_img_transition ) { #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}{{ image_margin_top }}<# } #><# } else { #>{{image_transition}}<# } #>{{ image_svg_color }}" src=\'{{ pagebuilder_base + image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}>
							<# } else { #>
								<img{{{ image_width }}} class="ui-img<# if(! check_img_transition ) { #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}{{ image_margin_top }}<# } #><# } else { #>{{image_transition}}<# } #>{{ image_svg_color }}" src=\'{{ image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}>
							<# } #>
						<# } else { #>
							{{{ icon_render }}}
						<# } #>
						
						<# if ( check_img_transition ) { #>
							</div>
						<# } #>

						<# if ( image_link && buttonUrl && panel_link == false ) { #>
							<# if (buttonUrl) { #>
								</a>
							<# } #>
						<# } #>

					<# } #>


					<# if ( meta_alignment == "above" && card_meta ) { #>
						<{{ meta_element }} class="ui-meta{{ meta_style }}">
							{{{ card_meta }}}
						</{{ meta_element }}>
					<# } #>
			
					<# if ( card_content ) { #>
						<div class="ui-content uk-panel{{ content_style }}">
							{{{ card_content }}}
						</div>
					<# } #>

					<# if ( meta_alignment == "content" && card_meta ) { #>
						<{{ meta_element }} class="ui-meta{{ meta_style }}">
							{{{ card_meta }}}
						</{{ meta_element }}>
					<# } #>
			
					<# if ( button_title && buttonUrl ) { #>
						<div class="{{ btn_margin_top }}">
						<# if (panel_link) { #>
							<div class="{{ button_style_cls }}">{{{ button_title }}}</div>
						<# } else { #>
							<a class="{{ button_style_cls }}" href=\'{{ buttonUrl }}\' target=\'{{ target }}\'{{{ ariaLabel }}} rel=\'{{ rel }}\'>{{{ button_title }}}</a>
						<# } #>
						</div>
					<# } #>
			
					<# if ( title_align == "left" ) { #>
						</div>
						</div>
					<# } #>

					<# if(image_padding) { #>
						</div>
					<# } #>
			
					<# if(card_content_padding) { #>
						</div>
					<# } #>

					<# if ( positions == "bottom" && ( image.src || icon || uk_icon || custom_icon ) ) { #>

						<# if(image_padding) { #>
							<div class="uk-card-media-bottom">
						<# } #>

						<# if ( image_link && buttonUrl && panel_link == false ) { #>
							<# if (buttonUrl) { #>
								<a href=\'{{ buttonUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'{{{ ariaLabel }}}>
							<# } #>
						<# } #>

						<# if ( check_img_transition ) { #>
							<div class="uk-inline-clip<# if (panel_link == false) { #> uk-transition-toggle<# } #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}{{ image_margin_top }}<# } #>">
						<# } #>

						<# if ( _.isEmpty(media_type) && image.src ) { #>
							<# if(image.src.indexOf("http://") == -1 && image.src.indexOf("https://") == -1){ #>
								<img{{{ image_width }}} class="ui-img<# if(! check_img_transition ) { #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}{{ image_margin_top }}<# } #><# } else { #>{{image_transition}}<# } #>{{ image_svg_color }}" src=\'{{ pagebuilder_base + image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}>
							<# } else { #>
								<img{{{ image_width }}} class="ui-img<# if(! check_img_transition ) { #><# if ( _.isEmpty(card) || (card && ! panel_card_image) ) { #>{{ image_border }}{{ image_box_shadow }}{{ image_hover_box_shadow }}{{ image_margin_top }}<# } #><# } else { #>{{image_transition}}<# } #>{{ image_svg_color }}" src=\'{{ image.src }}\' {{{ image_alt_init }}}{{ image_svg_inline_cls }}>
							<# } #>
						<# } else { #>
							{{{ icon_render }}}
						<# } #>
						
						<# if ( check_img_transition ) { #>
							</div>
						<# } #>

						<# if ( image_link && buttonUrl && panel_link == false ) { #>
							<# if (buttonUrl) { #>
								</a>
							<# } #>
						<# } #>

						<# if(image_padding) { #>
							</div>
						<# } #>
					
					<# } #>

				<# } #>

				<# if( panel_link && buttonUrl ) { #>
					</a>
				<# } else {  #>
					</div>
				<# } #>

				</div>
		
		<# }); #>
		<# } #>
		
		</div>

		</div>

		';
		return $output;
	}
}
