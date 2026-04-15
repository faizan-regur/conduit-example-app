<?php
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

header("Content-Type: application/json");
include '../config/db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn, $data);
        break;
    default:
        echo json_encode(['message' => 'Invalid request method']);
        break;
}

function handlePost($conn, $data) {

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

        echo json_encode([
            "user" => [
                "id" => $user_id,
                "username" => $username,
                "email" => $email
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to create user"]);
    }
}

function handleGet($conn) {

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

// if ($request === '/conduit/api/users/login' && $method === 'POST') {
//     $input = json_decode(file_get_contents("php://input"), true);
//     // Validation
//     if (
//         empty($input['user']['email']) ||
//         empty($input['user']['password'])
//     ) {
//         http_response_code(422);
//         echo json_encode(["error" => "All fields are required"]);
//         exit;
//     }

//     $email = $conn->real_escape_string($input['user']['email']);
//     $password = $input['user']['password'];

//     $check = $conn->query("SELECT id, username, email, password FROM users WHERE email='$email' LIMIT 1");
//     if ($check && $check->num_rows > 0) {
//         $user = $check->fetch_assoc();

//         if (password_verify($password, $user['password'])) {
//             echo json_encode([
//                 "user" => [
//                     "id" => $user['id'],
//                     "username" => $user['username'],
//                     "email" => $user['email']
//                 ]
//             ]);
//         } else {
//             http_response_code(401);
//             echo json_encode(["error" => "Incorrect password"]);
//         }
//     } else {
//         http_response_code(404);
//         echo json_encode(["error" => "User not found"]);
//     }
// }
