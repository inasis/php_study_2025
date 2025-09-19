<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Citrus\Database;

$db = new Database();

// Create
$create = $db->create("새로운 제목", "새로운 컨텐츠");
echo "<div>1. Created Post: {$create->title}</div><br />";

// Read
$read = $db->read($create->id);
echo "<div>2. Read Post: <div style='font-size:1.2em;font-weight:bold;'>{$read->title}</div><div>{$read->content}</div></div><br />";

// Update
$db->update($create->id, ['title' => '변경된 제목']);
$update = $db->read($create->id);
echo "<div>3. Updated Title: <div style='font-size:1.2em;font-weight:bold;'>{$update->title}</div><div>{$update->content}</div></div><br />";

// Delete
$db->delete($create->id);
$delete = $db->read($create->id);
echo "4. Deleted? " . ($delete ? "No" : "Yes") . "\n";
