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
}