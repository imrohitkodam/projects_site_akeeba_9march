<?php

/**
 * @package         Smile Pack
 * @version         2.1.1 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

if (!$value = $params->get('value'))
{
	return;
}

$value = json_decode(json_encode($value), true);

$gallery_items = isset($value['items']) ? $value['items'] : [];
if (!$gallery_items)
{
	return;
}

require_once dirname(__DIR__) . '/fields/helper.php';

$arrayParams = $params->toArray();


$style = 'grid';
$widgetName = 'Gallery';
$tags = [];




// Grid columns
$columns = $style === 'grid' && isset($arrayParams['columns']) ? $arrayParams['columns'] : null;



$payload = [
    'items' => SPGalleryHelper::prepareItems($gallery_items, $tags),
    'columns' => $columns,
    'gap' => isset($arrayParams['gap']) ? $arrayParams['gap'] : null,
    
    'style' => 'grid',
    'limit_files' => 8,
    'ordering' => 'default',
    
    
];



echo \NRFramework\Widgets\Helper::render($widgetName, $payload);