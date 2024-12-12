<?php
include_once 'db.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $field = $_POST['field']; // hangi alan: new veya active
    $value = $_POST['value']; // yeni değer: 1 veya 0
    $db_name = $_POST['database'];

    if (in_array($field, ['new', 'active', 'status'])) { // sadece izin verilen alanları güncelleyebiliriz
        $database = new Database();
        $query = "UPDATE $db_name SET `$field` = :value WHERE id = :id";
        $params = [
            'value' => $value,
            'id' => $id
        ];
        $result = $database->update($query, $params);

        if ($result) {
            echo "Update Successful";
        } else {
            echo "Error while updating";
        }
    } else {
        echo "Invalid input.";
    }
}

?>