<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'restricted access' );

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
class JpagebuilderAddonsubnav extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$heading_addon_margin = (isset ( $settings->heading_addon_margin ) && $settings->heading_addon_margin) ? $settings->heading_addon_margin : '';
		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= ($heading_addon_margin) ? ' uk-margin' . (($heading_addon_margin == 'default') ? '' : '-' . $heading_addon_margin) : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';

		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$style = (isset ( $settings->style ) && $settings->style) ? ' uk-subnav-' . $settings->style : '';

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

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' uk-text-' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->alignment_breakpoint ) && $settings->alignment_breakpoint) ? '@' . $settings->alignment_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->alignment_fallback ) && $settings->alignment_fallback) ? ' uk-text-' . $settings->alignment_fallback : '') : '';
		$general .= $text_alignment . $text_breakpoint . $text_alignment_fallback;

		$flex_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' uk-flex-' . $settings->alignment : '';
		$flex_alignment_breakpoint = ($flex_alignment) ? ((isset ( $settings->alignment_breakpoint ) && $settings->alignment_breakpoint) ? '@' . $settings->alignment_breakpoint : '') : '';
		$flex_alignment_fallback = ($flex_alignment && $flex_alignment_breakpoint) ? ((isset ( $settings->alignment_fallback ) && $settings->alignment_fallback) ? ' uk-flex-' . $settings->alignment_fallback : '') : '';
		$flex_alignment .= $flex_alignment_breakpoint . $flex_alignment_fallback;

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

		$output .= '<div class="ui-subnav' . $zindex_cls . $general . $max_width_cfg . '"' . $animation . '>';
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
		$output .= '<ul class="uk-subnav uk-margin-remove-bottom' . $style . $flex_alignment . '">';

		if (isset ( $settings->ui_subnav_items ) && count ( ( array ) $settings->ui_subnav_items )) {
			foreach ( $settings->ui_subnav_items as $key => $item ) {
				list ( $title_link, $link_target ) = JpagebuilderAddonHelper::parseLink ( $item, 'link', [ 
						'new_tab' => 'target',
						'url' => 'url'
				] );

				$render_linkscroll = (empty ( $link_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

				$link_title = (isset ( $item->link_title ) && $item->link_title) ? ' title="' . $item->link_title . '"' : '';

				$title = isset ( $item->title ) ? $item->title : '';
				$output .= '<li class="ui-item">';
				$output .= ($title_link) ? '<a class="ui-link" href="' . $title_link . '"' . $link_target . $render_linkscroll . $link_title . '>' : '';
				$output .= $title;
				$output .= ($title_link) ? '</a>' : '';
				$output .= '</li>';
			}
		}

		$output .= '</ul>';

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
		let addon_margin = data.addon_margin || "";

		var general = "";
		
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";
		let style = ( data.style ) ? " uk-subnav-"+data.style : "";
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

		let text_alignment = data.alignment ? " uk-text-" + data.alignment : "";
		let text_breakpoint = (data.alignment && data.text_breakpoint) ? "@" + data.text_breakpoint : "";
		let text_alignment_fallback = (data.alignment && data.text_breakpoint && data.text_alignment_fallback) ? " uk-text-" + data.text_alignment_fallback : "";
		general += text_alignment + text_breakpoint + text_alignment_fallback + max_width_cfg;

		let flex_alignment = data.alignment ? " uk-flex-" + data.alignment : "";
		let flex_alignment_breakpoint = (data.alignment && data.text_breakpoint) ? "@" + data.text_breakpoint : "";
		let flex_alignment_fallback = (data.alignment && data.text_breakpoint && data.text_alignment_fallback) ? " uk-flex-" + data.text_alignment_fallback : "";
		flex_alignment += flex_alignment_breakpoint + flex_alignment_fallback;

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
		<# if( !_.isEmpty( data.title_addon ) ){ #>
			<{{ title_heading_selector }} class="tm-addon-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span> <# } #>
					{{{ data.title_addon }}}
			 	<# if (title_heading_decoration == " uk-heading-line") { #></span> <# } #>
			</{{ title_heading_selector }}>
		<# } #>
		
		<div class="ui-subnav{{ zindex_cls }}{{ general }}"{{{ animation }}}>

		<ul class="uk-subnav uk-margin-remove-bottom{{ style }}{{ flex_alignment }}">
		<#
		_.each(data.ui_subnav_items, function (item){
		
			const isUrlObj = _.isObject(item?.link) && (!!item?.link?.url || !!item?.link?.page || !!item?.link?.menu);
			const isUrlString = _.isString(item?.link) && item?.link !== "";
			
			const isTarget = item?.link_open_new_window ? "_blank" : "";
			const urlObj = item?.link?.url ? item?.link : window.getSiteUrl(item?.link, isTarget);
			const {url, new_tab, nofollow, type, } = urlObj;
			const target = new_tab ? "_blank" : "";

			const rel = nofollow ? "noopener noreferrer" : "";
			var itemUrl = (type === "url" && url) || (type === "menu" && urlObj.menu) || ((type === "page" && !!urlObj.page) && "index.php?option=com_jpagebuilder&view=page&id=" + urlObj.page) || "";

			let link_title = ( item.link_title ) ? \' title="\' + item.link_title.replace(/"/g, "") + \'"\' : "";
			let title      = item.title ? item.title : "";
			#>
			<li class="ui-item">
			<# if (itemUrl) { #>
				<a class="ui-link" href="{{ itemUrl }}" target=\'{{ target }}\' rel=\'{{ rel }}\'{{{ link_title }}}>
			<# } #>
			{{{ title }}}
			<# if (itemUrl) { #>
				</a>
			<# } #>
			</li>
		<# }); #>

		</ul>

		</div>

		';
		return $output;
	}
}
