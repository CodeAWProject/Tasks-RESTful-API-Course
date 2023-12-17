<?php

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

if($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);
    header("Allow: POST");
    exit;
}

//Retrive an associative array of the JSON data passed in API request
$data = (array) json_decode(file_get_contents("php://input"), true);


// Without logi credentials
if(!array_key_exists("username", $data) ||
    !array_key_exists("password", $data)) {

       http_response_code(400);
       echo json_encode(["message" => "missing login credentials"]);
       exit; 
    }

 echo json_encode($data);   