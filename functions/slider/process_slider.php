<?php
include_once '../db.php';
include_once '../functions.php';
$database = new Database();
$type = $_POST['type'];
$action = $_POST['action'];
$site = $_POST['slider_site'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $type === 'slider') {
    $id = $_POST['id'];
    $slider_link = $_POST['slider_link'];
    $slider_img = !empty($_FILES['slider_img']['name']) ? $_FILES['slider_img']['name'] : null;

    if($action == 'update'){
        $query = "UPDATE slider SET `slider_link` = :slider_link" . (empty($slider_img) ? "" : ", `slider_photo` = :slider_photo") . " WHERE id = :id";

        $params = [
            'slider_link' => $slider_link,
            'id' => $id,
        ];

        if (!empty($slider_img)) {
            $img = validateAndSaveImage($_FILES['slider_img'], '../../assets/images/index/');
            $params['slider_photo'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }

    }elseif($action == 'insert'){
        $query = "INSERT INTO slider (`slider_link`" . (empty($slider_img) ? "" : ", `slider_photo`") . ") VALUES (:slider_link" . (empty($slider_img) ? "" : ", :slider_photo") . ")";

        $params = [
            'slider_link' => $slider_link,
        ];

        if (!empty($slider_img)) {
            $img = validateAndSaveImage($_FILES['slider_img'], '../../assets/images/index/');
            $params['slider_photo'] = $img;
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }


}


?>
