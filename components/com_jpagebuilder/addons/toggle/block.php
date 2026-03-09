<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
class JpagebuilderAddontoggle extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$toggle_type = (isset ( $settings->toggle_type ) && $settings->toggle_type) ? $settings->toggle_type : '';
		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$visible_text = (isset ( $settings->visible_text ) && $settings->visible_text) ? $settings->visible_text : '';
		$hidden_text = (isset ( $settings->hidden_text ) && $settings->hidden_text) ? $settings->hidden_text : '';

		$button_hidden_text = (isset ( $settings->button_hidden_text ) && $settings->button_hidden_text) ? $settings->button_hidden_text : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';

		$block_align = (isset ( $settings->block_align ) && $settings->block_align) ? $settings->block_align : '';
		$block_align_breakpoint = (isset ( $settings->block_align_breakpoint ) && $settings->block_align_breakpoint) ? '@' . $settings->block_align_breakpoint : '';
		$block_align_fallback = (isset ( $settings->block_align_fallback ) && $settings->block_align_fallback) ? $settings->block_align_fallback : '';

		// Block Alignment CLS.
		$block_cls [] = '';

		if (empty ( $block_align )) {
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = ' uk-margin-auto-right' . $block_align_breakpoint;
				$block_cls [] = 'uk-margin-remove-left' . $block_align_breakpoint . ($block_align_fallback == 'center' ? ' uk-margin-auto' : ' uk-margin-auto-left');
			}
		}

		if ($block_align == 'center') {
			$block_cls [] = ' uk-margin-auto' . $block_align_breakpoint;
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = 'uk-margin-auto' . ($block_align_fallback == 'right' ? '-left' : '');
			}
		}

		if ($block_align == 'right') {
			$block_cls [] = ' uk-margin-auto-left' . $block_align_breakpoint;
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = $block_align_fallback == 'center' ? 'uk-margin-remove-right' . $block_align_breakpoint . ' uk-margin-auto' : 'uk-margin-auto-left';
			}
		}

		$block_cls = implode ( ' ', array_filter ( $block_cls ) );

		$max_width_cfg .= $addon_max_width_breakpoint . ($max_width_cfg ? $block_cls : '');

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$general .= $text_alignment . $text_breakpoint . $text_alignment_fallback . $max_width_cfg;

		$toggle_animation = (isset ( $settings->toggle_animation ) && $settings->toggle_animation) ? ' uk-animation-' . $settings->toggle_animation : '';
		$toggle_animation_cls = '';
		if (! empty ( $toggle_animation )) {
			$toggle_animation_cls = ' animation:' . $toggle_animation;
		}

		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';

		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_size = (isset ( $settings->button_size ) && $settings->button_size) ? ' ' . $settings->button_size : '';

		$button_style_cls = '';
		if (empty ( $button_style )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size;
		} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
			$button_style_cls .= 'uk-' . $button_style;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $button_style . $button_size;
		}

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'div';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$before_toggle = (isset ( $settings->before_toggle ) && $settings->before_toggle) ? $settings->before_toggle : '';
		$after_toggle = (isset ( $settings->after_toggle ) && $settings->after_toggle) ? $settings->after_toggle : '';
		$toggle_align = (isset ( $settings->toggle_align ) && $settings->toggle_align) ? $settings->toggle_align : '';

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

		$parallax_target = (isset ( $settings->parallax_target ) && $settings->parallax_target) ? $settings->parallax_target : false;
		$target_cls = ($parallax_target) ? ' target: !.jpb-section;' : '';

		// Default Animation.

		$animation = (isset ( $settings->animation ) && $settings->animation) ? $settings->animation : '';
		$animation_repeat = ($animation) ? ((isset ( $settings->animation_repeat ) && $settings->animation_repeat) ? ' repeat: true;' : '') : '';

		$parallax_zindex = (isset ( $settings->parallax_zindex ) && $settings->parallax_zindex) ? $settings->parallax_zindex : false;
		$zindex_cls = ($parallax_zindex && $animation == 'parallax') ? ' uk-position-z-index uk-position-relative' : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . $target_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}

		$output = '';

		$output .= '<div class="ui-toggle' . $zindex_cls . $general . '" ' . $animation . '>';
		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-addon-title' . $title_style . $title_heading_decoration . '">';

			if ($title_heading_decoration == ' uk-heading-line') {
				$output .= '<span>';
				$output .= nl2br ( $title_addon );
				$output .= '</span>';
			} else {
				$output .= nl2br ( $title_addon );
			}
			$output .= '</' . $title_heading_selector . '>';
		}

		if ($toggle_type != '') {
			$output .= '<div class="ui-item"><button class="' . $button_style_cls . '" ' . $button_title . ' uk-toggle="target: #js-' . $this->addon->id . '; mode: click;' . $toggle_animation_cls . '">' . $button_title . '</button></div>';
			$output .= '<div id="js-' . $this->addon->id . '" class= "ui-content uk-panel uk-margin" hidden>';
			$output .= $button_hidden_text;
			$output .= '</div>';
		} else {
			$output .= '<div class="uk-flex uk-flex-middle uk-margin ' . $toggle_align . '">';
			$output .= '<div class="uk-margin-right">';
			$output .= '<' . $heading_selector . ' class="ui-rbs-head-1 uk-margin-remove-bottom' . $heading_style . '">';
			$output .= $before_toggle;
			$output .= '</' . $heading_selector . '>';
			$output .= '</div>';
			$output .= '<div class="uael-main-btn">';
			$output .= '<label class="ui-rbs-switch-label"><input class="ui-rbs-switch ui-switch-round-1" type="checkbox" uk-toggle="target:#js-' . $this->addon->id . ';' . $toggle_animation_cls . '"><span class="ui-rbs-slider ui-rbs-round"></span></label>';
			$output .= '</div>';
			$output .= '<div class="uk-margin-left">';
			$output .= '<' . $heading_selector . ' class="ui-rbs-head-2 uk-margin-remove-bottom' . $heading_style . '">';
			$output .= $after_toggle;
			$output .= '</' . $heading_selector . '>';
			$output .= '</div>';
			$output .= '</div>';

			$output .= '<div class="toggle-content-wrap">';
			$output .= '<div class="toggle-item">';
			$output .= '<div id="js-' . $this->addon->id . '" class= "toggle-front-visible">';
			$output .= '<div class= "toggle-content-inner">';
			$output .= $visible_text;
			$output .= '</div>';
			$output .= '</div>';
			$output .= '<div id="js-' . $this->addon->id . '" class= "toggle-back-hidden" hidden>';
			$output .= '<div class= "toggle-content-inner">';
			$output .= $hidden_text;
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}
	public function scripts() {
		HTMLHelper::_ ( 'script', 'components/com_jpagebuilder/assets/js/uitheme.js', [ ], [
				'defer' => true
		] );
		HTMLHelper::_ ( 'script', 'components/com_jpagebuilder/assets/js/uitheme-icons.js', [ ], [
				'defer' => true
		] );
	}
	public function stylesheets() {
		$style_sheet = [
				'components/com_jpagebuilder/assets/css/uitheme.css'
		];
		
		return $style_sheet;
	}
	public function css() {
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;
		$title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$toggle_color = (isset ( $settings->toggle_color ) && $settings->toggle_color) ? ' background-color: ' . $settings->toggle_color . ';' : '';

		$link_type = (isset ( $settings->toggle_type ) && $settings->toggle_type) ? $settings->toggle_type : '';
		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$css = '';

		if ($title_color) {
			$css .= $addon_id . ' .ui-rbs-head-1, ' . $addon_id . ' .ui-rbs-head-2 {' . $title_color . '}';
		}

		if ($content_color && $link_type == 'toogle_text') {
			$css .= $addon_id . ' .ui-content {' . $content_color . '}';
		} elseif ($content_color) {
			$css .= $addon_id . ' .toggle-content-inner {' . $content_color . '}';
		}
		if ($link_type == 'toogle_text' && $button_style == 'custom') {
			if ($button_background || $button_color) {
				$css .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
			}
			if ($button_background_hover || $button_hover_color) {
				$css .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active, ' . $addon_id . ' .uk-button-custom.uk-active {' . $button_background_hover . $button_hover_color . '}';
			}
		}
		$css .= $addon_id . ' .ui-rbs-switch-label{position:relative;display:inline-block;width:4.5em;height:2.3em;vertical-align:middle}.ui-rbs-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;-webkit-transition:.4s;transition:.4s}.ui-rbs-slider.ui-rbs-round{-webkit-border-radius:1.5em;border-radius:1.5em}.ui-rbs-switch:checked+.ui-rbs-slider:before{-webkit-transform:translateX(2.1em);-ms-transform:translateX(2.1em);transform:translateX(2.1em)}.ui-rbs-slider.ui-rbs-round:before{-webkit-border-radius:50%;border-radius:50%}.ui-rbs-slider:before{position:absolute;content:"";height:1.8em;width:1.8em;left:.25em;bottom:.25em;-webkit-transition:.4s;transition:.4s}.ui-rbs-slider{background-color:#d3d3d3}.uael-main-btn{font-size:15px}.ui-rbs-slider:before{background-color:#fff}.ui-rbs-slider.ui-rbs-round:before{-webkit-border-radius:50%;border-radius:50%}.ui-rbs-switch-label .ui-rbs-switch{display:none}';
		$css .= "\n";
		$css .= $addon_id . ' .ui-rbs-switch:checked + .ui-rbs-slider {' . $toggle_color . '}';
		$css .= "\n";
		return $css;
	}
	public static function getFrontendEditor() {
		$output = '
		<style type="text/css">
		#jpb-addon-{{ data.id }} .ui-rbs-switch-label {
			position:relative;
			display:inline-block;
			width:4.5em;
			height:2.3em;
			vertical-align:middle;
		}
		.ui-rbs-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;-webkit-transition:.4s;transition:.4s}.ui-rbs-slider.ui-rbs-round{-webkit-border-radius:1.5em;border-radius:1.5em}.ui-rbs-switch:checked+.ui-rbs-slider:before{-webkit-transform:translateX(2.1em);-ms-transform:translateX(2.1em);transform:translateX(2.1em)}.ui-rbs-slider.ui-rbs-round:before{-webkit-border-radius:50%;border-radius:50%}.ui-rbs-slider:before{position:absolute;content:"";height:1.8em;width:1.8em;left:.25em;bottom:.25em;-webkit-transition:.4s;transition:.4s}.ui-rbs-slider{background-color:#d3d3d3}.uael-main-btn{font-size:15px}.ui-rbs-slider:before{background-color:#fff}.ui-rbs-slider.ui-rbs-round:before{-webkit-border-radius:50%;border-radius:50%}.ui-rbs-switch-label .ui-rbs-switch{display:none}

		<# if (data.toggle_color) { #>
			#jpb-addon-{{ data.id }} .ui-rbs-switch:checked  .ui-rbs-slider {
				background-color: {{ data.toggle_color }};
			}
		<# } #>
		<# if(data.custom_title_color) { #>
				#jpb-addon-{{ data.id }} .ui-rbs-head-1,
				#jpb-addon-{{ data.id }} .ui-rbs-head-2 {
				color: {{ data.custom_title_color }};
			}
		<# } #>
		<# if(data.content_color) { #>
			<# if(data.toggle_type == "toogle_text") { #>
				#jpb-addon-{{ data.id }} .ui-content {
					color: {{ data.content_color }};
				}
			<# } else { #>
				#jpb-addon-{{ data.id }} .toggle-content-inner {
					color: {{ data.content_color }};
				}
			<# } #>
		<# } #>

		<# if(data.toggle_type == "toogle_text" && data.button_style == "custom") { #>
			#jpb-addon-{{ data.id }} .uk-button-custom {
				<# if(data.button_background) { #>
					background-color: {{ data.button_background }};
				<# } #>
				<# if(data.button_color) { #>
				color: {{ data.button_color }};
				<# } #>
			}
			#jpb-addon-{{ data.id }} .uk-button-custom:hover {
				<# if(data.button_background_hover) { #>
					background-color: {{ data.button_background_hover }};
				<# } #>
				<# if(data.button_hover_color) { #>
				color: {{ data.button_hover_color }};
				<# } #>
			}
		<# } #>

		</style>
		<#
		let heading_addon_margin = data.heading_addon_margin || "";

		var title_style = "";
		title_style = data.title_heading_style ? " uk-"+data.title_heading_style : "";
		title_style += data.title_heading_color ? " uk-"+data.title_heading_color : "";
		title_style += ( heading_addon_margin ) ? " uk-margin" + (( heading_addon_margin == "default" ) ? "" : "-" + heading_addon_margin ) : "";
		
		let title_heading_selector = data.title_heading_selector || "h3";
		var title_heading_decoration = data.title_heading_decoration ? " "+data.title_heading_decoration : "";

		let toggle_type  = ( data.toggle_type ) ? data.toggle_type : "";

		let addon_margin = data.addon_margin || "";

		var general = "";
		
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";

		let visible_text = ( data.visible_text ) ? data.visible_text : "";
		let hidden_text  = ( data.hidden_text ) ? data.hidden_text : "";

		let button_hidden_text = ( data.button_hidden_text ) ? data.button_hidden_text : "";

		let max_width_cfg = ( data.addon_max_width ) ? " uk-width-" + data.addon_max_width : "";
		let addon_max_width_breakpoint = (!_.isEmpty( data.addon_max_width) && data.addon_max_width_breakpoint ) ? "@" + data.addon_max_width_breakpoint : "";

		let block_align = ( data.block_align ) ? data.block_align : "";
		let block_align_breakpoint = ( data.block_align_breakpoint ) ? "@" + data.block_align_breakpoint : "";
		let block_align_fallback = ( data.block_align_fallback ) ? data.block_align_fallback : "";

		var block_cls = "";

		if ( _.isEmpty( block_align ) ) {
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += " uk-margin-auto-right" + block_align_breakpoint;
				block_cls += " uk-margin-remove-left" + block_align_breakpoint + ( block_align_fallback == "center" ? " uk-margin-auto" : " uk-margin-auto-left" );
			}
		}

		if ( block_align == "center" ) {
			block_cls += " uk-margin-auto" + block_align_breakpoint;
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += " uk-margin-auto" + ( block_align_fallback == "right" ? "-left" : "" );
			}
		}

		if ( block_align == "right" ) {
			 block_cls += " uk-margin-auto-left" + block_align_breakpoint;
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += block_align_fallback == "center" ? " uk-margin-remove-right" + block_align_breakpoint + " uk-margin-auto" : "uk-margin-auto-left";
			}
		}

		max_width_cfg += addon_max_width_breakpoint + ( max_width_cfg ? block_cls : "" );

		let text_alignment = data.alignment ? " " + data.alignment : "";
		let text_breakpoint = (data.alignment && data.text_breakpoint) ? "@" + data.text_breakpoint : "";
		let text_alignment_fallback = (data.alignment && data.text_breakpoint && data.text_alignment_fallback) ? " uk-text-" + data.text_alignment_fallback : "";
		general += text_alignment + text_breakpoint + text_alignment_fallback + max_width_cfg;

		let toggle_animation     = ( data.toggle_animation ) ? " uk-animation-"+data.toggle_animation : "";
		var toggle_animation_cls = "";
		if ( !_.isEmpty( toggle_animation ) ) {
			toggle_animation_cls = " animation:"+ toggle_animation;
		}

		let button_title = ( data.button_title ) ? data.button_title : "";
		let button_style = ( data.button_style ) ? data.button_style : "";
		let button_size    = ( data.button_size ) ? " " + data.button_size : "";
		var button_style_cls = "";

		if (_.isEmpty(button_style)) {
			button_style_cls += "uk-button uk-button-default" + button_size;
		} else if ( button_style == "link" || button_style == "link-muted" || button_style == "link-text") {
			button_style_cls += "uk-" + button_style;
		} else {
			button_style_cls += "uk-button uk-button-" + button_style + button_size;
		}

		// New style options.
		let heading_selector = data.heading_selector || "h3";
		 
		var heading_style    = ( data.heading_style ) ? " uk-" + data.heading_style : "";
		heading_style   += ( data.title_color ) ? " uk-text-" + data.title_color : "";
		heading_style   += ( data.title_text_transform ) ? " uk-text-" + data.title_text_transform : "";
		heading_style   += ( data.font_weight ) ? " uk-text-"+ data.font_weight : "";

		let before_toggle = ( data.before_toggle ) ? data.before_toggle : "";
		let after_toggle  = ( data.after_toggle ) ? data.after_toggle : "";
		let toggle_align  = ( data.toggle_align ) ? data.toggle_align : "";

		// Animation
		let horizontal_start = (!_.isEmpty(data.horizontal_start) && data.horizontal_start) ? data.horizontal_start : "0";
		let horizontal_end = (!_.isEmpty(data.horizontal_end) && data.horizontal_end) ? data.horizontal_end : "0";
		let horizontal = (!_.isEmpty(data.horizontal_start) || !_.isEmpty(data.horizontal_end)) ? \'x:\'+horizontal_start+\',\'+ horizontal_end +\';\' : "";

		let vertical_start = (!_.isEmpty(data.vertical_start) && data.vertical_start) ? data.vertical_start : "0";
		let vertical_end = (!_.isEmpty(data.vertical_end) && data.vertical_end) ? data.vertical_end : "0";
		let vertical = (!_.isEmpty(data.vertical_start) || !_.isEmpty(data.vertical_end)) ? \'y:\'+vertical_start+\',\'+ vertical_end +\';\' : "";

		let scale_start = (!_.isEmpty(data.scale_start) && data.scale_start) ? data.scale_start / 100 : "1";
		let scale_end = (!_.isEmpty(data.scale_end) && data.scale_end) ? data.scale_end / 100 : "1";
		let scale = (!_.isEmpty(data.scale_start) || !_.isEmpty(data.scale_end)) ? \'scale:\'+scale_start+\',\'+ scale_end +\';\' : "";

		let rotate_start = (!_.isEmpty(data.rotate_start) && data.rotate_start) ? data.rotate_start : "0";
		let rotate_end = (!_.isEmpty(data.rotate_end) && data.rotate_end) ? data.rotate_end : "0";
		let rotate = (!_.isEmpty(data.rotate_start) || !_.isEmpty(data.rotate_end)) ? \'rotate:\'+rotate_start+\',\'+ rotate_end +\';\' : "";

		let opacity_start = (!_.isEmpty(data.opacity_start) && data.opacity_start) ? parseInt(data.opacity_start) / 100 : "1";
		let opacity_end = (!_.isEmpty(data.opacity_end) && data.opacity_end) ? parseInt(data.opacity_end) / 100 : "1";
		let opacity = (!_.isEmpty(data.opacity_start) || !_.isEmpty(data.opacity_end)) ? \'opacity:\'+opacity_start+\',\'+ opacity_end +\';\' : "";

		let easing = (!_.isEmpty(data.easing) && data.easing) ? parseInt(data.easing) / 100 : "";
		let easing_cls = (!_.isEmpty(data.easing)) ? \'easing:\'+easing+\';\' : "";

		let breakpoint = (!_.isEmpty(data.breakpoint) && data.breakpoint) ? data.breakpoint : "";
		let breakpoint_cls = (!_.isEmpty(data.breakpoint)) ? \'media:@\'+breakpoint+\';\' : "";

		let viewport = (!_.isEmpty(data.viewport) && data.viewport) ? parseInt(data.viewport) / 100 : "";
		let viewport_cls = (!_.isEmpty(data.viewport)) ? \'viewport:\'+viewport+\';\' : "";
		
		let target_cls = data.parallax_target ? \'target:!.jpb-section;\' : "";

		let animation = data.animation ? data.animation : "";
		let animation_repeat = (animation && data.animation_repeat) ? "; repeat: true;" : "";
		
		let zindex_cls = (animation == "parallax" && data.parallax_zindex) ? " uk-position-z-index uk-position-relative" : "";	

		if (animation == "parallax") {
			animation = ` uk-parallax=${horizontal}${vertical}${scale}${rotate}${opacity}${easing_cls}${target_cls}${breakpoint_cls}${viewport_cls}`;
		} else if (animation) {
			animation = ` uk-scrollspy="cls:uk-animation-${animation}${animation_repeat}"`;
		}

		#>

		<div class="ui-toggle{{ zindex_cls }}{{ general }}"{{{ animation }}}>

		<# if( !_.isEmpty( data.title_addon ) ){ #>
			<{{ title_heading_selector }} class="tm-addon-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span> <# } #>
					{{{ data.title_addon }}}
			 	<# if (title_heading_decoration == " uk-heading-line") { #></span> <# } #>
			</{{ title_heading_selector }}>
		<# } #>

		<# if ( !_.isEmpty(toggle_type) ) { #>
			<div class="ui-item"><button class="{{ button_style_cls }}" {{ button_title }} uk-toggle="target: #js-{{data.id}}; mode: click;{{ toggle_animation_cls }}">{{{ button_title }}}</button></div>
			<div id="js-{{data.id}}" class= "ui-content uk-panel uk-margin" hidden>
			{{{ button_hidden_text }}}
			</div>
		<# } else { #>
			<div class="uk-flex uk-flex-middle uk-margin {{ toggle_align }}">
			<div class="uk-margin-right">

			<{{heading_selector}} class="ui-rbs-head-1 uk-margin-remove-bottom{{ heading_style }}">
				{{{before_toggle}}}
			</{{heading_selector}}>

			</div>
			<div class="uael-main-btn">
			<label class="ui-rbs-switch-label"><input class="ui-rbs-switch ui-switch-round-1" type="checkbox" uk-toggle="target:#js-{{data.id}};{{ toggle_animation_cls }}"><span class="ui-rbs-slider ui-rbs-round"></span></label>
			</div>
			<div class="uk-margin-left">
			
			<{{heading_selector}} class="ui-rbs-head-2 uk-margin-remove-bottom{{ heading_style }}">
				{{{after_toggle}}}
			</{{heading_selector}}>

			</div>
			</div>

			<div class="toggle-content-wrap">
			<div class="toggle-item">
			<div id="js-{{data.id}}" class= "toggle-front-visible">
			<div class= "toggle-content-inner">
			{{{visible_text}}}
			</div>
			</div>
			<div id="js-{{data.id}}" class= "toggle-back-hidden" hidden>
			<div class= "toggle-content-inner">
			{{{hidden_text}}}
			</div>
			</div>
			</div>
			</div>
		<# } #>

		</div>

		';
		return $output;
	}
}
