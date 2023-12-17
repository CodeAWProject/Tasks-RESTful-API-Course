<?php

class TaskController
{

    public function __construct(private TaskGateway $gateway)
    {

    }
    public function processRequest(string $method, ?string $id): void
    {
        if ($id === null) {
            
            if ($method == "GET") {
                
                echo json_encode($this->gateway->getAll());

            } elseif ($method == "POST") {
                
                echo "create";
            } else {
                
                $this->respondMethodNotAllowed("GET, POST");
            }
        } else {
            
            switch ($method) {
                
                case "GET":
                    echo json_encode($this->gateway->get($id));
                    break;
                
                case "PATCH":
                    echo "update $id";
                    break;
                    
                case "DELETE":
                    echo "delete $id";
                    break;

                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");    
            }
        }
    }

    // This won't return anything, so we are adding the void return type declaration
    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }
}










