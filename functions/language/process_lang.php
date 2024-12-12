<?php
include_once '../../../functions/db.php';
$database = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $key = $_POST['key'];
    $value = $_POST['value'];

    // Language multiple select bir array olabilir, bunu dizeye çeviriyoruz
    $language = is_array($_POST['language']) ? implode(",", $_POST['language']) : $_POST['language'];

    if ($action === 'insert') {
        // `key` rezerve kelime olduğundan tırnak içine alıyoruz
        $query = "INSERT INTO translations (`key`, `value`, `language`) VALUES (:key, :value, :language)";
        $params = [
            'key' => $key,
            'value' => $value,
            'language' => $language
        ];

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];

        // `key` rezerve kelime olduğundan tırnak içine alıyoruz
        $query = "UPDATE translations SET `key` = :key, `value` = :value, `language` = :language WHERE id = :id";
        $params = [
            'key' => $key,
            'value' => $value,
            'language' => $language,
            'id' => $id
        ];

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }
}


?>
