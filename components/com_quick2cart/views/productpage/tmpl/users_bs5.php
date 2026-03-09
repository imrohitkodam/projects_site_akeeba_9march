<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2024 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;

HTMLHelper::_('bootstrap.renderModal');

if (empty($this->itemdetail))
{
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
		<div class="well well small">
			<div class="alert alert-danger">
				<span><?php echo Text::_('QTC_PROD_INFO_NOT_FOUND'); ?> </span>
			</div>
		</div>
	</div>
	<?php
	return false;
}

$document = Factory::getDocument();
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/swipebox.min.css');
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> container-fluid " id="qtcProductPage">

	<div class="row" itemscope itemtype="http://schema.org/Product">
		<?php $productTotSpan = "col-xs-12";?>
			<div class="<?php echo $productTotSpan; ?>">
				
			<?php

			if (!empty($this->peopleWhoBought) && $this->socialintegration != 'none')
			{
				$who_bought_limit = 1000;
				$WhoBought_style = ($this->who_bought == 1) ? "display:block" : "display:none";
				?>
				<div style="<?php echo $WhoBought_style; ?>">
					<h4 class="sectionTitle"><?php echo Text::_('COM_QUICK2CART_WHO_BOUGHT');?></h4>
					<ul class="thumbnails qtc_ForLiStyle">
						<?php
						$i = 0;
						$libclass = $this->comquick2cartHelper->getQtcSocialLibObj();

						foreach ($this->peopleWhoBought as $data)
						{
							$usertable  = User::getTable();
							$buyed_user_id = intval( $data->id );

							if($usertable->load( $buyed_user_id ))
							{
								$i ++;
								?>
								<li>
									<a href="<?php echo $libclass->getProfileUrl(Factory::getUser($data->id));?>">
										<img title="<?php echo $data->name;?>" alt="<?php echo $data->name;?>"
											src="<?php echo $libclass->getAvatar(Factory::getUser($data->id));?>"
											class="user-bought img-rounded q2c_image" />
									</a>
								</li>
							<?php
							}
						}
						?>
					</ul>
				</div>

				<?php
			}
			?>
		</div>
</div>
