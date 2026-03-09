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
class JpagebuilderAddonHeadingHighLighted extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$heading_addon_margin = (isset ( $settings->heading_addon_margin ) && $settings->heading_addon_margin) ? $settings->heading_addon_margin : '';
		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$before_title = (isset ( $settings->before_title ) && $settings->before_title) ? $settings->before_title : '';
		$after_title = (isset ( $settings->after_title ) && $settings->after_title) ? $settings->after_title : '';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? $settings->heading_style : '';

		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= ($heading_addon_margin) ? ' uk-margin' . (($heading_addon_margin == 'default') ? '' : '-' . $heading_addon_margin) : '';

		// New link
		list ( $title_link, $link_target ) = JpagebuilderAddonHelper::parseLink ( $settings, 'title_link', [ 
				'title_link' => 'link',
				'new_tab' => 'link_target'
		] );

		$text_transform = (isset ( $settings->text_transform ) && $settings->text_transform) ? ' uk-text-' . $settings->text_transform : '';

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

		$output = '';

		if (! empty ( $title )) {
			$output .= '<' . $heading_selector . ' class="tm-title' . $zindex_cls . $general . $title_style . $text_transform . '"' . $animation . '>';
			$output .= '<span class="heading-highlighted-wrapper">';
			$output .= ($title_link) ? '<a class="uk-link-heading" href="' . $title_link . '" ' . $link_target . '>' : '';
			$output .= ($before_title) ? '<span class="heading-plain-text uk-margin-small-right">' . $before_title . '</span>' : '';
			$output .= '<span class="heading-highlighted-text heading-highlighted-text-active">';
			$output .= nl2br ( $title );
			if ($heading_style == 'circle') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M325,18C228.7-8.3,118.5,8.3,78,21C22.4,38.4,4.6,54.6,5.6,77.6c1.4,32.4,52.2,54,142.6,63.7 c66.2,7.1,212.2,7.5,273.5-8.3c64.4-16.6,104.3-57.6,33.8-98.2C386.7-4.9,179.4-1.4,126.3,20.7"></path></svg>';
			} elseif ($heading_style == 'curly-line') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M3,146.1c17.1-8.8,33.5-17.8,51.4-17.8c15.6,0,17.1,18.1,30.2,18.1c22.9,0,36-18.6,53.9-18.6 c17.1,0,21.3,18.5,37.5,18.5c21.3,0,31.8-18.6,49-18.6c22.1,0,18.8,18.8,36.8,18.8c18.8,0,37.5-18.6,49-18.6c20.4,0,17.1,19,36.8,19 c22.9,0,36.8-20.6,54.7-18.6c17.7,1.4,7.1,19.5,33.5,18.8c17.1,0,47.2-6.5,61.1-15.6"></path></svg>';
			} elseif ($heading_style == 'double') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M8.4,143.1c14.2-8,97.6-8.8,200.6-9.2c122.3-0.4,287.5,7.2,287.5,7.2"></path><path d="M8,19.4c72.3-5.3,162-7.8,216-7.8c54,0,136.2,0,267,7.8"></path></svg>';
			} elseif ($heading_style == 'double-line') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M5,125.4c30.5-3.8,137.9-7.6,177.3-7.6c117.2,0,252.2,4.7,312.7,7.6"></path><path d="M26.9,143.8c55.1-6.1,126-6.3,162.2-6.1c46.5,0.2,203.9,3.2,268.9,6.4"></path></svg>';
			} elseif ($heading_style == 'zigzag') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M9.3,127.3c49.3-3,150.7-7.6,199.7-7.4c121.9,0.4,189.9,0.4,282.3,7.2C380.1,129.6,181.2,130.6,70,139 c82.6-2.9,254.2-1,335.9,1.3c-56,1.4-137.2-0.3-197.1,9"></path></svg>';
			} elseif ($heading_style == 'diagonal') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M13.5,15.5c131,13.7,289.3,55.5,475,125.5"></path></svg>';
			} elseif ($heading_style == 'underline') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M7.7,145.6C109,125,299.9,116.2,401,121.3c42.1,2.2,87.6,11.8,87.3,25.7"></path></svg>';
			} elseif ($heading_style == 'delete') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M497.4,23.9C301.6,40,155.9,80.6,4,144.4"></path><path d="M14.1,27.6c204.5,20.3,393.8,74,467.3,111.7"></path></svg>';
			} elseif ($heading_style == 'strike') {
				$output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M3,75h493.5"></path></svg>';
			}
			$output .= '</span>';
			$output .= ($after_title) ? '<span class="heading-plain-text uk-margin-small-left">' . $after_title . '</span>' : '';
			$output .= ($title_link) ? '</a>' : '';
			$output .= '</span>';
			$output .= '</' . $heading_selector . '>';
		}

		return $output;
	}
	public function css() {
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? 'color: ' . $settings->title_color . ';' : '';
		$title_font_size = (isset ( $settings->title_fontsize ) && $settings->title_fontsize) ? $settings->title_fontsize : '';
		$title_lineheight = (isset ( $settings->title_lineheight ) && $settings->title_lineheight) ? $settings->title_lineheight : '';

		$css = '';
		$svg = '';
		if ($title_color) {
			$css .= $addon_id . ' .heading-highlighted-wrapper {' . $title_color . '}';
		}
		if ($title_font_size) {
			$css .= $addon_id . ' .tm-title {font-size:' . $title_font_size . 'px;}';
		}
		if ($title_lineheight) {
			$css .= $addon_id . ' .tm-title {line-height:' . $title_lineheight . 'px;}';
		}
		$svg .= (isset ( $settings->color ) && $settings->color) ? ' stroke: ' . $settings->color . ';' : '';
		$svg .= (isset ( $settings->shapes_width ) && $settings->shapes_width) ? ' stroke-width: ' . $settings->shapes_width . ';' : '';
		if ($svg) {
			$css .= "\n";
			$css .= $addon_id . ' .heading-highlighted-wrapper svg path {' . $svg . '}';
			$css .= "\n";
		}
		return $css;
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
		return [ 
				'components/com_jpagebuilder/assets/css/uitheme.css',
				'components/com_jpagebuilder/addons/headinghighlighted/assets/heading-highlighted.css'
		];
	}
	public static function getFrontendEditor() {
		$output = '
		<style type="text/css">
		<# if(data.title_color) { #>
			#jpb-addon-{{ data.id }} .heading-highlighted-wrapper {
				color: {{ data.title_color }};
			}
		<# } #>

		<# if(data.color) { #>
			#jpb-addon-{{ data.id }} .heading-highlighted-wrapper svg path {
				stroke: {{ data.color }};
				<# if(data.shapes_width) { #>
					stroke-width: {{ data.shapes_width }};
				<# } #>
			}
		<# } #>
		<# if (data.title_fontsize || data.title_lineheight) { #>
			#jpb-addon-{{ data.id }} .tm-title {
				<# if (data.title_fontsize) { #>
					font-size: {{data.title_fontsize}}px;
				<# } #>
				<# if (data.title_lineheight) { #>
					line-height: {{data.title_lineheight}}px;
				<# } #>
			}
		<# } #>
		</style>
		<#
		let addon_margin = data.addon_margin || "";

		var general = "";
		
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";

		let title            = ( data.title ) ? data.title : "";
		let heading_selector = data.heading_selector || "h3";
		let before_title     = ( data.before_title ) ? data.before_title : "";
		let after_title      = ( data.after_title ) ? data.after_title : "";
		
		let heading_style    = ( data.heading_style ) ? data.heading_style : "";
		
		let title_style   = ( data.title_heading_style ) ? " uk-" + data.title_heading_style : "";
		title_style   += ( data.title_heading_margin ) ? " uk-margin-" + data.title_heading_margin +"-top" : " uk-margin-top";
	
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

		let text_transform = ( data.text_transform ) ? " uk-text-"+data.text_transform : "";

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

		<# if ( !_.isEmpty( title ) ) { #>
			<{{heading_selector}} class="tm-title{{ zindex_cls }}{{ general }}{{ title_style }}{{ text_transform }}"{{{ animation }}}>
			<span class="heading-highlighted-wrapper">
			<# if (render_link) { #>
				<a class="uk-link-heading" href=\'{{ render_link }}\'{{ link_target }} rel=\'{{ relfollow }}\'>
			<# } #>
			<# if (before_title) { #>
				<span class="heading-plain-text uk-margin-small-right">
					{{{ before_title }}}
				</span>
			<# } #>
			<span class="heading-highlighted-text heading-highlighted-text-active">
			{{{ title }}}
			<# if ( heading_style == "circle" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M325,18C228.7-8.3,118.5,8.3,78,21C22.4,38.4,4.6,54.6,5.6,77.6c1.4,32.4,52.2,54,142.6,63.7 c66.2,7.1,212.2,7.5,273.5-8.3c64.4-16.6,104.3-57.6,33.8-98.2C386.7-4.9,179.4-1.4,126.3,20.7"></path></svg>
			<# } else if ( heading_style == "curly-line" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M3,146.1c17.1-8.8,33.5-17.8,51.4-17.8c15.6,0,17.1,18.1,30.2,18.1c22.9,0,36-18.6,53.9-18.6 c17.1,0,21.3,18.5,37.5,18.5c21.3,0,31.8-18.6,49-18.6c22.1,0,18.8,18.8,36.8,18.8c18.8,0,37.5-18.6,49-18.6c20.4,0,17.1,19,36.8,19 c22.9,0,36.8-20.6,54.7-18.6c17.7,1.4,7.1,19.5,33.5,18.8c17.1,0,47.2-6.5,61.1-15.6"></path></svg>
			<# } else if ( heading_style == "double" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M8.4,143.1c14.2-8,97.6-8.8,200.6-9.2c122.3-0.4,287.5,7.2,287.5,7.2"></path><path d="M8,19.4c72.3-5.3,162-7.8,216-7.8c54,0,136.2,0,267,7.8"></path></svg>
			<# } else if ( heading_style == "double-line" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M5,125.4c30.5-3.8,137.9-7.6,177.3-7.6c117.2,0,252.2,4.7,312.7,7.6"></path><path d="M26.9,143.8c55.1-6.1,126-6.3,162.2-6.1c46.5,0.2,203.9,3.2,268.9,6.4"></path></svg>
			<# } else if ( heading_style == "zigzag" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M9.3,127.3c49.3-3,150.7-7.6,199.7-7.4c121.9,0.4,189.9,0.4,282.3,7.2C380.1,129.6,181.2,130.6,70,139 c82.6-2.9,254.2-1,335.9,1.3c-56,1.4-137.2-0.3-197.1,9"></path></svg>
			<# } else if ( heading_style == "diagonal" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M13.5,15.5c131,13.7,289.3,55.5,475,125.5"></path></svg>
			<# } else if ( heading_style == "underline" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M7.7,145.6C109,125,299.9,116.2,401,121.3c42.1,2.2,87.6,11.8,87.3,25.7"></path></svg>
			<# } else if ( heading_style == "delete" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M497.4,23.9C301.6,40,155.9,80.6,4,144.4"></path><path d="M14.1,27.6c204.5,20.3,393.8,74,467.3,111.7"></path></svg>
			<# } else if ( heading_style == "strike" ) { #>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M3,75h493.5"></path></svg>
			<# } #>
			</span>
			<# if (after_title) { #>
				<span class="heading-plain-text uk-margin-small-left">
					{{{ after_title }}}
				</span>
			<# } #>
			<# if (render_link) { #>
				</a>
			<# } #>
			</span>
			</{{heading_selector}}>

		<# } #>

		';
		return $output;
	}
}
