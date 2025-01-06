<?php
include_once '../db.php';

$database = new Database();

function displayCategories($database) {
    $output = '';
    $categories = $database->fetchAll("SELECT * FROM nokta_urun_markalar ORDER BY id");

    foreach ($categories as $row) {
        $output .= '<tr>';
        $output .= '<td>'. $row['id'] . '</td>';
        $output .= '<td><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/brands/'. $row['hover_img'] . '" style="height: 50px"></td>';
        $output .= '<td>'. $row['title'] . '</td>';
        $output .= '<td>'. $row['order_by'] . '</td>';
        $output .= '<td>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton' . $row['id'] . '" data-bs-toggle="dropdown" aria-expanded="false">
                    Siteler
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $row['id'] . '">
                    <li>
                        <label class="switch switch-success">
                            <input type="checkbox" class="switch-input featured-checkbox" data-id="' . $row['id'] . '" ' . ($row['web_net'] == 1 ? 'checked' : '') . ' />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                <span class="switch-off"><i class="ti ti-x"></i></span>
                            </span>
                            <span class="switch-label">.net</span>
                        </label>
                    </li>
                    <li>
                        <label class="switch switch-success">
                            <input type="checkbox" class="switch-input new-checkbox" data-id="' . $row['id'] . '" ' . ($row['web_comtr'] == 1 ? 'checked' : '') . ' />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                <span class="switch-off"><i class="ti ti-x"></i></span>
                            </span>
                            <span class="switch-label">.com.tr</span>
                        </label>
                    </li>
                    <li>
                        <label class="switch switch-success">
                            <input type="checkbox" class="switch-input new-checkbox" data-id="' . $row['id'] . '" ' . ($row['web_cn'] == 1 ? 'checked' : '') . ' />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                <span class="switch-off"><i class="ti ti-x"></i></span>
                            </span>
                            <span class="switch-label">.com.cn</span>
                        </label>
                    </li>
                </ul>
            </div>
        </td>';
        $output .= '<td>'. $row['is_active'] . '</td>';
        $output .= '<td>
            <a class="cursor-pointer me-2 brand_sort"><i class="ti ti-list me-1"></i></a>
            <a class="cursor-pointer me-2 edit_brand" data-id="' . $row['id'] . '" data-name="' . $row['title'] . '"><i class="ti ti-pencil me-1"></i></a>
            <a class="cursor-pointer delete_brand" data-id="' . $row['id'] . '"><i class="ti ti-trash me-1"></i></a>
        </td>';
        $output .= '</tr>';
    }

    return $output;
}

// Kategorileri göster
echo displayCategories($database);
?>
