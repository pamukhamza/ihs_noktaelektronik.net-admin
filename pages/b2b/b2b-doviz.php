<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-doviz-ayarlari';
$template = new Template('Döviz Ayarları - NEBSİS',  $currentPage);
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
            <div class=" flex-grow-1 container-p-y container-xxl">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Döviz Ayarları</h5>
                            <div class="table-responsive">
                                <table id="example" class="table table-bordered second" >
                                    <thead class="bg-light">
                                    <tr class="border-0">
                                        <th class="border-0" >Döviz</th>
                                        <th class="border-0" >Alış Kuru</th>
                                        <th class="border-0" >Satış Kuru</th>
                                        <th class="border-0" >İşlem</th>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $database = new Database();
                                        $query = "SELECT * FROM b2b_kurlar";
                                        $results = $database->fetchAll($query);
                                        foreach ($results as $row) {
                                    ?>
                                        <form action="functions/b2b/muhasebe/kur_guncelle.php" method="post">
                                            <tr>
                                                <td><?php echo  $row['birim']; ?><input type="text" hidden id="id" name="id" value="<?php echo  $row['id']; ?>"></td>
                                                <td>
                                                    <input type="text" class="form-control" id="alis" name="alis" placeholder="Başlık giriniz..." oninput="convertToDecimal(this)" value="<?php echo number_format($row['alis'], 2, '.', ''); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="satis" name="satis" placeholder="Başlık giriniz..." oninput="convertToDecimal(this)" value="<?php echo number_format($row['satis'], 2, '.', ''); ?>">
                                                </td>
                                                <td><button type="submit" class="btn btn-primary" id="kur_guncelle" name="kur_guncelle">Güncelle</button></td>
                                            </tr>
                                        </form>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <span style="color:red; margin-left:20px">Virgül yerine nokta kullanınız!</span>
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

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const sParam = urlParams.get('s');

    if (sParam === '1') {
        Swal.fire({
            icon: 'success',
            title: 'Güncelleme Kaydedilmiştir!',
            toast: true,
            position: 'bottom-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
</script>
</body>
</html>
