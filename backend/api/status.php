<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Content-Type: application/json");

include("../config/db.php");

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = $data['id'];
$status = $data['status'];

$sql = "
UPDATE invoices
SET status='$status'
WHERE id='$id'
";

if ($conn->query($sql)) {

    echo json_encode([
        "success" => true
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
}

?>