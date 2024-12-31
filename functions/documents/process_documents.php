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
    $slider_site = $_POST['slider_site'];

    if($action == 'update'){
        $query = "UPDATE documents SET `title` = :slider_title, `text` = :slider_text, `site` = :slider_site WHERE id = :id";

        $params = [
            'slider_title' => $slider_title,
            'slider_text' => $slider_text,
            'slider_site' => $slider_site,
            'id' => $id,
        ];

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }elseif($action == 'insert'){
        $query = "INSERT INTO documents (`title`, `text`, `site`, `type`) VALUES (:slider_title, :slider_text, :slider_site, :kvkk_type)";

        $params = [
            'slider_title' => $slider_title,
            'slider_text' => $slider_text,
            'slider_site' => $slider_site,
            'kvkk_type' => $type,
        ];

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }


} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $type == 'gss'){

    $id = $_POST['id'];
    $gss_title = $_POST['gss_title'];
    $gss_text = $_POST['gss_text'];
    $gss_site = $_POST['gss_site'];

    if($action == 'update'){
        $query = "UPDATE documents SET `title` = :gss_title, `text` = :gss_text, `site` = :gss_site WHERE id = :id";

        $params = [
            'gss_title' => $gss_title,
            'gss_text' => $gss_text,
            'gss_site' => $gss_site,
            'id' => $id,
        ];

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }elseif($action == 'insert'){
        $query = "INSERT INTO documents (`title`, `text`, `site`, `type`) VALUES (:gss_title, :gss_text, :gss_site, :gss_type)";

        $params = [
            'gss_title' => $gss_title,
            'gss_text' => $gss_text,
            'gss_site' => $gss_site,
            'gss_type' => $type,
        ];

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $type == 'ss'){

    $id = $_POST['id'];
    $ss_title = $_POST['ss_title'];
    $ss_text = $_POST['ss_text'];
    $ss_site = $_POST['ss_site'];

    if($action == 'update'){
        $query = "UPDATE documents SET `title` = :ss_title, `text` = :ss_text, `site` = :ss_site WHERE id = :id";

        $params = [
            'ss_title' => $ss_title,
            'ss_text' => $ss_text,
            'ss_site' => $ss_site,
            'id' => $id,
        ];

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }elseif($action == 'insert'){
        $query = "INSERT INTO documents (`title`, `text`, `site`, `type`) VALUES (:ss_title, :ss_text, :ss_site, :ss_type)";

        $params = [
            'ss_title' => $ss_title,
            'ss_text' => $ss_text,
            'ss_site' => $ss_site,
            'ss_type' => $type,
        ];

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }

}


?>
