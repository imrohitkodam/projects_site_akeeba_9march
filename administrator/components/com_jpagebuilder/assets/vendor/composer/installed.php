<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

return array(
    'root' => array(
        'name' => 'storejextensions/jpagebuilder',
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'reference' => '89d3386326e9224ce73002fdd41f173b4b133a9b',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'facebook/graph-sdk' => array(
            'pretty_version' => '5.7.0',
            'version' => '5.7.0.0',
            'reference' => '2d8250638b33d73e7a87add65f47fabf91f8ad9b',
            'type' => 'library',
            'install_path' => __DIR__ . '/../facebook/graph-sdk',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'storejextensions/jpagebuilder' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '89d3386326e9224ce73002fdd41f173b4b133a9b',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
