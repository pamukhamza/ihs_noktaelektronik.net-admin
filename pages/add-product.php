<?php
include_once '../functions/db.php';
require '../functions/admin_template.php';

// Sayfa başlığı ve template ayarları
$currentPage = 'add-product';
$template = new Template('Yeni Ürün - Nokta Admin', $currentPage);

$template->head();

// Gelen ID'yi kontrol ediyoruz, yoksa 0 olarak kabul ediyoruz
$id = isset($_GET['id']) && !empty($_GET['id']) ? $_GET['id'] : 0;

$database = new Database();
if ($id == 0) {
    $insertQuery = "
        INSERT INTO nokta_urunler (UrunAdiTR, UrunAdiEN, UrunKodu, barkod, OzelliklerTR, OzelliklerEN, BilgiTR, BilgiEN, KategoriID)
        VALUES ('', '', '', '', '', '', '', '', NULL) 
    ";
    $database->insert($insertQuery); // execute() yerine query() kullanıyoruz

    $id = $database->lastInsertId(); // lastInsertId fonksiyonu var mı kontrol etmelisiniz
}

// ID'ye göre ürünü alıyoruz
$query = "SELECT * FROM nokta_urunler WHERE id = :id";
$params = ['id' => $id];
$product = $database->fetch($query, $params);

// Kategorileri veritabanından çekme fonksiyonu
function getCategories($parent_id = 0, $categories = [], $level = 0) {
    $database = new Database();
    $query = "SELECT * FROM nokta_kategoriler WHERE parent_id = :parent_id";
    $params = ['parent_id' => $parent_id];
    $results = $database->fetchAll($query, $params);
    
    foreach ($results as $row) {
        // Kategori başına uygun seviyeye göre boşluk ekliyoruz
        $row['KategoriAdiTr'] = str_repeat('-', $level * 4) . $row['KategoriAdiTr'];
        $categories[] = $row;
        $categories = getCategories($row['id'], $categories, $level + 1); // Alt kategoriler için recursive
    }

    return $categories;
}
$marka = $product["MarkaID"];
$query = "SELECT * FROM nokta_urun_markalar";
$brands = $database->fetchAll($query);

// Kategorileri çek
$categories = getCategories();

?>

