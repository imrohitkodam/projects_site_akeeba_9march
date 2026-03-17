<?php
/**
 * Test AJAX endpoint directly
 * Access via browser: http://yoursite.com/test_ajax.php?country=99
 */

define('_JEXEC', 1);
define('JPATH_BASE', __DIR__);

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

use Joomla\CMS\Factory;

// Start the application
$app = Factory::getApplication('site');

// Get country ID from URL
$countryId = isset($_GET['country']) ? (int) $_GET['country'] : 99; // Default India

echo "<h2>Testing TJVendors AJAX Endpoints</h2>";
echo "<p>Testing with Country ID: $countryId</p>";

// Load TJVendors utilities
require_once JPATH_ROOT . '/components/com_tjvendors/tjvendors.php';

try {
    $utilitiesObj = TJVendors::utilities();
    
    echo "<h3>1. Testing getRegions()</h3>";
    $regions = $utilitiesObj->getRegions($countryId);
    
    if ($regions) {
        echo "<p>✓ Found " . count($regions) . " regions</p>";
        echo "<pre>" . print_r(array_slice($regions, 0, 3), true) . "</pre>";
    } else {
        echo "<p>✗ No regions found</p>";
    }
    
    echo "<h3>2. Testing getCities()</h3>";
    $cities = $utilitiesObj->getCities($countryId);
    
    if ($cities) {
        echo "<p>✓ Found " . count($cities) . " cities</p>";
        echo "<pre>" . print_r(array_slice($cities, 0, 3), true) . "</pre>";
    } else {
        echo "<p>✗ No cities found</p>";
    }
    
    echo "<h3>3. Testing JSON Response Format (like AJAX)</h3>";
    
    // Simulate what the controller does
    $defaultRegion = array(
        "id"           => '',
        "region"       => 'Select an option',
        "region_jtext" => 'Select an option'
    );
    
    if (!empty($regions)) {
        array_unshift($regions, $defaultRegion);
    } else {
        $regions[] = $defaultRegion;
    }
    
    $jsonResponse = new \Joomla\CMS\Response\JsonResponse($regions);
    echo "<p>JSON Response:</p>";
    echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h3>Test Different Countries:</h3>";
echo "<ul>";
echo "<li><a href='?country=99'>India (99)</a></li>";
echo "<li><a href='?country=223'>USA (223)</a></li>";
echo "<li><a href='?country=222'>UK (222)</a></li>";
echo "<li><a href='?country=1'>Afghanistan (1)</a></li>";
echo "</ul>";
