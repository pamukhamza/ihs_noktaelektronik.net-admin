<?php
include_once '../db.php';

$database = new Database();
$parentId = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;

function displayCategories($parentId, $database) {
    $query = "SELECT * FROM nokta_kategoriler WHERE parent_id = $parentId ORDER BY sira";
    $results = $database->fetchAll($query);
    $output = '';

    foreach ($results as $row) {
        // Fetch subcategories
        $hasSubcategories = $database->fetchAll("SELECT * FROM nokta_kategoriler WHERE parent_id = " . $row['id']);
        $output .= '<tr>';
        $output .= '<td>' . (empty($hasSubcategories) ? '' : '<button class="toggle-subcat me-2" data-id="' . $row['id'] . '">+</button>') . $row['KategoriAdiTr'] . '</td>';
        $output .= '<td>' . $row['KategoriAdiEn'] . '</td>';
        $output .= '<td>
            <a class="cursor-pointer me-2 cat_prod_sort" data-id="' . $row['id'] . '"><i class="ti ti-box me-1"></i></a>
            <a class="cursor-pointer me-2 cat_sort" data-id="' . $row['id'] . '"><i class="ti ti-list me-1"></i></a>
            <a class="cursor-pointer me-2 edit_cat" data-id="' . $row['id'] . '" data-name="' . $row['KategoriAdiTr'] . '" data-name_cn="' . $row['KategoriAdiEn'] . '" data-category="' . $row['parent_id'] . '"><i class="ti ti-pencil me-1"></i></a>
            <a class="cursor-pointer delete_cat" data-id="' . $row['id'] . '"><i class="ti ti-trash me-1"></i></a>
            
        </td>';
        $output .= '<td>
            <label class="switch switch-success">
                <input type="checkbox" class="switch-input wnet-checkbox" data-id="' . $row['id'] . '" ' . ($row['web_net'] == 1 ? 'checked' : '') . ' />
                <span class="switch-toggle-slider">
                    <span class="switch-on"><i class="ti ti-check"></i></span>
                    <span class="switch-off"><i class="ti ti-x"></i></span>
                </span>
                <span class="switch-label"></span>
            </label>
        </td>';
        $output .= '<td>
            <label class="switch switch-success">
                <input type="checkbox" class="switch-input wcomtr-checkbox" data-id="' . $row['id'] . '" ' . ($row['web_comtr'] == 1 ? 'checked' : '') . ' />
                <span class="switch-toggle-slider">
                    <span class="switch-on"><i class="ti ti-check"></i></span>
                    <span class="switch-off"><i class="ti ti-x"></i></span>
                </span>
                <span class="switch-label"></span>
            </label>
        </td>';
        $output .= '<td>
            <label class="switch switch-success">
                <input type="checkbox" class="switch-input wcn-checkbox" data-id="' . $row['id'] . '" ' . ($row['web_cn'] == 1 ? 'checked' : '') . ' />
                <span class="switch-toggle-slider">
                    <span class="switch-on"><i class="ti ti-check"></i></span>
                    <span class="switch-off"><i class="ti ti-x"></i></span>
                </span>
                <span class="switch-label"></span>
            </label>
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
