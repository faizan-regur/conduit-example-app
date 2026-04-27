<?php

include 'create.php'; // Include the article model functions
include 'read.php';
include 'update.php';
include 'delete.php';

define('ARTICLE_DIR',  '/articles'); // Define a constant for the article directory

function article($data, $method, $request) {

    switch ($method) {
        case 'POST':
            if (str_contains($request, ARTICLE_DIR)) {
                createArticle($data); // Call the function to create a new article
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Endpoint not found"]); // Return a JSON response if the endpoint is not found
            }
            break;
        case 'GET':
            if (str_contains($request, ARTICLE_DIR)) {
                readArticle(); // Call the function to read articles
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Endpoint not found"]); // Return a JSON response if the endpoint is not found
            }
            break;
        case 'PUT':
        if (str_contains($request, ARTICLE_DIR)) {
            $segments = explode('/', $request);
            $slug = end($segments); // Extract the slug from the request URL
            updateArticle($data, $slug); // Call the function to update an article with the
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Endpoint not found"]); // Return a JSON response if the endpoint is not found
        }
            break;	
        case 'DELETE':
        if (str_contains($request, ARTICLE_DIR)) {
            $segments = explode('/', $request);
            $slug = end($segments); // Extract the slug from the request URL
            deleteArticle($slug); // Call the function to delete an article with the extracted slug
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