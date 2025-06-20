<?php
include_once '../../functions/db.php';
require '../../functions/admin_template.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
require '../../vendor/autoload.php';

$currentPage = 'b2b-vadesi-gecmis';
$template = new Template('Vadesi Geçmiş Borçlar - Nokta Admin',  $currentPage);
// head'i çağırıyoruz
$template->head();
$database = new Database();

if (isset($_POST['temizle'])) {
    $database->delete("DELETE FROM vadesi_gecmis_borc");
    echo "<div class='alert alert-success'>Tablo başarıyla temizlendi.</div>";
}

if (isset($_POST['yukle'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $dosya = $_FILES['excel']['tmp_name'];

    if ($dosya) {
        try {
            $spreadsheet = IOFactory::load($dosya);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $sayac = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) continue;

                $cari_kodu = trim($row[0] ?? '');
                $plasiyer = trim($row[1] ?? '');
                $ticari_unvani = trim($row[2] ?? '');
                $yetkilisi = trim($row[3] ?? '');
                $borc_bakiye = trim($row[4] ?? '');
                $hesap_turu = trim($row[5] ?? '');
                $geciken_tutar = trim($row[6] ?? '');
                $acik_hesap_gunu = intval($row[7]);
                $gerc_vade = trim($row[8] ?? '');
                $valoru = trim($row[9] ?? '');
                $bakiye_odeme_tarihi = trim($row[10] ?? '');
                $bilgi_kodu = trim($row[11] ?? '');
                $sube_kodu = trim($row[12] ?? '');

                if ($cari_kodu && floatval(str_replace(',', '.', $geciken_tutar)) > 0) {
                    $sql = "INSERT INTO vadesi_gecmis_borc (
                        cari_kodu, plasiyer, ticari_unvani, yetkilisi, borc_bakiye,
                        hesap_turu, geciken_tutar, acik_hesap_gunu, gerc_vade, valoru,
                        bakiye_odeme_tarihi, bilgi_kodu, sube_kodu
                    ) VALUES (
                        :cari_kodu, :plasiyer, :ticari_unvani, :yetkilisi, :borc_bakiye,
                        :hesap_turu, :geciken_tutar, :acik_hesap_gunu, :gerc_vade, :valoru,
                        :bakiye_odeme_tarihi, :bilgi_kodu, :sube_kodu
                    )";
                    
                    $params = [
                        'cari_kodu' => $cari_kodu,
                        'plasiyer' => $plasiyer,
                        'ticari_unvani' => $ticari_unvani,
                        'yetkilisi' => $yetkilisi,
                        'borc_bakiye' => $borc_bakiye,
                        'hesap_turu' => $hesap_turu,
                        'geciken_tutar' => $geciken_tutar,
                        'acik_hesap_gunu' => $acik_hesap_gunu,
                        'gerc_vade' => $gerc_vade,
                        'valoru' => $valoru,
                        'bakiye_odeme_tarihi' => $bakiye_odeme_tarihi,
                        'bilgi_kodu' => $bilgi_kodu,
                        'sube_kodu' => $sube_kodu
                    ];
                    
                    $insertResult = $database->insert($sql, $params);

                    if ($insertResult) {
                        $sayac++;
                    } else {
                        echo "<div style='color:red;'>Satır $index eklenemedi: {$hata[2]}</div>";
                    }
                }
            }

            echo "<div style='color:green;'>$sayac kayıt başarıyla yüklendi.</div>";

        } catch (Exception $e) {
            echo "<div style='color:red;'>Hata oluştu: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div style='color:red;'>Dosya yüklenemedi!</div>";
    }
}

