<?php

function deleteArticle($slug) {
    global $conn; // Use the global database connection

    // Check if the article exists in the database
    $stmt = $conn->prepare("SELECT articleId FROM articles WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["message" => "Article not found"]);
        return;
    }

    // Prepare and execute the SQL statement to delete the article from the database
    $stmt = $conn->prepare("DELETE FROM articles WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    if ($stmt->execute()) {
        echo json_encode(["message" => "Article deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error deleting article"]);
    }
}