#!/usr/bin/env php
<?php declare(strict_types=1);
include_once(__DIR__ . '/../datasource.php');

/** 
 * This script creates and fills the 'campaigns' table 
 * with the twingle ID's of the respective campaign-related layouts.
 * It also updates the layout names of the 'pages' entries
 * to a unified 'spenden' layout.
 * 
 * Run in terminal via `php console/update-campaigns.php` 
 */

const CAMPAIGN_LAYOUTS = ["spenden", "spenden-ccv", "foerderkreis", "foerderkreis-briefaktion"];

// add new campaigns table
$db->query('DROP TABLE IF EXISTS campaigns;');
$db->query("CREATE TABLE IF NOT EXISTS campaigns (
                id INTEGER PRIMARY KEY /*!40101 AUTO_INCREMENT */,
                page INTEGER NOT NULL,
                twid VARCHAR(50) NOT NULL,
                FOREIGN KEY (page) REFERENCES pages(id) ON DELETE CASCADE,
                UNIQUE (page, twid)
            );");

// fetch pages with campaign layouts
$placeholders = implode(',', array_fill(0, count(CAMPAIGN_LAYOUTS), '?'));
$sql = "SELECT id, layout, slug FROM pages WHERE layout IN ($placeholders)";
$stmt = $db->prepare($sql);
$stmt->execute(CAMPAIGN_LAYOUTS);
$donatePages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($donatePages) < 1) {
    echo "No campaign layouts found.\nExiting without changes.\n";
}

echo "Found campaign layouts in 'pages' table:\n";
foreach ($donatePages as $donatePage) {
    echo "  ID: " . $donatePage['id'] . ", Layout: " . $donatePage['layout'] . ", Slug: " . $donatePage['slug'] . "\n";
}

echo "\nInserting entries to 'campaigns' table...\n";

// prepare data for campaigns table
$values = [];
foreach ($donatePages as $page) {
    switch ($page['slug']) {
        case 'spenden':
            $values[] = ['page' => $page['id'], 'twid' => 'kub-spenden-allgemein/tw64df2b7d9f960'];
            break;
        case 'ccvossel':
            $values[] = ['page' => $page['id'], 'twid' => 'kub-ccvossel/tw65fc34564f0c6'];
            break;
        case 'foerderkreis':
            $values[] = ['page' => $page['id'], 'twid' => 'foerderkreis/tw656d9a25844ef'];
            break;
        case 'foerderkreis-briefaktion':
            $values[] = ['page' => $page['id'], 'twid' => 'foerderkreis-briefaktion/tw684bcdc396b26'];
            break;
        default:
            break;
    }
}

// insert data into campaigns table
try {
    $stmt = $db->prepare("INSERT INTO campaigns (page, twid) VALUES (:page, :twid);");
    foreach ($values as $row) {
        echo "  Writing twingle ID '" . $row['twid'] . "' for page ID '" . $row['page'] . "'...\n";
        $stmt->execute($row);
    }
} catch (PDOException $e) {
    echo "\nError inserting campaigns: " . $e->getMessage() . "\n";
    exit(0);
}

// update layout names in pages table to unified 'spenden' layout
echo "\nUpdating campaign layouts in 'pages' table...\n";

foreach ($donatePages as $page) {
    $newLayout = 'spenden';
    echo "  Updating page ID " . $page['id'] . " layout from '" . $page['layout'] . "' to '" . $newLayout . "'...\n";
    $updateStmt = $db->prepare("UPDATE pages SET layout=:layout WHERE id=:id;");
    try {
        $updateStmt->execute(['layout' => $newLayout, 'id' => $page['id']]);
    } catch (PDOException $e) {
        echo "\nError updating layout: " . $e->getMessage() . "\n";
    }
}

echo "\nDone.\n";
exit(0);
?>