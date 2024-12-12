<?php
include_once '../../../functions/db.php';
include_once '../functions.php';
$database = new Database();
$action = $_POST['action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'settings') {
    $id = 1;
    $title = $_POST['site_title'];
    $favicon = !empty($_FILES['favicon_img']['name']) ? $_FILES['favicon_img']['name'] : null;
    if(!empty($favicon)){

        $img = validateAndSaveImage($_FILES['favicon_img'], '../../assets/images/site/');

        $query = "UPDATE settings SET 
                 `site_title` = :title, 
                 `favicon` = :favicon
             WHERE id = :id";
        $params = [
            'title' => $title,
            'favicon' => $img,
            'id' => $id
        ];
        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }else {
        $query = "UPDATE settings SET 
                 `site_title` = :title
             WHERE id = :id";
        $params = [
            'title' => $title,
            'id' => $id
        ];
        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }


} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action == 'socials'){

    $id = 1;
    $twitter = $_POST['twitter'];
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $linkedin = $_POST['linkedin'];
    $youtube = $_POST['youtube'];

    // `key` rezerve kelime olduğundan tırnak içine alıyoruz
    $query = "UPDATE settings SET 
                 `twitter` = :twitter, 
                 `facebook` = :facebook, 
                 `instagram` = :instagram,
                 `linkedin` = :linkedin,
                 `youtube` = :youtube
             WHERE id = :id";
    $params = [
        'twitter' => $twitter,
        'facebook' => $facebook,
        'instagram' => $instagram,
        'linkedin' => $linkedin,
        'youtube' => $youtube,
        'id' => $id
    ];

    if ($database->update($query, $params)) {
        echo "Kayıt başarıyla güncellendi.";
    } else {
        echo "Güncelleme sırasında hata oluştu.";
    }

}


?>
