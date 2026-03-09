<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2025 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Language\Text;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * ProductPage controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerProductpage extends Quick2cartController
{

    /**
	 * Function to insert and update DB products details
	 *
	 * @return  status of sql query
	 *
	 * @since	
	 */
    public function toggleFavourite()
    {
        // Load add and remove Methods from Model
        $model = $this->getModel('productpage');

        $input = Factory::getApplication()->input;
        $productId = $input->getInt('product_id');
        $userId = $input->getInt('user_id');
        $action = $input->getCmd('action');

        // Initialize response
        $response = ['success' => false, 'message' => ''];

        if ($action === 'add') {
            $result = $model->addAsFavourite($productId, $userId);
            if ($result) {
                $response['success'] = true;
                $response['message'] = Text::_('COM_QUICK2CART_ADD_MSGG'); // Use language constant
            } else {
                $response['message'] = Text::_('COM_QUICK2CART_ERROR_UPDATE');
            }
        } elseif ($action === 'remove') {
            $result = $model->removeFromFavourite($productId, $userId);
            if ($result) {
                $response['success'] = true;
                $response['message'] = Text::_('COM_QUICK2CART_REMOVE_MSGG'); // Use language constant
            } else {
                $response['message'] = Text::_('COM_QUICK2CART_ERROR_UPDATE');
            }
        } else {
            $response['message'] = Text::_('COM_QUICK2CART_INVALID_ACTION');
        }

        // Return JSON response
        echo json_encode($response);
        Factory::getApplication()->close();
    }


}
