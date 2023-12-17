<?php 

class ErrorHandler
{
    //Throwable class is the base for all errors and exceptions thrown in PHP (details about error)
    public static function handleException(Throwable $exception): void
    {
        http_response_code(500);
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }    
}