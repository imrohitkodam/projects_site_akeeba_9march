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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
class JpagebuilderViewEditor extends HtmlView {
	/**
	 * The Form object
	 *
	 * @var Joomla\CMS\Form\Form
	 */
	protected $form;
	protected $item;
	public function display($tpl = null) {
		$app = Factory::getApplication ();
		$user = Factory::getApplication()->getIdentity();
		
		$model = $this->getModel();
		$this->form = $model->getForm();
		$this->item = $model->getItem();
		
		$isAuthorised = $user->authorise ( 'core.admin', 'com_jpagebuilder' ) || $user->authorise ( 'core.manage', 'com_jpagebuilder' ) || $user->authorise ( 'core.edit', 'com_jpagebuilder' ) || $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );

		if (! $isAuthorised) {
			$app->enqueueMessage ( Text::_ ( 'COM_JPAGEBUILDER_ERROR_EDIT_PERMISSION' ), 'error' );
			return;
		}

		$this->addToolBar ();

		JpagebuilderLanguageHelper::registerLanguageKeys ();
		JpagebuilderIconHelper::loadAssets ();

		parent::display ( $tpl );
	}
	protected function addToolBar() {
		ToolbarHelper::title ( Text::_ ( 'COM_JPAGEBUILDER' ), 'pagebuilder' );
	}
}
