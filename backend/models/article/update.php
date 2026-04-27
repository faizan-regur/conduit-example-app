<?php

function updateArticle($data, $slug) {

    global $conn; // Assuming you have a database connection variable
    
    // Validate the input data (you can add more validation as needed)
    if (empty($data['article']['title']) || empty($data['article']['description']) || empty($data['article']['body'])) {
        http_response_code(400);
        echo json_encode(["message" => "Title, description, and body are required"]); // Return a JSON response if validation fails
        return;
    }

    // Check if the article exists    
    $stmt = $conn->prepare("SELECT articleId FROM articles WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["message" => "Article not found"]); // Return a JSON response if the article is not found
        return;
    }
    $articleId = $result->fetch_assoc()['articleId'];

    // Update the article in the database
    $stmt = $conn->prepare("UPDATE articles SET title = ?, description = ?, body = ? WHERE articleId = ?");
    $stmt->bind_param("sssi", $data['article']['title'], $data['article']['description'], $data['article']['body'], $articleId);
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "Article updated successfully"]); // Return a JSON response if the update is successful
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to update article"]); // Return a JSON response if there was an error during the update
    }
}