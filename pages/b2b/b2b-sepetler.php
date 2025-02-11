<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-sepetler';
$template = new Template('Sepetler - NEBSİS',  $currentPage);
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
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-2">
                        <p class="">
                            <button type="submit" class="edit-yeni-sepet btn btn-space btn-primary font-weight-bold">Müşteri Sepeti Oluşturma</button>
                        </p>
                        <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Sepetler</h5>
                        <div class="card p-2">
                            <div class="table-responsive">
                                <table id="sepetlerTable" class="table table-bordered ">
                                    <thead class="bg-light">
                                    <th>Adı Soyadı</th>
                                    <th>Firma Ünvanı</th>
                                    <th>Email</th>
                                    <th>Telefon</th>
                                    <th>Tarih</th>
                                    <th>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="dynamicSil('', '', 'tum_sepetler', 'Tüm Sepetler Silindi!', 'adminSepetler.php?s=1');"><i class="fa-regular fa-trash-can"></i>  Tümünü Sil</button>
                                    </th>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $sepetler = $database -> fetchAll("SELECT * FROM uye_sepet GROUP BY uye_id");
                                        foreach ($sepetler as $sepet):
                                            $uye_id = $sepet['uye_id'];
                                            $uye = $database->fetch("SELECT * FROM uyeler WHERE id = $uye_id");
                                        ?>
                                        <tr>
                                            <td><?= !empty($uye['ad']) && !empty($uye['soyad']) ? $uye['ad'] .' '. $uye['soyad'] : ''; ?></td>
                                            <td><?= !empty($uye['firmaUnvani']) ? $uye['firmaUnvani'] : ''; ?></td>
                                            <td><?= !empty($uye['email']) ? $uye['email'] : ''; ?></td>
                                            <td><?= !empty($uye['tel']) ? $uye['tel'] : ''; ?></td>
                                            <td><?= !empty($sepet['tarih']) ? $sepet['tarih'] : ''; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-light" onclick="dynamicSil(<?= $sepet['uye_id'] ?>, '', 'sepetler', 'Sepet Silindi!', 'admin/siparisler/adminSepetler.php?s=1');" data-toggle="tooltip" title="Sil"><i class="fa-solid fa-trash-can"></i></button>

                                                <form method="POST" action="">
                                                    <input type="hidden" name="musteri" value="<?= !empty($uye['id']) ? $uye['id'] : ''; ?>">
                                                    <input type="hidden" name="email" value="<?= !empty($uye['email']) ? $uye['email'] : ''; ?>">
                                                    <button type="submit" name="sepetMailGonder" class="btn btn-sm btn-outline-light" data-toggle="tooltip" title="Sepet Hatırlatma Maili Gönder"><i class="fa-solid fa-envelope-open-text"></i></button>
                                                </form>
                                                
                                                <button type="button" name="uyeDuzenle" value="Düzenle" class="btn btn-sm btn-outline-light edit-sepet" data-sepet-id="<?= !empty($sepet['uye_id']) ? $sepet['uye_id'] : ''; ?>" data-toggle="tooltip" title="Düzenle"><i class="fa-regular fa-pen-to-square"></i></button>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
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
<!-- Edit User Modal -->
<div class="modal fade" id="editSepetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <div class="modal-body">
                <table id="musteriTablo" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Muhasebe Kodu</th>
                        <th>Firma Ünvanı</th>
                        <th>Ad</th>
                        <th>Soyad</th>
                        <th>Seçim</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $urun = $database->fetchAll("SELECT muhasebe_kodu, firmaUnvani, ad, soyad, id FROM uyeler");

                    foreach($urun as $row) {
                        ?>
                        <tr>
                            <td><?php echo $row['muhasebe_kodu']; ?></td>
                            <td><?php echo $row['firmaUnvani']; ?></td>
                            <td><?php echo $row['ad']; ?></td>
                            <td><?php echo $row['soyad']; ?></td>
                            <td><a href="pages/b2b/b2b-sepetdetay?id=<?php echo $row['id'];?>">Sepete Git</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--/ Edit User Modal -->
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
<script>
    $(document).ready(function() {
        $('#sepetlerTable').DataTable({
            "language": { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json', },
            "order": [[ 4, 'desc' ]],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }]
        });
        $(document).on('click', '.edit-sepet', function() {
            var id = $(this).data('sepet-id');
            window.location.href = 'pages/b2b/b2b-sepetdetay?id=' + id;
        });
        $(document).on('click', '.edit-yeni-sepet', function() {
            $('#editSepetModal').modal('show');
        });
        $('#musteriTablo').DataTable({
            // Optional: Customize the DataTable settings
            "paging": true,
            "searching": true,
            "info": true,
            "columnDefs": [
                { "orderable": false, "targets": 4 } // Disable ordering on the radio button column
            ]
        });
    });
</script>
</body>
</html>