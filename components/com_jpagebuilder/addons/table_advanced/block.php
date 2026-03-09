<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Language\Text;
class JpagebuilderAddonTable_advanced extends JpagebuilderAddons {
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
		$turn_off_heading = (isset ( $settings->turn_off_heading ) && $settings->turn_off_heading) ? 1 : 0;
		$table_searchable = (isset ( $settings->table_searchable ) && $settings->table_searchable) ? 1 : 0;
		$table_pagination = (isset ( $settings->table_pagination ) && $settings->table_pagination) ? 1 : 0;
		$pagination_item = (isset ( $settings->pagination_item ) && $settings->pagination_item) ? $settings->pagination_item : '';
		$pagination_position = (isset ( $settings->pagination_position ) && $settings->pagination_position) ? ' ' . $settings->pagination_position : ' left-pagi';
		$total_entries = (isset ( $settings->total_entries ) && $settings->total_entries) ? 1 : 0;
		$total_entries_position = (isset ( $settings->total_entries_position ) && $settings->total_entries_position) ? 1 : 0;
		$search_column_limit = (isset ( $settings->search_column_limit ) && $settings->search_column_limit) ? $settings->search_column_limit : '';
		$table_sortable = (isset ( $settings->table_sortable ) && $settings->table_sortable) ? $settings->table_sortable : '';
		$table_text_alignment = (isset ( $settings->table_text_alignment ) && $settings->table_text_alignment) ? ' ' . $settings->table_text_alignment : '';
		$turn_off_responsive = (isset ( $settings->turn_off_responsive ) && $settings->turn_off_responsive) ? 1 : 0;

		// check if alignment is class or just the direction
		$txt_alignment = explode ( "-", $table_text_alignment );
		if (count ( $txt_alignment ) < 2) {
			$table_text_alignment = ' jpb-text-' . trim ( $table_text_alignment );
		}

		// Output
		$output = '<div class="jpb-addon jpb-addon-table' . $class . '' . $table_text_alignment . '' . ($turn_off_responsive ? ' jpb-addon-table-not-responsive' : '') . '">';
		$output .= '<div class="jpb-addon-content">';

		if ($table_searchable) {
			$output .= '<div class="jpb-addon-table-search-wrap">';
			$output .= '<input type="text" placeholder="' . Text::_ ( 'COM_JPAGEBUILDER_ADDON_TABLE_ADVANCED_SEARCH_PLACEHOLDER' ) . '" class="jpb-form-control jpb-addon-table-search">';
			$output .= '<i class="fa fa-search" aria-hidden="true"></i>';
			$output .= '</div>';
		}

		$output .= '<table class="jpb-addon-table-main' . ($turn_off_heading ? ' jpb-no-table-header' : '') . '" ' . ($table_searchable ? 'data-searchable="true"' : 'data-searchable="false"') . ' ' . ($search_column_limit ? 'data-search-limit="' . $search_column_limit . '"' : '') . ' ' . ($table_sortable ? 'data-sortable="true"' : 'data-sortable="false"') . ' ' . ($table_pagination && $pagination_item ? 'data-pagination-item="' . $pagination_item . '"' : '') . ' data-responsive="' . ($turn_off_responsive ? 'false' : 'true') . '">';

		if (! $turn_off_heading) {
			$output .= '<thead>';
			$output .= '<tr>';

			if (isset ( $settings->jp_table_advanced_item ) && is_array ( $settings->jp_table_advanced_item )) {
				foreach ( $settings->jp_table_advanced_item as $item_key => $item_value ) {
					$output .= '<th ' . ($table_sortable ? 'class="jpb-table-addon-sortable-data"' : '') . ' ' . ((isset ( $item_value->head_col_span ) && $item_value->head_col_span) ? 'colspan="' . $item_value->head_col_span . '"' : '') . '>' . (isset ( $item_value->content ) ? $item_value->content : '') . '</th>';
				}
			}

			$output .= '</tr>';
			$output .= '</thead>';
		}

		$output .= '<tbody>';

