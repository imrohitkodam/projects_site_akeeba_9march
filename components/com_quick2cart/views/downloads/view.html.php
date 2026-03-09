<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * View class for a list for downloads.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewDownloads extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->params        = ComponentHelper::getParams('com_quick2cart');
		$user                = Factory::getUser();
		$app                 = Factory::getApplication();
		$jinput              = $app->input;
		$option              = $jinput->get('option');
		$view                = $jinput->get('view');
		$orderid             = $jinput->get('orderid', '');
		$emailMd5            = $jinput->get('guest_email', '', 'RAW');
		$model               = $this->getModel('downloads');
		$layout              = $jinput->get('layout', 'default', 'string');
		$comquick2cartHelper = new comquick2cartHelper;

		if ($layout == "default")
		{
			if ($emailMd5)
			{
				$this->guest_email_chk = $guest_email_chk = $comquick2cartHelper->checkmailhash($orderid, $emailMd5);

				// If order email and guest_email is same
				if (!$guest_email_chk )
				{
					$this->showMsg(Text::_('QTC_GUEST_MAIL_UNMATCH'));

					return false;
				}
			}

			if ($emailMd5)
			{
				$this->allDownloads = $model->getAllDownloads($user->id, $orderid);
				$this->pagination = $model->getPagination($user->id, $orderid);
			}
			else
			{
				$this->allDownloads = $model->getAllDownloads($user->id);
				$this->pagination = $model->getPagination($user->id);
			}

			$filter_order_Dir = $app->getUserStateFromRequest($option . "$view.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
			$filter_type      = $app->getUserStateFromRequest($option . "$view.filter_order", 'filter_order', 'oi.order_id', 'string');
			$search           = $app->getUserStateFromRequest($option . 'search_list', 'search_list', '', 'string');

			if ($search == null)
			{
				$search = '';
			}

			// Search filter
			$lists['search_list'] = $search;
			$lists['order_Dir']   = $filter_order_Dir;
			$lists['order']       = $filter_type;

			$this->lists = $lists;
		}

		$this->_setToolBar();
		parent::display($tpl);
	}

	/**
	 * Show error msg
	 *
	 * @param   String  $msg  message to print
	 *
	 * @return  void
	 *
	 * @since   2.2
	 */
	public function showMsg($msg)
	{
		?>
		<div class="well" >
			<div class="alert alert-danger">
				<span ><?php echo $msg; ?> </span>
			</div>
		</div>
		</div>
		<?php
	}

	/**
	 * Set toolbar
	 *
	 * @return  void
	 *
	 * @since   2.2
	 */
	public function _setToolBar()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::_('QTC_DOWNLOAD_PAGE'));
	}
}
