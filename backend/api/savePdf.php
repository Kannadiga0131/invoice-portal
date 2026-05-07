<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['pdf'])) {

    echo json_encode([
        "success" => false,
        "message" => "No PDF data"
    ]);

    exit;
}

$pdf = $data['pdf'];

$pdf = str_replace(
    'data:application/pdf;base64,',
    '',
    $pdf
);

$pdf = str_replace(
    ' ',
    '+',
    $pdf
);

$pdfData = base64_decode($pdf);

$folder = __DIR__ . "/../invoices/";

if (!file_exists($folder)) {

    mkdir($folder, 0777, true);
}

$fileName =
    "invoice_" .
    time() .
    ".pdf";

$filePath =
    $folder .
    $fileName;

$result =
    file_put_contents(
        $filePath,
        $pdfData
    );

if ($result) {

    echo json_encode([
        "success" => true,
        "file" => $fileName
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "PDF save failed"
    ]);
}
?>