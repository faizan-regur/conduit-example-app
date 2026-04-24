<?php

use Firebase\JWT\JWT;

function login($data) {
    global $conn, $secret_key, $issuer; // Use the global database connection and config variables

    // Validate required fields
    if (!isset($data['user']['email']) || !isset($data['user']['password'])) {
        http_response_code(422);
        echo json_encode(["message" => "Missing required fields"]);
        return;
    }

    $email = $conn->real_escape_string($data['user']['email']);
    $password = $data['user']['password'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid email or password"]);
        return;
    }

    $stmt->bind_result($user_id, $username, $hashed_password);
    $stmt->fetch();

    // Verify the password
    if ($hashed_password && password_verify($password, $hashed_password)) {
        // Generate JWT token
        $issued_at = time();
        $expire = $issued_at + 3600; // 1 hour
        $payload = [
            'iat' => $issued_at,
            'exp' => $expire,
            'iss' => $issuer,
            'user_id' => $user_id,
            'email' => $email,
            'username' => $username
        ];
        $token = JWT::encode($payload, $secret_key, 'HS256');
        
        echo json_encode([
            "user" => [
                "id" => $user_id,
                "username" => $username,
                "email" => $email,
                "token" => $token
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Invalid email or password"]);
    }
}