		if (isset ( $settings->table_advanced_item ) && is_array ( $settings->table_advanced_item )) {
			foreach ( $settings->table_advanced_item as $row_key => $row_value ) {
				$output .= '<tr>';

				if (isset ( $row_value->table_advanced_item ) && is_array ( $row_value->table_advanced_item )) {
					foreach ( $row_value->table_advanced_item as $data_key => $data_value ) {
						$output .= '<td ' . ((isset ( $data_value->row_span ) && $data_value->row_span) ? 'rowspan="' . $data_value->row_span . '"' : '') . ' ' . ((isset ( $data_value->col_span ) && $data_value->col_span) ? 'colspan="' . $data_value->col_span . '"' : '') . '' . (isset ( $data_value->td_inner_bg ) && $data_value->td_inner_bg ? 'style="background:' . $data_value->td_inner_bg . ';"' : '') . '>' . (isset ( $data_value->content ) ? $data_value->content : '') . '</td>';
					}
				}

				$output .= '</tr>';
			}
		}

		$output .= '</tbody>';
		$output .= '</table>';

		if ($table_pagination && $pagination_item) {
			$output .= '<div class="jpb-addon-table-pagination-wrap' . ($total_entries ? '' : $pagination_position) . '' . ($total_entries && $total_entries_position ? ' jpb-total-entries-to-left' : '') . '">';
			$output .= '<ul class="jpb-pagination"></ul>';

			if ($total_entries) {
				$output .= '<span class="jpb-table-total-reg"></span>';
			}

			$output .= '</div>';
		}

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
		$css = '';

		/**
		 * Header styles
		 */
		$headerSelector = '';
		$turnOffHeading = (isset ( $settings->turn_off_heading ) && $settings->turn_off_heading);
		$headerBackgroundType = isset ( $settings->header_bg_options ) && $settings->header_bg_options ? $settings->header_bg_options : '';

		if ($turnOffHeading) {
			$headerSelector .= '.jpb-addon-table-main tbody tr:first-child td,';
		}

		$headerSelector .= '.jpb-addon-table-main.bt tbody td:before,';
		$headerSelector .= '.jpb-addon-table-main.bt tbody td:before, .jpb-addon-table-main th';
		$headerStyle = $cssHelper->generateStyle ( $headerSelector, $settings, [ 
				'header_padding' => 'padding',
				'header_border' => 'border-style: solid; border-width',
				'header_border_color' => 'border-color',
				'header_color' => 'color',
				'header_bg_color' => $headerBackgroundType === 'color_bg' ? 'background' : null
		], [ 
				'header_padding' => false,
				'header_border' => false,
				'header_border_color' => false,
				'header_color' => false,
				'header_bg_color' => false
		], [ 
				'header_padding' => 'spacing'
		] );

		if ($headerBackgroundType === 'gradient_bg') {
			$gradientSelector = '';
			$settings->headerGradient = JpagebuilderCSSHelper::parseColor ( $settings, 'header_gradient_bg' );

			if ($turnOffHeading) {
				$gradientSelector .= '.jpb-addon-table-main tbody tr:first-child,';
			}

			$gradientSelector .= '.jpb-addon-table-main thead tr';

			$headerGradientStyle = $cssHelper->generateStyle ( $gradientSelector, $settings, [ 
					'headerGradient' => 'background'
			], false );

			$css .= $headerGradientStyle;
		}

		$paginationStyle = $cssHelper->generateStyle ( '.jpb-page-link', $settings, [ 
				'pagi_bg_color' => 'background',
				'pagi_padding' => 'padding',
				'pagi_color' => 'color',
				'pagi_border_color' => 'border-color',
				'pagi_border_width' => 'border-style: solid; border-width',
				'pagi_border_radius' => 'border-radius',
				'pagi_margin' => 'margin'
		], [ 
				'pagi_bg_color' => false,
				'pagi_color' => false,
				'pagi_border_color' => false,
				'pagi_padding' => false,
				'pagi_border_width' => false
		], [ 
				'pagi_padding' => 'spacing'
		] );