<body xmlns="http://www.w3.org/1999/html">
<div class="layout-wrapper layout-content-navbar  ">
    <div class="layout-container">
        <?php $template->header(); ?>
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="app-ecommerce">
                    <div class="row">
                        <div class="card-header px-0 pt-0">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-bilgi" aria-controls="form-tabs-bilgi" role="tab" aria-selected="true"><span class="ti ti-user ti-lg d-sm-none"></span><span class="d-none d-sm-block">Ürün Bilgileri</span></button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-indirme" aria-controls="form-tabs-indirme" role="tab" aria-selected="false"><span class="ti ti-phone ti-lg d-sm-none"></span><span class="d-none d-sm-block">İndirmeler</span></button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                            <div class="tab-pane fade show active" id="form-tabs-bilgi" role="tabpanel">
                                <div class="row">
                                    <div class="col-12 col-lg-8">
                                        <form action="../functions/products/edit_product.php" method="post">
                                            <div class="card mb-6">
                                                <div class="card-header"><h5 class="card-tile mb-0">Ürün Bilgileri</h5></div>
                                                <div class="card-body">
                                                    <p style="color: red">Ürün adı ve sku boş ise ürün otomatik silinir.</p>
                                                    <div class="mb-6 row">
                                                        <input type="hidden" name="id" value="<?= $id; ?>">
                                                        <div class="col">
                                                            <label class="form-label" for="name">Ürün Adı</label>
                                                            <input type="text" required class="form-control" id="name" placeholder="Ürün Adı" name="name" value="<?= $product['UrunAdiTR']; ?>">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label" for="name_en">Ürün Adı EN</label>
                                                            <input type="text" class="form-control" id="name_en" placeholder="Ürün Adı İngilizce" name="name_en" value="<?= $product['UrunAdiEN']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-6">
                                                        <div class="col">
                                                            <label class="form-label" for="urun_kodu">Ürün Kodu</label>
                                                            <input type="text" required class="form-control" id="urun_kodu" placeholder="Urun Kodu" name="urun_kodu" value="<?= $product['UrunKodu']; ?>">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label" for="barkod">Barkod</label>
                                                            <input type="text" class="form-control" id="barkod" placeholder="barkod" name="barkod" value="<?= $product['barkod']; ?>">
                                                        </div>
                                                    </div>
                                                    <!-- Kategori Seçimi -->
                                                    <div class="mb-6 row">
                                                        <div class="col">
                                                            <label class="form-label" for="category">Kategori</label>
                                                            <select class="form-control" id="category" name="category" required>
                                                                <option value="">Kategori Seçiniz</option>
                                                                <?php foreach ($categories as $cat): ?>
                                                                    <option value="<?= $cat['id']; ?>" <?= $product['KategoriID'] == $cat['id'] ? 'selected' : ''; ?> data-parent-id="<?= $cat['parent_id']; ?>">
                                                                        <?= htmlspecialchars($cat['KategoriAdiTr']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label" for="brand">Marka</label>
                                                            <select class="form-control" id="brand" name="brand" required>
                                                                <option value="">Marka Seçiniz</option>
                                                                <?php foreach ($brands as $brand): ?>
                                                                    <option value="<?= $brand['id']; ?>" <?= $product['MarkaID'] == $brand['id'] ? 'selected' : ''; ?>>
                                                                        <?= htmlspecialchars($brand['title']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Product Information -->
                                            <!-- General Features -->
                                            <div class="card mb-6">
                                                <div class="card-header"><h5 class="card-title mb-0">Genel Özellikler</h5></div>
                                                <div class="card-body">
                                                    <div data-repeater-list="group-a">
                                                        <!-- General Features -->
                                                        <div data-repeater-item class="mb-6">
                                                            <label class="form-label" for="ozellikler">Genel Özellikler</label>
                                                            <textarea id="ozellikler" name="ozellikler" class="form-control" style="display:none;"><?= $product['OzelliklerTR']; ?></textarea>
                                                        </div>
                                                        <div data-repeater-item class="mb-6">
                                                            <label class="form-label" for="ozellikler_en">Genel Özellikler EN</label>
                                                            <textarea id="ozellikler_en" name="ozellikler_en" class="form-control" style="display:none;"><?= $product['OzelliklerEN']; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /General Features -->
                                            <!-- Technical Specifications -->
                                            <div class="card mb-6">
                                                <div class="card-header"><h5 class="card-title mb-0">Teknik Özellikler</h5></div>
                                                <div class="card-body">
                                                    <div data-repeater-list="group-a">
                                                        <div data-repeater-item class="mb-6">
                                                            <label class="form-label" for="teknik_ozellikler">Teknik Özellikler</label>
                                                            <textarea id="teknik_ozellikler" name="teknik_ozellikler" class="form-control" style="display:none;"><?= $product['BilgiTR']; ?></textarea>
                                                        </div>
                                                        <div data-repeater-item class="mb-6">
                                                            <label class="form-label" for="teknik_ozellikler_en">Teknik Özellikler EN</label>
                                                            <textarea id="teknik_ozellikler_en" name="teknik_ozellikler_en" class="form-control" style="display:none;"><?= $product['BilgiEN']; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Technical Specifications -->
                                            <div class="mb-6">
                                                <button class="btn btn-primary" name="product-submit"><i class='ti ti-save ti-xs me-2'></i>Kaydet</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <!-- Product Images Card -->
                                        <div class="card mb-6">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Ürün Fotoğrafları</h5>
                                            </div>
                                            <div class="card-body">
                                                <input type="file" class="form-control" accept="image/*" name="images[]" id="imageInput" multiple />
                                                <div class="my-3">
                                                    <button class="btn btn-primary" id="uploadButton"><i class='ti ti-plus ti-xs me-2'></i>Ekle</button>
                                                </div>
                                                <div id="uploadStatus"></div> <!-- For displaying upload status -->
                                            </div>
                                        </div>
                                        <!-- /Product Images Card -->
                                        <!-- /Image List -->
                                        <div class="card mb-6">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Fotoğraf Listesi</h5>
                                            </div>
                                            <?php
                                            // Fetch images for the specific product
                                            $imagesQuery = "SELECT * FROM nokta_urunler_resimler WHERE UrunID = :product_id ORDER BY Sira ASC";
                                            $imageParams = ['product_id' => $id];
                                            $images = $database->fetchAll($imagesQuery, $imageParams);

                                            ?>
                                            <div class="card-body">
                                                <table class="table table-responsive">
                                                    <thead>
                                                    <tr>
                                                        <th>Sıra</th>
                                                        <th>Fotoğraf</th>
                                                        <th>İşlemler</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if (!empty($images)): ?>
                                                        <?php foreach ($images as $image): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($image['Sira']); ?></td>
                                                                <td>
                                                                    <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?= htmlspecialchars($image['KResim']); ?>" style="width: 50px; height: auto;">
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-danger btn-sm delete-image" data-image="<?= htmlspecialchars($image['KResim']); ?>" data-id="<?= $image['id']; ?>" data-product-id="<?= $id; ?>">Sil</button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3">Yüklenen fotoğraf yok.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /Image List -->
                                        <!-- Filtre Ekleme -->
                                        <div class="card mb-6" id="filter-section">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Filtre Ekleme</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="my-3">
                                                    <label for="filter-title">Ana Filtre</label>
                                                    <select class="form-control" id="filter-title" name="filter-title">
                                                        <option value="">Ana Filtre Seç</option>
                                                    </select>
                                                </div>
                                                <div class="my-3" id="filter-values-container">
                                                    <label for="filter-value">Filtreler</label>
                                                    <select class="form-control" id="filter-value" name="filter-value">
                                                        <option value="">Filtre Seç</option>
                                                    </select>
                                                </div>
                                                <div class="my-3">
                                                    <button id="save-filter" class="btn btn-primary">Filtre Ekle</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Filtre Ekleme -->
                                        <!-- /Filtre Listesi -->
                                        <div class="card mb-6">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Eklenmiş Filtreler</h5>
                                            </div>
                                            <?php
                                            // Fetch images for the specific product
                                            $filterQuery = "SELECT pf.id AS product_filter_rel_id, fv.* FROM products_filter_rel AS pf 
                                                            LEFT JOIN filter_value AS fv ON fv.id = pf.filter_value_id
                                                            WHERE pf.product_id = :product_id ORDER BY pf.id ASC";
                                            $filterParams = ['product_id' => $id];
                                            $filters = $database->fetchAll($filterQuery, $filterParams);

                                            ?>
                                            <div class="card-body">
                                                <table class="table table-responsive">
                                                    <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Filtre</th>
                                                        <th>İşlemler</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if (!empty($filters)): ?>
                                                        <?php foreach ($filters as $filter): ?>
                                                            <tr>
                                                                <td><?= $filter['id']; ?></td>
                                                                <td>
                                                                    <?= $filter["name"] ?>
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-danger btn-sm delete_filter" data-id="<?= $filter['product_filter_rel_id']; ?>">Sil</button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3">Eklenmiş filtre yok.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /Filtre Listesi -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="form-tabs-indirme" role="tabpanel">

                            </div>
                    </div>
                </div>
            </div>
            <div class="content-backdrop fade"></div>
        </div>
        <?php $template->footer(); ?>
    </div>
</div>
<div class="layout-overlay layout-menu-toggle"></div>
<div class="drag-target"></div>
</div>
<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/libs/popper/popper.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/libs/hammer/hammer.js"></script>
<script src="../assets/vendor/libs/i18n/i18n.js"></script>
<script src="../assets/vendor/libs/typeahead-js/typeahead.js"></script>
<script src="../assets/vendor/js/menu.js"></script>
<!-- endbuild -->
<!-- Vendors JS -->
<script src="../assets/vendor/libs/jquery-repeater/jquery-repeater.js"></script>
<script src="../assets/vendor/libs/flatpickr/flatpickr.js"></script>
<script src="../assets/vendor/libs/tagify/tagify.js"></script>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/app-ecommerce-product-add.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert 2 Kütüphanesi -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function initializeCKEditor(elementId) {
        CKEDITOR.replace(elementId, {
            filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html',
            filebrowserUploadUrl: 'ckeditor/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        ['ozellikler', 'ozellikler_en', 'teknik_ozellikler', 'teknik_ozellikler_en'].forEach(function(elementId) {
            initializeCKEditor(elementId);
        });
    });
</script>
<script>
    $(document).on('click', '.delete-image', function() {
        const imageName = $(this).data('image');
        const productId = $(this).data('product-id');

        if (confirm("Fotoğrafı silinecek, emin misiniz?")) {
            $.ajax({
                url: '../functions/products/delete_image.php',
                type: 'POST',
                data: {
                    image: imageName,
                    product_id: productId
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.status === "success") {
                        // Remove the corresponding row from the table
                        $('button[data-image="' + imageName + '"]').closest('tr').remove();
                        Swal.fire({
                            toast: true,
                            position: 'center',
                            title: 'Fotoğraf silindi!',
                            text: result.message,
                            icon: 'success',
                            timer: 1000, // Auto close after 1 second
                            timerProgressBar: true,
                            background: '#f8d7da',
                            color: '#721c24',
                            iconColor: '#721c24'
                        });
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Hata!',
                            text: result.message,
                            timer: 1000, // Auto close after 1 second
                            timerProgressBar: true,
                            showConfirmButton: false, // Hide the confirm button
                            background: '#f8d7da', // Optional: custom background color
                            color: '#721c24', // Optional: custom text color
                            iconColor: '#721c24' // Optional: custom icon color
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the image.',
                        icon: 'error',
                        timer: 1000, // Auto close after 1 second
                        timerProgressBar: true
                    });
                }
            });
        }
    });

