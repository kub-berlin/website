#!/usr/bin/env php
<?php declare(strict_types=1);
include_once(__DIR__ . '/../datasource.php');

/**
 * This script adds a 'twingle_id' column to the 'pages' table and fills it
 * with the twingle ID's of the respective campaign-related layouts.
 * It also updates the layout names of the 'pages' entries
 * to 'default'.
 *
 * Run in terminal via `php console/update-campaigns.php`
 */


const CAMPAIGN_LAYOUTS = ["spenden", "spenden-ccv", "foerderkreis", "foerderkreis-briefaktion"];

const VALUES = [
    ['slug' => 'spenden', 'twingle_id' => 'kub-spenden-allgemein/tw64df2b7d9f960'],
    ['slug' => 'ccvossel', 'twingle_id' => 'kub-ccvossel/tw65fc34564f0c6'],
    ['slug' => 'foerderkreis', 'twingle_id' => 'foerderkreis/tw656d9a25844ef'],
    ['slug' => 'foerderkreis-briefaktion', 'twingle_id' => 'foerderkreis-briefaktion/tw684bcdc396b26'],
];

$db->query("DROP TABLE IF EXISTS campaigns");
//$db->query("ALTER TABLE pages DROP COLUMN twingle_id;"); // reset for testing purposes


// add twingle_id column to pages table
$db->query("ALTER TABLE pages ADD twingle_id VARCHAR(50);");

// fetch pages with campaign layouts
$placeholders = implode(',', array_fill(0, count(CAMPAIGN_LAYOUTS), '?'));
$sql = "SELECT id, layout, slug FROM pages WHERE layout IN ($placeholders)";
$stmt = $db->prepare($sql);
$stmt->execute(CAMPAIGN_LAYOUTS);
$donatePages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($donatePages) < 1) {
    echo "No campaign layouts found.\nExiting without changes.\n";
}

foreach ($donatePages as $page) {
    echo "Updating page with slug '" . $page['slug'] . "'...\n";
    try {
        // update layout names in pages table to 'default' layout
        echo "  setting 'default' layout...\n";
        $renameStmt = $db->prepare("UPDATE pages SET layout=:layout WHERE id=:id;");
        $renameStmt->execute(['layout' => 'default', 'id' => $page['id']]);

        // write twingle_id values for existing campaign pages
        echo "  writing twingle_id...\n";
        $updateStmt = $db->prepare("UPDATE pages SET twingle_id=:twingle_id WHERE id=:id;");
        foreach (VALUES as $row) {
            if( $row['slug'] === $page['slug'] ) {
                $row['id'] = $page['id'];
                unset($row['slug']);
                $updateStmt->execute($row);
                $updateStmt->closeCursor();
            }
        }
    } catch (PDOException $e) {
        echo "\nError updating page entry: " . $e->getMessage() . "\n";
    }
}

echo "\nDone.\n";
exit(0);
?>