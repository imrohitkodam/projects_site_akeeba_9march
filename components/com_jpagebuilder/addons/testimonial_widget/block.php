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
class JpagebuilderAddontestimonial_widget extends JpagebuilderAddons {
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
		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? ' ' . $settings->card_size : '';
		$panel_content_padding = (isset ( $settings->card_content_padding ) && $settings->card_content_padding) ? $settings->card_content_padding : '';

		$card_content_padding = ($panel_content_padding && empty ( $card )) ? 'uk-padding' . (($panel_content_padding == 'default') ? ' uk-margin-remove-first-child' : '-' . $panel_content_padding . ' uk-margin-remove-first-child') : '';

		// Options.

		$message = (isset ( $settings->message ) && $settings->message) ? $settings->message : '';
		$client_review = (isset ( $settings->client_review ) && $settings->client_review) ? $settings->client_review : '';
		$name = (isset ( $settings->name ) && $settings->name) ? $settings->name : '';
		$company = (isset ( $settings->company ) && $settings->company) ? $settings->company : '';
		$avatar = (isset ( $settings->avatar ) && $settings->avatar) ? $settings->avatar : '';
		$avatar_src = isset ( $avatar->src ) ? $avatar->src : $avatar;
		if (strpos ( $avatar_src, 'http://' ) !== false || strpos ( $avatar_src, 'https://' ) !== false) {
			$avatar_src = $avatar_src;
		} elseif ($avatar_src) {
			$avatar_src = Uri::base ( true ) . '/' . $avatar_src;
		}
		$alt_text = (isset ( $settings->alt_text ) && $settings->alt_text) ? $settings->alt_text : '';
		$avatar_shape = (isset ( $settings->avatar_shape ) && $settings->avatar_shape) ? ' ' . $settings->avatar_shape : '';

		// New link
		list ( $link, $link_target ) = JpagebuilderAddonHelper::parseLink ( $settings, 'link', [ 
				'link' => 'link',
				'new_tab' => 'link_target'
		] );

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

		$text_alignment = (isset ( $settings->text_alignment ) && $settings->text_alignment) ? ' uk-text-' . $settings->text_alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$general .= $text_alignment . $text_breakpoint . $text_alignment_fallback;

		$head_alignment = (isset ( $settings->text_alignment ) && $settings->text_alignment) ? ' uk-flex-' . $settings->text_alignment : '';
		$head_breakpoint = ($head_alignment) ? ((isset ( $settings->head_breakpoint ) && $settings->head_breakpoint) ? '@' . $settings->head_breakpoint : '') : '';
		$head_alignment_fallback = ($head_alignment && $head_breakpoint) ? ((isset ( $settings->head_alignment_fallback ) && $settings->head_alignment_fallback) ? ' uk-flex-' . $settings->head_alignment_fallback : '') : '';
		$head_alignment .= $head_breakpoint . $head_alignment_fallback;

		$header_alignment = (isset ( $settings->header_alignment ) && $settings->header_alignment) ? $settings->header_alignment : '';
		$vertical_alignment = (isset ( $settings->vertical_alignment ) && $settings->vertical_alignment) ? 1 : 0;

		$vertical_alignment_cls = ($vertical_alignment) ? ' uk-flex-middle' : '';
		$image_grid_column_gap = (isset ( $settings->image_grid_column_gap ) && $settings->image_grid_column_gap) ? ' uk-grid-column-' . $settings->image_grid_column_gap : '';

