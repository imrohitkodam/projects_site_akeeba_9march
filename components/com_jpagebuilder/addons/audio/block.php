<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonAudio extends JpagebuilderAddons {
	/**
	 * The addon frontend render method.
	 * The returned HTML string will render to the frontend page.
	 *
	 * @return string The HTML string.
	 * @since 1.0.0
	 */
	public function render() {
		$class = (isset ( $this->addon->settings->class ) && $this->addon->settings->class) ? $this->addon->settings->class : '';
		$style = (isset ( $this->addon->settings->style ) && $this->addon->settings->style) ? $this->addon->settings->style : 'panel-default';
		$title = (isset ( $this->addon->settings->title ) && $this->addon->settings->title) ? $this->addon->settings->title : '';
		$heading_selector = (isset ( $this->addon->settings->heading_selector ) && $this->addon->settings->heading_selector) ? $this->addon->settings->heading_selector : 'h3';

		// Addon options
		$mp3_link = (isset ( $this->addon->settings->mp3_link ) && $this->addon->settings->mp3_link) ? $this->addon->settings->mp3_link : '';
		$ogg_link = (isset ( $this->addon->settings->ogg_link ) && $this->addon->settings->ogg_link) ? $this->addon->settings->ogg_link : '';
		$autoplay = (isset ( $this->addon->settings->autoplay ) && $this->addon->settings->autoplay) ? $this->addon->settings->autoplay : 0;
		$repeat = (isset ( $this->addon->settings->repeat ) && $this->addon->settings->repeat) ? $this->addon->settings->repeat : 0;

		$output = '<div class="jpb-addon jpb-addon-audio ' . $class . '">';

		if ($title) {
			$output .= '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>';
		}

		$output .= '<div class="jpb-addon-content">';
		$output .= '<audio controls ' . $autoplay . ' ' . $repeat . '>';
		$output .= '<source src="' . JpagebuilderEditorUtils::stringifyMediaItem ( $mp3_link ) . '" type="audio/mp3">';
		$output .= '<source src="' . JpagebuilderEditorUtils::stringifyMediaItem ( $ogg_link ) . '" type="audio/ogg">';
		$output .= 'Your browser does not support the audio element.';
		$output .= '</audio>';
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
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;
		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );
		$css = '';

		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-content', $settings, 'transform' );
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

		// title
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
		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );
		$output .= $lodash->color ( 'color', '.jpb-addon-title', 'data.title_text_color' );
		$output .= $lodash->generateTransformCss ( '.jpb-addon-content', 'data.transform' );
		$output .= '
		</style>
		<div class="jpb-addon jpb-addon-audio {{ data.class }}">
			<# if( !_.isEmpty( data.title ) ){ #><{{ data.heading_selector }} class="jpb-addon-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{ data.title }}</{{ data.heading_selector }}><# } #>
			<div class="jpb-addon-content">
				<audio controls {{ data.autoplay }} {{ data.repeat }}>
					<source src=\'{{ (data.mp3_link && data.mp3_link.src) ? data.mp3_link.src : data.mp3_link }}\' type="audio/mp3">
					<source src=\'{{ (data.ogg_link && data.ogg_link.src) ? data.ogg_link.src : data.ogg_link }}\' type="audio/ogg">
				</audio>
			</div>
		</div>';

		return $output;
	}
}
