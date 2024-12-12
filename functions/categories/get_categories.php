<?php
include_once '../db.php';

$database = new Database();
$parentId = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;

function displayCategories($parentId, $database) {
    $query = "SELECT * FROM categories WHERE parent_id = $parentId";
    $results = $database->fetchAll($query);
    $output = '';

    foreach ($results as $row) {
        // Fetch subcategories
        $hasSubcategories = $database->fetchAll("SELECT * FROM categories WHERE parent_id = " . $row['id']);
        $output .= '<tr>';
        $output .= '<td>' . (empty($hasSubcategories) ? '' : '<button class="toggle-subcat me-2" data-id="' . $row['id'] . '">+</button>') . $row['name'] . '</td>';
        $output .= '<td>' . $row['name_cn'] . '</td>';
        $output .= '<td>
            <a class="cursor-pointer me-2 cat_sort" data-id="' . $row['id'] . '"><i class="ti ti-list me-1"></i></a>
            <a class="cursor-pointer me-2 edit_cat" data-id="' . $row['id'] . '" data-name="' . $row['name'] . '" data-name_cn="' . $row['name_cn'] . '" data-category="' . $row['parent_id'] . '"><i class="ti ti-pencil me-1"></i></a>
            <a class="cursor-pointer delete_cat" data-id="' . $row['id'] . '"><i class="ti ti-trash me-1"></i></a>
        </td>';
        $output .= '</tr>';

        // Display subcategories (hidden initially)
        $output .= '<tr class="subcat-row subcat-' . $row['id'] . '" style="display: none;"><td colspan="4"><table class="table subcategory-table"><tbody>';
        $output .= displayCategories($row['id'], $database);
        $output .= '</tbody></table></td></tr>';
    }

    return $output;
}

// Output the categories for the requested parent ID
echo displayCategories($parentId, $database);
?>