		$heading_style = (isset ( $settings->title_style ) && $settings->title_style) ? ' uk-' . $settings->title_style : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_text_color ) && $settings->title_text_color) ? ' uk-text-' . $settings->title_text_color : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';

		$heading_style_cls = (isset ( $settings->title_style ) && $settings->title_style) ? ' uk-' . $settings->title_style : '';
		$heading_style_cls_init = (empty ( $heading_style_cls )) ? ' uk-card-title' : '';

		// Meta
		$meta_element = (isset ( $settings->meta_element ) && $settings->meta_element) ? $settings->meta_element : 'div';
		$meta_style_cls = (isset ( $settings->meta_style ) && $settings->meta_style) ? $settings->meta_style : '';

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->meta_font_weight ) && $settings->meta_font_weight) ? ' uk-text-' . $settings->meta_font_weight : '';
		$meta_style .= (isset ( $settings->meta_text_color ) && $settings->meta_text_color) ? ' uk-text-' . $settings->meta_text_color : '';
		$meta_style .= (isset ( $settings->meta_text_transform ) && $settings->meta_text_transform) ? ' uk-text-' . $settings->meta_text_transform : '';
		$meta_style .= (isset ( $settings->meta_margin_top ) && $settings->meta_margin_top) ? ' uk-margin-' . $settings->meta_margin_top . '-top' : ' uk-margin-top';

		// Remove margin for heading element
		if ($meta_element != 'div' || ($meta_style_cls && $meta_style_cls != 'text-meta')) {
			$meta_style .= ' uk-margin-remove-bottom';
		}

		// Content
		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_dropcap = (isset ( $settings->content_dropcap ) && $settings->content_dropcap) ? 1 : 0;
		$content_style .= ($content_dropcap) ? ' uk-dropcap' : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';
		$content_column = (isset ( $settings->content_column ) && $settings->content_column) ? ' uk-column-' . $settings->content_column : '';
		$content_column_breakpoint = ($content_column) ? ((isset ( $settings->content_column_breakpoint ) && $settings->content_column_breakpoint) ? '@' . $settings->content_column_breakpoint : '') : '';
		$content_column_divider = ($content_column) ? ((isset ( $settings->content_column_divider ) && $settings->content_column_divider) ? ' uk-column-divider' : false) : '';

		$content_style .= $content_column . $content_column_breakpoint . $content_column_divider;
		$content_style .= empty ( $header_alignment ) ? ((isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top') : '';

		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$image_position = (isset ( $settings->position ) && $settings->position) ? $settings->position : '';

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

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . $target_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}

		$rating_alignment = (isset ( $settings->rating_alignment ) && $settings->rating_alignment) ? $settings->rating_alignment : '';
		$center_cls = ($text_alignment == 'center') ? ' uk-text-center' : '';

		$img_center_cls = ($rating_alignment == 'image' && empty ( $text_alignment )) ? ' uk-text-center' : '';

		$header_margin_top = (isset ( $settings->header_margin_top ) && $settings->header_margin_top) ? ' uk-margin-' . $settings->header_margin_top : ' uk-margin';
		$image_width = (isset ( $settings->avatar_width ) && $settings->avatar_width) ? ' width="' . $settings->avatar_width . '"' : '';

		// New options.

		$image_grid_cls = (isset ( $settings->image_grid_width ) && $settings->image_grid_width) ? 'uk-width-' . $settings->image_grid_width : '';
		$image_grid_cls_bp = (isset ( $settings->image_grid_breakpoint ) && $settings->image_grid_breakpoint) ? '@' . $settings->image_grid_breakpoint : '';
		$cls_class = ($image_position == 'right') ? ' uk-flex-last' . $image_grid_cls_bp . '' : '';

		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';
		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;

		$render_linkscroll = (empty ( $link_target ) && strpos ( $link, '#' ) === 0) ? ' uk-scroll' : '';

		$image_link = (isset ( $settings->image_link ) && $settings->image_link) ? 1 : 0;
		$panel_link = (isset ( $settings->panel_link ) && $settings->panel_link) ? 1 : 0;
		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$link_title_hover = (isset ( $settings->title_hover_style ) && $settings->title_hover_style) ? ' class="uk-link-' . $settings->title_hover_style . '"' : '';

		$card_inverse = $card == 'primary' || $card == 'secondary' ? ' uk-light' : '';
		$panel_cls = ($card) ? 'uk-card uk-card-' . $card . $card_size . $card_inverse . $zindex_cls . $general . $max_width_cfg : 'uk-panel' . $zindex_cls . $general . $max_width_cfg;
		$panel_cls .= ($card && $card != 'hover' && $panel_link) ? ' uk-card-hover' : '';
		$panel_cls .= ($card) ? ' uk-card-body uk-margin-remove-first-child' : '';

		$panel_cls .= (empty ( $card ) && empty ( $panel_content_padding )) ? ' uk-margin-remove-first-child' : '';

		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';
		$icon_rating = (isset ( $settings->icon_rating ) && $settings->icon_rating) ? $settings->icon_rating : '';

		$image_loading = (isset ( $settings->image_loading ) && $settings->image_loading) ? 1 : 0;
		$image_loading_init = $image_loading ? '' : ' loading="lazy"';

		$output = '';
		$client_rating = '';

		if (! empty ( $client_review )) {
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
			} else {
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
				$client_rating .= (empty ( $icon_rating )) ? '<i class="voted far fa-star" aria-hidden="true"></i>' : '<span class="voted el-icon" uk-icon="icon: star;"></span>';
			}
		}

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		if ($panel_link && $link) {
			$output .= '<a class="' . $panel_cls . '" href="' . $link . '"' . $link_target . $animation . '>';
		} else {
			$output .= '<div class="' . $panel_cls . '"' . $animation . '>';
		}

		if ($header_alignment == 'bottom' && $message) {
			$output .= '<div class="ui-content uk-panel' . $content_style . $header_margin_top . '">';
			$output .= $message;
			$output .= '</div>';
		}

		$output .= ($card_content_padding) ? '<div class="' . $card_content_padding . '">' : '';

		if (empty ( $image_position ) || $image_position == 'right') {
			$output .= '<div class="uk-child-width-expand' . $head_alignment . $vertical_alignment_cls . $image_grid_column_gap . '" uk-grid>';
		}

		if ((empty ( $image_position ) || $image_position == 'right') && $avatar_src) {

			$output .= '<div class="ui-item ' . $image_grid_cls . $image_grid_cls_bp . $cls_class . $img_center_cls . '">';

			if ($image_link && $link && $panel_link == false) {
				$output .= '<a class="uk-link-reset" href="' . $link . '"' . $link_target . '>';
			}
			$output .= '<img' . $image_width . ' class="uk-display-inline-block' . $avatar_shape . $image_svg_color . '" src="' . $avatar_src . '" alt="' . str_replace ( '"', '', $alt_text ) . '"' . $image_svg_inline_cls . $image_loading_init . '>';
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
				$output .= '<a' . $link_title_hover . ' href="' . $link . '"' . $link_target . '>';
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

		if (! empty ( $client_review ) && empty ( $avatar_src )) {
			$output .= '<div class="ui-review">';
			$output .= $client_rating;
			$output .= '</div>';
		}

		if (! empty ( $client_review ) && $rating_alignment != 'image' && $avatar_src) {
			$output .= '<div class="ui-review">';
			$output .= $client_rating;
			$output .= '</div>';
		}

		if (empty ( $image_position ) || $image_position == 'right') {
			$output .= '</div>';
		}

		$output .= '</div>';

		if (empty ( $header_alignment ) && $message) {

			$output .= '<div class="ui-content uk-panel' . $content_style . '">';
			$output .= $message;
			$output .= '</div>';
		}

		$output .= ($card_content_padding) ? '</div>' : '';

		if ($panel_link && $link) {
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
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;
		$css = '';
		$icon_style = '';
		$card_style = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_background = (isset ( $settings->card_background ) && $settings->card_background) ? 'background-color: ' . $settings->card_background . ';' : '';
		$card_color = (isset ( $settings->card_color ) && $settings->card_color) ? 'color: ' . $settings->card_color . ';' : '';
		$title_color = (isset ( $settings->title_text_color ) && $settings->title_text_color) ? $settings->title_text_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? $settings->content_style : '';
		$content_size = (isset ( $settings->content_size ) && $settings->content_size) ? $settings->content_size : '';

		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-author {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$css .= $addon_id . ' .ui-meta {' . $custom_meta_color . '}';
		}
		if ($content_color) {
			$css .= $addon_id . ' .ui-content, ' . $addon_id . ' .ui-content blockquote {' . $content_color . '}';
		}

		if ($content_size && $content_style == 'custom') {
			$css .= $addon_id . ' .ui-content {';
			$css .= 'font-size:' . $content_size . 'px';
			$css .= '}';
		}

		$icon_style .= (isset ( $settings->icon_color ) && $settings->icon_color) ? 'color: ' . $settings->icon_color . ';' : '';

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
	public static function getFrontendEditor() {
		$output = '
		<style type="text/css">
		<# if(data.card_style == "custom" && data.card_background) { #>
			#jpb-addon-{{ data.id }} .uk-card-custom {
				background-color: {{ data.card_background }};
			}
		<# } #>

		<# if(_.isEmpty(data.title_text_color) && data.custom_title_color) { #>
			#jpb-addon-{{ data.id }} .ui-author {
				color: {{ data.custom_title_color }};
			}
		<# } #>

		<# if(_.isEmpty(data.meta_color) && data.custom_meta_color) { #>
			#jpb-addon-{{ data.id }} .ui-meta {
				color: {{ data.custom_meta_color }};
			}
		<# } #>
		<# if(data.content_size && data.content_style == "custom") { #>
			#jpb-addon-{{ data.id }} .ui-content {
				font-size: {{ data.content_size }}px;
			}
		<# } #>
		<# if(data.card_style == "custom" && data.card_background) { #>
			#jpb-addon-{{ data.id }} .uk-card-custom {
				background-color: {{ data.card_background }};
			}
		<# } #>
		<# if(data.card_styles == "custom" && data.card_background) { #>
			#jpb-addon-{{ data.id }} .uk-card-custom {
				background-color: {{ data.card_background }};
			}
		<# } #>
		<# if(data.card_styles == "custom" && data.card_color) { #>
			#jpb-addon-{{ data.id }} .uk-card-custom.uk-card-body,
			#jpb-addon-{{ data.id }} .uk-card-custom .ui-author,
			#jpb-addon-{{ data.id }} .uk-card-custom .ui-meta {
				color: {{ data.card_color }};
			}
		<# } #>
		<# if(data.content_color) { #>
			#jpb-addon-{{ data.id }} .ui-content,
			#jpb-addon-{{ data.id }} .ui-content blockquote {
				color: {{ data.content_color }};
			}
		<# } #>
		<# if(data.icon_color) { #>
			#jpb-addon-{{ data.id }} .ui-review .voted {
				color: {{ data.icon_color }};
			}
		<# } #>
		</style>
		<#
		let heading_addon_margin = data.heading_addon_margin || "";

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
		let card_size  = data.card_size ? " " + data.card_size : "";

		let panel_content_padding = ( data.card_content_padding ) ? data.card_content_padding : "";
		let card_content_padding   = ( panel_content_padding && _.isEmpty(card) ) ? "uk-padding"+((panel_content_padding == "default") ? " " : "-" + panel_content_padding + " ") : "";

		let message       = ( data.message ) ? data.message : "";
		let client_review = ( data.client_review ) ? data.client_review : "";
		let name          = ( data.name ) ? data.name : "";
		let company       = ( data.company ) ? data.company : "";
		var avatar = {}
		if (typeof data.avatar !== "undefined" && typeof data.avatar.src !== "undefined") {
			avatar = data.avatar
		} else {
			avatar = {src: data.avatar}
		}
		
		let alt_text     = ( data.alt_text ) ? data.alt_text : "";
		let avatar_shape = ( data.avatar_shape ) ? " " + data.avatar_shape : "";
		let link         = ( data.link ) ? data.link : "";
		var link_target = (data.link_target) ? \'target="_blank"\' : "";

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

		let text_alignment = data.text_alignment ? " uk-text-" + data.text_alignment : "";
		let text_breakpoint = (data.alignment && data.text_breakpoint) ? "@" + data.text_breakpoint : "";
		let text_alignment_fallback = (data.alignment && data.text_breakpoint && data.text_alignment_fallback) ? " uk-text-" + data.text_alignment_fallback : "";
		
		general += text_alignment + text_breakpoint + text_alignment_fallback + max_width_cfg;

		let head_alignment          = ( data.text_alignment ) ? " uk-flex-"+data.text_alignment : "";
		let head_breakpoint         = ( head_alignment ) ? ( ( data.head_breakpoint ) ? "@" +data.head_breakpoint : "" ) : "";
		let head_alignment_fallback = ( head_alignment && head_breakpoint ) ? ( ( data.head_alignment_fallback ) ? " uk-flex-"+data.head_alignment_fallback : "" ) : "";
		head_alignment         += head_breakpoint + head_alignment_fallback;

		let header_alignment = ( data.header_alignment ) ? data.header_alignment : "";
		let vertical_alignment = ( data.vertical_alignment ) ? 1 : 0;

		let vertical_alignment_cls = ( vertical_alignment ) ? " uk-flex-middle" : "";

		let image_grid_column_gap = ( data.image_grid_column_gap ) ? " uk-grid-column-" + data.image_grid_column_gap : "";

		let heading_selector = data.heading_selector || "h3";
		 
		var heading_style    = ( data.title_style ) ? " uk-" + data.title_style : "";
		heading_style   += ( data.title_text_color ) ? " uk-text-" + data.title_text_color : "";
		heading_style   += ( data.title_text_transform ) ? " uk-text-" + data.title_text_transform : "";
		heading_style   += ( data.title_margin_top ) ? " uk-margin-" + data.title_margin_top +"-top" : " uk-margin-top";
		let title_decoration = data.title_decoration ? " " + data.title_decoration : "";
		
		let heading_style_cls      = data.title_style ? " uk-" + data.title_style : "";
		let heading_style_cls_init = ( _.isEmpty( heading_style_cls ) ) ? " uk-card-title" : "";

		// Meta.
		let meta_element = data.meta_element || "div";
		let meta_style_cls = data.meta_style ? data.meta_style : "";

		let meta_style  = data.meta_style ? " uk-" + data.meta_style : "";
		meta_style += ( data.meta_font_weight ) ? " uk-text-"+ data.meta_font_weight : "";
		meta_style += data.meta_text_color ? " uk-text-" + data.meta_text_color : "";
		meta_style += data.meta_text_transform ? " uk-text-" + data.meta_text_transform : "";
		meta_style += data.meta_margin_top ? " uk-margin-" + data.meta_margin_top + "-top" : " uk-margin-top";

		// Remove margin for heading element
		if ( meta_element !== "div" || ( meta_style_cls && meta_style_cls !== "text-meta" ) ) {
			meta_style += " uk-margin-remove-bottom";
		}

		// Content.
		let content_style  = data.content_style ? " uk-" + data.content_style : "";

		let content_dropcap = data.content_dropcap ? 1 : 0;
		content_style	+= content_dropcap ? " uk-dropcap" : "";

		content_style += data.content_text_transform ? " uk-text-" + data.content_text_transform : "";

		let content_column = data.content_column ? " uk-column-"+data.content_column : "";
		let content_column_breakpoint = (data.content_column && data.content_column_breakpoint) ? "@"+data.content_column_breakpoint : "";
		let content_column_divider = (data.content_column && data.content_column_divider) ? " uk-column-divider" : "";
		
		content_style	+= content_column + content_column_breakpoint + content_column_divider;

		content_style += _.isEmpty( header_alignment ) ? ( data.content_margin_top ? " uk-margin-" + data.content_margin_top + "-top" : " uk-margin-top" ) : "";

		let image_position = ( data.position ) ? data.position : "";

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
		let animation_repeat = (animation && data.animation_repeat) ? "; repeat: true;" : "";
		
		let zindex_cls = (animation == "parallax" && data.parallax_zindex) ? " uk-position-z-index uk-position-relative" : "";	

		if (animation == "parallax") {
			animation = ` uk-parallax=${horizontal}${vertical}${scale}${rotate}${opacity}${easing_cls}${target_cls}${breakpoint_cls}${viewport_cls}`;
		} else if (animation) {
			animation = ` uk-scrollspy="cls:uk-animation-${animation}${animation_repeat}"`;
		}

		let rating_alignment = ( data.rating_alignment ) ? data.rating_alignment : "";
		let center_cls       = ( text_alignment == "center" ) ? " uk-text-center" : "";
		let img_center_cls = ( rating_alignment == "image" && _.isEmpty( text_alignment ) ) ? " uk-text-center" : "";
		let header_margin_top = ( data.header_margin_top ) ? " uk-margin-"+data.header_margin_top : " uk-margin";
		
		let image_width       = ( data.avatar_width ) ? \' width="\' + data.avatar_width + \'"\' : "";

		// New options.

		let image_grid_cls    = ( data.image_grid_width ) ? "uk-width-"+data.image_grid_width : "";
		let image_grid_cls_bp = ( data.image_grid_breakpoint ) ? "@"+data.image_grid_breakpoint : "";
		let cls_class         = ( image_position == "right" ) ? " uk-flex-last"+image_grid_cls_bp : "";

		let image_svg_inline     = ( data.image_svg_inline ) ? data.image_svg_inline : "";
		let image_svg_inline_cls = ( image_svg_inline ) ? " uk-svg" : "";
		let image_svg_color      = ( image_svg_inline ) ? ( ( data.image_svg_color ) ? " uk-text-"+data.image_svg_color : "" ) : "";

		let image_link       = ( data.image_link ) ? 1 : "";
		let panel_link       = ( data.panel_link ) ? 1 : "";
		let link_title       = ( data.link_title ) ? 1 : "";
		let link_title_hover = ( data.title_hover_style ) ? \' class="uk-link-\' + data.title_hover_style + \'"\' : "";

		let card_inverse = data.card_style == "primary" || data.card_style == "secondary" ? " uk-light" : "";

		var panel_cls = (card) ? "uk-card uk-card-" + card + card_size + card_inverse + zindex_cls + general : "uk-panel"+zindex_cls+general;
		
		panel_cls += (card && card != "hover" && panel_link) ? " uk-card-hover" : "";
		panel_cls += ( card ) ? " uk-card-body uk-margin-remove-first-child" : "";
		panel_cls += (_.isEmpty( card ) && _.isEmpty(panel_content_padding)) ? " uk-margin-remove-first-child" : "";

		let font_weight   = ( data.font_weight ) ? " uk-text-"+data.font_weight : "";
		let icon_rating   = ( data.icon_rating ) ? data.icon_rating : "";

		var client_rating = "";
		if ( client_review == 1 ) {
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
		} else if ( client_review == 2 ) {
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
		} else if ( client_review == 3 ) {
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
		} else if ( client_review == 4 ) {
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="far fa-star" aria-hidden="true"></i>\' : \'<span class="el-icon" uk-icon="icon: star;"></span>\';
		} else if ( client_review == 5 ) {
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
			client_rating += ( _.isEmpty( icon_rating ) ) ? \'<i class="voted far fa-star" aria-hidden="true"></i>\' : \'<span class="voted el-icon" uk-icon="icon: star;"></span>\';
		}
		#>

		<# if( !_.isEmpty( data.title_addon ) ){ #>
			<{{ title_heading_selector }} class="tm-addon-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span> <# } #>
					{{{ data.title_addon }}}
			 	<# if (title_heading_decoration == " uk-heading-line") { #></span> <# } #>
			</{{ title_heading_selector }}>
		<# } #>

		<# if( panel_link && link ) { #>
			<a class="{{ panel_cls }}" href="{{ link }}"{{{ link_target }}}{{{ animation }}}>
		<# } else {  #>
			<div class="{{ panel_cls }}"{{{ animation }}}>
		<# } #>

		<# if ( header_alignment == "bottom" && message ) { #>
			<div class="ui-content uk-panel{{ content_style }}{{ header_margin_top }}">
				{{{message}}}
			</div>
		<# } #>

		<# if(card_content_padding) { #>
			<div class="{{ card_content_padding }}">
		<# } #>

		<# if ( _.isEmpty( image_position ) || image_position == "right" ) { #>
			<div class="uk-child-width-expand{{ head_alignment }}{{ vertical_alignment_cls }}{{ image_grid_column_gap }}" uk-grid>
		<# } #>
 
		<# if ( ( _.isEmpty( image_position ) || image_position == "right" ) && avatar.src ) { #>
			<div class="ui-item {{ image_grid_cls }}{{ image_grid_cls_bp }}{{ cls_class }}{{ img_center_cls }}">
			<# if ( image_link && link && panel_link == false ) { #>
				<a class="uk-link-reset" href="{{ link }}"{{{ link_target }}}>
			<# } #>

			<# if(avatar.src.indexOf("http://") == -1 && avatar.src.indexOf("https://") == -1){ #>
				<img{{{ image_width }}} class="uk-display-inline-block{{ avatar_shape }}{{ image_svg_color }}" src=\'{{ pagebuilder_base + avatar.src }}\' alt="{{ alt_text.replace(/"/g, "") }}"{{ image_svg_inline_cls }}>
			<# } else { #>
				<img{{{ image_width }}} class="uk-display-inline-block{{ avatar_shape }}{{ image_svg_color }}" src=\'{{ avatar.src }}\' alt="{{ alt_text.replace(/"/g, "") }}"{{image_svg_inline_cls}}>
			<# } #>
			
			<# if ( image_link && link && panel_link == false ) { #>
				</a>
			<# } #>

			<# if ( !_.isEmpty( client_review ) && rating_alignment == "image" ) { #>
				<div class="ui-review uk-margin-small-top">
				 {{{ client_rating }}}
				</div>
			<# } #>

			</div>
		<# } #>

		<div class="ui-item{{ center_cls }}">

			<# if ( name ) { #>
				<{{ heading_selector }} class="ui-author uk-margin-remove-bottom{{ heading_style }}{{ heading_style_cls_init }}{{ font_weight }}">
				<# if ( link_title && link && panel_link == false ) { #>
					<a{{{ link_title_hover }}} href="{{ link }}"{{{ link_target }}}>
				<# } #>
				{{{ name }}}
				<# if ( link_title && link && panel_link == false ) { #>
					</a>
				<# } #>
				</{{ heading_selector }}>
			<# } #>

			<# if ( company ) { #>
				<{{ meta_element }} class="ui-meta{{ meta_style }}">
				{{{ company }}}
				</{{ meta_element }}>
			<# } #>

			<# if ( !_.isEmpty( client_review ) && _.isEmpty(avatar.src) ) { #>
				<div class="ui-review">
				{{{ client_rating }}}
				</div>
			<# } #>

			<# if ( !_.isEmpty( client_review ) && rating_alignment != "image" && avatar.src ) { #>
				<div class="ui-review">
				{{{ client_rating }}}
				</div>
			<# } #>

			<# if ( _.isEmpty( image_position ) || image_position == "right" ) { #>
				</div>
			<# } #>

		</div>

		<# if ( _.isEmpty(header_alignment) && message ) { #>
			<div class="ui-content uk-panel{{ content_style }}">
				{{{message}}}
			</div>
		<# } #>

		<# if(card_content_padding) { #>
			</div>
		<# } #>

		<# if(panel_link && link) { #>
			</a>
		<# } else { #>
			</div>
		<# } #>

		';
		return $output;
	}
}
