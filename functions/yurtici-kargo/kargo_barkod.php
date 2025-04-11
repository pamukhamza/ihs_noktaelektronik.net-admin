<?php
require_once('../../vendor/tcpdf/tcpdf.php');
$host = "noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com";
$username = "nokta";
$password = "Dell28736.!";
$database = "noktanetdb";

try {
    // Establish PDO connection
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set UTF-8 charset
    $db->exec("set names utf8");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
function kargopdf($uye_id, $sip_id, $cargoKey)
{
    global $db;
    // Retrieve order information
    $q = $db->prepare("SELECT * FROM b2b_siparisler WHERE id = :sip_id");
    $q->bindParam(':sip_id', $sip_id, PDO::PARAM_INT);
    $q->execute();
    $sip = $q->fetch(PDO::FETCH_ASSOC);

    // Retrieve user information
    $q = $db->prepare("SELECT * FROM uyeler WHERE id = :uye_id");
    $q->bindParam(':uye_id', $uye_id, PDO::PARAM_INT);
    $q->execute();
    $uye = $q->fetch(PDO::FETCH_ASSOC);

    $il_id = $sip["teslimat_il"];
    $ilce_id = $sip["teslimat_ilce"];

    // Retrieve address information
    $q = $db->prepare("SELECT * FROM b2b_adresler WHERE uye_id = :uye_id AND aktif = '1'");
    $q->bindParam(':uye_id', $uye_id, PDO::PARAM_INT);
    $q->execute();
    $adressorgu = $q->fetch(PDO::FETCH_ASSOC);

    $q = $db->prepare("SELECT * FROM iller WHERE il_id = :il_id");
    $q->bindParam(':il_id', $il_id, PDO::PARAM_INT);
    $q->execute();
    $iller = $q->fetch(PDO::FETCH_ASSOC);

    $il = $iller["il_adi"];

    $q = $db->prepare("SELECT * FROM ilceler WHERE ilce_id = :ilce_id");
    $q->bindParam(':ilce_id', $ilce_id, PDO::PARAM_INT);
    $q->execute();
    $ilceler = $q->fetch(PDO::FETCH_ASSOC);

    $ilce = $ilceler["ilce_adi"];

    $uyeAdSoyad = $sip["teslimat_ad"] . ' ' . $sip["teslimat_soyad"];
    $tel = $sip["teslimat_telefon"];
    $adres = $sip["teslimat_adres"];
    $firmaUnvani = $sip["teslimat_firmaadi"];

    // Create new PDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Nokta Elektronik ve Bilişim Sistemleri San. Tic. A.Ş.');
    $pdf->SetAuthor('');
    $pdf->SetTitle('Kargo');
    $pdf->SetSubject('Kargo');
    $pdf->SetKeywords('Kargo');

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('dejavusans', '', 12);

    // Add company logo and name
    $pdf->Image('../assets/images/logo_new.png', 10, 20, 40, '', 'PNG');
    $pdf->SetXY(10, 32); // Move to the right of the logo

    // Add title
    $pdf->SetFont('dejavusans', 'B', 16);

    // Draw a horizontal line
    $pdf->SetLineWidth(0.1);
    $pdf->Line(10, 50, $pdf->getPageWidth() - 10, 50);

    // Payer information and cargo details
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->SetXY(10, 50); // Move down below the line
    $pdf->SetX(10);
    $pdf->Cell(20, 10, 'Alıcı', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . (!empty($uyeAdSoyad) ? $uyeAdSoyad : $firmaUnvani), 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(20, 10, 'Telefon', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $tel, 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(20, 10, 'Adres', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $adres, 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(20, 10, '', 0, 0, 'L');
    $pdf->Cell(0, 10, '  ' . $il . '/' . $ilce, 0, 1, 'L');
    $pdf->SetX(10);

    // Draw a horizontal line
    $pdf->SetLineWidth(0.1);
    $pdf->Line(10, 100, $pdf->getPageWidth() - 10, 100);

    // Add barcode
    $pdf->SetXY(10, 140); // Move down to a new section
    $style = array(
        'border' => 0,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, // array(255,255,255)
        'text' => true, // print the barcode text below the barcode
        'font' => 'helvetica', // barcode text font
        'fontsize' => 8, // barcode text font size in points
        'stretchtext' => 4 // stretch text
    );
    $pdf->write1DBarcode($cargoKey, 'C128', 40, 120, '', '', 1, $style, 'N');
    $pdf->Text(55, 115, 'Barkod No: ' . $cargoKey);
    $pdf->Text(55, 110, 'Web servis bilgi: 187205434');

    $pdfDosyaAdi = 'kargo_' . uniqid() . '.pdf';
    $pdfDosyaYolu = realpath("../assets/uploads/kargo/") . "/" . $pdfDosyaAdi;
    $pdf->Output($pdfDosyaYolu, 'F');

    $query = "INSERT INTO b2b_kargo_pdf (sip_id, dosya) VALUES (?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$sip_id, $pdfDosyaAdi]);
}
?>