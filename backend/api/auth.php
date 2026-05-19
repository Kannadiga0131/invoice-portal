<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {

    echo json_encode([
        "success" => false,
        "message" => "No data received"
    ]);

    exit;
}

$action =
    $data["action"] ?? "";

// ================= REGISTER =================

if ($action === "register") {

    $name =
        $data["name"] ?? "";

    $email =
        $data["email"] ?? "";

    $password =
        $data["password"] ?? "";

    if (
        empty($name) ||
        empty($email) ||
        empty($password)
    ) {

        echo json_encode([
            "success" => false,
            "message" => "All fields required"
        ]);

        exit;
    }

    // CHECK EMAIL
    $check =
        $conn->prepare(
            "SELECT id FROM users WHERE email=?"
        );

    $check->bind_param(
        "s",
        $email
    );

    $check->execute();

    $result =
        $check->get_result();

    if (
        $result->num_rows > 0
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Email already exists"
        ]);

        exit;
    }

    // HASH PASSWORD
    $hashedPassword =
        password_hash(
            $password,
            PASSWORD_DEFAULT
        );

    // INSERT USER
    $stmt =
        $conn->prepare(
            "INSERT INTO users(name,email,password)
             VALUES(?,?,?)"
        );

    $stmt->bind_param(
        "sss",
        $name,
        $email,
        $hashedPassword
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Registration successful"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Registration failed"
        ]);
    }

    exit;
}

// ================= LOGIN =================

if ($action === "login") {

    $email =
        $data["email"] ?? "";

    $password =
        $data["password"] ?? "";

    if (
        empty($email) ||
        empty($password)
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Email & password required"
        ]);

        exit;
    }

    $stmt =
        $conn->prepare(
            "SELECT * FROM users WHERE email=?"
        );

    $stmt->bind_param(
        "s",
        $email
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    if (
        $result->num_rows === 0
    ) {

        echo json_encode([
            "success" => false,
            "message" => "User not found"
        ]);

        exit;
    }

    $user =
        $result->fetch_assoc();

    if (
        password_verify(
            $password,
            $user["password"]
        )
    ) {

        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $user["id"],
                "name" => $user["name"],
                "email" => $user["email"]
            ]
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Invalid password"
        ]);
    }

    exit;
}

// ================= INVALID =================

echo json_encode([
    "success" => false,
    "message" => "Invalid request"
]);
?>