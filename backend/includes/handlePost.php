<?php
use Firebase\JWT\JWT;


function handlePost($conn, $data, $request) {
    global $secret_key;
    global $issuer;

    if ($request === '/conduit/api/users'){
        // Validation
        if (
            empty($data['user']['username']) ||
            empty($data['user']['email']) ||
            empty($data['user']['password'])
        ) {
            http_response_code(422);
            echo json_encode(["error" => "All fields are required"]);
            exit;
        }

        $username = $conn->real_escape_string($data['user']['username']);
        $email = $conn->real_escape_string($data['user']['email']);
        $password = password_hash($data['user']['password'], PASSWORD_BCRYPT);


        $check = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            http_response_code(409);
            echo json_encode(['error' => "Email already exists"]);
            exit;
        }

        $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

        if ($conn->query($query)) {
            $user_id = $conn->insert_id;
            $payload = [
                "iss" => $issuer,
                "iat" => time(),
                "exp" => time() + (60 * 60), // 1 hour
                "data" => [
                    "id" => $user_id,
                    "email" => $email
                ]
            ];
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            echo json_encode([
                "user" => [
                    "id" => $user_id,
                    "username" => $username,
                    "email" => $email,
                    "token" => $jwt
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to create user"]);
        }
    }
    else if ($request === '/conduit/api/users/login'){
        // Validation
        if (
            empty($data['user']['email']) ||
            empty($data['user']['password'])
        ) {
            http_response_code(422);
            echo json_encode(["error" => "All fields are required"]);
            exit;
        }
    
        $email = $conn->real_escape_string($data['user']['email']);
        $password = $data['user']['password'];
    
        $check = $conn->query("SELECT id, username, email, password FROM users WHERE email='$email' LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $user = $check->fetch_assoc();
    
            if (password_verify($password, $user['password'])) {
                $payload = [
                "iss" => $issuer,
                "iat" => time(),
                "exp" => time() + (60 * 60), // 1 hour
                "data" => [
                    "id" => $user['id'],
                    "email" => $user['email']
                ]
                ];
                $jwt = JWT::encode($payload, $secret_key, 'HS256');
    
                echo json_encode([
                    "user" => [
                        "id" => $user['id'],
                        "username" => $user['username'],
                        "email" => $user['email'],
                        "token" => $jwt
                    ]
                ]);
                // start session and store user info
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Incorrect password"]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["error" => "User not found"]);
        }
    }
    
    // Follow User
    if(str_contains($request, '/follow')){    
        $targeted_user = getUserName($request);    
        $stmt = $conn->prepare("SELECT username, bio, image, following FROM users WHERE username = ? LIMIT 1");
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode(["error" => "Database query preparation failed"]);
            exit;
        }

        $stmt->bind_param("s", $targeted_user);
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