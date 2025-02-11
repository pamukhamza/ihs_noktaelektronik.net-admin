<?php
include_once '../db.php';
$database = new Database();

if (isset($_POST['promosyon_ols'])) {
    
    if (isset($_POST['indirim_miktari'])) {
        
        $indirim_miktari = $_POST['indirim_miktari'];
        $doviz_birimi = "TL";
        $min_sepet_tutar = $_POST['min_sepet'];
        $date = $_POST['date'];
        if (!is_numeric($min_sepet_tutar) || $min_sepet_tutar === '') {
            $min_sepet_tutar = '0.00';
        }
        $max_kul = $_POST['max_kul'];
        if (isset($_POST['uyeler'])) {
            if (is_array($_POST['uyeler'])) {
                $uyeler = implode(',', $_POST['uyeler']);
            } elseif(!is_array($_POST['uyeler']) && !empty($_POST['uyeler'])) {
                $uyeler = $_POST['uyeler'];
            }
        } else {
            $uyeler = NULL;
        }
        if (isset($_POST['urun'])) {
            if (is_array($_POST['urun'])) {
                $urunler = implode(',', $_POST['urun']);
            } elseif(!is_array($_POST['urun']) && !empty($_POST['urun'])) {
                $urunler = $_POST['urun'];
            }
        } else {
            $urunler = NULL;
        }
        function getAllSubCategories($db, $parentCategoryIds) {
            global $database;
            $placeholders = rtrim(str_repeat('?,', count($parentCategoryIds)), ',');
            $query = "SELECT id FROM nokta_kategoriler WHERE parent_id IN ($placeholders)";

            $subCategories = $database->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($subCategories)) {
                // Recursively get subcategories of the current subcategories
                $subCategories = array_merge($subCategories, getAllSubCategories($db, $subCategories));
            }

            return $subCategories;
        }

// Fetching categories from POST request
        if (isset($_POST['kategori'])) {
            if (is_array($_POST['kategori'])) {
                $selectedCategories = $_POST['kategori'];
            } elseif(!is_array($_POST['kategori']) && !empty($_POST['kategori'])) {
                $selectedCategories = [$_POST['kategori']];
            }
        } else {
            $selectedCategories = [];
        }

        if (!empty($selectedCategories)) {
            // Get all subcategories recursively
            $allCategories = array_merge($selectedCategories, getAllSubCategories($db, $selectedCategories));
            // Remove duplicates if any
            $allCategories = array_unique($allCategories);
            // Convert array to comma-separated string
            $kategoriler = implode(',', $allCategories);
        } else {
            $kategoriler = NULL;
        }

// Use $kategoriler as needed

        if (isset($_POST['marka'])) {
            if (is_array($_POST['marka'])) {
                $markalar = implode(',', $_POST['marka']);
            } elseif(!is_array($_POST['marka']) && !empty($_POST['marka'])) {
                $markalar = $_POST['marka'];
            }
        } else {
            $markalar = NULL;
        }


        $baslik = $_POST['baslik'];
        $aciklama = $_POST['aciklama'];
        $promosyon_kodu = generateUniqueCode();

        try {
            $query = "INSERT INTO b2b_promosyon (gecerlilik_tarihi, kategoriler, markalar, promosyon_kodu, tutar, doviz, kullanildi, minSepetTutar, urunler, max_kullanim_sayisi, kullanacak_uye_id, baslik, aciklama, aktif) 
            VALUES (:gecerlilik_tarihi, :kategoriler, :markalar, :promosyon_kodu, :tutar, :doviz, :kullanildi, :minSepetTutar, :urunler, :max_kullanim_sayisi, :kullanacak_uye_id, :baslik, :aciklama, :aktif)";
            $params = [
                'gecerlilik_tarihi' => $date,
                'kategoriler' => $kategoriler,
                'markalar' => $markalar,
                'promosyon_kodu' => $promosyon_kodu,
                'tutar' => $indirim_miktari,
                'doviz' => $doviz_birimi,
                'kullanildi' => 0,
                'minSepetTutar' => $min_sepet_tutar,
                'urunler' => $urunler,
                'max_kullanim_sayisi' => $max_kul,
                'kullanacak_uye_id' => $uyeler,
                'baslik' => $baslik,
                'aciklama' => $aciklama,
                'aktif' => 1
            ];
            $database->insert($query, $params);

            echo $promosyon_kodu;
        } catch (PDOException $e) {
            echo "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        echo "Geçersiz veri gönderimi.";
    }
}

function generateUniqueCode($length = 10) {
    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

?>