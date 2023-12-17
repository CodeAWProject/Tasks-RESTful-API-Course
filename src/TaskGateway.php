<?php

class TaskGateway
{

    private PDO $conn;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }
}