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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
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

// Here if min and max qty is not present then we assign it to min=1 and max=999
$min_qty = (!empty($this->itemdetail->min_quantity)) ? $this->itemdetail->min_quantity : 1;
$max_qty = (!empty($this->itemdetail->min_quantity)) ? $this->itemdetail->max_quantity : 999;
$slab    = (!empty($this->itemdetail->slab)) ? $this->itemdetail->slab : 1 ;
$client  = $this->client;

$q2cbaseUrl    = $this->comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=category&layout=default');
$productHelper = new productHelper;

require_once (JPATH_SITE . '/components/com_quick2cart/helpers/media.php');
$media = new qtc_mediaHelper();

$prodViewPath   = $this->comquick2cartHelper->getViewpath('product', 'product_bs5');
$pepoleViewPath = $this->comquick2cartHelper->getViewpath('product', 'pepole_bs5');
$on_editor      = $this->params->get('enable_editor', 0);

// Pin height for fixes pin layout
$layout_to_load    = $this->params->get('layout_to_load','','string');
$pinHeight         = $this->params->get('fix_pin_height', '', 'int');
$productDetailsUrl = 'index.php?option=com_quick2cart&view=productpage&layout=default&item_id=' . $this->item_id;
$productDetailsUrl = Uri::root() . substr(Route::_($productDetailsUrl, false), strlen(Uri::base(true)) + 1);

JLoader::import('cartcheckout', JPATH_SITE . '/components/com_quick2cart/models');
JLoader::import('productpage', JPATH_SITE . '/components/com_quick2cart/models');

$product = new Quick2cartModelProductpage;
$cartCheckoutModel = new Quick2cartModelcartcheckout;

// Get cart details
$cartItemsQty    = array();
$itemsCartItemId = array();
$cartItems       = $cartCheckoutModel->getCheckoutCartitemsDetails();
$isFavorite         = $product->isFavorite($this->itemdetail->item_id, Factory::getUser()->id);


foreach ($cartItems as $cartItem)
{
	$key                   = !empty($cartItem['variant_item_id']) ? $cartItem['variant_item_id'] : $cartItem['item_id'];
	$cartItemsQty[$key]    = $cartItem['qty'];
	$itemsCartItemId[$key] = $cartItem['id'];
}

$user = JFactory::getUser();
$isLoggedIn = !$user->guest;
$uri     = base64_encode(Uri::getInstance()->toString());
$loginUrl = Route::_('index.php?option=com_users&view=login&return=' . $uri);
?>
<script>

	function addToFavourite(productId, userId) 
	{
		// Check login status from PHP
		var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>; // Pass login status

		if (!isLoggedIn) 
		{
			// Redirect to login page if user is not logged in
			window.location.href = "<?php echo $loginUrl; ?>";
			return;
		}

		// Joomla AJAX URL
		var ajaxUrl = '<?php echo JUri::root(); ?>index.php?option=com_quick2cart&task=productpage.toggleFavourite';

		// Select button and icon elements
		let button = document.getElementById(`favorite-button-${productId}`);
		let icon = document.getElementById(`favorite-icon-${productId}`);
		let isFavorite = button.classList.contains('btn-success'); // Check button state

		// Determine action based on current state
		let action = isFavorite ? 'remove' : 'add';

		// Update UI instantly for better UX
		if (isFavorite) 
		{
			button.classList.remove('btn-success');
			button.classList.add('btn-outline-success');
			button.innerHTML = '<i class="fa fa-heart"></i> <?php echo Text::_('COM_QUICK2CART_SAVE_BUTTON_TEXT'); ?>';
		} 
		else 
		{
			button.classList.remove('btn-outline-success');
			button.classList.add('btn-success');
			button.innerHTML = '<i class="fa fa-heart"></i> <?php echo Text::_('COM_QUICK2CART_REMOVE_BUTTON_TEXT'); ?>';
		}

		// Send AJAX request to toggle favorite state
		Joomla.request({
			url: ajaxUrl,
			method: 'POST',
			data: `product_id=${productId}&user_id=${userId}&action=${action}`,
			onSuccess: function(response)
			{
				try 
				{
					let result = JSON.parse(response);
					if (!result.success) 
					{
						// Revert UI if the operation failed
						if (action === 'add') 
						{
							button.classList.remove('btn-success');
							button.classList.add('btn-outline-success');
							button.innerHTML = '<i class="fa fa-heart"></i> <?php echo Text::_('COM_QUICK2CART_SAVE_BUTTON_TEXT'); ?>';
						}
						else 
						{
							button.classList.remove('btn-outline-success');
							button.classList.add('btn-success');
							button.innerHTML = '<i class="fa fa-heart"></i> <?php echo Text::_('COM_QUICK2CART_REMOVE_BUTTON_TEXT'); ?>';
						}
						alert(result.message || '<?php echo Text::_('COM_QUICK2CART_REMOVE_ERROR'); ?>');
					}
				}
				catch (e) 
				{
					alert('<?php echo Text::_('COM_QUICK2CART_REMOVE_SERVER_ERROR'); ?>');
				}
				},
				onError: function() {
					alert('Error updating favorites.');
					// Revert UI on error
					if (action === 'add') {
						button.classList.remove('btn-success');
						button.classList.add('btn-outline-success');
						button.innerHTML = '<i class="fa fa-heart"></i> <?php echo Text::_('COM_QUICK2CART_SAVE_BUTTON_TEXT'); ?>';
					} else {
						button.classList.remove('btn-outline-success');
						button.classList.add('btn-success');
						button.innerHTML = '<i class="fa fa-heart"></i> <?php echo Text::_('COM_QUICK2CART_REMOVE_BUTTON_TEXT'); ?>';
					}
			}
		});
	}

	function showDescription(id)
	{
		techjoomla.jQuery("#promotionDesc"+id).toggle();
	}

	techjoomla.jQuery(function()
	{
		var update_prodImg = function(){
			var imgsrc=this.src;
			imgsrc = imgsrc.replace("_S.", "_L.");
			techjoomla.jQuery("#qtc_prod_image").attr("src", imgsrc);
		};

		techjoomla.jQuery(".qtc_prod_slider_image")
			.hover(update_prodImg);
		techjoomla.jQuery(".qtcpromotiondescription").hide();
	});

	function getlimit(limit,pid,parent,min_qtc,max_qtc)
	{
		var lim=limit.trim();

		if (lim=='min')
		{
			return min_qtc;
		}
		else
		{
			return max_qtc;
		}

		return returndata;
	}

	function checkforalphaLimit(el,pid,parent,slab,min_qtc,max_qtc)
	{
		var textval=Number(el.value);
		var minlim=getlimit('min',pid,parent,min_qtc,max_qtc)

		if (textval < minlim)
		{
			alert("<?php echo Text::_('QTC_MIN_LIMIT_MSG'); ?>"+minlim);
			el.value = minlim;

			return false;
		}

		var maxlim = getlimit('max',pid,parent,min_qtc,max_qtc)

		if (textval>maxlim)
		{
			alert("<?php echo Text::_('QTC_MAX_LIMIT_MSG'); ?> "+maxlim);
			el.value =maxlim;

			return false;
		}

		var slabquantity=textval%slab;

		if(slabquantity != 0)
		{
			/* @TODO add Text  */
			alert("Enter in multiples of " + slab);
			el.value = el.defaultValue;
			return false;
		}

		return true;
	}
