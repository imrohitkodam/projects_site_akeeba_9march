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
class JpagebuilderAddonheadingbox extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$class = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$class .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$class .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';
		$class .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';

		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';

		$heading_decoration = (isset ( $settings->decoration ) && $settings->decoration) ? ' ' . $settings->decoration : '';

		$heading_style .= (isset ( $settings->text_transform ) && $settings->text_transform) ? ' uk-text-' . $settings->text_transform : '';
		$heading_style .= (isset ( $settings->heading_color ) && $settings->heading_color) ? ' uk-' . $settings->heading_color : '';
		$heading_style .= (isset ( $settings->heading_font_weight ) && $settings->heading_font_weight) ? ' uk-text-' . $settings->heading_font_weight : '';

		// New link
		list ( $title_link, $link_target ) = JpagebuilderAddonHelper::parseLink ( $settings, 'title_link', [ 
				'title_link' => 'link',
				'new_tab' => 'link_target'
		] );
		$render_linkscroll = (empty ( $link_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

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

		$heading_style .= $class . $text_alignment . $text_breakpoint . $text_alignment_fallback . $max_width_cfg;

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

		// Parallax Background Image Options.
		$parallax_bg = (isset ( $settings->parallax_bg ) && $settings->parallax_bg) ? $settings->parallax_bg : false;
		$parallax_bg_image = (isset ( $settings->parallax_bg_image ) && $settings->parallax_bg_image) ? $settings->parallax_bg_image : '';
		$parallax_bg_src = isset ( $parallax_bg_image->src ) ? $parallax_bg_image->src : $parallax_bg_image;
		if (strpos ( $parallax_bg_src, 'http://' ) !== false || strpos ( $parallax_bg_src, 'https://' ) !== false) {
			$parallax_bg_src = $parallax_bg_src;
		} elseif ($parallax_bg_src) {
			$parallax_bg_src = Uri::base ( true ) . '/' . $parallax_bg_src;
		}

		$parallax_bg_color = (isset ( $settings->parallax_bg_color ) && $settings->parallax_bg_color) ? $settings->parallax_bg_color : '';
		$parallax_bg_image_effect = (isset ( $settings->parallax_bg_image_effect ) && $settings->parallax_bg_image_effect) ? $settings->parallax_bg_image_effect : '';
		$parallax_bg_overlay_color = (isset ( $settings->parallax_bg_overlay_color ) && $settings->parallax_bg_overlay_color) ? $settings->parallax_bg_overlay_color : '';
		$parallax_bg_image_styles = (isset ( $settings->parallax_bg_position ) && $settings->parallax_bg_position) ? ' uk-background-' . $settings->parallax_bg_position : '';
		$parallax_bg_image_styles .= (isset ( $settings->parallax_bg_image_size ) && $settings->parallax_bg_image_size) ? ' ' . $settings->parallax_bg_image_size : '';
		$parallax_bg_image_styles .= (isset ( $settings->parallax_bg_image_visibility ) && $settings->parallax_bg_image_visibility) ? ' uk-background-image@' . $settings->parallax_bg_image_visibility : '';
		$parallax_bg_image_styles .= (isset ( $settings->parallax_bg_padding ) && $settings->parallax_bg_padding) ? ' ' . $settings->parallax_bg_padding : '';
		$parallax_bg_image_styles .= (isset ( $settings->parallax_bg_text_color ) && $settings->parallax_bg_text_color) ? ' ' . $settings->parallax_bg_text_color : '';
		$parallax_bg_image_styles .= (isset ( $settings->parallax_bg_blend_modes ) && $settings->parallax_bg_blend_modes) ? ' uk-background-blend-' . $settings->parallax_bg_blend_modes : '';

		$parallax_bg_maxwidth = (isset ( $settings->parallax_bg_maxwidth ) && $settings->parallax_bg_maxwidth) ? '' . $settings->parallax_bg_maxwidth : '';
		$parallax_bg_maxwidth_cls = '';

		if (empty ( $parallax_bg_maxwidth )) {
			$parallax_bg_maxwidth_cls = 'jpb-row-container';
		} elseif ($parallax_bg_maxwidth == 'none') {
			$parallax_bg_maxwidth_cls = 'uk-width-1-1';
		} else {
			$parallax_bg_maxwidth_cls = 'uk-container uk-container-' . $parallax_bg_maxwidth . '';
		}

		$parallax_bg_horizontal_start = (isset ( $settings->parallax_bg_horizontal_start ) && $settings->parallax_bg_horizontal_start) ? $settings->parallax_bg_horizontal_start : '0';
		$parallax_bg_horizontal_end = (isset ( $settings->parallax_bg_horizontal_end ) && $settings->parallax_bg_horizontal_end) ? $settings->parallax_bg_horizontal_end : '0';
		$parallax_bg_horizontal = (! empty ( $parallax_bg_horizontal_start ) || ! empty ( $parallax_bg_horizontal_end )) ? 'bgx: ' . $parallax_bg_horizontal_start . ',' . $parallax_bg_horizontal_end . ';' : '';

		$parallax_bg_vertical_start = (isset ( $settings->parallax_bg_vertical_start ) && $settings->parallax_bg_vertical_start) ? $settings->parallax_bg_vertical_start : '0';
		$parallax_bg_vertical_end = (isset ( $settings->parallax_bg_vertical_end ) && $settings->parallax_bg_vertical_end) ? $settings->parallax_bg_vertical_end : '0';
		$parallax_bg_vertical = (! empty ( $parallax_bg_vertical_start ) || ! empty ( $parallax_bg_vertical_end )) ? 'bgy: ' . $parallax_bg_vertical_start . ',' . $parallax_bg_vertical_end . ';' : '';

		$parallax_bg_easing = (isset ( $settings->parallax_bg_easing ) && $settings->parallax_bg_easing) ? (( int ) $settings->parallax_bg_easing / 100) : '';
		$parallax_bg_easing_cls = (! empty ( $parallax_bg_easing )) ? 'easing:' . $parallax_bg_easing . ';' : '';

		$parallax_bg_breakpoint = (isset ( $settings->parallax_bg_breakpoint ) && $settings->parallax_bg_breakpoint) ? $settings->parallax_bg_breakpoint : '';
		$parallax_bg_breakpoint_cls = (! empty ( $parallax_bg_breakpoint )) ? 'media: @' . $parallax_bg_breakpoint . ';' : '';

		$parallax_background_init = ($parallax_bg_image_effect == 'parallax') ? ' uk-parallax="' . $parallax_bg_horizontal . $parallax_bg_vertical . $parallax_bg_easing_cls . $parallax_bg_breakpoint_cls . '"' : '';
		$parallax_background_cls = ($parallax_bg_image_effect == 'fixed') ? ' uk-background-fixed' : '';

		if (! empty ( $parallax_bg_color )) {
			$parallax_bg_color = 'background-color: ' . $parallax_bg_color . '; ';
		}

		$z_index_cls = (! empty ( $parallax_bg_overlay_color )) ? ' uk-position-relative' : '';

		$output = '';

		if ($parallax_bg) {
			$output .= '<div style="' . $parallax_bg_color . '" data-src="' . $parallax_bg_src . '" class="uk-background-norepeat uk-section' . $z_index_cls . $parallax_background_cls . $parallax_bg_image_styles . '"' . $parallax_background_init . ' uk-img loading="eager">';
			if (! empty ( $parallax_bg_overlay_color )) {
				$output .= '<div class="uk-position-cover" style="background-color: ' . $parallax_bg_overlay_color . ';"></div>';
			}
			$output .= '<div class="' . $parallax_bg_maxwidth_cls . $z_index_cls . '">';
		}

		if ($title) {
			$output .= '<' . $heading_selector . ' class="tm-title' . $heading_style . $zindex_cls . $heading_decoration . '"' . $animation . '>';
			$output .= ($title_link) ? '<a class="uk-link-heading" href="' . $title_link . '" ' . $link_target . $render_linkscroll . '>' : '';
			if ($heading_decoration == ' uk-heading-line') {
				$output .= '<span>';
				$output .= nl2br ( $title );
				$output .= '</span>';
			} else {
				$output .= nl2br ( $title );
			}
			$output .= ($title_link) ? '</a>' : '';
			$output .= '</' . $heading_selector . '>';
		}

		if ($parallax_bg) {
			$output .= '</div></div>';
		}

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
		$decoration_color = '';
		$decoration_color .= (isset ( $settings->decoration_color ) && $settings->decoration_color) ? ' border-color: ' . $settings->decoration_color . ';' : '';
		$decoration_color .= (isset ( $settings->decoration_width ) && $settings->decoration_width) ? ' border-width: ' . $settings->decoration_width . 'px;' : '';

		$heading_style = (isset ( $settings->heading_color ) && $settings->heading_color) ? $settings->heading_color : '';
		$heading_color = (isset ( $settings->custom_heading_color ) && $settings->custom_heading_color) ? 'color: ' . $settings->custom_heading_color . ';' : '';
		$css = '';

		$addon_id = '#jpb-addon-' . $this->addon->id;

		if (empty ( $heading_style ) && $heading_color) {
			$css .= $addon_id . ' .tm-title {' . $heading_color . '}';
		}

		if ($decoration_color) {
			$css .= "\n";
			$css .= $addon_id . ' .uk-heading-bullet::before {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-line>::after {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-line>::before {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-divider {' . $decoration_color . '}';
			$css .= "\n";
		}

		return $css;
	}
	public static function getFrontendEditor() {
		$output = '
			<style type="text/css">
			<# if(data.decoration_color) { #>
				#jpb-addon-{{ data.id }} .uk-heading-bullet::before {
					border-color: {{data.decoration_color}};
					border-width: {{data.decoration_width}}px;
				}
				#jpb-addon-{{ data.id }} .uk-heading-line>::after {
					border-color: {{data.decoration_color}};
					border-width: {{data.decoration_width}}px;
				}
				#jpb-addon-{{ data.id }} .uk-heading-line>::before {
					border-color: {{data.decoration_color}};
					border-width: {{data.decoration_width}}px;
				}
				#jpb-addon-{{ data.id }} .uk-heading-divider {
					border-color: {{data.decoration_color}};
					border-width: {{data.decoration_width}}px;
				}
			<# } #>
			<# if(_.isEmpty(data.heading_color) && data.custom_heading_color) { #>
				#jpb-addon-{{ data.id }} .tm-title {
					color: {{ data.custom_heading_color }}
				}
			<# } #>
			</style>
			<#
            let heading_selector = data.heading_selector || "h3";

			/*** link ***/
			const urlObj = _.isObject(data.title_link) ? data.title_link : window.getSiteUrl(data?.title_link || "", data?.link_target || "");
			const {url, menu, page, type, new_tab, nofollow} = urlObj;
			const target = new_tab ? "_blank" : "";
			const relValue = nofollow ? "noopener noreferrer" : "";
			let newUrl = "";
			if(type === "url" || !type) newUrl = url;
			if(type === "menu") newUrl = menu;
			if(type === "page") newUrl = page ? `index.php?option=com_jpagebuilder&view=page&id=${page}` : "";
			
			let render_link = newUrl;
			let link_target = (new_tab)? " target=\'"+ target +"\'": "";
			let relfollow = (nofollow)? relValue: "";

			var heading_style = "";
			heading_style = data.heading_style ? " uk-"+data.heading_style : "";
			heading_style += data.heading_color ? " uk-"+data.heading_color : "";
			heading_style += data.title_heading_margin ? " "+data.title_heading_margin : "";
			heading_style += data.text_transform ? " uk-text-" + data.text_transform : "";
			heading_style += data.heading_font_weight ? " uk-text-" + data.heading_font_weight : "";

			var heading_decoration = data.decoration ? " "+data.decoration : "";


			let addon_margin = data.addon_margin || "";

			var general = "";
			general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
			
			general += ( data.visibility ) ? " " + data.visibility : "";
			general += ( data.class ) ? " " + data.class : "";
	
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
	
			general += max_width_cfg;
	
			let text_alignment          = ( data.alignment ) ? " " + data.alignment : "";
			let text_breakpoint         = ( text_alignment ) ? ( ( data.text_breakpoint ) ? "@" + data.text_breakpoint : "" ) : "";
			let text_alignment_fallback = ( text_alignment && text_breakpoint ) ? ( ( data.text_alignment_fallback ) ? " uk-text-"+data.text_alignment_fallback : "" ) : "";
	
			general += text_alignment + text_breakpoint + text_alignment_fallback;

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
				animation = ` uk-parallax=${horizontal}${vertical}${scale}${rotate}${opacity}${easing_cls}${viewport_cls}${target_cls}${breakpoint_cls}`;
			} else if (animation) {
				animation = ` uk-scrollspy="cls:uk-animation-${animation}${animation_repeat}"`;
			}

			// Parallax Background Image Options.
			let parallax_bg       = data.parallax_bg ? 1 : 0;

			var parallax_bg_src = {}

			if (typeof data.parallax_bg_image !== "undefined" && typeof data.parallax_bg_image.src !== "undefined") {
				parallax_bg_src = data.parallax_bg_image
			} else {
				parallax_bg_src = {src: data.parallax_bg_image}
			}
	
			let parallax_bg_color         = data.parallax_bg_color ? data.parallax_bg_color : "";
			let parallax_bg_image_effect  = data.parallax_bg_image_effect ? data.parallax_bg_image_effect : "";
			let parallax_bg_overlay_color = data.parallax_bg_overlay_color ? data.parallax_bg_overlay_color : "";
			var parallax_bg_image_styles  = data.parallax_bg_position ? " uk-background-" + data.parallax_bg_position : "";
			parallax_bg_image_styles += data.parallax_bg_image_size ? " " + data.parallax_bg_image_size : "";
			parallax_bg_image_styles += data.parallax_bg_image_visibility ? " uk-background-image@" + data.parallax_bg_image_visibility : "";
			parallax_bg_image_styles += data.parallax_bg_padding ? " " + data.parallax_bg_padding : "";
			parallax_bg_image_styles += data.parallax_bg_text_color ? " " + data.parallax_bg_text_color : "";
			parallax_bg_image_styles += data.parallax_bg_blend_modes ? " uk-background-blend-" + data.parallax_bg_blend_modes : "";
	
			let parallax_bg_maxwidth     = data.parallax_bg_maxwidth ? "" + data.parallax_bg_maxwidth : "";
			var parallax_bg_maxwidth_cls = "";
	
			if (_.isEmpty(parallax_bg_maxwidth) ) {
				parallax_bg_maxwidth_cls = "jpb-row-container";
			} else if ( parallax_bg_maxwidth == "none" ) {
				parallax_bg_maxwidth_cls = "uk-width-1-1";
			} else {
				parallax_bg_maxwidth_cls = "uk-container uk-container-" + parallax_bg_maxwidth;
			}
	
			let parallax_bg_horizontal_start = (!_.isEmpty(data.parallax_bg_horizontal_start) && data.parallax_bg_horizontal_start) ? data.parallax_bg_horizontal_start : "0";
			let parallax_bg_horizontal_end   = (!_.isEmpty(data.parallax_bg_horizontal_end) && data.parallax_bg_horizontal_end) ? data.parallax_bg_horizontal_end : "0";
			let parallax_bg_horizontal       = ( !_.isEmpty( parallax_bg_horizontal_start ) || !_.isEmpty( parallax_bg_horizontal_end ) ) ? \'bgx:\'+parallax_bg_horizontal_start+\',\'+ parallax_bg_horizontal_end +\';\' : "";
	
			let parallax_bg_vertical_start = (!_.isEmpty(data.parallax_bg_vertical_start) && data.parallax_bg_vertical_start) ? data.parallax_bg_vertical_start : "0";
			let parallax_bg_vertical_end   = (!_.isEmpty(data.parallax_bg_vertical_end) && data.parallax_bg_vertical_end) ? data.parallax_bg_vertical_end : "0";
			let parallax_bg_vertical       = ( !_.isEmpty( parallax_bg_vertical_start ) || !_.isEmpty( parallax_bg_vertical_end ) ) ? \'bgy:\'+parallax_bg_vertical_start+\',\'+ parallax_bg_vertical_end +\';\' : "";
		
			let parallax_bg_easing     = (!_.isEmpty(data.parallax_bg_easing) && data.parallax_bg_easing) ? parseInt(data.parallax_bg_easing) / 100 : "";
			let parallax_bg_easing_cls = ( !_.isEmpty( data.parallax_bg_easing ) ) ? \'easing:\'+parallax_bg_easing+\';\' : "";
	
			let parallax_bg_breakpoint     = data.parallax_bg_breakpoint ? data.parallax_bg_breakpoint : "";
			let parallax_bg_breakpoint_cls = ( !_.isEmpty( parallax_bg_breakpoint ) ) ? "media: @" + parallax_bg_breakpoint + ";" : "";
	
			let parallax_background_init = ( parallax_bg_image_effect == "parallax" ) ? ` uk-parallax="${parallax_bg_horizontal}${parallax_bg_vertical}${parallax_bg_easing_cls}${parallax_bg_breakpoint_cls}"` : "";
			let parallax_background_cls  = ( parallax_bg_image_effect == "fixed" ) ? " uk-background-fixed" : "";
	
			if (!_.isEmpty(parallax_bg_color)) {
				parallax_bg_color = "background-color: " + parallax_bg_color + ";";
			}

			let bg_z_index_cls = ( !_.isEmpty( parallax_bg_overlay_color ) ) ? "uk-position-relative" : "";

            #>

			<# if ( data.title ) { #>

			<# if ( parallax_bg && parallax_bg_src.src ) { #>
				<div data-src="{{ parallax_bg_src.src }}" style="{{ parallax_bg_color }}" class="uk-background-norepeat uk-section{{ bg_z_index_cls + parallax_background_cls + parallax_bg_image_styles }}"{{{ parallax_background_init }}} uk-img>
				<# if ( !_.isEmpty( parallax_bg_overlay_color ) ) { #>
					<div class="uk-position-cover" style="background-color: {{ parallax_bg_overlay_color }};"></div>
				<# } #>
				<div class="{{ parallax_bg_maxwidth_cls }}{{ bg_z_index_cls }}">
			<# } #>
				
			<{{ heading_selector }} class="tm-title{{ heading_style }}{{ general }}{{ heading_decoration }}{{ zindex_cls }}"{{{ animation }}}>
			
			<# if(render_link){ #><a class="uk-link-heading" href=\'{{ render_link }}\'{{{ link_target }}} rel=\'{{ relfollow }}\'><# } #>

			<# if (data.decoration == "uk-heading-line" ) { #>
				<span>{{{ data.title }}}</span>
			<# } else { #>
				{{{ data.title }}}
			<# } #>

			<# if( render_link ){ #></a><# } #>

			</{{ heading_selector }}>

			<# if( parallax_bg ) { #></div></div><# } #>

			<# } #>
		
		';
		return $output;
	}
}
