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
class JpagebuilderAddonButtonstyled extends JpagebuilderAddons {
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
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';

		$btn_fullwidth = (isset ( $settings->grid_width ) && $settings->grid_width) ? 1 : '';
		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';
		$grid_row_gap = (isset ( $settings->grid_row_gap ) && $settings->grid_row_gap) ? $settings->grid_row_gap : '';

		$grid_cr = '';
		if ($grid_column_gap == $grid_row_gap) {
			$grid_cr .= (! empty ( $grid_column_gap ) && ! empty ( $grid_row_gap )) ? ' uk-grid-' . $grid_column_gap : '';
		} else {
			$grid_cr .= ! empty ( $grid_column_gap ) ? ' uk-grid-column-' . $grid_column_gap : '';
			$grid_cr .= ! empty ( $grid_row_gap ) ? ' uk-grid-row-' . $grid_row_gap : '';
		}

		$grid_cr .= ($btn_fullwidth) ? ' uk-child-width-1-1' : ' uk-child-width-auto';

		$buttons = (isset ( $settings->ui_list_buttons ) && $settings->ui_list_buttons) ? $settings->ui_list_buttons : array ();

		$button_alignment = (isset ( $settings->button_alignment ) && $settings->button_alignment) ? ' uk-flex-' . $settings->button_alignment : '';
		$button_breakpoint = ($button_alignment) ? ((isset ( $settings->button_breakpoint ) && $settings->button_breakpoint) ? '@' . $settings->button_breakpoint : '') : '';
		$button_alignment_fallback = ($button_alignment && $button_breakpoint) ? ((isset ( $settings->button_fallback ) && $settings->button_fallback) ? ' uk-flex-' . $settings->button_fallback : '') : '';
		$button_alignment .= $button_breakpoint . $button_alignment_fallback;

