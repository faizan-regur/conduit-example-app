<?php
$host = "localhost";
$db_name = "conduit";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}
?>