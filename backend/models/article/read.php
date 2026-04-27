<?php

function readArticle(){
    global $conn;
    $sql = "SELECT * FROM articles"; // SQL query to select all articles
    $result = $conn->query($sql); // Execute the query


    if ($result === false) {
        http_response_code(500);
        echo json_encode(["error" => "Database query failed"]);
        exit;
    }

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["message" => "Articles not found"]);
        return;
    }

    if ($result->num_rows > 0) {
        $articles = array();
        while($row = $result->fetch_assoc()) {
            $articles[] = $row; // Add each article to the articles array
        }
        echo json_encode($articles); // Return the articles as a JSON response
    } else {
        echo json_encode([]); // Return an empty array if no articles are found
    }
}