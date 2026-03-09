<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Language\Text;

/**
 * No direct access.
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Integration helper for the builder.
 *
 * @since 4.0.0
 */
class BuilderIntegrations {
	public static function getIntegrations(): array {
		return [ 
				'content' => [ 
						'title' => Text::_ ( "COM_JPAGEBUILDER_JOOMLA_ARTICLE" ),
						'group' => 'content',
						'name' => 'jpagebuilder',
						'view' => 'article',
						'id_alias' => 'id'
				]
		];
	}
}
