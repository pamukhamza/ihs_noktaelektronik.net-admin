<?php
include_once '../db.php';

$database = new Database();

function displayFilterTitle($database) {
    $output = '';
    $query = " SELECT * FROM nokta_urunler_ikonlar ";
    $ikons = $database->fetchAll($query);

    foreach ($ikons as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row['img'] . '</td>';
        $output .= '<td>' . $row['title']  .'</td>'; // Display the category name
        $output .= '<td>
            <a class="cursor-pointer me-2 edit_ikon" data-id="' . $row['id'] . '" data-title="' . $row['title'] . '""><i class="ti ti-pencil me-1"></i></a>
           <a class="cursor-pointer delete_ikon" data-id="' . $row['id'] . '"><i class="ti ti-trash me-1"></i></a>
        </td>';
        $output .= '</tr>';
    }

    $output .= '</tbody></table></td></tr>';
    return $output;
}

// Display ikons
echo displayFilterTitle($database);
?>
