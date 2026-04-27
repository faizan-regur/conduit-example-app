<?php

include 'create.php'; // Include the article model functions

function article($data, $method, $request) {

    switch ($method) {
        case 'POST':
            if (str_contains($request, '/articles')) {
                createArticle($data); // Call the function to create a new article
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Endpoint not found"]); // Return a JSON response if the endpoint is not found
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]); // Return a JSON response if the HTTP method is not allowed
            break;
    }
}