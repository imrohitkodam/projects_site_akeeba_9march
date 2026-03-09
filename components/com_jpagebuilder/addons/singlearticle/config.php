<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JpagebuilderConfig::addonConfig ( [ 
	'type' => 'content',
	'addon_name' => 'singlearticle',
	'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_TITLE' ),
	'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_DESC' ),
	'icon' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path opacity=".5" d="M11.643 9.571h-.603L8.138 1.246A.363.363 0 007.804 1h-1.63a.363.363 0 00-.335.246L2.937 9.57h-.58c-.2 0-.357.179-.357.358v.714c0 .2.156.357.357.357h3.036a.367.367 0 00.357-.357v-.714a.384.384 0 00-.357-.358h-.536l.58-1.785h3.08l.604 1.785h-.514c-.2 0-.357.179-.357.358v.714c0 .2.156.357.357.357h3.036a.367.367 0 00.357-.357v-.714a.384.384 0 00-.357-.358zm-5.76-3.28l.938-2.769c.09-.357.157-.647.179-.78 0 .155.045.446.156.78l.938 2.768h-2.21z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M30 16a1 1 0 01-1 1H3a1 1 0 110-2h26a1 1 0 011 1zM30 23a1 1 0 01-1 1H3a1 1 0 110-2h26a1 1 0 011 1zM16 30a1 1 0 01-1 1H3a1 1 0 110-2h12a1 1 0 011 1zM30 9a1 1 0 01-1 1H16a1 1 0 110-2h13a1 1 0 011 1zM30 2a1 1 0 01-1 1H16a1 1 0 110-2h13a1 1 0 011 1z" fill="currentColor"/></svg>',
	'category' => 'Content',
	'settings' => [ 
		'general' => [ 
			'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SETTINGS' ),
			'fields' => [ 
				'article_id' => [ 
						'type' => 'select',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SELECT_ARTICLE' ),
						'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SELECT_ARTICLE_DESC' ),
						'values' => JpagebuilderBase::getArticlesList (),
						'std' => ''
				],
				'itemid' => [ 
						'type' => 'text',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_CUSTOM_ITEMID' ),
						'desc' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_CUSTOM_ITEMID_DESC' ),
						'std' => ''
				],
				'show_title' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_TITLE' ),
						'std' => '1'
				],
				'link_titles' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_LINK_TITLES' ),
						'std' => '0'
				],
				'show_tags' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_TAGS' ),
						'std' => '1'
				],
				'show_intro' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_INTRO' ),
						'std' => '1'
				],
				'info_block_position' => [ 
						'type' => 'select',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_INFO_POSITION' ),
						'values' => [ 
								'0' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_POSITION_ABOVE' ),
								'1' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_POSITION_BELOW' ),
								'2' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_POSITION_SPLIT' )
						],
						'std' => '0'
				],
				'info_block_show_title' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_INFO_TITLE' ),
						'std' => '1'
				],
				'show_category' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_CATEGORY' ),
						'std' => '1'
				],
				'link_category' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_LINK_CATEGORY' ),
						'std' => '1'
				],
				'show_parent_category' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_PARENT_CATEGORY' ),
						'std' => '0'
				],
				'link_parent_category' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_LINK_PARENT_CATEGORY' ),
						'std' => '0'
				],
				'show_associations' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_ASSOCIATIONS' ),
						'std' => '0'
				],
				'flags' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_USE_FLAGS' ),
						'std' => '0'
				],
				'show_author' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_AUTHOR' ),
						'std' => '1'
				],
				'link_author' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_LINK_AUTHOR' ),
						'std' => '1'
				],
				'show_create_date' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_CREATE_DATE' ),
						'std' => '1'
				],
				'show_modify_date' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_MODIFY_DATE' ),
						'std' => '1'
				],
				'show_publish_date' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_PUBLISH_DATE' ),
						'std' => '1'
				],
				'show_item_navigation' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_NAVIGATION' ),
						'std' => '0'
				],
				'show_hits' => [ 
						'type' => 'checkbox',
						'title' => Text::_ ( 'COM_JPAGEBUILDER_ADDON_SINGLEARTICLE_SHOW_HITS' ),
						'std' => '1'
				]
			]
		]
	]
] );
