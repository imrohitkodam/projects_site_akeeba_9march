<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\MVC\Controller\BaseController;

$user = Factory::getApplication()->getIdentity();
if (!$user->authorise('core.manage', 'com_jpagebuilder'))
{
	throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Require helper file
require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/helpers/jpagebuilder.php';
require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/autoload.php';

JpagebuilderAutoload::loadClasses();
JpagebuilderAutoload::loadHelperClasses();

JpagebuilderHelperSite::loadLanguage();

$controller = BaseController::getInstance('jpagebuilder');
$controller->execute(Factory::getApplication()->getInput()->get('task'));
$controller->redirect();
