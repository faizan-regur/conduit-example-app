<?php

function handleGet($conn, $request) {
    if ($request === '/conduit/api/user'){
        $query = "SELECT id, username, email FROM users";
        $result = $conn->query($query);

        if ($result === false) {
            http_response_code(500);
            echo json_encode(["error" => "Database query failed"]);
            exit;
        }

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["error" => "User not found"]);
            exit;
        }

        // Fetch the user data
        $users = [];    
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                "id" => $row['id'],
                "username" => $row['username'],
                "email" => $row['email']
            ];
        }
        echo json_encode(["users" => $users]);
    }
    // Get Profile
    if (str_contains($request, '/conduit/api/profiles')) {
        $path = parse_url($request, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        $username = end($segments);
        $username = urldecode($username);

        if (!$username) {
            http_response_code(400);
            echo json_encode(["error" => "Username is required"]);
            exit;
        }

        $stmt = $conn->prepare("SELECT username, bio, image, following FROM users WHERE username = ? LIMIT 1");
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode(["error" => "Database query preparation failed"]);
            exit;
        }

        $stmt->bind_param("s", $username);
        $executed = $stmt->execute();
        if ($executed === false) {
            http_response_code(500);
            echo json_encode(["error" => "Database query failed"]);
            exit;
        }

        $check = $stmt->get_result();
        if ($check === false) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to fetch query result"]);
            exit;
        }

        if ($check->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["error" => "User not found"]);
            exit;
        }

        $row = $check->fetch_assoc();
        echo json_encode(["profile" => $row]);
        $stmt->close();
    }
}