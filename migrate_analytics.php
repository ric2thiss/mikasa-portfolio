<?php
require_once 'config.php';

$sql = "
CREATE TABLE IF NOT EXISTS page_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    country VARCHAR(100) DEFAULT 'Unknown',
    city VARCHAR(100) DEFAULT 'Unknown',
    visit_date DATE NOT NULL,
    visit_time DATETIME NOT NULL,
    user_agent TEXT,
    UNIQUE KEY ip_date (ip_address, visit_date)
);
";

try {
    $pdo->exec($sql);
    echo "Analytics table created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating analytics table: " . $e->getMessage() . "\n";
}
