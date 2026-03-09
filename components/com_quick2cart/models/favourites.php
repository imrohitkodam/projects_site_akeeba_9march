<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;


class Quick2cartModelFavourites extends ListModel
{
    public function getItems()
    {
        // Get current user ID
        $user = Factory::getUser();

        if($user)
        {
             $userId = $user->id;
        }
        else
        {
            return [];
        }

        // Database query to fetch favorite products for login user
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('f.item_id,p.*')
            ->from($db->quoteName('#__kart_favourite', 'f'))
            ->join('INNER', $db->quoteName('#__kart_items', 'p') . ' ON f.item_id = p.item_id')
            ->where($db->quoteName('p.state') .'= 1')
            ->where($db->quoteName('f.user_id') . ' = ' . (int) $userId);

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}
