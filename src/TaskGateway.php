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
        
        $data = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $row['is_completed'] = (bool) $row['is_completed'];

            $data[] = $row;
        } 

        return $data;
        
    }

    public function get(string $id): array | false
    {
        $sql = "SELECt *
                FROM task
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);        

        //Specifing that id shuald be inserted into SQL string as an integer
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data !== false) {
            $data['is_completed'] = (bool) $data['is_completed'];
        }

        return $data;
    }
}