		$totalEntriesStyle = $cssHelper->generateStyle ( '.jpb-table-total-reg', $settings, [ 
				'total_entries_color' => 'color'
		], false );
		$settings->dummy_entity_typography = null;
		$totalEntityFontStyle = $cssHelper->typography ( '.jpb-table-total-reg', $settings, 'dummy_entity_typography', [ 
				'size' => 'total_entries_fontsize',
				'uppercase' => 'total_entries_font_style.uppercase',
				'italic' => 'total_entries_font_style.italic',
				'underline' => 'total_entries_font_style.underline',
				'weight' => 'total_entries_font_style.weight'
		] );
		$searchStyle = $cssHelper->generateStyle ( '.jpb-addon-table input[type="text"].jpb-addon-table-search', $settings, [ 
				'search_bg_color' => 'background',
				'search_text_color' => 'color',
				'search_padding' => 'padding',
				'search_border_color' => 'border-color',
				'search_border' => 'border-style: solid; border-width'
		], [ 
				'search_bg_color' => false,
				'search_text_color' => false,
				'search_padding' => false,
				'search_border_color' => false,
				'search_border' => false
		], [ 
				'search_padding' => 'spacing'
		] );

		$searchTextStyle = $cssHelper->generateStyle ( '.jpb-addon-table-search-wrap i, .jpb-addon-table input[type="text"].jpb-addon-table-search::placeholder, .jpb-addon-table input[type="text"].jpb-addon-table-search:focus', $settings, [ 
				'search_text_color' => 'color'
		], false );
		$searchMarginStyle = $cssHelper->generateStyle ( '.jpb-addon-table-search-wrap', $settings, [ 
				'search_margin_bottom' => 'margin-bottom'
		] );
		$sortStyle = $cssHelper->generateStyle ( '.jpb-table-addon-sortable', $settings, [ 
				'sort_margin_right' => 'right'
		] );
		$sortBorderStyle = $cssHelper->generateStyle ( '.jpb-table-addon-sortable:before, .jpb-table-addon-sortable:after', $settings, [ 
				'sort_color' => [ 
						'border-top-color',
						'border-bottom-color'
				]
		], false );
		$trStyle = $cssHelper->generateStyle ( '.jpb-addon-table-main tbody tr', $settings, [ 
				'tr_bg_color' => 'background'
		], false );
		$trEvenStyle = $cssHelper->generateStyle ( '.jpb-addon-table-main tbody tr:nth-child(even)', $settings, [ 
				'tr_second_bg_color' => 'background'
		], false );
		$trHoverStyle = $cssHelper->generateStyle ( '.jpb-addon-table-main tbody tr:hover', $settings, [ 
				'tr_hover_bg_color' => 'background'
		], false );
		$tdStyle = $cssHelper->generateStyle ( '.jpb-addon-table-main tr td', $settings, [ 
				'td_bg_color' => 'background',
				'td_padding_original' => 'padding'
		], [ 
				'td_bg_color' => false,
				'td_padding_original' => false
		], [ 
				'td_padding_original' => 'spacing'
		] );

		$tdStyle .= $cssHelper->generateStyle ( '.jpb-addon-table-main tr td', $settings, [ 
				'td_border' => 'border-style: solid; border-width',
				'td_border_color' => 'border-color'
		], '' );
		$tdEvenStyle = $cssHelper->generateStyle ( '.jpb-addon-table-main tr td:nth-child(even)', $settings, [ 
				'td_second_bg_color' => 'background'
		], false );
		$paginationMarginStyle = $cssHelper->generateStyle ( '.jpb-addon-table-pagination-wrap .jpb-pagination', $settings, [ 
				'pagi_margin' => 'margin'
		] );
		$paginationHoverStyle = $cssHelper->generateStyle ( '.jpb-page-item:not(.active) .jpb-page-link:hover', $settings, [ 
				'pagi_hover_bg_color' => 'background',
				'pagi_hover_color' => 'color',
				'pagi_hover_border_color' => 'border-color'
		], false );
		$paginationActiveStyle = $cssHelper->generateStyle ( '.jpb-page-item.active .jpb-page-link', $settings, [ 
				'pagi_active_bg_color' => 'background',
				'pagi_active_color' => 'color',
				'pagi_active_border_color' => 'border-color'
		], false );
		$transformCss = $cssHelper->generateTransformStyle ( ':self', $settings, 'transform' );

