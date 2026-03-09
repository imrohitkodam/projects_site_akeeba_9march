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
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;

/**
 * View class for list view of Favourites products.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewFavourites extends HtmlView
{
    protected $items;

    /**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
    public function display($tpl = null)
    {
		$model = $this->getModel('favourites');
        $comquick2cartHelper  = new Comquick2cartHelper;
        $this->items = $model->getItems();
        parent::display($tpl);
    }
}
