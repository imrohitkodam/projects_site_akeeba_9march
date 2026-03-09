<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\String\StringHelper;

JLoader::import('helpers.storeHelper', JPATH_SITE . '/components/com_quick2cart');
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

/**
 * This Class supports attributes.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelAttributes extends BaseDatabaseModel
{
	/**
	 * Function to get item attribute
	 *
	 * @param   INT  $item_id  id
	 *
	 * @return  Array
	 */
	public function getItemAttributes($item_id)
	{
		$db = Factory::getDBO();

		if (!empty($item_id))
		{
			try
			{
				$query = $db->getQuery(true);
				$query->select("*")->from('#__kart_itemattributes')->where(" item_id = " . $item_id)->order(" itemattribute_id ASC");
				$db->setQuery($query);

				return $db->loadobjectList();
			}
			catch (Exception $e)
			{
				$this->setError($e->getMessage());

				return array();
			}
		}
	}

	/**
	 * Function to get item attribute options
	 *
	 * @param   INT  $attr_id  attribute id
	 *
	 * @return  ARRAY
	 */
	public function getItemAttributeOptions($attr_id)
	{
		$db    = Factory::getDBO();
		$query = 'SELECT opt.itemattributeoption_name FROM #__kart_itemattributeoptions AS opt WHERE opt.itemattribute_id='
		. (int) $attr_id . ' ORDER BY opt.ordering';
		$db->setQuery($query);
		$options = $db->loadColumn();

		return $options;
	}

	/**
	 * Function to get attribute
	 *
	 * @return  boolean
	 */
	public function getAttribute()
	{
		$db    = Factory::getDBO();
		$jinput = Factory::getApplication()->input;
		$id     = $jinput->get('attr_id');
		$query  = "SELECT itemattribute_name,attribute_compulsary,`attributeFieldType` FROM #__kart_itemattributes  WHERE itemattribute_id=" . (int) $id;
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}

	/**
	 * This attribute option
	 *
	 * @param   INT  $id  id
	 *
	 * @return  boolean
	 */
	public function getAttributeoption($id = '')
	{
		if (empty($id))
		{
			$jinput = Factory::getApplication()->input;
			$id     = $jinput->get('attr_id');
		}

		$db    = Factory::getDBO();
		$query = 'SELECT * FROM #__kart_itemattributeoptions AS opt WHERE opt.itemattribute_id=' . (int) $id . ' ORDER BY opt.ordering';
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!empty($result))
		{
			$comquick2cartHelper = new comquick2cartHelper;
			$path                = JPATH_SITE . '/components/com_quick2cart/models/cart.php';
			$Quick2cartModelcart = $comquick2cartHelper->loadqtcClass($path, "Quick2cartModelcart");

			foreach ($result as $key => $attriOption)
			{
				if (!empty($attriOption->child_product_item_id))
				{
					// Fetch item details
					$result[$key]->child_product_detail = $Quick2cartModelcart->getItemRec($attriOption->child_product_item_id);
				}
			}
		}

		return $result;
	}

	/**
	 * This function delete item
	 *
	 * @param   INT  $item_id  id
	 *
	 * @return  boolean
	 */
	public function deleteItem($item_id)
	{
		$db    = Factory::getDBO();
		$query = "DELETE FROM #__kart_items  WHERE item_id=" . (int) $item_id;
		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * This function delete attribute
	 *
	 * @param   INT  $id  id
	 *
	 * @return  boolean
	 */
	public function delattribute($id)
	{
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$attributeData       = $this->getAttributeData($id);
		$productData         = $this->getItemDetail(0, '', $attributeData->item_id);
		$storeOwner          = $storeHelper->getStoreOwner($productData['store_id']);
		$isOwner             = $comquick2cartHelper->checkOwnership($storeOwner);
		$db                  = Factory::getDbo();

		if ($isOwner)
		{
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__kart_itemattributes'));
			$query->where($db->quoteName('itemattribute_id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$db->execute();

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__kart_itemattributeoptions'));
			$query->where($db->quoteName('itemattribute_id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$db->execute();
		}
		else
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}

	/**
	 * Function to get attribute data
	 *
	 * @param   INT  $id  attribute id
	 *
	 * @return object
	 *
	 * @since  2.9.9
	 *
	 * */
	public function getAttributeData($id)
	{
		if (!empty($id))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__kart_itemattributes');
			$query->where('itemattribute_id' . ' = ' . $id);
			$db->setQuery($query);
			$conditionList = $db->loadObject();

			return $conditionList;
		}
	}

	/**
	 * This function delete attribute
	 *
	 * @param   INT  $id  id
	 *
	 * @return  boolean
	 */
	public function delattributeOnly($id)
	{
		$db    = Factory::getDBO();
		$query = "DELETE FROM #__kart_itemattributes  WHERE itemattribute_id=" . (int) $id;
		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * This function delete attribute options
	 *
	 * @param   INT  $id  id
	 *
	 * @return  boolean
	 */
	public function delattributeoption($id)
	{
		$db    = Factory::getDBO();
		$query = "DELETE FROM #__kart_itemattributeoptions  WHERE itemattributeoption_id=" . (int) $id;
		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * This function save/update attribute.
	 *
	 * @param   ARRAY   $data  data
	 *
	 * @param   STRING  $sku   sku
	 *
	 * @return  boolean
	 */
	public function store($data, $sku = '')
	{
		$app  = Factory::getApplication();
		$db   = Factory::getDBO();

		PluginHelper::importPlugin('system');
		$result = $app->triggerEvent('onBeforeQ2cAttributeSave', array($data));

		if (!empty($result[0]))
		{
			$data = $result[0];
		}

		// Depricated Start
		$result = $app->triggerEvent('onBeforeQ2cAttributeSave', array($data));

		if (!empty($result[0]))
		{
			$data = $result[0];
		}

		// Depricated End

		// To store attribute name in #__kart_itemattributes table

		// Field type= textbox then there will not be any options
		$userFields   = array();
		$userFields[] = 'Textbox';
		$userdata     = (!empty($data['fieldType']) && in_array($data['fieldType'], $userFields)) ? 1 : 0;

		if (empty($userdata))
		{
			$ind = 0;

			if (!empty($data['attri_opt']))
			{
				foreach ($data['attri_opt'] as $key => $att_options)
				{
					$ind = $key;
				}
			}
		}

		$DelTask = 0;

		if (!empty($data['delete_attri']))
		{
			$DelTask = 1;
			$data    = $this->deleteAttributeOption($data);
		}

		if (empty($userdata))
		{
			$data = $this->removeInvalideOption($data);

			// If Options r not present
			if (count($data['attri_opt']) == 0)
			{
				$this->noOptionThenDelAttr($data['attri_id']);

				// If true when delete task
				$return = ($DelTask == 1)?true:false;

				return $return;
			}
		}

		$row = new stdClass;

		if ($data['attri_name'])
		{
			// $row = new stdClass;
			$row->itemattribute_name  = $data['attri_name'];
			$row->attributeFieldType  = $data['fieldType'];
			$row->global_attribute_id = isset($data['global_attribute_set']) ? $data['global_attribute_set'] : 0;

			if (isset($data['is_stock_keeping']))
			{
				$row->is_stock_keeping = 1;
			}

			// 1. store attribute name
			$row->attribute_compulsary = (isset($data['iscompulsary_attr'])) ? 1 : 0;

			// While updating the attribute
			if (!empty($data['attri_id']))
			{
				// @TODO VM: if fieldtype = text then delete all attribute option (in db)
				$row->itemattribute_id = $data['attri_id'];

				if (!$db->updateObject('#__kart_itemattributes', $row, "itemattribute_id"))
				{
					echo $db->stderr();

					return false;
				}
				/*if ATTRIB IS GOING TO UPDATE THEN COMPARE (DB OPTIONS AND POST OPTION) ,DEL EXTRA OPTION FROM DB ONLY */
				$att_option_ids = array();

				// GETTING OPTION ID'S ARRAY
				foreach ($data['attri_opt'] as $option)
				{
					$att_option_ids[] = $option['id'];
				}

				if (!empty($att_option_ids))
				{
					$productHelper = new productHelper;
					$productHelper->deleteExtraAttributeOptions($data['attri_id'], $att_option_ids);
				}
			}
			else
			{
				// For new attribute
				// Load Attributes model  // REQUIRE WHEN CALLED FROM BACKEND
				$comquick2cartHelper = new comquick2cartHelper;
				$path                = JPATH_SITE . '/components/com_quick2cart/models/cart.php';
				$Quick2cartModelcart = $comquick2cartHelper->loadqtcClass($path, "Quick2cartModelcart");

				if (!empty($data['item_id']))
				{
					$item_id = $data['item_id'];
				}
				elseif (!empty($data['product_id']) && !empty($data['client']))
				{
					$item_id = $Quick2cartModelcart->getitemid($data['product_id'], $data['client']);
				}
				elseif (!empty($data['sku']))
				{
					$item_id = $Quick2cartModelcart->getitemid(0, $data['client'], $data['sku']);
				}

				$row->item_id = $item_id;

				if (!$db->insertObject('#__kart_itemattributes', $row, 'itemattribute_id'))
				{
					echo $db->stderr();

					return false;
				}
			}
		}

		if (!empty($userdata))
		{
			// GETTING OPTION ID'S ARRAY
			if (count($data['attri_opt']) > 1 && isset($data['attri_id']) && $data['attri_id'])
			{
				$count      = 0;
				$removeUnwantedAttr= array();
				foreach ($data['attri_opt'] as $option)
				{
					if ($option['id'])
					{
						if ($count)
						{
							$removeUnwantedAttr[] = $option['id'];
						}
						$count++;
					}
				}

				$this->deleteAttributeOptionAfterSelectToTextUpdate($data['attri_id'], $removeUnwantedAttr);
			}

			// Add extra option for user data
			$option                    = array();
			$op                        = $data['attri_opt'][0];
			$op['name']                = $row->itemattribute_name;
			$op['prefix']              = '+';

			foreach ($op['currency'] as $key => $curr)
			{
				$op['currency'][$key] = 0;
			}

			// Set default option to data
			$data['attri_opt'][0] = $op;
			array_splice($data['attri_opt'], 1);

		}

		$row->attribute_compulsary = (isset($data['iscompulsary_attr'])) ? 1 : 0;
		$is_stock_keepingAttri     = (isset($data['is_stock_keeping']) && ($row->attribute_compulsary == true))?1:0;

		//  2. to store attribute option in #__itemattributeoptions_ table
		foreach ($data['attri_opt'] as $key => $attri_opt)
		{
			//  Generate detail for child product id
			$optionDetail               = array();
			$optionDetail['item_id']    = $data['item_id'];
			$optionDetail['attri_name'] = $data['attri_name'];
			$optionDetail['attri_opt']  = $attri_opt;

			if ($attri_opt['name'] && $attri_opt['currency'] && $attri_opt['prefix']) // && $attri_opt['order'])
			{
				$opt                             = new stdClass;
				$opt->itemattributeoption_name   = $attri_opt['name'];
				$opt->global_option_id           = $attri_opt['globalOptionId'] ? $attri_opt['globalOptionId'] : 0;
				$opt->state                      = isset($attri_opt['state']) ? $attri_opt['state'] : 1;
				$currkeys                        = array_keys($attri_opt['currency']);

				// Make array of currency keys
				$currkey                         = $currkeys[0];

				if (isset($attri_opt['currency'][$currkey]) && !empty($attri_opt['currency'][$currkey]))
				{
					$opt->itemattributeoption_price  = $attri_opt['currency'][$currkey];
				}
				else
				{
					$opt->itemattributeoption_price  = 0;
				}

				$opt->itemattributeoption_prefix = $attri_opt['prefix'];
				$opt->ordering                   = $attri_opt['order'];

				// UPDATING ATT OPTION
				if (!empty($attri_opt['id']))
				{
					// Update attribute option
					$opt->itemattributeoption_id = $attri_opt['id'];

					if (!$db->updateObject('#__kart_itemattributeoptions', $opt, 'itemattributeoption_id'))
					{
						echo $db->stderr();

						return false;
					}

					// After success update table #__kart_option_currency
					$db    = Factory::getDBO();
					$query = "select * from `#__kart_option_currency` where itemattributeoption_id=" . (int) $opt->itemattributeoption_id;
					$db->setQuery($query);
					$result = $db->loadAssocList();

					if ($result)
					{
						foreach ($attri_opt['currency'] as $key => $value)
						{
							$flag     = -1;

							// To check currency field is present or not for that product
							$updateid = -1;

							foreach ($result as $dbkey => $dbvalue)
							{
								if ($key == $dbvalue['currency'])
								{
									$flag     = 1;
									$updateid = $dbvalue['id'];
									break;
								}
							}

							// Found currency so update updateid row
							if ($flag == 1 && $updateid)
							{
								$updateobj        = new stdClass;
								$updateobj->id    = $updateid;
								$updateobj->price = $value ? $value : 0;

								if (!$db->updateObject('#__kart_option_currency', $updateobj, 'id'))
								{
									echo $db->stderr();

									return false;
								}
							}
							else
							{
								$updateobj                         = new stdClass;
								$updateobj->id                     = null;
								$updateobj->itemattributeoption_id = (int) $opt->itemattributeoption_id;
								$updateobj->currency               = $key;
								$updateobj->price                  = $value ? $value : 0;

								if (!$db->insertObject('#__kart_option_currency', $updateobj, 'id'))
								{
									echo $db->stderr();

									return false;
								}
							}
						}
					}
					else
					{
						foreach ($attri_opt['currency'] as $key => $value)
						{
							$currobj                         = new stdClass;
							$currobj->id                     = null;
							$currobj->itemattributeoption_id = (int) $opt->itemattributeoption_id;
							$currobj->currency               = $key;
							$currobj->price                  = $value;

							if (!$db->insertObject('#__kart_option_currency', $currobj, 'id'))
							{
								echo $db->stderr();

								return false;
							}
						}
					}

					// Update child product stock
					if ($is_stock_keepingAttri)
					{
						$optionDetail['itemattributeoption_id'] = $opt->itemattributeoption_id;
						$chileProdItem_id = $this->createChildProd($optionDetail);

						// Ideally this should not require for update
						$this->mapChildprodutToOption($optionDetail['itemattributeoption_id'], $chileProdItem_id);
					}
				}
				else
				{
					// Adding new  ATT OPTION
					$opt->itemattribute_id = $row->itemattribute_id;

					if (!$db->insertObject('#__kart_itemattributeoptions', $opt, 'itemattributeoption_id'))
					{
						echo $db->stderr();

						return false;
					}
					// If INSERTED AND NOT ERROR THEN STORE OPTION CURRENCY
					// Add attribute option to DB
					$insert_id = $opt->itemattributeoption_id; // get last inserted id

					foreach ($attri_opt['currency'] as $key => $value)
					{
						$option                         = new stdClass;
						$option->id                     = null;
						$option->itemattributeoption_id = (int) $insert_id;
						$option->currency               = $key;
						$option->price                  = (float) $value;

						if (!$db->insertObject('#__kart_option_currency', $option, 'id'))
						{
							echo $db->stderr();

							return false;
						}
					}

					// Update child product stock
					if ($is_stock_keepingAttri)
					{
						$optionDetail['itemattributeoption_id'] = $opt->itemattributeoption_id;
						$chileProdItem_id = $this->createChildProd($optionDetail);

						// Map child product id to option
						$this->mapChildprodutToOption($optionDetail['itemattributeoption_id'], $chileProdItem_id);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Save the product basic option.
	 *
	 * @param   object  $curr_post  Post objec.
	 *
	 * @since   2.2
	 *
	 * @return   boolean|integer|string  $message
	 */
	public function storecurrency($curr_post)
	{
		$app      = Factory::getApplication();
		$itemname = $curr_post->get('item_name', '', 'STRING');
		$store_id = $curr_post->get('store_id', '', 'STRING');
		$pid      = $curr_post->get('pid', '', 'STRING');

		// @TODO check - ^sanjivani
		if (empty($pid))
		{
			// For native product manager
			$pid = $curr_post->get('item_id', '', 'STRING');
		}

		$client                    = $curr_post->get('client', '', 'STRING');
		$sku                       = $curr_post->get('sku', '', 'RAW');
		$res                       = '';
		$message                   = '';
		$comquick2cartHelper       = new comquick2cartHelper;
		$db                        = Factory::getDbo();
		$params                    = ComponentHelper::getParams('com_quick2cart');

		// Used to store in kart_item table
		$kart_curr_param           = $params->get('addcurrency');
		$kart_curr_param_array     = explode(',', $kart_curr_param);
		$kart_item_curr            = $kart_curr_param_array[0];
		$quick2cartModelAttributes = new quick2cartModelAttributes;

		$item_id          = $quick2cartModelAttributes->getitemid($pid, $client);
		$img_dimensions   = array();
		$img_dimensions[] = 'small';
		$img_dimensions[] = 'medium';
		$img_dimensions[] = 'large';
		$image_path       = array();

		// STORING ALL IMAGES images upladed (on new or edit)
		foreach ($_FILES as $key => $imgfile)
		{
			// Only process Q2C image file. ()
			$position = strpos($key, 'prod_img');

			if (is_numeric($position) && !empty($imgfile['name']))
			{
				$image_path[] = $comquick2cartHelper->imageupload($key, $img_dimensions);
			}
		}

		$qtc_prodImgs = $curr_post->get('qtc_prodImg', array(), 'ARRAY');

		if (!empty($qtc_prodImgs))
		{
			foreach ($image_path as $newImg)
			{
				$qtc_prodImgs[] = $newImg;
			}

			// $image_path = $curr_post->get('qtc_prodImg', array(), 'ARRAY');
			$image_path = array_filter($qtc_prodImgs, "trim");
		}

		$image_path = (!empty($image_path)) ? json_encode($image_path) : '';

		// @TODO save images and store in DB
		$images        = "";

		// GETTING ATTRIBUTE DETAILS,multi currency and discount details
		$multi_cur     = $curr_post->get('multi_cur', array(), 'ARRAY');
		$multi_dis_cur = $curr_post->get('multi_dis_cur', array(), 'ARRAY');

		$data = $curr_post->getArray();

		if (!$item_id)
		{
			$state = $curr_post->get('state');

			if (empty($state))
			{
				$state = 0;
			}

			// Call the trigger to add extra field in product page.
			PluginHelper::importPlugin("system");
			PluginHelper::importPlugin("actionlog");
			$app->triggerEvent("onBeforeQ2cSaveProduct", array($data, true));

			// Depricated
			$app->triggerEvent("onBeforeQ2cSavingProductBasicDetail", array($curr_post, 'insert'));

			// Save new product
			$item_id = $this->storeInKartItem('insert', $image_path, $multi_cur[$kart_item_curr], $curr_post);
			$data['item_id'] = $item_id;

			if ($item_id)
			{
				// On after product save
				PluginHelper::importPlugin("system");
				PluginHelper::importPlugin("actionlog");
				$app->triggerEvent("onAfterQ2cSaveProduct", array($data, true));
			}
		}
		else
		{
			// Dont set default value as 1 (require for unpublish)
			$state = $curr_post->get('state');

			if (isset($state))
			{
				$state = $state;
			}

			$productHelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/product.php", "productHelper");
			$productHelper->deleteNotReqProdImages($item_id, $image_path);

			// Call the trigger to add extra field in product page.
			PluginHelper::importPlugin("system");
			PluginHelper::importPlugin("actionlog");
			$app->triggerEvent("onBeforeQ2cSaveProduct", array($data, false));

			// Depricated
			$app->triggerEvent("onBeforeQ2cSavingProductBasicDetail", array($curr_post, 'update'));
			$item_id = $this->storeInKartItem('update', $image_path, $multi_cur[$kart_item_curr], $curr_post);
			$data['item_id'] = $item_id;

			if ($item_id)
			{
				// On after product save
				PluginHelper::importPlugin("system");
				PluginHelper::importPlugin("actionlog");
				$app->triggerEvent("onAfterQ2cSaveProduct", array($data, false));
			}
		}

		$message = $item_id;
		$query   = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__kart_base_currency'));
		$query->where($db->quoteName('item_id') . " = " . (int) $item_id);
		$db->setQuery($query);
		$res = $db->loadAssocList();

		if ($res)
		{
			foreach ($multi_cur as $cur_name => $cur_value)
			{
				$db   = Factory::getDBO();
				$flag = 0;
				$currencyId = 0;

				foreach ($res as $k => $v)
				{
					if ($cur_name == $v['currency'])
					{
						// Take currency value frm post and match whith Db currency
						$currencyId = $v['id'];
						$flag = 1;
						break;
					}
				}

				if ($flag == 1)
				{
					$items                 = new stdClass;
					$items->id             = $currencyId;
					$items->item_id        = $item_id;
					$items->currency       = $cur_name;
					$items->price          = $cur_value;
					$items->discount_price = null;

					if (isset($multi_dis_cur[$cur_name]) && $multi_dis_cur[$cur_name] != '')
					{
						$dis_curr = (float) $multi_dis_cur[$cur_name];
						$items->discount_price = $dis_curr;
					}

					if (!$db->updateObject('#__kart_base_currency', $items, 'id', true))
					{
						$message     = Text::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
					}
				}
				else
				{
					$items                 = new stdClass;
					$items->item_id        = $item_id;
					$items->currency       = $cur_name;
					$items->price          = $cur_value;
					$items->discount_price = null;

					if (isset($multi_dis_cur[$cur_name]) && $multi_dis_cur[$cur_name] != '')
					{
						$items->discount_price = (float) $multi_dis_cur[$cur_name];
					}

					if (!$db->insertObject('#__kart_base_currency', $items))
					{
						$message     = Text::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
					}
				}
			}
		}
		else
		{
			// Curr_post contain INR ,USD etc .....
			foreach ($multi_cur as $cur_name => $cur_value)
			{
				$discount_price = 'NULL';

				if (isset($multi_dis_cur[$cur_name]) && $multi_dis_cur[$cur_name] != '')
				{
					$discount_price = $multi_dis_cur[$cur_name];
				}

				$columns = array('item_id', 'currency', 'price', 'discount_price');

				$values = array($item_id, $db->quote($cur_name), $cur_value, $discount_price);

				$query   = $db->getQuery(true);
				$query->insert($db->quoteName('#__kart_base_currency'))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));
				$db->setQuery($query);

				if (!$db->execute())
				{
					$message     = Text::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
				}
			}
		}

		return $message;
	}

	/**
	 * Function to get product alias
	 *
	 * @param   Object  $curr_post  current post
	 *
	 * @return  string  $alias
	 */
	public function getAlias($curr_post)
	{
		// Alias added
		$app    = Factory::getApplication();
		$alias  = $curr_post->get('item_alias', '', 'STRING');
		$pid    = $curr_post->get('pid', '', 'STRING');
		$client = $curr_post->get('client', '', 'STRING');
		$itemId = $this->getitemid($pid, $client);
		$alias  = trim($alias);
		$db     = Factory::getDbo();

		if (empty($alias))
		{
			$alias = $curr_post->get('item_name', '', 'STRING');
		}

		if ($alias)
		{
			if (Factory::getConfig()->get('unicodeslugs') == 1)
			{
				$alias = OutputFilter::stringURLUnicodeSlug($alias);
			}
			else
			{
				$alias = OutputFilter::stringURLSafe($alias);
			}
		}

		// Check if product with same alias is present
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_categories/tables');
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');
		$table = Table::getInstance('Product', 'Quick2cartTable', array('dbo', $db));

		if ($table->load(array('alias' => $alias)) && ($table->item_id != $itemId || $itemId == 0))
		{
			$msg = Text::_('COM_QUICK2CART_SAVE_ALIAS_WARNING');

			while ($table->load(array('alias' => $alias)))
			{
				$alias = StringHelper::increment($alias, 'dash');
			}

			$app->enqueueMessage($msg, 'warning');
		}

		// Check if category with same alias is present
		$category = Table::getInstance('Category', 'JTable');

		if ($category->load(array('alias' => $alias)))
		{
			$msg = Text::_('COM_QUICK2CART_SAVE_PRODUCT_WARNING_DUPLICATE_CATEGORY_ALIAS');

			while ($category->load(array('alias' => $alias)))
			{
				$alias = StringHelper::increment($alias, 'dash');
			}

			$app->enqueueMessage($msg, 'warning');
		}

		$quick2cartViews = array('adduserform', 'createorder', 'productpage', 'shipprofileform', 'vendor',
			'attributes', 'customer_addressform', 'promotion', 'shipprofiles', 'zoneform', 'cart',
			'downloads', 'promotions', 'stores', 'zones', 'cartcheckout', 'taxprofileform', 'category',
			'orders', 'taxprofiles', 'couponform', 'payouts', 'registration', 'taxrateform', 'coupons',
			'product', 'shipping', 'taxrates');

		if (in_array($alias, $quick2cartViews))
		{
			$alias = StringHelper::increment($alias, 'dash');

			while ($table->load(array('alias' => $alias)))
			{
				$alias = StringHelper::increment($alias, 'dash');
			}
		}

		if (trim(str_replace('-', '', $alias)) == '')
		{
			$alias = Factory::getDate()->format("Y-m-d-H-i-s");
		}

		return $alias;
	}

	/**
	 * Function to store in cart item
	 *
	 * @param   String  $operation  operation
	 * @param   ARRAY   $images     images
	 * @param   INT     $price      price
	 * @param   Object  $post       product object
	 *
	 * @return  Integer|Boolean|String  $inserid   Product id
	 */
	public function storeInKartItem($operation, $images, $price, $post)
	{
		$params              = ComponentHelper::getParams('com_quick2cart');
		$q2cAttributesModel  = new quick2cartModelAttributes;
		$isEditorEnabled     = $params->get('enable_editor', 0);
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$storeOwner          = $storeHelper->getStoreOwner($post->get('store_id', 0, 'INT'));

		if (!$storeOwner)
		{
			$isOwner             = $comquick2cartHelper->checkOwnership($storeOwner);
			if (!$isOwner)
			{
				throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

		$kartItem                            = new stdClass;
		$kartItem->parent                    = $post->get('client', '', 'STRING');
		$kartItem->store_id                  = $post->get('store_id', 0, 'INT');
		$kartItem->product_id                = $post->get('pid', 0, 'INT');
		$kartItem->product_type              = $post->get('qtc_product_type', '1', 'STRING');
		$kartItem->name                      = $post->get('item_name', '', 'STRING');
		$kartItem->alias                     = (!empty($post->get('item_alias', '', 'STRING'))) ? $post->get('item_alias', '', 'STRING') : $this->getAlias($post);
		$kartItem->price                     = $price;
		$kartItem->category                  = $post->get('prod_cat', '', 'INT');
		$kartItem->sku                       = $post->get('sku', '', 'RAW');
		$kartItem->video_link                = $post->get('youtube_link', '', 'STRING');
		$kartItem->display_in_product_catlog = 1;
		$kartItem->metadesc                  = $post->get('metadesc', '', 'STRING');
		$kartItem->metakey                   = $post->get('metakey', '', 'STRING');
		$kartItem->item_length               = (FLOAT) $post->get('qtc_item_length', '', 'STRING');
		$kartItem->item_width                = (FLOAT) $post->get('qtc_item_width', '', 'STRING');
		$kartItem->item_height               = (FLOAT) $post->get('qtc_item_height', '', 'STRING');
		$kartItem->item_length_class_id      = $post->get('length_class_id', 0, 'INT');
		$kartItem->item_weight               = (FLOAT) $post->get('qtc_item_weight', '', 'STRING');
		$kartItem->item_weight_class_id      = $post->get('weigth_class_id', 0, 'INT');
		$kartItem->taxprofile_id             = $post->get('taxprofile_id', '', 'INT');
		$kartItem->shipProfileId             = $post->get('qtc_shipProfile', 0, 'INT');
		$kartItem->slab                      = $post->get('item_slab', '', 'INT');
		$kartItem->tags                      = $post->get('jform', array(), 'array')['tags'];
		$des                                 = $post->get('description', array(), 'ARRAY');
		$kartItem->description               = !empty($des['data']) ? $des['data'] : '';
		$kartItem->featured                  = $post->get('featured', '0', 'STRING');
		$kartItem->taxprofile_id             = $post->get('taxprofile_id', 0, 'INT');
		$kartItem->parent_id                 = $post->get('parent_id', 0, 'INT');

		// Remove html when editor is OFF
		if (!$isEditorEnabled)
		{
			$kartItem->description = !empty($des['data']) ? strip_tags($des['data']) : '';
		}

		// #40581 = temporary fix (For zoo state field is overlapping with item's state field)
		$kartItem->state = $post->get('state', 0, 'INT');
		$stateField      = $post->get('qtcProdState', '', 'INT');

		if ($stateField === 0 || $stateField === 1)
		{
			$kartItem->state = $stateField;
		}

		// @HAVE TO CODE TO STORE IMAGES
		// if stock is present it may be 0 But not NULL
		$stock = $post->get('stock');

		if ($stock)
		{
			$kartItem->stock = $stock;
		}
		else 
		{
			$kartItem->stock = 0;
		}

		// Set Product min quantity
		$minQuantity            = $post->get('min_item', 1, 'INT');
		$kartItem->min_quantity = $minQuantity;

		if ($minQuantity == 0)
		{
			$kartItem->min_quantity = 1;
		}

		// Set Product max quantity
		$maxQuantity            = $post->get('max_item', 999, 'INT');
		$kartItem->max_quantity = $maxQuantity;

		if ($maxQuantity == 0)
		{
			$kartItem->max_quantity = 999;
		}

		// Set image is present for product
		if (!empty($images))
		{
			$kartItem->images = $images;
		}

		if ($operation == 'insert')
		{
			$kartItem->cdate = Factory::getDate()->format('Y-m-d H:m:s');
		}
		elseif ($operation == 'update')
		{
			$pid               = $post->get('pid', '', 'STRING');
			$client            = $post->get('client', '', 'STRING');
			$itemId            = $q2cAttributesModel->getitemid($pid, $client);
			$kartItem->item_id = $itemId;
			$itemDetails       = $q2cAttributesModel->getItemDetail($pid, $client, $itemId);
			$kartItem->cdate   = (!empty($itemDetails['cdate'])) ? $itemDetails['cdate'] : Factory::getDate()->format('Y-m-d H:m:s');
		}

		$kartItem->mdate   = Factory::getDate()->format('Y-m-d H:m:s');
		$productTable = $this->getTable();

		if (isset($kartItem->tags) && !empty($kartItem->tags) && $kartItem->tags[0] != '')
		{
			$productTable->newTags = $kartItem->tags;
		}

		$productTable->bind((array) $kartItem);
		$productTable->check();

		if (!$productTable->store(true))
		{
			$this->setError($productTable->getError());

			return false;
		}

		$inserid = $productTable->get('item_id');

		if ($operation == 'insert')
		{
			if ($kartItem->parent == "com_quick2cart")
			{
				$q2cAttributesModel->copyItemidToProdid($inserid);
			}

			// Add point to Community extension when product added into Quick2cart
			$integrationWith = $params->get('integrate_with', 'none');
			$user           = Factory::getUser();

			if ($integrationWith != 'none')
			{
				$streamAddProd = $params->get('streamAddProd', 1);

				// According to integration create social lib class obj.
				$libclass      = $comquick2cartHelper->getQtcSocialLibObj();

				// Add in activity.
				if ($streamAddProd)
				{
					$prodLink    = '<a class="" href="' . $comquick2cartHelper->getProductLink($inserid, 'detailsLink', 1) . '">' . $kartItem->name . '</a>';
					$store_info  = $comquick2cartHelper->getSoreInfo($kartItem->store_id);
					$content     = Text::sprintf('QTC_ACTIVITY_ADD_PROD', $prodLink, $store_info['title']);
					$libclass->pushActivity($user->id, $act_type = '', $act_subtype = '', $content, $act_link = '', $title = '', $act_access = '');
				}

				// Add points
				$options['extension'] = 'com_quick2cart';

				if ($integrationWith == "EasySocial")
				{
					$options['command'] = 'add_product';
				}
				elseif ($integrationWith == "JomSocial")
				{
					$options['command'] = 'addproduct.points';
				}

				$libclass->addpoints($user, $options);
			}
		}

		return !empty($inserid) ? $inserid : "";
	}

	/**
	 * Function to get currency value
	 *
	 * @param   INT     $pid      product id
	 *
	 * @param   STRING  $curr     currency
	 *
	 * @param   STRING  $client   client
	 *
	 * @param   STRING  $item_id  item id
	 *
	 * @return  null
	 */
	public function getCurrenciesvalue($pid, $curr, $client, $item_id = '')
	{
		if (empty($item_id))
		{
			$quick2cartModelAttributes = new quick2cartModelAttributes;
			$item_id                   = $quick2cartModelAttributes->getitemid($pid, $client);
		}

		$db    = Factory::getDBO();
		$query = "SELECT * FROM #__kart_base_currency  WHERE item_id = " . (int) $item_id . " AND currency='" . $curr . "'";
		$db->setQuery($query);
		$result = $db->loadAssocList();

		return $result;
	}

	/**
	 * Function to get item id
	 *
	 * @param   INT     $product_id  product id
	 *
	 * @param   STRING  $client      client
	 *
	 * @param   STRING  $sku         product sku
	 *
	 * @return  Integer  $result  Product id
	 */
	public function getitemid($product_id = 0, $client = '', $sku = "")
	{
		$db = Factory::getDBO();

		if (!empty($product_id))
		{
			$query = "SELECT `item_id` FROM `#__kart_items`  where `product_id`=" . (int) $product_id . " AND parent='$client'";
		}
		else
		{
			$query = "SELECT `item_id` FROM `#__kart_items`  where parent='" . $client . "' AND sku=\"" . $sku . "\"";
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * This item value
	 *
	 * @param   INT     $pid     product id
	 *
	 * @param   STRING  $client  client
	 *
	 * @return  null
	 */
	public function getItemvalue($pid, $client)
	{
		$db    = Factory::getDBO();
		$query = "SELECT `name` FROM `#__kart_items`  where `product_id`=" . (int) $pid . " AND parent='$client'";
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * This function gets option currency value
	 *
	 * @param   INT     $iap_id  option attribute id
	 * @param   STRING  $curr    currency
	 *
	 * @return  null
	 */
	public function getOption_currencyValue($iap_id, $curr)
	{
		$db    = Factory::getDBO();
		$query = "SELECT * FROM #__kart_option_currency WHERE `itemattributeoption_id`=" . (int) $iap_id . " AND currency='" . $curr . "'";
		$db->setQuery($query);
		$result = $db->loadAssocList();

		return $result;
	}

	/**
	 * This function to validate attribute options
	 *
	 * @param   ARRAY  $options  options
	 * @param   ARRAY  $index    index
	 *
	 * @return  null
	 */
	public function validateAttributeOption($options, $index = 0)
	{
		if ($options[$index]['name'] && $options[$index]['prefix'] && $options[$index]['order'])
		{
			// Of currency text count
			$noofcurr        = count($options[$index]['currency']);
			$filledcurr      = array_filter($options[$index]['currency'], 'strlen');

			// Count after removing empty fields
			$filledcurrCount = count($filledcurr);

			if ($filledcurrCount == $noofcurr)
			{
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * This function delete attributeOptions
	 *
	 * @param   Object  $data  data
	 *
	 * @return  Object|Boolean  $data
	 */
	public function deleteAttributeOption($data)
	{
		$delete_attri = $data->get('delete_attri');
		$attri_id     = $data->get('attri_id');

		if (!empty($delete_attri) && !empty($attri_id))
		{
			$del_ids       = explode(',', trim($data->get('delete_attri', '', 'RAW')));

			// Remove only null/ empty element (keep zero)
			$del_ids_array = array_filter($del_ids, 'strlen');
			$del_ids       = implode(',', $del_ids_array);
			$db            = Factory::getDBO();

			// Step 1. Delete attribute Option
			$query = "DELETE FROM `#__kart_itemattributeoptions` where `itemattribute_id`="
			. $data->get('attri_id') . " AND  `itemattributeoption_id` IN (" . $del_ids . ")";
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

			// Step 2: after successful deletion :: Remove deleted option from data array
			$attri_opt = $data->get('attri_opt', array(), 'ARRAY');

			foreach ($attri_opt as $key => $option)
			{
				if (in_array($option['id'], $del_ids_array))
				{
					unset($attri_opt[$key]);
				}
			}
		}

		return $data;
	}

	/**
	 * This function delete attribute if doesn't have options
	 *
	 * @param   INT  $att_id  attribute id
	 *
	 * @return  null
	 *
	 * @since	2.5
	 */
	public function noOptionThenDelAttr($att_id)
	{
		if (!empty($att_id))
		{
			$db    = Factory::getDBO();
			$query = "Select count(*) from `#__kart_itemattributeoptions` where itemattribute_id=" . $att_id;
			$db->setQuery($query);
			$count = $db->loadResult();

			// Delete attribute
			if ($count == 0)
			{
				$query = " delete from `#__kart_itemattributes` where itemattribute_id=" . $att_id;
				$db->setQuery($query);

				if (!$db->execute())
				{
					return $db->getErrorMsg();
				}
			}
		}
	}

	/**
	 * This function removes invalid options
	 *
	 * @param   ARRAY  $data  option data
	 *
	 * @return  ARRAY
	 *
	 * @since	2.5
	 */
	public function removeInvalideOption($data)
	{
		foreach ($data['attri_opt'] as $key => $options)
		{
			$status = $this->validateAttributeOption($data['attri_opt'], $key);

			// Remove option from data
			if ($status == false)
			{
				unset($options[$key]);
			}
		}

		return $data;
	}

	/**
	 * This function return product details
	 *
	 * @param   INT     $product_id  product id
	 * @param   STRING  $client      client id
	 * @param   INT     $item_id     item id
	 *
	 * @return  ARRAY
	 *
	 * @since	2.5
	 */
	public function getItemDetail($product_id = 0, $client = '', $item_id = "")
	{
		$db      = Factory::getDBO();
		$colList = " `item_id`,`parent`,`product_id`,`store_id`,`name`,`stock`,`min_quantity`,`max_quantity`,`category`,`sku`,`images`,`description`,`video_link`,`state`,`featured`,`params`, `cdate`, `mdate` ";
		$colList = "*";

		if (!empty($item_id))
		{
			$query = 'SELECT ' . $colList . ' FROM `#__kart_items`  where `item_id`=' . (int) $item_id;
			$db->setQuery($query);
		}
		else
		{
			$query = 'SELECT ' . $colList . ' FROM `#__kart_items`  where `product_id`=' . (int) $product_id . " AND parent='$client'";
		}

		$db->setQuery($query);
		$result         = $db->loadAssoc();
		$result['tags'] = new TagsHelper;
		$result['tags']->getTagIds($result['item_id'], 'com_quick2cart.product');

		return $result;
	}

	/**
	 * This function copy itemid to parent id (For client= com_quick2cart)
	 *
	 * @param   INT  $item_id  item id
	 *
	 * @return  integer  item_id Item id of Q2C product
	 *
	 * @since	2.5
	 */
	public function copyItemidToProdid($item_id)
	{
		$db                    = Factory::getDBO();
		$kart_item             = new stdClass;
		$kart_item->product_id = $item_id;
		$kart_item->item_id    = $item_id;

		if (!$db->updateObject('#__kart_items', $kart_item, 'item_id'))
		{
			$message = Text::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
		}
	}

	/**
	 * This function create the child product
	 *
	 * @param   ARRAY  $data  data need to create child product
	 *
	 * @return  child product id
	 *
	 * @since	2.5
	 */
	public function createChildProd($data)
	{
		$parent_prod_id   = $data['item_id'];
		$attri_name       = $data['attri_name'];
		$attri_opt        = $data['attri_opt'];
		$optId            = $data['itemattributeoption_id'];
		$childProductName = $parent_prod_id . "-" . $attri_name . "-" . $attri_opt['name'];

		$comquick2cartHelper = new comquick2cartHelper;
		$path                = JPATH_SITE . '/components/com_quick2cart/models/cart.php';
		$Quick2cartModelcart = $comquick2cartHelper->loadqtcClass($path, "Quick2cartModelcart");
		$parentDetail        = $Quick2cartModelcart->getItemRec($parent_prod_id);

		$kart_item           = new stdClass;
		$kart_item->parent   = $parentDetail->parent;
		$kart_item->store_id = $parentDetail->store_id;
		$kart_item->display_in_product_catlog = 0;
		$kart_item->parent_id = $parent_prod_id;

		// Child product will be simple
		$kart_item->product_type = 1;
		$kart_item->name         = $childProductName;
		$kart_item->price        = 0;
		$kart_item->category     = $parentDetail->category;
		$kart_item->sku          = !empty($attri_opt['sku'])?$attri_opt['sku']:$childProductName . "-" . $optId;
		$kart_item->description  = '';
		$kart_item->video_link   = '';
		$kart_item->state        = 1;

		if ($attri_opt['stock'] !== "")
		{
			$kart_item->stock        = $attri_opt['stock'];
		}

		$kart_item->min_quantity = 1;
		$kart_item->max_quantity = 999;
		$operation = 'insertObject';

		if (!empty($attri_opt['child_product_item_id']))
		{
			$operation = 'updateObject';
			$kart_item->item_id = $attri_opt['child_product_item_id'];
		}

		$kart_item->mdate = Factory::getDate()->format('Y-m-d H:m:s');

		if ($operation == 'insertObject')
		{
			$kart_item->cdate = Factory::getDate()->format('Y-m-d H:m:s');
		}

		$db = Factory::getDbo();

		if (!$db->$operation('#__kart_items', $kart_item, 'item_id'))
		{
			$message = Text::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
		}
		else
		{
			if ($kart_item->parent == "com_quick2cart" && $operation == 'insertObject')
			{
				$this->copyItemidToProdid($kart_item->item_id);
			}
		}

		return $kart_item->item_id;
	}

	/**
	 * This function map the child product's item id to main products option id
	 *
	 * @param   integer  $itemAttrOptionId  Item attribute's option id
	 * @param   integer  $childItem_id      Item id of Q2C product
	 *
	 * @return  flag
	 *
	 * @since	2.5
	 */
	public function mapChildprodutToOption($itemAttrOptionId, $childItem_id)
	{
		$db                          = Factory::getDBO();
		$opt                         = new stdClass;
		$opt->itemattributeoption_id = $itemAttrOptionId;
		$opt->child_product_item_id  = $childItem_id;

		try
		{
			$db->updateObject('#__kart_itemattributeoptions', $opt, 'itemattributeoption_id');
		}
		catch (\RuntimeException $e)
		{
			echo $$e->setError($e->getMessage());

			return 0;
		}

		return 1;
	}

	/**
	 * Function to get Compulsory Attributes of the product
	 *
	 * @param   integer  $itemId  Item id of Q2C product
	 *
	 * @param   STRING   $type    attribute type
	 *
	 * @return  ARRAY  compulsory product attributes
	 *
	 * @since	2.9.18
	 */
	public function getCompulsoryAttributes($itemId, $type = '')
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('itemattribute_id'));
		$query->from($db->quoteName('#__kart_itemattributes'));
		$query->where($db->quoteName('item_id') . '=' . (int) $itemId);
		$query->where($db->quoteName('attribute_compulsary') . '=1');

		if (!empty($type))
		{
			$query->where($db->quoteName('attributeFieldType') . '=' . $db->quote($type));
		}

		$db->setQuery($query);

		return $db->loadColumn();
	}

	/**
	 * Function to get tags array
	 *
	 * @return  ARRAY  Tags
	 *
	 * @since	__DEPLOY_VERSION__
	 */
	public function getTags()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn(array('a.id', 'a.title', 'a.level', 'a.parent_id')))
			->from($db->qn('#__tags', 'a'))
			->where($db->qn('a.parent_id') .  '>' . (int) 0)
			->where($db->qn('a.published') . ' = ' . (int) 1)
			->order($db->qn('a.lft') . ' DESC');
		$db->setQuery($query);
		$tags = $db->loadAssocList();

		return $tags;
	}
	
	public function getTable($type = 'Product', $prefix = 'Quick2cartTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * This function delete all attribute if doesn't type is text
	 *
	 * @param   INT  $att_id  attribute id
	 *
	 * @return  null
	 *
	 * @since	2.5
	 */
	public function deleteAttributeAllOption($att_id)
	{
		if (!empty($att_id))
		{
			$db    = Factory::getDBO();

			$itemAttributeOptionsTable = $this->getTable('ItemAttributeOptions');
			$itemAttributeOptionsTable->load(['itemattribute_id' => $att_id], $db);
			$itemAttributeOptionsTable->delete();
		}
	}

	/**
	 * This function delete attributeOptions if we change Select to Text
	 *
	 * @param   Object  $attri_id  attri_id
	 *
	 * @return  Object|Boolean  $attri_id
	 */
	public function deleteAttributeOptionAfterSelectToTextUpdate($attri_id, $del_ids)
	{
		if (!empty($del_ids) && count($del_ids))
		{
			// Remove only null/ empty element (keep zero)
			$del_ids_array = array_filter($del_ids, 'strlen');
			$del_ids       = implode(',', $del_ids_array);
			$db            = Factory::getDBO();

			// Step 1. Delete attribute Option
			$query = $db->getQuery(true)
						->delete($db->qn('#__kart_itemattributeoptions'));
			
			$query->where($db->qn('itemattribute_id') . ' = ' . $attri_id);
			$query->where($db->qn('itemattributeoption_id') . 'IN (' . $del_ids . ')');
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}

		return $attri_id;
	}
}
