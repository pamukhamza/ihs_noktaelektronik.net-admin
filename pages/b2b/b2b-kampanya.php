<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';


$database = new Database();
$currentPage = 'b2b-kampanya';
$template = new Template('Kampanyalar - Nokta Admin',  $currentPage);
// head'i çağırıyoruz
$template->head();

?>
<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <h5><button type="button" class="btn btn-secondary edit-popup">Pop-up Ekle</button></h5>
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Pop Up Görseller</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered second">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0">ID</th>
                                            <th class="border-0">Pop-up Görsel</th>
                                            <th class="border-0">Pop-up Link</th>
                                            <th class="border-0">Aktif/Pasif</th>
                                            <th class="border-0">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable">
                                        <?php
                                            $q = "SELECT * FROM b2b_popup_kampanya";
                                            $results = $database->fetchAll($q);
                                            foreach ($results as $row) {
                                        ?>
                                            <tr data-id="<?php echo $row['id']; ?>">
                                                <td><?php echo  $row['id']; ?></td>
                                                <td><div class="m-r-10"><img width="45" class="rounded" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/campaigns/<?php echo $row['foto'];?>"/></div></td>
                                                <td><a target="_blank" href="<?php echo  $row['link']; ?>">Link</a></td>
                                                <td>
                                                    <label class="switch switch-success">
                                                        <input type="checkbox" class="switch-input active-checkbox-slider" data-id="<?= $row['id']; ?>" <?= $row['aktif'] == 1 ? 'checked' : ''; ?> />
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"><i class="ti ti-check"></i></span>
                                                            <span class="switch-off"><i class="ti ti-x"></i></span>
                                                        </span>
                                                        <span class="switch-label"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <button type="button" name="popupDuzenle" value="Düzenle" class="btn btn-sm btn-outline-light edit-popup" data-popup-id="<?php echo  $row['id']; ?>"><i class="far fa-edit"></i></button>
                                                    <button type="button" class="btn btn-sm btn-outline-light" onclick="popupSil(<?php echo  $row['id']; ?>, '<?php echo  $row['foto']; ?>');"><i class='far fa-trash-alt'></i></button>
                                                </td>
                                            </tr>
                                        <?php }  ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 ">
                        <h5><button type="button" class="btn btn-secondary" id="duzenle-kampanya">Kampanya Ekle</button></h5>
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Kampanyalar</h5>
                            <div class="">
                                <div class="table-responsive">
                                    <table class="table table-bordered second" style="width:100%">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-center">#ID</th>
                                                <th class="text-center">Kampanya Adı</th>
                                                <th class="text-center">Aktif/Pasif</th>
                                                <th class="text-center">Oluşturma Tarihi</th>
                                                <th class="text-center">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $q = "SELECT k.*, GROUP_CONCAT(u.UrunKodu SEPARATOR ', ') AS UrunKodular
                                                        FROM b2b_kampanyalar AS k
                                                        LEFT JOIN nokta_urunler u ON FIND_IN_SET(u.id, k.urun_id) > 0
                                                        GROUP BY k.id, k.ad, k.urun_id, k.tarih
                                                        ORDER BY k.id ASC";
                                                $results = $database->fetchAll($q);
                                                foreach ($results as $row) {
                                            ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $row['id']; ?></td>
                                                    <td class="text-center"><a target="_blank" href="https://www.noktaelektronik.com.tr/tr/kampanyalar/<?php echo $row['link']; ?>"><?php echo $row['ad']; ?></a></td>
                                            
                                                    <td>
                                                    <label class="switch switch-success">
                                                        <input type="checkbox" class="switch-input active-checkbox-kampanya" data-id="<?= $row['id']; ?>" <?= $row['aktif'] == 1 ? 'checked' : ''; ?> />
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"><i class="ti ti-check"></i></span>
                                                            <span class="switch-off"><i class="ti ti-x"></i></span>
                                                        </span>
                                                        <span class="switch-label"></span>
                                                    </label>
                                                </td>
                                                    <td class="text-center"><?php echo $row['tarih']; ?></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-light edit-kampanya" data-var-id="<?php echo $row['id']; ?>"><i class="far fa-edit"></i></button>
                                                        <button type="button" class="btn btn-sm btn-outline-light" onclick="kampanyaSil(<?php echo $row['id']; ?>, '<?php echo $row['urun_id']; ?>');">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-4" id="kampanya-olustur" style="display: none">
                        <div class="card">
                            <h5 class="card-header"  style="background-color: #0a78f1; color:white;">Kampanya Ekle</h5>
                            <div class="card-body">
                                <div class="form-group mt-4">
                                    <label for="var_adi">Kampanya Adı</label>
                                    <input type="text" class="form-control" id="var_adi" name="var_adi" required style="width: 100%">
                                </div>
                                <div class="form-group mt-4">
                                    <label for="keep-order">Ürünleri Seç</label>
                                    <select id='keep-order' name="urunler[]"  multiple='multiple' class="form-control" style="width: 100%;">
                                        <?php
                                        $q = "SELECT UrunKodu,UrunAdiTR,id,filtre,stok FROM nokta_urunler WHERE web_comtr = 1";
                                        $urun = $database->fetchAll($q);

                                        // $urun['filtre'] içeriğini virgülle ayrılmış bir dizi haline getirin
                                        $selectedFilters = explode(',', $urun['filtre']);

                                        foreach($urun as $row) {
                                            // Eğer mevcut filtre $selectedFilters dizisinde ise 'selected' ekleyin
                                            $selected = in_array($row['id'], $selectedFilters) ? 'selected' : '';
                                            ?>
                                            <option value='<?php echo $row['id']; ?>' <?php echo $selected; ?>><?php echo $row['UrunKodu']; ?> / <?php echo $row['UrunAdiTR']; ?> / stok:<?php echo $row['stok']; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-space btn-primary mt-4" name="kampanya_kaydet" id="kampanya_kaydet">Kaydet</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Content backdrop -->
            <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
        <?php $template->footer(); ?>
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->
 <!-- Modal Popup Form -->
<div class="modal fade" id="editKampanyaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKampanyaModalLabel" name="baslik">Kampanya Düzenle</h5>
            </div>
            <div class="modal-body">
                <!-- Edit Kampanya Form -->
                <form id="editKampanyaForm">
                    <div class="form-group">
                        <label for="editVarAdi">Kampanya Adı</label>
                        <input type="text" id="editVarId" name="editVarId" hidden>
                        <input type="text" class="form-control" id="editVarAdi" name="editVarAdi" readonly>
                    </div>
                    <div class="form-group katmans">
                        <label for="editVarUrun">Seçili Ürünler</label>
                        <select id='editVarUrun' name="urunler[]"  multiple='multiple' class="select2 form-select" style="width: 100%;">
                            <?php
                            $q = "SELECT UrunKodu,UrunAdiTR,id,filtre,stok FROM nokta_urunler WHERE web_comtr = 1";
                            $urun = $database->fetchAll($q);

                            // $urun['filtre'] içeriğini virgülle ayrılmış bir dizi haline getirin
                            $selectedFilters = explode(',', $urun['filtre']);

                            foreach($urun as $row) {
                                // Eğer mevcut filtre $selectedFilters dizisinde ise 'selected' ekleyin
                                $selected = in_array($row['id'], $selectedFilters) ? 'selected' : '';
                                ?>
                                <option value='<?php echo $row['id']; ?>' <?php echo $selected; ?>><?php echo $row['UrunKodu']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveEditKampanya">Kaydet</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Popup Form -->
<div class="modal fade" data-backdrop="static" id="editPopupModal" tabindex="-1" role="dialog" aria-labelledby="editPopupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPopupModalLabel" name="baslik"></h5>
            </div>
            <div class="modal-body">
                <!-- Edit Popup Form -->
                <form id="editPopupForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="editPopupLink">Popup Link</label>
                        <input style="display: none;" type="text" id="editPopupId" name="editPopupId">
                        <input type="text" class="form-control" id="editPopupLink" name="editPopupLink" placeholder="Or. www.noktaelektronik.com.tr">
                    </div>
                    <div class="form-group">
                        <label for="editPopupGorsel">Popup Görsel</label>
                        <input type="file" class="form-control" id="editPopupGorsel" name="popupGorsel" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveEditPopup">Kaydet</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/libs/i18n/i18n.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<script src="assets/js/form-layouts.js"></script>

</body>
</html>
<script>
    $('.active-checkbox-slider').on('change', function() {
        var id = $(this).data('id');
        var activeStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'functions/update_status.php',  // PHP dosyanızın ismini yazın
            type: 'POST',
            data: {
                id: id,
                field: 'aktif',
                value: activeStatus,
                database: 'b2b_popup_kampanya'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    text: response,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function() {
                alert('Error while updating');
            }
        });
    });
    $('.active-checkbox-kampanya').on('change', function() {
        var id = $(this).data('id');
        var activeStatus = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'functions/update_status.php',  // PHP dosyanızın ismini yazın
            type: 'POST',
            data: {
                id: id,
                field: 'aktif',
                value: activeStatus,
                database: 'b2b_kampanyalar'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    text: response,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function() {
                alert('Error while updating');
            }
        });
    });
    $(document).ready(function() {
        var isUploading = false;
        // Edit button click event handler
        $(document).on('click', '.edit-popup', function() {
            // Get the popup ID from the data attribute
            var popupId = $(this).data('popup-id');
            if (popupId) {
                $('.modal-title').html("Popup Düzenle");
            } else {
                $('.modal-title').html("Yeni Popup Ekle");
            }
            $('#editPopupForm')[0].reset();

            $.ajax({
                url: 'functions/b2b/kampanya/get_info.php',
                method: 'post',
                dataType: 'json',
                data: { id: popupId,
                    type : 'popup' },
                success: function(response) {

                    // Populate the modal with the fetched data
                    $('#editPopupLink').val(response.link);
                    $('#editPopupId').val(response.id);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                    alert('AJAX Error: ' +  error);
                }
            });
            // Show the edit modal
            $('#editPopupModal').modal('show');
        });

        // Save button click event handler
        $('#saveEditPopup').click(function() {
            if (isUploading) {
                return;
            }

            isUploading = true;
            // Get the Popup ID from the form
            var sId = $('#editPopupId').val();
            var sLink = $('#editPopupLink').val();
            var sGorselInput = document.getElementById('editPopupGorsel');
            var sGorsel = sGorselInput.files[0];
            alert(sId + " " + sLink + " " + sGorsel);
            // Create a FormData object to handle the file input data
            var formData = new FormData();
            formData.append('id', sId);
            formData.append('popupLink', sLink);
            formData.append('popupGorsel', sGorsel);
            formData.append('type', 'popup');

            $.ajax({
                url: 'functions/b2b/kampanya/popup_kaydet.php',
                method: 'post',
                data: formData,
                processData: false, // Important! Don't process data, allows FormData to handle it
                contentType: false, // Important! Don't set contentType
                success: function() {
                    // Close the edit modal
                    $('#editPopupModal').modal('hide');
                    // Refresh the popup list
                    isUploading = false;
                    location.reload();
                }
            });
        });
    });
</script>

<!--Multi Select-->
<script>
    $(document).ready(function() {
        $('#keep-order').select2();
        $('#search-filter').on('keyup', function() {
            $('#keep-order').val(null).trigger('change'); // Reset the selected values
            var searchText = $(this).val().toLowerCase();
            $('#keep-order option').each(function() {
                var optionText = $(this).text().toLowerCase();
                if (optionText.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
    $(document).ready(function() {
        $('#editVarUrun').select2();
        $('#search-filter2').on('keyup', function() {
            $('#editVarUrun').val(null).trigger('change'); // Reset the selected values
            var searchText = $(this).val().toLowerCase();
            $('#editVarUrun option').each(function() {
                var optionText2 = $(this).text().toLowerCase();
                if (optionText2.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>

<!--Varysayon Düzenle-->
<script>
    // Edit button click event handler
    $(document).on('click', '.edit-kampanya', function () {
        // Get the marka ID from the data attribute
        var varId = $(this).data('var-id');
        $('#editKampanyaForm')[0].reset();

        $.ajax({
            url: 'functions/b2b/kampanya/get_info.php',
            method: 'post',
            dataType: 'json',
            data: {
                id: varId,
                type: 'kampanya'
            },
            success: function (response) {
                // Populate the modal with the fetched data
                $('#editVarAdi').val(response.ad);
                $('#editVarId').val(response.id);
                var filtre = response.urun_id.split(','); // Assuming filtre is a comma-separated string
                $('#editVarUrun').val(filtre).trigger('change');
            }
        });
        // Show the edit modal
        $('#editKampanyaModal').modal('show');
    });

    // Save button click event handler
    $('#saveEditKampanya').click(function () {
        // Get the Marka ID from the form
        var varId = $('#editVarId').val();
        var varAdi = $('#editVarAdi').val();
        var varUrunler = $('#editVarUrun').select2('val');
        var formData = new FormData();
        formData.append('id', varId);
        formData.append('ad', varAdi);
        formData.append('urun_id', varUrunler);
        formData.append('type', 'kampanyaEkle');

        $.ajax({
            url: 'functions/b2b/kampanya/kampanya_kaydet.php',
            method: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function (gel) {
                // Close the edit modal
                $('#editKampanyaModal').modal('hide');
                // Refresh the Marka list
                location.reload();
            }
        });
    });
</script>

<!--Kampanya Kaydet-->
<script>
    $('#kampanya_kaydet').click(function () {
        var varAdi = $('#var_adi').val();
        var varUrunler = $('#keep-order').select2('val');

        // Create a FormData object to handle the file input data
        var formData = new FormData();
        formData.append('ad', varAdi);
        formData.append('urun_id', varUrunler);
        formData.append('type', 'kampanya_kaydet');

        $.ajax({
            url: 'function.php',
            method: 'post',
            data: formData,
            processData: false, // Important! Don't process data, allows FormData to handle it
            contentType: false, // Important! Don't set contentType
            success: function () {
                location.reload();
            }
        });
    });
</script>
<script>
    document.getElementById('duzenle-kampanya').addEventListener('click', function() {
        document.getElementById('kampanya-olustur').style.display = 'block';
    });
</script>