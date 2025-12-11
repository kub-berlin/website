#!/usr/bin/env php
<?php declare(strict_types=1);
include_once(__DIR__ . '/../datasource.php');

/** 
 * This script adds a 'twid' column to the 'pages' table and fills it
 * with the twingle ID's of the respective campaign-related layouts.
 * It also updates the layout names of the 'pages' entries
 * to a unified 'spenden' layout.
 * 
 * Run in terminal via `php console/update-campaigns.php` 
 */


const CAMPAIGN_LAYOUTS = ["spenden", "spenden-ccv", "foerderkreis", "foerderkreis-briefaktion"];

const VALUES = [
    ['slug' => 'spenden', 'twid' => 'kub-spenden-allgemein/tw64df2b7d9f960'],
    ['slug' => 'ccvossel', 'twid' => 'kub-ccvossel/tw65fc34564f0c6'],
    ['slug' => 'foerderkreis', 'twid' => 'foerderkreis/tw656d9a25844ef'],
    ['slug' => 'foerderkreis-briefaktion', 'twid' => 'foerderkreis-briefaktion/tw684bcdc396b26'],
];

$db->query("DROP TABLE IF EXISTS campaigns");
//$db->query("ALTER TABLE pages DROP COLUMN twid;"); // reset for testing purposes

// fetch pages with campaign layouts
$placeholders = implode(',', array_fill(0, count(CAMPAIGN_LAYOUTS), '?'));
$sql = "SELECT id, layout, slug FROM pages WHERE layout IN ($placeholders)";
$stmt = $db->prepare($sql);
$stmt->execute(CAMPAIGN_LAYOUTS);
$donatePages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($donatePages) < 1) {
    echo "No campaign layouts found.\nExiting without changes.\n";
}

// update layout names in pages table to unified 'spenden' layout
echo "\nUnifying campaign layouts to 'spenden'...\n";
foreach ($donatePages as $page) {
    try {
        $renameStmt = $db->prepare("UPDATE pages SET layout=:layout WHERE id=:id;");
        $renameStmt->execute(['layout' => 'spenden', 'id' => $page['id']]);
    } catch (PDOException $e) {
        echo "\nError updating layout: " . $e->getMessage() . "\n";
    }
}

// add twid column to pages table
echo "Adding new 'twid' column to the 'pages' table...\n";
$db->query("ALTER TABLE pages ADD twid VARCHAR(50);");

// write twid values for existing campaign pages
$updateStmt = $db->prepare("UPDATE pages SET twid=:twid WHERE slug=:slug AND layout=:layout;");
echo "Writing 'twid' values to 'pages' table...\n";
try {
    foreach (VALUES as $row) {
        $row['layout'] = 'spenden';
        $updateStmt->execute($row);
        $updateStmt->closeCursor();
    }
} catch (PDOException $e) {
    echo "\nError writing twingle IDs: " . $e->getMessage() . "\n";
    exit(0);
}

echo "\nDone.\n";
exit(0);
?>