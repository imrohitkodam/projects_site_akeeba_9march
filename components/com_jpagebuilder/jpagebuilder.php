<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/autoload.php';

JpagebuilderAutoload::loadClasses();
JpagebuilderAutoload::loadHelperClasses();
JpagebuilderAutoload::loadGlobalAssets();

$controller = BaseController::getInstance('Jpagebuilder');
$controller->execute(Factory::getApplication()->getInput()->get('task'));
$controller->redirect();
