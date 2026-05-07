<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

include("../config/db.php");

$method = $_SERVER['REQUEST_METHOD'];


// ===== GET INVOICES =====
if ($method == "GET") {

    $user_id = $_GET['user_id'];

    $sql = "
    SELECT invoices.*, clients.name as client_name
    FROM invoices
    JOIN clients
    ON invoices.client_id = clients.id
    WHERE invoices.user_id='$user_id'
    ORDER BY invoices.id DESC
    ";

    $result = $conn->query($sql);

    $invoices = [];

    while ($row = $result->fetch_assoc()) {

        $invoices[] = $row;
    }

    echo json_encode($invoices);
}



// ===== CREATE INVOICE =====
if ($method == "POST") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $client_id =
      $data['client_id'];

    $subtotal =
      $data['subtotal'];

    $gst =
      $data['gst'];

    $total =
      $data['total'];

    $user_id =
      $data['user_id'];

    $items =
      json_encode(
        $data['items']
      );

    $status = "Pending";

    $invoice_number =
      "INV-" . time();

    $date =
      date("Y-m-d");

    $sql = "
    INSERT INTO invoices(

      client_id,
      subtotal,
      gst,
      total,
      status,
      user_id,
      items,
      invoice_number,
      invoice_date

    )

    VALUES(

      '$client_id',
      '$subtotal',
      '$gst',
      '$total',
      '$status',
      '$user_id',
      '$items',
      '$invoice_number',
      '$date'
    )
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
}

?>