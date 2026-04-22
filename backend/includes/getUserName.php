<?php

function getUserName($request){
    $path = parse_url($request, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $username = end($segments);
    $username = urldecode($username);
    
    if (!$username) {
        http_response_code(400);
        echo json_encode(["error" => "Username is required"]);
        exit;
    }
    return $username;
}