<?php

function createArticle($data) {
    global $conn; // Use the global database connection
    
    // Validate required fields
    if (empty($data['article']['title']) || empty($data['article']['description']) || empty($data['article']['body'])) {
        http_response_code(400);
        echo json_encode(["error" => "All fields are required"]);
        return; 
    }
    $title = $data['article']['title'];
    $description = $data['article']['description'];
    $body = $data['article']['body'];

    // Check if the article already exists in the database
    $stmt = $conn->prepare("SELECT articleId FROM articles WHERE title = ?");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["message" => "Article already exists"]);
        return;
    }

    // prepare and execute the SQL statement to insert the new article into the database
    $stmt = $conn->prepare("INSERT INTO articles (title, description, body) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $body);
    if ($stmt->execute()) {
        $article_id = $stmt->insert_id; // Get the ID of the newly created article
        echo json_encode([
            "article" => [
                "id" => $article_id,
                "title" => $title,
                "description" => $description,
                "body" => $body
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error creating article"]);
    }
}