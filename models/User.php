<?php

namespace Models;

class User {

    protected $pdo;

    public function __construct ($pdo)
    {
        $this->pdo = $pdo;
    }

    public function find($username, $password)
    {
        try {
            if ($username !== "" && $password !== "") {

                $password = md5($password);
    
                $query = "SELECT * FROM user WHERE username = ? AND password = ?";
                
                $statement = $this->pdo->prepare($query);
                
                $statement->execute([
                    $username,
                    $password
                ]);

                if ($statement->rowCount() <= 0) {
                    return null;
                }

                return $statement->fetch(\PDO::FETCH_ASSOC);
            } else {
                
                return null;
               
            }
        } catch (\Exception $e) {
            return null;
        } 
    }
}