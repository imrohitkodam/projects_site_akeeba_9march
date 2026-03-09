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
class JpagebuilderAddonImage_element extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$heading_addon_margin = (isset ( $settings->heading_addon_margin ) && $settings->heading_addon_margin) ? $settings->heading_addon_margin : '';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= ($heading_addon_margin) ? ' uk-margin' . (($heading_addon_margin == 'default') ? '' : '-' . $heading_addon_margin) : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$title_position = (isset ( $settings->title_position ) && $settings->title_position) ? $settings->title_position : 'top';

		// Options.
		$image = (isset ( $settings->image ) && $settings->image) ? $settings->image : '';
		$image_src = isset ( $image->src ) ? $image->src : $image;
		if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
			$image_src = $image_src;
		} elseif ($image_src) {
			$image_src = Uri::base ( true ) . '/' . $image_src;
		}
		$alt_text = (isset ( $settings->alt_text ) && $settings->alt_text) ? $settings->alt_text : '';

		$link_type = (isset ( $settings->link_type ) && $settings->link_type) ? $settings->link_type : '';

		$link_type_cls = $link_type == 'use_modal' ? ' uk-lightbox="toggle: a[data-type]"' : '';

		// New link
		list ( $title_link, $link_target ) = JpagebuilderAddonHelper::parseLink ( $settings, 'title_link', [ 
				'title_link' => 'link',
				'new_tab' => 'link_target'
		] );

		$image_styles = (isset ( $settings->image_border ) && $settings->image_border) ? ' ' . $settings->image_border : '';
		$image_styles .= (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' ' . $settings->box_shadow : '';
		$image_styles .= (isset ( $settings->hover_box_shadow ) && $settings->hover_box_shadow) ? ' ' . $settings->hover_box_shadow : '';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$image_panel = (isset ( $settings->image_panel ) && $settings->image_panel) ? 1 : 0;
		$media_background = ($image_panel) ? ((isset ( $settings->blend_bg_color ) && $settings->blend_bg_color) ? ' style="background-color: ' . $settings->blend_bg_color . ';"' : '') : '';
		$media_blend_mode = ($image_panel && $media_background) ? ((isset ( $settings->image_blend_modes ) && $settings->image_blend_modes) ? ' ' . $settings->image_blend_modes : '') : false;
		$media_overlay = ($image_panel) ? ((isset ( $settings->media_overlay ) && $settings->media_overlay) ? '<div class="uk-position-cover" style="background-color: ' . $settings->media_overlay . '"></div>' : '') : '';

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
		$general .= $text_alignment . $text_breakpoint . $text_alignment_fallback;

		$image_transition = (isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . ' uk-transition-opaque' : '';

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

		$image_svg_inline = (isset ( $settings->image_svg_inline ) && $settings->image_svg_inline) ? $settings->image_svg_inline : false;
		$image_svg_inline_cls = ($image_svg_inline) ? ' uk-svg' : '';
		$image_svg_color = ($image_svg_inline) ? ((isset ( $settings->image_svg_color ) && $settings->image_svg_color) ? ' uk-text-' . $settings->image_svg_color : '') : false;

		$lightbox_init = (! empty ( $title_link )) ? ' data-type="iframe"' : ' data-type="image"';

		if ($link_type == 'use_modal' && empty ( $title_link )) {
			$title_link .= $image_src;
		}
		$image_loading = (isset ( $settings->image_loading ) && $settings->image_loading) ? 1 : 0;
		$image_loading_init = $image_loading ? '' : ' loading="lazy"';
		$ariaLabel = ! empty ( $settings->link_aria_label ) ? ' aria-label="' . $settings->link_aria_label . '"' : '';

		$output = '';

		if ($image_src) {

			$output .= '<div class="ui-addon-image' . $zindex_cls . $general . $max_width_cfg . '"' . $animation . $link_type_cls . '>';

			if ($title && $title_position == 'top') {
				$output .= '<' . $title_heading_selector . ' class="tm-addon-title' . $title_style . $title_heading_decoration . '">';
				if ($title_heading_decoration == ' uk-heading-line') {
					$output .= '<span>';
					$output .= nl2br ( $title );
					$output .= '</span>';
				} else {
					$output .= nl2br ( $title );
				}
				$output .= '</' . $title_heading_selector . '>';
			}

			$output .= ($link_type == 'use_modal' && $title_link) ? '<a href="' . $title_link . '" ' . $ariaLabel . $lightbox_init . ' data-caption="<h4 class=\'uk-margin-remove\'>' . str_replace ( '"', '', $alt_text ) . '</h4>">' : '';

			$output .= ($link_type == 'use_link' && $title_link) ? '<a href="' . $title_link . '"' . $link_target . $ariaLabel . '>' : '';

			if ($image_transition) {
				$output .= '<div class="uk-inline-clip uk-transition-toggle" tabindex="0"' . $media_background . '>';
			} elseif ($media_background) {
				$output .= '<div class="uk-inline-clip"' . $media_background . '>';
			}

			$output .= '<img class="el-image' . $image_svg_color . $image_transition . $image_styles . $media_blend_mode . '" src="' . $image_src . '" alt="' . str_replace ( '"', '', $alt_text ) . '"' . $image_svg_inline_cls . $image_loading_init . '>';
			$output .= $media_overlay;
			if ($image_transition) {
				$output .= '</div>';
			} elseif ($media_background) {
				$output .= '</div>';
			}

			$output .= ($link_type == 'use_link' && $title_link) ? '</a>' : '';

			$output .= ($link_type == 'use_modal' && $title_link) ? '</a>' : '';

			if ($title && $title_position == 'bottom') {
				$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';
				if ($title_heading_decoration == ' uk-heading-line') {
					$output .= '<span>';
					$output .= nl2br ( $title );
					$output .= '</span>';
				} else {
					$output .= nl2br ( $title );
				}
				$output .= '</' . $title_heading_selector . '>';
			}
			$output .= '</div>';
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
	public static function getFrontendEditor() {
		$output = '
		<#
		let heading_addon_margin = data.heading_addon_margin || "";
		let title = ( data.title ) ? data.title : "";
		var title_style = "";
		title_style = data.title_heading_style ? " uk-"+data.title_heading_style : "";
		title_style += data.title_heading_color ? " uk-"+data.title_heading_color : "";
		title_style += ( heading_addon_margin ) ? " uk-margin" + (( heading_addon_margin == "default" ) ? "" : "-" + heading_addon_margin ) : "";
		
		let title_heading_selector = data.title_heading_selector || "h3";
		var title_heading_decoration = data.title_heading_decoration ? " "+data.title_heading_decoration : "";

		let title_position = ( data.title_position ) ? data.title_position : "top";

		// Image 

		var image = {}
		if (typeof data.image !== "undefined" && typeof data.image.src !== "undefined") {
			image = data.image
		} else {
			image = {src: data.image}
		}

		let alt_text = ( data.alt_text ) ? data.alt_text : "";

		let link_type = ( data.link_type ) ? data.link_type : "";

		let link_type_cls = link_type == "use_modal" ? \' uk-lightbox="toggle: a[data-type]"\' : "";

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

		var image_styles  = ( data.image_border ) ? " "+data.image_border : "";
		image_styles += ( data.box_shadow ) ? " "+data.box_shadow : "";
		image_styles += ( data.hover_box_shadow ) ? " "+data.hover_box_shadow : "";

		let addon_margin = data.addon_margin || "";
		var general = "";
		
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";

		let image_panel      = ( data.image_panel ) ? 1 : "";
		let media_background = ( image_panel ) ? ( ( data.blend_bg_color ) ? \' style="background-color: \' + data.blend_bg_color + \';"\' : "" ) : "";
		let media_blend_mode = ( image_panel && media_background ) ? ( ( data.image_blend_modes ) ? " "+data.image_blend_modes : "" ) : "";
		let media_overlay    = ( image_panel ) ? ( ( data.media_overlay ) ? \'<div class="uk-position-cover" style="background-color: \' + data.media_overlay + \'"></div>\' : "" ) : "";

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

		let image_svg_inline     = ( data.image_svg_inline ) ? data.image_svg_inline : "";
		let image_svg_inline_cls = ( image_svg_inline ) ? " uk-svg" : "";
		let image_svg_color      = ( image_svg_inline ) ? ( ( data.image_svg_color ) ? " uk-text-"+data.image_svg_color : "" ) : "";

		var lightbox_init = ( render_link ) ? \' data-type="iframe"\' : \' data-type="image"\';

		if ( link_type == "use_modal" && _.isEmpty( render_link ) ) {
			render_link += image.src;
		}

		let ariaLabel = ( data.link_aria_label ) ? \' aria-label="\' + data.link_aria_label + \'" \' : "";

		#>

		<# if ( image.src ) { #>

			<div class="ui-addon-image{{ zindex_cls }}{{ general }}{{ max_width_cfg }}"{{{ animation }}}{{{ link_type_cls }}}>

			<# if ( title && title_position == "top" ) { #>
				<{{ title_heading_selector }} class="tm-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span><# } #>
					{{{ title }}}
				<# if (title_heading_decoration == " uk-heading-line") { #></span><# } #>
				</{{ title_heading_selector }}>
			<# } #>

			<# if (link_type == "use_modal" && render_link) { #>
				<a href=\'{{ render_link }}\' {{{ lightbox_init }}} data-caption="<h4 class=uk-margin-remove>{{ alt_text.replace(/"/g, "") }}</h4>"{{{ ariaLabel }}}>
			<# } #>

			<# if (link_type == "use_link" && render_link) { #>
				<a href=\'{{ render_link }}\'{{{ link_target }}} rel=\'{{ relfollow }}\'{{{ ariaLabel }}}>
			<# } #>

			<# if (image_transition) { #>
				<div class="uk-inline-clip uk-transition-toggle" tabindex="0"{{{ media_background }}}>
			<# } else if (media_background) { #>
				<div class="uk-inline-clip"{{{ media_background }}}>
			<# } #>

			<# if(image.src.indexOf("http://") == -1 && image.src.indexOf("https://") == -1){ #>
				<img class="el-image{{ image_svg_color }}{{ image_transition }}{{ image_styles }}{{ media_blend_mode }}" src=\'{{ pagebuilder_base + image.src }}\' alt="{{ alt_text.replace(/"/g, "") }}"{{ image_svg_inline_cls }}>
			<# } else { #>
				<img class="el-image{{ image_svg_color }}{{ image_transition }}{{ image_styles }}{{ media_blend_mode }}" src=\'{{ image.src }}\' alt="{{ alt_text.replace(/"/g, "") }}"{{ image_svg_inline_cls }}>
			<# } #>
			
			{{{ media_overlay }}}

			<# if (image_transition) { #>
				</div>
			<# } else if (media_background) { #>
				</div>
			<# } #>

			<# if (link_type == "use_link" && render_link) { #>
				</a>
			<# } #>

			<# if (link_type == "use_modal" && render_link) { #>
				</a>
			<# } #>

			<# if ( title && title_position == "bottom" ) { #>
				<{{ title_heading_selector }} class="tm-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span><# } #>
					{{{ title }}}
				<# if (title_heading_decoration == " uk-heading-line") { #></span><# } #>
				</{{ title_heading_selector }}>
			<# } #>

			</div>
		<# } #>

		';
		return $output;
	}
}
