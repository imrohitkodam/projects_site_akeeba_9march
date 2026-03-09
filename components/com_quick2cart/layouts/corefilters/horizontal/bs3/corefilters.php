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

$comquick2cartHelper = new Comquick2cartHelper;
$removeParamOnchangeCat = $comquick2cartHelper->getParameterToRemoveOnChangeOfCategory();
$compSpecificFilterHtml = $comquick2cartHelper->getComponentSpecificFilterHtml();
echo $compSpecificFilterHtml;
