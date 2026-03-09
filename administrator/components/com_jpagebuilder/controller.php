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
use Joomla\Filesystem\Folder;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class JpagebuilderController extends BaseController {
	protected $default_view = 'editor';
	function display($cachable = false, $urlparams = false) {
		$user = Factory::getApplication()->getIdentity();

		if (! $user->id) {
			$return_url = base64_encode ( Uri::current () );
			$joomlaLoginUrl = 'index.php?option=com_users&view=login&return=' . $return_url;

			$this->setRedirect ( Route::_ ( $joomlaLoginUrl, false ), 'Need to logged in.' );

			return $this;
		}

		return parent::display ( $cachable, $urlparams );
	}
	public function resetcss() {
		$css_folder_path = JPATH_ROOT . '/media/com_jpagebuilder/css';
		if (is_dir ( $css_folder_path )) {
			Folder::delete ( $css_folder_path );
		}
		die ();
	}
	public function export() {
		$input = Factory::getApplication ()->getInput();
		$template = $input->get ( 'template', '[]', 'raw' );
		$filename = 'template' . rand ( 10000, 99999 );

		header ( "Pragma: public" );
		header ( "Expires: 0" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Content-Type: application/force-download" );
		header ( "Content-Type: application/octet-stream" );
		header ( "Content-Type: application/download" );
		header ( "Content-Disposition: attachment;filename=$filename.json" );
		header ( "Content-Type: application/json" );
		header ( "Content-Transfer-Encoding: binary " );

		echo $template;
		die ();
	}
}