</script>
<?php $itemstate=$this->itemdetail->state; ?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> container-fluid " id="qtcProductPage">
	<?php
	if (! empty($this->store_role_list))
	{
		$this->store_role_list = "";
		$active = 'productpage';
		$view = $this->comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
		ob_start();
		include ($view);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
	}
	?>

	<div class="row" itemscope itemtype="http://schema.org/Product">
		<?php $productTotSpan = "col-xs-12";?>
			<div class="<?php echo $productTotSpan; ?>">
				<div class="row">
					<div class=" col-xs-12">
						<div class="row">
							<div class=" col-xs-12">
								<div>
									<?php
									if ($this->itemdetail->featured=='1')
									{
										?>
										<span class="float-start">
											<img title="<?php echo Text::_('QTC_FEATURED_PROD')?>" src="<?php echo Uri::base().'components/com_quick2cart/assets/images/featured.png'; ?>"/> &nbsp;
										</span>
										<?php
									}
									?>
									<h2 itemprop="name"><?php echo $this->itemdetail->name;?></h2>
									<?php
									if (!empty($this->productRating))
									{
										echo $this->productRating;
									}?>
								</div>
								<div class="clearfix"></div>
								<hr class="hr hr-condensed"/>
							</div>
						</div>

						<?php
						$store_owner = '';
						$store_list  = $this->store_list;
					
						if ((!empty($store_list)) && (!empty($this->itemdetail->store_id)) && (in_array($this->itemdetail->store_id, $store_list)))
						{
							$store_owner = 1;
						}
						?>

						<!-- Show category, store name -->
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<?php
								if (! empty($this->itemdetail->category))
								{
									?>
									<div>
										<?php
										$storeHelper = new storeHelper();

										if (!empty($this->itemdetail->category))
										{
											echo Text::_('QTC_CATEGORY') . ":&nbsp;";
										}

										echo $storeHelper->getCatHierarchyLink($this->itemdetail->category, 'com_quick2cart');
										?>
									</div>
									<?php
								}

								$multivendor_enable = $this->params->get('multivendor');

								if (! empty($this->itemdetail->store_id) && ! empty($multivendor_enable))
								{
									?>
									<!--  STORE NAME -->
									<div class="" itemprop="brand" itemscope itemtype="http://schema.org/Brand">
										<span>
											<?php
											$storeinfo   = $this->comquick2cartHelper->getSoreInfo($this->itemdetail->store_id);
											$storeHelper = new storeHelper();
											$storeLink   = $storeHelper->getStoreLink($this->itemdetail->store_id);
											$contact_ink = Uri::base() . 'index.php?option=com_quick2cart&view=vendor&layout=contactus&store_id=' .
											$this->itemdetail->store_id . '&item_id=' . $this->item_id . '&tmpl=component';
											?>
											<?php echo Text::_('QTC_STORE_NAME')?>:&nbsp;
											<a href="<?php echo $storeLink;?>">
												<span itemprop="name"><?php echo $storeinfo['title'];?></span>
											</a>
										</span> &nbsp;

										<?php
											$modalConfig = array('width' => '350px', 
												'height' => '150px', 
												'closeButton' => false, 
												'modalWidth' => 80, 
												'onClose' =>'alert("helo")',
												'bodyHeight' => 70);
											$modalConfig['url'] = $contact_ink;
											echo HTMLHelper::_('bootstrap.renderModal', 'sendEnquiryModal', $modalConfig);
										?>

										<a title="<?php echo Text::_('QTC_CONTACT_STORE_OWN')?>"
											class="qtcModal qtcContacStoreOwner"
											data-bs-toggle="modal" data-bs-target="#sendEnquiryModal"
											href="javascript:;">
											<i class="<?php echo Q2C_ICON_ENVELOPE; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i>
											<?php echo Text::_('COM_QUICK2CART_SEND_ENQUIRY')?>
										</a>
									</div>
									<?php
								}
								?>
								<div class="row">
									<div class="col-xs-12">
										<div class="mt-15">
											<?php
											if ($this->enable_tags == 1 && !empty($this->item->tags->itemTags))
											{
												if ($this->redirect_tags_to_products == 1)
												{
													?>
													<div class="tags">
														<i class="fa fa-tags"></i>
														<?php
														foreach ($this->item->tags->itemTags as $key => $tag)
														{
															$url = 'index.php?option=com_quick2cart&view=category&layout=default';

															
															// Get itemid
															$itemId = $this->comquick2cartHelper->getitemid($url);
															$href = Route::_(Uri::base(true) . '/index.php?option=com_quick2cart&view=category&layout=default&filter[tags]=' . $tag->tag_id . '&Itemid=' . $itemId);
															?>
															<span class="tag-2 tag-list<?php echo $key; ?>" itemprop="keywords">
																<a href="<?php echo $href; ?>" class="label label-info">
																	<?php echo $tag->title;?>
																</a>
															</span>
															<?php
														}
														?>
													</div>
													<?php
												}
												elseif ($this->redirect_tags_to_products == 0)
												{
													$this->item->tagLayout = new JLayoutFile('joomla.content.tags');
													echo ($this->item->tagLayout->render($this->item->tags->itemTags));
												}
											}
											?>
										</div>
									</div>
								</div>
							</div>

							<?php
							// AS we are using the sepereate plugin for "jlike for quick2cart" plugin
							if (!empty($this->addLikeButtons) )
							{
								?>
								<div class="qtcJlikeBtn col-md-4 col-xs-12">
									<?php  echo $this->addLikeButtons; ?>
								</div>
								<?php
							}
							?>
						</div>

						<div class="row">
							<?php
							if($this->params->get('social_sharing'))
							{
								if($this->params->get('social_shring_type')=='addthis')
								{
									$publisher_id = $this->params->get('addthis_publishid', '');
									$add_this_js='http://s7.addthis.com/js/300/addthis_widget.js';
									HTMLHelper::_('script',$add_this_js);

									$add_this_share='
									<!-- AddThis Button BEGIN -->
									<div class="addthis_toolbox addthis_default_style">
									<a class="addthis_button_facebook_like" fb:like:layout="button_count" class="addthis_button" addthis:url="'.$productDetailsUrl.'"></a>
									<a class="addthis_button_google_plusone" g:plusone:size="medium" class="addthis_button" addthis:url="'.$productDetailsUrl.'"></a>
									<a class="addthis_button_tweet" class="addthis_button" addthis:url="'.$productDetailsUrl.'"></a>
									<a class="addthis_button_pinterest_pinit" class="addthis_button" addthis:url="'.$productDetailsUrl.'"></a>
									<a class="addthis_counter addthis_pill_style" class="addthis_button" addthis:url="'.$productDetailsUrl.'"></a>
									</div>
									<script type="text/javascript">
										var addthis_config ={ pubid: "'.$publisher_id.'"};
									</script>
									<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid="' . $publisher_id .'"></script>
									<!-- AddThis Button END -->' ;

									echo' <div id="rr" style="">
										<div class="social_share_container">
										<div class="social_share_container_inner">'.
											$add_this_share.
										'</div>
									</div>
									</div>
									';
								}
								else
								{
									echo '<div id="fb-root"></div>';
									$fblike_tweet = Uri::root(true) . '/components/com_quick2cart/assets/js/fblike.js';
									echo "<script type='text/javascript' src='".$fblike_tweet."'></script>";

									echo '<div class="q2c_horizontal_social_buttons">';
									echo '<div class="float-start">
											<div class="fb-like" data-href="'.$productDetailsUrl.'" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
										</div>';
									echo '<div class="float-start">
											&nbsp; <div class="g-plus" data-action="share" data-annotation="bubble" data-href="'.$productDetailsUrl.'"></div>
										</div>';
									echo '<div class="float-start">
											&nbsp; <a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$productDetailsUrl.'" data-counturl="'.$productDetailsUrl.'"  data-lang="en">Tweet</a>
										</div>';
									echo '</div>
										<div class="clearfix"></div>';
								}
							}
							?>
						</div>
						<div class="clearfix"></div>
						<hr class="hr hr-condensed"/>
					</div>
				</div>

				<?php
				$img_divSize  = " col-md-6 col-xs-12 ";
				$prod_divSize = " col-md-6 col-xs-12 ";
				$images       = (! empty($this->itemdetail->images)) ? json_decode($this->itemdetail->images, true) : array();

				// Start OG tag support.
				$config    = Factory::getConfig();
				$site_name = $config->get('sitename');
				$document->addCustomTag('<meta property="og:title" content="' . $this->itemdetail->name . '" />');
				$document->addCustomTag('<meta property="og:url" content="' . $productDetailsUrl . '" />');
				$document->addCustomTag('<meta property="og:description" content="' . strip_tags($this->itemdetail->description) . '" />');
				$document->addCustomTag('<meta property="og:site_name" content="' . $site_name . '" />');
				// End OG tag support.
				?>
				<div class="row qtc_bottom">
					<!-- FOR PROD IMG -->
					<div class="<?php echo $img_divSize;?>">
						<!-- Show main image-->
						<div class="row">
							<div class='col-xs-12 af-mt-10 af-mb-20'>
								<!-- LM- Product Carousel Start-->
								<?php
								$ogImg                  = '';
								$allowedToAutoplayVideo = ($this->allowAutoplayVideo == '1') ? 'autoplay' : '';
								$allowedToUnmuteVideo   = ($this->enableAudioOnVideoAutoplay == '0') ? 'muted' : '';

								if (!empty($images)  && count($images) > 1)
								{
								?>
									<div class="row q2cProdCarousel">
										<div id="myCarousel" class="carousel slide" data-bs-ride="carousel" data-interval="false">
											<div class="carousel-inner q2cProdImgWrapper" role="listbox">
												<?php
												$i = 0;
												foreach ($images as $image)
												{
													$file_name_without_extension = $media->get_media_file_name_without_extension($image);
													$media_extension             = $media->get_media_extension($image);
													$img_big = Uri::base() . 'components/com_quick2cart/assets/images/default_product.jpg';

													if ($this->videoGallery && in_array($media_extension, $this->videoSupportedExtension))
													{
														$img_big = $this->comquick2cartHelper->isValidImg($file_name_without_extension . '.' . $media_extension);
													}
													else
													{
														$img_big = $this->comquick2cartHelper->isValidImg($file_name_without_extension . '_L.' . $media_extension);
													}

													$ogImg = $img_big;
													?>
													<div class="carousel-item <?php if ($i == 0) {echo 'active';} ?>">
													<?php
														if ($this->videoGallery && in_array($media_extension, $this->videoSupportedExtension))
														{
															?>
															<video controls class="qtc-prod-img" <?php echo $allowedToAutoplayVideo . ' ';?><?php echo $allowedToUnmuteVideo;?>>
																<source src="<?php echo $img_big;?>" type="<?php echo 'video/' . $media_extension;?>">
															</video>
															<?php
														}
														else
														{
														?>
															<div itemprop="image" class="qtc q2cProdImgWrapper" title="<?php echo $this->itemdetail->name; ?>"  alt="<?php echo $this->itemdetail->name; ?>"  id="<?php echo 'q2cProdImg'.$i ; ?>" style="background-image: url('<?php echo htmlentities($img_big); ?>'); ">
															</div>
														<?php
														}?>
													</div>
													<?php
													$i++;
												}
												?>
											</div>
											<a class="carousel-control-prev qtcCarouselControl btn-primary" href="#myCarousel" role="button" data-bs-target="#myCarousel" data-bs-slide="prev">
												<span class="carousel-control-prev-icon" aria-hidden="true"></span>
												<span class="visually-hidden">Previous</span>
											</a>
											<a class="carousel-control-next qtcCarouselControl btn-primary" href="#myCarousel" role="button" data-bs-target="#myCarousel" data-bs-slide="next">
												<span class="carousel-control-next-icon" aria-hidden="true"></span>
												<span class="visually-hidden">Next</span>
											</a>
										</div>
									</div>
								<?php
								}
								else
								{
									$firstKey = 0;

									foreach ($images as $key=>$img)
									{
										$firstKey = $key;
										break;
									}

									// Only one image
									$image                       = $images[$firstKey];
									$file_name_without_extension = $media->get_media_file_name_without_extension($image);
									$media_extension             = $media->get_media_extension($image);
									$img_big                     = $this->comquick2cartHelper->isValidImg($file_name_without_extension . '.' . $media_extension);

									if (empty($img_big))
									{
										$img_big = Uri::base() . 'components/com_quick2cart/assets/images/default_product.jpg';
									}

									$ogImg = $img_big;

									if ($this->videoGallery && in_array($media_extension, $this->videoSupportedExtension))
									{
										?>
										<video controls class="qtc-prod-img" <?php echo $allowedToAutoplayVideo . ' ';?> <?php echo $allowedToUnmuteVideo;?>>
											<source src="<?php echo $img_big;?>" type="<?php echo 'video/' . $media_extension;?>">
										</video>
										<?php
									}
									else
									{
										?>
										<div class="q2cProdImgWrapper">
											<div itemprop="image" class="qtc-prod-detail-img q2cProdImgWrapper" style="background-image: url('<?php echo htmlentities($img_big); ?>'); ">
											</div>
										 </div>
										<?php
									}
								}

								$document->addCustomTag('<meta property="og:image" content="' . $ogImg . '" />');
								?>
								<!-- if condition end-->
								<!-- LM- Product Carousel end-->
							</div>
						</div>
						<!--END ::100 X 100 image -->
					</div>
					<!-- END:: FOR PROD IMG -->

					<!-- FOR PROD name att, option etc -->
					<div class="<?php echo $prod_divSize;?> qtc_prod_blog_page-bs3">
						<!-- FOR FORM HORIZANTAL -->
						<div class="form-horizontal mt-2" id="<?php echo $this->item_id;?>_item" style="width: auto;">
							<?php $discount_present = ($this->params->get('usedisc') && isset($this->price['discount_price']) && !is_null($this->price['discount_price'])) ? 1 : 0;?>
							<div class="form-group row align-items-center mb-1">
								<label class="col-xs-6 col-sm-3 qtc-control-label fs-6 fw-bold">
									<strong><?php echo Text::_('QTC_ITEM_AMT')?></strong>
								</label>
								<div class="col-xs-6 col-sm-9 qtc_controls_text">
									<span class="fs-6 text-secondary fw-semibold" id="<?php echo ( (isset($this->price['price'])) ? $this->product_id.'_price' :'' );?>">
										<?php echo ($discount_present == 1) ? '<del>' . $this->comquick2cartHelper->getFromattedPrice($this->price['price']) . '</del>' : $this->comquick2cartHelper->getFromattedPrice($this->price['price']);?>
									</span>
								</div>
							</div>

							<?php
							if ( $discount_present)
							{
								?>
								<div class="row mb-1 form-group">
									<label class="col-xs-6 col-sm-3 qtc-control-label fs-6 fw-bold">
										<strong><?php echo Text::_('QTC_ITEM_DIS_AMT')?></strong>
									</label>
									<div class="col-6 col-sm-9 fs-6 qtc_controls_text fs-6 text-secondary fw-semibold" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
										<span itemprop="price" id="<?php echo $this->product_id;?>_price">
											<?php echo $this->comquick2cartHelper->getFromattedPrice($this->price['discount_price']);  ?>
										</span>
									</div>
								</div>
								<?php
							}

							if ($this->attributes)
							{
								foreach ($this->attributes as $attribute)
								{
									$requiredLabel = ($attribute->attribute_compulsary) ? '*' : '';
									?>
									<div class="form-group row">
										<label class="col-xs-6 col-sm-4 col-md-4 qtc-control-label form-label">
											<strong><?php echo $attribute->itemattribute_name . ' ' . $requiredLabel; ?></strong>
										</label>
										<?php
										$productHelper                = new productHelper();
										$data['itemattribute_id']     = $attribute->itemattribute_id;
										$data['fieldType']            = $attribute->attributeFieldType;
										$data['parent']               = $this->itemdetail->parent;
										$data['product_id']           = $this->item_id;
										$data['attribute_compulsary'] = $attribute->attribute_compulsary;
										$data['attributeDetail']      = $attribute;

										// Rendor layout
										$layout    = new FileLayout('productpage.attribute_option_display');
										$fieldHtml = $layout->render($data);
										?>
										<div class="col-xs-6 col-sm-8 col-md-8">
											<?php echo $fieldHtml;?>
										</div>
									</div>
								<?php
								}
							}
							?>

							<!-- free download links-->
							<?php
							if (! empty($this->mediaFiles))
							{
								?>
								<div class="form-group">
									<div class="col-xs-6 col-sm-4 qtc-control-label">
										<strong><?php echo Text::_( "COM_QUICK2CART_PROD_FREE_DOWNLOAD"); ?></strong>
									</div>
									<div class="col-xs-6 col-sm-8 qtc_padding_class_attributes">
										<?php
										$productHelper = new productHelper();

										foreach ($this->mediaFiles as $mediaFile)
										{
											$linkData = array();
											$linkData['linkName'] = $mediaFile['file_display_name'];
											$linkData['href'] = $productHelper->getMediaDownloadLinkHref($mediaFile['file_id']);
											$linkData['event'] = '';
											$linkData['functionName'] = '';
											$linkData['fnParam'] = '';
											echo $productHelper->showMediaDownloadLink($linkData) . "<br/>";
										}
										?>
										<br/>
									</div>
								</div>
								<?php
							}
							?>
							<!-- END free download links-->
							<?php
							$showqty       = $this->params->get('qty_buynow', 1);
							$showqty_style = (empty($showqty)) ? "display:none;" : '';

							if ($this->showBuyNowBtn)
							{
								$data                  = $this->itemdetail;
								$showQtyIncDecSecStyle = $showQtyToCartSecStyle = $showQtyIncDecSecStyleSuffix = $showQtyToCartSecStyleSuffix = "";

								if (array_key_exists($data->product_id, $cartItemsQty))
								{
									$showQtyToCartSecStyleSuffix = '-tmp';
									$showQtyToCartSecStyle       = 'style="display:none;"';
								}
								else
								{
									$showQtyIncDecSecStyleSuffix = '-tmp';
									$showQtyIncDecSecStyle       = 'style="display:none;"';
								}

								if ($this->params->get('usestock', '', 'int') && !$this->params->get('outofstock_allowship', '', 'int') && is_numeric($data->stock) && $data->stock < $data->max_quantity)
								{
									$data->max_quantity = $data->stock;
								}

								$textboxid        = $data->parent . '-' . $data->product_id . "_itemcount";
								$parent           = $data->parent;
								$slab             = $data->slab;
								$limits           = $data->min_quantity . "," . $data->max_quantity;
								$arg              = "'" . $textboxid . "','" . $data->product_id . "','" . $itemsCartItemId[$data->product_id] . "','" . $parent . "','" . $slab . "'," . $limits;
								$min_msg          = Text::_('QTC_MIN_LIMIT_MSG');
								$max_msg          = Text::_('QTC_MAX_LIMIT_MSG');
								$fun_param        = $parent . '-' . $data->product_id;
								$entered_numerics = "'" . Text::_('QTC_ENTER_NUMERICS') . "'";
								?>
								<div style="<?php echo $qtyDivStyle;?>" class="form-inline q2c-inline-flex mt-2 ms-1">
									<div class="q2c-item-qtycount-increment-decrement-section-<?php echo $data->product_id;?>" <?php echo $showQtyIncDecSecStyle;?>>
										<div class="input-group">
											<span class="input-group-text">
												<a href="javascript:void(0);" onclick="qtc_decrement(<?php echo $arg;?>)" class="qtc_icon-qtcminus qtc_pointerCusrsor"></a>
											</span>
											<input id="<?php echo $textboxid . $showQtyIncDecSecStyleSuffix; ?>"
												name="<?php echo $data->product_id; ?>_itemcount"
												class="qtc_textbox_small qtc_item_count_inputbox qtc_count form-control"
												type="text"
												value="<?php echo $cartItemsQty[$data->product_id]; ?>"
												size="2"
												maxlength="3"
												data-cart-item-id="<?php echo $itemsCartItemId[$data->product_id];?>"
												<?php echo $disableBuyBtn; ?>
												onblur="checkforalphaLimit(this,'<?php echo $data->product_id; ?>','<?php echo $parent; ?>','<?php echo $slab; ?>',<?php echo $limits; ?>,'<?php echo $min_msg; ?>','<?php echo $max_msg; ?>');"
												Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>)" />
											<span class="input-group-text">
												<a href="javascript:void(0);" onclick="qtc_increment(<?php echo $arg;?>)" class="qtc_icon-qtcplus qtc_pointerCusrsor"></a>
											</span>
										</div>
									</div>
									<div class="q2c-item-to-cart-section-<?php echo $data->product_id;?>" <?php echo $showQtyToCartSecStyle;?>>
										<div class="input-group" >
											<span class="input-group-text"
												for="<?php echo $textboxid; ?>">
												<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PIN_QUANTITY'), '', Text::_('COM_QUICK2CART_PRODUCT_QTY')); ?>
											</span>
											<input id="<?php echo $textboxid . $showQtyToCartSecStyleSuffix; ?>"
												name="<?php echo $data->product_id; ?>_itemcount"
												class="qtc_textbox_small qtc_item_count_inputbox qtc_count form-control"
												type="text"
												value="<?php echo $data->min_quantity; ?>"
												size="2"
												maxlength="3"
												data-cart-item-id="<?php echo $itemsCartItemId[$data->product_id];?>"
												<?php echo $disableBuyBtn; ?>
												onblur="checkforalphaLimit(this,'<?php echo $data->product_id; ?>','<?php echo $parent; ?>','<?php echo $slab; ?>',<?php echo $limits; ?>,'<?php echo $min_msg; ?>','<?php echo $max_msg; ?>');"
												Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>)" />
										</div>
										<span>&nbsp;&nbsp;</span>
									</div>
									<div class="q2c-item-to-cart-section-<?php echo $data->product_id;?>" <?php echo $showQtyToCartSecStyle;?>>
										<button class="btn btn-sm btn-success q2c-small-buy-button ms-2" type="button" <?php echo $disableBuyBtn; ?> onclick="qtc_addtocart('<?php echo $fun_param; ?>');">
											<i class="<?php echo QTC_ICON_CART; ?>"></i> <?php echo Text::_('COM_QUICK2CART_ADD_PRODUCT_TO_CART'); ?>
										</button>
										<?php
											$cartlink = Route::_('index.php?option=com_quick2cart&view=cart&tmpl=component');

											echo HTMLHelper::_(
												'bootstrap.renderModal',
												'cartModal',
												array(
													'title'		 => Text::_('QTC_CART'),
													'url'        => $cartlink,
													'modalWidth' => '80',
													'bodyHeight' => '70',
													'width'      => '100%',
													'height'     => '600px',
												)
											)
										?>
										 <!-- Heart button for adding to favorites -->
    
									</div>
									
									<div class="clearfix"></div>
								</div>
								
								<div class="clearfix"></div>


								<button class="btn btn-sm <?php echo $isFavorite ? 'btn-success' : 'btn-outline-success'; ?> ms-1 mt-2" 
										id="favorite-button-<?php echo $data->product_id; ?>" 
										onclick="addToFavourite(<?php echo $data->product_id; ?>, <?php echo $user->id; ?>)">
									<i class="fa fa-heart"></i> 
									<?php echo $isFavorite ? Text::_('COM_QUICK2CART_REMOVE_BUTTON_TEXT') : Text::_('COM_QUICK2CART_SAVE_BUTTON_TEXT'); ?>
								</button>

								<?php
								$popup_buynow = $this->params->get('popup_buynow', 1);

								if ($popup_buynow == 2)
								{
									$checkout    = 'index.php?option=com_quick2cart&view=cart';
									$itemid      = $this->comquick2cartHelper->getitemid($checkout);
									$action_link = Uri::root() . substr(Route::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemid, false), strlen(Uri::base(true)) + 1);
									?>
									<div class="cart-popup" id="<?php echo $fun_param; ?>_popup" style="display: none;">
										<div class="message"></div>
										<div class="cart_link">
											<a class="btn btn-success" href="<?php echo $action_link; ?>">
												<?php echo Text::_('COM_QUICK2CART_VIEW_CART');?>
											</a>
											<a class="btn btn-primary" href="<?php echo $q2cbaseUrl; ?>">
												<?php echo Text::_('QTC_BACK');?>
											</a>
										</div>
										<i class="<?php echo QTC_ICON_REMOVE; ?> cart-popup_close" onclick="techjoomla.jQuery(this).parent().slideUp().hide();"></i>
									</div>
									<?php
								}

								$userId = Factory::getUser()->id;
								JLoader::register('Quick2cartModelPromotions', JPATH_SITE . '/components/com_quick2cart/models/promotions.php');
								$helper = new Quick2cartModelPromotions;

								// Get all promotion IDs
								$promoIds = array_map(function ($p) { return $p->id; }, $this->applicablePromotions);
								$eligibleUsersMap = $helper->getEligibleUsersForPromotions($promoIds);

								$hasVisiblePromotion = false;

								// Check if any promotions are visible
								foreach ($this->applicablePromotions as $promotion)
								{
									$isSpecific = (int) $promotion->allowspecificuserpromotion === 1;
									$isUserEligible = in_array($userId, $eligibleUsersMap[$promotion->id]['users'] ?? []);

									if (!$isSpecific || ($isSpecific && $isUserEligible))
									{
										$hasVisiblePromotion = true;
										break;
									}
								}

								if ($hasVisiblePromotion)
								{
									?>
									<h5 class = "mt-4 fs-6"><?php echo Text::_("COM_QUICK2CART_AVAILABLE_OFFERS");?></h5>
									<div class="qtc-applicable-promotions-wrapper p-3 border rounded-2 shadow-sm bg-light">
										<?php
										foreach ($this->applicablePromotions as $promotion)
										{
											?>
											<div class="qtc-applicable-promotion-wrapper">
											<?php
												if ($promotion->coupon_required== '1' && $promotion->catlog_promotion== '1')
												{
													$flag  = 1;
													$count = count($this->applicablePromotions);
													$flag++;

													if (!empty($promotion->coupon_code))
													{
														?>
															<h5 class="mb-2 fs-6"><?php echo Text::_("QTC_CUPCODE") . " : ";?><span class="qtc-applicable-promotions badge bg-success fs-6 text-white"><?php echo $promotion->coupon_code;?></span></h5>
														<?php
													}

													if (!empty($promotion->exp_date) && $promotion->exp_date != '0000-00-00 00:00:00')
													{
														?>
															<h5 class="fs-6"><?php echo Text::_("QTC_PROMO_EXP_DATE") . " : ";?><span class="qtc-applicable-promotions"><?php echo $promotion->exp_date;?></span></h5>
														<?php
													}
													?>

													<div class="fw-bold text-dark fs-6"><strong><?php echo ucfirst($promotion->discount_type) . ' : ' . $promotion->discount . ($promotion->discount_type == 'flat' ? $this->currencies_sym : '%');?></strong></div>
													<div><strong><?php echo $promotion->name;?></strong></div>
													<div><a class="qtcHandPointer fs-6 text-primary" onclick="showDescription('<?php echo $promotion->id;?>')"><?php echo Text::_("COM_QUICK2CART_PROMOTION_DETAILS");?></a></div>
													<div class="qtcpromotiondescription" id="promotionDesc<?php echo $promotion->id;?>"><?php echo $promotion->description;?></div>
													<?php
													if ($flag <= $count)
													{
														?>
														<hr>
														<?php
													}
												}
												elseif($promotion->coupon_required == '0')
												{
													$flag = 1;
													$count = count($this->applicablePromotions);

													$flag++;
													?>
													<div><strong><?php echo $promotion->name;?></strong></div>
													<div><a class="qtcHandPointer" onclick="showDescription('<?php echo $promotion->id;?>')"><?php echo Text::_("COM_QUICK2CART_PROMOTION_DETAILS");?></a></div>
													<div class="qtcpromotiondescription" id="promotionDesc<?php echo $promotion->id;?>"><?php echo $promotion->description;?></div>
													<?php
													if ($flag <= $count)
													{
													?>
														<hr>
													<?php
													}
												}
												?>
											</div>
										<?php
										}
									?>
									</div>
									<?php
								}
								?>
								<div class="form-group">
									<?php
									if ($this->getPincodeCheckAvailability[0] === true)
									{?>
										<div class="col-xs-6 col-xs-offset-6 col-sm-8 col-sm-offset-4">
											<div class="">
												<input type="text" name="pincode" id="pincode"
												placeholder="<?php echo Text::_('enter pincode'); ?>"
												class="form-control input input-small"/>
											</div>
											<div class="">
												<a class="btn btn-default" onclick="checkPincode(<?php echo $this->item_id;?>)">Check</a>
											</div>
										</div>
										<div class="availabilitystatus"></div>
									<?php
									}?>
								</div>
								<?php
								PluginHelper::importPlugin('discounts');
								$shareButtonHtml = Factory::getApplication()->triggerEvent('onGetDiscountHtml',array($productDetailsUrl));
								echo $shareButtonHtml[0];
							}
							else
							{
								?>
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									<strong><?php echo Text::_('QTC_WARNING'); ?></strong>
									<?php echo Text::_('QTC_OUT_OF_STOCK_MSG'); ?>
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>
								<?php
							}
							?>
							<br/>
							<div class="form-group" style="margin-top : 20px;">
								<div class="col-xs-6 col-sm-4"></div>
							</div>
						</div>
						<!-- END FORM HORIZANTAL -->
					</div>
					<!-- END:: PROD name att, option etc -->
				</div>

				<!-- FOR PROD video -->
				<?php
				if (! empty($this->itemdetail->video_link))
				{
					?>
					<div class="row">
						<div class=" col-xs-12">
							<?php
							$url = (! empty($this->itemdetail->video_link)) ? ($this->itemdetail->video_link) : '';
							preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
							$id      = $matches[1];
							$srclink = "https://www.youtube.com/embed/" . $id;
							?>
							<div class="q2c-videoWrapper">
								<iframe width="100%" height="350" src="<?php echo $srclink;?>" frameborder="0" allowfullscreen></iframe>
							</div>
						</div>
						<div class="clearfix"></div>
						<hr class="hr hr-condensed"/>
					</div>
					<?php
				}
				?>
				<!-- END:: FOR PROD video -->

				<!-- PROD DESCRIPTION-->
				<?php
				// Trigger content plugins in description of product
				$this->itemdetail->description = HTMLHelper::_('content.prepare', $this->itemdetail->description);
				$other_details_class           = (!empty($this->itemdetail->description))? '':'active';

				if (!empty($this->itemdetail->description) || !empty($this->extraData))
				{?>
					<div class="clearfix"></div>
					<div class="qtcClearBoth"></div>
					<div class="row">
						<div class="col-sm-12 col-lg-12 col-md-12 af-mt-20">
							<ul class="nav nav-tabs" id="productPageTab" role="tablist">
								<?php
								if (!empty($this->itemdetail->description))
								{?>
									<li class="nav-item" role="presentation">
										<a class="nav-link active" id="description-data-tab" data-bs-toggle="tab" href="#description-data" role="tab" aria-controls="description-data" aria-selected="true">
											<?php echo Text::_('COM_QUICK2CART_PROD_DESC'); ?>
										</a>
									</li>
								<?php
								}

								if (!empty($this->extraData))
								{?>
									<li class="nav-item" role="presentation">
										<a class="nav-link" id="other-details-data-tab" data-bs-toggle="tab" href="#other-details-data" role="tab" aria-controls="other-details-data" aria-selected="false">
											<?php echo Text::_('COM_QUICK2CART_PROD_OTHER_DETAIL'); ?>
										</a>
									</li>
								<?php
								}?>
							</ul>
							<div class="tab-content" id="productPageTab">
								<div class="tab-pane fade show active" id="description-data" role="tabpanel" aria-labelledby="description-data-tab">
									<div class="clearfix"></div>
									<div class="clo-lg-12 col-md-12  col-sm-12 col-xs-12">
										<?php
										if (!$on_editor)
										{
											// Do nl2br when editor is OFF
											$prodDes = (!empty($this->itemdetail->description)) ? nl2br($this->itemdetail->description) : '';
											$prodDes = str_replace('  ', '&nbsp;&nbsp;', $prodDes);
											?>
											<p><?php echo $prodDes;?></p>
										<?php
										}
										else
										{
											$prodDes = (!empty($this->itemdetail->description)) ? $this->itemdetail->description : '';?>
											<p><?php echo $prodDes;?></p>
										<?php
										}
										?>
									</div>
								</div>
								<div class="tab-pane fade" id="other-details-data" role="tabpanel" aria-labelledby="other-details-data-tab">
									<div class="clearfix"></div>
									<?php
									if ($this->form_extra)
									{
										$count = 0;
										$xmlFieldSets = array();

										foreach ($this->formXml as $k => $xmlFieldSet)
										{
											$xmlFieldSets[$count] = $xmlFieldSet;
											$count++;
										}

										$itemData         = new stdClass();
										$itemData->id     = $this->item_id;
										$itemData->client = 'com_quick2cart.product';

										// Call the JLayout to render the fields in the details view
										$layout = new FileLayout('productpage.extrafields', JPATH_ROOT . '/components/com_quick2cart');
										echo $layout->render(array('xmlFormObject' => $xmlFieldSets, 'formObject' => $this->form_extra, 'itemData' => $itemData));
									}?>
								</div>
							</div>
						</div>
					</div>
				<?php
				}?>
				<!-- END :: PROD DESCRIPTION-->
			</div>
			<?php
			// Create span4 DIV if any one of peopleAlsoBought & prodFromSameStore DATA FOUND
			if (! empty($this->peopleAlsoBought) || ! empty($this->prodFromSameStore) )
			{
				?>
				<div class="col-xs-12">
					<!-- PEOPLE ALSO BOUGHT -->
					<?php
					if (! empty($this->peopleAlsoBought))
					{
						?>
						<h4 class="sectionTitle"><?php echo Text::_('QTC_PEOPLE_ALSO_BOUGHT_PRODUCTS');?></h4>

						<?php
						$random_container = 'q2c_pc_people_also_bought';

						// We are defining pin width here itself, bcoz this will be shown on side
						$pin_width_defined = '1';
						?>
						<style type="text/css">
							.q2c_pin_item_<?php echo $random_container;?>
							<?php
							if ($layout_to_load == "flexible_layout")
							{
								echo "{width: 160px !important; margin-bottom: 3px !important;}";
							}
							?>
						</style>
						<div id="q2c_pc_people_also_bought">
							<?php
							//LM added variables
							$Fixed_pin_classes = "";
							$noOfPin_xs = 12;
							$noOfPin_sm = 6;
							$noOfPin_md = 4;
							$noOfPin_lg = 3;

							if ($layout_to_load == "fixed_layout")
							{
								$Fixed_pin_classes = " qtc-prod-pin col-xs-" . $noOfPin_xs . " col-sm-" . $noOfPin_sm . " col-md-" . $noOfPin_md . " col-lg-" . $noOfPin_lg;
							}

							foreach ($this->peopleAlsoBought as $data)
							{
								?>
								<div class="q2c_pin_item_<?php echo $random_container . $Fixed_pin_classes; ?>">
								<?php
								ob_start();
								include ($prodViewPath);
								$html = ob_get_contents();
								ob_end_clean();
								echo $html;
								$prodclass = '';
								?>
								</div>
								<?php
							}
							?>
						</div>
						<?php
						if ($layout_to_load == "flexible_layout")
						{
						?>
							<!-- setup pin layout script-->
							<script type="text/javascript">
								var pin_container_<?php echo $random_container; ?> = 'q2c_pc_people_also_bought';
							</script>

							<?php
							$view = $this->comquick2cartHelper->getViewpath('product', 'pinsetup_bs5');
							ob_start();
							include($view);
							$html = ob_get_contents();
							ob_end_clean();
							echo $html;
						}
					}
					?>
					<!-- END :: PEOPLE ALSO BOUGHT -->
					<div class="clearfix"></div>
					<!-- OTHER PRODUCT FROM SAME STORE -->
					<?php
					if (! empty($this->prodFromSameStore))
					{
						?>
						<h4 class="sectionTitle"><?php echo Text::_('QTC_PRODUCTS_FROM_SAME_STORE');?></h4>
						<?php
						$random_container = 'q2c_pc_products_from_same_store';

						// We are defining pin width here itself, bcoz this will be shown on side
						$pin_width_defined = '1';
						?>

						<style type="text/css">
							.q2c_pin_item_<?php echo $random_container;?>
							<?php
							if ($layout_to_load == "flexible_layout")
							{
								echo "{width: 160px !important; margin-bottom: 3px !important;}";
							}
							?>
						</style>
						<div id="q2c_pc_products_from_same_store">
							<?php
							// LM added variables
							$Fixed_pin_classes = "";
							$noOfPin_xs = 12;
							$noOfPin_sm = 6;
							$noOfPin_md = 4;
							$noOfPin_lg = 3;

							if ($layout_to_load == "fixed_layout")
							{
								$Fixed_pin_classes = " qtc-prod-pin col-xs-" . $noOfPin_xs . " col-sm-" . $noOfPin_sm . " col-md-" . $noOfPin_md . " col-lg-" . $noOfPin_lg;
							}

							// REDERING
							foreach($this->prodFromSameStore as $data)
							{
								?>
								<div class="q2c_pin_item_<?php echo $random_container . $Fixed_pin_classes;?>">
								<?php
								ob_start();
								include($prodViewPath);
								$html = ob_get_contents();
								ob_end_clean();
								echo $html;
								?>
								</div>
								<?php
							}
							?>
						</div>

						<?php
						if ($layout_to_load == "flexible_layout")
						{
						?>
							<!-- setup pin layout script-->
							<script type="text/javascript">
								var pin_container_<?php echo $random_container; ?> = 'q2c_pc_products_from_same_store';
							</script>

							<?php
							$view = $this->comquick2cartHelper->getViewpath('product', 'pinsetup_bs5');
							ob_start();
							include($view);
							$html = ob_get_contents();
							ob_end_clean();
							echo $html;
						}
					}
					?>
					<div class="clearfix"></div>
					<!-- END :: OTHER PRODUCT FROM SAME STORE -->
				</div>
				<?php
			}
			// END OF SPAN3 DIV IF LOOP
			?>

			<!-- RELEATED  PRODUCT FROM SAME CAT-->
			<div class="clearfix"></div>

			<?php
			if ($this->prodFromCat)
			{
				$prodCatName = (!empty($this->itemdetail->category)) ? $this->comquick2cartHelper->getCatName($this->itemdetail->category) : '';
				?>
				<div>
					<div class="col-xs-12">
						<h4 class="sectionTitle">
							<?php echo Text::sprintf('QTC_SIMILAR_CAT_PRODUCTS', Text::_(trim($prodCatName))); ?>
						</h4>
						<?php $random_container = 'q2c_pc_similar_products';?>
						<div class="clearfix"></div>
						<div id="q2c_pc_similar_products" class="row">
							<?php
							$noOfPin_xs = 12;
							$noOfPin_sm = 6;
							$noOfPin_md = 4;
							$noOfPin_lg = 3;
							$Fixed_pin_classes = "";

							if ($layout_to_load == "fixed_layout")
							{
								$Fixed_pin_classes = " qtc-prod-pin col-xs-" . $noOfPin_xs . " col-sm-" . $noOfPin_sm . " col-md-" . $noOfPin_md . " col-lg-" . $noOfPin_lg;
							}

							foreach ($this->prodFromCat as $data)
							{
							?>
								<div class='q2c_pin_item_<?php echo $random_container . $Fixed_pin_classes . " ";?>'>
								<?php
									ob_start();
									include ($prodViewPath);
									$html = ob_get_contents();
									ob_end_clean();
									echo $html;
								?>
								</div>
							<?php
							}
							?>
						</div>
						<?php
						if ($layout_to_load == "flexible_layout")
						{
						?>
							<!-- setup pin layout script-->
							<script type="text/javascript">
								var pin_container_<?php echo $random_container; ?> = 'q2c_pc_similar_products';
							</script>
							<?php
							$view = $this->comquick2cartHelper->getViewpath('product', 'pinsetup_bs5');
							ob_start();
							include($view);
							$html = ob_get_contents();
							ob_end_clean();
							echo $html;
						}
						?>
					</div>
				</div>
			<?php
			}
			?>
			<!-- END :: RELEATED  PRODUCT FROM SAME CAT-->

			<!-- Pepole Who bought this -->
			<div class="clearfix"></div>
			<?php

			if (!empty($this->peopleWhoBought) && $this->socialintegration != 'none')
			{
				$who_bought_limit = $this->who_bought_limit;
				$WhoBought_style = ($this->who_bought == 1) ? "display:block" : "display:none";
				?>
				<div class="" style="<?php echo $WhoBought_style; ?>">
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

							if ($i == $who_bought_limit)
							{
								echo "</ul>";
								$modalConfig = array('width' => '700px', 
									'height' => '500px',
									'title' => Text::_('COM_QUICK2CART_WHO_BOUGHT'),
									'closeButton' => true,
									'modalWidth' => 80, 
									'bodyHeight' => 70);
								$modalConfig['url'] = 'index.php?option=com_quick2cart&view=productpage&layout=users_bs5&itemid=' . $this->item_id . '&tmpl=component';
								echo HTMLHelper::_('bootstrap.renderModal', 'showMoreModal', $modalConfig);
								echo '<a data-bs-target="#showMoreModal" data-bs-toggle="modal" class="">' . 
									Text::_('COM_QUICK2CART_SHOW_MORE') . '</a>';
								break;
							}
						}
						?>
					</ul>
				</div>

				<?php
			}
			?>
		</div>

