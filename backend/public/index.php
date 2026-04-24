<?php
// backend/public/index.php
// This file serves as the entry point for all API requests. It routes requests to the appropriate functions based on the URL and HTTP method.

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload dependencies using Composer

$request = $_SERVER['REQUEST_URI']; // Get the request URI to determine which endpoint is being accessed
$method = $_SERVER['REQUEST_METHOD']; // Get the HTTP method to determine which function to call for the endpoint
$data = json_decode(file_get_contents("php://input"), true); // Get the request body and decode it from JSON to an associative array for use in the functions

header("Content-Type: application/json"); // Set the content type to JSON for all responses

include '../config/db_connection.php'; // initialize database connection here
include '../config/config.php'; // include configuration settings here
include '../models/user/index.php'; // include user model functions here

define("USER", 'user');
define("ARTICLE", 'article');
define("COMMENT", 'comment');
define("TAG", 'tag');

if (str_contains($request, USER)) {
    user($data, $method, $request); // Call the user function to handle user-related requests (registration and login)
} 
else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]); // Return a JSON response if the endpoint is not found
}

