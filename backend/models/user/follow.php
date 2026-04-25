<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function follow_user($params){
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
        $userId = $decoded->user_id;

        $usernameToFollow = $params;

        // Fetch the ID of the user to follow from the database
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $usernameToFollow);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "User to follow not found"]);
            return;
        }

        $userToFollow = $result->fetch_assoc();
        $userToFollowId = $userToFollow['id'];

        // Insert a new record into the followers table
        $stmt = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $userToFollowId);
        
        if ($stmt->execute()) {
            // UPDATE folowing status in users table
            $update = $conn->prepare("UPDATE users SET following = 1 WHERE id = ?");
            if ($update === false) {
                http_response_code(500);
                echo json_encode(["error" => "Database query preparation failed"]);
                exit;
            }
            $update->bind_param("i", $userId);
            $executed_update = $update->execute();
            if ($executed_update === false) {
                http_response_code(500);
                echo json_encode(["error" => "Database query failed"]);
                exit;
            }
            // Return full profile
            getProfile($usernameToFollow);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to follow user"]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid or expired token"]);
    }
}