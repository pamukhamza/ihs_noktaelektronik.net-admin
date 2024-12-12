<?php
include_once '../../../functions/db.php';
$database = new Database();
$action = $_POST['action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'about') {
    $id = 1;
    $about_title = $_POST['about_title'];
    $about_title_cn = $_POST['about_title_cn'];
    $about_banner = $_POST['about_banner'];
    $about_banner_cn = $_POST['about_banner_cn'];
    $about_banner2 = $_POST['about_banner2'];
    $about_banner2_cn = $_POST['about_banner2_cn'];
    $about_message = $_POST['about_message'];
    $about_message_cn = $_POST['about_message_cn'];

    // `key` rezerve kelime olduğundan tırnak içine alıyoruz
    $query = "UPDATE about SET 
                 `title` = :about_title, 
                 `title_cn` = :about_title_cn, 
                 `banner_title` = :about_banner,
                 `banner_title_cn` = :about_banner_cn,
                 `banner_title2` = :about_banner2,
                 `banner_title2_cn` = :about_banner2_cn, 
                 `text` = :about_message, 
                 `text_cn` = :about_message_cn
             WHERE id = :id";
    $params = [
        'about_title' => $about_title,
        'about_title_cn' => $about_title_cn,
        'about_banner' => $about_banner,
        'about_banner_cn' => $about_banner_cn,
        'about_banner2' => $about_banner2,
        'about_banner2_cn' => $about_banner2_cn,
        'about_message' => $about_message,
        'about_message_cn' => $about_message_cn,
        'id' => $id
    ];

    if ($database->update($query, $params)) {
        echo "Kayıt başarıyla güncellendi.";
    } else {
        echo "Güncelleme sırasında hata oluştu.";
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action == 'contact'){

    $id = 1;
    $contact_w_hours = $_POST['contact_w_hours'];
    $contact_w_hours_cn = $_POST['contact_w_hours_cn'];
    $contact_phone = $_POST['contact_phone'];
    $contact_email = $_POST['contact_email'];
    $contact_address = $_POST['contact_address'];
    $contact_address_cn = $_POST['contact_address_cn'];

    // `key` rezerve kelime olduğundan tırnak içine alıyoruz
    $query = "UPDATE contact SET 
                 `w_hours` = :contact_w_hours, 
                 `w_hours_cn` = :contact_w_hours_cn, 
                 `phone` = :contact_phone,
                 `email` = :contact_email,
                 `address` = :contact_address,
                 `address_cn` = :contact_address_cn
             WHERE id = :id";
    $params = [
        'contact_email' => $contact_email,
        'contact_phone' => $contact_phone,
        'contact_w_hours' => $contact_w_hours,
        'contact_w_hours_cn' => $contact_w_hours_cn,
        'contact_address' => $contact_address,
        'contact_address_cn' => $contact_address_cn,
        'id' => $id
    ];

    if ($database->update($query, $params)) {
        echo "Kayıt başarıyla güncellendi.";
    } else {
        echo "Güncelleme sırasında hata oluştu.";
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action == 'policy'){

    $id = 1;
    $policy_banner = $_POST['policy_banner'];
    $policy_title = $_POST['policy_title'];
    $policy_text = $_POST['policy_text'];
    $policy_banner_cn = $_POST['policy_banner_cn'];
    $policy_title_cn = $_POST['policy_title_cn'];
    $policy_text_cn = $_POST['policy_text_cn'];

    // `key` rezerve kelime olduğundan tırnak içine alıyoruz
    $query = "UPDATE policy SET 
                 `banner` = :policy_banner, 
                 `banner_cn` = :policy_banner_cn, 
                 `title` = :policy_title,
                 `title_cn` = :policy_title_cn,
                 `text` = :policy_text,
                 `text_cn` = :policy_text_cn
             WHERE id = :id";
    $params = [
        'policy_banner' => $policy_banner,
        'policy_banner_cn' => $policy_banner_cn,
        'policy_title' => $policy_title,
        'policy_title_cn' => $policy_title_cn,
        'policy_text' => $policy_text,
        'policy_text_cn' => $policy_text_cn,
        'id' => $id
    ];

    if ($database->update($query, $params)) {
        echo "Kayıt başarıyla güncellendi.";
    } else {
        echo "Güncelleme sırasında hata oluştu.";
    }

}


?>
