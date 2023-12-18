<?php 

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

if($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);
    header("Allow: POST");
    exit;
}


//For acces token -------->
//Retrive an associative array of the JSON data passed in API request
$data = (array) json_decode(file_get_contents("php://input"), true);


// Without logi credentials
if(!array_key_exists("token", $data)) {

       http_response_code(400);
       echo json_encode(["message" => "missing token"]);
       exit; 
    }

$codec = new JWTCodec($_ENV["SECRET_KEY"]);



try {
    $payload = $codec->decode($data["token"]);

} catch (Exception) {

    http_response_code(400);
    echo json_encode(["message" => "invalid token"]);
    exit;
}

// If is valid getting user ID from the token

$user_id = $payload["sub"];

var_dump($user_id);