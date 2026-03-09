<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonHeading extends JpagebuilderAddons {
	/**
	 * The addon frontend render method.
	 * The returned HTML string will render to the frontend page.
	 *
	 * @return string The HTML string.
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->addon->settings;
		$class = (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h2';
		$title_icon = (isset ( $settings->title_icon ) && $settings->title_icon) ? $settings->title_icon : '';
		$title_icon_position = (isset ( $settings->title_icon_position ) && $settings->title_icon_position) ? $settings->title_icon_position : 'before';

		$output = '';

		list ( $link, $target ) = JpagebuilderAddonHelper::parseLink ( $settings, 'title_link', [ 
				'url' => 'title_link',
				'new_tab' => 'link_new_tab'
		] );

		if ($title) {
			$output .= '<div class="jpb-addon jpb-addon-header' . $class . '">';
			$output .= ! empty ( $link ) ? '<a ' . $target . ' href="' . $link . '">' : '';
			$output .= '<' . $heading_selector . ' class="jpb-addon-title">';

			if ($title_icon) {
				$icon_arr = array_filter ( explode ( ' ', $title_icon ) );

				if (count ( $icon_arr ) === 1) {
					$title_icon = 'fa ' . $title_icon;
				}
			}

			if ($title_icon && $title_icon_position === 'before') {
				$output .= '<span class="' . $title_icon . ' jpb-addon-title-icon" aria-hidden="true"></span> ';
			}

			// CHeck if HTML tags should be stripped out
			if(isset($settings->remove_html_tags) && $settings->remove_html_tags == 1) {
				$title = strip_tags($title);
			}
			
			$output .= nl2br ( $title );

			if ($title_icon && $title_icon_position === 'after') {
				$output .= ' <span class="' . $title_icon . ' jpb-addon-title-icon" aria-hidden="true"></span>';
			}

			$output .= '</' . $heading_selector . '>';
			$output .= ! empty ( $link ) ? '</a>' : '';
			$output .= '</div>';
		}

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

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h2';
		$title_icon = (isset ( $settings->title_icon ) && $settings->title_icon) ? $settings->title_icon : '';

		$css = '';

		$colorType = (isset ( $settings->title_color->type ) && $settings->title_color->type) ? $settings->title_color->type : '';

		$settings->plain_title_color = JpagebuilderCSSHelper::parseColor ( $settings, 'title_color' );

		$settings->alignment = JpagebuilderCSSHelper::parseAlignment ( $settings, 'alignment' );
		$settings->title_text_shadow = JpagebuilderCSSHelper::parseBoxShadow ( $settings, 'title_text_shadow', true );

		$headingTypographyFallbacks = [ 
				'font' => 'title_font_family',
				'size' => 'title_fontsize',
				'line_height' => 'title_lineheight',
				'letter_spacing' => 'title_letterspace',
				'uppercase' => 'title_font_style.uppercase',
				'italic' => 'title_font_style.italic',
				'underline' => 'title_font_style.underline',
				'weight' => 'title_font_style.weight'
		];

		$headingTypography = $cssHelper->typography ( '.jpb-addon-header .jpb-addon-title', $settings, 'heading_typography', $headingTypographyFallbacks );
		$css .= $headingTypography;

		/**
		 * We've passed the font family here for the heading addon.
		 * As the the other typography field's are handled by the
		 * addon's global CSS settings.
		 */
		$titleProps = [ 
				'title_margin' => 'margin',
				'title_padding' => 'padding',
				'plain_title_color' => $colorType != "solid" ? '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-image' : 'color',
				'title_text_shadow' => 'text-shadow'
		];

		$units = [ 
				'title_margin' => false,
				'title_padding' => false,
				'plain_title_color' => false,
				'title_text_shadow' => false
		];
		$modifiers = [ 
				'title_margin' => 'spacing',
				'title_padding' => 'spacing'
		];

		$titleStyle = $cssHelper->generateStyle ( '.jpb-addon-header .jpb-addon-title', $settings, $titleProps, $units, $modifiers );
		$alignment = $cssHelper->generateStyle ( '.jpb-addon.jpb-addon-header', $settings, [ 
				'alignment' => 'text-align'
		], false );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-title', $settings, 'transform' );
		$css .= $transformCss;

		$css .= $alignment;
		$css .= $titleStyle;

		if (! empty ( $settings->title_font_family )) {
			$cssHelper->loadGoogleFont ( $settings->title_font_family );
		}

		if ($title_icon) {
			$iconColorStyle = $cssHelper->generateStyle ( $heading_selector . '.jpb-addon-title .jpb-addon-title-icon', $settings, [ 
					'title_icon_color' => 'color',
					'title_icon_color' => '-webkit-text-fill-color'
			], [ 
					'title_icon_color' => false
			] );
			$css .= $iconColorStyle;
		}

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

		$headingTypographyFallbacks = [ 
				'font' => 'data.title_font_family',
				'size' => 'data.title_fontsize',
				'line_height' => 'data.title_lineheight',
				'letter_spacing' => 'data.title_letterspace',
				'custom_letter_spacing' => 'data?.custom_letterspacing',
				'uppercase' => 'data.title_font_style?.uppercase',
				'italic' => 'data.title_font_style?.italic',
				'underline' => 'data.title_font_style?.underline',
				'weight' => 'data.title_font_style?.weight'
		];

		$output .= $lodash->color ( 'color', '.jpb-addon-title', 'data.title_color' );
		$output .= $lodash->color ( 'color', '.jpb-addon-title-icon', 'data.title_icon_color' );
		$output .= $lodash->unit ( '-webkit-text-fill-color', '.jpb-addon-title-icon', 'data.title_icon_color', '', false );

		$output .= $lodash->spacing ( 'margin', '.jpb-addon-title', 'data.title_margin' );
		$output .= $lodash->spacing ( 'padding', '.jpb-addon-title', 'data.title_padding' );
		$output .= $lodash->alignment ( 'text-align', '.jpb-addon-header', 'data.alignment' );
		$output .= $lodash->typography ( '.jpb-addon-header .jpb-addon-title', 'data.heading_typography', $headingTypographyFallbacks );
		$output .= $lodash->textShadow ( '.jpb-addon-title', 'data.title_text_shadow' );
		$output .= $lodash->generateTransformCss ( '.jpb-addon-title', 'data.transform' );

		$output .= '   
		</style>
        <div class="jpb-addon jpb-addon-header {{ data.class}}">
            <#
			let heading_selector = data.heading_selector || "h2";
			const isMenu = _.isObject(data.title_link) && data.title_link.type === "menu" && data.title_link?.menu;
			const isPage = _.isObject(data.title_link) && data.title_link.type === "page" && data.title_link?.page;
			const isUrl = _.isObject(data.title_link) && data.title_link.type === "url" && data.title_link?.url;
			const isOldUrl = _.isString(data.title_link) && data.title_link !== "";
			
			let rel="";

            if (isMenu || isPage || isUrl || isOldUrl) { 
				const urlObj = _.isObject(data.title_link) ? data.title_link : window.getSiteUrl(data.title_link, data.link_new_tab === 1 ? "_blank" : "");
				const {url, page, menu, type, new_tab, nofollow, noopener, noreferrer} = urlObj;
				const target = new_tab ? "_blank": "";
				
				rel += nofollow ? "nofollow": "";
				rel += noopener ? " noopener": "";
				rel += noreferrer ? " noreferrer": "";
			
				let newUrl = "";
				if(type === "url") newUrl = url;
				if(type === "menu") newUrl = menu;
				if(type === "page") newUrl = page ? `index.php?option=com_jpagebuilder&view=page&id=${page}` : "";
				
				#>
				
				<a href=\'{{ newUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'>
				<# } #>
                <{{ heading_selector }} class="jpb-addon-title">
                <#
                let icon_arr = (typeof data.title_icon !== "undefined" && data.title_icon) ? data.title_icon.split(" ") : "";
                let icon_name = icon_arr.length === 1 ? "fa "+data.title_icon : data.title_icon;
                
                if(data.title_icon && data.title_icon_position == "before"){ #><span class="{{ icon_name }} jpb-addon-title-icon"></span>
                <# } #>
                <span style="white-space: pre-wrap;" class="jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</span>
                <# if(data.title_icon && data.title_icon_position == "after"){ #> <span class="{{ icon_name }} jpb-addon-title-icon"></span> <# } #>
                </{{ heading_selector }}>
            <# if(!_.isEmpty(data.title_link) || data.title_link?.url){ #></a><# } #>
        </div>
        ';

		return $output;
	}
}