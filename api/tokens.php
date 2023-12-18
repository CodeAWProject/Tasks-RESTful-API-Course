<?php 

$payload = [
    "sub" => $user["id"],
    "name" => $user["name"],
    "exp" => time() + 20
];


// //base64 will turn it into a simple string of characters
// //It the access token is encoded ID and name of the iser
// $access_token = base64_encode(json_encode($payload));


$access_token = $codec->encode($payload);

$refresh_token = $codec->encode([
    "sub" => $user["id"],
    //5 days
    "exp" => time() + 432000
]);

echo json_encode([
    "access_token" => $access_token,
    "refresh_token" => $refresh_token
]);