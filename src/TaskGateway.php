<?php

class TaskGateway
{

    private PDO $conn;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    //Getting all the task records
    public function getAllForUser(int $user_id): array
    {
        $sql = "SELECT *
                FROM task
                WHERE user_id = :user_id
                ORDER BY name";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();
        
        $data = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $row['is_completed'] = (bool) $row['is_completed'];

            $data[] = $row;
        } 

        return $data;
        
    }


    //This returns false if there's no record with that ID
    public function getForUser(int $user_id, string $id): array | false
    {
        $sql = "SELECt *
                FROM task
                WHERE id = :id
                AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);        

        //Specifing that id shuald be inserted into SQL string as an integer
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data !== false) {
            $data['is_completed'] = (bool) $data['is_completed'];
        }

        return $data;
    }

    public function createForUser(int $user_id, array $data): string
    {
        $sql = "INSERT INTO task(name, priority, is_completed, user_id)
                VALUE (:name, :priority, :is_completed, :user_id)";

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

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);                

        $stmt->execute();
        
        //Calling last insterted id into database
        return $this->conn->lastInsertId();
    }

    public function updateForUser(int $user_id, string $id, array $data): int
    {
        $fields = [];

        if ( ! empty($data["name"])) {

            $fields["name"] = [
                $data["name"],
                PDO::PARAM_STR
            ];
        }

        if (array_key_exists("priority", $data)) {

            $fields["priority"] = [
                $data["priority"],
                $data["priority"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }

        if (array_key_exists("is_completed", $data)) {

            $fields["is_completed"] = [
                $data["is_completed"],
                PDO::PARAM_BOOL
            ];
        }

        if (empty($fields)) {

            return 0;

        } else {
            // Setting value in the array
            $sets = array_map(function($value) {
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE task"
                . " SET " . implode(", ", $sets)
                . " WHERE id = :id"
                . " AND user_id = :user_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

            // Looping around the array to getting the keys and the values
            foreach ($fields as $name => $values) {
                $stmt->bindValue(":$name", $values[0], $values[1]);
            }

            $stmt->execute();

            return $stmt->rowCount();
        }

        
        

        
    }

    public function deleteForUser(int $user_id, string $id): int
    {
        $sql = "DELETE FROM task
                WHERE id = :id
                AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}