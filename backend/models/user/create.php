<?php

function create($data) {
    global $conn; // Use the global database connection variable

    // Validate required fields
    if (!isset($data['user']['username']) || !isset($data['user']['email']) || !isset($data['user']['password'])) {
        http_response_code(422);
        echo json_encode(["message" => "Missing required fields"]);
        return;
    }

    $username = $conn->real_escape_string($data['user']['username']);
    $email = $conn->real_escape_string($data['user']['email']);
    $password = password_hash($data['user']['password'], PASSWORD_BCRYPT);

    // Check if the username or email already exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["message" => "Username or email already exists"]);
        return;
    }

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        
        echo json_encode([
            "user" => [
                "id" => $user_id,
                "username" => $username,
                "email" => $email,            
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error registering user"]);
    }
}

