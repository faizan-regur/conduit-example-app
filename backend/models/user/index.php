<?php

// backend/models/user/index.php

include 'create.php'; // Include the register function for user registration
include 'login.php'; // Include the login function for user authentication 
include 'update.php'; // Include the update function for updating user information
include 'read.php'; // Include the read function for retrieving user information
include 'profiles.php'; // Include the profiles function for retrieving user full profile information

define("API_BASE_URL", "/conduit/api/");
define("REGISTER_ENDPOINT", API_BASE_URL . "users");
define('LOGIN_ENDPOINT', API_BASE_URL . 'users/login');
define('USER_ENDPOINT', API_BASE_URL . 'user'); // Endpoint to get current authenticated user's information
define('PROFILES_ENDPOINT', API_BASE_URL . 'profiles');

function user($data, $method, $request) {

    switch ($method) {
        case 'POST':
            if (str_contains($request, LOGIN_ENDPOINT)) {
                login($data); // Call the login function for user authentication
            } else if (str_contains($request, REGISTER_ENDPOINT)) {
                create($data); // Call the register function for user registration
            }
            break;
        case 'PUT':
            if (str_contains($request, USER_ENDPOINT)) {
                updateUser($data); // Call the update function to update user information
            }
            break;
        case 'GET':
            if (str_contains($request, USER_ENDPOINT)) {
                getCurrentUser(); // Call the function to get the current authenticated user's information
                }
            if(str_contains($request, PROFILES_ENDPOINT)){
                $params = getPathParms($request);
                getProfile($params);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
    }
}

function getPathParms($request){
    $path = parse_url($request, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $username = $segments[3];
    $username = urldecode($username);
    
    if (!$username) {
        http_response_code(400);
        echo json_encode(["error" => "Username is required"]);
        exit;
    }
    return $username;
}