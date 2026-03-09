<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

$options = $displayData['options'];
$custom_class  = (isset($options->class)) ? ' ' . $options->class : '';
$hiddenClass = '';
$data_attr = '';
$doc = Factory::getApplication ()->getDocument ();

//Image lazy load
$config = ComponentHelper::getParams('com_jpagebuilder');
$lazyload = $config->get('lazyloadimg', '0');
$background_image = (isset($options->background_image) && $options->background_image) ? $options->background_image : '';
$background_image_src = isset($background_image->src) ? $background_image->src : $background_image;

if ($lazyload && $background_image_src)
{
	if ($options->background_type == 'image')
	{
		$custom_class .= ' jpb-element-lazy';
	}
}

// Responsive
if (isset($options->sm_col) && $options->sm_col)
{
	$options->cssClassName .= ' jpb-' . $options->sm_col;
}

if (isset($options->xs_col) && $options->xs_col)
{
	$options->cssClassName .= ' jpb-' . $options->xs_col;
}


if (isset($options->items_align_center) && $options->items_align_center)
{
	$options->cssClassName .= ' jpb-column-vertical-align';
}
//Column order
$column_order = '';
if (isset($options->tablet_order_landscape) && $options->tablet_order_landscape)
{
	$column_order .= ' jpb-order-lg-' . $options->tablet_order_landscape;
}
if (isset($options->tablet_order) && $options->tablet_order)
{
	$column_order .= ' jpb-order-md-' . $options->tablet_order;
}
if (isset($options->mobile_order_landscape) && $options->mobile_order_landscape)
{
	$column_order .= ' jpb-order-sm-' . $options->mobile_order_landscape;
}
if (isset($options->mobile_order) && $options->mobile_order)
{
	$column_order .= ' jpb-order-xs-' . $options->mobile_order;
}

// Visibility

if (isset($options->hidden_xl) && $options->hidden_xl)
{
	$hiddenClass .= ' jpb-hidden-xl';
}

if (isset($options->hidden_lg) && $options->hidden_lg)
{
	$hiddenClass .= ' jpb-hidden-lg';
}

if (isset($options->hidden_md) && $options->hidden_md)
{
	$hiddenClass .= ' jpb-hidden-md';
}

if (isset($options->hidden_sm) && $options->hidden_sm)
{
	$hiddenClass .= ' jpb-hidden-sm';
}

if (isset($options->hidden_xs) && $options->hidden_xs)
{
	$hiddenClass .= ' jpb-hidden-xs';
}

if (isset($options->items_content_alignment) && ($options->items_content_alignment == 'top' || $options->items_content_alignment == 'start'))
{
	$custom_class .= (isset($options->items_align_center) && $options->items_align_center) ?  ' jpb-align-items-top' : '';
}
else if (isset($options->items_content_alignment) && ($options->items_content_alignment == 'bottom' || $options->items_content_alignment == 'end'))
{
	$custom_class .= (isset($options->items_align_center) && $options->items_align_center) ?  ' jpb-align-items-bottom' : '';
}
else
{
	$custom_class .= (isset($options->items_align_center) && $options->items_align_center) ?  ' jpb-align-items-center' : '';
}

// Animation
$hasEnableAnimationProperty = property_exists($options, 'enable_animation');
$isAnimationEnabled = false;

if ($hasEnableAnimationProperty)
{
	$isAnimationEnabled = !empty($options->enable_animation) && !empty($options->animation);
}
else
{
	$isAnimationEnabled = !empty($options->animation);
}

if ($isAnimationEnabled)
{

	$custom_class .= ' jpb-wow ' . $options->animation;

	if (!empty($options->animationduration))
	{
		$data_attr .= ' data-jpb-wow-duration="' . $options->animationduration . 'ms"';
	}

	if (!empty($options->animationdelay))
	{
		$data_attr .= ' data-jpb-wow-delay="' . $options->animationdelay . 'ms"';
	}
}

$html  = '';
$html .= '<div class="jpb-' . $options->cssClassName . ' ' . $hiddenClass . ' ' . $column_order . '" id="column-wrap-id-' . $options->dynamicId . '">';
$html .= '<div id="column-id-' . $options->dynamicId . '" class="jpb-column ' . $custom_class . '" ' . $data_attr . '>';

if ($background_image_src)
{
	if (isset($options->overlay_type) && $options->overlay_type !== 'overlay_none')
	{
		$html .= '<div class="jpb-column-overlay"></div>';
	}
}

$html .= '<div class="jpb-column-addons">';

echo $html;
