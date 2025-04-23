<?php
// returns the edited PDF (blob stream)
require 'config.php';
require 'vendor/autoload.php';
use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !isset($_FILES['pdfFile']) || $_FILES['pdfFile']['error']!==UPLOAD_ERR_OK)
{
    http_response_code(400); exit('bad upload');
}

/* -------- basic validation -------- */
$f = $_FILES['pdfFile'];
if ($f['size'] > MAX_FILE_SIZE || !in_array($f['type'], ALLOWED_FILE_TYPES))
    exit('invalid file');

$tmp = $f['tmp_name'];
$toDrop = array_flip(
    array_map('intval',
        array_filter(explode(',', $_POST['pagesToRemove'] ?? ''), 'strlen')
    )
);

/* -------- build the new PDF -------- */
$pdf = new Fpdi();
$total = $pdf->setSourceFile($tmp);

for ($i=1;$i<=$total;$i++) {
    if (isset($toDrop[$i])) continue;          // skip unwanted page
    $tpl = $pdf->importPage($i);
    $pdf->AddPage();
    $pdf->useTemplate($tpl);
}

/* -------- stream back -------- */
header('Content-Type: application/pdf');
header('Content-Disposition:inline; filename=modified.pdf');
$pdf->Output('I');
