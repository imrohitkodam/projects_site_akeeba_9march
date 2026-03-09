<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
class JpagebuilderAddonCountdown extends JpagebuilderAddons {
	/**
	 * The addon frontend render method.
	 * The returned HTML string will render to the frontend page.
	 *
	 * @return string The HTML string.
	 * @since 1.0.0
	 */
	public function render() {
		// Options
		$class = (isset ( $this->addon->settings->class ) && $this->addon->settings->class) ? ' ' . $this->addon->settings->class : '';
		$title = (isset ( $this->addon->settings->title ) && $this->addon->settings->title) ? $this->addon->settings->title : '';
		$heading_selector = (isset ( $this->addon->settings->heading_selector ) && $this->addon->settings->heading_selector) ? $this->addon->settings->heading_selector : 'h3';

		$output = '';
		$output .= '<div class="jpb-addon jpb-addon-countdown ' . $class . '">';
		$output .= '<div class="countdown-text-wrap">';
		$output .= ($title) ? '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>' : '';
		$output .= '</div>';
		$output .= "<div class='jpb-countdown-timer jpb-row'></div>";
		$output .= '</div>';

		return $output;
	}

	/**
	 * Attach the required scripts.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function scripts() {
		return [ 
				'components/com_jpagebuilder/assets/js/jquery.countdown.min.js'
		];
	}

	/**
	 * Write inline JavaScript.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function js() {
		$date = HTMLHelper::_ ( 'date', $this->addon->settings->date, 'Y/m/d' );
		$time = $this->addon->settings->time;
		$finish_text = addslashes ( $this->addon->settings->finish_text );

		$js = "jQuery(function($){
			var addon_id = '#jpb-addon-'+'" . $this->addon->id . "';
			$( addon_id +' .jpb-addon-countdown .jpb-countdown-timer').each(function () {
					var cdDateFormate = '" . $date . "' + ' ' + '" . $time . "';
					$(this).countdown(cdDateFormate, function (event) {
							$(this).html(event.strftime('<div class=\"jpb-countdown-days jpb-col-xs-6 jpb-col-sm-3 jpb-text-center\"><span class=\"jpb-countdown-number\">%-D</span><span class=\"jpb-countdown-text\">%!D: ' + '" . Text::_ ( 'COM_JPAGEBUILDER_DAY' ) . "' + ',' + '" . Text::_ ( 'COM_JPAGEBUILDER_DAYS' ) . "' + ';</span></div><div class=\"jpb-countdown-hours jpb-col-xs-6 jpb-col-sm-3 jpb-text-center\"><span class=\"jpb-countdown-number\">%H</span><span class=\"jpb-countdown-text\">%!H: ' + '" . Text::_ ( 'COM_JPAGEBUILDER_HOUR' ) . "' + ',' + '" . Text::_ ( 'COM_JPAGEBUILDER_HOURS' ) . "' + ';</span></div><div class=\"jpb-countdown-minutes jpb-col-xs-6 jpb-col-sm-3 jpb-text-center\"><span class=\"jpb-countdown-number\">%M</span><span class=\"jpb-countdown-text\">%!M:' + '" . Text::_ ( 'COM_JPAGEBUILDER_MINUTE' ) . "' + ',' + '" . Text::_ ( 'COM_JPAGEBUILDER_MINUTES' ) . "' + ';</span></div><div class=\"jpb-countdown-seconds jpb-col-xs-6 jpb-col-sm-3 jpb-text-center\"><span class=\"jpb-countdown-number\">%S</span><span class=\"jpb-countdown-text\">%!S:' + '" . Text::_ ( 'COM_JPAGEBUILDER_SECOND' ) . "' + ',' + '" . Text::_ ( 'COM_JPAGEBUILDER_SECONDS' ) . "' + ';</span></div>'))
							.on('finish.countdown', function () {
									$(this).html('<div class=\"jpb-countdown-finishedtext-wrap jpb-col-xs-12 jpb-col-sm-12 jpb-text-center\"><h3 class=\"jpb-countdown-finishedtext\">' + '" . $finish_text . "' + '</h3></div>');
							});
					});
			});
		})";
		return $js;
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

		// Counter
		$use_border = (isset ( $settings->counter_user_border ) && $settings->counter_user_border) ? 1 : 0;

		$counterStyle = '';

		if ($use_border) {
			$counterStyle = $cssHelper->generateStyle ( '.jpb-countdown-number, .jpb-countdown-finishedtext', $settings, [ 
					'counter_border_width' => 'border-width',
					'counter_border_style' => 'border-style',
					'counter_border_color' => 'border-color'
			], [ 
					'counter_border_style' => false,
					'counter_border_color' => false
			] );
		}

		$css = '';

		$countdownNumberTypographyFallbacks = [ 
				'font' => 'counter_text_font_family',
				'size' => 'counter_font_size',
				'weight' => 'counter_text_font_weight'
		];
		$countdownNumberTypography = $cssHelper->typography ( '.jpb-countdown-number', $settings, 'counter_text_typography', $countdownNumberTypographyFallbacks );

		$countdownNumberProps = [ 
				'counter_border_radius' => 'border-radius',
				'counter_height' => 'height',
				'counter_height' => 'line-height',
				'counter_width' => 'width',
				'counter_text_color' => 'color',
				'counter_background_color' => 'background-color',
				'label_margin' => 'margin'
		];

		$countdownNumberUnit = [ 
				'counter_background_color' => false,
				'counter_text_color' => false,
				'label_margin_original' => false
		];

		$countdownNumber = $cssHelper->generateStyle ( '.jpb-countdown-number, .jpb-countdown-finishedtext', $settings, $countdownNumberProps, $countdownNumberUnit, [ 
				'label_margin' => 'spacing'
		], null );
		$countdownText = $cssHelper->generateStyle ( '.jpb-countdown-text', $settings, [ 
				'label_color' => 'color',
				'label_margin' => 'margin'
		], [ 
				'label_color' => false,
				'label_margin' => false
		], [ 
				'label_margin' => 'spacing'
		] );

		// Label typography fallback
		$countdownTextTypographyFallbacks = [ 
				'font' => 'label_font_family',
				'size' => 'label_font_size',
				'uppercase' => 'label_font_style?.uppercase',
				'italic' => 'label_font_style?.italic',
				'underline' => 'label_font_style?.underline',
				'weight' => 'label_font_style?.weight'
		];

		$countdownTextTypography = $cssHelper->typography ( '.jpb-countdown-text', $settings, 'label_typography', $countdownTextTypographyFallbacks );

		$titleTextTypographyFallbacks = [ 
				'font' => 'label_font_family',
				'size' => 'label_font_size',
				'uppercase' => 'label_font_style?.uppercase',
				'italic' => 'label_font_style?.italic',
				'underline' => 'label_font_style?.underline',
				'weight' => 'label_font_style?.weight'
		];

		$titleTextTypography = $cssHelper->typography ( '.jpb-addon-title', $settings, 'title_typography', $titleTextTypographyFallbacks );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-countdown-timer', $settings, 'transform' );

		$css .= $countdownNumber;
		$css .= $countdownText;
		$css .= $countdownTextTypography;
		$css .= $countdownNumberTypography;
		$css .= $titleTextTypography;
		$css .= $counterStyle;
		$css .= $transformCss;

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
		$output = '<style type="text/css">';

		// Title typography fallbacks.
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
		$output .= $lodash->typography ( '.jpb-addon-title', 'data.title_typography', $titleTypographyFallbacks );

		// Counter typography fallback
		$counterTypographyFallbacks = [ 
				'font' => 'data.counter_text_font_family',
				'size' => 'data.counter_font_size',
				'weight' => 'data.counter_text_font_weight'
		];
		$output .= $lodash->typography ( '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_text_typography', $counterTypographyFallbacks );
		$output .= $lodash->unit ( 'height', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_height', 'px' );
		$output .= $lodash->unit ( 'line-height', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_height', 'px' );
		$output .= $lodash->unit ( 'width', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_width', 'px' );
		$output .= $lodash->unit ( 'border-radius', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_border_radius', 'px' );
		$output .= $lodash->color ( 'color', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_text_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_background_color' );
		$output .= '<# if(data.counter_user_border) { #>';
		$output .= $lodash->unit ( 'border-width', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_border_width', 'px' );
		$output .= $lodash->unit ( 'border-style', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_border_style', '', false );
		$output .= $lodash->unit ( 'border-color', '.jpb-countdown-number, .jpb-countdown-finishedtext', 'data.counter_border_color', '', false );
		$output .= '<# } #>';

		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );

		// Labels typography fallback
		$labelsTypographyFallbacks = [ 
				'font' => 'data.label_font_family',
				'size' => 'data.label_font_size',
				'uppercase' => 'data.label_font_style?.uppercase',
				'italic' => 'data.label_font_style?.italic',
				'underline' => 'data.label_font_style?.underline',
				'weight' => 'data.label_font_style?.weight'
		];
		$output .= $lodash->typography ( '.jpb-countdown-text', 'data.label_typography', $labelsTypographyFallbacks );
		$output .= $lodash->spacing ( 'margin', '.jpb-countdown-text', 'data.label_margin' );
		$output .= $lodash->color ( 'color', '.jpb-countdown-text', 'data.label_color' );
		$output .= $lodash->generateTransformCss ( '.jpb-countdown-timer', 'data.transform' );

		$output .= '</style>
		<div class="jpb-addon jpb-addon-countdown {{ data.class }}">
			<div class="countdown-text-wrap">
				<# if( !_.isEmpty( data.title ) ){ #><{{ data.heading_selector }} class="jpb-addon-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{ data.title }}</{{ data.heading_selector }}><# } #>
			</div>
			<div class="jpb-countdown-timer jpb-row" data-date="{{ data.date }}" data-time="{{ data.time }}" data-finish-text="{{ data.finish_text }}"></div>
		</div>
		';

		return $output;
	}
}
