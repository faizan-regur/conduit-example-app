<?php

function handlePut($conn, $data, $request){
    if($request === '/conduit/api/user'){
        // Validation
        if(empty($data['user']['bio']) && empty($data['user']['image']) && empty($data['user']['email']) && empty($data['user']['password']) && empty($data['user']['username'])){
            http_response_code(422);
            echo json_encode(["error" => "Please enter value to update"]);
            exit;
        }

        // $username = $conn->real_escape_string($data['user']['username']);
        // $email = $conn->real_escape_string($data['user']['email']);
        // $password = password_hash($data['user']['password'], PASSWORD_BCRYPT);
        // $image = $conn->real_escape_string($data['user']['image']);
        // $bio = $conn->real_escape_string($data['user']['bio']);

    }
}