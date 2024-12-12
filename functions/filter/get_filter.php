<?php
include_once '../db.php';

$database = new Database();

function displayFilterTitle($database) {
    $output = '';
    $id = $_GET['id'];
    // Query to join `category_filter_rel` and `categories` tables to fetch `name`
    $query = "
        SELECT * FROM filter_value WHERE filter_title_id = $id";

    $categories = $database->fetchAll($query);

    foreach ($categories as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row['value'] . '</td>';
        $output .= '<td>
            <a class="cursor-pointer me-2 edit_filter" data-id="' . $row['id'] . '" data-name="' . $row['value'] . '" data-nameEn="' . $row['value_en'] . '"><i class="ti ti-pencil me-1"></i></a>
           <a class="cursor-pointer delete_filter" data-id="' . $row['id'] . '"><i class="ti ti-trash me-1"></i></a>
        </td>';
        $output .= '</tr>';
    }

    $output .= '</tbody></table></td></tr>';
    return $output;
}

// Display categories
echo displayFilterTitle($database);
?>
