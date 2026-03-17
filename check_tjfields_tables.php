<?php
/**
 * Diagnostic script to check TJFields tables
 * Run this from your Joomla root directory
 */

define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	require_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

use Joomla\CMS\Factory;

// Create a JConfig object
$config = new JConfig();

// Get database instance
$option = array(
    'driver'   => $config->dbtype,
    'host'     => $config->host,
    'user'     => $config->user,
    'password' => $config->password,
    'database' => $config->db,
    'prefix'   => $config->dbprefix
);

$db = \Joomla\Database\DatabaseDriver::getInstance($option);
$db = Factory::getDbo();

echo "=== TJFields Tables Diagnostic ===\n\n";

// Check if tables exist
$tables = ['#__tj_country', '#__tj_region', '#__tj_city'];

foreach ($tables as $table) {
    $query = "SHOW TABLES LIKE " . $db->quote(str_replace('#__', $db->getPrefix(), $table));
    $db->setQuery($query);
    $result = $db->loadResult();
    
    if ($result) {
        echo "✓ Table $table exists\n";
        
        // Count records
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from($table);
        $db->setQuery($query);
        $count = $db->loadResult();
        echo "  Records: $count\n";
        
        // Check com_tjvendors column
        if ($table !== '#__tj_city') {
            $query = "SHOW COLUMNS FROM " . str_replace('#__', $db->getPrefix(), $table) . " LIKE 'com_tjvendors'";
            $db->setQuery($query);
            $column = $db->loadResult();
            
            if ($column) {
                echo "  ✓ com_tjvendors column exists\n";
                
                // Count enabled records for com_tjvendors
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from($table);
                $query->where('com_tjvendors = 1');
                $db->setQuery($query);
                $enabledCount = $db->loadResult();
                echo "  Records enabled for com_tjvendors: $enabledCount\n";
            } else {
                echo "  ✗ com_tjvendors column MISSING\n";
            }
        }
    } else {
        echo "✗ Table $table DOES NOT EXIST\n";
    }
    echo "\n";
}

// Test getting countries for com_tjvendors
echo "=== Testing TjGeoHelper ===\n\n";

$helperPath = JPATH_ROOT . '/components/com_tjfields/helpers/geo.php';
if (file_exists($helperPath)) {
    echo "✓ TjGeoHelper file exists\n";
    
    require_once $helperPath;
    
    try {
        $geoHelper = new TjGeoHelper();
        $countries = $geoHelper->getCountryList('com_tjvendors');
        
        echo "✓ getCountryList() works\n";
        echo "  Countries found: " . count($countries) . "\n\n";
        
        if (count($countries) > 0) {
            echo "Sample country: " . print_r($countries[0], true) . "\n";
            
            // Test getting regions for first country
            $firstCountryId = $countries[0]['id'];
            $regions = $geoHelper->getRegionList($firstCountryId, 'com_tjvendors');
            echo "Regions for country ID $firstCountryId: " . count($regions) . "\n";
            
            // Test getting cities
            $cities = $geoHelper->getCityList($firstCountryId, 'com_tjvendors');
            echo "Cities for country ID $firstCountryId: " . count($cities) . "\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ TjGeoHelper file NOT FOUND at: $helperPath\n";
}

echo "\n=== End Diagnostic ===\n";
