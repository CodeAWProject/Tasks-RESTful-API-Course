<?php 

class Auth
{
    private int $user_id;

    public function __construct(private UserGateway $user_gateway) {
            
    }
    public function authenticateAPIKey(): bool
    {
        
        //Checking if the API key is available in the request
        if (empty($_SERVER["HTTP_X_API_KEY"])) {

            http_response_code(400);
            echo json_encode(["message" => "missing API key"]);
            return false;
        }

        $api_key = $_SERVER["HTTP_X_API_KEY"];

        $user = $this->user_gateway->getByAPIKey($api_key);

        if ($user === false) {

            http_response_code(401);
            echo json_encode(["message" => "invalid API key"]);
            return false;
        
        }

        $this->user_id = $user["id"];

        return true;
    }

    public function getUserID(): int
    {
        return $this->user_id;
    }

    public function authenticateAccessToken(): bool
    {
        //If this regular expression matches the header value, the second element will contain the actual token value
       if ( ! preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches))
       {
        http_response_code(400);
        echo json_encode(["message" => "incomplete authorization header"]);
        return false;
       }

       //Decoding acces token
       $plain_text = base64_decode($matches[1], true);


       if($plain_text === false)
       {

        http_response_code(400);
        echo json_encode(["message" => "invalid authorization header"]);
        return false;
       }

       //Get an aray instead of an object
       $data = json_decode($plain_text, true);


       if ($data === null) {

        http_response_code(400);
        echo json_encode(["message" => "invalid JSON"]);
       }

       $this->user_id = $data["id"];

       return true;
    }
}