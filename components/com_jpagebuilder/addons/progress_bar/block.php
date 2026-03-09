<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonProgress_bar extends JpagebuilderAddons {
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
		$class .= (isset ( $settings->shape ) && $settings->shape) ? 'jpb-progress-' . $settings->shape : '';
		$type = (isset ( $settings->type ) && $settings->type) ? $settings->type : '';
		$progress = (isset ( $settings->progress ) && $settings->progress) ? $settings->progress : '';
		$text = (isset ( $settings->text ) && $settings->text) ? $settings->text : '';
		$stripped = (isset ( $settings->stripped ) && $settings->stripped) ? $settings->stripped : '';
		$show_percentage = (isset ( $settings->show_percentage ) && $settings->show_percentage) ? $settings->show_percentage : 0;

		// Output
		$output = "";
		$output .= '<div class="jpb-addon ' . $class . '">';
		$output .= ($show_percentage) ? '<div class="jpb-progress-label clearfix">' . $text . '<span>' . ( int ) $progress . '%</span></div>' : '';
		$output .= '<div class="jpb-progress">';
		$output .= '<div class="jpb-progress-bar ' . $type . ' ' . $stripped . '" role="progressbar" aria-valuenow="' . ( int ) $progress . '" aria-valuemin="0" aria-valuemax="100" data-width="' . ( int ) $progress . '%">';

		if (! $show_percentage) {
			$output .= ($text) ? $text : '';
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
		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );
		$css = '';

		$type = (isset ( $settings->type ) && $settings->type) ? $settings->type : '';

		$barStyle = $cssHelper->generateStyle ( '.jpb-progress', $settings, [ 
				'bar_height' => 'height'
		] );
		$barLineHeightStyle = $cssHelper->generateStyle ( '.jpb-progress-bar', $settings, [ 
				'bar_height' => 'line-height'
		] );
		$textStyle = $cssHelper->generateStyle ( '.jpb-progress-label', $settings, [ 
				'text_color' => 'color'
		], false );
		$textFontStyle = $cssHelper->typography ( '.jpb-progress-label', $settings, 'label_typography', [ 
				'font' => 'text_fontfamily',
				'size' => 'text_fontsize',
				'line_height' => 'text_lineheight',
				'weight' => 'text_fontweight'
		] );
		$percentStyle = $cssHelper->generateStyle ( '.jpb-progress-label > span', $settings, [ 
				'percentage_color' => 'color'
		], false );
		$percentFontStyle = $cssHelper->typography ( '.jpb-progress-label > span', $settings, 'percentage_typography', [ 
				'font' => 'percentage_fontfamily',
				'size' => 'percentage_fontsize',
				'line_height' => 'percentage_lineheight',
				'weight' => 'percentage_fontweight'
		] );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-progress', $settings, 'transform' );

		if ($type === 'custom') {
			$customBarStyle = $cssHelper->generateStyle ( '.jpb-progress', $settings, [ 
					'bar_background' => "background-color"
			], false );
			$customBarBackgroundStyle = $cssHelper->generateStyle ( '.jpb-progress-bar', $settings, [ 
					'progress_bar_background' => "background-color"
			], false );

			$css .= $customBarStyle;
			$css .= $customBarBackgroundStyle;
		}

		$css .= $barStyle;
		$css .= $textStyle;
		$css .= $percentStyle;
		$css .= $transformCss;
		$css .= $textFontStyle;
		$css .= $percentFontStyle;
		$css .= $barLineHeightStyle;

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
				let show_percentage = data.show_percentage || 0
				let progressClass = (!_.isEmpty(data.shape)) ? "jpb-progress-"+data.shape:""
			#>

			<style type="text/css">';
		$output .= $lodash->unit ( 'height', '.jpb-progress', 'data.bar_height', 'px', false );
		$output .= $lodash->unit ( 'line-height', '.jpb-progress-bar', 'data.bar_height', 'px', false );
		$output .= '<# if(data.type == "custom") { #>';
		$output .= $lodash->color ( 'color', '.jpb-progress-label', 'data.text_color' );
		$output .= $lodash->color ( 'color', '.jpb-progress-label span ', 'data.percentage_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-progress', 'data.bar_background' );
		$output .= $lodash->color ( 'background-color', '.jpb-progress-bar', 'data.progress_bar_background' );
		// Label
		$labelTypographyFallbacks = [ 
				'font' => 'data.text_fontfamily',
				'size' => 'data.text_fontsize',
				'line_height' => 'data.text_lineheight',
				'weight' => 'data.text_fontweight'
		];
		$output .= $lodash->typography ( '.jpb-progress-label', 'data.label_typography', $labelTypographyFallbacks );

		// Percentage
		$percentageTypographyFallbacks = [ 
				'font' => 'data.percentage_fontfamily',
				'size' => 'data.percentage_fontsize',
				'line_height' => 'data.percentage_lineheight',
				'weight' => 'data.percentage_fontweight'
		];

		$output .= $lodash->typography ( '.jpb-progress-label span', 'data.percentage_typography', $percentageTypographyFallbacks );
		$output .= $lodash->generateTransformCss ( '.jpb-progress', 'data.transform' );
		$output .= '<# } #>';
		$output .= '
			</style>
			<div class="jpb-addon {{ data.class }}">
			<# if( show_percentage != 0 ) {#>
			<div class="jpb-progress-label clearfix">
				{{ data.text }}
				<span> {{ data.progress }}%</span>
			</div>
			<# } #>

			<div class="jpb-progress {{ progressClass }}">
			<div class="jpb-progress-bar {{ data.type }} {{ data.stripped }}" role="progressbar" aria-valuenow="{{ data.progress }}" aria-valuemin="0" aria-valuemax="100" data-width="{{ data.progress }}%">
				<# if(show_percentage == 0) { #>
					{{ data.text }}
				<# } #>
			</div>
			</div>
			</div>
			';

		return $output;
	}
}
