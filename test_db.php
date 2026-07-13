<?php
require 'config.php';
try {
    $visitsQuery = $pdo->prepare("SELECT visit_date, COUNT(*) as count FROM page_views WHERE 1=1 GROUP BY visit_date ORDER BY visit_date ASC");
    $visitsQuery->execute();
    echo "Query OK. Rows: " . count($visitsQuery->fetchAll());
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
