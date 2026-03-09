<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait for managing export layout API endpoint.
 */
trait JPageBuilderFrameworkExport {
	public function export() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'GET',
				'DELETE',
				'PUT',
				'PATCH'
		], $method );

		if ($method === 'POST') {
			$this->exportLayout ();
		}
	}

	/**
	 * Export template layout.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function exportLayout() {
		$user = Factory::getApplication()->getIdentity();
		$authorised = $user->authorise ( 'core.edit', 'com_jpagebuilder' );
		$canEditOwn = $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );

		$model = $this->getModel ( 'Editor' );

		$pageId = $this->getInput ( 'pageId', '', 'string' );
		$isSeoChecked = $this->getInput ( 'isSeoChecked', '', 'string' );

		if (empty ( $pageId )) {
			$this->sendResponse ( [ 
					'message' => 'Page Id missing.'
			], 400 );
		}

		if ($canEditOwn && ! empty ( $pageId )) {
			require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/models/page.php' );

			$item_info = JpagebuilderModelPage::getPageInfoById ( $pageId );

			$canEditOwn = $item_info->created_by == $user->id;
		}

		if (! $authorised && ! $canEditOwn) {
			die ( 'Restricted Access' );
		}

		$content = $model->getPageContent ( $pageId );

		if (empty ( $content )) {
			$this->sendResponse ( [ 
					'message' => 'Requesting page not found!'
			], 404 );
		}

		$content = JpagebuilderApplicationHelper::preparePageData ( $content );

		$seoSettings = [ ];

		if ($isSeoChecked) {
			$seoSettings = [ 
					'og_description' => isset ( $content->og_description ) ? $content->og_description : '',
					'og_image' => '',
					'og_title' => isset ( $content->og_title ) ? $content->og_title : '',
					'meta_title' => isset ( $content->attribs ) && isset ( $content->attribs->meta_title ) ? $content->attribs->meta_title : '',
					'meta_description' => isset ( $content->attribs ) && isset ( $content->attribs->meta_description ) ? $content->attribs->meta_description : '',
					'meta_keywords' => isset ( $content->attribs ) && isset ( $content->attribs->meta_keywords ) ? $content->attribs->meta_keywords : '',
					'og_type' => isset ( $content->attribs ) && isset ( $content->attribs->og_type ) ? $content->attribs->og_type : '',
					'robots' => isset ( $content->attribs ) && isset ( $content->attribs->robots ) ? $content->attribs->robots : '',
					'seo_spacer' => isset ( $content->attribs ) && isset ( $content->attribs->seo_spacer ) ? $content->attribs->seo_spacer : ''
			];
		}

		$pageContent = ( object ) [ 
				'template' => isset ( $content->content ) ? $content->content : $content->text,
				'css' => isset ( $content->css ) ? $content->css : '',
				'seo' => json_encode ( $seoSettings ),
				'title' => $content->title,
				'language' => isset ( $content->language ) ? $content->language : '*'
		];

		$this->sendResponse ( $pageContent );
	}
}
