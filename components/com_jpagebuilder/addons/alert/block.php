<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonAlert extends JpagebuilderAddons {
	/**
	 * The addon frontend render method.
	 * The returned HTML string will render to the frontend page.
	 *
	 * @return string The HTML string.
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$class = (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';
		$type = (isset ( $settings->alrt_type ) && $settings->alrt_type) ? ' jpb-alert-' . $settings->alrt_type : '';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : '';
		$close = (isset ( $settings->close ) && $settings->close) ? $settings->close : false;
		$text = (isset ( $settings->text ) && $settings->text) ? $settings->text : '';

		if ($text) {
			$output = '<div class="jpb-addon jpb-addon-alert' . $class . '">';
			$output .= (! empty ( $title )) ? '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>' : '';
			$output .= '<div class="jpb-addon-content">';
			$output .= '<div class="jpb-alert' . $type . ' jpb-fade in" role="alertdialog">';
			$output .= ($close) ? '<button type="button" class="jpb-close" data-dismiss="jpb-alert" aria-label="alert dismiss" data-id="' . $addon_id . '"><span aria-hidden="true">&times;</span></button>' : '';
			$output .= $text;
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';

			return $output;
		}

		return;
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

		$textFontStyle = $cssHelper->typography ( '.jpb-addon-content', $settings, 'content_typography', [ 
				'font' => 'text_font_family'
		] );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-alert', $settings, 'transform' );

		$css .= $textFontStyle;
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
		$output = '
		<style type="text/css">';
		// Title
		$titleTypographyFallbacks = [ 
				'font' => 'data.font_family',
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

		// Content
		$contentTypographyFallbacks = [ 
				'font' => 'data.text_font_family'
		];

		$output .= $lodash->typography ( '.jpb-addon-content', 'data.content_typography', $contentTypographyFallbacks );
		$output .= $lodash->generateTransformCss ( '.jpb-alert', 'data.transform' );
		$output .= '
		</style>
		<div class="jpb-addon jpb-addon-alert {{ data.class }}">
			<# if( !_.isEmpty( data.title ) ){ #><{{ data.heading_selector }} class="jpb-addon-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</{{ data.heading_selector }}><# } #>
			<div class="jpb-addon-content">
				<div class="jpb-alert jpb-alert-{{ data.alrt_type }} jpb-fade in" role="alertdialog">
					<# if( data.close ){ #>
						<button type="button" class="jpb-close"><span aria-hidden="true">&times;</span></button>
					<# } #>
					<div id="addon-text-{{data.id}}" class="jp-editable-content" data-id={{data.id}} data-fieldName="text">{{{ data.text }}}</div>
				</div>
			</div>
		</div>';

		return $output;
	}
}