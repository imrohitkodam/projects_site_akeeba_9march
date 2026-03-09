<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access
defined ( 'JPATH_BASE' ) or die ();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Field\ListField;

class JFormFieldParentMenu extends ListField {
	protected $type = 'ParentMenu';
	protected function getOptions() {
		$options = array ();

		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query = $query->select ( 'DISTINCT(a.id) AS value, a.title AS text, a.level, a.lft' )
					   ->from ( '#__menu AS a' );

		// Filter by menu type.
		if ($menuType = $this->form->getValue ( 'menutype' )) {
			$query->where ( 'a.menutype = ' . $db->quote ( $menuType ) );
		} else {
			$query->where ( 'a.menutype = ' . $db->quote ( 'mainmenu' ) );
		}

		// Filter by client id.
		$clientId = $this->getAttribute ( 'clientid' );

		if (! is_null ( $clientId )) {
			$query->where ( $db->quoteName ( 'a.client_id' ) . ' = ' . ( int ) $clientId );
		}

		// Prevent parenting to children of this item.
		if ($id = $this->form->getValue ( 'id' )) {
			$query->join ( 'LEFT', $db->quoteName ( '#__menu' ) . ' AS p ON p.id = ' . ( int ) $id )
				  ->where ( 'NOT(a.lft >= p.lft AND a.rgt <= p.rgt)' );
		}

		$query->where ( 'a.published != -2' )->order ( 'a.lft ASC' );

		// Get the options.
		$db->setQuery ( $query );

		try {
			$options = $db->loadObjectList ();
		} catch ( RuntimeException $e ) {
			throw new \Exception ( $e->getMessage (), 500 );
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for($i = 0, $n = count ( $options ); $i < $n; $i ++) {
			if ($clientId != 0) {
				// Allow translation of custom admin menus
				$options [$i]->text = str_repeat ( '- ', $options [$i]->level ) . Text::_ ( $options [$i]->text );
			} else {
				$options [$i]->text = str_repeat ( '- ', $options [$i]->level ) . $options [$i]->text;
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge ( parent::getOptions (), $options );

		return $options;
	}
}
