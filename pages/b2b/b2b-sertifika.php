<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-sertifika';
$template = new Template('Sertifika - Nokta Admin',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();
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
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-2">
                        <div class="card col-md-6 col-sm-12">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <form action="functions/b2b/sertifika/sertifika_olustur.php" method="post" >
                                            <input type="text" name="uye_id" value="<?= $_SESSION['user_session']['id']; ?>" hidden>
                                            <h5>Şirket ve Proje Adı</h5>
                                            <textarea class="form-control" name="orta_yazi" cols="5">Çelik İnşaat Emlak Taah. Tic.İth.İhr.Ltd.Şti - İnönü Üniversitesi Diş Hekimliği Fakültesi</textarea>

                                            <button type="submit" class="btn btn-primary mt-3" name="btn-kaydet">Kaydet</button>
                                        </form>
                                    </div>
                                    <div class="col-6">
                                        <h5>Örnek</h5>
                                        <img src="assets/images/ornek_sertifika.PNG" width="100%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card col-md-6 col-sm-12 mt-5">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Oluşturulan Sertifikalar</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered w-100" id="sertifikaTable">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Oluşturan</th>
                                        <th>Sertifika Adı</th>
                                        <th>Tarih</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $q = "SELECT s.id, CONCAT(k.username) AS kullanici_adi, s.field1, s.tarih 
                                                FROM b2b_sertifikalar s
                                                JOIN users k ON s.uye_id = k.id
                                                ORDER BY s.id DESC";
                                            $urun = $database->fetchAll($q);
                                            foreach($urun as $row) { 
                                        ?>
                                                <tr>
                                                    <td><?= $row["id"]; ?></td>
                                                    <td><?= $row["kullanici_adi"]; ?></td>
                                                    <td><?= $row["field1"]; ?></td>
                                                    <td><?= $row["tarih"]; ?></td>
                                                </tr>
                                        <?php }  ?>
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

</body>
</html>
