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

jimport( 'activity.socialintegration.profiledata' );
require_once JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

$productHelper = new productHelper();

// Get Item Id by url parameter
$jinput  = Factory::getApplication()->input;
$itemId  = $jinput->get('itemid');

$params = ComponentHelper::getParams('com_quick2cart');
$socialintegration = $params->get('integrate_with','none');
$who_bought_limit = $params->get('who_bought_limit', 2);
$peopleWhoBought = $productHelper->peopleWhoBought($itemId,$params->get('who_bought_limit',2));
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
		<div class="row">
					<div class="span12 well well-small">
						<div align="center"><h4><?php echo Text::_('COM_QUICK2CART_WHO_BOUGHT') ;?></h4></div>
						<ul class="center thumbnails qtc_ForLiStyle" >
							<?php
							$i = 0 ;
							foreach($peopleWhoBought as $data)
							{
								$i ++;
								$libclass = new activitysocialintegrationprofiledata();
							?>
								<li>
									<a href="<?php echo $libclass->getUserProfileUrl($socialintegration, $data->id);?>">
											<img title="<?php echo $data->name; ?>" alt="Image Not Found" src="<?php echo $libclass->getUserAvatar($socialintegration, $data);?>" class="user-bought img-rounded ">
									</a>
								</li>
							<?php
							}
							?>

						</ul>
					</div>
		</div>
</div>

