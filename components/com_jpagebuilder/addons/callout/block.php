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
class JpagebuilderAddonCallout extends JpagebuilderAddons {
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

		$card = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_width = (isset ( $settings->card_width ) && $settings->card_width) ? ' uk-margin-auto uk-width-' . $settings->card_width : '';
		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? ' ' . $settings->card_size : '';
		$panel_link = (isset ( $settings->panel_link ) && $settings->panel_link) ? 1 : 0;
		$positions = (isset ( $settings->card_alignment ) && $settings->card_alignment) ? $settings->card_alignment : '';

		$panel_image_padding = (isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0;
		$image_padding = ($card && $positions != 'between') ? ((isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0) : '';

		// Alignment and Margin for left/right

		$grid_cls = (isset ( $settings->grid_width ) && $settings->grid_width) ? 'uk-width-' . $settings->grid_width : '';
		$grid_cls_bp = (isset ( $settings->grid_breakpoint ) && $settings->grid_breakpoint) ? '@' . $settings->grid_breakpoint : '';
		$cls_class = ($positions == 'right') ? ' uk-flex-last' . $grid_cls_bp . '' : '';
		$img_class = ($positions == 'left' || $positions == 'right') ? ' uk-card-media-' . $positions . '' : '';

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
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$grid_parallax = (isset ( $settings->grid_parallax ) && $settings->grid_parallax) ? $settings->grid_parallax : '';
		$gallery_parallax = ($grid_parallax) ? 'parallax: ' . $grid_parallax . '' : '';

		$masonry = (isset ( $settings->masonry ) && $settings->masonry) ? 1 : 0;
		$masonry_cls = ($masonry) ? 'masonry: true;' : '';

		$column_align = (isset ( $settings->grid_column_align ) && $settings->grid_column_align) ? 1 : 0;
		$row_align = (isset ( $settings->grid_row_align ) && $settings->grid_row_align) ? 1 : 0;

		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';
		$grid_row_gap = (isset ( $settings->grid_row_gap ) && $settings->grid_row_gap) ? $settings->grid_row_gap : '';

		$divider = ($grid_column_gap != 'collapse' && $grid_row_gap != 'collapse') ? (isset ( $settings->grid_divider ) && $settings->grid_divider) ? 1 : 0 : '';

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

		$grid .= ($divider) ? ' uk-grid-divider' : '';
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

		$attribs = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? ' target="' . $settings->link_new_tab . '"' : '';
		$btn_styles = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$btn_size = (isset ( $settings->link_button_size ) && $settings->link_button_size) ? ' ' . $settings->link_button_size : '';

		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';

		$button_style_cls = '';

		if (empty ( $btn_styles )) {
			$button_style_cls .= 'uk-button uk-button-default' . $btn_size;
		} elseif ($btn_styles == 'link' || $btn_styles == 'link-muted' || $btn_styles == 'link-text') {
			$button_style_cls .= 'uk-' . $btn_styles;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $btn_styles . $btn_size;
		}

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

		$image_width = (isset ( $settings->img_width ) && $settings->img_width) ? ' width="' . $settings->img_width . '"' : '';
		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';
		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;
		$cover_init = (! empty ( $card ) && $image_padding) ? ' uk-cover' : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$link_title_hover = (isset ( $settings->title_hover_style ) && $settings->title_hover_style) ? ' class="uk-link-' . $settings->title_hover_style . '"' : '';
		$icon_size = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? '; width: ' . $settings->faw_icon_size . '' : '';
		$panel_cls = ($card) ? 'uk-card uk-card-' . $card . $card_size . $card_width : 'uk-panel' . $card_width;
		$panel_cls .= ($card && $card != 'hover' && $panel_link) ? ' uk-card-hover' : '';
		$panel_cls .= (($card && $panel_image_padding == false) || ($card && $positions == 'between' && $panel_image_padding)) ? ' uk-card-body uk-margin-remove-first-child' : '';
		$panel_cls .= (empty ( $card ) && empty ( $panel_content_padding )) ? ' uk-margin-remove-first-child' : '';

		$toggle_transition = ($panel_link) ? ' uk-transition-toggle' : '';
		$output = '';

		$output .= '<div class="ui-callout' . $general . $max_width_cfg . '"' . $animation . '>';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div uk-grid="' . $masonry_cls . $gallery_parallax . '" class="uk-grid-match ' . $text_alignment . $grid . '">';

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
				$item_css = (isset ( $value->item_css ) && $value->item_css) ? ' ' . $value->item_css : '';
				$button_title = (isset ( $value->button_title ) && $value->button_title) ? $value->button_title : '';
				if (empty ( $button_title )) {
					$button_title .= $all_button_title;
				}

				$icon = ($media_type === 'fontawesome_icon') ? ((isset ( $value->faw_icon ) && $value->faw_icon) ? $value->faw_icon : '') : false;
				$uk_icon = ($media_type === 'uikit_icon') ? ((isset ( $value->uikit ) && $value->uikit) ? $value->uikit : '') : false;

				// Fallback old icon cls
				$fb_icon = (isset ( $value->custom_icon ) && $value->custom_icon) ? $value->custom_icon : '';

				$custom_icon = ($media_type === 'custom' && $fb_icon) ? (strpos ( $fb_icon, '<' ) === 0 ? '<div class="tm-custom-icon">' . $fb_icon . '</div>' : '<div class="tm-custom-icon"><span class="' . $fb_icon . '"></span></div>') : false;

				$icon_arr = array_filter ( explode ( ' ', $icon ) );
				if (count ( $icon_arr ) === 1) {
					$icon = 'fa ' . $icon;
				}

				if ($icon) {
					$icon_render = '<i class="uk-icon-link' . ($positions == 'between' || $positions == 'bottom' ? $image_margin_top : '') . ' ' . $icon . '" aria-hidden="true"></i>';
				} elseif ($uk_icon) {
					$icon_render = '<span class="uk-icon-link' . ($positions == 'between' || $positions == 'bottom' ? $image_margin_top : '') . '" uk-icon="icon: ' . $uk_icon . $icon_size . '"></span>';
				} else {
					$icon_render = $custom_icon;
				}

				$title_link = (isset ( $value->title_link->src ) && $value->title_link->src) ? $value->title_link->src : '';
				$link_transition = ($panel_link && $title_link) ? ' uk-display-block uk-link-toggle' : '';
				$image_alt = (isset ( $value->alt_text ) && $value->alt_text) ? $value->alt_text : '';

				$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';
				$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';

				$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';

				$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';
				$output .= '<div class="ui-item' . $item_css . '">';

				if ($panel_link && $title_link) {
					$output .= '<a class="' . $panel_cls . $link_transition . $toggle_transition . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . $scrollspy_cls . '>';
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
							$output .= ($title_link) ? '<a href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' : '';
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

					// end 1st colum

					$output .= empty ( $card ) && ! empty ( $card_content_padding ) || $card && $image_padding ? '<div>' : '';

					$output .= ($image_padding) ? '<div class="uk-card-body uk-margin-remove-first-child">' : '<div class="' . $card_content_padding . 'uk-margin-remove-first-child">';

					$output .= ($label_text) ? '<div class="uk-card-badge uk-label ' . $label_styles . '">' . $label_text . '</div>' : '';

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
						$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $heading_style_cls_init . $title_decoration . $font_weight . '">';
						$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
						if ($link_title && $title_link && $panel_link == false) {
							$output .= '<a' . $link_title_hover . ' href="' . $title_link . '" ' . $attribs . $render_linkscroll . '>';
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
						if ($panel_link == false) {
							$output .= '<div class="' . $button_style_cls . '">' . $button_title . '</div>';
						} else {
							$output .= '<a class="' . $button_style_cls . '" href="' . $title_link . '" ' . $attribs . $render_linkscroll . '>' . $button_title . '</a>';
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
								$output .= ($title_link) ? '<a href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' : '';
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
						$output .= '<div class="' . $title_grid_width . $title_breakpoint . ' uk-margin-remove-first-child">';
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
							$output .= '<a' . $link_title_hover . ' href="' . $title_link . '" ' . $attribs . $render_linkscroll . '>';
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
							$output .= ($title_link) ? '<a href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' : '';
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
							$output .= '<a class="' . $button_style_cls . '" href="' . $title_link . '" ' . $attribs . $render_linkscroll . '>' . $button_title . '</a>';
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
							$output .= ($title_link) ? '<a href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' : '';
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

		$styles = array ();
		foreach ( $settings->ui_grid_item as $key => $addon_item ) {
			$key ++;
			$media_type = (isset ( $value->media_type ) && $value->media_type) ? $value->media_type : '';
			$icon_size = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? $settings->faw_icon_size : '';
			$css = '';
			if ($icon_size && $media_type != 'uikit_icon') {
				$css .= $addon_id . ' .tm-custom-icon span,' . $addon_id . ' .uk-icon-link {';
				$css .= 'font-size:' . $icon_size . 'px;';
				$css .= '}';
			}
			$styles [$key] = $css;
		}

		$styles_explode = implode ( "\n", $styles );

		$css_makeup = '';

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
			$css_makeup .= $addon_id . ' .uk-icon-link {' . $icon_color . '}';
		}
		$styles_explode .= $css_makeup;

		return $styles_explode;
	}
}