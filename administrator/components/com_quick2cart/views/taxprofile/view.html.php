<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit
 *
 * @since  3.0.1
 */
class Quick2cartViewTaxprofile extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse;
	 *                        automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$layout = $jinput->get('layout', 'edit');
		$model  = BaseDatabaseModel::getInstance('Taxprofile', 'Quick2cartModel', array('ignore_request' => true));

		if ($layout == 'edit')
		{
			$this->state = $this->get('State');
			$this->item	 = $this->get('Item');
			$this->form	 = $this->get('Form');

			// Get taxprofile_id
			$taxprofile_id = $app->input->get('id', 0, 'INT');

			// Getting saved tax rules.
			if (!empty($taxprofile_id))
			{
				$this->taxrules = $model->getTaxRules($taxprofile_id);
			}

			// Get store name while edit view
			if (!empty($this->item->id) && !empty($this->item->store_id))
			{
				$comquick2cartHelper = new comquick2cartHelper;
				$this->storeDetails  = $comquick2cartHelper->getSoreInfo($this->item->store_id);

				// Getting tax rates and Adress types
				$this->taxrate = $model->getTaxRateListSelect($this->item->store_id, '');
				$this->address = $model->getAddressList();
			}

			// Check for errors.
			if (count($errors = $this->get('Errors')))
			{
				throw new Exception(implode("\n", $errors));
			}

			$this->addToolbar();
		}
		else
		{
			$this->taxRule_id = $jinput->get('id');
			$defaultTaxRateId = '';
			$defaultAddressId = '';

			// Getting saved tax rules.
			if (!empty($this->taxRule_id))
			{
				$this->taxrules = $model->getTaxRules('', $this->taxRule_id);

				if (!empty($this->taxrules))
				{
					$defaultTaxRateId = $this->taxrules[0]->taxrate_id;
					$defaultAddressId = $this->taxrules[0]->address;
				}

				// Get store id of taxrule
				$taxHelper = new taxHelper;
				$store_id  = $taxHelper->getStoreIdFromTaxrule($this->taxRule_id);

				if (empty($store_id))
				{
					$this->qtcStoreNotFoundMsg();
				}

				// Getting tax rates and Adress types
				$this->taxrate = $model->getTaxRateListSelect($store_id, $defaultTaxRateId);
				$this->address = $model->getAddressList($defaultAddressId);
			}
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$viewTitle  = ($isNew) ? Text::_('COM_QUICK2CART_ADD_TAXPROFILE') : Text::_('COM_QUICK2CART_EDIT_TAXPROFILE');

		ToolBarHelper::title($viewTitle, 'pencil-2');

		if (isset($this->item->checked_out))
		{
			$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		}
		else
		{
			$checkedOut = false;
		}

		$canDo		= Quick2CartHelper::getActions();

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{
			ToolBarHelper::apply('taxprofile.apply', 'JTOOLBAR_APPLY');
			ToolBarHelper::save('taxprofile.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			ToolBarHelper::custom('taxprofile.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		if (empty($this->item->id))
		{
			ToolBarHelper::cancel('taxprofile.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolBarHelper::cancel('taxprofile.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */

	protected function qtcStoreNotFoundMsg()
	{
		?>
		<div class="techjoomla-bootstrap" >
			<div class="well" >
				<div class="alert alert-error">
					<span ><?php echo Text::_('QTC_SOMTHING_IS_WRONG_STORE_ID_NOT_FOUND'); ?> </span>
				</div>
			</div>
		</div><!-- eoc techjoomla-bootstrap -->
		<?php
		return false;
	}
}
