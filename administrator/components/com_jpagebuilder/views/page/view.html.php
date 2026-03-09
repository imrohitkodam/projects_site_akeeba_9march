<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct accees
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
class JpagebuilderViewPage extends HtmlView {

	/**
	 * The \JForm object
	 *
	 * @var \Joomla\CMS\Form\Form
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var object
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var object
	 */
	protected $state;

	/**
	 * The actions the user is authorised to perform
	 *
	 * @var \JObject
	 */
	protected $canDo;

	/**
	 * Pagebreak TOC alias
	 *
	 * @var string
	 */
	protected $eName;

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl
	 *        	The name of the template file to parse; automatically searches through the template paths.
	 *        	
	 * @return mixed A string if successful, otherwise an Error object.
	 *        
	 * @throws \Exception
	 */
	public function display($tpl = null) {
		$model = $this->getModel();
		$this->form = $model->getForm();
		$this->item = $model->getItem();
		$this->state = $model->getState();
		
		$this->canDo = ContentHelper::getActions ( 'com_jpagebuilder', 'page', $this->item->id );

		parent::display ( $tpl );
	}
}
