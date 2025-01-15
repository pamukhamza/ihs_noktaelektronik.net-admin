<?php
include_once '../db.php';
$database = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $filter_title_id = $_POST['filter_title_id'];
    $name = $_POST['name'];
    $nameEn = $_POST['nameEn'];
    $id = $_POST['id']; // ID
    if ($action === 'insert') {
        $query = "INSERT INTO filter_value (`name`, `name_cn`, `filter_title_id`) VALUES (:name, :nameEn, :filter_title_id)";
        $params = [
            'name' => $name,
            'nameEn' => $nameEn,
            'filter_title_id' => $filter_title_id
        ];
        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    } elseif ($action === 'update') {
        $query = "UPDATE filter_value SET `name` = :name, `name_cn` = :nameEn WHERE id = :id";
        $params = [
            'name' => $name,
            'nameEn' => $nameEn,
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
