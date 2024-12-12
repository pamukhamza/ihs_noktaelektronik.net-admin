<?php
include_once '../db.php';
include_once '../functions.php';
$database = new Database();
$type = $_POST['type'];
$action = $_POST['action'];

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


} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $type == 'banner'){

    $id = $_POST['id'];
    $banner_link = $_POST['banner_link'];
    $banner_img = !empty($_FILES['banner_img']['name']) ? $_FILES['banner_img']['name'] : null;

    if($action == 'update') {
        $query = "UPDATE banner SET `banner_link` = :banner_link" . (empty($banner_img) ? "" : ", `banner_photo` = :banner_photo") . " WHERE id = :id";

        $params = [
            'banner_link' => $banner_link,
            'id' => $id,
        ];

        if (!empty($banner_img)) {
            $img = validateAndSaveImage($_FILES['banner_img'], '../../assets/images/index/');
            $params['banner_photo'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }elseif($action == 'insert'){
        $query = "INSERT INTO banner (`banner_link`" . (empty($banner_img) ? "" : ", `banner_photo`") . ") VALUES (:banner_link" . (empty($banner_img) ? "" : ", :banner_photo") . ")";

        $params = [
            'banner_link' => $banner_link,
        ];

        if (!empty($banner_img)) {
            $img = validateAndSaveImage($_FILES['banner_img'], '../../assets/images/index/');
            $params['banner_photo'] = $img;
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }

    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $type == 'poster'){

    $id = 1;
    $text = $_POST['text'];
    $sm_text = $_POST['sm_text'];
    $bb_text = $_POST['bb_text'];
    $b_link = $_POST['b_link'];
    $b_text = $_POST['b_text'];

    $text_cn = $_POST['text_cn'];
    $sm_text_cn = $_POST['sm_text_cn'];
    $bb_text_cn = $_POST['bb_text_cn'];
    $b_text_cn = $_POST['b_text_cn'];

    // `key` rezerve kelime olduğundan tırnak içine alıyoruz
    $query = "UPDATE poster SET 
                 `text` = :text, 
                 `small_text` = :small_text, 
                 `black_box_text` = :black_box_text,
                 `button_link` = :button_link,
                 `button_text` = :button_text,
                 `text_cn` = :text_cn, 
                 `small_text_cn` = :small_text_cn, 
                 `black_box_text_cn` = :black_box_text_cn,
                 `button_text_cn` = :button_text_cn
             WHERE id = :id";
    $params = [
        'text' => $text,
        'small_text' => $sm_text,
        'black_box_text' => $bb_text,
        'button_link' => $b_link,
        'button_text' => $b_text,
        'text_cn' => $text_cn,
        'small_text_cn' => $sm_text_cn,
        'black_box_text_cn' => $bb_text_cn,
        'button_text_cn' => $b_text_cn,
        'id' => $id
    ];

    if ($database->update($query, $params)) {
        echo "Kayıt başarıyla güncellendi.";
    } else {
        echo "Güncelleme sırasında hata oluştu.";
    }

}


?>
