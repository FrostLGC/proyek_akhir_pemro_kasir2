<?php
ini_set('display_errors', 0);
error_reporting(0);

session_start();
ob_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/fpdf/fpdf.php';

$id = intval($_GET['id']);

// ambil pesanan
$q = mysqli_query($conn, "SELECT * FROM pesanan WHERE id=$id");
if (!$q || mysqli_num_rows($q) == 0) {
    ob_end_clean();
    exit;
}
$pes = mysqli_fetch_assoc($q);

// detail item
$det = mysqli_query($conn,
    "SELECT dp.*, m.nama_menu 
     FROM detail_pesanan dp 
     JOIN menu m ON dp.menu_id = m.id 
     WHERE dp.pesanan_id = $id"
);

// transaksi terakhir
$trx = mysqli_query($conn,
    "SELECT * FROM transaksi 
     WHERE pesanan_id = $id 
     ORDER BY id DESC LIMIT 1"
);
$trans = mysqli_num_rows($trx) ? mysqli_fetch_assoc($trx) : null;

// ===== PDF =====
$pdf = new FPDF('P','mm',[80,200]);
$pdf->AddPage();

// Header
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'Cafe AHMF',0,1,'C');

$pdf->Ln(1);
$pdf->Cell(0,3,'-------------------------------------------------------',0,1,'C');

$pdf->SetFont('Arial','',9);
$pdf->Cell(0,5,'Struk: '.$pes['kode'],0,1);
$pdf->Cell(0,5,'Tanggal: '.$pes['created_at'],0,1);
$pdf->Cell(0,5,'Nama: '.$pes['nama_pemesan'],0,1);

// tampilkan meja jika ada
if (!empty($pes['meja'])) {
    $pdf->Cell(0,5,'Meja: '.$pes['meja'],0,1);
}

$pdf->Ln(1);
$pdf->Cell(0,3,'-------------------------------------------------------',0,1,'C');

// Detail item
while ($row = mysqli_fetch_assoc($det)) {

    // item utama
    $pdf->Cell(
        0,
        5,
        $row['nama_menu'].' x'.$row['jumlah'].'  Rp '.number_format($row['subtotal'],0,',','.'),
        0,
        1
    );

    // catatan (jika ada)
    if (!empty($row['catatan'])) {
        $pdf->SetFont('Arial','I',8);
        $pdf->MultiCell(
            0,
            4,
            '  Catatan: '.$row['catatan'],
            0
        );
        $pdf->SetFont('Arial','',9);
    }
}


$pdf->Ln(1);
$pdf->Cell(0,3,'-------------------------------------------------------',0,1,'C');

$pdf->Ln(2);
$pdf->Cell(0,5,'Total: Rp '.number_format($pes['total_harga'],0,',','.'),0,1);

// pembayaran
if ($trans) {
    $pdf->Cell(0,5,'Bayar: Rp '.number_format($trans['bayar'],0,',','.'),0,1);
    $pdf->Cell(0,5,'Kembali: Rp '.number_format($trans['kembali'],0,',','.'),0,1);
}

$pdf->Ln(1);
$pdf->Cell(0,3,'-------------------------------------------------------',0,1,'C');

$pdf->Cell(0,5,'Terima kasih',0,1,'C');
$pdf->Cell(0,5,'Silahkan Datang Kembali',0,1,'C');

ob_end_clean();
$pdf->Output('I','struk_'.$pes['kode'].'.pdf');
exit;
