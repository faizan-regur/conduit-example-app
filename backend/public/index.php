<?php
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

header("Content-Type: application/json");
include '../config/db_connection.php';
include '../includes/handleGet.php';
include '../includes/handlePost.php';


$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        handleGet($conn, $request);
        break;
    case 'POST':
        handlePost($conn, $data, $request);
        break;
    default:
        echo json_encode(['message' => 'Invalid request method']);
        break;
}