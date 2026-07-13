<?php
require_once 'auth.php';
requireLogin();
if (isset($_GET['logout'])) { logout(); }
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikasa Admin — <?= ucfirst($currentPage) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
    <?php if (file_exists("css/{$currentPage}.css")): ?>
        <link rel="stylesheet" href="css/<?= $currentPage ?>.css">
    <?php endif; ?>
</head>
<body class="admin-body">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-topbar" id="admin-topbar">
        <span class="sb-name">Mikasa CMS</span>
        <div class="admin-hamburger" id="admin-hamburger"><span></span><span></span><span></span></div>
    </div>
    <div class="admin-main">
        <div class="admin-header">
            <h1><?= ucfirst($currentPage) ?> Management</h1>
            <p>Changes reflect instantly on the live site.</p>
        </div>
        <div class="admin-alert hidden" id="admin-alert"></div>