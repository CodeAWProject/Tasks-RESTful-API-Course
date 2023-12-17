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


    //This returns false if there's no record with that ID
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

    public function create(array $data): string
    {
        $sql = "INSERT INTO task(name, priority, is_completed)
                VALUE (:name, :priority, :is_completed)";

        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);

        // IF this element of the array is empty, we'll bind null to the priority placeholder
        if (empty($data["priority"])) {
            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);
            //we'll bind the value from the array as an integer
        } else {
            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }

        //Making this value optional
        $stmt->bindValue(":is_completed", $data["is_completed"] ?? false,
                        PDO::PARAM_BOOL);

        $stmt->execute();
        
        //Calling last insterted id into database
        return $this->conn->lastInsertId();
    }
}