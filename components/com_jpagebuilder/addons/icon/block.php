<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonIcon extends JpagebuilderAddons {
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
		$class .= (isset ( $settings->hover_effect ) && $settings->hover_effect) ? ' jpb-icon-hover-effect-' . $settings->hover_effect : '';
		$name = (isset ( $settings->name ) && $settings->name) ? $settings->name : '';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		// $link = (isset($settings->link) && $settings->link) ? $settings->link : '';
		// $target = (isset($settings->target) && $settings->target) ? 'rel="noopener noreferrer" target="' . $settings->target . '"' : '';

		list ( $link, $target ) = JpagebuilderAddonHelper::parseLink ( $settings, 'title_link', [ 
				'url' => 'link',
				'new_tab' => 'target'
		] );

		if ($name) {
			$output = '<div class="jpb-icon ' . $class . '">';
			if (! empty ( $link )) {
				$output .= '<a ' . $target . ' href="' . $link . '">';
			}
			$output .= '<span class="jpb-icon-inner">';

			$icon_arr = array_filter ( explode ( ' ', $name ) );
			if (count ( $icon_arr ) === 1) {
				$name = 'fa ' . $name;
			}

			$output .= '<i class="' . $name . '" aria-hidden="true" title="' . $title . '"></i>';
			$output .= '<span class="jpb-form-label-visually-hidden">' . (empty ( $title ) ? $name : $title) . '</span>';
			$output .= '</span>';
			if (! empty ( $link )) {
				$output .= '</a>';
			}
			$output .= '</div>';
			return $output;
		}
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
		$settings->alignment = $cssHelper->parseAlignment ( $settings, 'alignment' );

		$iconStyle = $cssHelper->generateStyle ( '.jpb-icon-inner', $settings, [ 
				'margin' => 'margin',
				'height' => 'height',
				'width' => 'width',
				'border_radius' => 'border-radius',
				'border_width' => 'border-width',
				'color' => 'color',
				'background' => 'background-color',
				'border_color' => 'border-style: solid;border-color:%s'
		], [ 
				'margin' => false,
				'color' => false,
				'background' => false,
				'border_color' => false
		], [ 
				'margin_original' => 'spacing'
		] );
		$fontStyle = $cssHelper->generateStyle ( '.jpb-icon-inner i', $settings, [ 
				'height' => 'line-height',
				'size' => 'font-size',
				'border_width' => 'margin-top:-%s'
		] );
		$iconHoverStyle = $cssHelper->generateStyle ( '.jpb-icon-inner:hover', $settings, [ 
				'hover_color' => 'color',
				'hover_background' => 'background-color',
				'hover_border_color' => 'border-color',
				'hover_border_width' => 'border-width',
				'hover_border_radius' => 'border-radius'
		], [ 
				'hover_color' => false,
				'hover_background' => false,
				'hover_border_color' => false
		] );

		$css .= $iconStyle;
		$css .= $iconHoverStyle;
		$css .= $fontStyle;

		$css .= $cssHelper->generateStyle ( ':self', $settings, [ 
				'alignment' => 'text-align'
		], false );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-icon-inner', $settings, 'transform' );
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
		$output = '<style type="text/css">';

		$output .= $lodash->alignment ( 'text-align', '.jpb-icon', 'data.alignment' );
		$output .= $lodash->color ( 'background-color', '.jpb-icon-inner', 'data.background' );
		$output .= $lodash->color ( 'color', '.jpb-icon-inner', 'data.color' );
		$output .= $lodash->spacing ( 'margin', '.jpb-icon-inner', 'data.margin' );
		$output .= $lodash->unit ( 'height', '.jpb-icon-inner', 'data.height', 'px' );
		$output .= $lodash->unit ( 'width', '.jpb-icon-inner', 'data.width', 'px' );

		$output .= '<# if (data.border_width) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-icon-inner {border-style: solid;}';
		$output .= $lodash->unit ( 'border-color', '.jpb-icon-inner', 'data.border_color', '', false );
		$output .= $lodash->unit ( 'border-width', '.jpb-icon-inner', 'data.border_width', 'px' );
		$output .= '<# } #>';
		$output .= $lodash->unit ( 'border-radius', '.jpb-icon-inner', 'data.border_radius', 'px' );

		$output .= $lodash->unit ( 'font-size', '.jpb-icon-inner i', 'data.size', 'px' );
		$output .= $lodash->unit ( 'line-height', '.jpb-icon-inner i', 'data.height', 'px' );
		$output .= $lodash->unit ( 'margin-top', '.jpb-icon-inner i', 'data.border_width', 'px', true, '-' );

		// Hover
		$output .= $lodash->color ( 'color', '.jpb-icon-inner:hover', 'data.hover_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-icon-inner:hover', 'data.hover_background' );
		$output .= $lodash->unit ( 'border-color', '.jpb-icon-inner:hover', 'data.hover_border_color', '', false );
		$output .= $lodash->unit ( 'border-width', '.jpb-icon-inner:hover', 'data.hover_border_width', 'px' );
		$output .= $lodash->unit ( 'border-radius', '.jpb-icon-inner:hover', 'data.hover_border_radius', 'px' );
		$output .= $lodash->generateTransformCss ( '.jpb-icon-inner', 'data.transform' );

		$output .= '
		</style>
		<# 
		let hover_effect_class = (!_.isEmpty(data.hover_effect) && data.hover_effect) ? ` jpb-icon-hover-effect-${data.hover_effect}` : "";

		if (data.name) { #>
			<div class="jpb-icon {{ data.class }} {{ hover_effect_class }}">
				<# 
				const isMenu = _.isObject(data.title_link) && data.title_link.type === "menu" && data.title_link?.menu;
				const isPage = _.isObject(data.title_link) && data.title_link.type === "page" && data.title_link?.page;
				const isUrl = _.isObject(data.title_link) && data.title_link.type === "url" && data.title_link?.url;
				const isOldUrl = _.isString(data.link) && !_.isEmpty(data.link);

				if (isMenu || isPage || isUrl || isOldUrl) {
					const urlObj = data?.title_link || window.getSiteUrl(data.link, data.target || false);
					const {url, page, menu, type, new_tab, nofollow, noopener, noreferrer} = urlObj;
					const target = new_tab ? "_blank": "";
					
					let rel = "";
					rel += nofollow ? "nofollow": "";
					rel += noopener ? " noopener": "";
					rel += noreferrer ? " noreferrer": "";
					
					let newUrl = "";
					if(type === "url") newUrl = url;
					if(type === "menu") newUrl = menu;
					if(type === "page") newUrl = page ? `index.php?option=com_jpagebuilder&view=page&id=${page}` : "";
					#>
					<a href=\'{{ newUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'>
				<# }
				let icon_arr = (typeof data.name !== "undefined" && data.name) ? data.name.split(" ") : "";
                let icon_name = icon_arr.length === 1 ? "fa " + data.name : data.name;
				let icon_title = data.title ? data.title : icon_name;
				#>
				<span class="jpb-icon-inner">
					<i class="{{ icon_name }}" title={{data.title}}></i>
					<span class="jpb-form-label-visually-hidden">{{ icon_title }}</span>
				</span>
				<# if (isMenu || isPage || isUrl || isOldUrl) { #>
					</a>
				<# } #>
			</div>
		<# } #>';

		return $output;
	}
}