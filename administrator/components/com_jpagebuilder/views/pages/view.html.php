<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
class JpagebuilderViewPages extends HtmlView {
	public $filterForm;
	public $activeFilters = [ ];
	protected $items = [ ];
	protected $pagination;
	protected $state;
	protected $databaseIssue;
	public function display($tpl = null) {
		$model = $this->getModel();
		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->state = $model->getState();
		$this->filterForm = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->databaseIssue = false;

		// Joomla Component Helper
		$this->params = ComponentHelper::getParams ( 'com_jpagebuilder' );

		$this->addToolbar ();

		parent::display ( $tpl );
	}
	protected function addToolBar() {
		$state = $this->getModel()->getState();
		$canDo = ContentHelper::getActions ( 'com_jpagebuilder' );
		$user = Factory::getApplication()->getIdentity();

		// Set the title
		ToolbarHelper::title ( Text::_ ( 'COM_JPAGEBUILDER' ) . ' - ' . Text::_ ( 'COM_JPAGEBUILDER_PAGES' ), 'none pbfont pbfont-pagebuilder' );

		$version = new Version ();
		$JoomlaVersion = ( float ) $version->getShortVersion ();

		$document = Factory::getApplication()->getDocument();
		if(method_exists($document, 'getToolbar')) {
			$toolbar = $document->getToolbar( 'toolbar' );
		} else {
			// Fallback to legacy
			$toolbar = Toolbar::getInstance ( 'toolbar' );
		}

		// new page button
		if ($canDo->get ( 'core.create' ) || count ( $user->getAuthorisedCategories ( 'com_jpagebuilder', 'core.create' ) ) > 0) {
			$toolbar->addNew ( 'page.add' );
		}

		if ($canDo->get ( 'core.edit.state' )) {
			$dropdown = $toolbar->dropdownButton ( 'status-group' )->text ( 'JTOOLBAR_CHANGE_STATUS' )->toggleSplit ( false )->icon ( 'fas fa-ellipsis-h' )->buttonClass ( 'btn btn-action' )->listCheck ( true );

			$childBar = $dropdown->getChildToolbar ();

			$childBar->publish ( 'pages.publish' )->listCheck ( true );
			$childBar->unpublish ( 'pages.unpublish' )->listCheck ( true );
			$childBar->checkin ( 'pages.checkin' )->listCheck ( true );
			$childBar->trash ( 'pages.trash' )->listCheck ( true );
		}

		if ($this->state->get ( 'filter.published' ) == - 2 && $canDo->get ( 'core.delete' )) {
			$toolbar->delete ( 'pages.delete' )->text ( 'JTOOLBAR_EMPTY_TRASH' )->message ( 'JGLOBAL_CONFIRM_DELETE' )->listCheck ( true );
		}

		if ($user->authorise ( 'core.admin', 'com_jpagebuilder' ) || $user->authorise ( 'core.options', 'com_jpagebuilder' )) {
			$toolbar->preferences ( 'com_jpagebuilder' );
		}
	}
}
