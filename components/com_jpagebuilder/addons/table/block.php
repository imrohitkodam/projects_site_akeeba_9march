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
class JpagebuilderAddonTable extends JpagebuilderAddons {
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

		$highlight_row = (isset ( $settings->highlight_row ) && $settings->highlight_row) ? 1 : 0;
		$remove_left_right_padding = (isset ( $settings->remove_left_right_padding ) && $settings->remove_left_right_padding) ? 1 : 0;
		$vertical_alignment = (isset ( $settings->vertical_alignment ) && $settings->vertical_alignment) ? 1 : 0;

		$table_cls = (isset ( $settings->style ) && $settings->style) ? ' uk-table-' . $settings->style : '';

		$table_cls .= ($highlight_row) ? ' uk-table-hover' : '';
		$table_cls .= ($remove_left_right_padding) ? ' uk-table-justify' : '';

		$table_cls .= ($vertical_alignment) ? ' uk-table-middle' : '';

		$table_cls .= (isset ( $settings->size ) && $settings->size) ? ' uk-table-' . $settings->size : '';

		$responsive = (isset ( $settings->responsive ) && $settings->responsive) ? $settings->responsive : '';

		$title_width = (isset ( $settings->table_width_title ) && $settings->table_width_title) ? $settings->table_width_title : '';
		$meta_width = (isset ( $settings->table_width_meta ) && $settings->table_width_meta) ? $settings->table_width_meta : '';
		$content_width = (isset ( $settings->table_width_content ) && $settings->table_width_content) ? $settings->table_width_content : '';

