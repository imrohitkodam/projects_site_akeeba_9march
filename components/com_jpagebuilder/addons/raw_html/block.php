<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonRaw_html extends JpagebuilderAddons {
	public function render() {
		$class = (isset ( $this->addon->settings->class ) && $this->addon->settings->class) ? $this->addon->settings->class : '';
		$title = (isset ( $this->addon->settings->title ) && $this->addon->settings->title) ? $this->addon->settings->title : '';
		$heading_selector = (isset ( $this->addon->settings->heading_selector ) && $this->addon->settings->heading_selector) ? $this->addon->settings->heading_selector : 'h3';

		// Options
		$html = (isset ( $this->addon->settings->html ) && $this->addon->settings->html) ? $this->addon->settings->html : '';

		// Output
		if ($html) {
			$output = '<div class="jpb-addon jpb-addon-raw-html ' . $class . '">';
			$output .= ($title) ? '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>' : '';
			$output .= '<div class="jpb-addon-content">';
			$output .= $html;
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
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-content', $settings, 'transform' );
		$css .= $transformCss;

		return $css;
	}
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( '#jpb-addon-{{ data.id }}' );

		// Title
		$rewHTMLTypographyFallbacks = [ 
				'font' => 'data.title_font_family',
				'size' => 'data.title_fontsize',
				'line_height' => 'data.title_lineheight',
				'letter_spacing' => 'data.title_letterspace',
				'uppercase' => 'data.title_font_style?.uppercase',
				'italic' => 'data.title_font_style?.italic',
				'underline' => 'data.title_font_style?.underline',
				'weight' => 'data.title_font_style?.weight'
		];

		$output = '<style type="text/css">';
		$output .= $lodash->typography ( '.jpb-addon-raw-html .jpb-addon-title', 'data.title_typography', $rewHTMLTypographyFallbacks );
		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );
		$output .= $lodash->generateTransformCss ( '.jpb-addon-content', 'data.transform' );

		$output .= '</style>';
		$output .= '
			<div class="jpb-addon jpb-addon-raw-html {{ data.class }}">
				<# if( !_.isEmpty( data.title ) ){ #><{{ data.heading_selector }} class="jpb-addon-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</{{ data.heading_selector }}><# } #>
				<div id="builder-raw-html" class="jpb-addon-content jp-inline-editable-element" data-id={{data.id}} data-fieldName="html" contenteditable="true">
					{{{ data.html }}}
				</div>
			</div>
		';

		return $output;
	}
}
