<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

include("../config/db.php");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {

    $user_id = $_GET['user_id'];

    $result = $conn->query("
        SELECT * FROM clients
        WHERE user_id='$user_id'
        ORDER BY id DESC
    ");

    $clients = [];

    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }

    echo json_encode($clients);
}

if ($method == "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $address = $data['address'];
    $user_id = $data['user_id'];

    $sql = "
        INSERT INTO clients(name,email,phone,address,user_id)
        VALUES('$name','$email','$phone','$address','$user_id')
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