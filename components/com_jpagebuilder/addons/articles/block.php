<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'resticted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
class JpagebuilderAddonArticles extends JpagebuilderAddons {
	public function render() {
		$page_view_name = isset ( $_GET ['view'] );
		$app = Factory::getApplication ();

		$version = new Version ();
		$JoomlaVersion = $version->getShortVersion ();

		if ($app->isClient ( 'administrator' )) {
			return ''; // prevent from loading in the admin view
		}

		$settings = $this->addon->settings;

		$class = (isset ( $settings->class ) && $settings->class) ? $settings->class : '';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';

		// Addon options
		$resource = (isset ( $settings->resource ) && $settings->resource) ? $settings->resource : 'article';
		$catid = (isset ( $settings->catid ) && $settings->catid) ? $settings->catid : [ ];
		$tagids = (isset ( $settings->tagids ) && $settings->tagids) ? $settings->tagids : array ();
		$include_subcat = (isset ( $settings->include_subcat )) ? ( int ) $settings->include_subcat : 1;
		$post_type = (isset ( $settings->post_type ) && $settings->post_type) ? $settings->post_type : '';
		$ordering = (isset ( $settings->ordering ) && $settings->ordering) ? $settings->ordering : 'latest';
		$thumb_size = (isset ( $settings->thumb_size ) && $settings->thumb_size) ? $settings->thumb_size : 'image_thumbnail';
		$limit = (isset ( $settings->limit ) && $settings->limit) ? ( int ) $settings->limit : 3;

		$previous_columns = (isset ( $settings->columns ) && ! is_object ( $settings->columns )) ? ( int ) $settings->columns : 3;
		$columns_lg = (isset ( $settings->columns_original->xl ) && $settings->columns_original->xl) ? ( int ) $settings->columns_original->xl : $previous_columns;
		$columns_md = (isset ( $settings->columns_original->lg ) && $settings->columns_original->lg) ? ( int ) $settings->columns_original->lg : $columns_lg;
		$columns_sm = (isset ( $settings->columns_original->md ) && $settings->columns_original->md) ? ( int ) $settings->columns_original->md : $columns_md;
		$columns_xs = (isset ( $settings->columns_original->sm ) && $settings->columns_original->sm) ? ( int ) $settings->columns_original->sm : 2;
		$columns = (isset ( $settings->columns_original->xs ) && $settings->columns_original->xs) ? ( int ) $settings->columns_original->xs : 1;

		$article_heading_selector = (isset ( $settings->article_heading_selector ) && $settings->article_heading_selector) ? $settings->article_heading_selector : 'h3';

		$show_intro = (isset ( $settings->show_intro )) ? ( int ) $settings->show_intro : 1;
		$intro_limit = (isset ( $settings->intro_limit ) && $settings->intro_limit) ? ( int ) $settings->intro_limit : 200;
		$hide_thumbnail = (isset ( $settings->hide_thumbnail )) ? ( int ) $settings->hide_thumbnail : 0;
		$show_author = (isset ( $settings->show_author )) ? ( int ) $settings->show_author : 1;
		$show_tags = (isset ( $settings->show_tags )) ? ( int ) $settings->show_tags : 1;
		$show_category = (isset ( $settings->show_category )) ? ( int ) $settings->show_category : 1;
		$show_date = (isset ( $settings->show_date )) ? ( int ) $settings->show_date : 1;
		$show_readmore = (isset ( $settings->show_readmore )) ? ( int ) $settings->show_readmore : 1;
		$readmore_text = (isset ( $settings->readmore_text ) && $settings->readmore_text) ? $settings->readmore_text : Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_READ_MORE' );
		$link_articles = (isset ( $settings->link_articles )) ? ( int ) $settings->link_articles : 0;
		$link_catid = (isset ( $settings->link_catid )) ? ( int ) $settings->link_catid : 0;
		$show_custom_field = (isset ( $settings->show_custom_field )) ? $settings->show_custom_field : 0;

		$show_date_text = (isset ( $settings->show_date_text )) ? $settings->show_date_text : '';
		$show_last_modified_date = (isset ( $settings->show_last_modified_date )) ? $settings->show_last_modified_date : 0;
		$show_last_modified_date_text = (isset ( $settings->show_last_modified_date_text )) ? $settings->show_last_modified_date_text : '';
		$article_modified_date = ComponentHelper::getParams ( 'com_content' )->get ( 'show_modify_date' );
		$article_created_date = ComponentHelper::getParams ( 'com_content' )->get ( 'show_publish_date' );

		$all_articles_btn_text = (! empty ( $settings->all_articles_btn_text ) && $settings->all_articles_btn_text) ? $settings->all_articles_btn_text : Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SEE_ALL_POSTS' );
		$all_articles_btn_aria_label_text = $all_articles_btn_text;
		$all_articles_btn_class = (! empty ( $settings->all_articles_btn_size ) && $settings->all_articles_btn_size) ? ' jpb-btn-' . $settings->all_articles_btn_size : '';
		$all_articles_btn_class .= (! empty ( $settings->all_articles_btn_type ) && $settings->all_articles_btn_type) ? ' jpb-btn-' . $settings->all_articles_btn_type : ' jpb-btn-default';
		$all_articles_btn_class .= (! empty ( $settings->all_articles_btn_shape ) && $settings->all_articles_btn_shape) ? ' jpb-btn-' . $settings->all_articles_btn_shape : ' jpb-btn-rounded';
		$all_articles_btn_class .= (! empty ( $settings->all_articles_btn_appearance ) && $settings->all_articles_btn_appearance) ? ' jpb-btn-' . $settings->all_articles_btn_appearance : '';
		$all_articles_btn_class .= (! empty ( $settings->all_articles_btn_block ) && $settings->all_articles_btn_block) ? ' ' . $settings->all_articles_btn_block : '';
		$all_articles_btn_icon = (! empty ( $settings->all_articles_btn_icon ) && $settings->all_articles_btn_icon) ? $settings->all_articles_btn_icon : '';
		$all_articles_btn_icon_position = (! empty ( $settings->all_articles_btn_icon_position ) && $settings->all_articles_btn_icon_position) ? $settings->all_articles_btn_icon_position : 'left';

		$layout = (isset ( $settings->layout ) && $settings->layout) ? $settings->layout : 'default';

		$output = '';
		$article_helper = JPATH_ROOT . '/components/com_jpagebuilder/helpers/articles.php';

		require_once $article_helper;
		$items = JpagebuilderHelperArticles::getArticles ( $limit, $ordering, $catid, $include_subcat, $post_type, $tagids );

		if (! count ( $items )) {
			$output .= '<p class="alert alert-warning">' . Text::_ ( 'COM_JPAGEBUILDER_NO_ITEMS_FOUND' ) . '</p>';
			return $output;
		}

		if (count ( ( array ) $items )) {
			$output .= '<div class="jpb-addon jpb-addon-articles ' . $class . '">';

			if ($title) {
				$output .= '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>';
			}

			$output .= '<div class="jpb-addon-content">';

			$layoutRowCls = 'jpb-row';

			if ($layout === 'masonry') {
				$layoutRowCls .= ' jpb-addon-article-layout-masonry-row ';
				$output .= '<style>
				.jpb-addon-articles .jpb-addon-article-layout-masonry-row {
					display: block;
					column-count: ' . $columns_lg . ';
				}
				@media (max-width: 1200px) {
					.jpb-addon-articles .jpb-addon-article-layout-masonry-row {
						column-count: ' . $columns_md . ';
					}
				}
				@media (max-width: 992px) {
					.jpb-addon-articles .jpb-addon-article-layout-masonry-row {
						column-count: ' . $columns_sm . '; 
					}
				}
				@media (max-width: 768px) {
					.jpb-addon-articles .jpb-addon-article-layout-masonry-row {
						column-count: ' . $columns_xs . '; 
					}
				}
				@media (max-width: 575px) {
					.jpb-addon-articles .jpb-addon-article-layout-masonry-row {
						column-count: ' . $columns . '; 
					}
				}
				</style>';
			} elseif ($layout === 'editorial' || $layout === 'magazine') {
				$layoutRowCls .= ' jpb-addon-article-layout-' . $layout . '-row ';
				$output .= '<style>
				.jpb-addon-articles .jpb-addon-article-layout-' . $layout . '-row {
					display: grid;
					grid-template-columns: repeat(' . $columns_lg . ', 1fr);
				}
				@media (max-width: 1200px) {
					.jpb-addon-articles .jpb-addon-article-layout-' . $layout . '-row {
						grid-template-columns: repeat(' . $columns_md . ', 1fr);
					}
				}
				@media (max-width: 992px) {
					.jpb-addon-articles .jpb-addon-article-layout-' . $layout . '-row {
						grid-template-columns: repeat(' . $columns_sm . ', 1fr);
					}
				}
				@media (max-width: 768px) {
					.jpb-addon-articles .jpb-addon-article-layout-' . $layout . '-row {
						grid-template-columns: repeat(' . $columns_xs . ', 1fr); 
					}
				}
				@media (max-width: 575px) {
					.jpb-addon-articles .jpb-addon-article-layout-' . $layout . '-row {
						grid-template-columns: repeat(' . $columns . ', 1fr);
					}
				}
				</style>';
			}

			$output .= '<div class="' . $layoutRowCls . '">';

			$layoutWrapperCls = 'jpb-addon-article-layout ';
			$layoutContentCls = 'jpb-addon-article-layout-content ';

			if ($layout === 'default' || $layout === '' || $layout === null) {
				$layoutWrapperCls .= 'jpb-col-xs-' . round ( 12 / $columns_xs ) . ' jpb-col-sm-' . round ( 12 / $columns_sm ) . ' jpb-col-md-' . round ( 12 / $columns_md ) . ' jpb-col-lg-' . round ( 12 / $columns_lg ) . ' jpb-col-' . round ( 12 / $columns );
			}

			if ($layout === 'editorial') {
				$layoutWrapperCls .= ' jpb-addon-article-layout-editorial-wrapper';
				$layoutContentCls .= ' jpb-addon-article-layout-editorial-content';
			} elseif ($layout === 'side') {
				$layoutWrapperCls .= ' jpb-col-12 jpb-addon-article-layout-side-wrapper';
				$layoutContentCls .= ' jpb-addon-article-layout-side-content';
			} elseif ($layout === 'magazine') {
				$layoutWrapperCls .= ' jpb-addon-article-layout-magazine-wrapper';
				$layoutContentCls .= ' jpb-addon-article-layout-magazine-content';
			} elseif ($layout === 'masonry') {
				$layoutWrapperCls .= ' jpb-addon-article-layout-masonry-wrapper';
				$layoutContentCls .= ' jpb-addon-article-layout-masonry-content';
			}

			foreach ( $items as $key => $item ) {
				$output .= '<div class="' . $layoutWrapperCls . '">';
				$output .= '<div class="jpb-addon-article ' . $layoutContentCls . '">';

				if (! $hide_thumbnail) {
					$image = $item->{$thumb_size} ?? $item->image_thumbnail;

					if ($item->post_format === 'gallery') {
						if (count ( ( array ) $item->imagegallery->images )) {
							$output .= '<div class="jpb-carousel jpb-slide" data-jpb-ride="jpb-carousel">';
							$output .= '<div class="jpb-carousel-inner">';
							foreach ( $item->imagegallery->images as $key => $gallery_item ) {
								$active_class = '';
								if ($key == 0) {
									$active_class = ' active';
								}
								if (isset ( $gallery_item ['thumbnail'] ) && $gallery_item ['thumbnail']) {
									$output .= '<div class="jpb-item' . $active_class . '">';
									$output .= '<img src="' . $gallery_item ['thumbnail'] . '" alt="">';
									$output .= '</div>';
								} elseif (isset ( $gallery_item ['full'] ) && $gallery_item ['full']) {
									$output .= '<div class="jpb-item' . $active_class . '">';
									$output .= '<img src="' . $gallery_item ['full'] . '" alt="">';
									$output .= '</div>';
								}
							}
							$output .= '</div>';

							if ($layout !== 'magazine') {
								$output .= '<a class="left jpb-carousel-control" role="button" data-slide="prev" aria-label="' . Text::_ ( 'COM_JPAGEBUILDER_ARIA_PREVIOUS' ) . '"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';
								$output .= '<a class="right jpb-carousel-control" role="button" data-slide="next" aria-label="' . Text::_ ( 'COM_JPAGEBUILDER_ARIA_NEXT' ) . '"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
							}

							$output .= '</div>';
						} elseif (isset ( $item->image_thumbnail ) && $item->image_thumbnail) {
							// Lazyload image
							$placeholder = $item->image_thumbnail == '' ? false : $this->get_image_placeholder ( $item->image_thumbnail );

							// Get image ALT text
							$img_obj = json_decode ( $item->images );
							$img_obj_helix = json_decode ( $item->attribs );

							$img_blog_op_alt_text = (isset ( $img_obj->image_intro_alt ) && $img_obj->image_intro_alt) ? $img_obj->image_intro_alt : "";
							$img_helix_alt_text = (isset ( $img_obj_helix->helix_ultimate_image_alt_txt ) && $img_obj_helix->helix_ultimate_image_alt_txt) ? $img_obj_helix->helix_ultimate_image_alt_txt : "";
							$img_alt_text = "";

							if ($img_helix_alt_text) {
								$img_alt_text = $img_helix_alt_text;
							} else if ($img_blog_op_alt_text) {
								$img_alt_text = $img_blog_op_alt_text;
							} else {
								$img_alt_text = $item->title;
							}

							$output .= '<a href="' . $item->link . '" itemprop="url"><img class="jpb-img-responsive' . ($placeholder && $page_view_name != 'form' ? ' jpb-element-lazy' : '') . '" src="' . ($placeholder && $page_view_name != 'form' ? $placeholder : $item->image_thumbnail) . '" alt="' . $img_alt_text . '" itemprop="thumbnailUrl" ' . ($placeholder && $page_view_name != 'form' ? 'data-large="' . $image . '"' : '') . '  loading="lazy"></a>';
						}
					} elseif ($item->post_format == 'video' && isset ( $item->video_src ) && $item->video_src) {
						$output .= '<div class="entry-video embed-responsive embed-responsive-16by9">';
						$output .= '<object class="embed-responsive-item" style="width:100%;height:100%;" data="' . $item->video_src . '">';
						$output .= '<param name="movie" value="' . $item->video_src . '">';
						$output .= '<param name="wmode" value="transparent" />';
						$output .= '<param name="allowFullScreen" value="true">';
						$output .= '<param name="allowScriptAccess" value="always"></param>';
						$output .= '<embed src="' . $item->video_src . '" type="application/x-shockwave-flash" allowscriptaccess="always"></embed>';
						$output .= '</object>';
						$output .= '</div>';
					} elseif ($item->post_format == 'audio' && isset ( $item->audio_embed ) && $item->audio_embed) {
						$output .= '<div class="entry-audio embed-responsive embed-responsive-16by9">';
						$output .= $item->audio_embed;
						$output .= '</div>';
					} elseif ($item->post_format == 'link' && isset ( $item->link_url ) && $item->link_url) {
						$output .= '<div class="entry-link">';
						$output .= '<a target="_blank" rel="noopener noreferrer" href="' . $item->link_url . '"><h4>' . $item->link_title . '</h4></a>';
						$output .= '</div>';
					} else {
						if (isset ( $image ) && $image) {
							// Lazyload image
							$default_placeholder = $image == '' ? false : $this->get_image_placeholder ( $image );

							// Get image ALT text
							$img_obj = json_decode ( $item->images );
							$img_obj_helix = json_decode ( $item->attribs );

							$img_blog_op_alt_text = (isset ( $img_obj->image_intro_alt ) && $img_obj->image_intro_alt) ? $img_obj->image_intro_alt : "";
							$img_helix_alt_text = (isset ( $img_obj_helix->helix_ultimate_image_alt_txt ) && $img_obj_helix->helix_ultimate_image_alt_txt) ? $img_obj_helix->helix_ultimate_image_alt_txt : "";
							$img_alt_text = "";

							if ($img_helix_alt_text) {
								$img_alt_text = $img_helix_alt_text;
							} else if ($img_blog_op_alt_text) {
								$img_alt_text = $img_blog_op_alt_text;
							} else {
								$img_alt_text = $item->title;
							}

							$output .= '<a class="jpb-article-img-wrap" href="' . $item->link . '" itemprop="url"><img class="jpb-img-responsive' . ($default_placeholder && $page_view_name != 'form' ? ' jpb-element-lazy' : '') . '" src="' . ($default_placeholder && $page_view_name != 'form' ? $default_placeholder : $image) . '" alt="' . $img_alt_text . '" itemprop="thumbnailUrl" ' . ($default_placeholder && $page_view_name != 'form' ? 'data-large="' . $image . '"' : '') . ' loading="lazy"></a>';
						}
					}
				}

				$output .= '<div class="jpb-article-info-wrap" role="article">';
				$output .= '<' . $article_heading_selector . '><a href="' . $item->link . '" itemprop="url">' . $item->title . '</a></' . $article_heading_selector . '>';

				if ($show_author || $show_category || $show_date || $show_tags) {
					$output .= '<div class="jpb-article-meta">';

					if ($show_date) {
						$date = ($article_created_date) ? HTMLHelper::_ ( 'date', $item->publish_up, 'DATE_FORMAT_LC3' ) : '<p class="alert alert-warning">' . Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_LAST_CREATED_DATE_WARNING_MESSAGE' ) . '</p>';

						$date_text = ($show_date_text) ? '<b>' . Text::_ ( $show_date_text ) . ': </b>' : '';
						$date_format = ($article_created_date) ? HTMLHelper::_ ( 'date', $item->publish_up, 'DATE_FORMAT_FILTER_DATE' ) : '';
						$output .= '<time datetime="' . $date_format . '" class="jpb-meta-date jpb-meta-date-unmodified">' . $date_text . $date . '</time>';
					}

					if ($show_last_modified_date) {
						$modify_date = ($article_modified_date) ? HTMLHelper::_ ( 'date', $item->modified, 'DATE_FORMAT_LC3' ) : '<p class="alert alert-warning">' . Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_SHOW_LAST_MODIFIED_DATE_WARNING_MESSAGE' ) . '</p>';

						$modify_text = ($show_last_modified_date_text) ? '<b>' . Text::_ ( $show_last_modified_date_text ) . ': </b>' : '';
						$modify_date_format = ($article_modified_date) ? HTMLHelper::_ ( 'date', $item->modified, 'DATE_FORMAT_FILTER_DATE' ) : '';
						$output .= '<time datetime="' . $modify_date_format . '" class="jpb-meta-date jpb-meta-date-modified">' . $modify_text . $modify_date . '</time>';
					}

					if ($show_category) {
						$item->catUrl = Route::_ ( Joomla\Component\Content\Site\Helper\RouteHelper::getCategoryRoute ( $item->catslug ) );
						$output .= '<span class="jpb-meta-category"><a href="' . $item->catUrl . '" itemprop="genre">' . $item->category . '</a></span>';
					}

					if ($show_author) {
						$author = ($item->created_by_alias ? $item->created_by_alias : $item->username);
						$output .= '<span class="jpb-meta-author" itemprop="name">' . $author . '</span>';
					}

					if ($show_tags) {
						$item->tagLayout = new FileLayout ( 'joomla.content.tags' );
						$output .= $item->tagLayout->render ( $item->tags->itemTags );
					}

					$output .= '</div>';
				}

				if ($show_custom_field) {
					if (( float ) $JoomlaVersion >= 4) {
						JLoader::registerAlias ( 'FieldsHelper', 'Joomla\Component\Fields\Administrator\Helper\FieldsHelper' );
					} else {
						require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/helpers/loader.php');
						JpagebuilderLoader::setup();
						JpagebuilderLoader::register ( 'FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php' );
					}

					// 🚨 Alert: Do not add “FieldsHelper” as a namespace as Joomla 3 doesn't support it.
					$custom_fields = FieldsHelper::getFields ( 'com_content.article', $item );

					$output .= FieldsHelper::render ( 'com_content.article', 'fields.render', array (
							'context' => 'com_content.article',
							'item' => $item,
							'fields' => $custom_fields
					) );
				}

				if ($show_intro) {
					$output .= '<div class="jpb-article-introtext">' . mb_substr ( strip_tags ( $item->introtext ), 0, $intro_limit, 'UTF-8' ) . '...</div>';
				}

				if ($show_readmore) {
					$max_title_characters = 25;
					$aria_label = strlen ( $item->title ) > $max_title_characters ? mb_substr ( strip_tags ( $item->title ), 0, $max_title_characters, 'UTF-8' ) . '...' : strip_tags ( $item->title );
					$full_aria_label = Text::_ ( 'COM_JPAGEBUILDER_ADDON_ARTICLES_READ_MORE_ABOUT' ) . $aria_label;

					$output .= '<a class="jpb-readmore" href="' . $item->link . '" aria-label="' . $full_aria_label . '" itemprop="url">' . $readmore_text . '</a>';
				}

				$output .= '</div>'; // .jpb-article-info-wrap

				$output .= '</div>';
				$output .= '</div>';
			}

			$output .= '</div>';

			// See all link
			if ($link_articles) {

				$icon_arr = array_filter ( explode ( ' ', $all_articles_btn_icon ) );
				if (count ( $icon_arr ) === 1) {
					$all_articles_btn_icon = 'fa ' . $all_articles_btn_icon;
				}

				if ($all_articles_btn_icon_position == 'left') {
					$all_articles_btn_text = ($all_articles_btn_icon) ? '<i class="' . $all_articles_btn_icon . '" aria-hidden="true"></i> ' . $all_articles_btn_text : $all_articles_btn_text;
				} else {
					$all_articles_btn_text = ($all_articles_btn_icon) ? $all_articles_btn_text . ' <i class="' . $all_articles_btn_icon . '" aria-hidden="true"></i>' : $all_articles_btn_text;
				}

				list ( $link, $new_tab ) = JpagebuilderAddonHelper::parseLink ( $settings, 'all_articles_btn_url', [ 
						'url' => 'link',
						'new_tab' => 'target'
				] );

				$hrefValue = ! empty ( $link ) ? $link : (! empty ( $link_catid ) ? Route::_ ( Joomla\Component\Content\Site\Helper\RouteHelper::getCategoryRoute ( $link_catid ) ) : '');

				$output .= '<a href="' . $hrefValue . '" ' . $new_tab . ' id="btn-' . $this->addon->id . '" class="jpb-btn' . $all_articles_btn_class . '"' . ' aria-label="' . $all_articles_btn_aria_label_text . '">' . $all_articles_btn_text . '</a>';
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}
	public function css() {
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$layout_path = JPATH_ROOT . '/components/com_jpagebuilder/layouts';
		$css_path = new FileLayout ( 'addon.css.button', $layout_path );

		$settings = $this->addon->settings;

		$settings->border_width = (isset ( $settings->border_width )) ? $settings->border_width : 1;
		$settings->border_color = (isset ( $settings->border_color ) && $settings->border_color) ? $settings->border_color : '#0000001a';
		$settings->gap = (isset ( $settings->gap ) && ! empty ( $settings->gap )) ? $settings->gap : 15;

		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );

		$options = new stdClass ();
		$options->button_type = (isset ( $this->addon->settings->all_articles_btn_type ) && $this->addon->settings->all_articles_btn_type) ? $this->addon->settings->all_articles_btn_type : '';
		$options->button_appearance = (isset ( $this->addon->settings->all_articles_btn_appearance ) && $this->addon->settings->all_articles_btn_appearance) ? $this->addon->settings->all_articles_btn_appearance : '';
		$options->button_color = (isset ( $this->addon->settings->all_articles_btn_color ) && $this->addon->settings->all_articles_btn_color) ? $this->addon->settings->all_articles_btn_color : '';
		$options->button_color_hover = (isset ( $this->addon->settings->all_articles_btn_color_hover ) && $this->addon->settings->all_articles_btn_color_hover) ? $this->addon->settings->all_articles_btn_color_hover : '';
		$options->button_background_color = (isset ( $this->addon->settings->all_articles_btn_background_color ) && $this->addon->settings->all_articles_btn_background_color) ? $this->addon->settings->all_articles_btn_background_color : '';
		$options->button_background_color_hover = (isset ( $this->addon->settings->all_articles_btn_background_color_hover ) && $this->addon->settings->all_articles_btn_background_color_hover) ? $this->addon->settings->all_articles_btn_background_color_hover : '';
		$options->button_fontstyle = (isset ( $this->addon->settings->all_articles_btn_font_style ) && $this->addon->settings->all_articles_btn_font_style) ? $this->addon->settings->all_articles_btn_font_style : '';
		$options->button_font_style = (isset ( $this->addon->settings->all_articles_btn_font_style ) && $this->addon->settings->all_articles_btn_font_style) ? $this->addon->settings->all_articles_btn_font_style : '';
		$options->button_letterspace = (isset ( $this->addon->settings->all_articles_btn_letterspace ) && $this->addon->settings->all_articles_btn_letterspace) ? $this->addon->settings->all_articles_btn_letterspace : '';

		$layout = (isset ( $settings->layout ) && $settings->layout) ? $settings->layout : 'default';

		$css = $css_path->render ( array (
				'addon_id' => $addon_id,
				'options' => $options,
				'id' => 'btn-' . $this->addon->id
		) );

		$show_boxshadow = (isset ( $settings->show_boxshadow ) && $settings->show_boxshadow) ? $settings->show_boxshadow : '';
		$show_radius = (isset ( $settings->radius ) && $settings->radius) ? $settings->radius : '';
		$show_border = (isset ( $settings->border ) && $settings->border) ? $settings->border : '';

		$article_heading_selector = (isset ( $settings->article_heading_selector ) && $settings->article_heading_selector) ? $settings->article_heading_selector : 'h3';
		$show_intro = (isset ( $settings->show_intro ) && $settings->show_intro) ? $settings->show_intro : '';
		$show_author = (isset ( $settings->show_author ) && $settings->show_author) ? $settings->show_author : '';
		$show_tags = (isset ( $settings->show_tags ) && $settings->show_tags) ? $settings->show_tags : '';
		$show_category = (isset ( $settings->show_category ) && $settings->show_category) ? $settings->show_category : '';
		$show_date = (isset ( $settings->show_date ) && $settings->show_date) ? $settings->show_date : '';
		$show_last_modified_date = (isset ( $settings->show_last_modified_date ) && $settings->show_last_modified_date) ? $settings->show_last_modified_date : '';
		$show_readmore = (isset ( $settings->show_readmore ) && $settings->show_readmore) ? $settings->show_readmore : '';

		$css .= $cssHelper->typography ( '.jpb-addon-articles .jpb-article-info-wrap ' . $article_heading_selector . ' a', $settings, 'article_title_typography' );
		$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap ' . $article_heading_selector . '', $settings, [ 
				'article_title_margin_top' => 'margin-top',
				'article_title_margin_bottom' => 'margin-bottom'
		] );
		$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap ' . $article_heading_selector . ' a', $settings, [ 
				'article_title_text_color' => 'color'
		], false );
		$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap ' . $article_heading_selector . ' a:hover', $settings, [ 
				'article_title_text_color_hover' => 'color'
		], false );

		if ($show_intro) {
			$css .= $cssHelper->typography ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-introtext', $settings, 'intro_typography' );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-introtext', $settings, [ 
					'intro_margin' => 'margin',
					'intro_padding' => 'padding',
					'intro_color' => 'color'
			], false );
		}
		if ($show_author) {
			$css .= $cssHelper->typography ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-author', $settings, 'author_typography' );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-author', $settings, [ 
					'author_margin_left' => 'margin-left',
					'author_margin_right' => 'margin-right'
			] );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-author', $settings, [ 
					'author_color' => 'color'
			], false );
		}
		if ($show_category) {
			$css .= $cssHelper->typography ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-category a', $settings, 'category_typography' );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-category', $settings, [ 
					'category_margin_left' => 'margin-left',
					'category_margin_right' => 'margin-right'
			] );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-category a', $settings, [ 
					'category_color' => 'color'
			], false );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-category a:hover', $settings, [ 
					'category_color_hover' => 'color'
			], false );
		}

		if ($show_date) {
			$css .= $cssHelper->typography ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-date-unmodified', $settings, 'date_typography' );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-date-unmodified', $settings, [ 
					'date_margin_left' => 'margin-left',
					'date_margin_right' => 'margin-right'
			] );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-date-unmodified', $settings, [ 
					'date_color' => 'color'
			], false );
		}

		if ($show_last_modified_date) {
			$css .= $cssHelper->typography ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-date-modified', $settings, 'last_modified_date_typography' );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-date-modified', $settings, [ 
					'last_modified_date_margin_left' => 'margin-left',
					'last_modified_date_margin_right' => 'margin-right'
			] );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .jpb-meta-date-modified', $settings, [ 
					'last_modified_date_color' => 'color'
			], false );
		}
		if ($show_readmore) {
			$css .= $cssHelper->typography ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-readmore', $settings, 'readmore_typography' );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-readmore', $settings, [ 
					'readmore_margin' => 'display: inline-block; margin',
					'readmore_padding' => 'display: inline-block; padding',
					'readmore_color' => 'color'
			], false );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-readmore:hover', $settings, [ 
					'readmore_color_hover' => 'color'
			], false );
		}

		if ($show_tags) {
			$css .= $cssHelper->typography ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .tags li a', $settings, 'tags_typography' );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .tags li a', $settings, [ 
					'tags_border_radius' => 'border-radius'
			] );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .tags li', $settings, [ 
					'tags_margin' => 'margin'
			], false );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .tags li a', $settings, [ 
					'tags_color' => 'color',
					'tags_background_color' => 'background-color',
					'tags_padding' => 'padding'
			], false );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-article-info-wrap .jpb-article-meta .tags li a:hover', $settings, [ 
					'tags_color_hover' => 'color',
					'tags_background_color_hover' => 'background-color'
			], false );
		}

		$box_shadow = '';
		$border_radius = '';
		$content_radius = '';
		$image_radius = '';
		$border_color = '';
		$border_width = '';

		if ($show_boxshadow) {
			$box_shadow .= (isset ( $settings->shadow_horizontal ) && $settings->shadow_horizontal) ? $settings->shadow_horizontal . 'px ' : '0 ';
			$box_shadow .= (isset ( $settings->shadow_vertical ) && $settings->shadow_vertical) ? $settings->shadow_vertical . 'px ' : '0 ';
			$box_shadow .= (isset ( $settings->shadow_blur ) && $settings->shadow_blur) ? $settings->shadow_blur . 'px ' : '0 ';
			$box_shadow .= (isset ( $settings->shadow_spread ) && $settings->shadow_spread) ? $settings->shadow_spread . 'px ' : '0 ';
			$box_shadow .= (isset ( $settings->shadow_color ) && $settings->shadow_color) ? $settings->shadow_color : 'rgba(0, 0, 0, .5)';
		}

		if ($show_radius) {
			$border_radius = (isset ( $settings->border_radius ) && $settings->border_radius) ? $settings->border_radius : 0;
			$content_radius = (isset ( $settings->content_radius ) && $settings->content_radius) ? $settings->content_radius : 0;
			$image_radius = (isset ( $settings->image_radius ) && $settings->image_radius) ? $settings->image_radius : 0;
		}

		if ($show_border) {
			$border_color = (isset ( $settings->border_color ) && $settings->border_color) ? $settings->border_color : '#0000001a';
			$border_width = (isset ( $settings->border_width ) && $settings->border_width) ? $settings->border_width : 1;
		}

		$settings->dummy_box_shadow = $box_shadow;
		$settings->dummy_border_radius = $border_radius;
		$settings->dummy_content_radius = $content_radius;
		$settings->dummy_image_radius = $image_radius;
		$settings->dummy_border_color = $border_color;
		$settings->dummy_border_width = $border_width;

		if ($layout !== 'default') {
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content .jpb-article-info-wrap', $settings, [ 
					'content_padding' => 'padding'
			], false );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content img, .jpb-addon-articles .jpb-addon-article-layout-content .embed-responsive', $settings, [ 
					'image_padding' => 'padding'
			], false );

			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content', $settings, [ 
					'background_color' => 'background-color'
			], false );

			if ($layout === 'side') {
				$image_at = (isset ( $settings->image_at ) && $settings->image_at) ? $settings->image_at : 'left';

				if ($image_at === 'left') {
					$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-side-content', $settings, [ 
							'image_width' => 'grid-template-columns'
					], 'px 1fr' );
				} elseif ($image_at === 'right' && isset ( $settings->image_width ) && $settings->image_width) {
					$css .= ' .jpb-addon-articles .jpb-addon-article-layout-side-content {
						grid-template-columns: 1fr ' . $settings->image_width . 'px;
					} ';
				}
			}

			if ($layout === 'editorial' || $layout === 'side' || $layout === 'magazine') {
				$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-' . $layout . '-content img', $settings, [ 
						'image_height' => 'height'
				] );
				$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-' . $layout . '-content .embed-responsive', $settings, [ 
						'image_height' => 'height'
				], 'px !important' );

				$img_height = (isset ( $settings->image_height ) && $settings->image_height) ? $settings->image_height : 250;

				$css .= ' .jpb-addon-articles .jpb-addon-article-layout-content .jpb-carousel img {
					height: ' . $img_height . 'px;
				} ';
			}

			if ($border_radius) {
				$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content', $settings, [ 
						'dummy_border_radius' => 'overflow: hidden; border-radius'
				] );
				$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content img', $settings, [ 
						'dummy_border_radius' => '    border-top-left-radius'
				] );
				$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content img', $settings, [ 
						'dummy_border_radius' => '    border-top-right-radius'
				] );
			} else {
				$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content', $settings, [ 
						'dummy_content_radius' => 'border-radius'
				] );
				$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content img', $settings, [ 
						'dummy_image_radius' => 'border-radius'
				] );
			}

			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content', $settings, [ 
					'dummy_box_shadow' => 'box-shadow'
			], false );

			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content', $settings, [ 
					'dummy_border_width' => 'border-style: solid; border-width'
			] );
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-content', $settings, [ 
					'dummy_border_color' => 'border-color'
			], false );
		}

		$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-side-content, .jpb-addon-articles .jpb-addon-article-layout-masonry-row', $settings, [ 
				'gap' => 'column-gap'
		] );
		$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-side-wrapper', $settings, [ 
				'gap' => 'margin-bottom'
		] );
		$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-masonry-content', $settings, [ 
				'gap' => 'margin-bottom'
		] );

		$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-editorial-row, .jpb-addon-articles .jpb-addon-article-layout-magazine-row', $settings, [ 
				'gap' => 'gap'
		] );

		if ($layout === 'magazine') {
			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-magazine-content:hover .jpb-article-info-wrap', $settings, [ 
					'overlay_color' => 'background'
			], false );
		}

		if ($layout === 'side') {
			$image_at = (isset ( $settings->image_at ) && $settings->image_at) ? $settings->image_at : 'left';
			$order = 0;

			if ($image_at === 'right') {
				$order = - 1;
			}

			$settings->image_order = $order;

			$css .= $cssHelper->generateStyle ( '.jpb-addon-articles .jpb-addon-article-layout-side-content .jpb-article-info-wrap', $settings, [ 
					'image_order' => 'order'
			], false );
		}

		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-content', $settings, 'transform' );

		$css .= $transformCss;

		return $css;
	}
	static function isComponentInstalled($component_name) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'a.enabled' );
		$query->from ( $db->quoteName ( '#__extensions', 'a' ) );
		$query->where ( $db->quoteName ( 'a.name' ) . " = " . $db->quote ( $component_name ) );
		$db->setQuery ( $query );
		$is_enabled = $db->loadResult ();
		return $is_enabled;
	}
}
