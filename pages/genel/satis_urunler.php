<?php 
include_once '../../functions/db.php';
require '../../functions/admin_template.php';
$currentPage = 'b2b-uyeler';
$template = new Template('Üyeler - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();
?>YAKINDA YAPILACAK