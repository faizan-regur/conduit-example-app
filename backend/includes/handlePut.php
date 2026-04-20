<?php

function handlePut($conn, $data, $request){
    if($request === '/conduit/api/user'){
        if (empty($data['user']['email'])) {
            http_response_code(422);
            echo json_encode(["error" => "Email is required"]);
            exit;
        }

        $email = $conn->real_escape_string($data['user']['email']);
        $updates = [];

        if (!empty($data['user']['username'])) {
            $username = $conn->real_escape_string($data['user']['username']);
            $updates[] = "username='$username'";
        }

        if (!empty($data['user']['password'])) {
            $password = password_hash($data['user']['password'], PASSWORD_BCRYPT);
            $updates[] = "password='$password'";
        }

        if (isset($data['user']['bio'])) {
            $bio = $conn->real_escape_string($data['user']['bio']);
            $updates[] = "bio='$bio'";
        }

        if (isset($data['user']['image'])) {
            $image = $conn->real_escape_string($data['user']['image']);
            $updates[] = "image='$image'";
        }

        if (isset($data['user']['new_email']) && $data['user']['new_email'] !== '') {
            $newEmail = $conn->real_escape_string($data['user']['new_email']);
            $updates[] = "email='$newEmail'";
        }

        if (empty($updates)) {
            http_response_code(422);
            echo json_encode(["error" => "No fields to update"]);
            exit;
        }

        $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE email='$email'";

        if ($conn->query($query)) {
            if ($conn->affected_rows > 0) {
                echo json_encode(['message' => 'User data updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "User not found or no changes made"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update user data"]);
        }
    }
}