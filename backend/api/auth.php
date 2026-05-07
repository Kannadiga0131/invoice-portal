<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request"
    ]);
    exit;
}

$email = $data["email"];
$password = $data["password"];
$action = $data["action"];

if ($action == "register") {

    $name = $data["name"];

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($check->num_rows > 0) {

        echo json_encode([
            "success" => false,
            "message" => "Email already exists"
        ]);

    } else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $conn->query("
            INSERT INTO users(name,email,password)
            VALUES('$name','$email','$hashed')
        ");

        echo json_encode([
            "success" => true
        ]);
    }

} elseif ($action == "login") {

    $result = $conn->query("
        SELECT * FROM users WHERE email='$email'
    ");

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            echo json_encode([
                "success" => true,
                "user" => $user
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "Wrong password"
            ]);
        }

    } else {

        echo json_encode([
            "success" => false,
            "message" => "User not found"
        ]);
    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Invalid action"
    ]);
}

?>