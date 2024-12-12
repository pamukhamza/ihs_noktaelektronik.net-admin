<?php
include_once '../../../functions/db.php';

$database = new Database();

function displayCategories($database) {

    $output = '';
    $categories = $database->fetchAll("SELECT * FROM brands");

    foreach ($categories as $row) {
        $output .= '<tr>';
        $output .= '<td>'. $row['title'] . '</td>';
        $output .= '<td>'. $row['order_by'] . '</td>';
        $output .= '<td>
            <a class="cursor-pointer me-2 brand_sort"><i class="ti ti-list me-1"></i></a>
            <a class="cursor-pointer me-2 edit_brand" data-id="' . $row['id'] . '" data-name="' . $row['title'] . '"><i class="ti ti-pencil me-1"></i></a>
            <a class="cursor-pointer delete_brand" data-id="' . $row['id'] . '"><i class="ti ti-trash me-1"></i></a>
        </td>';
        $output .= '</tr>';

    }

    $output .= '</tbody></table></td></tr>';
    return $output;
}

// Kategorileri göster
echo displayCategories($database);
?>
