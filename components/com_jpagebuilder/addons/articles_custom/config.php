<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('restricted access');

use Joomla\CMS\Language\Text;

JpagebuilderConfig::addonConfig ( array (
		'type' => 'content',
		'addon_name' => 'articles_custom',
		'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES' ) . ' Custom',
		'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_DESC' ),
		'category' => 'Content',
		'icon' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path opacity=".5" d="M11.643 9.571h-.603L8.138 1.246A.363.363 0 007.804 1h-1.63a.363.363 0 00-.335.246L2.937 9.57h-.58c-.2 0-.357.179-.357.358v.714c0 .2.156.357.357.357h3.036a.367.367 0 00.357-.357v-.714a.384.384 0 00-.357-.358h-.536l.58-1.785h3.08l.604 1.785h-.514c-.2 0-.357.179-.357.358v.714c0 .2.156.357.357.357h3.036a.367.367 0 00.357-.357v-.714a.384.384 0 00-.357-.358zm-5.76-3.28l.938-2.769c.09-.357.157-.647.179-.78 0 .155.045.446.156.78l.938 2.768h-2.21z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M30 16a1 1 0 01-1 1H3a1 1 0 110-2h26a1 1 0 011 1zM30 23a1 1 0 01-1 1H3a1 1 0 110-2h26a1 1 0 011 1zM16 30a1 1 0 01-1 1H3a1 1 0 110-2h12a1 1 0 011 1zM30 9a1 1 0 01-1 1H16a1 1 0 110-2h13a1 1 0 011 1zM30 2a1 1 0 01-1 1H16a1 1 0 110-2h13a1 1 0 011 1z" fill="currentColor"/></svg>',
		'attr' => array (
				'general' => array (
						'admin_label' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ADMIN_LABEL_DESC' ),
								'std' => ''
						),

						'title' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_DESC' ),
								'std' => ''
						),

						'heading_selector' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS_DESC' ),
								'values' => array (
										'h1' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS_H1' ),
										'h2' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS_H2' ),
										'h3' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS_H3' ),
										'h4' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS_H4' ),
										'h5' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS_H5' ),
										'h6' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_HEADINGS_H6' )
								),
								'std' => 'h3',
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								)
						),

						'title_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_FONT_FAMILY' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_FONT_FAMILY_DESC' ),
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.sppb-addon-title { font-family: "{{ VALUE }}"; }'
								)
						),

						'title_fontsize' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_FONT_SIZE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_FONT_SIZE_DESC' ),
								'std' => '',
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								),
								'responsive' => true,
								'max' => 400
						),

						'title_lineheight' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_LINE_HEIGHT' ),
								'std' => '',
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								),
								'responsive' => true,
								'max' => 400
						),

						'title_font_style' => array (
								'type' => 'fontstyle',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_FONT_STYLE' ),
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								)
						),

						'title_letterspace' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LETTER_SPACING' ),
								'values' => array (
										'0' => 'Default',
										'1px' => '1px',
										'2px' => '2px',
										'3px' => '3px',
										'4px' => '4px',
										'5px' => '5px',
										'6px' => '6px',
										'7px' => '7px',
										'8px' => '8px',
										'9px' => '9px',
										'10px' => '10px'
								),
								'std' => '0',
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								)
						),

						'title_text_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_TEXT_COLOR' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_TEXT_COLOR_DESC' ),
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								)
						),

						'title_margin_top' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_MARGIN_TOP' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_MARGIN_TOP_DESC' ),
								'placeholder' => '10',
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								),
								'responsive' => true,
								'max' => 400
						),

						'title_margin_bottom' => array (
								'type' => 'slider',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_MARGIN_BOTTOM' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_TITLE_MARGIN_BOTTOM_DESC' ),
								'placeholder' => '10',
								'depends' => array (
										array (
												'title',
												'!=',
												''
										)
								),
								'responsive' => true,
								'max' => 400
						),

						'separator_options' => array (
								'type' => 'separator',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_ADDON_OPTIONS' )
						),

						'resource' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLE_RESOURCE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLE_RESOURCE_DESC' ),
								'values' => array (
										'article' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLE_RESOURCE_ARTICLE' )
								),
								'std' => 'article'
						),

						'catid' => array (
								'type' => 'category',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_CATID' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_CATID_DESC' ),
								'depends' => array (
										'resource' => 'article'
								),
								'multiple' => true
						),

						'tagids' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_TAGS' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_TAGS_DESC' ),
								'depends' => array (
										'resource' => 'article'
								),
								'values' => JpagebuilderBase::getArticleTags (),
								'multiple' => true
						),

						'post_type' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_DESC' ),
								'values' => array (
										'' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_ALL' ),
										'standard' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_STANDARD' ),
										'audio' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_AUDIO' ),
										'video' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_VIDEO' ),
										'gallery' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_GALLERY' ),
										'link' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_LINK' ),
										'quote' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_QUOTE' ),
										'status' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_POST_TYPE_STATUS' )
								),
								'std' => '',
								'depends' => array (
										'resource' => 'article'
								)
						),

						'include_subcat' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_INCLUDE_SUBCATEGORIES' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_INCLUDE_SUBCATEGORIES_DESC' ),
								'values' => array (
										1 => Text::_ ( 'COM_JPAGEBUILDER_YES' ),
										0 => Text::_ ( 'COM_JPAGEBUILDER_NO' )
								),
								'std' => 1
						),

						'ordering' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING_DESC' ),
								'values' => array (
										'latest' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING_LATEST' ),
										'oldest' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING_OLDEST' ),
										'hits' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING_POPULAR' ),
										'featured' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING_FEATURED' ),
										'alphabet_asc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING_ALPHABET_ASC' ),
										'alphabet_desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING_ALPHABET_DESC' ),
										'random' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ORDERING_RANDOM' )
								),
								'std' => 'latest'
						),

						'show_intro' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_INTRO' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_INTRO_DESC' ),
								'std' => 1
						),

						'intro_limit' => array (
								'type' => 'number',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_INTRO_LIMIT' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_INTRO_LIMIT_DESC' ) . " We recommend 60 characters.",
								'std' => '60',
								'depends' => array (
										'show_intro' => '1'
								)
						),

						'link_articles' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ALL_ARTICLES_BUTTON' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ALL_ARTICLES_BUTTON_DESC' ),
								'values' => array (
										1 => Text::_ ( 'COM_JPAGEBUILDER_YES' ),
										0 => Text::_ ( 'COM_JPAGEBUILDER_NO' )
								),
								'std' => 0
						),

						'link_catid' => array (
								'type' => 'category',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_CATID' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_CATID_DESC' ),
								'depends' => array (
										array (
												'resource',
												'=',
												'article'
										),
										array (
												'link_articles',
												'=',
												'1'
										)
								)
						),

						'all_articles_btn_text' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ALL_ARTICLES_BUTTON_TEXT' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_ALL_ARTICLES_BUTTON_TEXT_DESC' ),
								'std' => 'See all posts',
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'all_articles_btn_font_family' => array (
								'type' => 'fonts',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_FONT_FAMILY' ),
								'depends' => array (
										'link_articles' => '1'
								),
								'selector' => array (
										'type' => 'font',
										'font' => '{{ VALUE }}',
										'css' => '.sppb-btn { font-family: "{{ VALUE }}"; }'
								)
						),

						'all_articles_btn_font_style' => array (
								'type' => 'fontstyle',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_FONT_STYLE' ),
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'all_articles_btn_letterspace' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_LETTER_SPACING' ),
								'values' => array (
										'0' => 'Default',
										'1px' => '1px',
										'2px' => '2px',
										'3px' => '3px',
										'4px' => '4px',
										'5px' => '5px',
										'6px' => '6px',
										'7px' => '7px',
										'8px' => '8px',
										'9px' => '9px',
										'10px' => '10px'
								),
								'std' => '0',
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'all_articles_btn_type' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_STYLE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_STYLE_DESC' ),
								'values' => array (
										'default' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_DEFAULT' ),
										'primary' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_PRIMARY' ),
										'secondary' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_SECONDARY' ),
										'success' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_SUCCESS' ),
										'info' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_INFO' ),
										'warning' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_WARNING' ),
										'danger' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_DANGER' ),
										'dark' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_DARK' ),
										'link' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LINK' ),
										'custom' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_CUSTOM' )
								),
								'std' => 'default',
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'all_articles_btn_appearance' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_DESC' ),
								'values' => array (
										'' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_FLAT' ),
										'outline' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_OUTLINE' ),
										'3d' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_3D' )
								),
								'std' => 'flat',
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'all_articles_btn_background_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BACKGROUND_COLOR' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BACKGROUND_COLOR_DESC' ),
								'std' => '#444444',
								'depends' => array (
										array (
												'link_articles',
												'=',
												'1'
										),
										array (
												'all_articles_btn_type',
												'=',
												'custom'
										)
								)
						),

						'all_articles_btn_color' => array (
								'type' => 'color',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_COLOR' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_COLOR_DESC' ),
								'std' => '#fff',
								'depends' => array (
										array (
												'link_articles',
												'=',
												'1'
										),
										array (
												'all_articles_btn_type',
												'=',
												'custom'
										)
								)
						),

						'all_articles_btn_background_color_hover' => array (
								'type' => 'color',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BACKGROUND_COLOR_HOVER' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BACKGROUND_COLOR_HOVER_DESC' ),
								'std' => '#222',
								'depends' => array (
										array (
												'link_articles',
												'=',
												'1'
										),
										array (
												'all_articles_btn_type',
												'=',
												'custom'
										)
								)
						),

						'all_articles_btn_color_hover' => array (
								'type' => 'color',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_COLOR_HOVER' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_COLOR_HOVER_DESC' ),
								'std' => '#fff',
								'depends' => array (
										array (
												'link_articles',
												'=',
												'1'
										),
										array (
												'all_articles_btn_type',
												'=',
												'custom'
										)
								)
						),

						'all_articles_btn_size' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_SIZE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_SIZE_DESC' ),
								'values' => array (
										'' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_SIZE_DEFAULT' ),
										'lg' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_SIZE_LARGE' ),
										'xlg' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_SIZE_XLARGE' ),
										'sm' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_SIZE_SMALL' ),
										'xs' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_SIZE_EXTRA_SAMLL' )
								),
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'all_articles_btn_icon' => array (
								'type' => 'icon',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_ICON' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_ICON_DESC' ),
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'all_articles_btn_icon_position' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_ICON_POSITION' ),
								'values' => array (
										'left' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_LEFT' ),
										'right' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_RIGHT' )
								),
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'all_articles_btn_block' => array (
								'type' => 'select',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_BLOCK' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_BUTTON_BLOCK_DESC' ),
								'values' => array (
										'' => Text::_ ( 'JNO' ),
										'sppb-btn-block' => Text::_ ( 'JYES' )
								),
								'depends' => array (
										'link_articles' => '1'
								)
						),

						'class' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_CLASS' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_CLASS_DESC' ),
								'std' => ''
						)
				),
				'custom settings' => array (
						'article_style' => array (
								'type' => 'select',
								'title' => 'Choose Article Style',
								'desc' => 'Select a article style from the list.',
								'values' => array (
										'article-style-01' => 'Article Style 01',
										'article-style-02' => 'Article Style 02',
										'article-style-03' => 'Article Style 03',
										'article-style-04' => 'Article Style 04',
										'article-style-05' => 'Article Style 05',
										'article-style-06' => 'Article Style 06',
										'article-style-07' => 'Article Style 07',
										'article-style-08' => 'Article Style 08'
								),
								'std' => 'article-style-01'
						),

						'image_link' => array (
								'type' => 'checkbox',
								'title' => 'Image Link',
								'desc' => 'Whether to show image link.',
								'std' => 1
						),

						'separator_limit_columns' => array (
								'type' => 'separator',
								'title' => 'Limit & Columns Options'
						),

						'limit' => array (
								'type' => 'number',
								'title' => 'Article Limit',
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_LIMIT_DESC' ),
								'std' => '3'
						),

						'columns_md' => array (
								'type' => 'number',
								'title' => 'Columns - Desktop',
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_COLUMNS_DESC' ),
								'std' => '3'
						),

						'columns_sm' => array (
								'type' => 'number',
								'title' => 'Columns - Tablet',
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_COLUMNS_DESC' ),
								'std' => '2'
						),

						'columns_xs' => array (
								'type' => 'number',
								'title' => 'Columns - Mobile',
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_COLUMNS_DESC' ),
								'std' => '1'
						)
				),
				'options' => array (

						'hide_thumbnail' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_HIDE_THUMBNAIL' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_HIDE_THUMBNAIL_DESC' ),
								'values' => array (
										1 => Text::_ ( 'COM_JPAGEBUILDER_YES' ),
										0 => Text::_ ( 'COM_JPAGEBUILDER_NO' )
								),
								'std' => 0
						),

						'show_author' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_AUTHOR' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_AUTHOR_DESC' ),
								'values' => array (
										1 => Text::_ ( 'COM_JPAGEBUILDER_YES' ),
										0 => Text::_ ( 'COM_JPAGEBUILDER_NO' )
								),
								'std' => 1
						),

						'show_category' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_CATEGORY' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_CATEGORY_DESC' ),
								'values' => array (
										1 => Text::_ ( 'COM_JPAGEBUILDER_YES' ),
										0 => Text::_ ( 'COM_JPAGEBUILDER_NO' )
								),
								'std' => 1
						),

						'show_date' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_DATE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_DATE_DESC' ),
								'values' => array (
										1 => Text::_ ( 'COM_JPAGEBUILDER_YES' ),
										0 => Text::_ ( 'COM_JPAGEBUILDER_NO' )
								),
								'std' => 1
						),

						'show_readmore' => array (
								'type' => 'checkbox',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_READMORE' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_READMORE_DESC' ),
								'values' => array (
										1 => Text::_ ( 'COM_JPAGEBUILDER_YES' ),
										0 => Text::_ ( 'COM_JPAGEBUILDER_NO' )
								),
								'std' => 1
						),

						'readmore_text' => array (
								'type' => 'text',
								'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_READMORE_TEXT' ),
								'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_READMORE_TEXT_DESC' ),
								'std' => 'Read More',
								'depends' => array (
										'show_readmore' => '1'
								)
						)
				)
		)
) );
