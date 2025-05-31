<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP Info and Debug Information</h1>";

echo "<h2>Basic PHP Info</h2>";
phpinfo(INFO_GENERAL);

echo "<h2>Request Information</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "</pre>";

echo "<h2>File System Check</h2>";
echo "<pre>";
echo "Current file: " . __FILE__ . "\n";
echo "File exists: " . (file_exists(__FILE__) ? 'Yes' : 'No') . "\n";
echo "File readable: " . (is_readable(__FILE__) ? 'Yes' : 'No') . "\n";
echo "</pre>";
?>
