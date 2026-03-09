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
class JpagebuilderAddonProgress extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';
		$progress_value = (isset ( $settings->progress_value ) && $settings->progress_value) ? $settings->progress_value : 70;
		$text = (isset ( $settings->text ) && $settings->text) ? $settings->text : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$title = (isset ( $settings->text ) && $settings->text) ? $settings->text : '';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->text_transform ) && $settings->text_transform) ? ' uk-text-' . $settings->text_transform : '';
		$heading_decoration = (isset ( $settings->decoration ) && $settings->decoration) ? $settings->decoration : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';

		$bar_margin_top = (isset ( $settings->bar_margin_top ) && $settings->bar_margin_top) ? ' uk-margin-' . $settings->bar_margin_top . '-top' : ' uk-margin-top';

		$heading_decoration_cls = '';
		if ($heading_decoration) {
			$heading_decoration_cls = ' uk-heading-' . $heading_decoration;
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

		$font_weight = (isset ( $settings->heading_font_weight ) && $settings->heading_font_weight) ? ' uk-text-' . $settings->heading_font_weight : '';
		$output = '';

		$output .= '<div class="ui-progress' . $text_alignment . $general . $max_width_cfg . '"' . $animation . '>';
		$output .= '<div id="progress-wrapper" class="progress-element">';

		$output .= '<div class="uk-child-width-auto uk-grid-small uk-flex-bottom" uk-grid>';
		$output .= '<div class="uk-width-expand">';

		if ($text) {
			$output .= '<' . $heading_selector . ' class="progress-name uk-margin-remove-bottom' . $heading_style . $font_weight . $heading_decoration_cls . '">';
			if ($heading_decoration == 'line') {
				$output .= '<span>';
				$output .= nl2br ( $title );
				$output .= '</span>';
			} else {
				$output .= nl2br ( $title );
			}
			$output .= '</' . $heading_selector . '>';
		}

		$output .= '</div>';
		$output .= '<div class="uk-width-auto uk-flex-last">';
		$output .= '<div><span class="counter"">' . ( int ) $progress_value . '</span>%</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="ui-skill' . $bar_margin_top . '">';
		$output .= '<div class="ui-skillholder" data-percent="' . ( int ) $progress_value . '%">';
		$output .= '<div class="ui-skillbar"></div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '</div>';

		return $output;
	}
	public function js() {
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$js = 'jQuery(function($){
			$("' . $addon_id . '").appear(function () {$(\'.ui-skillholder\').each(function () {$(this).find(\'.ui-skillbar\').animate({width: $(this).attr(\'data-percent\')}, 2500);});});
		})';

		return $js;
	}
	public function scripts() {
		return array (
				'components/com_jpagebuilder/assets/js/uitheme.js',
				'components/com_jpagebuilder/assets/js/uitheme-icons.js',
				'components/com_jpagebuilder/addons/progress/assets/js/jquery.appear.js',
				'components/com_jpagebuilder/addons/progress/assets/js/jquery.counterup.min.js',
				'components/com_jpagebuilder/addons/progress/assets/js/waypoints.min.js'
		);
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
		$bar_height = (isset ( $settings->bar_height ) && $settings->bar_height) ? $settings->bar_height : 0;
		$bar_radius = (isset ( $settings->bar_radius ) && $settings->bar_radius) ? $settings->bar_radius : 0;
		$progress_bar_background = (isset ( $settings->progress_bar_background ) && $settings->progress_bar_background) ? $settings->progress_bar_background : '';
		$progress_text_color = (isset ( $settings->progress_text_color ) && $settings->progress_text_color) ? 'color:' . $settings->progress_text_color . ';' : '';

		$css = '';
		$css .= $addon_id . ' .ui-skill .ui-skillholder {';
		$css .= 'background: #e7ebf1;';
		$css .= 'display: block;';
		$css .= 'height: ' . $bar_height . 'px;';
		$css .= 'width: 100%;';
		$css .= 'border-radius: ' . $bar_radius . 'px;';
		$css .= '}';

		$css .= $addon_id . ' .ui-skill .ui-skillholder .ui-skillbar {';
		$css .= 'height: ' . $bar_height . 'px;';
		$css .= 'float: left;';
		$css .= 'border-radius: ' . $bar_radius . 'px;';
		$css .= '}';
		if ($progress_text_color) {
			$css .= $addon_id . ' .progress-name {';
			$css .= $progress_text_color;
			$css .= '}';
		}
		if ($progress_bar_background) {
			$css .= $addon_id . ' .ui-skill .ui-skillholder .ui-skillbar { background-color: ' . $progress_bar_background . '}';
			$css .= $addon_id . ' .uk-panel .progress-val { color: ' . $progress_bar_background . '}';
		}
		return $css;
	}
}
