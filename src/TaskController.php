<?php

class TaskController
{

    public function __construct(private TaskGateway $gateway,
                                private int $user_id)
    {

    }
    public function processRequest(string $method, ?string $id): void
    {
        if ($id === null) {
            
            if ($method == "GET") {
                
                echo json_encode($this->gateway->getAllForUser($this->user_id));

            } elseif ($method == "POST") {


                //If the body is empty or contains invalid JSON, we'll just have an empty array
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if( !empty($errors)) {
                    // It can't insert a new record because the data isn't valid

                    $this->respondUnprocessableEntity($errors);
                    return;

                }

                //Assigning the return value of this method $data to the $id variable
                $id = $this->gateway->createForUser($this->user_id, $data);

                $this->respondCreated($id);

            } else {
                
                $this->respondMethodNotAllowed("GET, POST");
            }
        } else {

            $task = $this->gateway->getForUser($this->user_id, $id);


            //Checking that the value is strictly Boolean false
            if($task === false) {

                $this->respondNotFound($id);
                return;
            }
            
            switch ($method) {
                
                case "GET":
                    echo json_encode($task);
                    break;
                
                case "PATCH":

                    //Geting data from the request
                    $data = (array) json_decode(file_get_contents("php://input"), true);

                    //Validating the data
                    $errors = $this->getValidationErrors($data, false);

                    //Return 422 if it's invalid
                    if( !empty($errors)) {
                        // It can't insert a new record because the data isn't valid

                        $this->respondUnprocessableEntity($errors);
                        return;

                    }

                    $rows = $this->gateway->updateForUser($this->user_id, $id, $data);
                    echo json_encode(["message" => "Task updated", "rows" => $rows]);
                    break;
                    
                case "DELETE":
                    $rows = $this->gateway->deleteForUser($this->user_id, $id);
                    echo json_encode(["message" => "Task deleted", "rows" => $rows]);
                    break;

                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");    
            }
        }
    }

    private function respondUnprocessableEntity(array $errors):void
    {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }

    // This won't return anything, so we are adding the void return type declaration
    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    private function respondNotFound(string $id): void
    {
        http_response_code(404);
        echo json_encode(["message" => "Task with ID $id not found"]);
    }

    private function respondCreated(string $id):void
    {
        http_response_code(201);
        echo json_encode(["message" => "Task created", "id" => $id]);
    }

    // This boolean means we don't have to change the existing code where we're calling this method when we create a new record
    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if ($is_new && empty($data["name"])) {

            $errors[] = "name is required";
        }

        if( ! empty($data["priority"])) {

            //Checking it contains an integer value
            if(filter_var($data["priority"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "priority must be an integer";
            }
        }

        return $errors;
    }
}