$veriler = $database->fetchAll("SELECT * FROM vadesi_gecmis_borc ");
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
                    <div class="col-12 mt-2"><!-- Temizleme ve Yükleme Formları -->
                        <form method="post" class="mb-3 d-flex justify-content-between" enctype="multipart/form-data">
                            <div>
                                <button type="submit" name="temizle" class="btn btn-danger" onclick="return confirm('Tüm kayıtlar silinecek. Emin misiniz?')">
                                    Veritabanını Temizle
                                </button>
                            </div>
                        </form>
                        <form method="post" class="mb-3 d-flex justify-content-between" enctype="multipart/form-data">
                            <div class="input-group w-50">
                                <input type="file" name="excel" accept=".xlsx, .xls" required class="form-control">
                                <button type="submit" name="yukle" class="btn btn-primary">Yükle</button>
                            </div>
                        </form>
                        <form method="post" action="functions/muhasebe/tahsilatemailesitle.php" class="mb-3 d-flex justify-content-between" enctype="multipart/form-data">
                            <div>
                                <button type="submit" name="emailesitle" class="btn btn-danger" onclick="return confirm('Tüm kayıtlar silinecek. Emin misiniz?')">
                                    E-mail Eşitleme
                                </button>
                            </div>
                        </form>
                        <small style="color:red">yeni liste yüklenecekse ilk önce veritabanı temizlenmeli,<br> Sonrada yapıya uygun excel yüklenmeli!</small><br>
                        <small style="color:red">İlk önce mail adresi alanını doldurup kaydetmeli.<br>Kaydetme işlemi bittikten sonra mail gönder butonuna YALNIZCA 1 DEFA tıklanıp mail gönderilir!</small>
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Vadesi Geçmiş Borçlar</h5>
                            <div class="card-body">
                                <?php if (!empty($mesaj)) : ?>
                                    <div class="alert alert-success"><?php echo $mesaj; ?></div>
                                <?php endif; ?>
                                <!-- Tablo -->
                                <div class="table-responsive">
                                    <table id="vadesiGecmisTable" class="table table-bordered table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Cari Kodu</th>
                                                <th>Ticari Ünvanı</th>
                                                <th>Yetkili</th>
                                                <th>Geciken Tutar</th>
                                                <th>Gerçek Vade</th>
                                                <th>Valör</th>
                                                <th>E-posta</th>
                                                <th>İşlem</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-backdrop fade"></div>
        </div>
        <?php $template->footer(); ?>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js"></script>
<script>
$(document).ready(function() {
    var table = $('#vadesiGecmisTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'functions/muhasebe/get_vadesi_gecmis_data.php',
            type: 'POST',
            dataSrc: function(json) {
                if (json.error) {
                    console.error('Server Error:', json.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Veri Yükleme Hatası',
                        text: json.message || 'Veriler yüklenirken bir hata oluştu.'
                    });
                    return [];
                }
                return json.data || [];
            },
            error: function (xhr, error, thrown) {
                console.error('DataTables Ajax Error:', error, thrown);
                console.log('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Veri Yükleme Hatası',
                    text: 'Veriler yüklenirken bir hata oluştu. Lütfen sayfayı yenileyin.'
                });
            }
        },
        columns: [
            { data: 0 }, // Cari Kodu
            { data: 1 }, // Ticari Ünvanı
            { data: 2 }, // Yetkili
            { 
                data: 3, // Geciken Tutar
                className: 'text-danger fw-bold'
            },
            { data: 4 }, // Gerçek Vade
            { data: 5 }, // Valör
            { 
                data: 6, // E-posta
                orderable: false
            },
            { 
                data: 7, // İşlem
                orderable: false
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tümü"]]
    });

    $(document).on('click', '.update-email', function() {
        var id = $(this).data('id');
        var email = $(this).closest('.input-group').find('.email-input').val();
        
        $.ajax({
            url: 'functions/muhasebe/update_email.php',
            type: 'POST',
            data: {
                id: id,
                email: email
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'E-posta adresi güncellendi.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'E-posta adresi güncellenirken bir hata oluştu. :' + response.message 
                    });
                }
            }
        });
    });

    $(document).on('click', '.send-mail', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'functions/muhasebe/send_mail.php',
            type: 'POST',
            data: {id: id},
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'E-posta gönderildi.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    console.error('Server Error:', response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message || 'E-posta gönderilirken bir hata oluştu.',
                        footer: response.debug ? `Hata Detayı: ${response.debug.file}:${response.debug.line}` : ''
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                let errorMessage = 'Sunucu ile iletişim kurulurken bir hata oluştu.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: errorMessage,
                    footer: 'Lütfen konsolu kontrol edin (F12)'
                });
            }
        });
    });
});
</script>
</body>
</html>