		$css .= $transformCss;

		$css .= $trStyle;
		$css .= $tdStyle;
		$css .= $sortStyle;
		$css .= $searchStyle;
		$css .= $headerStyle;
		$css .= $trEvenStyle;
		$css .= $tdEvenStyle;
		$css .= $trHoverStyle;
		$css .= $sortBorderStyle;
		$css .= $paginationStyle;
		$css .= $searchTextStyle;
		$css .= $searchMarginStyle;
		$css .= $totalEntriesStyle;
		$css .= $totalEntityFontStyle;
		$css .= $paginationHoverStyle;
		$css .= $paginationMarginStyle;
		$css .= $paginationActiveStyle;

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

		$output = '
		<style type="text/css">
		<# if(data.turn_off_heading) { #>
			#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:first-child td,
		<# } #>
		#jpb-addon-{{ data.id }} .jpb-addon-table-main.bt tbody td:before,
		#jpb-addon-{{ data.id }} .jpb-addon-table-main th {
			<# if(data.header_bg_options == "color_bg"){ #>
				background: {{data.header_bg_color}};
			<# }
			if(_.isObject(data.header_padding)) {
			#>
				padding: {{data.header_padding.md}};
			<# } else { #>
				padding: {{data.header_padding}};
			<# }
			if(_.trim(data.header_border)) {
			#>
				border-width: {{data.header_border}};
				border-style:solid;
			<# } #>
			border-color: {{data.header_border_color}};
			color: {{data.header_color}};
		}
		<# if(data.turn_off_heading) { #>
			#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:first-child,
		<# } #>
		#jpb-addon-{{ data.id }} .jpb-addon-table-main thead tr {
			<# if(data.header_bg_options == "gradient_bg"){
				let header_gradient_bg = (!_.isEmpty(data.header_gradient_bg) && data.header_gradient_bg) ? data.header_gradient_bg : "";
				let header_gradient_color1 = (_.isObject(header_gradient_bg) && header_gradient_bg.color) ? header_gradient_bg.color : "";
				let header_gradient_color2 = (_.isObject(header_gradient_bg) && header_gradient_bg.color2) ? header_gradient_bg.color2 : "";
				let header_degree = (_.isObject(header_gradient_bg) && header_gradient_bg.deg) ? header_gradient_bg.deg : "45";
				let header_type = (_.isObject(header_gradient_bg) && header_gradient_bg.type) ? header_gradient_bg.type : "linear";
				let header_radialPos = (_.isObject(header_gradient_bg) && header_gradient_bg.radialPos) ? header_gradient_bg.radialPos : "center center";
				let header_radial_angle1 = (_.isObject(header_gradient_bg) && header_gradient_bg.pos) ? header_gradient_bg.pos : "10";
				let header_radial_angle2 = (_.isObject(header_gradient_bg) && header_gradient_bg.pos2) ? header_gradient_bg.pos2 : "100";
	
				if(header_type !== "radial"){
			#>
					background: -webkit-linear-gradient({{header_degree}}deg, {{header_gradient_color1}} {{header_radial_angle1}}%, {{header_gradient_color2}} {{header_radial_angle2}}%) transparent;
					background: linear-gradient({{header_degree}}deg, {{header_gradient_color1}} {{header_radial_angle1}}%, {{header_gradient_color2}} {{header_radial_angle2}}%) transparent;
				<# } else { #>
					background: -webkit-radial-gradient(at {{header_radialPos}}, {{header_gradient_color1}} {{header_radial_angle1}}%, {{header_gradient_color2}} {{header_radial_angle2}}%) transparent;
					background: radial-gradient(at {{header_radialPos}}, {{header_gradient_color1}} {{header_radial_angle1}}%, {{header_gradient_color2}} {{header_radial_angle2}}%) transparent;
				<# }
			} #>
		}

