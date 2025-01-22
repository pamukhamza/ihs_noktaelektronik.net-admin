<?php
include_once '../db.php';
include_once '../functions.php';

$database = new Database();
$type = $_POST['type'];
$action = $_POST['action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $type === 'kvkk') {
    $id = $_POST['id'];
    $slider_title = $_POST['slider_title'];
    $slider_text = $_POST['slider_text'];
    $slider_img = !empty($_FILES['slider_photo']['name']) ? $_FILES['slider_photo']['name'] : null;

    if ($action == 'update') {
        $query = "UPDATE indata_projects SET `p_name` = :p_name, `p_desc` = :p_desc" . (empty($slider_img) ? "" : ", `p_image` = :p_image") . " WHERE id = :id";

        $params = [
            'p_name' => $slider_title,
            'p_desc' => $slider_text,
            'id' => $id,
        ];

        if (!empty($slider_img)) {
            $img = validateAndSaveImage($_FILES['slider_photo'], '../../assets/images/');
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['p_image'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    } elseif ($action == 'insert') {
        $query = "INSERT INTO indata_projects (`aktif`, `p_name`, `p_desc`" . (empty($slider_img) ? "" : ", `p_image`") . ") VALUES (1, :p_name, :p_desc" . (empty($slider_img) ? "" : ", :p_image") . ")";

        $params = [
            'p_name' => $slider_title,
            'p_desc' => $slider_text
        ];

        if (!empty($slider_img)) {
            $img = validateAndSaveImage($_FILES['slider_photo'], '../../assets/images/');
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['p_image'] = $img;
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }
}
?>