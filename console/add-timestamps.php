#!/usr/bin/env php
<?php declare(strict_types=1);
include_once(__DIR__ . '/../datasource.php');

/** 
 * This script adds a timestamp 'updated_at' column to the 'translations' table.
 * 
 * Run in terminal via `php console/add-timestamps.php` 
 */


try {
    $db->query("ALTER TABLE translations ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;");
} catch (PDOException $e) {
    echo $e->getMessage();
}
echo "\nDone.\n";
exit(0);
?>