<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.JShortcodes
 *
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$attributes = array();

// Title
if ($item->anchor_title)
{
	$attributes[] = 'title="' . htmlspecialchars($item->anchor_title, ENT_COMPAT, 'UTF-8') . '"';
}

// Classes
$class = 'mod-menu__separator separator';
if ($item->anchor_css)
{
	$class .= ' ' . htmlspecialchars($item->anchor_css, ENT_COMPAT, 'UTF-8');
}

// Bootstrap dropdown toggle for parent items
if ($item->parent)
{
	$class .= ' dropdown-toggle';
	$attributes[] = 'data-bs-toggle="dropdown"';
	$attributes[] = 'role="button"';
	$attributes[] = 'aria-expanded="false"';
}

// aria-current
if ($item->id == $active_id)
{
	$attributes[] = 'aria-current="' . ($item->current ? 'page' : 'location') . '"';
}

// Link type with image if any
$linktype = $item->title;
if ($item->menu_image)
{
	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title, $image_attributes);
	}
	else
	{
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title);
	}
	
	if ($itemParams->get('menu_text', 1))
	{
		$linktype .= '<span class="image-title">' . $item->title . '</span>';
	}
}

?>
<span class="nav-link <?php echo $class; ?>" <?php echo implode(' ', $attributes); ?>>
	<?php echo $linktype; ?>
</span>
