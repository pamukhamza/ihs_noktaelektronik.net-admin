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

                $cari_kodu = trim($row[0]);
                $plasiyer = trim($row[1]);
                $ticari_unvani = trim($row[2]);
                $yetkilisi = trim($row[3]);
                $borc_bakiye = trim($row[4]);
                $hesap_turu = trim($row[5]);
                $geciken_tutar = trim($row[6]);
                $acik_hesap_gunu = intval($row[7]);
                $gerc_vade = trim($row[8]);
                $valoru = trim($row[9]);
                $bakiye_odeme_tarihi = trim($row[10]);
                $bilgi_kodu = trim($row[11]);
                $sube_kodu = trim($row[12]);

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
                        <div class="card">
                            <h5 class="card-header p-2" style="background-color: #0a78f1; color:white;">Vadesi Geçmiş Borçlar</h5>
                            <div class="card-body">
                                <?php if (!empty($mesaj)) : ?>
                                    <div class="alert alert-success"><?php echo $mesaj; ?></div>
                                <?php endif; ?>
                                <!-- Tablo -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="vadesiGecmisTable">
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
                                        <tbody>
                                            <?php foreach ($veriler as $veri): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($veri['cari_kodu']) ?></td>
                                                    <td><?= htmlspecialchars($veri['ticari_unvani']) ?></td>
                                                    <td><?= htmlspecialchars($veri['yetkilisi']) ?></td>
                                                    <td class="text-danger fw-bold"><?= number_format($veri['geciken_tutar'], 2, ',', '.') ?> ₺</td>
                                                    <td><?= $veri['gerc_vade'] ?></td>
                                                    <td><?= $veri['valoru'] ?></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="email" class="form-control email-input" 
                                                                   value="<?= htmlspecialchars($veri['email'] ?? '') ?>" 
                                                                   data-id="<?= $veri['id'] ?>">
                                                            <button type="button" class="btn btn-primary update-email" 
                                                                    data-id="<?= $veri['id'] ?>">
                                                                <i class="fas fa-save"></i> Kaydet
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm send-mail" 
                                                                data-id="<?= $veri['id'] ?>"
                                                                data-email="<?= htmlspecialchars($veri['email'] ?? '') ?>">
                                                            <i class="fas fa-envelope"></i> Mail Gönder
                                                        </button>
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
            </div>
            <div class="content-backdrop fade"></div>
        </div>
        <?php $template->footer(); ?>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>
<!-- Core JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
<!-- Main JS -->
<script src="assets/js/main.js"></script>

<!-- Custom Scripts -->
<script>
$(document).ready(function() {
    console.log('Document ready çalıştı'); // Test için console.log
    

    $('#vadesiGecmisTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
        },
        pageLength: 25,
        order: [[3, 'desc']], // Sort by Geciken Tutar by default
        responsive: true,
        dom: 'Bfrtip'
    });

    // Email güncelleme butonu - doğrudan buton seçicisi kullan
    $('.update-email').click(function(e) {
        e.preventDefault();
        console.log('Butona tıklandı');
        
        const id = $(this).data('id');
        const email = $(this).closest('.input-group').find('.email-input').val();
        
        console.log('ID:', id);
        console.log('Email:', email);

        if (!email) {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı!',
                text: 'Lütfen bir email adresi giriniz!'
            });
            return;
        }

        // AJAX isteği öncesi loading göster
        Swal.fire({
            title: 'Güncelleniyor...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'functions/muhasebe/update_email.php',
            method: 'POST',
            data: {id: id, email: email},
            dataType: 'json',
            success: function(response) {
                console.log('Server yanıtı:', response);
                
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
                        text: response.message || 'E-posta adresi güncellenirken bir hata oluştu.'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Hatası:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Sunucu ile iletişim kurulamadı. Lütfen daha sonra tekrar deneyin.'
                });
            }
        });
    });

    // Mail gönderme butonu
    $('.send-mail').on('click', function() {
        const id = $(this).data('id');
        const email = $(this).data('email');
        if(!email) {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı!',
                text: 'Lütfen önce e-posta adresi giriniz.'
            });
            return;
        }

        Swal.fire({
            title: 'Mail Gönder',
            text: 'Mail göndermek istediğinize emin misiniz?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Evet, Gönder',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'functions/muhasebe/send_mail.php',
                    method: 'POST',
                    data: {
                        id: id,
                        email: email
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Mail başarıyla gönderildi.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Mail gönderilirken bir hata oluştu.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Bir hata oluştu.'
                        });
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