		$title_width_cls = ($title_width) ? ' class="' . (($title_width == 'shrink') ? 'uk-text-nowrap uk-table-shrink"' : 'uk-width-' . $title_width . '"') : '';
		$meta_width_cls = ($meta_width) ? ' class="' . (($meta_width == 'shrink') ? 'uk-text-nowrap uk-table-shrink"' : 'uk-width-' . $meta_width . '"') : '';
		$content_width_cls = ($content_width) ? ' class="' . (($content_width == 'shrink') ? 'uk-text-nowrap uk-table-shrink"' : 'uk-width-' . $content_width . '"') : '';

		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$meta = (isset ( $settings->meta ) && $settings->meta) ? $settings->meta : '';
		$content = (isset ( $settings->content ) && $settings->content) ? $settings->content : '';
		$image = (isset ( $settings->image ) && $settings->image) ? $settings->image : '';
		$link = (isset ( $settings->link ) && $settings->link) ? $settings->link : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		// New style options.

		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->meta_color ) && $settings->meta_color) ? ' uk-text-' . $settings->meta_color : '';

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';

		$attribs = (isset ( $settings->target ) && $settings->target) ? ' target="' . $settings->target . '"' : '';

		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_size = (isset ( $settings->button_size ) && $settings->button_size) ? ' ' . $settings->button_size : '';
		$button_width = (isset ( $settings->expand_width ) && $settings->expand_width) ? ' uk-width-1-1' : '';

		$button_style_cls = '';
		if (empty ( $button_style )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size . $button_width;
		} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
			$button_style_cls .= 'uk-' . $button_style;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $button_style . $button_size . $button_width;
		}

		$all_button_title = (isset ( $settings->all_button_title ) && $settings->all_button_title) ? $settings->all_button_title : 'Learn more';

		$image_styles = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '';
		$image_styles .= (isset ( $settings->image_border ) && $settings->image_border) ? ' uk-border-' . $settings->image_border : '';
		$image_width = (isset ( $settings->image_width ) && $settings->image_width) ? $settings->image_width : '';
		$image_width_cls = '';
		if (! empty ( $image_width )) {
			$image_width_cls .= ' width=' . $image_width . '';
		}

		$order = (isset ( $settings->order ) && $settings->order) ? $settings->order : '';

		$hide_title = (isset ( $settings->hide_title ) && $settings->hide_title) ? 1 : 0;
		$hide_meta = (isset ( $settings->hide_meta ) && $settings->hide_meta) ? 1 : 0;
		$hide_content = (isset ( $settings->hide_content ) && $settings->hide_content) ? 1 : 0;
		$hide_link = (isset ( $settings->hide_link ) && $settings->hide_link) ? 1 : 0;
		$hide_image = (isset ( $settings->hide_image ) && $settings->hide_image) ? 1 : 0;

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

		$output = '';

		$output .= '<div class="ui-table' . $text_alignment . $general . $max_width_cfg . '" ' . $animation . '>';
		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}
		if ($responsive == 'overflow') {
			$output .= '<div class="uk-overflow-auto">';
			$output .= '<table class="uk-table' . $table_cls . '">';
		} else {
			$output .= '<table class="uk-table uk-table-responsive' . $table_cls . '">';
		}

		if ($meta || $title || $content || $image || $link) {
			$output .= '<thead>';
			$output .= '<tr>';

			if (empty ( $order )) {
				if (! $hide_meta) {
					$output .= '<th class="uk-text-nowrap">' . $meta . '</th>';
				}

				if (! $hide_image) {
					$output .= '<th class="uk-text-nowrap">' . $image . '</th>';
				}

				if (! $hide_title) {
					$output .= '<th class="uk-text-nowrap">' . $title . '</th>';
				}
				if (! $hide_content) {
					$output .= '<th>' . $content . '</th>';
				}
				if (! $hide_link) {
					$output .= '<th class="uk-text-nowrap">' . $link . '</th>';
				}
			} elseif ($order == '2') {
				if (! $hide_title) {
					$output .= '<th class="uk-text-nowrap">' . $title . '</th>';
				}
				if (! $hide_image) {
					$output .= '<th class="uk-text-nowrap">' . $image . '</th>';
				}

				if (! $hide_meta) {
					$output .= '<th class="uk-text-nowrap">' . $meta . '</th>';
				}
				if (! $hide_content) {
					$output .= '<th>' . $content . '</th>';
				}
				if (! $hide_link) {
					$output .= '<th class="uk-text-nowrap">' . $link . '</th>';
				}
			} elseif ($order == '3') {
				if (! $hide_image) {
					$output .= '<th class="uk-text-nowrap">' . $image . '</th>';
				}
				if (! $hide_title) {
					$output .= '<th class="uk-text-nowrap">' . $title . '</th>';
				}
				if (! $hide_content) {
					$output .= '<th>' . $content . '</th>';
				}
				if (! $hide_meta) {
					$output .= '<th class="uk-text-nowrap">' . $meta . '</th>';
				}
				if (! $hide_link) {
					$output .= '<th class="uk-text-nowrap">' . $link . '</th>';
				}
			} elseif ($order == '4') {
				if (! $hide_image) {
					$output .= '<th class="uk-text-nowrap">' . $image . '</th>';
				}
				if (! $hide_title) {
					$output .= '<th class="uk-text-nowrap">' . $title . '</th>';
				}
				if (! $hide_meta) {
					$output .= '<th class="uk-text-nowrap">' . $meta . '</th>';
				}
				if (! $hide_content) {
					$output .= '<th>' . $content . '</th>';
				}
				if (! $hide_link) {
					$output .= '<th class="uk-text-nowrap">' . $link . '</th>';
				}
			} elseif ($order == '5') {
				if (! $hide_title) {
					$output .= '<th class="uk-text-nowrap">' . $title . '</th>';
				}
				if (! $hide_meta) {
					$output .= '<th class="uk-text-nowrap">' . $meta . '</th>';
				}
				if (! $hide_content) {
					$output .= '<th>' . $content . '</th>';
				}
				if (! $hide_link) {
					$output .= '<th class="uk-text-nowrap">' . $link . '</th>';
				}
				if (! $hide_image) {
					$output .= '<th class="uk-text-nowrap">' . $image . '</th>';
				}
			} elseif ($order == '6') {
				if (! $hide_meta) {
					$output .= '<th class="uk-text-nowrap">' . $meta . '</th>';
				}
				if (! $hide_title) {
					$output .= '<th class="uk-text-nowrap">' . $title . '</th>';
				}
				if (! $hide_content) {
					$output .= '<th>' . $content . '</th>';
				}
				if (! $hide_link) {
					$output .= '<th class="uk-text-nowrap">' . $link . '</th>';
				}
				if (! $hide_image) {
					$output .= '<th class="uk-text-nowrap">' . $image . '</th>';
				}
			}

			$output .= '</tr>';
			$output .= '</thead>';
		}

		$output .= '<tbody>';

		foreach ( $settings->ui_table_item as $key => $value ) {

			$table_title = (isset ( $value->table_title ) && $value->table_title) ? $value->table_title : '';
			$table_meta = (isset ( $value->table_meta ) && $value->table_meta) ? $value->table_meta : '';
			$table_content = (isset ( $value->table_content ) && $value->table_content) ? $value->table_content : '';

			$image = (isset ( $value->image ) && $value->image) ? $value->image : '';
			$image_src = isset ( $image->src ) ? $image->src : $image;
			if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
				$image_src = $image_src;
			} elseif ($image_src) {
				$image_src = Uri::base ( true ) . '/' . $image_src;
			}
			$alt_text = (isset ( $value->alt_text ) && $value->alt_text) ? $value->alt_text : '';
			$alt_text_init = (empty ( $alt_text )) ? 'alt="' . str_replace ( '"', '', $table_title ) . '"' : 'alt="' . str_replace ( '"', '', $alt_text ) . '"';

			$button_title = (isset ( $value->button_title ) && $value->button_title) ? $value->button_title : '';

			if (empty ( $button_title )) {
				$button_title .= $all_button_title;
			}

			$title_link = (isset ( $value->link ) && $value->link) ? $value->link : '';

			$check_target = (isset ( $settings->target ) && $settings->target) ? $settings->target : '';
			$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';
			$media = '';
			if ($image_src) {
				$media .= '<img class="el-image uk-preserve-width' . $image_styles . '" src="' . $image_src . '" ' . $alt_text_init . $image_width_cls . '>';
			}

			$output .= '<tr class="el-item">';

			if (empty ( $order )) {

				if (! $hide_meta) {
					$output .= '<td' . $meta_width_cls . '>';

					if (! empty ( $table_meta )) {
						$output .= '<div class="el-meta' . $meta_style . '">' . $table_meta . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_image) {
					$output .= '<td class="uk-table-shrink">' . $media . '</td>';
				}

				if (! $hide_title) {
					$output .= '<td' . $title_width_cls . '>';

					if (! empty ( $table_title )) {
						$output .= '<div class="el-title' . $heading_style . '">' . $table_title . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_content) {

					$output .= '<td' . $content_width_cls . '>';

					if (! empty ( $table_content )) {
						$output .= '<div class="el-content' . $content_style . '">' . $table_content . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_link) {
					$output .= '<td class="uk-text-nowrap uk-table-shrink">';

					$output .= ($title_link) ? '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a>' : '';

					$output .= '</td>';
				}
			} elseif ($order == '2') {

				if (! $hide_title) {
					$output .= '<td' . $title_width_cls . '>';

					if (! empty ( $table_title )) {
						$output .= '<div class="el-title' . $heading_style . '">' . $table_title . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_image) {
					$output .= '<td class="uk-table-shrink">' . $media . '</td>';
				}

				if (! $hide_meta) {
					$output .= '<td' . $meta_width_cls . '>';

					if (! empty ( $table_meta )) {
						$output .= '<div class="el-meta' . $meta_style . '">' . $table_meta . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_content) {

					$output .= '<td' . $content_width_cls . '>';

					if (! empty ( $table_content )) {
						$output .= '<div class="el-content' . $content_style . '">' . $table_content . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_link) {
					$output .= '<td class="uk-text-nowrap uk-table-shrink">';

					$output .= ($title_link) ? '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a>' : '';

					$output .= '</td>';
				}
			} elseif ($order == '3') {

				if (! $hide_image) {
					$output .= '<td class="uk-table-shrink">' . $media . '</td>';
				}

				if (! $hide_title) {
					$output .= '<td' . $title_width_cls . '>';

					if (! empty ( $table_title )) {
						$output .= '<div class="el-title' . $heading_style . '">' . $table_title . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_content) {

					$output .= '<td' . $content_width_cls . '>';

					if (! empty ( $table_content )) {
						$output .= '<div class="el-content' . $content_style . '">' . $table_content . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_meta) {
					$output .= '<td' . $meta_width_cls . '>';

					if (! empty ( $table_meta )) {
						$output .= '<div class="el-meta' . $meta_style . '">' . $table_meta . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_link) {
					$output .= '<td class="uk-text-nowrap uk-table-shrink">';

					$output .= ($title_link) ? '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a>' : '';

					$output .= '</td>';
				}
			} elseif ($order == '4') {

				if (! $hide_image) {
					$output .= '<td class="uk-table-shrink">' . $media . '</td>';
				}

				if (! $hide_title) {
					$output .= '<td' . $title_width_cls . '>';

					if (! empty ( $table_title )) {
						$output .= '<div class="el-title' . $heading_style . '">' . $table_title . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_meta) {
					$output .= '<td' . $meta_width_cls . '>';

					if (! empty ( $table_meta )) {
						$output .= '<div class="el-meta' . $meta_style . '">' . $table_meta . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_content) {

					$output .= '<td' . $content_width_cls . '>';

					if (! empty ( $table_content )) {
						$output .= '<div class="el-content' . $content_style . '">' . $table_content . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_link) {
					$output .= '<td class="uk-text-nowrap uk-table-shrink">';

					$output .= ($title_link) ? '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a>' : '';

					$output .= '</td>';
				}
			} elseif ($order == '5') {

				if (! $hide_title) {
					$output .= '<td' . $title_width_cls . '>';

					if (! empty ( $table_title )) {
						$output .= '<div class="el-title' . $heading_style . '">' . $table_title . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_meta) {
					$output .= '<td' . $meta_width_cls . '>';

					if (! empty ( $table_meta )) {
						$output .= '<div class="el-meta' . $meta_style . '">' . $table_meta . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_content) {

					$output .= '<td' . $content_width_cls . '>';

					if (! empty ( $table_content )) {
						$output .= '<div class="el-content' . $content_style . '">' . $table_content . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_link) {
					$output .= '<td class="uk-text-nowrap uk-table-shrink">';

					$output .= ($title_link) ? '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a>' : '';

					$output .= '</td>';
				}

				if (! $hide_image) {
					$output .= '<td class="uk-table-shrink">' . $media . '</td>';
				}
			} elseif ($order == '6') {

				if (! $hide_meta) {
					$output .= '<td' . $meta_width_cls . '>';

					if (! empty ( $table_meta )) {
						$output .= '<div class="el-meta' . $meta_style . '">' . $table_meta . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_title) {
					$output .= '<td' . $title_width_cls . '>';

					if (! empty ( $table_title )) {
						$output .= '<div class="el-title' . $heading_style . '">' . $table_title . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_content) {

					$output .= '<td' . $content_width_cls . '>';

					if (! empty ( $table_content )) {
						$output .= '<div class="el-content' . $content_style . '">' . $table_content . '</div>';
					}
					$output .= '</td> ';
				}

				if (! $hide_link) {
					$output .= '<td class="uk-text-nowrap uk-table-shrink">';

					$output .= ($title_link) ? '<a class="' . $button_style_cls . '" href="' . $title_link . '"' . $attribs . $render_linkscroll . '>' . $button_title . '</a>' : '';

					$output .= '</td>';
				}

				if (! $hide_image) {
					$output .= '<td class="uk-table-shrink">' . $media . '</td>';
				}
			}

			$output .= '</tr>';
		}

		$output .= '</tbody>';

		if ($responsive == 'overflow') {
			$output .= '</table>';
			$output .= '</div>';
		} else {
			$output .= '</table>';
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

		$head_background = (isset ( $settings->head_background ) && $settings->head_background) ? 'background-color: ' . $settings->head_background . ';' : '';
		$head_color = (isset ( $settings->head_color ) && $settings->head_color) ? 'color: ' . $settings->head_color . ';' : '';

		$head_background = (isset ( $settings->head_background ) && $settings->head_background) ? 'background-color: ' . $settings->head_background . ';' : '';
		$head_color = (isset ( $settings->head_color ) && $settings->head_color) ? 'color: ' . $settings->head_color . ';' : '';

		$table_background = (isset ( $settings->table_background ) && $settings->table_background) ? 'background-color: ' . $settings->table_background . ';' : '';
		$table_color = (isset ( $settings->table_color ) && $settings->table_color) ? 'color: ' . $settings->table_color . ';' : '';

		$table_style = (isset ( $settings->style ) && $settings->style) ? $settings->style : '';

		$css = '';

		if (empty ( $table_style )) {
			if ($head_background) {
				$css .= $addon_id . ' .uk-table thead tr {' . $head_background . '}';
			}
			if ($head_color) {
				$css .= $addon_id . ' .uk-table thead tr th {' . $head_color . '}';
			}
			if ($table_background) {
				$css .= $addon_id . ' .uk-table tbody {' . $table_background . '}';
			}
			if ($table_color) {
				$css .= $addon_id . ' .uk-table tbody tr td {' . $table_color . '}';
			}
		}

		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .el-title {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$css .= $addon_id . ' .el-meta {' . $custom_meta_color . '}';
		}
		if ($content_color) {
			$css .= $addon_id . ' .el-content {' . $content_color . '}';
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
