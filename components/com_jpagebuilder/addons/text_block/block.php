<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Uri\Uri;

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonText_block extends JpagebuilderAddons {
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
		$text = (isset ( $settings->text ) && $settings->text) ? $settings->text : '';
		$dropcap = (isset ( $settings->dropcap ) && $settings->dropcap) ? $settings->dropcap : 0;
		$content_truncation = (isset ( $settings->content_truncation ) && $settings->content_truncation) ? $settings->content_truncation : 0;

		$dropcapCls = '';

		if ($dropcap) {
			$dropcapCls = ' jpb-dropcap';
		}

		// Output
		$output = '<div class="jpb-addon jpb-addon-text-block' . $dropcapCls . ' ' . $class . '" >';
		$output .= ($title) ? '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>' : '';
		$output .= '<div class="jpb-addon-content">';

		$plain_text = strip_tags ( $text );

		$text_block_text = $text;

		if ($content_truncation && ! empty ( $settings->content_truncation_max_word ) && ( int ) str_word_count ( $plain_text ) > ( int ) $settings->content_truncation_max_word) {
			$arrayString = explode ( ' ', $plain_text );
			$text_block_text = implode ( ' ', array_slice ( $arrayString, 0, ( int ) $settings->content_truncation_max_word ) );
			$text_block_text .= '<template class="jpb-addon-content-full-text">' . $text . '</template>';
			$text_block_text .= '<div class="jpb-btn-container jpb-content-truncation-show"><div role="button" class="jpb-btn-show-more">' . $settings->content_truncation_action_text . '</div></div>';
		}

		$output .= $text_block_text;

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
		$dropCap = ! empty ( $settings->dropcap );
		$contentTruncation = ! empty ( $settings->content_truncation );

		$settings->alignment = JpagebuilderCSSHelper::parseAlignment ( $settings, 'alignment' );

		$css = '';

		$dropcapStyle = $cssHelper->generateStyle ( '.jpb-dropcap .jpb-addon-content:first-letter, .jpb-dropcap .jpb-addon-content p:first-letter', $settings, [ 
				'dropcap_color' => 'color',
				'dropcap_font_size' => [ 
						'font-size',
						'line-height'
				]
		], [ 
				'dropcap_color' => false
		] );

		$textFontStyle = $cssHelper->typography ( '.jpb-addon-text-block .jpb-addon-content', $settings, 'text_typography', [ 
				'font' => 'text_font_family',
				'size' => 'text_fontsize',
				'line_height' => 'text_lineheight',
				'weight' => 'text_fontweight'
		] );

		if ($dropCap) {
			$css .= $dropcapStyle;
		}

		if ($contentTruncation) {
			$contentTruncationStyle = $cssHelper->generateStyle ( '.jpb-content-truncation-show', $settings, [ 
					'content_truncation_action_text_color' => 'color'
			], false );
			$contentTruncationStyle .= $cssHelper->typography ( '.jpb-content-truncation-show', $settings, 'content_truncation_action_typography' );

			$css .= $contentTruncationStyle;
		}

		$css .= $cssHelper->generateStyle ( '.jpb-addon-text-block', $settings, [ 
				'alignment' => 'text-align'
		], false );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-text-block', $settings, 'transform' );

		$css .= $transformCss;
		$css .= $textFontStyle;

		return $css;
	}

	/**
	 * Load external scripts.
	 *
	 * @return array
	 * @since 5.0.0
	 */
	public function scripts() {
		return array (
				'components/com_jpagebuilder/assets/js/addons/text_block.js'
		);
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
			var dropcap = "";

			if(data.dropcap){
				dropcap = "jpb-dropcap";
			}

			if(!data.heading_selector){
				data.heading_selector = "h3";
			}
		#>
		<style type="text/css">';
		// Text
		$textFallbacks = [ 
				'font' => 'data.text_font_family',
				'size' => 'data.text_fontsize',
				'line_height' => 'data.text_lineheight',
				'weight' => 'data.text_fontweight'
		];
		// Title
		$titleFallbacks = [ 
				'font' => 'data.font_family',
				'size' => 'data.title_fontsize',
				'line_height' => 'data.title_lineheight',
				'letter_spacing' => 'data.title_letterspace',
				'uppercase' => 'data.title_font_style?.uppercase',
				'italic' => 'data.title_font_style?.italic',
				'underline' => 'data.title_font_style?.underline',
				'weight' => 'data.title_font_style?.weight'
		];

		$output .= $lodash->alignment ( 'text-align', '.jpb-addon-text-block', 'data.alignment' );
		$output .= $lodash->color ( 'color', '.jpb-addon-text-block .jpb-addon-title', 'data.title_text_color' );
		$output .= $lodash->color ( 'color', '.jpb-dropcap .jpb-addon-content:first-letter', 'data.dropcap_color' );
		$output .= $lodash->unit ( 'font-size', '.jpb-dropcap .jpb-addon-content:first-letter', 'data.dropcap_font_size', 'px' );
		$output .= $lodash->unit ( 'line-height', '.jpb-dropcap .jpb-addon-content:first-letter', 'data.dropcap_font_size', 'px' );
		$output .= $lodash->typography ( '.jpb-addon-text-block .jpb-addon-content', 'data.text_typography', $textFallbacks );

		$output .= $lodash->typography ( '.jpb-addon-text-block .jpb-addon-title', 'data.title_typography', $titleFallbacks );
		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );
		$output .= $lodash->generateTransformCss ( '.jpb-addon-text-block', 'data.transform' );

		$output .= $lodash->color ( 'color', '.jpb-content-truncation-show', 'data.content_truncation_action_text_color' );
		$output .= $lodash->typography ( '.jpb-content-truncation-show', 'data.content_truncation_action_typography' );

		$output .= '
		</style>
		<div class="jpb-addon jpb-addon-text-block {{ dropcap }} {{ data.class }}" >
			<#
			let heading_selector = data.heading_selector || "h3";
			let content_truncation = data?.content_truncation ?? 0;
			let max_words = data?.content_truncation_max_word ?? data?.text?.length;
			let stripped_content = data?.text?.replace(/<\/?[^>]+(>|$)/g, "") || "";

			if( !_.isEmpty( data.title ) ){ #><{{ heading_selector }} class="jpb-addon-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</{{ heading_selector }}><# } #>
			<div id="addon-text-{{data.id}}" class="jpb-addon-content jp-editable-content" data-id={{data.id}} data-addon="text-block" data-is-truncated={{content_truncation}} data-max-words={{ max_words }} data-full-text="{{data.text}}" data-stripped-text="{{ stripped_content }}" data-action-text="{{data.content_truncation_action_text}}" data-fieldName="text">
			    <# if(!content_truncation || max_words >= stripped_content.split(" ").length || !max_words || max_words == 0) { #>
        			{{{ data.text }}}
    			<# } else { #>
        			<#
					let truncated_content = stripped_content.split(" ").slice(0, max_words).join(" ");
					#>
					{{truncated_content}}
					<div class="jpb-btn-container jpb-content-truncation-show"><div role="button" class="jpb-btn-show-more">{{data.content_truncation_action_text}}</div></div>
    			<# } #>
			</div>
		</div>';
		return $output;
	}
}
