    <?php 

include_once '../../db.php';
$database = new Database();
$vid = $_POST['id'];
$vAdi = $_POST['ad'];
$vUrunler = $_POST['urun_id'];

global $db;

$query = "UPDATE b2b_kampanyalar SET ad = :ad, urun_id = :u_id WHERE id = :id";
$params = [
    'ad' => $vAdi,
    'u_id' => $vUrunler,
    'id' => $vid
];
$database->update($query, $params);


?>