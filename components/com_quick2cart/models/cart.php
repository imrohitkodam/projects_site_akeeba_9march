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
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Quick2cartModelcart
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class Quick2cartModelcart extends BaseDatabaseModel
{
	// Instad of this use product helers funtion

	/**
	 * This function  give item attributes.
	 *
	 * @param   integer  $itemid  item_id
	 *
	 * @since   2.2.2
	 *
	 * @return   Object list.
	 */
	public function getAttributes($itemid)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from('#__kart_itemattributes');
		$query->where('item_id=' . $itemid);
		$query->order('`itemattribute_id` ASC');
		$db->setQuery($query);

		return $db->loadobjectList();
	}

	/**
	 * method to get item id
	 *
	 * @param   integer  $itemid            item id
	 *
	 * @param   integer  $return_disc       client id
	 *
	 * @param   integer  $getOriginalPrice  product sku
	 *
	 * @return  price of PRODUCT ACCORDING TO CURRENCY
	 *
	 * @since	2.5
	 */
	public function getPrice($itemid, $return_disc = 0, $getOriginalPrice = 0)
	{
		$db                  = Factory::getDBO();
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();
		/*	if(!$currency) //if set in session
		{
		$params = ComponentHelper::getParams('com_quick2cart');
		$currencies=$params->get('addcurrency');
		$currencies=explode(',',$currencies);
		$currkeys=array_keys($currencies); //make array of currency keys
		$currency=$currkeys[0];
		}*/

		// $Quick2cartModelcart =  new Quick2cartModelcart;
		$params = ComponentHelper::getParams('com_quick2cart');

		// $itemid =$Quick2cartModelcart->getitemid($pid,$client);
		$query = "SELECT ";

		if ($params->get('usedisc') && $return_disc == 1)
		{
			$query .= " discount_price , ";
		}
		elseif ($params->get('usedisc') && $getOriginalPrice == 0)
		{
			$query .= " CASE WHEN ( discount_price IS NOT NULL) THEN discount_price
				ELSE price
				END as ";
		}

		$query .= " price
		FROM #__kart_base_currency
		WHERE item_id = " . (int) $itemid . " AND currency='$currency'";
		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	/**
	 * method to get item id
	 *
	 * @param   integer  $product_id  item id
	 *
	 * @param   integer  $client      client id
	 *
	 * @param   integer  $sku         product sku
	 *
	 * @return  Object list.
	 *
	 * @since	2.5
	 */
	public function getitemid($product_id = 0, $client = '', $sku = '')
	{
		$db = Factory::getDBO();

		if (!empty($sku))
		{
			$query = "SELECT `item_id` FROM `#__kart_items`  where parent='" . $client . "' AND sku='" . $sku . "'";
		}
		else
		{
			$query = "SELECT `item_id` FROM `#__kart_items`  where `product_id`=" . (int) $product_id . " AND parent='$client'";
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * method to get item id and its stare
	 *
	 * @param   integer  $client      client id
	 *
	 * @param   integer  $product_id  item id
	 *
	 * @return  Object list.
	 *
	 * @since	2.5
	 */
	public function getitemidAndState($client, $product_id = 0)
	{
		$db    = Factory::getDBO();
		$query = "SELECT `item_id`,`state` FROM `#__kart_items`  where `product_id`=" . (int) $product_id . " AND parent='$client'";
		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

/*
	Output of this function will like
	Array
	(
	[0] => Array
	(
	[base_currency_id] => 16
	[item_id] => 16
	[price] => 35.00
	[name] => Steve Heels
	[count] => 1
	)

	[1] => Array
	(
	[23] => Array
	(
	[itemattributeoption_id] => 35
	[global_option_id] => 20
	[itemattribute_id] => 23
	[child_product_item_id] => 0
	[itemattributeoption_name] => 5
	[itemattributeoption_price] => 0.00
	[itemattributeoption_code] =>
	[itemattributeoption_prefix] => +
	[ordering] => 1
	[state] => 1
	[optioncurrency] => USD
	[optionprice] => 0.00
	)

	[132] => Array
	(
	[itemattributeoption_id] => 298
	[global_option_id] => 0
	[itemattribute_id] => 132
	[child_product_item_id] => 0
	[itemattributeoption_name] => Text to Pring
	[itemattributeoption_price] => 0.00
	[itemattributeoption_code] =>
	[itemattributeoption_prefix] => +
	[ordering] => 1
	[state] => 1
	[optioncurrency] => USD
	[optionprice] => 0.00
	)
	)

	[2] => Array
	(
	[0] => Array
	(
	[itemattribute_id] => 23
	[item_id] => 16
	[itemattribute_name] => Shoes and sandals size
	[ordering] => 0
	[attribute_compulsary] => 1
	[attributeFieldType] => Select
	[global_attribute_id] => 0
	[is_stock_keeping] => 0
	)

	[1] => Array
	(
	[itemattribute_id] => 132
	[item_id] => 16
	[itemattribute_name] => Text to Pring
	[ordering] => 0
	[attribute_compulsary] => 0
	[attributeFieldType] => Textbox
	[global_attribute_id] => 0
	[is_stock_keeping] => 0
	)
	)
	)
*/

	/**
	 * This method return product details from kart _items,_kart_itemattributes,_kart_itemattributeoptions
	 *
	 * @param   integer  $item_obj  item object
	 *
	 * @return  Object list.
	 *
	 * @since	2.5
	 */
	public function getProd($item_obj)
	{
		$db                  = Factory::getDbo();
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();
		$Quick2cartModelcart = new Quick2cartModelcart;

		if (empty($item_obj['item_id']))
		{
			$item_id = $Quick2cartModelcart->getitemid($item_obj["id"], $item_obj['parent']);
		}
		else
		{
			$item_id = $item_obj['item_id'];
		}

		$itemid_rec = $this->getItemRec($item_id);
		$params     = ComponentHelper::getParams('com_quick2cart');
		$query      = "SELECT kc.id as base_currency_id,kc.item_id,";

		if ($params->get('usedisc'))
		{
			$query .= " CASE WHEN ( kc.discount_price IS NOT NULL) THEN kc.discount_price
				ELSE kc.price
				END as price, ";
		}
		else
		{
			$query .= " kc.price, ";
		}

		$query .= " ki.name
		FROM #__kart_items AS ki
		LEFT JOIN  `#__kart_base_currency` AS kc
		ON  `ki`.`item_id` =  `kc`.`item_id`
		WHERE ki.`item_id` =" . $itemid_rec->item_id . "
		AND kc.currency = '" . $currency . "'";
		$this->_db->setQuery($query);

		try
		{
			$item_result = $this->_db->loadAssoc();
		}
		catch (Exception $e)
		{
			throw new Exception(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 500);
		}

		// $pid=(int) $item_obj["id"]; //product_id
		$item_result['price'] = $item_result['price'];

		// Input to this f()
		$item_result["count"] = $item_obj["count"];

		// Store item detail in array
		$final_result[] = $item_result;

		// IF product has attributes
		if ($item_obj['options'])
		{
			$query = $db->getQuery(true);
			$query->select("ko.*, currency as optioncurrency,price as optionprice")->from(
			'#__kart_itemattributeoptions as ko'
			)->join('LEFT', '#__kart_option_currency as kc ON  `ko`.`itemattributeoption_id`=`kc`.`itemattributeoption_id`')->where(
			" ko.itemattributeoption_id IN (" . $item_obj['options'] . ") AND currency='" . $currency . "'"
			);

			$db->setQuery($query);
			$options_result = $this->_db->loadAssocList('itemattribute_id');
			$item_options  = $options_result;

			foreach ($options_result as $options_result)
			{
				$item_options_arr[] = $options_result['itemattribute_id'];
			}

			$item_attri = '';

			if (!empty($item_options_arr))
			{
				$itemattribute_ids = implode(",", $item_options_arr);

				// Store option detail
				$final_result[] = $item_options;
				$query          = "SELECT * FROM #__kart_itemattributes WHERE itemattribute_id IN (" . $itemattribute_ids . ")";
				$this->_db->setQuery($query);
				$item_attri = $this->_db->loadAssocList();
			}

			// Store attribute detail
			$final_result[] = $item_attri;
		}

		return $final_result;
	}

	/**
	 * Functio to make cart
	 *
	 * @return null
	 */
	public function makeCart()
	{
		$user         = Factory::getUser();
		$row          = new stdClass;
		$row->user_id = ($user->id) ? $user->id : 0;

		// Return cart_id with current session
		$cart = $this->getCartId();

		// If cart exists EXCEPT LAST UPDATED ENTRY  del all entry against user_id
		if ($cart)
		{
			if ($user->id)
			{
				$query = "Select cart_id FROM #__kart_cart WHERE user_id='$user->id' ORDER BY last_updated DESC";
				$this->_db->setQuery($query);
				$cart_ids = $this->_db->loadColumn();

				if (!empty($cart_ids))
				{
					$cart = $cart_ids[0];
					unset($cart_ids[0]);

					if (!empty($cart_ids))
					{
						$cart_ids_str = implode(',', $cart_ids);
						$query        = "DELETE FROM #__kart_cart WHERE cart_id IN ($cart_ids_str)";
						$this->_db->setQuery($query);
						$this->_db->execute();

						$query = "DELETE FROM #__kart_cartitems WHERE cart_id IN ($cart_ids_str)";
						$this->_db->setQuery($query);
						$this->_db->execute();
					}
				}
			}

			$row->cart_id      = $cart;
			$row->last_updated = date("Y-m-d H:i:s");

			try
			{
				$this->_db->updateObject('#__kart_cart', $row, 'cart_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}
		else
		{
			/* if "not logged" in and "not cart entry" with oldsession(checked in getcartid ::called before if)*/
			$session         = Factory::getSession();
			$session_id      = $session->getId();
			$row->session_id = $session_id;

			try
			{
				$this->_db->insertObject('#__kart_cart', $row, 'cart_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

		}

		return $row->cart_id;
	}

	/**
	 * This method  set session/update session
	 * if user logged in and oldsession is set UPDATE cart record against session.
	 * ELSE IF user NOT logged in
	 * 			then set session
	 * Finally fetch cartid depending upon userid or session_id
	 *
	 * @return null
	 */
	public function getCartId()
	{
		// @TODO ask ashwin about this
		$db         = Factory::getDbo();
		$user       = Factory::getUser();
		$session    = Factory::getSession();
		$session_id = $session->getId();

		if ($user->id)
		{
			$where         = "user_id='$user->id'";
			$old_sessionid = $session->get('old_sessionid');

			if (!empty($old_sessionid))
			{
				// UpdateuserCart($old_sessionid);

				// @TODO ask ashwin about this
				$db = Factory::getDbo();

				// Quick2cartModelcart::delUser_idCartDetails($user->id);
				$row             = new stdClass;
				$row->session_id = $old_sessionid;
				$row->user_id    = $user->id;

				// Intval(JUserHelper::getUserId($user['username']));
				$row->last_updated = date("Y-m-d H:i:s");

				try
				{
					$db->updateObject('#__kart_cart', $row, 'session_id');
				}
				catch (\RuntimeException $e)
				{
					$this->setError($e->getMessage());

					return false;
				}
			}
		}
		else
		{
			$session->set('old_sessionid', $session_id);

			// Store the old session for after user login use
			$where = "session_id='$session_id'";
		}

		$query = "Select cart_id FROM #__kart_cart WHERE $where order by cart_id DESC";
		$db->setQuery($query);
		$cart_id = $db->loadResult();

		return $cart_id;
	}

	/**
	 * getOriginalPrice is set then product original price will be considered in calculation
	 *
	 * @param   INT  $getOriginalPrice  flag
	 *
	 * @return array
	 *
	 * @since  2.2
	 *
	 */
	public function getCartitems($getOriginalPrice = 0)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		// @TODO ask ashwin about this
		$db                  = Factory::getDbo();
		$Quick2cartModelcart = new Quick2cartModelcart;
		$cart_id             = $Quick2cartModelcart->getCartId();

		if (!isset($cart_id))
		{
			return;
		}

		$this->delNotValidProdFromCart($cart_id);
		$query = "Select k.cart_item_id as id, 	k.cart_id,k.store_id,i.sku,
					k.order_item_name as title,	k.user_info_id,
					k.cdate,						k.mdate,
					k.product_quantity as qty, 		k.product_attribute_names as options,
					k.item_id, i.category,k.product_item_price,k.product_attributes_price,k.product_final_price,k.original_price,k. params,currency,
					k.product_attributes, k.variant_item_id
		 FROM #__kart_cartitems as k,#__kart_items as i,#__kart_store as s
		 WHERE k.item_id=i.item_id and k.cart_id='$cart_id' and k.store_id=s.id AND i.state = 1 AND s.live = 1 order by k.`store_id`";
		$db->setQuery($query);
		$cart = $db->loadAssocList();

		foreach ($cart as $key => $rec)
		{
			$cart[$key]['seller_id'] = $rec['store_id'];

			// Task 1 fetch item price
			$pricearray  = "";
			$return_disc = 0;
			$prod_price  = $Quick2cartModelcart->getPrice($rec['item_id'], $return_disc, $getOriginalPrice);

			// Product_item_price as amt
			$cart[$key]['amt'] = $prod_price['price'];

			// Task 2 attribute price

			if (isset($rec['product_attributes']) && $rec['product_attributes'])
			{
				$totop_price = $Quick2cartModelcart->getCurrPriceFromBaseCurrencyOption($rec['product_attributes']);

				// Product_attributes_price as opt_amt
				$cart[$key]['opt_amt'] = $totop_price;
			}
			else
			{
				// Product_attributes_price as opt_amt
				$cart[$key]['opt_amt'] = 0;
			}

			// Task 3 calculate total m price
			// Product_final_price as tamt
			$cart[$key]['tamt'] = ((float) $cart[$key]['amt'] + (float) $cart[$key]['opt_amt']) * (float) $cart[$key]['qty'];

			// Synchronize new item price and its related in cart_item table
			$cart_item                           = new stdClass;
			$cart_item->cart_item_id             = $cart[$key]['id'];
			$cart_item->product_quantity         = abs($cart[$key]['qty']);
			$cart_item->product_item_price       = $cart[$key]['amt'];
			$cart_item->product_attributes_price = $cart[$key]['opt_amt'];

			if (!empty($rec['params']))
			{
				$params = json_decode($rec['params'], true);
				JLoader::import('cartcheckout', JPATH_SITE . '/components/com_quick2cart/models');
				$Quick2cartModelcartcheckout = new Quick2cartModelcartcheckout;

				if (!empty($params['coupon_code']))
				{
					$coupon = $Quick2cartModelcartcheckout->getcoupon($params['coupon_code']);
					$coupon = $coupon ? $coupon : array();

					// If user entered code is matched with dDb coupon code
					if (isset($coupon) && $coupon)
					{
						if (in_array($cart[$key]['item_id'], $coupon[0]->item_id))
						{
							$camt                         = -1;
							$cart[$key]['original_price'] = $cart[$key]['tamt'];
							$cart_item->original_price    = $cart[$key]['original_price'];
							$coupon[0]->cop_code          = $params['coupon_code'];

							if ($coupon[0]->val_type == 1)
							{
								$cval = ($coupon[0]->value / 100) * $cart[$key]['tamt'];
							}
							else
							{
								$cval = $coupon[0]->value;
								$cval = $cval * $cart[$key]['qty'];
								/*multiply cop disc with qty*/
							}

							$camt = $cart[$key]['tamt'] - $cval;

							if ($camt <= 0)
							{
								$camt = 0;
							}

							$cart[$key]['tamt'] = (!($camt == -1)) ? $camt : $cart[$key]['tamt'];
						}
					}
				}
			}

			$cart_item->product_final_price = $cart[$key]['tamt'];
			$cart_item->currency            = $comquick2cartHelper->getCurrencySession();
			$db                             = Factory::getDBO();

			if ($cart_item->product_item_price != $cart[$key]['product_item_price']
				|| $cart_item->product_attributes_price != $cart[$key]['product_attributes_price']
				|| $cart_item->product_final_price != $cart[$key]['product_final_price']
				|| $cart_item->currency != $cart[$key]['currency'])
			{
				if (!$db->updateObject('#__kart_cartitems', $cart_item, "cart_item_id"))
				{
					echo $this->_db->stderr();

					return -1;
				}
				else
				{
					// Update price in fetched array according to current currency
					$cart[$key]['product_item_price']       = $cart_item->product_item_price;
					$cart[$key]['product_attributes_price'] = $cart_item->product_attributes_price;
					$cart[$key]['product_final_price']      = $cart_item->product_final_price;
					$cart[$key]['currency']                 = $cart_item->currency;
				}
			}
		}

		return $cart;
	}

	// End of getCartitems()

	/**
	 * This function remove whole cart
	 *
	 * @return null
	 *
	 * @since	2.5
	 */
	public function empty_cart()
	{
		$c_id  = $this->getCartId();
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('cart_item_id'));
		$query->from($db->quoteName('#__kart_cartitems'));
		$query->where($db->quoteName('cart_id') . '=' . (int) $c_id);
		$db->setQuery($query);
		$items_id = $db->loadColumn();

		foreach ($items_id as $item_id)
		{
			$query      = $db->getQuery(true);
			$conditions = array($db->quoteName('cart_item_id') . '=' . (int) $item_id);
			$query->delete($db->quoteName('#__kart_cartitemattributes'));
			$query->where($conditions);
			$db->setQuery($query);
			$db->execute();
		}

		$query      = $db->getQuery(true);
		$conditions = array($db->quoteName('cart_id') . '=' . (int) $c_id);
		$query->delete($db->quoteName('#__kart_cartitems'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * This function delete the items from cart which are not valid (deleted or unpublished)
	 *
	 * @param   integer  $cart_id  cart_id
	 *
	 * @return  Object list.
	 *
	 * @since	2.5
	 */
	public function delNotValidProdFromCart($cart_id)
	{
		// Fetch unpublished items
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select("c.cart_item_id")->from(
		'#__kart_cartitems AS c')->join(
		'LEFT', "#__kart_items as i ON c.item_id = i.item_id")->where(
		" c.cart_id = " . $cart_id
		)->where("i.state=" . 0);
		$db->setQuery($query);
		$citems = $db->loadColumn();

		if (!empty($citems))
		{
			foreach ($citems as $cart_item_id)
			{
				$this->remove_cartItem($cart_item_id);
			}
		}
	}

	/**
	 * This function remove the cart item
	 *
	 * @param   integer  $c_item_id  c_item_id
	 *
	 * @param   integer  $item_id    item id(index of #__kart_cartitems table)..
	 *
	 * @return  Object list.
	 *
	 * @since	2.5
	 */
	public function remove_cartItem($c_item_id = 0, $item_id = 0)
	{
		$app       = Factory::getApplication();
		$db        = Factory::getDbo();
		$c_item_id = (int) $c_item_id;
		$item_id   = (int) $item_id;

		if (empty($c_item_id) && empty($item_id))
		{
			return;
		}

		if (empty($c_item_id) && $item_id)
		{
			// Get cart id across all carts which as current item
			try
			{
				$query = $db->getQuery(true);
				$query->select($db->quoteName("ci.cart_item_id"));
				$query->from($db->quoteName("#__kart_cartitems", "ci"));
				$query->where($db->quoteName("ci.item_id") . " = " . $db->quote($item_id));
				$db->setQuery($query);

				$cart_item_idArray = $db->loadColumn();

				if (empty($cart_item_idArray))
				{
					return 0;
				}
			}
			catch (Exception $e)
			{
				echo $e->getMessage();

				return 0;
			}
		}
		else
		{
			$cart_item_idArray[] = $c_item_id;
		}

		// Get cart item id
		try
		{
			foreach ($cart_item_idArray as $cart_item_id)
			{
				$allowedToRemoCartItem = $this->allowedToRemoveCartItem($cart_item_id);

				if ($allowedToRemoCartItem)
				{
					// GET CART ITEMS
					$query = $db->getQuery(true);
					$query->select("*");
					$query->from($db->quoteName('#__kart_cartitems'));
					$query->where($db->quoteName("cart_item_id") . " = " . $db->quote($cart_item_id));
					$db->setQuery($query);
					$cart['cartitem'] = $db->loadAssocList();

					// GET CART ITEMS attributes
					$query = $db->getQuery(true);
					$query->select("*");
					$query->from($db->quoteName('#__kart_cartitemattributes'));
					$query->where($db->quoteName("cart_item_id") . " = " . $db->quote($cart_item_id));
					$db->setQuery($query);
					$cart['cartitemattributes'] = $db->loadAssocList();

					// Delete cart item attributes
					$query      = $db->getQuery(true);
					$conditions = array($db->quoteName('cart_item_id') . ' = ' . $db->quote($cart_item_id));
					$query->delete($db->quoteName('#__kart_cartitemattributes'));
					$query->where($conditions);
					$db->setQuery($query);
					$db->execute();

					// Delete cart item
					$query      = $db->getQuery(true);
					$conditions = array($db->quoteName('cart_item_id') . ' = ' . $db->quote($cart_item_id));
					$query->delete($db->quoteName('#__kart_cartitems'));
					$query->where($conditions);
					$db->setQuery($query);
					$db->execute();

					// START Q2C Sample development
					PluginHelper::importPlugin('system');
					$app->triggerEvent('onAfterQ2cProductRemoveFromCart', array($cart));

					// Depricated
					$app->triggerEvent('onAfterQ2cRemoveFromCart', array($cart));
					// END Q2C Sample development
				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();

			return 0;
		}
	}

	/**
	 * Update Product Attribute & Quantity
	 *
	 * @param   ARRAY  $item_ids  item ids.
	 * @param   INT    $item_qty  product store id
	 *
	 * @since   2.2.2
	 *
	 * @return   boolean true or false.
	 */
	public function update_cart($item_ids, $item_qty)
	{
		$db   = Factory::getDBO();
		$user = Factory::getUser();

		foreach ($item_ids as $k => $cartitem)
		{
			$query = "Select product_item_price, product_attributes_price FROM #__kart_cartitems WHERE cart_item_id='$cartitem' ";
			$db->setQuery($query);
			list($product_item_price, $product_attributes_price) = $this->_db->loadRow();

			// $product_item_price = $product_item_price + $product_attributes_price;
			$items                   = new stdClass;
			$items->cart_item_id     = $cartitem;
			$items->product_quantity = abs($item_qty[$k]);

			try
			{
				$db->updateObject('#__kart_cartitems', $items, "cart_item_id");
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}

		return true;
	}

	/**
	 * This method check whether product is from same store or not.
	 *
	 * @param   integer  $cartId           cart id.
	 * @param   integer  $currItemStoreId  product store id
	 *
	 * @since   2.2.2
	 *
	 * @return   boolean true or false.
	 */
	public function isProdFromSameStore($cartId, $currItemStoreId)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('k.store_id');
		$query->from('#__kart_cartitems as k,#__kart_items as i');
		$query->where('k.item_id=i.item_id and k.cart_id=' . $cartId);
		$db->setQuery($query);
		$storelist = $db->loadColumn();

		// Cart doesn't have items.
		if (empty($storelist))
		{
			return true;
		}

		// If from same store
		if (in_array($currItemStoreId, $storelist))
		{
			return true;
		}

		// Add msg
		Factory::getApplication()->enqueueMessage(Text::_('COM_QUICK2CART_SINGLE_STORE_CKOUT_ALLOWED'));

		return false;
	}

	/**
	 * This method check whether user bought the product or not.
	 *
	 * @param   integer  $c_id          cart id.
	 * @param   array    $cartitem      items detail 0=> array of basic info,1=>selection option info. 2=> attribute info.
	 * @param   integer  $cart_item_id  cart item id while updating the cart from checkout view.
	 *
	 * @since   2.2.2
	 *
	 * @return   boolean true or false.
	 */
	public function putCartitem($c_id, $cartitem, $cart_item_id = '')
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$productHelper       = new productHelper;
		$params              = ComponentHelper::getParams('com_quick2cart');
		$item_details        = $cartitem[0];
		$chkLotMinMax		 = $params->get('minmax_quantity', 0, 'Integer');

		// Option details size->big,medium
		$item_options = (isset($cartitem[1])) ? $cartitem[1] : null;

		// Attribute details
		$item_attris = (isset($cartitem[2])) ? $cartitem[2] : null;

		if (!$c_id)
		{
			$c_id = $this->getCartId();
		}

		// Get current item store id.
		$currItemStoreId  = $comquick2cartHelper->getSoreID($item_details['item_id']);
		$singleStoreCkout = $params->get('singleStoreCkout', 0);

		if ($singleStoreCkout)
		{
			$status = $this->isProdFromSameStore($c_id, $currItemStoreId);

			// If not from same store.
			if (!$status)
			{
				return 2;
			}
		}

		$timestamp = date("Y-m-d H:i:s");
		$opt_price = 0;
		$opt       = $opt_ids = '';
		$matchAttr = 1;

		// Product has attributes.
		if ($item_attris)
		{
			foreach ($item_attris as $item_attri)
			{
				$item_option = $item_options[$item_attri['itemattribute_id']];
				$opt .= $item_attri['itemattribute_name'] . ": " . $item_option['itemattributeoption_name'] . ",";
				$opt_ids .= $item_option['itemattributeoption_id'] . ",";

				if ($item_option['itemattributeoption_prefix'] == '+')
				{
					$opt_price += $item_option['optionprice'];
				}
				elseif ($item_option['itemattributeoption_prefix'] == '-')
				{
					$opt_price -= $item_option['optionprice'];
				}
			}
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select("cart_item_id,item_id,product_quantity");
		$query->from("#__kart_cartitems");

		$conditions = array(
			$db->quoteName('cart_id') . ' =' . (int) $c_id,
			$db->quoteName('item_id') . ' =' . (int) $item_details['item_id'],
			$db->quoteName('product_attributes') . ' =' . '"' . $opt_ids . '"',
			$db->quoteName('product_attribute_names') . ' =' . $db->quote($opt)
		);

		$query->where($conditions);
		$db->setQuery($query);
		$cart = $db->loadAssoc();

		$final_price                     = $item_details['price'] + $opt_price;
		$items                           = new stdClass;
		$items->product_final_price      = $item_details['count'] * $final_price;
		$items->product_item_price       = $item_details['price'];
		$items->product_attributes       = $opt_ids;
		$items->product_attribute_names  = $opt;
		$items->product_attributes_price = $opt_price;
		$items->mdate                    = $timestamp;
		$items->currency                 = $comquick2cartHelper->getCurrencySession();
		$items->store_id                 = $comquick2cartHelper->getSoreID($item_details['item_id']);
		$items->original_price           = (array_key_exists("original_price",$item_details)) ? $item_details['original_price'] : 0.00;

		// Get min and max field details
		$dbItemDetail     = $this->getItemRec($item_details['item_id']);
		$checForMainStock = 1;

		// Get stock according to current selected varient.
		if (!empty($item_attris) && !empty($item_options))
		{
			$formattedAttriDetails = $this->getFormatedAttrData($item_attris, $item_options);

			//  Get Stock according to STOCKABLE attribute's option
			$childProdDetail = $productHelper->getAttriBasedStock($formattedAttriDetails);

			if (!empty($childProdDetail) && !empty($childProdDetail['child_product_item_id']))
			{
				$dbItemDetail->stock    = $childProdDetail['stock'];
				$items->variant_item_id = $childProdDetail['child_product_item_id'];
				$checForMainStock       = 0;
			}
		}

		/*For attribut based stock-> child product
		@TODO if (check for child product stock and show appropriate msg)
		If #__kart_cartitems contain entry for item and also check  present  same && all attr are same*/
		if ($cart['cart_item_id'] && $cart['item_id'] == $item_details['item_id'])
		{
			$final_qty = 0;

			// Just clicking on refresh button while updating cart from checkout view
			if ($cart_item_id && $cart['cart_item_id'] == $cart_item_id)
			{
				$final_qty = $item_details['count'];
			}
			else
			{
				$final_qty = $cart['product_quantity'] + $item_details['count'];
			}

			$dbItemDetail->slab = !empty($dbItemDetail->slab) ? $dbItemDetail->slab : 1;
			$canAddToCart       = $productHelper->isInStockProduct($dbItemDetail, $final_qty);

			if ($canAddToCart == 0)
			{
				if ($params->get('usestock', '', 'int') && !$params->get('outofstock_allowship', '', 'int'))
				{
					$buyQty = min($dbItemDetail->max_quantity, $dbItemDetail->stock);
				}
				else 
				{
					$buyQty = $dbItemDetail->max_quantity;
				}

				if (empty($buyQty))
				{
					return Text::sprintf('COM_QUICK2CART_STOCK_IS_NOT_AVAILABLE');
				}

				if ($chkLotMinMax)
				{
					if ($final_qty < $dbItemDetail->min_quantity)
					{
						return Text::sprintf('COM_QUICK2CART_MIN_ERROR_SUD_BE_INRANGE', $dbItemDetail->min_quantity);
					}
					elseif (($final_qty > $dbItemDetail->max_quantity) && ($final_qty < $dbItemDetail->stock))
					{
						return Text::sprintf('COM_QUICK2CART_MIN_MAX_ERROR_SUD_BE_INRANGE', $dbItemDetail->max_quantity);
					}
					elseif (($final_qty > $dbItemDetail->max_quantity) && ($final_qty > $dbItemDetail->stock))
					{
						return Text::sprintf('COM_QUICK2CART_STOCK_IS_NOT_AVAILABLE', $buyQty);
					}
					elseif ($final_qty > $buyQty)
					{
						return Text::sprintf('COM_QUICK2CART_STOCK_IS_NOT_AVAILABLE', $buyQty);
					}
				}
			}

			$slabquantity = $item_details['count'] % $dbItemDetail->slab;

			if ($slabquantity != 0 && $chkLotMinMax == 1)
			{
				return Text::sprintf('COM_QUICK2CART_QUANTITY_SHOULD_BE_MULI_OF_SLAB', $dbItemDetail->slab);
			}

			$items->product_quantity    = abs($final_qty);
			$items->cart_item_id        = $cart['cart_item_id'];
			$items->product_final_price = $items->product_quantity * $final_price;

			try
			{
				$this->_db->updateObject('#__kart_cartitems', $items, "cart_item_id");
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return -1;
			}

			// If updating from the checkout view then delete current cart entry.
			// If just updated cart entry and update cart entry is same then don't delete the entry. (in case of qty update)
			if (!empty($cart_item_id) && $cart_item_id != $items->cart_item_id)
			{
				$this->remove_cartItem($cart_item_id);
			}
		}
		else
		{
			$final_qty = $item_details['count'];

			// Main stock checking then check  already present item's stock. This condition needed if product with different attribute are adding
			if ($checForMainStock == 1)
			{
				// Get no of products in cart with same item_id
				$item_idCount = $productHelper->getCartItemQuantity($c_id, $item_details['item_id']);

				$final_qty += $item_idCount;
			}

			// Else add update while updating the cart from checkout page-> refresh button
			$canAddToCart = $productHelper->isInStockProduct($dbItemDetail, $final_qty);

			if ($canAddToCart == 0)
			{
				$buyQty = min($dbItemDetail->max_quantity, $dbItemDetail->stock);

				if (empty($buyQty))
				{
					return Text::sprintf('COM_QUICK2CART_STOCK_IS_NOT_AVAILABLE');
				}
				else
				{
					return Text::sprintf('COM_QUICK2CART_MIN_MAX_ERROR_SUD_BE_INRANGE', $buyQty);
				}
			}

			$dbItemDetail->slab = !empty($dbItemDetail->slab) ? $dbItemDetail->slab : 1;
			$slabquantity       = $item_details['count'] % $dbItemDetail->slab;

			if ($slabquantity != 0)
			{
				return Text::sprintf('COM_QUICK2CART_QUANTITY_SHOULD_BE_MULI_OF_SLAB', $dbItemDetail->slab);
			}

			$items->cart_id          = $c_id;
			$items->item_id          = $item_details['item_id'];
			$items->product_quantity = abs($item_details['count']);
			$items->order_item_name  = $item_details['name'];
			$items->cdate            = $timestamp;
			$dbAction                = 'insertObject';
			$action                  = 'insert';

			if (!empty($cart_item_id))
			{
				// Primary key = cart_item_id
				$items->cart_item_id = $cart_item_id;
				$dbAction            = 'updateObject';
				$action              = 'update';
			}

			try
			{
				$this->_db->$dbAction('#__kart_cartitems', $items, 'cart_item_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return -1;
			}

			$insertOrUpdateRowId = $items->cart_item_id;

			if ($item_options)
			{
				$this->addEntryInCartItemAttributes($item_options, $insertOrUpdateRowId, $action);
			}
		}

		return (ARRAY) $items;
	}

	/**
	 * This function add or update cart attribute entry.
	 *
	 * @param   array   $item_options  attributes detail array
	 * @param   string  $cart_item_id  cart item id (also used while updating attribute from cartcheckout view.)
	 * @param   string  $action        action to be performed
	 *
	 * @return  html.
	 *
	 * @since   1.6
	 */
	public function addEntryInCartItemAttributes($item_options, $cart_item_id, $action = 'insert')
	{
		$db        = Factory::getDBO();
		$dbAttOpts = array();

		// If update
		if ($action == 'update')
		{
			// Get Existing cartitemattribute_id (primary key) and option id
			$query = $db->getQuery(true)->select('cartitemattribute_id,itemattributeoption_id')->from('#__kart_cartitemattributes AS cia');
			$query->where('cia.cart_item_id=' . $cart_item_id);
			$db->setQuery($query);
			$dbAttOpts = $db->loadObjectList('itemattributeoption_id');
		}

		foreach ($item_options as $item_option)
		{
			$items_opt = new stdClass;
			$dbAction  = 'insertObject';

			// Update cart action.
			if ($action == 'update')
			{
				$key = $item_option['itemattributeoption_id'];

				// Check whether option already present in fetched attr option list
				if (isset($dbAttOpts[$key]))
				{
					$dbAction = 'updateObject';

					// For update, set primary key(cartitemattribute_id) of table
					$items_opt->cartitemattribute_id = $dbAttOpts[$key]->cartitemattribute_id;
					unset($dbAttOpts[$key]);
				}
			}

			$items_opt->cart_item_id             = $cart_item_id;
			$items_opt->itemattributeoption_id   = $item_option['itemattributeoption_id'];
			$items_opt->cartitemattribute_name   = $item_option['itemattributeoption_name'];
			$items_opt->cartitemattribute_price  = $item_option['optionprice'];
			$items_opt->cartitemattribute_prefix = $item_option['itemattributeoption_prefix'];

			try
			{
				$db->$dbAction('#__kart_cartitemattributes', $items_opt, 'cartitemattribute_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return -1;
			}
		}

		// Delete all older remaining/changed cart attribute option entry
		if (!empty($dbAttOpts))
		{
			foreach ($dbAttOpts as $attopt)
			{
				if (!empty($attopt->cartitemattribute_id))
				{
					try
					{
						$query = $db->getQuery(true)->delete('#__kart_cartitemattributes')->where('cartitemattribute_id =' . $attopt->cartitemattribute_id);
						$db->setQuery($query);
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						throw new Exception($this->_db->getErrorMsg());
					}
				}
			}
		}
	}

	/**
	 * Function to sync currencys
	 *
	 * @param   INT  $oldcurr  old currency
	 *
	 * @param   INT  $newcurr  new currency
	 *
	 * @return  array
	 */
	public function syncCartCurrency($oldcurr, $newcurr)
	{
		// @TODO MODIFICATION NEEDED
		$params  = ComponentHelper::getParams('com_quick2cart');
		$db      = Factory::getDBO();
		$cart_id = $this->getCartId();

		$query   = "select * from #__kart_cartitems where cart_id=" . (int) $cart_id;
		$db->setQuery($query);
		$result = $db->loadAssocList();

		foreach ($result as $key => $rec)
		{
			$pid   = $rec['product_id'];
			$qnt   = $rec['product_quantity'];
			$query = "SELECT ";

			if ($params->get('usedisc'))
			{
				$query .= " CASE WHEN (discount_price IS NOT NULL ) THEN discount_price
						ELSE price
						END as ";
			}

			$query .= " price
				FROM #__kart_base_currency WHERE item_id = " . (int) $pid . " AND currency='$newcurr'";
			$db->setQuery($query);
			$price                      = $db->loadAssocList();
			$newprice                   = $price[0]['price'];
			$items                      = new stdClass;
			$items->cart_item_id        = $rec['cart_item_id'];
			$items->cart_id             = $cart_id;
			$items->product_item_price  = $newprice;
			$items->product_final_price = (int) $qnt * (float) $newprice;

			try
			{
				$db->updateObject('#__kart_cartitems', $items, 'cart_item_id');
			}
			catch (\RuntimeException $e)
			{
				$this->setError('Error in updating kart_option_currency');

				return false;
			}
		}

		return true;
	}

	/**
	 * Accept product and return itemid rec contain  item_id, parent, name,product_id
	 *
	 * @param   INT  $item_id  itemid from kart_items table
	 *
	 * @return  Object
	 */
	public function getItemRec($item_id)
	{
		try
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("*")->from('#__kart_items')->where(" item_id = " . $item_id);
			$db->setQuery($query);
			$itemObj = $db->loadObject();

			if ($itemObj)
			{
				$itemObj->tags = new TagsHelper;
				$itemObj->tags->getTagIds($itemObj->item_id, 'com_quick2cart.product');

				return $itemObj;
			}

			return new stdClass;
		}
		catch (Exception $e)
		{
			echo $e->getMessage();

			return new stdClass;
		}
	}

	/**
	 * This function Accept itemattributeoption_id and fetch its price according currencey
	 *
	 * @param   INT  $itemattributeoption_ids  option id
	 *
	 * @return  int total price of attribute option
	 */
	public function getCurrPriceFromBaseCurrencyOption($itemattributeoption_ids)
	{
		$comquick2cartHelper     = new comquick2cartHelper;
		$db                      = Factory::getDBO();
		$itemattributeoption_ids = trim($itemattributeoption_ids);
		$arr                     = explode(",", $itemattributeoption_ids);
		$newarray                = array_filter($arr, "trim");
		$idstr                   = implode(",", $newarray);

		if ($idstr)
		{
			$currency = $comquick2cartHelper->getCurrencySession();
			$q        = "SELECT oc.price, attop.itemattributeoption_prefix
			FROM  `#__kart_option_currency` AS oc
			LEFT JOIN  `#__kart_itemattributeoptions` AS attop ON oc.itemattributeoption_id = attop.itemattributeoption_id
			WHERE attop.itemattributeoption_id IN (" . $idstr . ") AND currency='" . $currency . "'";
			$db->setQuery($q);
			$result         = $db->loadAssocList();
			$totoptionprice = 0;

			foreach ($result as $key => $attrib)
			{
				$price_with_sign = (float) ($attrib['itemattributeoption_prefix'] . $attrib['price']);
				$totoptionprice  = (float) $totoptionprice + $price_with_sign;
			}
		}

		return $totoptionprice;
	}

	/**
	 * This compare coma seperated old and new attributeoption
	 *
	 * @param   INT  $attr    arrtibute info
	 *
	 * @param   INT  $Dbattr  arrtibute info
	 *
	 * @return  status
	 */
	public function isAttributesSame($attr, $Dbattr)
	{
		$list = explode(",", $Dbattr);
		$attr = explode(",", $attr);

		for ($i = 0; $i < count($attr) - 1; $i++)
		{
			// Not found then dont chek for next
			if (!in_array($attr[$i], $list))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * This return stock detail which is used before displaying buy button
	 *
	 * @param   INT  $parent  parent id
	 *
	 * @param   INT  $pid     product id
	 *
	 * @return  stock limit
	 */
	public function stockStatus($parent, $pid)
	{
		$db    = Factory::getDBO();
		$query = "SELECT `stock` FROM `#__kart_items`  where `product_id`=" . (int) $pid . " AND parent='$parent'";
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Function to get stock limit
	 *
	 * @param   INT  $pid     product id
	 *
	 * @param   INT  $parent  parent id
	 *
	 * @param   INT  $limit   limit
	 *
	 * @return  stock limit
	 */
	public function getStockLimit($pid, $parent, $limit)
	{
		$limit  = trim($limit);
		$parent = trim($parent);
		$pid    = trim($pid);
		$column = ($limit == 'max') ? 'max_quantity' : 'min_quantity';

		if (!empty($column))
		{
			$db    = Factory::getDBO();
			$query = "SELECT `$column` FROM `#__kart_items`  where `product_id`=" . (int) $pid . " AND parent='$parent'";
			$db->setQuery($query);
			$result = $db->loadResult();

			return $result;
		}

		return false;
	}

	/**
	 * This function provides order prefix
	 *
	 * @param   iterable|object  $attoptionIds  option id
	 *
	 * @return  array of prefixes
	 */
	public function getPrefix($attoptionIds)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		// GetFromattedPrice($price,$curr=NULL)
		$prefixarray = array();
		$db          = Factory::getDBO();

		foreach ($attoptionIds as $key => $attid)
		{
			$query = "Select i.`itemattributeoption_prefix` as prefix,op.`price` FROM `#__kart_itemattributeoptions`  as i
						LEFT JOIN `#__kart_option_currency` AS op
						 ON i.`itemattributeoption_id`=op.`itemattributeoption_id`
						WHERE i.`itemattributeoption_id`=" . $attid . " AND op.`currency`='" . $currency . "'";
			$db->setQuery($query);
			$option_detail = $db->loadObject();

			if (empty($option_detail))
			{
				$option_detail         = new stdClass;
				$option_detail->price  = 0;
				$option_detail->prefix = '+';
			}

			$price             = $comquick2cartHelper->getFromattedPrice($option_detail->price);
			$prefixarray[$key] = $option_detail->prefix . " " . $price;
		}

		return $prefixarray;
	}

	/**
	 * This  provides all info about item attributes ( id,att_name,options details, price
	 *
	 * @param   INT  $item_id  item id
	 *
	 * @return  STRING
	 */
	public function getItemCompleteAttrDetail($item_id)
	{
		$productHelper = new productHelper;

		return $productHelper->getItemCompleteAttrDetail($item_id);
	}

	/**
	 * function to get cart items count
	 *
	 * @return  STRING
	 */
	public function countCartitems()
	{
		$db                  = Factory::getDBO();
		$Quick2cartModelcart = new Quick2cartModelcart;
		$cart_id             = $Quick2cartModelcart->getCartId();

		if (!isset($cart_id))
		{
			return;
		}

		$query = "Select SUM(product_quantity) FROM #__kart_cartitems WHERE cart_id='$cart_id' ";
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * function to get item ids of items in cart
	 *
	 * @return  STRING
	 */
	public function getCartItemIds()
	{
		$cartid = $this->getCartId();
		$query  = "SELECT kc.item_id
		FROM #__kart_cartitems as kc
		WHERE kc.cart_id =" . $cartid;
		$this->_db->setQuery($query);

		return $this->_db->loadColumn();
	}

	/**
	 * According to to selected users option. Restructure the attribute and its option data
	 *
	 * @param   ARRAY  $attributes      product attribute details
	 * @param   ARRAY  $selectedOption  Only selected option details
	 *
	 * @return  Object list.
	 *
	 * @since	2.5
	 */
	public function getFormatedAttrData($attributes, $selectedOption)
	{
		$formattedAttriDetails = array();

		if (!empty($attributes) && !empty($selectedOption))
		{
			foreach ($attributes as $key => $attri)
			{
				$formattedAttriDetails[$key] = $attri;
				$attriId                     = $attri['itemattribute_id'];

				if (!empty($selectedOption[$attriId]))
				{
					$formattedAttriDetails[$key]['selectedOptionDetail'] = $selectedOption[$attriId];
				}
			}
		}

		return $formattedAttriDetails;
	}

	/**
	 * Function to check if user is allowed to remove cart item from cart
	 *
	 * @param   INT  $cartItemId  Cart item ID
	 *
	 * @return  boolean
	 *
	 * @since	2.5
	 */
	public function allowedToRemoveCartItem($cartItemId)
	{
		$app  = Factory::getApplication();
		$db   = Factory::getDbo();
		$user = Factory::getUser();

		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');
		$cartItemTable = Table::getInstance('CartItem', 'Quick2cartTable', array('dbo', $db));
		$cartItemTable->load(array('cart_item_id' => $cartItemId));

		$productHelper = new productHelper;
		$storeHelper = new storeHelper;

		$productData = $productHelper->getItemCompleteDetail($cartItemTable->item_id);
		$storeOwner  = $storeHelper->getStoreOwner($productData->store_id);

		$cartTable = Table::getInstance('Cart', 'Quick2cartTable', array('dbo', $db));
		$cartTable->load(array('cart_id' => $cartItemTable->cart_id));

		// If cart is already empty
		if (empty($cartTable->cart_id))
		{
			return true;
		}

		if ($user->id)
		{
			// If user is cart owner, store owner or the admin then he is allowed to delete cart item
			if ($cartTable->user_id == $user->id || $user->id == $storeOwner || $user->authorise('core.admin'))
			{
				return true;
			}
		}
		else
		{
			$session   = Factory::getSession();
			$sessionId = $session->getId();

			if ($sessionId == $cartTable->session_id)
			{
				return true;
			}
		}

		$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
	}

	/**
	 * This function retrieves the quantity associated with a given promotion ID from the database.
	 *
	 * @param   int  $promotionId  The ID of the promotion whose quantity is to be fetched.
	 *
	 * @return  int  $quantity  The quantity associated with the given promotion ID.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getPromotionQuantity($promotionId)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('quantity'))
			->from($db->quoteName('#__kart_promotions_rules'))
			->where($db->quoteName('promotion_id') . ' = ' . (int) $promotionId);

		$db->setQuery($query);
		return $db->loadResult();
	}
}
