<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonAccordion extends JpagebuilderAddons {
	/**
	 * The addon frontend render method.
	 * The returned HTML string will render to the frontend page.
	 *
	 * @return string The HTML string.
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->addon->settings;
		$class = (isset ( $settings->class ) && $settings->class) ? $settings->class : '';
		$style = (isset ( $settings->style ) && $settings->style) ? $settings->style : 'panel-default';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$icon_position = (isset ( $settings->icon_position ) && $settings->icon_position) ? $settings->icon_position : '';

		$output = '';
		$output = '<div class="jpb-addon jpb-addon-accordion ' . $class . '">';

		if ($title) {
			$output .= '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>';
		}

		$output .= '<div class="jpb-addon-content">';
		$output .= '<div class="jpb-panel-group">';

		if (isset ( $settings->jp_accordion_item ) && is_array ( $settings->jp_accordion_item ) && count ( $settings->jp_accordion_item )) {
			foreach ( $settings->jp_accordion_item as $key => $item ) {
				$item_title = (isset ( $item->title ) && $item->title) ? $item->title : '';

				$output .= '<div class="jpb-panel jpb-' . $style . '">';
				$output .= '<button type="button" class="jpb-reset-button-styles jpb-w-full jpb-panel-heading' . (($key == 0) ? ' active' : '') . ' ' . ($icon_position == 'right' ? 'jpb-accordion-icon-position-right' : '') . '" id="jpb-ac-heading-' . $this->addon->id . '-key-' . $key . '" aria-expanded="' . (($key == 0) ? 'true' : 'false') . '" aria-controls="jpb-ac-content-' . $this->addon->id . '-key-' . $key . '">';

				if (isset ( $item->icon ) && $item->icon != '' && $style == 'panel-custom') {
					$output .= '<span class="jpb-accordion-icon-wrap" aria-label="' . trim ( strip_tags ( $item_title ) ) . '">';

					$icon_arr = array_filter ( explode ( ' ', $item->icon ) );

					if (count ( $icon_arr ) === 1) {
						$item->icon = 'fa ' . $item->icon;
					}

					$output .= '<i class="' . $item->icon . '" aria-hidden="true"></i> ';
					$output .= '</span>'; // .jpb-accordion-icon-wrap
				}

				$output .= '<span class="jpb-panel-title" aria-label="' . trim ( strip_tags ( $item_title ) ) . '">';

				if (isset ( $item->icon ) && $item->icon != '' && $style !== 'panel-custom') {

					$icon_arr = array_filter ( explode ( ' ', $item->icon ) );
					if (count ( $icon_arr ) === 1) {
						$item->icon = 'fa ' . $item->icon;
					}

					$output .= '<i class="' . $item->icon . '" aria-hidden="true"></i> ';
				}

				$output .= $item_title;
				$output .= '</span>'; // .jpb-panel-title

				if ($style !== 'panel-custom') {
					$output .= '<span class="jpb-toggle-direction" aria-label="Toggle Direction Icon ' . ($key + 1) . '"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>';
				}

				$output .= '</button>'; // .jpb-panel-heading
				$output .= '<div id="jpb-ac-content-' . $this->addon->id . '-key-' . $key . '" class="jpb-panel-collapse"' . (($key != 0) ? ' style="display: none;"' : '') . ' aria-labelledby="jpb-ac-heading-' . $this->addon->id . '-key-' . $key . '">';
				$output .= '<div class="jpb-panel-body">';
				$output .= isset ( $item->content ) ? $item->content : '';
				$output .= '</div>'; // .jpb-panel-body
				$output .= '</div>'; // .jpb-panel-collapse
				$output .= '</div>'; // .jpb-panel
			}
		}

		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Generate the CSS string for the frontend page.
	 *
	 * @return string The CSS string for the page.
	 * @since 1.0.0
	 */
	public function css() {
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$css = '';
		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );

		$itemHeaderFontStyle = $cssHelper->typography ( '.jpb-panel-custom .jpb-panel-heading .jpb-panel-title', $settings, 'item_title_typography', [ 
				'font' => 'item_title_font_family',
				'size' => 'item_title_fontsize',
				'line_height' => 'item_title_lineheight',
				'letter_spacing' => 'item_title_letterspace',
				'uppercase' => 'item_title_font_style.uppercase',
				'italic' => 'item_title_font_style.italic',
				'underline' => 'item_title_font_style.underline',
				'weight' => 'item_title_font_style.weight'
		] );

		$itemStyle = $cssHelper->generateStyle ( '.jpb-panel.jpb-panel-custom', $settings, [ 
				'item_margin' => 'margin',
				'item_padding' => 'padding',
				'item_bg' => 'background',
				'item_border_color' => 'border-color',
				'item_border_width' => 'border-style: solid; border-width',
				'item_border_radius' => 'border-radius'
		], [ 
				'item_padding' => false,
				'item_margin' => false,
				'item_bg' => false,
				'item_border_color' => false
		], [ 
				'item_padding' => 'spacing',
				'item_margin' => 'spacing'
		] );
		$spacingStyle = $cssHelper->generateStyle ( '.jpb-panel-group .jpb-panel.jpb-panel-custom:not(:last-child)', $settings, [ 
				'item_spacing' => 'margin-bottom'
		] );
		$titleStyle = $cssHelper->generateStyle ( '.jpb-panel-custom .jpb-panel-heading', $settings, [ 
				'item_title_bg_color' => 'background',
				'item_title_text_color' => 'color',
				'item_title_padding' => 'padding'
		], false, [ 
				'item_title_padding' => 'spacing'
		] );
		$iconStyle = $cssHelper->generateStyle ( '.jpb-panel-custom .jpb-accordion-icon-wrap', $settings, [ 
				'icon_margin' => 'margin',
				'icon_text_color' => 'color',
				'icon_fontsize' => 'font-size'
		], [ 
				'icon_margin' => false,
				'icon_text_color' => false
		], [ 
				'icon_margin' => 'spacing'
		] );
		$contentStyle = $cssHelper->generateStyle ( '.jpb-panel-custom .jpb-panel-body', $settings, [ 
				'item_content_padding' => 'padding',
				'item_border_width' => 'border-top-style: solid; border-top-width',
				'item_border_color' => 'border-top-color'
		], [ 
				'item_content_padding' => 'px',
				'item_border_width' => 'px',
				'item_border_color' => false
		], [ 
				'item_content_padding' => 'spacing'
		] );
		$activeTitleStyle = $cssHelper->generateStyle ( '.jpb-panel-custom .jpb-panel-heading.active', $settings, [ 
				'active_title_bg_color' => 'background',
				'active_title_text_color' => 'color'
		], false );
		$activeIconStyle = $cssHelper->generateStyle ( '.jpb-panel-custom .active .jpb-accordion-icon-wrap', $settings, [ 
				'active_icon_color' => 'color',
				'active_icon_rotate' => 'transform: rotate(%sdeg)'
		], false );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-panel-custom', $settings, 'transform' );

		$css .= $itemStyle;
		$css .= $iconStyle;
		$css .= $titleStyle;
		$css .= $contentStyle;
		$css .= $spacingStyle;
		$css .= $activeIconStyle;
		$css .= $activeTitleStyle;
		$css .= $itemHeaderFontStyle;
		$css .= $transformCss;

		return $css;
	}

	/**
	 * Attach inline scripts.
	 *
	 * @return string
	 */
	public function js() {
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$openitem = (isset ( $settings->openitem ) && $settings->openitem) ? $settings->openitem : '';

		if ($openitem) {
			$js = "jQuery(document).ready(function($){'use strict';
				if('" . $openitem . "' === 'hide') {
					$( '" . $addon_id . "' + ' .jpb-addon-accordion .jpb-panel-heading').removeClass('active');
				} else {
					$( '" . $addon_id . "' + ' .jpb-addon-accordion .jpb-panel-heading').addClass('active');
				}
				$( '" . $addon_id . "' + ' .jpb-addon-accordion .jpb-panel-collapse')." . $openitem . "();
			});";
			return $js;
		}
		return;
	}

	/**
	 * Generate the lodash template string for the frontend editor.
	 *
	 * @return string The lodash template string.
	 * @since 1.0.0
	 */
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( '#jpb-addon-{{ data.id }}' );
		$output = '

		<style  type="text/css">';

		// Title
		$titleTypographyFallbacks = [ 
				'font' => 'data.title_font_family',
				'size' => 'data.title_fontsize',
				'line_height' => 'data.title_lineheight',
				'letter_spacing' => 'data.title_letterspace',
				'uppercase' => 'data.title_font_style?.uppercase',
				'italic' => 'data.title_font_style?.italic',
				'underline' => 'data.title_font_style?.underline',
				'weight' => 'data.title_font_style?.weight'
		];
		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );
		$output .= $lodash->color ( 'color', '.jpb-addon-title', 'data.title_text_color' );
		$output .= $lodash->typography ( '.jpb-addon-title', 'data.title_typography', $titleTypographyFallbacks );

		// Accordion
		$itemTitleTypographyFallbacks = [ 
				'font' => 'data.item_title_font_family',
				'size' => 'data.item_title_fontsize',
				'line_height' => 'data.item_title_lineheight',
				'letter_spacing' => 'data.item_title_letterspace',
				'uppercase' => 'data.item_title_font_style?.uppercase',
				'italic' => 'data.item_title_font_style?.italic',
				'underline' => 'data.item_title_font_style?.underline',
				'weight' => 'data.item_title_font_style?.weight'
		];

		$output .= $lodash->typography ( '.jpb-panel-title', 'data.item_title_typography', $itemTitleTypographyFallbacks );
		$output .= $lodash->unit ( 'font-size', '.jpb-accordion-icon-wrap', 'data.icon_fontsize', 'px' );
		$output .= $lodash->spacing ( 'margin', '.jpb-accordion-icon-wrap', 'data.icon_margin' );
		$output .= $lodash->transform ( 'rotate', '.active .jpb-accordion-icon-wrap', 'data.active_icon_rotate', 'deg' );

		$output .= $lodash->spacing ( 'padding', '.jpb-panel-body', 'data.item_content_padding' );

		// custom
		$output .= '<# if (data.style == "panel-custom") { #>';
		$output .= $lodash->spacing ( 'padding', '.jpb-panel.jpb-panel-custom', 'data.item_padding' );
		$output .= $lodash->spacing ( 'padding', '.jpb-panel-custom .jpb-panel-heading', 'data.item_title_padding' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-panel-group .jpb-panel:not(:last-child)', 'data.item_spacing', 'px' );

		// panel
		$output .= $lodash->color ( 'background-color', '.jpb-panel', 'data.item_bg' );
		$output .= $lodash->unit ( 'border-radius', '.jpb-panel', 'data.item_border_radius', 'px' );

		// icon
		$output .= $lodash->color ( 'color', '.jpb-accordion-icon-wrap', 'data.icon_text_color' );
		$output .= $lodash->color ( 'color', '.active .jpb-accordion-icon-wrap', 'data.active_icon_color' );

		// heading
		$output .= $lodash->color ( 'color', '.jpb-panel-heading', 'data.item_title_text_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-panel-heading', 'data.item_title_bg_color' );
		$output .= $lodash->color ( 'color', '.jpb-panel-heading.active', 'data.active_title_text_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-panel-heading.active', 'data.active_title_bg_color' );
		$output .= $lodash->spacing ( 'margin', '.jpb-panel.jpb-panel-custom', 'data.item_margin' );
		// accordion
		$output .= '<# if (!_.isEmpty(data.item_border_width)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-panel.jpb-panel-custom {border-style: solid;}';
		$output .= $lodash->unit ( 'border-width', '.jpb-panel.jpb-panel-custom', 'data.item_border_width', 'px', false );
		$output .= $lodash->unit ( 'border-color', '.jpb-panel.jpb-panel-custom', 'data.item_border_color', '', false );

		$output .= '#jpb-addon-{{ data.id }} .jpb-panel-body {border-top-style: solid;}';
		$output .= $lodash->unit ( 'border-top-width', '.jpb-panel-body', 'data.item_border_width', 'px', false );
		$output .= $lodash->unit ( 'border-top-color', '.jpb-panel-body', 'data.item_border_color', '', false );
		$output .= '<# } #>';

		$output .= '<# } #>';

		$output .= $lodash->generateTransformCss ( '.jpb-panel-custom', 'data.transform' );

		$output .= '
		</style>
		';
		return $output;
	}
}
