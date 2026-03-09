<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Language\Text;

// no direct accees
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JpagebuilderConfig::addonConfig ( [ 
		'type' => 'general',
		'addon_name' => 'particles',
		'title' => Text::_ ( 'Particles' ),
		'desc' => Text::_ ( 'A simple and easy to use addon for creating particles' ),
		'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M18.488,12.285 L16.205,16.237 C15.322,15.496 14.185,15.281 13.303,15.791 C12.428,16.289 12.047,17.373 12.246,18.5 L7.735,18.5 C7.938,17.374 7.553,16.299 6.684,15.791 C5.801,15.27 4.655,15.492 3.773,16.237 L1.5,12.285 C2.573,11.871 3.317,10.999 3.317,9.991 C3.305,8.98 2.573,8.121 1.5,7.716 L3.765,3.784 C4.645,4.516 5.794,4.738 6.687,4.232 C7.555,3.722 7.939,2.637 7.735,1.5 L12.263,1.5 C12.072,2.637 12.441,3.71 13.314,4.22 C14.206,4.73 15.343,4.516 16.225,3.794 L18.487,7.714 C17.404,8.117 16.661,8.988 16.67,10.009 C16.672,11.018 17.415,11.88 18.488,12.285 L18.488,12.285 Z"></path><circle class="blackcolor" fill="none" stroke="#000" cx="9.997" cy="10" r="3.31"></circle></svg>',
		'category' => 'Interface',
		'settings' => [ 
				'particle_list_items' => [ 
						'title' => Text::_ ( 'Particle ID(s)' ),
						'fields' => [ 
								'ui_particle_list_items' => [ 
										'type' => 'repeatable',
										'title' => Text::_ ( 'Items' ),
										'attr' => [ 
												'type' => [ 
														'type' => 'select',
														'title' => Text::_ ( 'Style' ),
														'values' => [ 
																'' => Text::_ ( 'Default' ),
																'nasa' => Text::_ ( 'Nasa' ),
																'bubble' => Text::_ ( 'Bubble' ),
																'snow' => Text::_ ( 'Snow' ),
																'nyancat2' => Text::_ ( 'Nyan' )
														],
														'std' => ''
												],

												'title' => [ 
														'type' => 'text',
														'title' => Text::_ ( 'Section CSS selector' ),
														'desc' => 'Enter the CSS selector for the element you wish to apply the particle effect, you can select an element by ID or by class name. You need to define the element for the section to which to apply the effect in page builder as a separate block.',
														'std' => ''
												],

												'value' => [ 
														'type' => 'number',
														'title' => Text::_ ( 'Value' ),
														'desc' => 'Default: 80, Nasa: 160, Bubble: 160. Snow: 400, Nyan: 400',
														'placeholder' => '80',
														'std' => '80',
														'depends' => [ 
																[ 
																		'title',
																		'!=',
																		''
																]
														]
												],

												'color' => [ 
														'type' => 'color',
														'title' => Text::_ ( 'Color' ),
														'desc' => 'Default: #ffffff, Nasa: #ffffff, Bubble: #1b1e34. Snow: #ffffff, Nyan: #ffffff',
														'std' => '',
														'depends' => [ 
																[ 
																		'title',
																		'!=',
																		''
																]
														]
												],

												'shape' => [ 
														'type' => 'select',
														'title' => Text::_ ( 'Shape' ),
														'desc' => 'Shape types: "circle", "edge", "triangle", Style Bubble: Polygon, "star"',
														'values' => [ 
																'circle' => Text::_ ( 'Circle' ),
																'edge' => Text::_ ( 'Edge' ),
																'triangle' => Text::_ ( 'Triangle' ),
																'polygon' => Text::_ ( 'Polygon' ),
																'star' => Text::_ ( 'Star' )
														],
														'std' => 'circle',
														'inline' => true,
														'depends' => [ 
																[ 
																		'title',
																		'!=',
																		''
																]
														]
												],

												'size' => [ 
														'type' => 'number',
														'title' => Text::_ ( 'Size' ),
														'desc' => 'Default: 3, Nasa: 3, Bubble: 20. Snow: 10, Nyan: 4',
														'placeholder' => '3',
														'std' => '3',
														'depends' => [ 
																[ 
																		'title',
																		'!=',
																		''
																]
														]
												],

												'line_linked' => [ 
														'type' => 'color',
														'title' => Text::_ ( 'Line Linked' ),
														'desc' => 'Default: #ffffff, Nasa: #ffffff, Bubble: #1b1e34. Snow: #ffffff, Nyan: #ffffff',
														'std' => '',
														'depends' => [ 
																[ 
																		'title',
																		'!=',
																		''
																]
														]
												],

												'speed' => [ 
														'type' => 'number',
														'title' => 'Speed of particles movement',
														'desc' => 'Enter the speed of particles movement. Default Style: Default - 6, Nasa - 1, Bubble - 8, Nyan - 14, Snow - 6.',
														'placeholder' => '6',
														'std' => '6',
														'depends' => [
																[
																		'title',
																		'!=',
																		''
																]
														]
												],

												'outmode' => [ 
														'type' => 'select',
														'title' => 'Out Mode',
														'desc' => 'Choose the mode when particles touch the edge',
														'values' => [ 
																'out' => Text::_ ( 'Out' ),
																'bounce' => Text::_ ( 'Bounce' )
														],
														'inline' => true,
														'std' => 'out',
														'depends' => [
																[
																		'title',
																		'!=',
																		''
																]
														]
												],

												'direction' => [ 
														'type' => 'select',
														'title' => 'Direction',
														'desc' => 'Choose the direction mode when particles appear: "none, top, top-right, right, bottom-right, bottom, bottom-left, left, top-left',
														'values' => [ 
																'' => Text::_ ( 'None' ),
																'top' => Text::_ ( 'Top' ),
																'top-right' => Text::_ ( 'Top Right' ),
																'right' => Text::_ ( 'Right' ),
																'bottom-right' => Text::_ ( 'Bottom Right' ),
																'bottom' => Text::_ ( 'Bottom' ),
																'bottom-left' => Text::_ ( 'Bottom Left' ),
																'left' => Text::_ ( 'Left' ),
																'top-left' => Text::_ ( 'Top Left' )
														],
														'inline' => true,
														'std' => '',
														'depends' => [
																[
																		'title',
																		'!=',
																		''
																]
														]
												]
										]
								]
						]
				]
		]
] );
