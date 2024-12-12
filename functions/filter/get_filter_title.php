<?php
include_once '../db.php';

$database = new Database();

function displayFilterTitle($database) {
    $output = '';

    // Query to join `category_filter_rel` and `categories` tables to fetch `name`
    $query = "
        SELECT 
            ft.id, 
            ft.title, 
            ft.title_en, 
            cfr.category_id, 
            c.name AS category_name
        FROM 
            filter_title ft
        LEFT JOIN 
            category_filter_rel cfr 
        ON 
            ft.id = cfr.filter_title_id
        LEFT JOIN 
            categories c 
        ON 
            cfr.category_id = c.id
    ";

    $categories = $database->fetchAll($query);

    foreach ($categories as $row) {
        $output .= '<tr>';
        $output .= '<td>' . $row['title'] . '</td>';
        $output .= '<td>' . ($row['category_name'] ?? 'No Category') . '</td>'; // Display the category name
        $output .= '<td>
            <a class="cursor-pointer me-2 edit_filter_title" data-id="' . $row['id'] . '" data-name="' . $row['title'] . '" data-nameEn="' . $row['title_en'] . '"><i class="ti ti-pencil me-1"></i></a>
            <a class="cursor-pointer me-2 list_filter_title" data-id="' . $row['id'] . '" ><i class="ti ti-list me-1"></i></a>
           <a class="cursor-pointer delete_filter_title" data-id="' . $row['id'] . '"><i class="ti ti-trash me-1"></i></a>
        </td>';
        $output .= '</tr>';
    }

    $output .= '</tbody></table></td></tr>';
    return $output;
}

// Display categories
echo displayFilterTitle($database);
?>
