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
class JpagebuilderAddonCardheader extends JpagebuilderAddons {
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

		// Options.

		$avatar_shape = (isset ( $settings->avatar_shape ) && $settings->avatar_shape) ? ' ' . $settings->avatar_shape : '';

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

		$general .= $text_alignment . $text_breakpoint . $text_alignment_fallback;
		$general .= $max_width_cfg;

		// New option.

		$grid_parallax = (isset ( $settings->parallax ) && $settings->parallax) ? $settings->parallax : '';
		$parallax = ($grid_parallax) ? 'parallax: ' . $grid_parallax . '' : '';

		$column_align = (isset ( $settings->grid_column_align ) && $settings->grid_column_align) ? 1 : 0;
		$row_align = (isset ( $settings->grid_row_align ) && $settings->grid_row_align) ? 1 : 0;

		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';
		$grid_row_gap = (isset ( $settings->grid_row_gap ) && $settings->grid_row_gap) ? $settings->grid_row_gap : '';

		$divider = (isset ( $settings->divider ) && $settings->divider) ? 1 : 0;

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

		$card = (isset ( $settings->card_style ) && $settings->card_style) ? ' uk-card-' . $settings->card_style : '';
		$card_width = (isset ( $settings->card_width ) && $settings->card_width) ? ' uk-margin-auto uk-width-' . $settings->card_width : '';
		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? ' ' . $settings->card_size : '';

		$card_init = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_inverse = $card_init == 'primary' || $card_init == 'secondary' ? ' uk-light' : '';

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

