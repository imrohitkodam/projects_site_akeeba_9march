<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Uri\Uri;
class JpagebuilderViewForm extends HtmlView {
	protected $form;
	protected $item;
	protected $additionalAttributes = [ ];

	/**
	 * Prepare Page Title and Site Name
	 *
	 * @param string $title
	 * @return void
	 */
	protected function _prepareDocument($title = '') {
		/** @var CMSApplication */
		$app = Factory::getApplication ();
		$config = Factory::getApplication()->getConfig();
		$doc = Factory::getApplication ()->getDocument ();
		$menus = $app->getMenu ();
		$menu = $menus->getActive ();

		if (isset ( $menu )) {
			if ($menu->getParams ()->get ( 'page_title', '' )) {
				$title = $menu->getParams ()->get ( 'page_title' );
			} else {
				$title = $menu->title;
			}
		}

		// Include Site title
		$sitetitle = $title;
		if ($config->get ( 'sitename_pagetitles' ) == 2) {
			$sitetitle = Text::sprintf ( 'JPAGETITLE', $sitetitle, $app->get ( 'sitename' ) );
		} elseif ($config->get ( 'sitename_pagetitles' ) == 1) {
			$sitetitle = Text::sprintf ( 'JPAGETITLE', $app->get ( 'sitename' ), $sitetitle );
		}

		$doc->setTitle ( $sitetitle );
	}
	function display($tpl = null) {
		/** @var CMSApplication */
		$app = Factory::getApplication ();
		$user = Factory::getApplication()->getIdentity();

		$model = $this->getModel();
		$this->item = $model->getItem();
		$this->form = $model->getForm();

		$this->item = JpagebuilderApplicationHelper::preparePageData ( $this->item );

		if (! $user->id) {
			$uri = Uri::getInstance ();
			$pageURL = $uri->toString ();
			$return_url = base64_encode ( $pageURL );
			$joomlaLoginUrl = 'index.php?option=com_users&view=login&return=' . $return_url;
			$app->enqueueMessage ( Text::_ ( 'JERROR_ALERTNOAUTHOR' ), 'notice' );
			$app->redirect ( Uri::base () . $joomlaLoginUrl, 403 );
		}

		$input = $app->getInput();
		$pageid = $input->get ( 'id', '', 'int' );
		$item_info = JpagebuilderModelPage::getPageInfoById ( $pageid );
		$authorised = $user->authorise ( 'core.edit', 'com_jpagebuilder.page.' . $pageid ) || ($user->authorise ( 'core.edit.own', 'com_jpagebuilder.page.' . $pageid ) && $item_info->created_by == $user->id);

		// checkout
		if (! ($this->item->checked_out == 0 || $this->item->checked_out == $user->id)) {
			$app->enqueueMessage ( Text::_ ( 'COM_JPAGEBUILDER_ERROR_CHECKED_IN' ), 'warning' );
			$app->redirect ( $this->item->link, 403 );
			return false;
		}

		if ($authorised !== true) {
			$app->enqueueMessage ( Text::_ ( 'COM_JPAGEBUILDER_ERROR_EDIT_PERMISSION' ), 'warning' );
			$app->redirect ( $this->item->link, 403 );
			return false;
		}

		$this->_prepareDocument ( $this->item->title );
		JpagebuilderHelperSite::loadLanguage ();
		parent::display ( $tpl );
	}
}