		$text_alignment = (isset ( $settings->button_alignment ) && $settings->button_alignment) ? ' uk-text-' . $settings->button_alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->button_breakpoint ) && $settings->button_breakpoint) ? '@' . $settings->button_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->button_fallback ) && $settings->button_fallback) ? ' uk-text-' . $settings->button_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

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

		$button_size = (isset ( $settings->button_size ) && $settings->button_size) ? ' uk-button-' . $settings->button_size : '';
		$icon_size = (isset ( $settings->faw_icon_size ) && $settings->faw_icon_size) ? '; width: ' . $settings->faw_icon_size . '' : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$output = '';

		if (is_array ( $buttons ) && count ( $buttons ) > 1) {
			$output .= '<div class="ui-button' . $general . $max_width_cfg . '" ' . $animation . '>';
		} else {
			$output .= '<div class="ui-button' . $general . $max_width_cfg . $text_alignment . '" ' . $animation . '>';
		}

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		if (is_array ( $buttons ) && count ( $buttons ) > 1) {
			$output .= '<div class="uk-flex-middle' . $button_alignment . $grid_cr . '" uk-grid>';
		}

		if (isset ( $settings->ui_list_buttons ) && count ( ( array ) $settings->ui_list_buttons )) {
			foreach ( $settings->ui_list_buttons as $key => $button ) {
				$target = (isset ( $button->target ) && $button->target) ? ' target="' . $button->target . '"' : '';
				$icon_type = (isset ( $button->icon_type ) && $button->icon_type) ? $button->icon_type : '';
				$icon_position = (isset ( $button->icon_position ) && $button->icon_position) ? $button->icon_position : 'left';
				$link = (isset ( $button->link ) && $button->link) ? $button->link : '';
				$link_title = (isset ( $button->link_title ) && $button->link_title) ? ' title="' . $button->link_title . '"' : '';
				$title = (isset ( $button->title ) && $button->title) ? $button->title : '';
				$button_style = (isset ( $button->button_style ) && $button->button_style) ? $button->button_style : '';

				$icon = (empty ( $icon_type )) ? ((isset ( $button->btn_icon ) && $button->btn_icon) ? $button->btn_icon : '') : false;
				$uk_icon = ($icon_type === 'uikit') ? ((isset ( $button->uikit_icon ) && $button->uikit_icon) ? $button->uikit_icon : '') : false;
				$custom_icon = ($icon_type === 'custom') ? ((isset ( $button->custom_icon ) && $button->custom_icon) ? '<span class="uk-icon ' . $button->custom_icon . '"></span>' : '') : false;

				$icon_arr = array_filter ( explode ( ' ', $icon ) );
				if (count ( $icon_arr ) === 1) {
					$icon = 'fa ' . $icon;
				}

				if ($icon) {
					$icon_render = '<i class="' . $icon . '" aria-hidden="true"></i>';
				} elseif ($uk_icon) {
					$icon_render = '<span class="uk-icon" uk-icon="icon: ' . $uk_icon . $icon_size . '"></span>';
				} else {
					$icon_render = $custom_icon;
				}

				$button_width = (isset ( $settings->grid_width ) && $settings->grid_width) ? ' uk-width-1-1' : '';
				$button_style_cls = '';
				if (empty ( $button_style )) {
					$button_style_cls .= 'uk-button uk-button-default' . $button_width . $button_size;
				} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
					$button_style_cls .= 'uk-' . $button_style;
				} else {
					$button_style_cls .= 'uk-button uk-button-' . $button_style . $button_width . $button_size;
				}

				$check_target = (isset ( $button->target ) && $button->target) ? $button->target : '';
				$render_linkscroll = (empty ( $check_target ) && strpos ( $link, '#' ) === 0) ? ' uk-scroll' : '';
				$icon_left = '';
				$icon_right = '';

				if ($icon_position == 'left') {
					$icon_left = ($icon || $uk_icon || $custom_icon) ? $icon_render . ' ' : '';
				} else {
					$icon_right = ($icon || $uk_icon || $custom_icon) ? ' ' . $icon_render : '';
				}

				if ($title) {
					if (is_array ( $buttons ) && count ( $buttons ) > 1) {
						$output .= '<div class="ui-item-' . $key . '"' . $scrollspy_cls . '><a class="' . $button_style_cls . $font_weight . '" href="' . $link . '"' . $target . $render_linkscroll . $link_title . '>' . $icon_left . (($icon_type === 'uikit' && $uk_icon) ? '<span class="uk-text-middle">' . $title . '</span>' : $title) . $icon_right . '</a></div>';
					} else {
						$output .= '<a class="' . $button_style_cls . $font_weight . '" href="' . $link . '"' . $target . $render_linkscroll . $link_title . '>' . $icon_left . (($icon_type === 'uikit' && $uk_icon) ? '<span class="uk-text-middle">' . $title . '</span>' : $title) . $icon_right . '</a>';
					}
				}
			}
		}
		if (is_array ( $buttons ) && count ( $buttons ) > 1) {
			$output .= '</div>';
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
		$buttons = (isset ( $settings->ui_list_buttons ) && $settings->ui_list_buttons) ? $settings->ui_list_buttons : array ();
		$css = '';

		// Buttons style.
		if (isset ( $settings->ui_list_buttons ) && count ( ( array ) $settings->ui_list_buttons )) {
			foreach ( $settings->ui_list_buttons as $key => $button ) {
				$link_button_style = (isset ( $button->button_style ) && $button->button_style) ? $button->button_style : '';
				$button_background = (isset ( $button->button_background ) && $button->button_background) ? 'background-color: ' . $button->button_background . ';' : '';
				$button_color = (isset ( $button->button_color ) && $button->button_color) ? 'color: ' . $button->button_color . ';' : '';

				$button_background_hover = (isset ( $button->button_background_hover ) && $button->button_background_hover) ? 'background-color: ' . $button->button_background_hover . ';' : '';
				$button_hover_color = (isset ( $button->button_hover_color ) && $button->button_hover_color) ? 'color: ' . $button->button_hover_color . ';' : '';

				if ($link_button_style == 'custom') {
					if (is_array ( $buttons ) && count ( $buttons ) > 1) {
						if ($button_background || $button_color) {
							$css .= $addon_id . ' .ui-item-' . $key . ' .uk-button-custom {' . $button_background . $button_color . '}';
						}
						if ($button_background_hover || $button_hover_color) {
							$css .= $addon_id . ' .ui-item-' . $key . ' .uk-button-custom:hover, ' . $addon_id . ' .ui-item-' . $key . ' .uk-button-custom:focus, ' . $addon_id . ' .ui-item-' . $key . ' .uk-button-custom:active {' . $button_background_hover . $button_hover_color . '}';
						}
					} else {
						if ($button_background || $button_color) {
							$css .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
						}
						if ($button_background_hover || $button_hover_color) {
							$css .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active {' . $button_background_hover . $button_hover_color . '}';
						}
					}
				}
			}
		}

		return $css;
	}
}
