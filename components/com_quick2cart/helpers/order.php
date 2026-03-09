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
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Order helper
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       __DEPLOY_VERSION__
 */
class OrderHelper
{
	/**
	 * This function return delivery slot html per day wise
	 *
	 * @param   String  $eachDate    each advance delviery date
	 * @return  String  html
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDeliverySlotHtml($eachDate)
	{
		$params             = ComponentHelper::getParams('com_quick2cart');
		$deliveryTimeFormat = $params->get('delivery_time_format', 12, 'Integer');
		$minDeliveryTime    = $params->get('min_delivery_time', 30, 'Integer');

		// Subform field value
		$deliverytimeslots  = $params->get('deliverytimeslots');
		$timeConvertedArray = array();

		if (!empty($deliverytimeslots))
		{
			foreach($deliverytimeslots as $eachtimeslot)
			{
				$eachDateArray = array();

				// If admin has configured 12 hours time format then convert delivery slot time into 12 hours format
				if ($deliveryTimeFormat == 12)
				{
					$fromDateString = $eachtimeslot->deliveryslotfromtime.':00:00 ' . $eachDate;
					$fromDate  = date('h a', strtotime($fromDateString));

					$toDateString = $eachtimeslot->deliveryslottotime.':00:00 ' . $eachDate;
					$toDate  = date('h a', strtotime($toDateString));

					$eachDateArray['originalfromTime'] = $eachtimeslot->deliveryslotfromtime;
					$eachDateArray['originaltoTime']   = $eachtimeslot->deliveryslottotime;
					$eachDateArray['convertedTime']    = $fromDate . '-' . $toDate;
					$timeConvertedArray[$eachDate][]   = $eachDateArray;
				}
				elseif ($deliveryTimeFormat == 24)
				{
					$eachDateArray['originalfromTime'] = $eachtimeslot->deliveryslotfromtime;
					$eachDateArray['originaltoTime']   = $eachtimeslot->deliveryslottotime;
					$eachDateArray['convertedTime']    = $eachtimeslot->deliveryslotfromtime . '-' . $eachtimeslot->deliveryslottotime;
					$timeConvertedArray[$eachDate][]   = $eachDateArray;
				}
			}
		}

		$currenctDateTime = new DateTime(HtmlHelper::Date('now','d-m-Y H:s:i'));
		$today    = HtmlHelper::date('now', 'd-m-Y');
		$tomorrow = date("d-m-Y", strtotime('tomorrow'));

		//Get last delivery slot time
		$maxDeliverySlotTime = 0;

		foreach($timeConvertedArray[$eachDate] as $key => $value)
		{
			if ($maxDeliverySlotTime < $value['originaltoTime'])
			{
				$maxDeliverySlotTime = $value['originaltoTime'];
			}
		}

		$html = '
			<div class="row">
				<div class="col-sm-12">
					<ul class="list-inline perDaydeliverySlots" id="perDaydeliverySlots_' . $eachDate . '">';

					if ($today == $eachDate)
					{
						$now            = date("d-m-Y H:i:s",time());
						$everyDateTime  = new DateTime($eachDate . $maxDeliverySlotTime . ':00:00');

						// Get min delivery time slot
						$minDeliverySlotArray   = $this->getMinDeliveryTimeSlot();
						$minDeliverySlotTime    = $minDeliverySlotArray['deliveryslottotime'];
						$minDeliverySlotTimeKey = $minDeliverySlotArray['deliveryslottotimeKey'];

						// Get max delivery time slot
						$maxDeliverySlotArray   = $this->getMaxDeliveryTimeSlot();
						$maxDeliverySlotTime    = $maxDeliverySlotArray['deliveryslottotime'];
						$maxDeliverySlotTimeKey = $maxDeliverySlotArray['deliveryslottotimeKey'];
						$todaysMaxDeliveryTime  = new DateTime(date('d-m-Y') . $maxDeliverySlotTime . ':00:00');

						$nextImmediateDeliverySlotArray    = $this->getNextImmediateDeliveryTimeSlot();
						$nextImmediateDeliveryTimeSlot     = $nextImmediateDeliverySlotArray['deliveryslottotime'];
						$nextImmediateDeliveryTimeSlotKey  = $nextImmediateDeliverySlotArray['deliveryslottotimeKey'];
						$nextImmediateDeliveryTimeSlotFlag = $nextImmediateDeliverySlotArray['nextImmediateDeliveryTimeSlotFlag'];

						if ($nextImmediateDeliveryTimeSlotFlag)
						{
							$todaysNextImmediateDeliveryTime  = date('d-m-Y') . ' ' . $nextImmediateDeliveryTimeSlot . ':00:00';
						}

						$timeinterval = round(abs(strtotime($todaysNextImmediateDeliveryTime) - strtotime($now))/60);

						if ($deliveryTimeFormat == 12)
						{
							if ($nextImmediateDeliveryTimeSlotFlag && ($timeinterval < $minDeliveryTime))
							{
								$fromDateString = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslotfromtime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
								$toDateString = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslottotime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
							}
							elseif($currenctDateTime > $todaysMaxDeliveryTime)
							{
								$fromDateString = $deliverytimeslots->$minDeliverySlotTimeKey->deliveryslotfromtime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
								$toDateString = $deliverytimeslots->$minDeliverySlotTimeKey->deliveryslottotime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
							}
							else
							{
								$fromDateString = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslotfromtime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
								$toDateString = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslottotime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
							}

							$fromDate = date('h a', strtotime($fromDateString));
							$toDate   = date('h a', strtotime($toDateString));
						}
						else
						{
							if ($nextImmediateDeliveryTimeSlotFlag && ($timeinterval < $minDeliveryTime))
							{
								$fromDate = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslotfromtime;
								$toDate   = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslottotime;
							}
							if($currenctDateTime > $todaysMaxDeliveryTime)
							{
								$fromDate = $deliverytimeslots->$minDeliverySlotTimeKey->deliveryslotfromtime;
								$toDate   = $deliverytimeslots->$minDeliverySlotTimeKey->deliveryslottotime;
							}
							else
							{
								$fromDate = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslotfromtime;
								$toDate   = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslottotime;
							}
						}
	
						if ($nextImmediateDeliveryTimeSlotFlag && ($timeinterval < $minDeliveryTime))
						{
							$immediateOrderBtnLabel = Text::sprintf("QTC_ORDER_DETAIL_DEFAULT_MIN_DELIVERY_SLOT_TIME", $minDeliveryTime);
							$dataDateAttrValue      = $today;
							$dataTimeAttrValue      = $fromDate . '-' . $toDate;
						}
						elseif ($currenctDateTime > $everyDateTime)
						{
							$immediateOrderBtnLabel = Text::sprintf("QTC_ORDER_DETAIL_DEFAULT_MIN_DELIVERY_SLOT_FOR_TOMORROW", $fromDate . '-' . $toDate);
							$dataDateAttrValue = date("d-m-Y", strtotime('tomorrow'));
							$dataTimeAttrValue = $fromDate . '-' . $toDate;
						}
						else
						{
							$immediateOrderBtnLabel = Text::sprintf("QTC_ORDER_DETAIL_IMMEDIATE_DELIVERY_SLOT_TIME_FOR_TODAY", $fromDate . '-' . $toDate);
							$dataDateAttrValue = $today;
							$dataTimeAttrValue = $fromDate . '-' . $toDate;
						}

						$html .= '
						<li style="margin-bottom: 15px;" class="list-inline-item">
							<button data-slotlabel="' . $immediateOrderBtnLabel .'" data-slot="defaultSlot" data-date="' . $dataDateAttrValue . '" data-time="' . $dataTimeAttrValue . '" type="button" class="btn btn-primary selectDeliverySlot active" id="defaultSlot">' . $immediateOrderBtnLabel . '</button>
						</li>
						<br>';
					}

					foreach($timeConvertedArray[$eachDate] as $key => $value)
					{
						if ($today == $eachDate)
						{
							$immediateOrderBtnLabel = Text::sprintf("QTC_ORDER_DETAIL_IMMEDIATE_DELIVERY_SLOT_TIME_FOR_TODAY", $value['convertedTime']);
						}
						elseif ($tomorrow == $eachDate)
						{
							$immediateOrderBtnLabel = Text::sprintf("QTC_ORDER_DETAIL_DEFAULT_MIN_DELIVERY_SLOT_TIME_FOR_TOMORROW", $value['convertedTime']);
						}
						else
						{
							$immediateOrderBtnLabel = $value['convertedTime'] . ', ' . HTMLHelper::_('date', $eachDate, 'j M');
						}

						$everyDateTime     = new DateTime($eachDate . $value['originaltoTime'] . ':00:00');
						$disableButton = '';

						if ($currenctDateTime > $everyDateTime)
						{
							$disableButton = 'disabled';
						}

						$html .= '
						<li style="margin-bottom: 15px;" class="list-inline-item">
							<button data-slotlabel="' . $immediateOrderBtnLabel . '" data-slot="' . $key .'" data-date="' . $eachDate . '" data-time="' . $value['convertedTime'] . '" type="button" class="btn btn-primary selectDeliverySlot" ' . $disableButton . '>' . $value['convertedTime'] . '</button>
						</li>';
					}
		$html .= '
					</ul>
				</div>
			</div>
		';

		return $html;
	}

	/**
	 * This function return min delivery slot time array
	 *
	 * @return  String  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getMinDeliveryTimeSlot()
	{
		$params              = ComponentHelper::getParams('com_quick2cart');
		$deliverytimeslots   = $params->get('deliverytimeslots');
		$minDeliverySlotTime = 24;

		foreach($deliverytimeslots as $key => $value)
		{
			if ($minDeliverySlotTime > $value->deliveryslottotime)
			{
				$minDeliverySlotTime    = $value->deliveryslottotime;
				$minDeliverySlotTimeKey = $key;
			}
		}

		$minDeliverySlotArray                          = array();
		$minDeliverySlotArray['deliveryslottotime']    = $minDeliverySlotTime;
		$minDeliverySlotArray['deliveryslottotimeKey'] = $minDeliverySlotTimeKey;

		return $minDeliverySlotArray;
	}

	/**
	 * This function return max delivery slot time array
	 *
	 * @return  String  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getMaxDeliveryTimeSlot()
	{
		$params              = ComponentHelper::getParams('com_quick2cart');
		$deliverytimeslots   = $params->get('deliverytimeslots');
		$maxDeliverySlotTime = 0;

		foreach($deliverytimeslots as $key => $value)
		{
			if ($maxDeliverySlotTime < $value->deliveryslottotime)
			{
				$maxDeliverySlotTime    = $value->deliveryslottotime;
				$maxDeliverySlotTimeKey = $key;
			}
		}

		$maxDeliverySlotArray                          = array();
		$maxDeliverySlotArray['deliveryslottotime']    = $maxDeliverySlotTime;
		$maxDeliverySlotArray['deliveryslottotimeKey'] = $maxDeliverySlotTimeKey;

		return $maxDeliverySlotArray;
	}

	/**
	 * This function return immediate delivery slot time from current time is in array
	 *
	 * @return  String  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getNextImmediateDeliveryTimeSlot()
	{
		$params              = ComponentHelper::getParams('com_quick2cart');
		$deliverytimeslots   = $params->get('deliverytimeslots');
		$nextImmediateDeliveryTimeSlot     = HTMLHelper::date('', 'H');
		$nextImmediateDeliveryTimeSlotFlag = false;

		if($nextImmediateDeliveryTimeSlot == '00')
		{
			$minDeliverySlotArray              = $this->getMinDeliveryTimeSlot();
			$nextImmediateDeliveryTimeSlot     = $minDeliverySlotArray['deliveryslottotime'];
			$nextImmediateDeliveryTimeSlotKey  = $minDeliverySlotArray['deliveryslottotimeKey'];
			$nextImmediateDeliveryTimeSlotFlag = true;
		}
		else
		{
			foreach($deliverytimeslots as $key => $value)
			{
				if ($nextImmediateDeliveryTimeSlot < $value->deliveryslottotime)
				{
					$nextImmediateDeliveryTimeSlot     = $value->deliveryslottotime;
					$nextImmediateDeliveryTimeSlotKey  = $key;
					$nextImmediateDeliveryTimeSlotFlag = true;
					break;
				}
			}
		}

		$nextImmediateDeliverySlotArray                          = array();
		$nextImmediateDeliverySlotArray['deliveryslottotime']    = $nextImmediateDeliveryTimeSlot;
		$nextImmediateDeliverySlotArray['deliveryslottotimeKey'] = $nextImmediateDeliveryTimeSlotKey;
		$nextImmediateDeliverySlotArray['nextImmediateDeliveryTimeSlotFlag'] = $nextImmediateDeliveryTimeSlotFlag;

		return $nextImmediateDeliverySlotArray;
	}
}
