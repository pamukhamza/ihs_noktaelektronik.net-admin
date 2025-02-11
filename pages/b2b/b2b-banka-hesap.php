<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-banka-bilgileri';
$template = new Template('Banka Hesap Bilgileri - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();
?>
<body>
<style>
    .form-group{margin-top:10px}
</style>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class=" flex-grow-1 container-p-y container-xxl">
                <div class="row g-6">
                    <!-- Categories table -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table id="deneme" class="table table-striped">
                                    <thead>
                                        <tr class="border-0">
                                                <th class="border-0">ID</th>
                                                <th class="border-0">Banka Adı</th>
                                                <th class="border-0">Şube Adı</th>
                                                <th class="border-0">IBAN No</th>
                                                <th class="border-0">Kolay Adres</th>
                                                <th class="border-0">Hesap Sahibi</th>
                                                <th class="border-0">Hesap Türü</th>
                                                <th class="border-0">Swift Kodu</th>
                                                <th class="border-0">Durum</th>
                                                <th class="border-0">İşlem</th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $query = "SELECT * FROM nokta_banka_bilgileri ORDER BY id ASC";
                                            $results = $database->fetchAll($query);
                                            foreach ($results as $row) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['banka_adi']; ?></td>
                                                <td><?php echo $row['sube_adi']; ?></td>
                                                <td><?php echo $row['iban']; ?></td>
                                                <td><?php echo $row['kolay_adres']; ?></td>
                                                <td><?php echo $row['hesap_adi']; ?></td>
                                                <td><?php echo $row['hesap']; ?></td>
                                                <td><?php echo $row['swift']; ?></td>
                                                <td>
                                                    <label class="switch switch-success">
                                                        <input type="checkbox" class="switch-input aktifPasifBankaBilgisi" name="<?= $row['id']; ?>" id="<?= $row['id']; ?>"  data-id="<?= $row['id']; ?>"  <?php echo ($row['aktif'] == 1 ? 'checked' : ''); ?> />
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"><i class="ti ti-check"></i></span>
                                                            <span class="switch-off"><i class="ti ti-x"></i></span>
                                                        </span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-success edit-bankaBilgisi" data-bs-toggle="modal" data-banka-id="<?php echo $row['id']; ?>"> <i class="far fa-edit"></i> </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="dynamicSil('<?php echo $row['id']; ?>', '', 'bankaBilgisi', 'Banka Bilgisi Silindi!', 'admin/muhasebe/adminHesapBilgileri.php');"><i class='far fa-trash-alt'></i></button>
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
<!-- Core JS -->
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<script src="assets/js/app.js"></script>



<!-- Edit User Modal -->
<div class="modal fade" id="editBankaBilgisiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2">Banka Bilgileri</h4>
                </div>
                <form id="editBankaBilgisiForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="editBankaAdi">Banka Adı</label>
                        <input type="text" hidden id="editBankaBilgisiId" name="editBankaBilgisiId">
                        <input type="text" class="form-control" id="editBankaAdi" name="editBankaAdi" >
                    </div>
                    <div class="form-group">
                        <label for="editSubeAdi">Şube Adı</label>
                        <input type="text" class="form-control" id="editSubeAdi" name="editSubeAdi" >
                    </div>
                    <div class="form-group">
                        <label for="editIban">IBAN</label>
                        <input type="text" class="form-control" id="editIban" name="editIban" >
                    </div>
                    <div class="form-group">
                        <label for="editKolayAdres">Kolay Adres</label>
                        <input type="text" class="form-control" id="editKolayAdres" name="editKolayAdres" >
                    </div>
                    <div class="form-group">
                        <label for="editHesapSahibi">Hesap Sahibi</label>
                        <input type="text" class="form-control" id="editHesapSahibi" name="editHesapSahibi" >
                    </div>
                    <div class="form-group">
                        <label for="editHesapTuru">Hesap Türü</label>
                        <select class="form-control" id="editHesapTuru" name="editHesapTuru">
                            <option value="TÜRK LİRASI">TÜRK LİRASI</option>
                            <option value="USD">USD</option>
                            <option value="EURO">EURO</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editSwift">Swift Kodu</label>
                        <input type="text" class="form-control" id="editSwift" name="editSwift" >
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" id="saveEditBankaBilgisi" class="btn btn-primary me-3">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Edit User Modal -->
</body>
</html>
<script>
    $(document).ready(function() {
        $(document).on('click', '.aktifPasifBankaBilgisi', function() {
            var id = $(this).attr("id");
            var konum = "nokta_banka_bilgileri";
            var durum = ($(this).is(':checked')) ? '1' : '0';
            $.ajax({
                type: 'POST',
                url: 'functions/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
                data: { id:id, durum: durum, konum: konum },
                success: function (result) {
                    Swal.fire({
                        title: "Aktif/Pasif işlemi yapıldı!" ,
                        toast: true,
                        position: 'bottom-end',
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function () {
                    alert('Hata');
                }
            });
        });
        $(document).on('click', '.edit-bankaBilgisi', function() {
            var bankaBilgisiId = $(this).data('banka-id');
            $('#editBankaBilgisiForm')[0].reset();

            $.ajax({
                url: 'functions/b2b/muhasebe/get_banka_bilgileri.php',
                method: 'post',
                dataType: 'json',
                data: { 
                    id: bankaBilgisiId,
                    type: 'bankaBilgisi' 
                },
                success: function(response) {
                    if (response) {
                        $('#editBankaAdi').val(response.banka_adi);
                        $('#editSubeAdi').val(response.sube_adi);
                        $('#editIban').val(response.iban);
                        $('#editKolayAdres').val(response.kolay_adres);
                        $('#editHesapSahibi').val(response.hesap_adi);
                        $('#editHesapTuru').val(response.hesap);
                        $('#editSwift').val(response.swift);
                        $('#editBankaBilgisiId').val(response.id);
                    } else {
                        Swal.fire({
                            title: "Hata!",
                            text: "Veri alınamadı.",
                            icon: "error",
                            confirmButtonText: "Tamam"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: "Hata!",
                        text: "İşlem sırasında bir hata oluştu: " + xhr.responseText,
                        icon: "error",
                        confirmButtonText: "Tamam"
                    });
                }
            });

            $('#editBankaBilgisiModal').modal('show');
        });
        $('#saveEditBankaBilgisi').click(function() {
            var bankaBilgisiId = $('#editBankaBilgisiId').val();
            var bankaAdi = $('#editBankaAdi').val();
            var subeAdi = $('#editSubeAdi').val();
            var iban = $('#editIban').val();
            var kolayAdres = $('#editKolayAdres').val();
            var hesapSahibi = $('#editHesapSahibi').val();
            var hesapTuru = $('#editHesapTuru').val();
            var swift = $('#editSwift').val();


            var formData = new FormData();
            formData.append('id', bankaBilgisiId);
            formData.append('bankaAdi', bankaAdi);
            formData.append('subeAdi', subeAdi);
            formData.append('iban', iban);
            formData.append('kolayAdres', kolayAdres);
            formData.append('hesapSahibi', hesapSahibi);
            formData.append('hesapTuru', hesapTuru);
            formData.append('swift', swift);
            formData.append('type', 'bankaBilgileri');

            $.ajax({
                url: 'functions/b2b/muhasebe/edit_banka_bilgileri.php',
                method: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    $('#editBankaBilgisiModal').modal('hide');
                    location.reload();
                }
            });
        });
    });
</script>