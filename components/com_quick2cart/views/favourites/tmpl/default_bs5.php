<?php
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;



require_once(JPATH_SITE . '/components/com_quick2cart/helpers/media.php');
$helperobj        = new comquick2cartHelper;

$user=Factory::getUser();

$userID=$user->id;


if ($user->guest) {
    // Redirect to the login page
    $app = Factory::getApplication();
    $loginUrl = 'index.php?option=com_users&view=login';

    // Optionally, add a return URL to redirect back after login
    $returnUrl = base64_encode($app->input->get('Itemid', '', 'int') ? 'index.php?Itemid=' . $app->input->get('Itemid') : 'index.php');
    $loginUrl .= '&return=' . $returnUrl;

    $app->redirect($loginUrl);
    exit;
}

?>

<h2><?php echo Text::_('COM_QUICK2CART_MY_FAVOURITES');?></h2>

<div class="container">
    <div class="row g-3">
        <?php
       
        $target_data = $this->items;
        if (empty($target_data)) {
        ?>
            <div class="alert alert-warning">
                <span><?php echo Text::_('QTC_NO_PRODUCTS_FOUND'); ?></span>
            </div>
        <?php
        } else {
            foreach ($target_data as $data) {
                $data = (array) $data;

                //Get product link
		        $product_link = $helperobj->getProductLink($data['item_id'], 'detailsLink');

        
            $images = json_decode($data['images'], true);
            $img    = Uri::base().'components/com_quick2cart/assets/images/default_product.jpg';

            if (!empty($images))
            {
                // Get first key
                $firstKey = 0;
                foreach ($images as $key=>$img)
                {
                    $firstKey = $key;

                    break;
                }

                // create object of media helper class
                $media                       = new qtc_mediaHelper();
                $file_name_without_extension = $media->get_media_file_name_without_extension($images[$firstKey]);
                $media_extension             = $media->get_media_extension($images[$firstKey]);
                $img                         = $helperobj->isValidImg($file_name_without_extension.'_L.'.$media_extension);

                if (empty($img))
                {
                    $img = Uri::base().'components/com_quick2cart/assets/images/default_product.jpg';
                }
            }
            ?>



                <!-- Block View - Each product takes full width -->
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="row g-0 align-items-center">
                            <!-- Image Column -->
                            <div class="col-4 col-md-3 text-center">
                                <a title="<?php echo htmlentities($data['name']);?>" href="<?php echo $product_link; ?>">
                                    <img class="img-fluid  w-50 mt-0"
                                        src="<?php echo $img; ?>"
                                        alt="<?php echo Text::_('QTC_IMG_NOT_FOUND'); ?>"
                                        title="<?php echo $data['name']; ?>" />
                                </a>
                            </div>
                            <!-- Details Column -->
                            <div class="col-8 col-md-9 position-relative">
                                <div class="card-body p-2">
                                    <a title="<?php echo htmlentities($data['name']); ?>" 
                                    href="<?php echo $product_link; ?>" 
                                    class="text-decoration-none text-dark fw-bold d-block mb-1">
                                        <?php echo htmlspecialchars($data['name']); ?>
                                    </a>
                                    <p class="card-text fw-bold mb-0">
                                        <strong>Price : </strong> <?php echo $helperobj->getFromattedPrice($data['price']) ?>
                                    </p>
                                </div>
                                <!-- Delete Button -->
                                <button class="btn btn-sm position-absolute top-0 end-0 m-2 p-1" 
                                        onclick="deleteFavourite('<?php echo $data['item_id']?>', '<?php echo $userID ?>')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 448 512">
                                        <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" fill="#c2c2c2" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

        <?php
            }
        }
        ?>
    </div>
</div>

<script>

    // JavaScript function to handle delete
    function deleteFavourite(productId, userId) 
    {
        const ajaxUrl = '<?php echo Uri::root(); ?>index.php?option=com_quick2cart&task=productpage.toggleFavourite';

        if (confirm('<?php echo Text::_('COM_QUICK2CART_REMOVE_ALERT'); ?>')) 
        {
            // Send AJAX request to remove product
            Joomla.request({
                url: ajaxUrl,
                method: 'POST',
                data: `product_id=${encodeURIComponent(productId)}&user_id=${encodeURIComponent(userId)}&action=remove`,
                onSuccess: function (response) 
                {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            location.reload(); // Refresh the page to update the list
                        } else {
                            alert(result.message || '<?php echo Text::_('COM_QUICK2CART_REMOVE_ERROR'); ?>');
                        }
                    } catch (e) {
                        alert('<?php echo Text::_('COM_QUICK2CART_REMOVE_SERVER_ERROR'); ?>');
                    }
                },
                onError: function () 
                {
                    alert('<?php echo Text::_('COM_QUICK2CART_REMOVE_ERROR'); ?>');
                }
            });
        }
    }

</script>
