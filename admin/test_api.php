<?php
$_POST['action'] = 'get_sections';
session_start();
$_SESSION['admin_id'] = 1;
require 'api.php';
