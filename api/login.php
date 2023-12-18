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
if(!array_key_exists("username", $data) ||
    !array_key_exists("password", $data)) {

       http_response_code(400);
       echo json_encode(["message" => "missing login credentials"]);
       exit; 
    }


$database = new Database($_ENV["DB_HOST"],
                        $_ENV["DB_NAME"],
                        $_ENV["DB_USER"],
                        $_ENV["DB_PASS"]);


$user_gateway = new UserGateway($database);

$user = $user_gateway->getByUsername($data["username"]);


// Invalid user
if ($user === false) {

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

// Invalid password
if(! password_verify($data["password"], $user["password_hash"])) {

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

$payload = [
    "sub" => $user["id"],
    "name" => $user["name"]
];


// //base64 will turn it into a simple string of characters
// //It the access token is encoded ID and name of the iser
// $access_token = base64_encode(json_encode($payload));

$codec = new JWTCodec($_ENV["SECRET_KEY"]);
$access_token = $codec->encode($payload);

echo json_encode([
    "access_token" => $access_token
]);



