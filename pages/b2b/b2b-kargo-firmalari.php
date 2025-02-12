<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'kargo-firmalari';
$template = new Template('Kargo Firmaları - NEBSİS',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();

$kargolar = $database->fetchAll("SELECT * FROM b2b_kargo");
?>
<body>
<style>
 /* SweetAlert'i en üste taşı */
.swal2-container {
    z-index: 99999 !important; /* Tüm öğelerden daha üstte olacak */
}

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
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Kargo Firmaları</h5>
                            <div class="table-responsive" id="employee_table">
                                <table id="odemeler" class="table  table-bordered second" style="width:100%">
                                    <thead class="bg-light">
                                        <tr class="border-0">
                                            <th class="border-0">#</th>
                                            <th class="border-0">Kargo Adı</th>
                                            <th class="border-0">Gösterim</th>
                                            <th class="border-0">Durum</th>
                                            <th class="border-0">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php   foreach($kargolar as $kargo){
                                            $gosterim = $kargo['gosterim'];
                                            $yayin_durumu = $kargo['yayin_durumu']; ?>
                                            <tr>
                                                <td><?php echo  $kargo['id']; ?></td>
                                                <td><?php echo  $kargo['kargo_adi']; ?></td>
                                                <td><?php if ($gosterim == '1') { echo 'Müşteri ve Bayi (B2C) - (B2B)';} elseif ($gosterim == '2') {echo 'Müşteri (B2C)';}
                                                    elseif ($gosterim == '3') {echo 'Bayi (B2B)';} ?>
                                                </td>
                                                <td><?php if ($yayin_durumu == '1') {
                                                        echo '<i class="fas fa-check-circle text-success"></i> Aktif';
                                                    } elseif ($yayin_durumu == '0') {
                                                        echo '<i class="fas fa-times-circle text-danger"></i> Pasif';
                                                    } ?></td>
                                                <td>
                                                    <button type="button" value="Düzenle" class="btn btn-sm btn-outline-light edit-kargo" data-kargo-id="<?= $kargo['id']?>" ><i class="fa-regular fa-pen-to-square"></i></button>
                                                    <button type="button" value="Desiler" class="btn btn-sm btn-outline-light edit-desi" data-kargo-id="<?= $kargo['id']?>" ><i class="fa fa-cubes"></i></button>
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
<div class="modal fade" id="editKargoDesi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple modal-edit-user">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2">Banka Bilgileri</h4>
                </div>
                <div class="table table-responsive">
                    <table id="desi-table" class="table">
                        <tbody>
                            <tr class="border"></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Edit User Modal -->
</body>
</html>

<script>
    $(document).ready(function() {
        $(document).on('click', '.edit-kargo', function() {
            var id = $(this).data('kargo-id');
            window.location.href = 'pages/b2b/b2b-kargoDuzenle.php?w=noktab2b&id=' + id;
        });
        $(document).on('click', '.edit-desi', function() {
            var kargoID = $(this).data('kargo-id');

            // Ajax isteği gönder
            $.ajax({
                url: 'functions/b2b/kargo/getDesiData.php', // Bu dosyayı oluşturmalısınız
                type: 'POST',
                data: {kargoID: kargoID},
                success: function(response) {
                    // Başarılı bir şekilde tamamlandığında
                    $('#desi-table tbody').html(response);
                    $('#editKargoDesiLabel').html('Desi Bilgileri');
                    $('#editKargoDesi').modal('show');
                },
                error: function() {
                    // Hata durumunda
                    alert('Desi bilgileri getirilirken bir hata oluştu.');
                }
            });
        });
    });
</script>
<script>
    function kargoDesiKaydet(id) {
        var desiAlt = $("input[name='desi_alt'][data-id='" + id + "']").val();
        var desiUst = $("input[name='desi_ust'][data-id='" + id + "']").val();
        var fiyat = $("input[name='fiyat'][data-id='" + id + "']").val();

        $.ajax({
            url: 'functions/b2b/kargo/updateDesi.php',
            type: 'POST',
            data: {
                id: id,
                desi_ust: desiUst,
                desi_alt: desiAlt,
                fiyat: fiyat
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Desi bilgileri başarıyla güncellendi!',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass:{
                        container: 'swal2-container'
                    }
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Desi bilgileri güncellenirken bir hata oluştu!',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass:{
                        container: 'swal2-container'
                    }
                });
            }
        });
    }
    function kargoDesiSil(id) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: 'Bu desi bilgisini silmek istediğinizden emin misiniz?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, Sil',
            cancelButtonText: 'İptal',
            customClass:{
                container: 'swal2-container'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Silme işlemi için Ajax isteği gönder
                $.ajax({
                    url: 'functions/b2b/kargo/deleteDesi.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response === 'Success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Desi bilgisi başarıyla silindi!',
                                toast: true,
                                position: 'top-end',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass:{
                                    container: 'swal2-container'
                                }
                            });
                            $('#desi-table tbody').find('[data-id="' + id + '"]').closest('tr').remove();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Desi bilgisi silinirken bir hata oluştu!',
                                toast: true,
                                position: 'top-end',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass:{
                                    container: 'swal2-container'
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Desi bilgisi silinirken bir hata oluştu!',
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass:{
                                container: 'swal2-container'
                            }
                        });
                    }
                });
            }
        });
    }
    function yeniDesiEkle(kargoID) {
        // Yeni satır HTML'i oluştur
        var newRowHTML = '<tr>';
        newRowHTML += '<td><input type="text" name="desi_alt" /></td>';
        newRowHTML += '<td><input type="text" name="desi_ust" /></td>';
        newRowHTML += '<td><input type="text" name="fiyat" /></td>';
        newRowHTML += '<td>';
        newRowHTML += '<button type="button" class="btn btn-sm btn-outline-light" onclick="kargoDesiKaydet(' + kargoID + ');"><i class="far fa-save"></i></button>';
        newRowHTML += '<button type="button" class="btn btn-sm btn-outline-light" onclick="kargoDesiSil(' + kargoID + ');"><i class="far fa-trash-alt"></i></button>';
        newRowHTML += '</td>';
        newRowHTML += '</tr>';

        // Yeni satırı tabloya ekle
        $('#desi-table tbody').append(newRowHTML);

        // Yeni satırı veritabanına eklemek için Ajax isteği gönder
        $.ajax({
            url: 'functions/b2b/kargo/insertDesi.php', // Yeni satır eklemeyi gerçekleştirecek dosya
            type: 'POST',
            data: {
                kargoID: kargoID,
                // Diğer gerekli verileri buraya ekleyebilirsiniz
            },
            success: function(response) {
                if (response) {
                    // Başarılı durumunda yapılacak işlemler
                    console.log('Yeni desi başarıyla veritabanına eklendi:', response);
                    // Ajax ile dönen veri ile satırı güncelle
                    var $newRow = $('#desi-table tbody tr:last');
                    var newDesiID = response.id;
                    $newRow.find('input[name="desi_alt"]').attr('data-id', newDesiID);
                    $newRow.find('input[name="desi_ust"]').attr('data-id', newDesiID);
                    $newRow.find('input[name="fiyat"]').attr('data-id', newDesiID);
                    $newRow.find('button[onclick^="kargoDesiKaydet"]').attr('onclick', 'kargoDesiKaydet(' + newDesiID + ')');
                    $newRow.find('button[onclick^="kargoDesiSil"]').attr('onclick', 'kargoDesiSil(' + newDesiID + ')');
                } else {
                    // Başarısız durumunda yapılacak işlemler
                    console.error('Yeni desi veritabanına eklenirken bir hata oluştu.');
                }
            },
            error: function() {
                // Hata durumunda yapılacak işlemler
                console.error('Yeni desi veritabanına eklenirken bir hata oluştu.');
            }
        });
    }
</script>