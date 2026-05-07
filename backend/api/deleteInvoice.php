<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Content-Type: application/json");

include("../config/db.php");

$id = $_GET['id'];

$sql = "
DELETE FROM invoices
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