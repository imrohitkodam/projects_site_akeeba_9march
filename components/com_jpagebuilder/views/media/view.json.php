<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (! class_exists ( 'JpagebuilderHelperSite' )) {
	require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/helper.php';
}
class JpagebuilderViewMedia extends HtmlView {
	public function display($tpl = null) {
		$user = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise ( 'core.edit', 'com_jpagebuilder' );
		$canEditOwn = $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );
		$hasAdminAccess = $user->authorise ( 'core.admin', 'com_jpagebuilder' ) || $user->authorise ( 'core.manage', 'com_jpagebuilder' );

		$canEditPage = $hasAdminAccess || $canEdit || $canEditOwn;

		if (! $canEditPage) {
			throw new Exception ( Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_ACCESS' ) );
		}

		$input = Factory::getApplication ()->getInput();
		$layout = $input->get ( 'layout', 'browse', 'string' );
		$this->date = $input->post->get ( 'date', NULL, 'string' );
		$this->start = $input->post->get ( 'start', 0, 'int' );
		$this->search = $input->post->get ( 'search', NULL, 'string' );
		$this->limit = 30;

		$model = $this->getModel ();

		if (($layout == 'browse') || ($layout == 'modal')) {
			$this->items = $model->getItems ();
			$this->filters = $model->getDateFilters ( $this->date, $this->search );
			$this->total = $model->getTotalMedia ( $this->date, $this->search );
			$this->categories = $model->getMediaCategories ();
		} else {
			$this->media = $model->getFolders ();
		}

		JpagebuilderHelperSite::loadLanguage ();

		parent::display ( $tpl );
	}
}
