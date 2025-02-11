<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-iade';
$template = new Template('İade ve Değişim - NEBSİS', $currentPage);
$template->head();
$database = new Database();

$q = "SELECT * FROM uyeler";
$result = $database->fetchAll($q);
?>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
    
     <div class="dashboard-wrapper">
          <div class="dashboard-ecommerce">
               <div class="container dashboard-content ">
                    <div class="ecommerce-widget overflow-hidden">
                         <div class="row">
                              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-2">
                                  <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">İadeler</h5>
                                   <div class="card">
                                         <div class="table-responsive mt-1" id="employee_table">
                                              <table id="deneme" class="table table-striped table-bordered second" style="width:100%">
                                                   <thead class="bg-light">
                                                        <tr class="border-0">
                                                             <th class="border-0">#</th>
                                                             <th class="border-0">İade Başvuru Tarihi</th>
                                                             <th class="border-0">Firma Adı</th>
                                                             <th class="border-0">İade Nedeni</th>
                                                             <th class="border-0">Sipariş No</th>
                                                             <th class="border-0">Ürün Adı</th>
                                                             <th class="border-0">İade Sonuç Durumu	</th>
                                                             <th class="border-0">İşlemler</th>
                                                        </tr>
                                                   </thead>
                                              </table>
                                         </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>

<!-- Modal Form -->
<div class="modal fade" data-backdrop="static"  id="editIadeModal" role="dialog" aria-labelledby="editIadeModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIadeModalLabel" name="baslik"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Edit Blog Form -->
                <form id="editIadeForm" method="post" >
                    <div class="row">
                        <input hidden type="text" id="editIadeId" name="editIadeId">
                        <input hidden type="text" id="sipUrunId" name="sipUrunId">
                        <div class="col-sm-12">
                            <label for="musteri" class="form-label">Müşteri Ad-Soyad</label>
                            <input type="text" class="form-control" id="musteri" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label for="tel" class="form-label">Telefon*</label>
                            <input type="tel" class="form-control" id="tel" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label for="email" class="form-label">E-Posta*</label>
                            <input type="email" class="form-control" id="email"  readonly>
                        </div>
                        <div class="col-sm-12">
                            <label for="editIadeOption">Iade Durumu Seç</label>
                            <select class="form-control" id="editIadeOption" name="editIadeOption">
                                <option value="1">Yeni İade</option>
                                <option value="2">İade Onaylandı</option>
                                <option value="3">İade Teslim Alındı</option>
                                <option value="4">İade Başarılı</option>
                                <option value="5">İade Reddedildi</option>
                                <option value="6">İade Başarısız</option>
                                <option value="7">İptal Edildi</option>
                                <option value="8">Müşteri İptal Etti</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="iadeDuzenleKaydet">Kaydet</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>

        </div>
    </div>
</div>
</body>
</html>

<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
<script src="assets/js/app.js"></script>

<script>
$(document).ready(function() {
    const baseUrl = window.location.origin + '/admin/';
    var table = $('#deneme').DataTable({
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json',
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": `${baseUrl}functions/siparisler/server_iade.php`,
            "type": "POST",
            "data": function(d) {
        }
        },
        "columns": [
            { "data": "id","orderable": false },
            { "data": "tarih" },
            { "data": "firmaUnvani" },
            { "data": "iade_nedeni" },
            { "data": "siparis_no" },
            { "data": "UrunAdiTR" },
            { "data": "durum" },
            {
            "data": null, "orderable": false,
            "render": function (data, type, row) {
                 return `
                      <td>
                           <button type="submit" name="iadeDuzenle" value="Düzenle" class="btn btn-sm btn-outline-dark edit-iadeDuzenle" data-iade-id="${data.id}" data-toggle="tooltip" title="Düzenle"><i class="fa-regular fa-pen-to-square"></i></button>
                      </td>
                 `;
            }
            }
        ],
        "colReorder": true,
        "initComplete": function() {
    }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script>
    $(document).ready(function() {
        // Edit button click event handler
        $(document).on('click', '.edit-iadeDuzenle', function() {
            // Get the blog ID from the data attribute
            var iadeId = $(this).data('iade-id');

            $.ajax({
                url: 'functions/siparisler/fonksiyonlar.php',
                method: 'post',
                dataType: 'json',
                data: { id: iadeId,
                    tur : 'iadeDuzenle' },
                success: function(response) {
                    $('#musteri').val(response.ad);
                    $('#editIadeOption').val(response.durum);
                    $('#tel').val(response.tel);
                    $('#email').val(response.email);
                    $('#editIadeId').val(response.id);
                    $('#sipUrunId').val(response.sip_urun_id);
                }
            });
            // Show the edit modal
            $('#editIadeModal').modal('show');
        });
        // Save button click event handler
        $('#iadeDuzenleKaydet').click(function() {

            var durum = $('#editIadeOption').val();
            var id = $('#editIadeId').val();
            var sipUrunId = $('#sipUrunId').val();
            var type = 'iadeDuzenle';
            
            $.ajax({
                url: 'functions/siparisler/promosyon.php',
                method: 'post',
                data: {
                    id : id,
                    sipUrunId : sipUrunId,
                    durum : durum,
                    type : type
                },
                success: function(response) {
                    $('#editIadeModal').modal('hide');
                    window.location.reload();

                }
            });
        });
    });
</script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>