		#jpb-addon-{{ data.id }} .jpb-table-addon-sortable {
			right: {{data.sort_margin_right}}px;
		}

		#jpb-addon-{{ data.id }} .jpb-table-addon-sortable:before,
		#jpb-addon-{{ data.id }} .jpb-table-addon-sortable:after {
			border-top-color: {{data.sort_color}};
			border-bottom-color: {{data.sort_color}};
		}
	
		#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr {
			background: {{data.tr_bg_color}};
		}
		#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:hover {
			background: {{data.tr_hover_bg_color}};
		}
		#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:nth-child(even) {
			background: {{data.tr_second_bg_color}};
		}
		#jpb-addon-{{ data.id }} .jpb-addon-table-main tr td:nth-child(even) {
			background: {{data.td_second_bg_color}};
		}
		#jpb-addon-{{ data.id }} .jpb-addon-table-main tr td {
			<# if(_.trim(data.td_border)) { #>
				border-width: {{data.td_border}};
				border-style:solid;
			<# } #>
			border-color: {{data.td_border_color}};
			background: {{data.td_bg_color}};
			<# if(_.isObject(data.td_padding)){ #>
				padding: {{data.td_padding.md}};
			<# } else { #>
				padding: {{data.td_padding}};
			<# } #>
		}
		
		#jpb-addon-{{ data.id }} .jpb-page-link {
			background: {{data.pagi_bg_color}};
			color: {{data.pagi_color}};
			border-color: {{data.pagi_border_color}};
			border-width: {{data.pagi_border_width}};
			<# if(data.pagi_border_width) { #>
				border-style:solid;
			<# } #>
			border-radius:{{data.pagi_border_radius}}px;
			margin: {{data.pagi_margin}}px;
			<# if(_.trim(data.pagi_padding)) { #>
				padding: {{data.pagi_padding}};
			<# } #>
		}
		<# if(data.pagi_margin){ #>
			#jpb-addon-{{ data.id }} .jpb-addon-table-pagination-wrap .jpb-pagination {
				margin: -{{data.pagi_margin}}px;
			}
		<# } #>
		
		#jpb-addon-{{ data.id }} .jpb-page-item:not(.active) .jpb-page-link:hover {
			background: {{data.pagi_hover_bg_color}};
			color: {{data.pagi_hover_color}};
			border-color: {{data.pagi_hover_border_color}};
		}

		#jpb-addon-{{ data.id }} .jpb-page-item.active .jpb-page-link {
			color: {{data.pagi_active_color}};
			background: {{data.pagi_active_bg_color}};
			border-color: {{data.pagi_active_border_color}};
		}
		#jpb-addon-{{ data.id }} .jpb-addon-table input[type="text"].jpb-addon-table-search {
			background: {{data.search_bg_color}};
			color: {{data.search_text_color}};
			<# if(_.trim(data.search_padding)) { #>
				padding: {{data.search_padding}};
			<# } #>
			border-color: {{data.search_border_color}};
			border-width: {{data.search_border}};
			<# if(data.search_border) { #>
				border-style:solid;
			<# } #>
		}

		#jpb-addon-{{ data.id }} .jpb-addon-table-search-wrap {
			margin-bottom: {{data.search_margin_bottom}}px;
		}
		#jpb-addon-{{ data.id }} .jpb-addon-table-search-wrap i,
		#jpb-addon-{{ data.id }} .jpb-addon-table input[type="text"].jpb-addon-table-search::placeholder,
		#jpb-addon-{{ data.id }} .jpb-addon-table input[type="text"].jpb-addon-table-search:focus {
			color: {{data.search_text_color}};
		}

		#jpb-addon-{{ data.id }} .jpb-table-total-reg {
			color: {{data.total_entries_color}};
			font-size: {{data.total_entries_fontsize}}px;
			<# if(_.isObject(data.total_entries_font_style)) {
				if(data.total_entries_font_style.underline){
			#>
					text-decoration:underline;
				<# }
				if(data.total_entries_font_style.italic){
				#>
					font-style:italic;
				<# }
				if(data.total_entries_font_style.uppercase){
				#>
					text-transform:uppercase;
				<# }
				if(data.total_entries_font_style.weight){
				#>
					font-weight:{{data.total_entries_font_style.weight}};
				<# }
			} #>
		}

		@media (min-width: 1200px) {
			#jpb-addon-{{ data.id }} .jpb-addon-table-main tr td  {
				<# if(_.isObject(data.td_padding)){ #>
					padding: {{data.td_padding.xl}};
				<# } #>
			}
			<# if(data.turn_off_heading) { #>
				#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:first-child td,
			<# } #>
			#jpb-addon-{{ data.id }} .jpb-addon-table-main.bt tbody td:before,
			#jpb-addon-{{ data.id }} .jpb-addon-table-main th {
				<# if(_.isObject(data.header_padding)) { #>
					padding: {{data.header_padding.xl}};
				<# } #>
			}
		}

		@media (min-width: 991.98px) and (max-width: 1199.98px) {
			#jpb-addon-{{ data.id }} .jpb-addon-table-main tr td  {
				<# if(_.isObject(data.td_padding)){ #>
					padding: {{data.td_padding.lg}};
				<# } #>
			}
			<# if(data.turn_off_heading) { #>
				#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:first-child td,
			<# } #>
			#jpb-addon-{{ data.id }} .jpb-addon-table-main.bt tbody td:before,
			#jpb-addon-{{ data.id }} .jpb-addon-table-main th {
				<# if(_.isObject(data.header_padding)) { #>
					padding: {{data.header_padding.lg}};
				<# } #>
			}
		}

		@media (min-width: 991.98px) and (max-width: 1199.98px) {
			#jpb-addon-{{ data.id }} .jpb-addon-table-main tr td  {
				<# if(_.isObject(data.td_padding)){ #>
					padding: {{data.td_padding.lg}};
				<# } #>
			}
			<# if(data.turn_off_heading) { #>
				#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:first-child td,
			<# } #>
			#jpb-addon-{{ data.id }} .jpb-addon-table-main.bt tbody td:before,
			#jpb-addon-{{ data.id }} .jpb-addon-table-main th {
				<# if(_.isObject(data.header_padding)) { #>
					padding: {{data.header_padding.lg}};
				<# } #>
			}
		}

		@media (min-width: 768px) and (max-width: 991px) {
			#jpb-addon-{{ data.id }} .jpb-addon-table-main tr td  {
				<# if(_.isObject(data.td_padding)){ #>
					padding: {{data.td_padding.sm}};
				<# } #>
			}
			<# if(data.turn_off_heading) { #>
				#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:first-child td,
			<# } #>
			#jpb-addon-{{ data.id }} .jpb-addon-table-main.bt tbody td:before,
			#jpb-addon-{{ data.id }} .jpb-addon-table-main th {
				<# if(_.isObject(data.header_padding)) { #>
					padding: {{data.header_padding.sm}};
				<# } #>
			}
		}
		@media (max-width: 767px) {
			#jpb-addon-{{ data.id }} .jpb-addon-table-main tr td  {
				<# if(_.isObject(data.td_padding)){ #>
					padding: {{data.td_padding.xs}};
				<# } #>
			}
			<# if(data.turn_off_heading) { #>
				#jpb-addon-{{ data.id }} .jpb-addon-table-main tbody tr:first-child td,
			<# } #>
			#jpb-addon-{{ data.id }} .jpb-addon-table-main.bt tbody td:before,
			#jpb-addon-{{ data.id }} .jpb-addon-table-main th {
				<# if(_.isObject(data.header_padding)) { #>
					padding: {{data.header_padding.xs}};
				<# } #>
			}
		}';

		$output .= $lodash->generateTransformCss ( '', 'data.transform' );

		$output .= '</style>';
		return $output;
	}
}
