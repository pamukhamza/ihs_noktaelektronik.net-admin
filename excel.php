<?php
// Include the PhpSpreadsheet library (Make sure you've installed it via Composer)
require 'vendor/autoload.php';

// Database connection
$host = 'localhost'; // Your database host
$user = 'root'; // Your database username
$pass = ''; // Your database password
$dbname = 'nokta'; // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check for database connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch data from the table
$query = "
    SELECT n.id, n.UrunKodu, n.UrunAdiTR, n.UrunAdiEN, m.title AS MarkaTitle  FROM nokta_urunler n
    LEFT JOIN nokta_urun_markalar m ON n.MarkaID = m.id
";
$result = $conn->query($query);

// Check if there are rows to export
if ($result->num_rows > 0) {
    // Create a new spreadsheet object
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the header row (titles of your columns)
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'UrunKodu');
    $sheet->setCellValue('C1', 'UrunAdiTR');
    $sheet->setCellValue('D1', 'UrunAdiEN');
    $sheet->setCellValue('E1', 'MarkaTitle');

    // Fetch the data and insert it into the spreadsheet
    $rowNumber = 2; // Start inserting data from row 2
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNumber, $row['id']);
        $sheet->setCellValue('B' . $rowNumber, $row['UrunKodu']);
        $sheet->setCellValue('C' . $rowNumber, $row['UrunAdiTR']);
        $sheet->setCellValue('D' . $rowNumber, $row['UrunAdiEN']);
        $sheet->setCellValue('E' . $rowNumber, $row['MarkaTitle']);
        $rowNumber++;
    }

    // Create an Excel writer and save the file
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    // Set headers to ensure proper file download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="products_data.xlsx"');
    header('Cache-Control: max-age=0');

    // Output the file directly to the browser
    $writer->save('php://output');

    // Ensure that no further content is sent
    exit;
} else {
    echo "No data found!";
}

// Close the database connection
$conn->close();
?>
