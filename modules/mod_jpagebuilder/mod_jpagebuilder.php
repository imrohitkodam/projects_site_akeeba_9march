<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('restricted access');

use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/helper.php';
require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/autoload.php';

JpagebuilderAutoload::loadClasses();
JpagebuilderAutoload::loadHelperClasses();

$data = ModJPagebuilderHelper::getData($module->id, $params);
$moduleclass_sfx = !empty($params->get('moduleclass_sfx')) ? htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8') : "";

require ModuleHelper::getLayoutPath('mod_jpagebuilder', $params->get('layout', 'default'));
