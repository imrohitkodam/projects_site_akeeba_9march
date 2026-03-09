<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.renderModal', 'a.modal');
$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

/**
 * Attribute controller class.
 *
 * @since  2.5
 */
class Quick2cartControllerAttributes extends quick2cartController
{
	public $qtc_icon_edit = '';

	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->qtc_icon_edit = "  icon-apply ";
	}

	/**
	 * function to delete attribute
	 *
	 * @return  null
	 */
	public function delattribute()
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$jinput  = Factory::getApplication()->input;
		$pid     = $jinput->get('pid');
		$attr_id = $jinput->get('attr_id');
		$model   = $this->getModel('attributes');
		$result  = $model->delattribute($attr_id);

		$quick2cartModelAttributes = new quick2cartModelAttributes;
		$attributes                = $quick2cartModelAttributes->getItemAttributes($pid);
		$data                      = array();
		$data['html']              = $html = '';

		if (empty($attributes))
		{
			$html = '<thead>
			<tr>
			<th width="35%" align="left"><b>' . Text::_('QTC_ADDATTRI_NAME') . ' </b></th>
			<th width="30%"	align="left"><b>' . Text::_('QTC_ADDATTRI_OPT') . '</b> </th>
			<th width="15%"	align="left"></th>
			</tr>
			</thead>
			<tbody>';
			$html .= '<tr id="empty_attr">
			<td colspan="3">' . Text::_('QTC_ADDATTRI_EMPTY_MSG') . '</td>
			</tr>';
		}

		$data['html'] = $html;
		echo json_encode($html);

		jexit();
	}

	/**
	 * Function to delete attribute options
	 *
	 * @return  null
	 */
	public function delattributeoption()
	{
		$jinput = Factory::getApplication()->input;
		$op_id  = $jinput->get('opt_id');

		if (!empty($op_id))
		{
			// Get attribute id

			$db    = Factory::getDBO();
			$query = 'SELECT itemattribute_id	From `#__kart_itemattributeoptions`
			WHERE `itemattributeoption_id`=' . $op_id;
			$db->setQuery($query);
			$att_id = $db->loadResult();

			// GET ATT OPTION COUNT
			$query = 'SELECT count(*)	From `#__kart_itemattributeoptions`
			WHERE `itemattribute_id`=' . $att_id;
			$db->setQuery($query);
			$count = $db->loadResult();
			$productHelper = new productHelper;

			if ($count == 1)
			{
				// Delete attribute with its option
				$productHelper->delWholeAttribute($att_id);
			}
			else
			{
				// DELTE OPTION ONLY
				$productHelper->delWholeAttributeOption($op_id);
			}
		}
	}

	/**
	 * Function to save attributes
	 *
	 * @return  null
	 */
	public function save()
	{
		Session::checkToken() or jexit('Invalid Token');

		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

		$model   = $this->getModel('attributes');
		$jinput  = Factory::getApplication()->input;
		$post    = $jinput->post;
		$baseURL = Uri::base() . "index.php";
		$client  = $post->get('client');

		switch ($jinput->get('task'))
		{
			case 'cancel':
				$this->setRedirect($baseURL . '?option=com_quick2cart');
				break;

			case 'save':
				$edit       = $post->get('edit');
				$att_detail = $post->get('att_detail', array(), 'ARRAY');
				$result     = $model->store($att_detail);

				if ($result)
				{
					if (isset($client) && !empty($client))
					{
						echo '<script type="text/javascript">
							window.setTimeout(window.parent.location.reload(), 300);
						</script>';
					}
					else
					{
						$msg = Text::_('QTC_ATTRI_SAVE');

						if ($edit == '1')
						{
							echo $edit = 3;
						}

						$baseURL = $baseURL . "?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=";
						$this->setRedirect($baseURL . $post->get('product_id') . "&edits=" . $edit . "&client=" . $client, $msg);
					}
				}
				else
				{
					$baseURL = $baseURL . '?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=';
					$msg = Text::_('QTC_ATTRI_SAVE_PROBLEM');
					$this->setRedirect($baseURL . $post->get('product_id') . "&client=" . $client, $msg);
				}
				break;
		}
	}

	/**
	 * Function to add currency
	 *
	 * @return  null
	 */
	public function addcurrency()
	{
		$jinput        = Factory::getApplication()->input;
		$cur_post      = $jinput->post;
		$item_name     = $jinput->get('item_name', '', 'STRING');
		$multi_cur     = $cur_post->get('multi_cur', array(), 'ARRAY');
		$data          = array();
		$originalCount = count($multi_cur);

		// Remove empty currencies from multi_curr
		$filtered_curr = array_filter($multi_cur, 'strlen');
		$filter_count  = count($filtered_curr);

		if ($item_name && $originalCount == $filter_count)
		{
			$comquick2cartHelper = new comquick2cartHelper;
			$result              = $comquick2cartHelper->saveProduct($cur_post);

			if ($result && !is_numeric($result))
			{
				$data = array(
					'0' => '0',
					'1' => Text::_('QTC_OPTIONS_NOT_SAVE', true)
				);
			}
			else
			{
				$data = array(
					'0' => '1',
					'1' => Text::_('COM_QUICK2CART_ITEM_SAVED_SUCCESSFULLY', true)
				);
			}
		}
		else
		{
			$data = array(
				'0' => '0',
				'1' => Text::_('QTC_OPTIONS_REQUIRED')
			);
		}

		echo json_encode($data);
		jexit();
	}

	/**
	 * Function to check product sku
	 *
	 * @return  attribute html
	 */
	public function checkSku()
	{
		$jinput = Factory::getApplication()->input;
		$sku    = $jinput->get('sku', '', 'RAW');

		// Call to front end controller funtion to make consistant
		$path = JPATH_SITE . '/components/com_quick2cart/controllers/product.php';

		if (!class_exists('Quick2cartControllerProduct'))
		{
			JLoader::register('Quick2cartControllerProduct', $path);
			JLoader::load('Quick2cartControllerProduct');
		}

		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		echo $Quick2cartControllerProduct->checkSku($sku);

		jexit();
	}

	/**
	 * Function for adding media file
	 *
	 * @return  null
	 */
	public function addMediaFile()
	{
		$jinput        = Factory::getApplication()->input;
		$post          = $jinput->post;
		$media_detail  = $post->get('prodMedia', array(), 'ARRAY');
		$item_id       = $post->get('item_id', '', 'INT');
		$mediafile_id  = $post->get('mediafile_id', '', 'INT');
		$productHelper = new productHelper;
		$status        = $productHelper->saveProdMediaDetails($media_detail, $item_id, 0);
		$edit          = $post->get('edit');
		$msg           = Text::_('QTC_MEDIA_SAVE_PROBLEM');

		if (!empty($status) && $status == 1)
		{
			$msg = Text::_('QTC_ATTRI_SAVE_SUCCESSFULL_CN_ADD_MORE');
		}

		if ($edit == '1')
		{
			$edit = 3;
		}

		echo '<script type="text/javascript">
				window.setTimeout(window.parent.location.reload(), 300);
			</script>';

		/*$base_url = 'index.php?option=com_quick2cart&view=attributes&layout=media&tmpl=component&item_id=';
		$this->setRedirect($base_url . $item_id . "&edits=" . $edit . "&file_id=" . $mediafile_id, $msg);*/
	}

	/**
	 * Function for deleting media file
	 *
	 * @return  null
	 */
	public function deleteMediFile()
	{
		// Add Language file.
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

		// Get Product_id via ajax url.

		$jinput  = Factory::getApplication()->input;
		$item_id = $jinput->get('pid');

		// Get file id for delete.

		$file_id = $jinput->get('file_id');
		$path    = JPATH_SITE . '/components/com_quick2cart/models/attributes.php';

		if (!class_exists('attributes'))
		{
			// Require_once $path;
			JLoader::register('attributes', $path);
			JLoader::load('attributes');
		}

		$quick2cartModelAttributes = new quick2cartModelAttributes;
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

		if (!class_exists('productHelper'))
		{
			// Require_once $path;
			JLoader::register('productHelper', $path);
			JLoader::load('productHelper');
		}

		$productHelper = new productHelper;
		$delFiles      = array();
		$delFiles[]    = $file_id;

		$productHelper->deleteProductMediaFile($delFiles);
		$attributes     = $quick2cartModelAttributes->getItemAttributes($item_id);
		$getMediaDetail = $productHelper->getMediaDetail($item_id);
		$data           = array();
		$data['html']   = $html = '';

		if (empty($getMediaDetail))
		{
			$html = '<thead>
						<tr>
							<th width="35%" align="left"><b>' . Text::_('QTC_MEDIAFILE_NAME') . '</b></th>
							<th width="30%"	align="left"><b>' . Text::_('QTC_MEDIAFILE_PURCHASE_REQUIRE') . '</b> </th>
							<th width="15%"	align="left"></th>
						</tr>
					</thead>';
			$html .= '<tr class="empty_media">
					<td colspan="3">' . Text::_('QTC_MEDIAFILE_EMPTY_MSG') . '</td>
				</tr>';
		}

		$data['html'] = $html;
		echo json_encode($html);

		jexit();
	}

	/**
	 * This function for only edit attribute
	 *
	 * @return  null
	 */
	public function EditAttribute()
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$params  = ComponentHelper::getParams('com_quick2cart');
		$jinput  = Factory::getApplication()->input;
		$pid     = $jinput->get('pid');
		$attr_id = $jinput->get('att_id');
		$model   = $this->getModel('attributes');

		$quick2cartModelAttributes = new quick2cartModelAttributes;
		$attributes                = $quick2cartModelAttributes->getItemAttributes($pid);
		$path                      = JPATH_SITE . '/components/com_quick2cart/helpers.php';

		if (!class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$qtc_base_url = Uri::base();
		$add_link     = $qtc_base_url . 'index.php?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=' . $pid;
		$del_link     = $qtc_base_url . 'index.php?option=com_quick2cart&controller=attributes&task=delattribute';
		$html         = '';

		if (!empty($attributes))
		{
			$invalid_op_price = array();

			foreach ($attributes as $attribute)
			{
				if ($attr_id == $attribute->itemattribute_id)
				{
					$html .= '<tr class="' . "att_" . $attribute->itemattribute_id . '">
								<td>' . $attribute->itemattribute_name . '</td>
								<td id="' . "att_list_" . $attribute->itemattribute_id . '">';
					$comquick2cartHelper = new comquick2cartHelper;
					$currencies          = $params->get('addcurrency');
					$curr                = explode(',', $currencies);
					$atri_options        = $comquick2cartHelper->getAttributeDetails($attribute->itemattribute_id);

					foreach ($atri_options as $atri_option)
					{
						$html .= '<div>';
						$noticeicon = "";
						$opt_str    = $atri_option->itemattributeoption_name . ": " . $atri_option->itemattributeoption_prefix;
						$itemnotice = '';

						foreach ($curr as $value)
						{
							if (property_exists($atri_option, $value))
							{
								if ($atri_option->$value)
								{
									$opt_str .= $atri_option->$value . " " . $value . ", ";
								}
							}
							else
							{
								// Add current cur
								$invalid_op_price[$value] = $value;

								if (empty($itemnotice))
								{
									$noticeicon = "<i class='icon-hand-right'></i> ";
								}
							}
						}

						$html .= $detail_str = $noticeicon . $opt_str;
						$html .= '</div>';
					}

					$html .= '</td>';
					$edit_link = $add_link . '&attr_id=' . $attribute->itemattribute_id . '&edit=1';
					$del_link = $del_link . '&attr_id=' . $attribute->itemattribute_id;
					$html .= '<td><a  rel="{handler: \'iframe\', size: {x : window.innerWidth-450, y : window.innerHeight-250}, onClose: function(){EditAttribute('
					. $attribute->itemattribute_id . ',' . $pid . ');}}" class="btn btn-mini btn-primary modal qtc_modal" href="'
					. $edit_link . '"> <i class="'
					. $this->qtc_icon_edit . ' icon22-white22"></i></a><button type="button" class="btn btn-mini btn-danger "  onclick=\'deleteAttribute("'
					. $attribute->itemattribute_id . '","' . $pid . '" )\'><i class="icon-trash icon22-white22"></i></button></td></tr>';
				}
			}
		}

		$data         = array();
		$data['html'] = $html;
		echo json_encode($html);

		jexit();
	}

	/**
	 * This function for only edit attribute
	 *
	 * @return  null
	 */
	public function AddNewAttribute()
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$params = ComponentHelper::getParams('com_quick2cart');
		$jinput = Factory::getApplication()->input;

		$pid                       = $jinput->get('pid');
		$model                     = $this->getModel('attributes');
		$quick2cartModelAttributes = new quick2cartModelAttributes;
		$attributes                = $quick2cartModelAttributes->getItemAttributes($pid);
		$path                      = JPATH_SITE . '/components/com_quick2cart/helpers.php';

		if (!class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$app          = Factory::getApplication();
		$qtc_base_url = Uri::base();
		$add_link     = $qtc_base_url . 'index.php?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=' . $pid;

		$del_link = $qtc_base_url . 'index.php?option=com_quick2cart&controller=attributes&task=delattribute';
		$html     = '';
		$count    = $jinput->get('count');
		$count--;

		if (!empty($attributes))
		{
			$invalid_op_price = array();

			for ($i = 0; $i < count($attributes); $i++)
			{
				if ($i > $count)
				{
					$html .= '<tr class="' . "att_" . $attributes[$i]->itemattribute_id . '">
								<td>' . $attributes[$i]->itemattribute_name . '</td>
								<td id="' . "att_list_" . $attributes[$i]->itemattribute_id . '">';
					$comquick2cartHelper = new comquick2cartHelper;
					$currencies          = $params->get('addcurrency');
					$curr                = explode(',', $currencies);
					$atri_options        = $comquick2cartHelper->getAttributeDetails($attributes[$i]->itemattribute_id);

					foreach ($atri_options as $atri_option)
					{
						$html .= '<div>';
						$noticeicon = "";
						$opt_str    = $atri_option->itemattributeoption_name . ": " . $atri_option->itemattributeoption_prefix;
						$itemnotice = '';

						foreach ($curr as $value)
						{
							if (property_exists($atri_option, $value))
							{
								if ($atri_option->$value)
								{
									$opt_str .= $atri_option->$value . " " . $value . ", ";
								}
							}
							else
							{
								// Add current cur
								$invalid_op_price[$value] = $value;

								if (empty($itemnotice))
								{
									$noticeicon = "<i class='icon-hand-right'></i> ";
								}
							}
						}

						$html .= $detail_str = $noticeicon . $opt_str;
						$html .= '</div>';
					}

					$html .= '</td>';
					$edit_link = $add_link . '&attr_id=' . $attributes[$i]->itemattribute_id . '&edit=1&test=test';
					$del_link = $del_link . '&attr_id=' . $attributes[$i]->itemattribute_id;
					$html .= '<td><a  rel="{handler: \'iframe\', size: {x : window.innerWidth-450, y : window.innerHeight-250}, onClose: function(){EditAttribute('
					. $attributes[$i]->itemattribute_id . ',' . $pid . ');}}" class="btn btn-mini btn-primary modal qtc_modal" href="' . $edit_link . '"> <i class="'
					. $this->qtc_icon_edit . ' icon22-white22"></i></a><button type="button" class="btn btn-mini btn-danger "  onclick=\'deleteAttribute("'
					. $attributes[$i]->itemattribute_id . '","' . $pid . '" )\'><i class="icon-trash icon22-white22"></i></button></td></tr>';
				}
			}
		}

		$data['html'] = $html;
		echo json_encode($html);

		jexit();
	}

	/**
	 * Function to edit media file
	 *
	 * @return  html
	 */
	public function EditMediFile()
	{
		// Add Language file.

		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$qtc_base_url = Uri::base();

		// Get Product_id via ajax url.
		$jinput  = Factory::getApplication()->input;
		$item_id = $jinput->get('pid');

		// Get file id for delete.

		$file_id = $jinput->get('file_id');
		$path = JPATH_SITE . '/components/com_quick2cart/models/attributes.php';

		if (!class_exists('attributes'))
		{
			// Require_once $path;

			JLoader::register('attributes', $path);
			JLoader::load('attributes');
		}

		$quick2cartModelAttributes = new quick2cartModelAttributes;
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

		if (!class_exists('productHelper'))
		{
			// Require_once $path;

			JLoader::register('productHelper', $path);
			JLoader::load('productHelper');
		}

		$productHelper  = new productHelper;
		$delFiles       = array();
		$delFiles[]     = $file_id;
		$attributes     = $quick2cartModelAttributes->getItemAttributes($item_id);
		$getMediaDetail = $productHelper->getMediaDetail($item_id, $file_id);
		$addMediaLink   = $qtc_base_url . 'index.php?option=com_quick2cart&view=attributes&layout=media&tmpl=component&item_id=' . $item_id;
		$html           = '';
		$count          = $jinput->get('count');
		$count--;

		if (!empty($getMediaDetail))
		{
			$mediaCount = count($getMediaDetail);

			for ($i = 0; $i < $mediaCount; $i++)
			{
				if ($i > $count)
				{
					$html .= '<tr class="' . "file_" . $getMediaDetail[$i]['file_id'] . '">
							<td>' . $getMediaDetail[$i]['file_display_name'] . '</td>
							<td>';
					$mediaClass = ' badge';
					$purchaseStatus = Text::_('QTC_ADDATTRI_PURCHASE_REQ_NO');

					if (!empty($getMediaDetail[$i]['purchase_required']))
					{
						$mediaClass = ' badge badge-success';
						$purchaseStatus = Text::_('QTC_ADDATTRI_PURCHASE_REQ_YES');
					}

					$html .= '<span class="' . $mediaClass . '">' . $purchaseStatus . '</span>
							</td>';
					$edit_link = $addMediaLink . '&file_id=' . $getMediaDetail[$i]['file_id'] . '&edit=1';
					$del_link = $addMediaLink . '&file_id=' . $getMediaDetail[$i]['file_id'];
					$html .= '<td>
								<a  rel="{handler: \'iframe\', size: {x : window.innerWidth-400, y : window.innerHeight-200}, onClose: function(){EditFile('
								. $getMediaDetail[$i]['file_id'] . ',' . $item_id . ');}}" class="btn btn-mini btn-primary modal qtc_modal" href="' . $edit_link
								. '"> <i class="' . $this->qtc_icon_edit . ' icon22-white22"></i>
								</a>
								<button type="button" class="btn btn-mini btn-danger "  onclick="deleteMediFile('
								. $getMediaDetail[$i]['file_id'] . ',' . $item_id . ' )"><i class="icon-trash icon22-white22"></i></button>

							 </td>
						</tr>';
				}
			}

			$data         = array();
			$data['html'] = $html;
			echo json_encode($html);
		}

		jexit();
	}

	/**
	 * Function Add extra field for CCK product
	 *
	 * @return  void
	 */
	public function addfield()
	{
		$jinput           = Factory::getApplication()->input;
		$data             = $jinput->post;
		$item_id          = $data->get('item_id', '', 'INT');
		$extra_jform_data = $jinput->get('jform', array(), 'array');

		if (!class_exists('Quick2cartModelProduct'))
		{
			JLoader::register('Quick2cartModelProduct', JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/product.php');
			JLoader::load('Quick2cartModelProduct');
		}

		$data                   = array();
		$data['client']         = 'com_quick2cart.products';
		$quick2cartModelProduct = new Quick2cartModelProduct;

		$data                = array();
		$data['content_id']  = $item_id;
		$data['client']      = 'com_quick2cart.product';
		$data['fieldsvalue'] = $extra_jform_data;
		$result              = $quick2cartModelProduct->saveExtraFields($data);
		$msg                 = Text::_('COM_QUICK2CART_FIELD_SAVE_PROBLEM');

		if ($result == 1)
		{
			$msg = Text::_('COM_QUICK2CART_FIELDS_SAVE_SUCCESSFULL_CN_ADD_MORE');
		}

		echo '<script type="text/javascript">
				window.setTimeout(window.parent.location.reload(), 300);
			</script>';
	}
}
