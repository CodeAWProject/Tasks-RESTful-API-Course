<?php

class TaskGateway
{

    private PDO $conn;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    //Getting all the task records
    public function getAll(): array
    {
        $sql = "SELECT *
                FROM task
                ORDER BY name";
        $stmt = $this->conn->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}