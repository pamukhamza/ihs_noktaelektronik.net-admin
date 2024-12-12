<?php
include_once 'user.php';
$user = new User($database);

$user->logout();

?>
