<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column headers
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'NIS');

// Make headers bold
$sheet->getStyle('A1:C1')->getFont()->setBold(true);

// Auto-size columns
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);

// Add sample data
$sheet->setCellValue('A2', 'Rizqi Ahsan Setiawan');
$sheet->setCellValue('B2', 'XII RPL 5');
$sheet->setCellValue('C2', '1234567890');

// Set the content type
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="template_import_siswa.xlsx"');
header('Cache-Control: max-age=0');

// Save to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
