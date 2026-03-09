<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonBlocknumber extends JpagebuilderAddons {
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
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : '';
		$text = (isset ( $settings->text ) && $settings->text) ? $settings->text : '';
		$number = (isset ( $settings->number ) && $settings->number) ? $settings->number : '';
		$alignment = (isset ( $settings->alignment ) && $settings->alignment) ? $settings->alignment : '';
		$heading = (isset ( $settings->heading ) && $settings->heading) ? $settings->heading : '';

		if ($number) {
			$block_number = '<span class="jpb-blocknumber-number">' . $number . '</span>';
		}
		// Output start
		$output = '';
		$output .= '<div class="jpb-addon jpb-addon-blocknumber ' . $class . '">';

		if ($title) {
			$output .= '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>';
		}

		$output .= '<div class="jpb-addon-content">';
		$output .= '<div class="jpb-blocknumber jpb-media">';
		if ($alignment == 'center') {
			if ($number) {
				$output .= '<div class="jpb-text-center">' . $block_number . '</div>';
			}
			$output .= '<div class="jpb-media-body jpb-text-center">';
			if ($heading)
				$output .= '<h3 class="jpb-media-heading">' . $heading . '</h3>';
			if ($text) {
				$output .= '<div class="jpb-blocknumber-text">' . $text . '</div>';
			}
		} else {
			if ($number) {
				$output .= '<div class="pull-' . $alignment . '">' . $block_number . '</div>';
			}
			$output .= '<div class="jpb-media-body jpb-text-' . $alignment . '">';
			if ($heading)
				$output .= '<h3 class="jpb-media-heading">' . $heading . '</h3>';
			$output .= '<div class="jpb-blocknumber-text">' . $text . '</div>';
		}

		$output .= '</div>'; // .jpb-media-body
		$output .= '</div>'; // .jpb-media
		$output .= '</div>'; // .jpb-addon-content
		$output .= '</div>'; // .jpb-addon-blocknumber

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

		$numberProps = [ 
				'size' => [ 
						'width',
						'height',
						'line-height'
				],
				'background' => 'background-color',
				'color' => 'color',
				'number_font_size' => 'font-size',
				'border_radius' => 'border-radius',
				'number_border_width' => 'border-width',
				'number_border_color' => 'border-color',
				'number_border_style' => 'border-style'
		];
		$units = [ 
				'background' => false,
				'color' => false,
				'number_border_color' => false,
				'number_border_style' => false
		];

		$numberStyle = $cssHelper->generateStyle ( '.jpb-blocknumber-number', $settings, $numberProps, $units );
		$headingStyle = $cssHelper->generateStyle ( '.jpb-media-heading', $settings, [ 
				'heading_color' => 'color',
				'heading_margin' => 'margin'
		], false, [ 
				'heading_margin' => 'spacing'
		] );
		$titleStyle = $cssHelper->generateStyle ( '.jpb-blocknumber-text', $settings, [ 
				'text_color' => 'color'
		], false );

		$numberFallback = [ 
				'font' => 'number_font_family',
				'size' => 'number_font_size',
				'uppercase' => 'number_font_style.uppercase',
				'italic' => 'number_font_style.italic',
				'underline' => 'number_font_style.underline',
				'weight' => 'number_font_style.weight'
		];
		$titleFallback = [ 
				'font' => 'title_font_family',
				'size' => 'title_fontsize',
				'line_height' => 'title_lineheight',
				'letter_spacing' => 'title_letterspace',
				'uppercase' => 'title_font_style.uppercase',
				'italic' => 'title_font_style.italic',
				'underline' => 'title_font_style.underline',
				'weight' => 'title_font_style.weight'
		];
		$headingFallback = [ 
				'font' => 'heading_font_family',
				'size' => 'heading_fontsize',
				'line_height' => 'heading_lineheight',
				'letter_spacing' => 'heading_letterspace',
				'uppercase' => 'heading_font_style.uppercase',
				'italic' => 'heading_font_style.italic',
				'underline' => 'heading_font_style.underline',
				'weight' => 'heading_font_style.weight'
		];
		$textFallback = [ 
				'font' => 'text_font_family',
				'size' => 'text_fontsize',
				'line_height' => 'text_lineheight',
				'letter_spacing' => 'text_letterspace',
				'uppercase' => 'text_font_style.uppercase',
				'italic' => 'text_font_style.italic',
				'underline' => 'text_font_style.underline',
				'weight' => 'text_font_style.weight'
		];

		$numberTypographyStyle = $cssHelper->typography ( '.jpb-blocknumber-number', $settings, 'number_typography', $numberFallback );
		$titleTypographyStyle = $cssHelper->typography ( '.jpb-addon-blocknumber .jpb-addon-title', $settings, 'title_typography', $titleFallback );
		$headingTypographyStyle = $cssHelper->typography ( '.jpb-media-heading', $settings, 'heading_typography', $headingFallback );
		$textTypographyStyle = $cssHelper->typography ( '.jpb-blocknumber-text', $settings, 'text_typography', $textFallback );
		$numberMarginStyle = $cssHelper->generateStyle ( '.jpb-blocknumber .jpb-text-center, .jpb-blocknumber .pull-right, .jpb-blocknumber .pull-left', $settings, [ 
				'number_margin' => 'margin'
		], false, [ 
				'number_margin' => 'spacing'
		] );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-content', $settings, 'transform' );

		$css .= $titleStyle;
		$css .= $numberStyle;
		$css .= $headingStyle;
		$css .= $numberMarginStyle;
		$css .= $textTypographyStyle;
		$css .= $titleTypographyStyle;
		$css .= $numberTypographyStyle;
		$css .= $headingTypographyStyle;
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
		// number
		$numberTypographyFallbacks = [ 
				'font' => 'data.number_font_family',
				'size' => 'data.number_font_size',
				'uppercase' => 'data.number_font_style?.uppercase',
				'italic' => 'data.number_font_style?.italic',
				'underline' => 'data.number_font_style?.underline',
				'weight' => 'data.number_font_style?.weight'
		];
		$output .= $lodash->typography ( '.jpb-blocknumber-number', 'data.number_typography', $numberTypographyFallbacks );
		$output .= $lodash->color ( 'color', '.jpb-blocknumber-number', 'data.color' );
		$output .= $lodash->color ( 'background-color', '.jpb-blocknumber-number', 'data.background' );
		$output .= $lodash->unit ( 'border-width', '.jpb-blocknumber-number', 'data.number_border_width', 'px' );
		$output .= $lodash->unit ( 'border-style', '.jpb-blocknumber-number', 'data.number_border_style' );
		$output .= $lodash->unit ( 'border-color', '.jpb-blocknumber-number', 'data.number_border_color' );
		$output .= $lodash->unit ( 'border-radius', '.jpb-blocknumber-number', 'data.border_radius', 'px' );
		$output .= $lodash->spacing ( 'margin', '.jpb-blocknumber .jpb-text-center, .jpb-blocknumber .pull-right, .jpb-blocknumber .pull-left', 'data.number_margin' );
		$output .= $lodash->unit ( 'width', '.jpb-blocknumber-number', 'data.size', 'px' );
		$output .= $lodash->unit ( 'height', '.jpb-blocknumber-number', 'data.size', 'px' );
		$output .= $lodash->unit ( 'line-height', '.jpb-blocknumber-number', 'data.size', 'px' );

		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );

		// heading
		$headingTypographyFallbacks = [ 
				'font' => 'data.heading_font_family',
				'size' => 'data.heading_fontsize',
				'line_height' => 'data.heading_lineheight',
				'letter_spacing' => 'data.heading_letterspace',
				'uppercase' => 'data.heading_font_style?.uppercase',
				'italic' => 'data.heading_font_style?.italic',
				'underline' => 'data.heading_font_style?.underline',
				'weight' => 'data.heading_font_style?.weight'
		];
		$output .= $lodash->typography ( '.jpb-media-heading', 'data.heading_typography', $headingTypographyFallbacks );
		$output .= $lodash->spacing ( 'margin', '.jpb-media-heading', 'data.heading_margin' );
		$output .= $lodash->color ( 'color', '.jpb-media-heading', 'data.heading_color' );

		// text
		$textTypographyFallbacks = [ 
				'font' => 'data.text_font_family',
				'size' => 'data.text_fontsize',
				'line_height' => 'data.text_lineheight',
				'letter_spacing' => 'data.text_letterspace',
				'uppercase' => 'data.text_font_style?.uppercase',
				'italic' => 'data.text_font_style?.italic',
				'underline' => 'data.text_font_style?.underline',
				'weight' => 'data.text_font_style?.weight'
		];
		$output .= $lodash->typography ( '.jpb-blocknumber-text', 'data.text_typography', $textTypographyFallbacks );
		$output .= $lodash->color ( 'color', '.jpb-blocknumber-text', 'data.text_color' );

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
		$output .= $lodash->generateTransformCss ( '.jpb-addon-content', 'data.transform' );
		$output .= '
		</style>
		<div class="jpb-addon jpb-addon-blocknumber {{ data.class }}">
			<# if( !_.isEmpty( data.title ) ){ #><{{ data.heading_selector }} class="jpb-addon-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</{{ data.heading_selector }}><# } #>
			<div class="jpb-addon-content">
				<div class="jpb-blocknumber jpb-media">
					<# if( data.alignment == "center" ) { #>
						<# if(data.number) { #>
							<div class="jpb-text-center"><span class="jpb-blocknumber-number jp-inline-editable-element" data-id={{data.id}} data-fieldName="number" contenteditable="true">{{ data.number }}</span></div>
						<# } #>
						<div class="jpb-media-body jpb-text-center">
							<# if(data.heading) { #>
								<h3 class="jpb-media-heading jp-inline-editable-element" data-id={{data.id}} data-fieldName="heading" contenteditable="true">{{{ data.heading }}}</h3>
							<# } #>
							<div class="jpb-blocknumber-text jp-inline-editable-element" data-id={{data.id}} data-fieldName="text" contenteditable="true">{{{ data.text }}}</div>
						</div>
					<# } else { #>
						<# if(data.number) { #>
							<div class="pull-{{ data.alignment }}"><span class="jpb-blocknumber-number jp-inline-editable-element" data-id={{data.id}} data-fieldName="number" contenteditable="true">{{ data.number }}</span></div>
						<# } #>
						<div class="jpb-media-body jpb-text-{{ data.alignment }}">
							<# if(data.heading){ #>
								<h3 class="jpb-media-heading jp-inline-editable-element" data-id={{data.id}} data-fieldName="heading" contenteditable="true">{{{ data.heading }}}</h3>
							<# } #>
							<div class="jpb-blocknumber-text jp-inline-editable-element" data-id={{data.id}} data-fieldName="text" contenteditable="true">{{{ data.text }}}</div>
						</div>
					<# } #>
				</div>
			</div>
		</div>';

		return $output;
	}
}
