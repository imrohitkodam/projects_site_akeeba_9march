<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Uri\Uri;
class JpagebuilderAddonPie_progress extends JpagebuilderAddons {
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
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';

		// Options
		$percentage = (isset ( $settings->percentage ) && $settings->percentage) ? $settings->percentage : '';
		$border_color = (isset ( $settings->border_color ) && $settings->border_color) ? $settings->border_color : '#eeeeee';
		$border_active_color = (isset ( $settings->border_active_color ) && $settings->border_active_color) ? $settings->border_active_color : '';
		$border_width = (isset ( $settings->border_width ) && $settings->border_width) ? $settings->border_width : '';
		$size = (isset ( $settings->size ) && $settings->size) ? $settings->size : '';
		$icon_name = (isset ( $settings->icon_name ) && $settings->icon_name) ? $settings->icon_name : '';
		$icon_size = (isset ( $settings->icon_size ) && $settings->icon_size) ? $settings->icon_size : '';
		$text = (isset ( $settings->text ) && $settings->text) ? $settings->text : '';
		$animation_duration = (isset ( $settings->animation_duration ) && $settings->animation_duration) ? $settings->animation_duration : '';

		// Output start
		$output = '';
		$output .= '<div class="jpb-addon jpb-addon-pie-progress ' . $class . '">';
		$output .= '<div class="jpb-addon-content jpb-text-center">';
		$output .= '<div class="jpb-pie-chart" data-size="' . ( int ) $size . '" data-duration="' . ($animation_duration ? $animation_duration : false) . '" data-percent="' . $percentage . '" data-width="' . $border_width . '" data-barcolor="' . $border_active_color . '" data-trackcolor="' . $border_color . '">';

		if ($icon_name) {
			$icon_arr = array_filter ( explode ( ' ', $icon_name ) );
			if (count ( $icon_arr ) === 1) {
				$icon_name = 'fa ' . $icon_name;
			}
			$output .= '<div class="jpb-chart-icon"><span><i class="' . $icon_name . ' ' . $icon_size . '" aria-hidden="true"></i></span></div>';
		} else {
			$output .= '<div class="jpb-chart-percent"><span></span></div>';
		}

		$output .= '</div>';
		$output .= ($title) ? '<' . $heading_selector . ' class="jpb-addon-title" style="display: block;" >' . $title . '</' . $heading_selector . '>' : '';
		$output .= '<div class="jpb-addon-text">';
		$output .= $text;
		$output .= '</div>';

		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Attach additional script required for the addon.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function scripts() {
		return [
				'components/com_jpagebuilder/assets/js/jquery.easypiechart.min.js'
		];
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
		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );

		$css = '';
		$progressStyle = $cssHelper->generateStyle ( '.jpb-pie-chart', $settings, [ 
				'size' => [ 
						'height',
						'width'
				]
		] );
		$percentStyle = $cssHelper->generateStyle ( '.jpb-chart-percent span', $settings, [ 
				'percentage_font_size' => 'font-size',
				'percentage_color' => 'color'
		], [ 
				'percentage_color' => false
		] );
		$contentTypographyStyle = $cssHelper->typography ( '.jpb-addon-text', $settings, 'content_typography' );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-pie-progress', $settings, 'transform' );

		$css .= $percentStyle;
		$css .= $transformCss;
		$css .= $progressStyle;
		$css .= $contentTypographyStyle;

		return $css;
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
			<#
                let border_color = data.border_color || "#eeeeee"
                let duration = ""
                if(data.animation_duration){
                    duration = data.animation_duration
                } else {
                    duration = false
                }
			#>

			<style type="text/css">';
		$output .= $lodash->unit ( 'height', '.jpb-pie-chart', 'data.size', 'px', false );
		$output .= $lodash->unit ( 'width', '.jpb-pie-chart', 'data.size', 'px', false );
		$output .= $lodash->unit ( 'font-size', '.jpb-chart-percent span', 'data.percentage_font_size', 'px' );
		$output .= $lodash->color ( 'color', '.jpb-chart-percent span', 'data.percentage_color' );

		// Title
		$pieTyphographyFallbacks = [ 
				'font' => 'data.title_font_family',
				'size' => 'data.title_fontsize',
				'line_height' => 'data.title_lineheight',
				'letter_spacing' => 'data.title_letterspace',
				'uppercase' => 'data.title_font_style?.uppercase',
				'italic' => 'data.title_font_style?.italic',
				'underline' => 'data.title_font_style?.underline',
				'weight' => 'data.title_font_style?.weight'
		];
		$output .= $lodash->typography ( '.jpb-addon-title', 'data.title_typography', $pieTyphographyFallbacks );
		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );

		$output .= $lodash->typography ( '.jpb-addon-text', 'data.content_typography' );
		$output .= $lodash->generateTransformCss ( '.jpb-addon-pie-progress', 'data.transform' );

		$output .= '
            </style>

			<div class="jpb-addon jpb-addon-pie-progress {{ data.class }}">
                <div class="jpb-addon-content jpb-text-center">
                    <div class="jpb-pie-chart" data-size="{{ data.size }}" data-duration="{{duration}}" data-percent="{{ data.percentage }}" data-width="{{ data.border_width }}" data-barcolor="{{ data.border_active_color }}" data-trackcolor="{{ border_color }}">

                    <#
                    if(!_.isEmpty(data.icon_name)) {
                        let icon_arr = (typeof data.icon_name !== "undefined" && data.icon_name) ? data.icon_name.split(" ") : "";
			            let icon_name = icon_arr.length === 1 ? "fa "+data.icon_name : data.icon_name;
                    #>
                        <div class="jpb-chart-icon">
                        <span><i class="{{ icon_name }} {{ data.icon_size }}"></i></span>
                        </div>
                    <# } else { #>
                        <div class="jpb-chart-percent"><span></span></div>
                    <# } #>

                    </div>

                    <# if(!_.isEmpty(data.title) && data.heading_selector) { #>
                    <{{data.heading_selector}} class="jpb-addon-title jp-inline-editable-element" style="display: block;" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</{{data.heading_selector}}>
                    <# } #>

                    <div id="addon-text-{{data.id}}" class="jpb-addon-text jp-editable-content" data-id={{data.id}} data-fieldName="text">
                        {{{ data.text }}}
                    </div>
                </div>
			</div>
			';

		return $output;
	}
}