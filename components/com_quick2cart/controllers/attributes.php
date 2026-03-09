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

/**
 * Attributes controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerAttributes extends quick2cartController
{
	public $qtc_icon_edit = '';

	/**
	 * Constructor.
	 *
	 * @see     JController
	 *
	 * @since   1.6
	 */
	public function __construct()
	{
		parent::__construct();

		$this->qtc_icon_edit = "  icon-apply ";
	}

	/**
	 * This function delete Attributes
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function delattribute()
	{
		$jinput  = Factory::getApplication()->input;
		$pid     = $jinput->get('pid', '0', 'INTEGER');
		$attr_id = $jinput->get('attr_id', '', 'INTEGER');
		$model   = $this->getModel('attributes');
		$model->delattribute($attr_id);

		$quick2cartModelAttributes = new quick2cartModelAttributes;
		$attributes                = $quick2cartModelAttributes->getItemAttributes($pid);

		$data         = array();
		$data['html'] = $html = '';

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
			$html = $html . '<tr id="empty_attr">
			<td colspan="3">' . Text::_('QTC_ADDATTRI_EMPTY_MSG') . '</td>
			</tr>';
		}

		$data['html'] = $html;
		echo json_encode($html);

		jexit();
	}

	/**
	 * This function delete Attributes option
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function delattributeoption()
	{
		$jinput = Factory::getApplication()->input;
		$op_id  = $jinput->get('opt_id', '0', 'INTEGER');

		if (!empty($op_id))
		{
			// Get attribute id
			$db    = Factory::getDBO();
			$query = 'SELECT itemattribute_id From `#__kart_itemattributeoptions`
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
	 * This function Save
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function save()
	{
		Session::checkToken() or jexit('Invalid Token');
		$app     = Factory::getApplication();
		$model   = $this->getModel('attributes');
		$jinput  = $app->input;
		$post    = $jinput->post;
		$baseURL = Uri::base() . "index.php";
		$client  = $post->get('client');

		switch ($jinput->get('task'))
		{
			case 'cancel':
				$this->setRedirect($baseURL . '?option=com_quick2cart');
				break;

			case 'save':
				$edit = $post->get('edit');
				$att_detail = $post->get('att_detail', array(), 'ARRAY');
				$result = $model->store($att_detail);

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

						if ($edit === '1')
						{
							echo $edit = 3;
						}

						$this->setRedirect(
						$baseURL . "?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=" .
						$post->get('product_id') . "&edits=" . $edit . "&client=" . $post->get('client'), $msg
						);
					}
				}
				else
				{
					$msg = Text::_('QTC_ATTRI_SAVE_PROBLEM');
					$this->setRedirect(
					$baseURL . "?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=" .
					$post->get('product_id') . "&client=" . $post->get('client'), $msg
					);
				}

				break;
		}
	}

	/**
	 * This function Add currency
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function addcurrency()
	{
		$jinput        = Factory::getApplication()->input;
		$cur_post      = $jinput->post;
		$item_name     = $jinput->get('item_name', '', 'STRING');
		$multi_cur     = $cur_post->get('multi_cur', array(), 'ARRAY');
		$data          = array();
		$originalCount = count($multi_cur);

		//  Remove empty currencies from multi_curr
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
	 * This function Checksku
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function checkSku()
	{
		$jinput = Factory::getApplication()->input;
		$sku    = $jinput->get('sku', '', 'RAW');

		// Call to front end controller funtion to make consistant
		$path = JPATH_SITE . '/components/com_quick2cart/controllers/product.php';

		if (!class_exists('Quick2cartControllerProduct'))
		{
			// Require_once $path;
			JLoader::register('Quick2cartControllerProduct', $path);
			JLoader::load('Quick2cartControllerProduct');
		}

		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		echo $res = $Quick2cartControllerProduct->checkSku($sku);
		jexit();
	}

	/**
	 * This function Add Media File
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function addMediaFile()
	{
		$jinput       = Factory::getApplication()->input;
		$post         = $jinput->post;
		$media_detail = $post->get('prodMedia', array(), 'ARRAY');
		$item_id      = $post->get('item_id', '', 'INT');
		$mediafile_id = $post->get('mediafile_id', '', 'INT');
		$edit         = $post->get('edit');

		$productHelper = new productHelper;
		$status        = $productHelper->saveProdMediaDetails($media_detail, $item_id, 0);
		$msg           = Text::_('QTC_MEDIA_SAVE_PROBLEM');

		if (!empty($status) && $status == 1)
		{
			$msg = Text::_('QTC_ATTRI_SAVE_SUCCESSFULL_CN_ADD_MORE');
		}

		if ($edit === '1')
		{
			$edit = 3;
		}

		echo '<script type="text/javascript">
				window.setTimeout(window.parent.location.reload(), 300);
			</script>';
	}

	/**
	 * This function Delete Media File
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function deleteMediFile()
	{
		// Get Product_id via ajax url.
		$jinput  = Factory::getApplication()->input;
		$item_id = $jinput->get('pid', '', 'INTEGER');

		// Get file id for delete.
		$file_id = $jinput->get('file_id', '', 'INTEGER');
		$path    = JPATH_SITE . '/components/com_quick2cart/models/attributes.php';

		if (!class_exists('attributes'))
		{
			// Require_once $path;
			JLoader::register('attributes', $path);
			JLoader::load('attributes');
		}

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
		$getMediaDetail = $productHelper->getMediaDetail($item_id);

		if (empty($getMediaDetail))
		{
			$html = '<thead>
						<tr>
							<th width="35%" align="left"><b>' . Text::_('QTC_MEDIAFILE_NAME') . '</b></th>
							<th width="30%"	align="left"><b>' . Text::_('QTC_MEDIAFILE_PURCHASE_REQUIRE') . '</b> </th>
							<th width="15%"	align="left"></th>
						</tr>
					</thead>';
			$html = $html . '<tr class="empty_media">
					<td colspan="3">' . Text::_('QTC_MEDIAFILE_EMPTY_MSG') . '</td>
				</tr>';
			$data = array();
			$data['html'] = $html;
			echo json_encode($html);
		}

		jexit();
	}

	/**
	 * This function for only edit attribute
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function EditAttribute()
	{
		$params  = ComponentHelper::getParams('com_quick2cart');
		$jinput  = Factory::getApplication()->input;
		$pid     = $jinput->get('pid');
		$attr_id = $jinput->get('att_id');

		$quick2cartModelAttributes = new quick2cartModelAttributes;
		$attributes                = $quick2cartModelAttributes->getItemAttributes($pid);
		$path = JPATH_SITE . '/components/com_quick2cart/helpers.php';

		if (!class_exists('comquick2cartHelper'))
		{
			// Require_once $path;

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
					$currencies = $params->get('addcurrency');
					$curr = explode(',', $currencies);
					$atri_options = $comquick2cartHelper->getAttributeDetails($attribute->itemattribute_id);

					foreach ($atri_options as $atri_option)
					{
						$html = $html . '<div>';
						$noticeicon = "";
						$opt_str = $atri_option->itemattributeoption_name . ": " . $atri_option->itemattributeoption_prefix;
						$itemnotice = '';

						foreach ($curr as $value)
						{
							if (property_exists($atri_option, $value))
							{
								if ($atri_option->$value)
								{
									$opt_str = $opt_str . $atri_option->$value . " " . $value . ", ";
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

						$html = $html . $detail_str = $noticeicon . $opt_str;
						$html = $html . '</div>';
					}

					$html = $html . '</td>';
					$edit_link = $add_link . '&attr_id=' . $attribute->itemattribute_id . '&edit=1';
					$del_link = $del_link . '&attr_id=' . $attribute->itemattribute_id;
					$html = $html . '<td><a  rel="{handler: \'iframe\',
					size: {x : window.innerWidth-450, y : window.innerHeight-250},
					onClose: function(){EditAttribute(' .
					$attribute->itemattribute_id . ',' . $pid . ');}}"
					class="btn btn-sm btn-primary modal qtc_modal" href="' .
					$edit_link . '"> <i class="' . $this->qtc_icon_edit . ' icon-white"></i></a>
				<button type="button" class="btn btn-sm btn-danger "  onclick=\'deleteAttribute(
				"' . $attribute->itemattribute_id . '","' . $pid . '"
				)\'><i class="icon-trash icon-white"></i></button>
				 </td>
				</tr>';
				}
			}
		}

		$data = array();
		$data['html'] = $html;
		echo json_encode($html);
		jexit();
	}

	/**
	 * This function for Add new attribute
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function AddNewAttribute()
	{
		$params = ComponentHelper::getParams('com_quick2cart');
		$jinput = Factory::getApplication()->input;
		$pid    = $jinput->get('pid');

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
		$count        = $jinput->get('count');

		// Changes by Deepa
		/*$count = $count - 1;*/
		$count = $count --;

		if (!empty($attributes))
		{
			$invalid_op_price = array();

			for ($i = 0; $i < count($attributes); $i++)
			{
				if ($i > $count)
				{
					$html = $html . '<tr class="' . "att_" . $attributes[$i]->itemattribute_id . '">
								<td>' . $attributes[$i]->itemattribute_name . '</td>
								<td id="' . "att_list_" . $attributes[$i]->itemattribute_id . '">';
					$comquick2cartHelper = new comquick2cartHelper;
					$currencies          = $params->get('addcurrency');
					$curr                = explode(',', $currencies);
					$atri_options        = $comquick2cartHelper->getAttributeDetails($attributes[$i]->itemattribute_id);

					foreach ($atri_options as $atri_option)
					{
						$html       = $html . '<div>';
						$noticeicon = "";
						$opt_str    = $atri_option->itemattributeoption_name . ": " . $atri_option->itemattributeoption_prefix;
						$itemnotice = '';

						foreach ($curr as $value)
						{
							if (property_exists($atri_option, $value))
							{
								if ($atri_option->$value)
								{
									$opt_str = $opt_str . $atri_option->$value . " " . $value . ", ";
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

						$html = $html . $detail_str = $noticeicon . $opt_str;
						$html = $html . '</div>';
					}

					$html = $html . '</td>';
					$edit_link = $add_link . '&attr_id=' . $attributes[$i]->itemattribute_id . '&edit=1&test=test';
					$del_link = $del_link . '&attr_id=' . $attributes[$i]->itemattribute_id;
					$html = $html . '<td><a  rel="{handler: \'iframe\', size: {x : window.innerWidth-450, y : window.innerHeight-250},
					onClose: function(){EditAttribute(' . $attributes[$i]->itemattribute_id . ',' . $pid . ');}}"
					class="btn btn-sm btn-primary modal qtc_modal" href="' . $edit_link . '">
					<i class="' . $this->qtc_icon_edit . ' icon-white"></i></a>
				 <button type="button" class="btn btn-sm btn-danger "  onclick=\'deleteAttribute(
				 "' . $attributes[$i]->itemattribute_id . '","' . $pid . '" )\'>
				 <i class="icon-trash icon-white"></i></button>
				 </td>
				</tr>';
				}
			}
		}

		$data['html'] = $html;
		echo json_encode($html);
		jexit();
	}

	/**
	 * This function for Edit Media  File
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function EditMediFile()
	{
		$qtc_base_url = Uri::base();

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
		$getMediaDetail = $productHelper->getMediaDetail($item_id, $file_id);
		$addMediaLink   = $qtc_base_url . 'index.php?option=com_quick2cart&view=attributes&layout=media&tmpl=component&item_id=' . $item_id;
		$html           = '';
		$count          = $jinput->get('count');

		// Changes by Deepa
		/*$count = $count - 1;*/
		$count = $count --;

		if (!empty($getMediaDetail))
		{
			$mediaCount = count($getMediaDetail);

			for ($i = 0; $i < $mediaCount; $i++)
			{
				if ($i > $count)
				{
					$html = $html . '<tr class="' . "file_" . $getMediaDetail[$i]['file_id'] . '">
							<td>' . $getMediaDetail[$i]['file_display_name'] . '</td>
							<td>';
					$mediaClass = ' badge';
					$purchaseStatus = Text::_('QTC_ADDATTRI_PURCHASE_REQ_NO');

					if (!empty($getMediaDetail[$i]['purchase_required']))
					{
						$mediaClass = ' badge badge-success';
						$purchaseStatus = Text::_('QTC_ADDATTRI_PURCHASE_REQ_YES');
					}

					$html = $html . '<span class="' . $mediaClass . '">' . $purchaseStatus . '</span>
							</td>';
					$edit_link = $addMediaLink . '&file_id=' . $getMediaDetail[$i]['file_id'] . '&edit=1';
					$html = $html . '<td>
								<a  rel="{handler: \'iframe\', size: {x : window.innerWidth-400, y : window.innerHeight-200}, onClose: function(){EditFile(
								' . $getMediaDetail[$i]['file_id'] . ',' . $item_id . ');}}"
								class="btn btn-sm btn-primary modal qtc_modal" href="' . $edit_link . '"> <i class="' . $this->qtc_icon_edit . ' icon-white"></i>
								</a>
								<button type="button" class="btn btn-sm btn-danger "  onclick="deleteMediFile(
								' . $getMediaDetail[$i]['file_id'] . ',' . $item_id . ' )"><i class="icon-trash icon-white"></i></button>

							 </td>
						</tr>';
				}
			}

			$data = array();
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

		$data                = array();
		$data['client']      = 'com_quick2cart.products';
		$data['content_id']  = $item_id;
		$data['client']      = 'com_quick2cart.product';
		$data['fieldsvalue'] = $extra_jform_data;

		$quick2cartModelProduct = new Quick2cartModelProduct;
		$result                 = $quick2cartModelProduct->saveExtraFields($data);
		$msg                    = Text::_('COM_QUICK2CART_FIELD_SAVE_PROBLEM');

		if ($result == 1)
		{
			$msg = Text::_('COM_QUICK2CART_FIELDS_SAVE_SUCCESSFULL_CN_ADD_MORE');
		}

		echo '<script type="text/javascript">
				window.setTimeout(window.parent.location.reload(), 300);
			</script>';
	}
}
