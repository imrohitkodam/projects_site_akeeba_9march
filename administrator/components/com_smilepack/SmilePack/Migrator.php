<?php

/**
 * @package         Smile Pack
 * @version         2.1.1 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace SmilePack;

defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;
use NRFramework\File;
use Joomla\CMS\Factory;

class Migrator
{
    /**
     * The database class
     *
     * @var object
     */
    private $db;

    /**
     * The destination database class
     * 
     * @var object
     */
    private $dbDestination;

    /**
     * Indicates the current installed version of the extension
     *
     * @var string
     */
    private $installedVersion;
    
    /**
     * Class constructor
     *
     * @param string $installedVersion  The current extension version
     */
    public function __construct($installedVersion, $dbSource = null, $dbDestination = null)
    {
        $this->db = $dbSource ? $dbSource : Factory::getDbo();
        $this->dbDestination = $dbDestination ? $dbDestination : Factory::getDbo();
        $this->installedVersion = $installedVersion;
    }
    
    /**
     * Start the migration process
     *
     * @return void
     */
    public function start()
    {
        $this->migrateGalleryModules();
    }

    private function migrateGalleryModules()
    {
        if (version_compare($this->installedVersion, '1.1.2', '>')) 
        {
            return;
        }

        if (!$data = $this->getModules('mod_spgallery'))
        {
            return;
        }

        $ds = DIRECTORY_SEPARATOR;
        
        foreach ($data as $key => $module)
        {
            $module->params = new Registry($module->params);

            // Set the masonry columns
            $module->params->set('masonry_columns', $module->params->get('columns'));

            // Update the thumbnails size (thumbnails)
            if ($module->params->get('thumb_width', '') || $module->params->get('thumb_height', ''))
            {
                $module->params->set('thumbnail_size', $module->params->get('thumb_width', ''));
                $module->params->set('masonry_thumbnails_width', $module->params->get('thumb_width', ''));
                $module->params->set('slideshow_thumbnail_size', $module->params->get('thumb_width', ''));
            }

            // Update full image size (full_image)
            if ($module->params->get('original_image_resize_width', '') || $module->params->get('original_image_resize_height', ''))
            {
                $by = 'custom';
                if ($module->params->get('original_image_resize_width', '') && !$module->params->get('original_image_resize_height', ''))
                {
                    $by = 'width';
                }
                else if (!$module->params->get('original_image_resize_width', '') && $module->params->get('original_image_resize_height', ''))
                {
                    $by = 'height';
                }
                else if (!$module->params->get('original_image_resize_width', '') && !$module->params->get('original_image_resize_height', ''))
                {
                    $by = 'disabled';
                }
                $module->params->set('full_image', [
                    'by' => $by,
                    'width' => $module->params->get('original_image_resize_width', ''),
                    'height' => $module->params->get('original_image_resize_height', '')
                ]);
                
                // Update the slideshow image size (slideshow_image)
                $slideshow_image_width = $module->params->get('original_image_resize_width', '');
                $slideshow_image_height = $module->params->get('original_image_resize_height', '');
    
                if (!$slideshow_image_width && !$slideshow_image_height)
                {
                    $by = 'custom';
                    $slideshow_image_width = 600;
                    $slideshow_image_height = 300;
                }
                
                $module->params->set('slideshow_image', [
                    'by' => $by,
                    'width' => $slideshow_image_width,
                    'height' => $slideshow_image_height
                ]);
            }

            // Remove old keys
            $module->params->remove('thumb_width');
            $module->params->remove('thumb_height');
            $module->params->remove('original_image_resize_width');
            $module->params->remove('original_image_resize_height');

            // Update module using id as the primary key.
            $module->params = json_encode($module->params);

            $this->dbDestination->updateObject('#__modules', $module, 'id');
        }
    }
    
    /**
     * Get all modules given its type from the database.
     * 
     * @param   string  $module  The module type
     *
     * @return  array
     */
    private function getModules($module = '')
    {
        if (!$module)
        {
            return;
        }
        
        $db = $this->db;
    
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__modules')
            ->where($db->qn('module') . ' = ' . $db->q($module));
        
        $db->setQuery($query);
    
        return $db->loadObjectList();
    }
}