<?php

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);

$resource = $parts[4];

$id = $parts[5] ?? null;

if ($resource != "tasks") {
    
    //header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
    http_response_code(404);
    exit;
}


$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

$user_gateway = new UserGateway($database);


$codec = new JWTCodec($_ENV["SECRET_KEY"]);

//Featching all HTTP request headers
// $headers = apache_request_headers();
// echo $headers["Authorization"];

$auth = new Auth($user_gateway, $codec);

if (! $auth->authenticateAccessToken()) {
    exit;
}



$user_id = $auth->getUserID();



//Passing parameter for constructor method
$task_gateway = new TaskGateway($database);


$controller = new TaskController($task_gateway, $user_id);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);