		// Meta.

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->meta_color ) && $settings->meta_color) ? ' uk-text-' . $settings->meta_color : '';
		$meta_style .= (isset ( $settings->meta_margin_top ) && $settings->meta_margin_top) ? ' uk-margin-' . $settings->meta_margin_top . '-top' : ' uk-margin-top';

		$meta_alignment = (isset ( $settings->meta_alignment ) && $settings->meta_alignment) ? $settings->meta_alignment : '';

		// Content.

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$btn_styles = (isset ( $settings->link_button_style ) && $settings->link_button_style) ? $settings->link_button_style : '';
		$button_size = (isset ( $settings->link_button_size ) && $settings->link_button_size) ? ' ' . $settings->link_button_size : '';

		$button_style_cls = '';

		if (empty ( $btn_styles )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size;
		} elseif ($btn_styles == 'link' || $btn_styles == 'link-muted' || $btn_styles == 'link-text') {
			$button_style_cls .= 'uk-' . $btn_styles;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $btn_styles . $button_size;
		}

		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? ' uk-margin-' . $settings->button_margin_top . '-top' : ' uk-margin-top';
		$all_button_title = (isset ( $settings->all_button_title ) && $settings->all_button_title) ? $settings->all_button_title : 'Learn more';

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

		$image_loading = (isset ( $settings->image_loading ) && $settings->image_loading) ? 1 : 0;
		$image_loading_init = $image_loading ? '' : ' loading="lazy"';

		$output = '';
		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			if ($title_heading_decoration == ' uk-heading-line') {
				$output .= '<span>';
				$output .= nl2br ( $title_addon );
				$output .= '</span>';
			} else {
				$output .= nl2br ( $title_addon );
			}
			$output .= '</' . $title_heading_selector . '>';
		}
		$output .= '<div class="ui-card-header' . $zindex_cls . $general . '">';

		if ($parallax) {
			$output .= '<div class="uk-grid-match ' . $grid . '" uk-grid="' . $parallax . '">';
		} else {
			$output .= '<div class="uk-grid-match ' . $grid . '" uk-grid ' . $animation . '>';
		}

		foreach ( $settings->ui_cardheader_item as $key => $value ) {
			$message = (isset ( $value->message ) && $value->message) ? $value->message : '';
			$button_title = (isset ( $value->button_title ) && $value->button_title) ? $value->button_title : '';
			$ariaLabel = ! empty ( $value->link_aria_label ) ? ' aria-label="' . $value->link_aria_label . '"' : '';

			if (empty ( $button_title )) {
				$button_title .= $all_button_title;
			}

			list ( $title_link, $link_target ) = JpagebuilderAddonHelper::parseLink ( $value, 'title_link', [ 
					'new_tab' => 'target',
					'url' => 'url'
			] );

			$check_render_link = (empty ( $link_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

			$image = (isset ( $value->avatar ) && $value->avatar) ? $value->avatar : '';
			$image_src = isset ( $image->src ) ? $image->src : $image;
			if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
				$image_src = $image_src;
			} elseif ($image_src) {
				$image_src = Uri::base ( true ) . '/' . $image_src;
			}
			$output .= '<div>';

			if (! empty ( $card )) {
				$output .= '<div class="uk-card' . $card . $card_size . $card_width . $card_inverse . '"' . $scrollspy_cls . '>';
			} else {
				$output .= '<div class="uk-panel' . $card_width . '"' . $scrollspy_cls . '>';
			}

			$name = (isset ( $value->title ) && $value->title) ? $value->title : '';

			$company = (isset ( $value->company ) && $value->company) ? $value->company : '';

			$output .= '<div class="uk-card-header">';

			$output .= '<div class="uk-grid-small uk-flex-middle" uk-grid>';

			$output .= '<div class="uk-width-auto">';
			$output .= $image_src ? '<img src="' . $image_src . '" class="ui-image' . $avatar_shape . '" alt="' . $name . '"' . $image_loading_init . '>' : '';
			$output .= '</div>';
			$output .= '<div class="uk-width-expand">';

			if ($meta_alignment == 'top' && $company) {
				$output .= '<div class="ui-meta' . $meta_style . '">';
				$output .= $company;
				$output .= '</div>';
			}

			if ($name) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . '">';
				if ($title_decoration == ' uk-heading-line') {
					$output .= '<span>';
					$output .= $name;
					$output .= '</span>';
				} else {
					$output .= $name;
				}
				$output .= '</' . $heading_selector . '>';
			}

			if ($meta_alignment != 'top' && $company) {
				$output .= '<div class="ui-meta' . $meta_style . '">';
				$output .= $company;
				$output .= '</div>';
			}

			$output .= '</div>';

			$output .= '</div>';
			$output .= '</div>';
			$output .= '<div class="uk-card-body uk-margin-remove-first-child">';

			if ($message) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $message;
				$output .= '</div>';
			}

			$output .= '</div>';

			$output .= ($title_link) ? '<div class="uk-card-footer' . $btn_margin_top . '"><a href="' . $title_link . '" class="' . $button_style_cls . '"' . $link_target . $ariaLabel . $check_render_link . '>' . $button_title . '</a></div>' : '';

			$output .= '</div>';

			$output .= '</div>';
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
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;

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

		$avatar_size = (isset ( $settings->avatar_width ) && $settings->avatar_width) ? $settings->avatar_width : '40';

		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? $settings->title_decoration : '';
		$decoration_color = '';
		$decoration_color .= (isset ( $settings->title_decoration_color ) && $settings->title_decoration_color) ? ' border-color: ' . $settings->title_decoration_color . ';' : '';
		$decoration_color .= (isset ( $settings->title_decoration_width ) && $settings->title_decoration_width) ? ' border-width: ' . $settings->title_decoration_width . 'px;' : '';

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

		if ($link_button_style == 'custom') {
			if ($button_background || $button_color) {
				$css .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
			}
			if ($button_background_hover || $button_hover_color) {
				$css .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active {' . $button_background_hover . $button_hover_color . '}';
			}
		}

		$css .= $addon_id . ' .uk-card-header img {width:' . $avatar_size . 'px;}';
		foreach ( $settings->ui_cardheader_item as $key => $value ) {
			$card = (isset ( $settings->card_style ) && $settings->card_style) ? ' uk-card-' . $settings->card_style : '';
			if ($card == ' uk-card-primary') {
				$css .= '#jpb-addon-' . $this->addon->id . ' .uk-card-primary .uk-card-header { border-bottom: 1px solid rgba(255,255,255,0.125) }';
				$css .= '#jpb-addon-' . $this->addon->id . ' .uk-card-primary .uk-card-footer { border-top: 1px solid rgba(255,255,255,0.125)}';
			}
			if ($card == ' uk-card-secondary') {
				$css .= '#jpb-addon-' . $this->addon->id . ' .uk-card-secondary .uk-card-header { border-bottom: 1px solid rgba(255,255,255,0.125) }';
				$css .= '#jpb-addon-' . $this->addon->id . ' .uk-card-secondary .uk-card-footer { border-top: 1px solid rgba(255,255,255,0.125)}';
			}
		}

		if ($decoration_color && $title_decoration) {
			$css .= "\n";
			$css .= $addon_id . ' .uk-heading-bullet::before {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-line>::after {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-line>::before {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-divider {' . $decoration_color . '}';
			$css .= "\n";
		}

		return $css;
	}
	public static function getFrontendEditor() {
		$output = '
		<style type="text/css">
		<# if(data.title_decoration_color && !_.isEmpty(data.title_decoration)) { #>
			#jpb-addon-{{ data.id }} .uk-heading-bullet::before {
				border-color: {{data.title_decoration_color}};
				border-width: {{data.title_decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-line>::after {
				border-color: {{data.title_decoration_color}};
				border-width: {{data.title_decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-line>::before {
				border-color: {{data.title_decoration_color}};
				border-width: {{data.title_decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-divider {
				border-color: {{data.title_decoration_color}};
				border-width: {{data.title_decoration_width}}px;
			}
		<# } #>

		<# _.each(data.ui_cardheader_item, function(value, key){ 
			let card = ( data.card_style ) ? " uk-card-" + data.card_style : "";
			#>
			<# if ( card == " uk-card-primary" ) { #>
				#jpb-addon-{{ data.id }} .uk-card-primary .uk-card-header { border-bottom: 1px solid rgba(255,255,255,0.125) }
				#jpb-addon-{{ data.id }} .uk-card-primary .uk-card-footer { border-top: 1px solid rgba(255,255,255,0.125)}
			<# } #>
			<# if ( card == " uk-card-secondary" ) { #>
				#jpb-addon-{{ data.id }} .uk-card-secondary .uk-card-header { border-bottom: 1px solid rgba(255,255,255,0.125)}
        		#jpb-addon-{{ data.id }} .uk-card-secondary .uk-card-footer { border-top: 1px solid rgba(255,255,255,0.125)}
			<# } #>
		<# }); #>


		<# if(data.avatar_width) { #>
			#jpb-addon-{{ data.id }} .uk-card-header img {
				width: {{ data.avatar_width }}px;
			}
		<# } #>
		<# if(_.isEmpty(data.title_color) && data.custom_title_color) { #>
			<# if(data.link_title && data.panel_link == false) { #>
				#jpb-addon-{{ data.id }} .ui-title a {
			<# } else { #>
				#jpb-addon-{{ data.id }} .ui-title {
			<# } #>	
				color: {{ data.custom_title_color }};
			}
		<# } #>

		<# if(_.isEmpty(data.meta_color) && data.custom_meta_color) { #>
			#jpb-addon-{{ data.id }} .ui-meta {
				color: {{ data.custom_meta_color }};
			}
		<# } #>

		<# if(data.content_color) { #>
			#jpb-addon-{{ data.id }} .ui-content {
				color: {{ data.content_color }};
			}
		<# } #>

		<# if(data.link_button_style == "custom") { #>
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

		var title_style = "";
		title_style = data.title_heading_style ? " uk-"+data.title_heading_style : "";
		title_style += data.title_heading_color ? " uk-"+data.title_heading_color : "";
		title_style += ( heading_addon_margin ) ? " uk-margin" + (( heading_addon_margin == "default" ) ? "" : "-" + heading_addon_margin ) : "";
		
		let title_heading_selector = data.title_heading_selector || "h3";
		var title_heading_decoration = data.title_heading_decoration ? " "+data.title_heading_decoration : "";

		let avatar_shape = ( data.avatar_shape ) ? " " +data.avatar_shape : "";

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

		let text_alignment = data.alignment ? " " + data.alignment : "";
		let text_breakpoint = (data.alignment && data.text_breakpoint) ? "@" + data.text_breakpoint : "";
		let text_alignment_fallback = (data.alignment && data.text_breakpoint && data.text_alignment_fallback) ? " uk-text-" + data.text_alignment_fallback : "";
		
		let addon_margin = data.addon_margin || "";

		var general = "";
		
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";

		general += text_alignment + text_breakpoint + text_alignment_fallback + max_width_cfg;

		var grid_parallax    = ( data.parallax ) ? data.parallax : "";
		let parallax = ( grid_parallax ) ? "parallax: " +grid_parallax : "";
		let divider = ( data.divider ) ? 1 : "";

		let column_align = ( data.grid_column_align ) ? 1 : "";
		let row_align    = ( data.grid_row_align ) ? 1 : "";

		let grid_column_gap = ( data.grid_column_gap ) ? data.grid_column_gap : "";
		let grid_row_gap    = ( data.grid_row_gap ) ? data.grid_row_gap : "";

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

		let card       = data.card_style ? " uk-card-" + data.card_style : "";
		let card_width = ( data.card_width ) ? " uk-margin-auto uk-width-"+data.card_width : "";
		let card_size  = data.card_size ? " " + data.card_size : "";
		let card_inverse = data.card_style == "primary" || data.card_style == "secondary" ? " uk-light" : "";

		let heading_selector = data.heading_selector || "h3";
		 
		var heading_style    = ( data.heading_style ) ? " uk-" + data.heading_style : "";
		heading_style   += ( data.title_color ) ? " uk-text-" + data.title_color : "";
		heading_style   += ( data.title_margin_top ) ? " uk-margin-" + data.title_margin_top +"-top" : " uk-margin-top";
		let title_decoration = data.title_decoration ? " " + data.title_decoration : "";

		// Meta.
		let meta_style  = data.meta_style ? " uk-" + data.meta_style : "";
		meta_style += data.meta_color ? " uk-text-" + data.meta_color : "";
		meta_style += data.meta_margin_top ? " uk-margin-" + data.meta_margin_top + "-top" : " uk-margin-top";

		meta_alignment = data.meta_alignment ? data.meta_alignment : "";

		// Content.
		let content_style  = data.content_style ? " uk-" + data.content_style : "";
		content_style	+= data.content_margin_top ? " uk-margin-" + data.content_margin_top + "-top" : " uk-margin-top";

		let btn_styles = ( data.link_button_style ) ? data.link_button_style : "";
		let button_size    = ( data.link_button_size ) ? " " + data.link_button_size : "";
		var button_style_cls = "";

		if (_.isEmpty(btn_styles)) {
			button_style_cls += "uk-button uk-button-default" + button_size;
		} else if ( btn_styles == "link" || btn_styles == "link-muted" || btn_styles == "link-text") {
			button_style_cls += "uk-" + btn_styles;
		} else {
			button_style_cls += "uk-button uk-button-" + btn_styles + button_size;
		}
		let btn_margin_top = ( data.button_margin_top ) ? " uk-margin-" + data.button_margin_top + "-top" : " uk-margin-top";
		let all_button_title = ( data.all_button_title ) ? data.all_button_title : "";
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

		#>

		<# if( !_.isEmpty( data.title_addon ) ){ #>
			<{{ title_heading_selector }} class="tm-addon-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span> <# } #>
					{{{ data.title_addon }}}
			 	<# if (title_heading_decoration == " uk-heading-line") { #></span> <# } #>
			</{{ title_heading_selector }}>
		<# } #>

		<div class="ui-card-header{{ zindex_cls }}{{ general }}">

		<# if ( parallax ) { #>
			<div class="uk-grid-match {{ grid }}" uk-grid="{{ parallax }}">
		<# } else { #>
			<div class="uk-grid-match {{ grid }}" uk-grid{{{ animation }}}>
		<# } #>

		<#
		if(_.isObject(data.ui_cardheader_item) && data.ui_cardheader_item){
			_.each(data.ui_cardheader_item, function(value){

			let message      = ( value.message ) ? value.message : "";
			let button_title = ( value.button_title ) ? value.button_title : "";
			let ariaLabel = ( value.link_aria_label ) ? \' aria-label="\' + value.link_aria_label + \'" \' : "";

			const isUrlObj = _.isObject(value?.title_link) && (!!value?.title_link?.url || !!value?.title_link?.page || !!value?.title_link?.menu);
			const isUrlString = _.isString(value?.title_link) && value?.title_link !== "";
			
			const isTarget = value?.link_open_new_window ? "_blank" : "";
			const urlObj = value?.title_link?.url ? value?.title_link : window.getSiteUrl(value?.title_link, isTarget);
			const {url, new_tab, nofollow, type, } = urlObj;
			const target = new_tab ? "_blank" : "";
			
			const rel = nofollow ? "noopener noreferrer" : "";
			var buttonUrl = (type === "url" && url) || (type === "menu" && urlObj.menu) || ((type === "page" && !!urlObj.page) && "index.php?option=com_jpagebuilder&view=page&id=" + urlObj.page) || "";

			if ( _.isEmpty( button_title ) ) {
				button_title += all_button_title;
			}

			var image = {}
			if (typeof value.avatar !== "undefined" && typeof value.avatar.src !== "undefined") {
				image = value.avatar
			} else {
				image = {src: value.avatar}
			}

			#>

			<div>

			<# if ( !_.isEmpty( card ) ) { #>
				<div class="uk-card{{ card }}{{ card_size }}{{ card_width }}{{ card_inverse }}"{{ scrollspy_cls }}>
			<# } else { #>
				<div class="uk-panel{{ card_width }}"{{ scrollspy_cls }}>
			<# } #>

			<#
			let name = ( value.title ) ? value.title : "";

			let company = ( value.company ) ? value.company : "";
			#>

			<div class="uk-card-header">

			<div class="uk-grid-small uk-flex-middle" uk-grid>

			<div class="uk-width-auto">

			<# if ( image.src ) { #>
				<# if(image.src.indexOf("http://") == -1 && image.src.indexOf("https://") == -1){ #>
					<img class="ui-img{{ avatar_shape }}" src=\'{{ pagebuilder_base + image.src }}\' alt="{{ name }}">
				<# } else { #>
					<img class="ui-img{{ avatar_shape }}" src=\'{{ image.src }}\' alt="{{ name }}">
				<# } #>
			<# } #>

			</div>

			<div class="uk-width-expand">

			<# if ( meta_alignment == "top" && company ) { #>
				<div class="ui-meta{{ meta_style }}">
					{{{ company }}}
				</div>
			<# } #>

			<# if ( name ) { #>
				<{{ heading_selector }} class="ui-title uk-margin-remove-bottom{{ heading_style }}{{ title_decoration }}">
				<# if (title_decoration == " uk-heading-line") { #><span><# } #>
					{{{ name }}}
				<# if (title_decoration == " uk-heading-line") { #></span><# } #>
				</{{ heading_selector }}>
			<# } #>

			<# if ( meta_alignment != "top" && company ) { #>
				<div class="ui-meta{{ meta_style }}">
					{{{ company }}}
				</div>
			<# } #>

			</div>

			</div>

			</div>
			
			<div class="uk-card-body uk-margin-remove-first-child">

			<# if ( message ) { #>
				<div class="ui-content uk-panel{{ content_style }}">
					{{{ message }}}
				</div>
			<# } #>

			</div>

			<# if ( button_title && buttonUrl ) { #>
				<div class="uk-card-footer{{ btn_margin_top }}">
					<a href=\'{{ buttonUrl }}\' class="{{ button_style_cls }}" target=\'{{ target }}\'{{{ ariaLabel }}} rel=\'{{ rel }}\'>{{{ button_title }}}</a>
				</div>
			<# } #>
		
			</div>

			</div>
		
		<# }); #>
			
		<# } #>

		</div>

		</div>

		';
		return $output;
	}
}
