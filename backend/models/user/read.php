<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getCurrentUser() {
    global $conn; // Use the global database connection variable
    global $secret_key; // Use the global secret key for JWT validation

    // Get the Authorization header from the request
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["message" => "Authorization header missing"]);
        return;
    }

    $authHeader = $headers['Authorization'];
    list($type, $token) = explode(" ", $authHeader, 2);

    if (strtolower($type) !== 'bearer' || empty($token)) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid authorization header format"]);
        return;
    }

    // Validate the token and extract user information
    try {
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        $userId = $decoded->sub;

        // Fetch user information from the database
        $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
            return;
        }

        $user = $result->fetch_assoc();
        
        echo json_encode([
            "user" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "email" => $user['email']
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid or expired token"]);
    }
}