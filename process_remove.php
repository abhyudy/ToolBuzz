<?php
require_once 'vendor/autoload.php'; // Include Composer autoload for Fpdi and Fpdf

use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the incoming data
    $pdfData = $_POST['pdfData'] ?? '';
    $pagesToRemove = $_POST['pagesToRemove'] ?? '';

    // Validate the data
    if (empty($pdfData) || empty($pagesToRemove)) {
        http_response_code(400);
        exit('Missing required data');
    }

    // Decode the base64 PDF data
    $pdfBinary = base64_decode($pdfData, true);
    if ($pdfBinary === false) {
        http_response_code(400);
        exit('Invalid PDF data');
    }

    // Save the PDF temporarily
    $tempFile = tempnam(sys_get_temp_dir(), 'pdf_');
    file_put_contents($tempFile, $pdfBinary);

    // Parse pages to remove
    $pagesToRemoveArray = array_map('intval', explode(',', $pagesToRemove));

    // Validate pages to remove
    if (empty($pagesToRemoveArray)) {
        http_response_code(400);
        unlink($tempFile);
        exit('No pages specified to remove');
    }

    // Process the PDF to remove pages
    $outputFile = tempnam(sys_get_temp_dir(), 'pdf_');
    try {
        // Initialize FPDI
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($tempFile);

        // Validate pages to remove against the total page count
        foreach ($pagesToRemoveArray as $page) {
            if ($page < 1 || $page > $pageCount) {
                http_response_code(400);
                unlink($tempFile);
                exit("Invalid page number: $page");
            }
        }

        // Import all pages except the ones to remove
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            if (!in_array($pageNo, $pagesToRemoveArray)) {
                $pdf->AddPage();
                $tplIdx = $pdf->importPage($pageNo);
                $pdf->useTemplate($tplIdx);
            }
        }

        // Output the new PDF to the temporary file
        $pdf->Output('F', $outputFile);

        // Read the processed PDF and send it back
        $outputData = file_get_contents($outputFile);
        header('Content-Type: application/pdf');
        echo $outputData;
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Error processing PDF: ' . $e->getMessage();
    } finally {
        // Clean up temporary files
        unlink($tempFile);
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }
    }
} else {
    http_response_code(405);
    exit('Method not allowed');
}
?>