</script>

<script>
    $(".delete_product").on('click', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Silmek istediğinize emin misiniz?',
            text: "Bu işlem geri alınamaz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'Hayır, iptal et!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../functions/functions.php',
                    type: 'POST',
                    data: { id: id, tablename: 'products', type: 'delete' },  // Type delete olarak gönderiliyor
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Silindi!',
                            text: 'Kayıt başarıyla silindi.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();  // Sayfayı yeniden yükle
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Silme işlemi başarısız oldu.',
                        });
                    }
                });
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".delete_filter").on('click', function () {
            const id = $(this).data('id');
            const relId = $(this).data('rel-id');

            Swal.fire({
                title: 'Silmek istediğinize emin misiniz?',
                text: "Bu işlem geri alınamaz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Hayır, iptal et!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../functions/functions.php',
                        type: 'POST',
                        data: { id: id, rel_id: relId, tablename: 'products_filter_rel', type: 'delete' },  // Type delete olarak gönderiliyor
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Silindi!',
                                text: 'Kayıt başarıyla silindi.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();  // Sayfayı yeniden yükle
                            });
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Silme işlemi başarısız oldu.',
                            });
                        }
                    });
                }
            });
        });

    });
</script>
<script>
    $(document).ready(function() {
        $('#uploadButton').click(function(e) {
            e.preventDefault(); // Prevent the default form submission

            var formData = new FormData();
            var fileInput = $('#imageInput')[0];

            // Check if files were selected
            if (fileInput.files.length > 0) {
                for (var i = 0; i < fileInput.files.length; i++) {
                    formData.append('images[]', fileInput.files[i]); // Append each file to the formData
                }

                // Change button text and disable it
                $('#uploadButton').html('<i class="ti ti-loader ti-xs me-2"></i>Yükleniyor...').prop('disabled', true);

                $.ajax({
                    url: '../functions/products/upload_images.php?id=<?= $id; ?>', // Change to your upload script path
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#uploadStatus').html('<div class="alert alert-success">Başarıyla yüklendi!</div>');
                    },
                    error: function() {
                        $('#uploadStatus').html('<div class="alert alert-danger">Yükleme başarısız!</div>');

                    },
                    complete: function() {
                        // Re-enable the button and reset its text
                        $('#uploadButton').html('<i class="ti ti-plus ti-xs me-2"></i>Ekle').prop('disabled', false);
                        location.reload();
                    }
                });
            } else {
                $('#uploadStatus').html('<div class="alert alert-warning">Lütfen en az bir dosya seçin.</div>');
            }
        });
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    var categorySelect = document.getElementById('category');

    function handleCategoryChange() {
        var categoryId = categorySelect.value;
        var parentId = categorySelect.options[categorySelect.selectedIndex].getAttribute('data-parent-id');
        var filterSection = document.getElementById('filter-section');

        if (categoryId) {
            fetchFilters(categoryId);
            if (parentId) {
                fetchFilters(parentId); // Fetch filters for parent category
            }
        }
    }

    // Run the function on page load
    handleCategoryChange();

    // Add event listener for change event
    categorySelect.addEventListener('change', handleCategoryChange);
    });

    function fetchFilters(categoryId) {
        // Make an AJAX request to get filter titles based on categoryId
        fetch('../functions/products/get_filters.php?category_id=' + categoryId)
            .then(response => response.json())
            .then(data => {
                console.log(data); // Log the response for debugging
                var filterTitleSelect = document.getElementById('filter-title');
                filterTitleSelect.innerHTML = '<option value="">Ana Filtre Seç</option>';
                data.forEach(function(filter) {
                    var option = document.createElement('option');
                    option.value = filter.id;
                    option.textContent = filter.title;
                    filterTitleSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching filters:', error)); // Log any errors
    }

    document.getElementById('filter-title').addEventListener('change', function() {
        var filterTitleId = this.value;
        if (filterTitleId) {
            fetchFilterValues(filterTitleId);
        }
    });

    function fetchFilterValues(filterTitleId) {
        // Make an AJAX request to get filter values based on filterTitleId
        fetch('../functions/products/get_filter_values.php?filter_title_id=' + filterTitleId)
            .then(response => response.json())
            .then(data => {
                var filterValueSelect = document.getElementById('filter-value');
                filterValueSelect.innerHTML = '<option value="">Filtre Seç</option>';
                data.forEach(function(value) {
                    var option = document.createElement('option');
                    option.value = value.id;
                    option.textContent = value.name;
                    filterValueSelect.appendChild(option);
                });
                document.getElementById('filter-values-container').style.display = 'block';
            });
    }
</script>
<script>
    document.getElementById('save-filter').addEventListener('click', function() {
        var filterValueId = document.getElementById('filter-value').value;
        var productId = <?= $id; ?>;

        if (filterValueId) {
            // Send AJAX request to save the selected filter
            fetch('../functions/products/add_filter.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    filter_value_id: filterValueId
                })
            })
                .then(response => response.json())
                .then(data => {
                    // Handle success or error
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Filtre eklendi!',
                            timer: 2000,
                            timerProgressBar: true,
                            showCloseButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error adding filter: ' + data.message,
                            timer: 2000,
                            timerProgressBar: true,
                            showCloseButton: false
                        });
                    }
                });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Please select a filter value.',
                timer: 2000,
                timerProgressBar: true,
                showCloseButton: false
            });
        }
    });
</script>

<script>
    // Sayfa yüklendiğinde çalışacak
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);

        const status = urlParams.get('s');
        const message = urlParams.get('msg');

        if (status === '1') {
            Swal.fire({
                icon: 'success',
                title: 'Updated Successfully',
                text: 'Product Updated Successfully.',
                timer: 2000,
                timerProgressBar: true,
                showCloseButton: false
            });
        } else if (status === '2' && message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: decodeURIComponent(message),
                timer: 2000,
                timerProgressBar: true,
                showCloseButton: false
            });
        }
    });
</script>

</body>
</html>