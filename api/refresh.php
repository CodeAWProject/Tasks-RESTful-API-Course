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
//Getting the user record from the database based on the ID in the refresh token

$user_id = $payload["sub"];


$database = new Database($_ENV["DB_HOST"],
                        $_ENV["DB_NAME"],
                        $_ENV["DB_USER"],
                        $_ENV["DB_PASS"]);

$user_gateway = new UserGateway($database);

$user = $user_gateway->getByID($user_id);

// If no record is found with that ID
if ($user === false) {

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

require __DIR__ . "/tokens.php";