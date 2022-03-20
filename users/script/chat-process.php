<?php
require_once '../../core/init.php';
$general = new General();
$user = new User();
$show = new Show();
$db = Database::getInstance();
if (isset($_POST['action']) && $_POST['action'] == 'generate') {
    $rand = rand(11111111, 99999999);
    echo json_encode($rand);
}