<?php
if (!empty($this->afterProductDisplay))
{
	?>
	<div >
		<?php echo $this->afterProductDisplay; ?>
	</div>
	<?php
}
?>
</div>

<!--jQuery and mootool conflict resolved for carousel -- start-->
<script type="text/javascript">
	if (typeof jQuery != 'undefined') {
		(function($) {
			$(document).ready(function(){
				$('.carousel').each(function(index, element) {
					$(this)[index].slide = null;
				});
			});
		})(jQuery);
	}

	// Add click function to add products in favourites list
	function toggleFavourite(event) 
	{
		event.preventDefault();	// Prevent default button behavior
	
		const favebutton = event.currentTarget;
		const productId = favebutton.dataset.productId;
		const isLoggedIn = favebutton.dataset.loggedIn === '1';
		const userId = favebutton.dataset.userId;
		const ajaxUrl = favebutton.dataset.ajaxUrl;

		// Redirect to login page if not logged in
		if (isLoggedIn) 
		{
			alert(Joomla.JText._('COM_QUICK2CART_LOGIN_ALERT'));
			return;
		}
	
		// Define button and SVG path element IDs
		const buttonId = `wishlistButton${productId}`;
		const heartPathId = `heartPath${productId}`;
		const button = document.getElementById(buttonId);
		const heartPath = document.getElementById(heartPathId);
	
		// Validate elements
		if (!button || !heartPath) 
		{
			console.error(`Missing elements: Button or heart path for product ID: ${productId}`);
			return;
		}
	
		// Determine the current favorite state based on color
		const currentColor = heartPath.getAttribute('fill');
		const isFavorite = currentColor === '#ff4343';
	
		// Toggle UI immediately for better UX
		heartPath.setAttribute('fill', isFavorite ? '#c2c2c2' : '#ff4343');
		const action = isFavorite ? 'remove' : 'add';
	
		// Send AJAX request to update the favorite state
		Joomla.request({
			url: ajaxUrl,
			method: 'POST',
			data: `product_id=${productId}&user_id=${userId}&action=${action}`,
			onSuccess: function (response) {
				try {
					const result = JSON.parse(response);
					if (!result.success) 
					{
						// Revert UI if the operation failed
						heartPath.setAttribute('fill', isFavorite ? '#ff4343' : '#c2c2c2');
						alert(result.message);
					} 
					else 
					{
						alert(result.message);
					}
				} 
				catch (error) 
				{
					alert(Joomla.JText._('COM_QUICK2CART_SERVER_ERROR'));
	
					// Revert UI on error
					heartPath.setAttribute('fill', isFavorite ? '#ff4343' : '#c2c2c2');
				}
			},
			onError: function () 
			{
				alert(Joomla.JText._('COM_QUICK2CART_ERROR_UPDATE'));
				// Revert UI on error
				heartPath.setAttribute('fill', isFavorite ? '#ff4343' : '#c2c2c2');
			}
		});
	}
</script>
<!--jQuery and mootool conflict resolved for carousel -- end-->
