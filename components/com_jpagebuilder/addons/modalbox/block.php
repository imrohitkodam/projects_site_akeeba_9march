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
class JpagebuilderAddonModalbox extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$heading_addon_margin = (isset ( $settings->heading_addon_margin ) && $settings->heading_addon_margin) ? $settings->heading_addon_margin : '';
		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= ($heading_addon_margin) ? ' uk-margin' . (($heading_addon_margin == 'default') ? '' : '-' . $heading_addon_margin) : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

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

		$button_text = (isset ( $settings->button_text ) && $settings->button_text) ? $settings->button_text : '';

		$button_style = (isset ( $settings->button_type ) && $settings->button_type) ? '' . $settings->button_type : '';
		$button_size = (isset ( $settings->button_size ) && $settings->button_size) ? ' uk-button-' . $settings->button_size : '';

		$button_style_cls = '';
		if (empty ( $button_style )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size;
		} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
			$button_style_cls .= 'uk-' . $button_style;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $button_style . $button_size;
		}

		$muted_video = (isset ( $settings->muted_video ) && $settings->muted_video) ? $settings->muted_video : '';
		if ($muted_video) {
			$muted_video = ' ;automute: true';
		}

		$center_modal = (isset ( $settings->center_modal ) && $settings->center_modal) ? $settings->center_modal : '';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$modal_selector = (isset ( $settings->modal_selector ) && $settings->modal_selector) ? $settings->modal_selector : '';

		$modal_content_type = (isset ( $settings->modal_content_type ) && $settings->modal_content_type) ? $settings->modal_content_type : 'text';
		$modal_content_title = (isset ( $settings->modal_content_title ) && $settings->modal_content_title) ? $settings->modal_content_title : '';
		$modal_content_text = (isset ( $settings->modal_content_text ) && $settings->modal_content_text) ? $settings->modal_content_text : '';
		$modal_content_image = (isset ( $settings->modal_content_image ) && $settings->modal_content_image) ? $settings->modal_content_image : '';
		$modal_image_src = isset ( $modal_content_image->src ) ? $modal_content_image->src : $modal_content_image;
		if (strpos ( $modal_image_src, 'http://' ) !== false || strpos ( $modal_image_src, 'https://' ) !== false) {
			$modal_image_src = $modal_image_src;
		} else {
			$modal_image_src = Uri::base ( true ) . '/' . $modal_image_src;
		}
		$modal_heading_selector = (isset ( $settings->modal_heading_selector ) && $settings->modal_heading_selector) ? $settings->modal_heading_selector : 'h3';
		$modal_heading_style = (isset ( $settings->modal_heading_style ) && $settings->modal_heading_style) ? ' uk-' . $settings->modal_heading_style : '';

		$modal_content_video_url_mp4 = (isset ( $settings->modal_content_video_url_mp4 ) && $settings->modal_content_video_url_mp4) ? $settings->modal_content_video_url_mp4 : '';
		$modal_content_video_url_mp4_src = isset ( $modal_content_video_url_mp4->src ) ? $modal_content_video_url_mp4->src : $modal_content_video_url_mp4;
		if ($modal_content_video_url_mp4_src && (strpos ( $modal_content_video_url_mp4_src, "http://" ) !== false || strpos ( $modal_content_video_url_mp4_src, "https://" ) !== false)) {
			$modal_content_video_url_mp4 = $modal_content_video_url_mp4_src;
		} else {
			if (! empty ( $modal_content_video_url_mp4 )) {
				$modal_content_video_url_mp4 = Uri::base ( true ) . '/' . $modal_content_video_url_mp4_src;
			}
		}

		$modal_content_video_youtube_url = (isset ( $settings->modal_content_video_youtube_url ) && $settings->modal_content_video_youtube_url) ? $settings->modal_content_video_youtube_url : '';
		$modal_content_video_vimeo_url = (isset ( $settings->modal_content_video_vimeo_url ) && $settings->modal_content_video_vimeo_url) ? $settings->modal_content_video_vimeo_url : '';

		// Options.
		$image = (isset ( $settings->image ) && $settings->image) ? $settings->image : '';
		$image_src = isset ( $image->src ) ? $image->src : $image;
		if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
			$image_src = $image_src;
		} elseif ($image_src) {
			$image_src = Uri::base ( true ) . '/' . $image_src;
		}
		$alt_text = (isset ( $settings->alt_text ) && $settings->alt_text) ? $settings->alt_text : '';
		$image_styles = (isset ( $settings->image_border ) && $settings->image_border) ? ' ' . $settings->image_border : '';
		$image_styles .= (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' ' . $settings->box_shadow : '';
		$image_styles .= (isset ( $settings->hover_box_shadow ) && $settings->hover_box_shadow) ? ' ' . $settings->hover_box_shadow : '';
		$image_transition = (isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . ' uk-transition-opaque' : '';

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';

		$general .= $text_alignment . $text_breakpoint . $text_alignment_fallback . $max_width_cfg;

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

		$parallax_zindex = (isset ( $settings->parallax_zindex ) && $settings->parallax_zindex) ? $settings->parallax_zindex : false;
		$zindex_cls = ($parallax_zindex && $animation == 'parallax') ? ' uk-position-z-index uk-position-relative' : '';

		$animation_repeat = ($animation) ? ((isset ( $settings->animation_repeat ) && $settings->animation_repeat) ? ' repeat: true;' : '') : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . $target_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? 'uk-' . $settings->content_style : '';

		$image_loading = (isset ( $settings->image_loading ) && $settings->image_loading) ? 1 : 0;
		$image_loading_init = $image_loading ? '' : ' loading="lazy"';

		$output = '';

		$output .= '<div class="ui-modal' . $zindex_cls . $general . '"' . $animation . '>';
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
		if (empty ( $modal_selector )) {
			$output .= '<a class="' . $button_style_cls . '" href="#js-' . ($this->addon->id) . '" uk-toggle>';
			$output .= $button_text;
			$output .= '</a>';
		} else {
			if ($image_src) {
				$output .= '<a href="#js-' . ($this->addon->id) . '" uk-toggle>';
				$output .= ($image_transition) ? '<div class="uk-inline-clip uk-transition-toggle" tabindex="0">' : '';
				$output .= '<img class="uk-display-inline-block' . $image_transition . $image_styles . '" src="' . $image_src . '" alt="' . str_replace ( '"', '', $alt_text ) . '"' . $image_loading_init . '>';
				$output .= ($image_transition) ? '</div>' : '';
				$output .= '</a>';
			}
		}

		if ($modal_content_type == 'text') {

			if ($center_modal) {
				$output .= '<div id="js-' . ($this->addon->id) . '" class="uk-flex-top" uk-modal>';
				$output .= '<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">';
			} else {
				$output .= '<div id="js-' . ($this->addon->id) . '" uk-modal>';
				$output .= '<div class="uk-modal-dialog uk-modal-body">';
			}

			$output .= '<button class="uk-modal-close-outside" type="button" uk-close></button>';

			$output .= ($modal_content_title) ? '<' . $modal_heading_selector . ' class="ui-heading-title' . $modal_heading_style . '">' . $modal_content_title . '</' . $modal_heading_selector . '>' : '';

			if ($content_style) {
				$output .= '<div class="' . $content_style . '">';
			}
			$output .= $modal_content_text;
			if ($content_style) {
				$output .= '</div>';
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		if ($modal_content_type == 'image') {

			if ($center_modal) {
				$output .= '<div id="js-' . ($this->addon->id) . '" class="uk-flex-top" uk-modal>';
			} else {
				$output .= '<div id="js-' . ($this->addon->id) . '" uk-modal>';
			}
			$output .= '<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">';
			$output .= '<button class="uk-modal-close-outside" type="button" uk-close></button>';

			$output .= '<img class="ui-image" src="' . $modal_image_src . '">';

			$output .= '</div>';
			$output .= '</div>';
		}

		if ($modal_content_type == 'video') {

			if ($center_modal) {
				$output .= '<div id="js-' . ($this->addon->id) . '" class="uk-flex-top" uk-modal>';
			} else {
				$output .= '<div id="js-' . ($this->addon->id) . '" uk-modal>';
			}
			$output .= '<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">';
			$output .= '<button class="uk-modal-close-outside" type="button" uk-close></button>';

			$output .= '<video src="' . $modal_content_video_url_mp4 . '" controls playsinline uk-video="' . $muted_video . '">';
			$output .= '</video>';

			$output .= '</div>';
			$output .= '</div>';
		}

		if ($modal_content_type == 'youtube') {

			$youtube_url = $modal_content_video_youtube_url;

			if ($center_modal) {
				$output .= '<div id="js-' . ($this->addon->id) . '" class="uk-flex-top" uk-modal>';
			} else {
				$output .= '<div id="js-' . ($this->addon->id) . '" uk-modal>';
			}
			$output .= '<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">';
			$output .= '<button class="uk-modal-close-outside" type="button" uk-close></button>';
			$output .= '<iframe src="//www.youtube-nocookie.com/embed/' . $youtube_url . '" width="560" height="315" frameborder="0" uk-video="' . $muted_video . '"></iframe>';
			$output .= '</div>';
			$output .= '</div>';
		}

		if ($modal_content_type == 'vimeo') {

			$vimeo_url = $modal_content_video_vimeo_url;

			if ($center_modal) {
				$output .= '<div id="js-' . ($this->addon->id) . '" class="uk-flex-top" uk-modal>';
			} else {
				$output .= '<div id="js-' . ($this->addon->id) . '" uk-modal>';
			}
			$output .= '<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">';
			$output .= '<button class="uk-modal-close-outside" type="button" uk-close></button>';
			$output .= '<iframe src="//player.vimeo.com/video/' . $vimeo_url . '" width="560" height="315" frameborder="0" uk-video="autoplay: inview' . $muted_video . '"></iframe>';
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
		$settings = $this->addon->settings;
		$addon_id = '#js-' . $this->addon->id;
		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$link_type = (isset ( $settings->modal_selector ) && $settings->modal_selector) ? $settings->modal_selector : '';
		$button_style = (isset ( $settings->button_type ) && $settings->button_type) ? $settings->button_type : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$css = '';
		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-heading-title {' . $custom_title_color . '}';
		}

		if ($content_color) {
			$css .= $addon_id . ' .uk-modal-body p {' . $content_color . '}';
		}

		if (empty ( $link_type ) && $button_style == 'custom') {
			if ($button_background || $button_color) {
				$css .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
			}
			if ($button_background_hover || $button_hover_color) {
				$css .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active, ' . $addon_id . ' .uk-button-custom.uk-active {' . $button_background_hover . $button_hover_color . '}';
			}
		}

		return $css;
	}
	public static function getFrontendEditor() {
		$output = '
		<#
		let heading_addon_margin = data.heading_addon_margin || "";

		var title_style = "";
		title_style = data.title_heading_style ? " uk-"+data.title_heading_style : "";
		title_style += data.title_heading_color ? " uk-"+data.title_heading_color : "";
		title_style += ( heading_addon_margin ) ? " uk-margin" + (( heading_addon_margin == "default" ) ? "" : "-" + heading_addon_margin ) : "";
		
		let title_heading_selector = data.title_heading_selector || "h3";
		var title_heading_decoration = data.title_heading_decoration ? " "+data.title_heading_decoration : "";

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

		let button_text = ( data.button_text ) ? data.button_text : "";
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

		let muted_video = ( data.muted_video ) ? data.muted_video : "";
		if ( muted_video ) {
			muted_video = " ;automute: true";
		}

		let center_modal = ( data.center_modal ) ? data.center_modal : "";

		let text_alignment = data.alignment ? " " + data.alignment : "";
		let text_breakpoint = (data.alignment && data.text_breakpoint) ? "@" + data.text_breakpoint : "";
		let text_alignment_fallback = (data.alignment && data.text_breakpoint && data.text_alignment_fallback) ? " uk-text-" + data.text_alignment_fallback : "";
		
		let addon_margin = data.addon_margin || "";

		var general = "";
		
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";

		general += text_alignment + text_breakpoint + text_alignment_fallback + max_width_cfg;

		let modal_selector = ( data.modal_selector ) ? data.modal_selector : "";

		let modal_content_type  = ( data.modal_content_type ) ? data.modal_content_type : "";
		let modal_content_title = ( data.modal_content_title ) ? data.modal_content_title : "";
		let modal_content_text  = ( data.modal_content_text ) ? data.modal_content_text : "";
		
		var modal_image = {}
		if (typeof data.modal_content_image !== "undefined" && typeof data.modal_content_image.src !== "undefined") {
			modal_image = data.modal_content_image
		} else {
			modal_image = {src: data.modal_content_image}
		}

		let modal_heading_selector = data.modal_heading_selector || "h3";

		let modal_heading_style    = ( data.modal_heading_style ) ? " uk-"+ data.modal_heading_style : "";

		let modal_content_video_url_mp4 = (!_.isEmpty(data.modal_content_video_url_mp4) && data.modal_content_video_url_mp4) ? data.modal_content_video_url_mp4 : "https://storejextensions.org/cdn/templatesvideos/video-placeholder.mp4";

		if (typeof modal_content_video_url_mp4 !== "undefined" && typeof modal_content_video_url_mp4.src !== "undefined") {
			modal_content_video_url_mp4 = data.modal_content_video_url_mp4
		} else {
			modal_content_video_url_mp4 = {src: data.modal_content_video_url_mp4}
		}

		let modal_content_video_url_ogv = (!_.isEmpty(data.modal_content_video_url_ogv) && data.modal_content_video_url_ogv) ? data.modal_content_video_url_ogv : "https://storejextensions.org/cdn/templatesvideos/video-placeholder.mp4";

		if (typeof modal_content_video_url_ogv !== "undefined" && typeof modal_content_video_url_ogv.src !== "undefined") {
			modal_content_video_url_ogv = data.modal_content_video_url_ogv
		} else {
			modal_content_video_url_ogv = {src: data.modal_content_video_url_ogv}
		}

		let modal_content_video_youtube_url = data.modal_content_video_youtube_url ? data.modal_content_video_youtube_url : "";
		let modal_content_video_vimeo_url   = data.modal_content_video_vimeo_url ? data.modal_content_video_vimeo_url : "";

		var image = {}
		if (typeof data.image !== "undefined" && typeof data.image.src !== "undefined") {
			image = data.image
		} else {
			image = {src: data.image}
		}
		let alt_text         = ( data.alt_text ) ? data.alt_text : "";
		let image_styles     = ( data.image_border ) ? " "+ data.image_border : "";
		image_styles    += ( data.box_shadow ) ? " " +data.box_shadow : "";
		image_styles    += ( data.hover_box_shadow ) ? " " +data.hover_box_shadow : "";
		let image_transition = ( data.image_transition ) ? " uk-transition-"+data.image_transition + " uk-transition-opaque" : "";
		
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

		let content_style = ( data.content_style ) ? "uk-"+data.content_style : "";

		#>

		<div class="ui-modal{{ zindex_cls }}{{ general }}"{{{ animation }}}>
		
		<# if( !_.isEmpty( data.title_addon ) ){ #>
			<{{ title_heading_selector }} class="tm-addon-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span> <# } #>
					{{{ data.title_addon }}}
			 	<# if (title_heading_decoration == " uk-heading-line") { #></span> <# } #>
			</{{ title_heading_selector }}>
		<# } #>

		<# if ( _.isEmpty( modal_selector ) ) { #>
			<a class="{{ button_style_cls }}" href="#js-{{ data.id }}" uk-toggle>
			{{{ button_text }}}
			</a>
		<# } else if (image.src) { #>
				<a href="#js-{{ data.id }}" uk-toggle>
				<# if (image_transition) { #>
					<div class="uk-inline-clip uk-transition-toggle" tabindex="0">
				<# } #>
				<# if(image.src.indexOf("http://") == -1 && image.src.indexOf("https://") == -1){ #>
					<img class="uk-display-inline-block{{ image_styles }}{{image_transition}}" src=\'{{ pagebuilder_base + image.src }}\' alt="{{ alt_text.replace(/"/g, "") }}">
				<# } else { #>
					<img class="uk-display-inline-block{{image_styles}}{{image_transition}}" src=\'{{ image.src }}\' alt="{{ alt_text.replace(/"/g, "") }}">
				<# } #>
				<# if (image_transition) { #>
					</div>
				<# } #>
				</a>
		<# } #>

		<# if ( modal_content_type == "text" ) { #>

			<div id="js-{{data.id}}"<# if ( center_modal ) { #> class="uk-flex-top"<# } #> uk-modal>
			<div class="uk-modal-dialog uk-modal-body<# if ( center_modal ) { #> uk-margin-auto-vertical<# } #>">

			<button class="uk-modal-close-outside" type="button" uk-close></button>

			<# if ( modal_content_title ) { #>
				<{{ modal_heading_selector }} class="ui-heading-title{{ modal_heading_style }}">
					{{{ modal_content_title }}}
				</{{ modal_heading_selector }}>
			<# } #>

			<# if ( content_style ) { #>
				<div class="{{ content_style }}">
			<# } #>
			
			{{{ modal_content_text }}}

			<# if ( content_style ) { #>
				</div>
			<# } #>

			</div>
			</div>
		<# } #>

		<# if ( modal_content_type == "image" && modal_image.src ) { #>
			<div id="js-{{data.id}}"<# if ( center_modal ) { #> class="uk-flex-top"<# } #> uk-modal>
			<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">
			<button class="uk-modal-close-outside" type="button" uk-close></button>
			<# if(modal_image.src.indexOf("http://") == -1 && modal_image.src.indexOf("https://") == -1){ #>
				<img class="ui-image" src=\'{{ pagebuilder_base + modal_image.src }}\'>
			<# } else { #>
				<img class="ui-image" src=\'{{ modal_image.src }}\'>
			<# } #>
			</div>
			</div>

		<# } #>

		<# if ( modal_content_type == "video" && (modal_content_video_url_mp4.src || modal_content_video_url_ogv.src) ) { #>

			<div id="js-{{data.id}}"<# if ( center_modal ) { #> class="uk-flex-top"<# } #> uk-modal>
			<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">
			
			<button class="uk-modal-close-outside" type="button" uk-close></button>
			
			<# if(!_.isEmpty(modal_content_video_url_mp4.src)){ #>
				<# if(modal_content_video_url_mp4.src.indexOf("http://") == -1 && modal_content_video_url_mp4.src.indexOf("https://") == -1){ #>
					<video src=\'{{ pagebuilder_base + modal_content_video_url_mp4.src }}\' controls playsinline uk-video="{{ muted_video }}"></video>
				<# } else { #>
					<video src=\'{{ modal_content_video_url_mp4.src }}\' controls playsinline uk-video="{{ muted_video }}"></video>
				<# } #> 
			<# } else if(!_.isEmpty(modal_content_video_url_ogv.src)) { #>
				<# if(modal_content_video_url_ogv.src.indexOf("http://") == -1 && modal_content_video_url_ogv.src.indexOf("https://") == -1){ #>
					<video src=\'{{ pagebuilder_base + modal_content_video_url_ogv.src }}\' controls playsinline uk-video="{{ muted_video }}"></video>
				<# } else { #>
					<video src=\'{{ modal_content_video_url_ogv.src }}\' controls playsinline uk-video="{{ muted_video }}"></video>
				<# } #>
			<# } #>
			
			</div>
			</div>
		<# } #>

		<# if ( modal_content_type == "youtube" ) { #>
			<# let youtube_url = modal_content_video_youtube_url #>
			<div id="js-{{data.id}}"<# if ( center_modal ) { #> class="uk-flex-top"<# } #> uk-modal>
				<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">
			<button class="uk-modal-close-outside" type="button" uk-close></button>
				<iframe src="//www.youtube-nocookie.com/embed/{{ youtube_url }}" width="560" height="315" frameborder="0" uk-video="{{ muted_video }}"></iframe>
				</div>
			</div>
		<# } #>

		<# if ( modal_content_type == "vimeo" ) { #>

			<# let vimeo_url = modal_content_video_vimeo_url #>
			<div id="js-{{data.id}}"<# if ( center_modal ) { #> class="uk-flex-top"<# } #> uk-modal>
			<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">
			<button class="uk-modal-close-outside" type="button" uk-close></button>
			<iframe src="//player.vimeo.com/video/{{ vimeo_url }}" width="560" height="315" frameborder="0" uk-video="autoplay: inview{{ muted_video }}"></iframe>
			</div>
			</div>
		<# } #>

		</div>

		';
		return $output;
	}
}
