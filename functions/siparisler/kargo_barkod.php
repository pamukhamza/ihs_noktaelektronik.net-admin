<?php
include ('../db.php');
require_once('../../vendor/tcpdf/tcpdf.php');

$database = new Database();

function kargopdf($uye_id, $sip_id, $cargoKey)
{
    global $database;
    // Retrieve order information
    $q = "SELECT * FROM b2b_siparisler WHERE id = :sip_id";
    $params = [
        'sip_id' => $sip_id
    ];
    $sip = $database->fetch($q, $params);

    // Retrieve user information
    $q = "SELECT * FROM uyeler WHERE id = :uye_id";
    $params = [
        'id' => $uye_id
    ];
    $uye = $database->fetch($q, $params);

    $il_id = $sip["teslimat_il"];
    $ilce_id = $sip["teslimat_ilce"];

    // Retrieve address information
    $q = "SELECT * FROM adresler WHERE uye_id = :uye_id AND aktif = :aktif";
    $params = [
        'uye_id' => $uye_id,
        'aktif' => '1'
    ];
    $adressorgu = $database->fetch($q, $params);


    $q = "SELECT * FROM iller WHERE il_id = :il_id";
    $params = [
        'il_id' => $il_id
    ];
    $iller = $database->fetch($q, $params);


    $il = $iller["il_adi"];

    $q = "SELECT * FROM ilceler WHERE ilce_id = :ilce_id";
    $params = [
        'ilce_id' => $ilce_id
    ];
    $ilceler = $database->fetch($q, $params);


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
    $pdf->Image('../../assets/images/logo_new.png', 10, 20, 40, '', 'PNG');
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
    $pdfDosyaYolu = realpath("../../assets/uploads/kargo/") . "/" . $pdfDosyaAdi;
    $pdf->Output($pdfDosyaYolu, 'F');

    $query = "INSERT INTO b2b_kargo_pdf (sip_id, dosya) VALUES (:id, :dosya)";
    $params = [
        'id' => $sip_id,
        'dosya' => $pdfDosyaAdi
    ];
    $database->insert($query, $params);
}
?>