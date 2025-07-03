<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';

$currentPage = 'b2b-siparisler';
$template = new Template('Siparişler - NEBSİS', $currentPage);
$template->head();
$database = new Database();

$sDurum = filter_var($_GET['sDurum'] ?? 0, FILTER_VALIDATE_INT);

// Add page-specific CSS and JS
?>
<link rel="stylesheet" href="functions/siparisler/style.css">

<input type="text" hidden class="sDurum" value="<?= $sDurum ?>">
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php $template->header(); ?>
        
        <div class="content-wrapper">
            <div class="container flex-grow-1 container-p-y">
                <div class="dashboard-wrapper">
                    <div class="dashboard-ecommerce">
                        <div class="container-fluid dashboard-content">
                            <!-- Status Navigation -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="status-nav">
                                        <a href="pages/b2b/b2b-siparisler?sDurum=0&w=noktab2b" class="btn btn-primary">Tüm Siparişler</a>
                                        <a href="pages/b2b/b2b-siparisler?sDurum=1&w=noktab2b" class="btn btn-primary">Yeni</a>
                                        <a href="pages/b2b/b2b-siparisler?sDurum=2&w=noktab2b" class="btn btn-primary">Onaylanan</a>
                                        <a href="pages/b2b/b2b-siparisler?sDurum=3&w=noktab2b" class="btn btn-primary">Kargo Aşamasında</a>
                                        <a href="pages/b2b/b2b-siparisler?sDurum=4&w=noktab2b" class="btn btn-primary">Kargolanan</a>
                                        <a href="pages/b2b/b2b-siparisler?sDurum=5&w=noktab2b" class="btn btn-primary">Teslim Edilen</a>
                                        <a href="pages/b2b/b2b-siparisler?sDurum=6&w=noktab2b" class="btn btn-primary">İptal Edilen</a>
                                        <a href="pages/b2b/b2b-siparisler?sDurum=7&w=noktab2b" class="btn btn-primary">Müşteri İptali</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <?php if ($sDurum == 1): ?>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-secondary onayla-btn">Onaylandı Yap</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($sDurum == 2): ?>
                                <div class="row mb-4">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-secondary kargo-numara-ver-btn">Kargo Numarası Ver</button>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button type="button" class="btn btn-secondary teslim-edildi-btn">Teslim Edildi Yap</button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($sDurum == 3): ?>
                                <div class="row mb-4">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-secondary">Kargolandı Yap</button>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button type="button" class="btn btn-secondary yazdir-btn">Yazdır</button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($sDurum == 4): ?>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-secondary">Teslim Edildi Yap</button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Orders Table -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="card-header status-header">
                                        <?php
                                        $statusTitles = [
                                            0 => 'Tüm Siparişler',
                                            1 => 'Yeni Siparişler',
                                            2 => 'Onaylanan Siparişler',
                                            3 => 'Kargo Aşamasında Siparişler',
                                            4 => 'Kargolanan Siparişler',
                                            5 => 'Teslim Edilen Siparişler',
                                            6 => 'İptal Edilen Siparişler',
                                            7 => 'Müşteri İptali Siparişler'
                                        ];
                                        echo $statusTitles[$sDurum] ?? 'Siparişler';
                                        ?>
                                    </h5>
                                    <div class="card">
                                        <div class="table-responsive mt-1">
                                            <table id="orders-table" class="table table-striped table-bordered">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Sipariş No</th>
                                                        <th>Ad Soyad</th>
                                                        <th>Firma Adı</th>
                                                        <th>Email</th>
                                                        <th>Sipariş Tarihi</th>
                                                        <th>Durum</th>
                                                        <th>Ödeme Şekli</th>
                                                        <th>Kargo</th>
                                                        <th>Toplam</th>
                                                        <th>İşlemler</th>
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
    </div>
</div>

<!-- Required Scripts -->
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/libs/i18n/i18n.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/form-layouts.js"></script>
<script src="functions/siparisler/scripts.js"></script>
</body>
</html>
<script>
    $(document).ready(function() {
    // Initialize variables
    const selectedIds = [];
    const baseUrl = window.location.origin + '/admin/';
     
     // Handle order status changes
    function handleOrderStatusChange(action, targetStatus) {
        console.log('Handling order status change:', action, targetStatus);
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: 'Lütfen en az bir sipariş seçin.'
            });
            return;
        }
            console.log('Seçilen Sipariş ID\'leri: ' + selectedIds.join(', '));
            const requests = selectedIds.map(id => {
            const requestData = { type: targetStatus, sip_id: id };
            const requestUrl = `${baseUrl}${action}`;

            return $.ajax({
                url: requestUrl,
                method: 'POST',
                data: requestData
            });
        });


        Promise.all(requests)
            .then(() => {
                window.location.href = `${baseUrl}pages/b2b/b2b-siparisler.php?sDurum=${targetStatus}&w=noktab2b`;
            })
            .catch(error => {
                console.error('Status change failed:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: 'İşlem sırasında bir hata oluştu.'
                });
            });
    }

    // Button click handlers
    $('.onayla-btn').click(() => handleOrderStatusChange('functions/siparisler/fonksiyonlar.php', 'siparis_onay'));
    $('.kargo-numara-ver-btn').click(() => handleOrderStatusChange('kargo_gonder.php', '3'));
    $('.teslim-edildi-btn').click(() => handleOrderStatusChange('functions/siparisler/fonksiyonlar.php', 'teslim_edildi'));

    });
</script>