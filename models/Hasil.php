<?php

namespace Models;

class Hasil {

    protected $pdo;

    public function __construct ($pdo)
    {
        $this->pdo = $pdo;
    }

    public function index ()
    {
        $query = "SELECT hasil.alternatif_id AS alternatif_id, alternatif.nama AS nama, hasil.nilai AS nilai, hasil.no AS 'no' FROM hasil LEFT JOIN alternatif ON alternatif.id = hasil.alternatif_id ";

        $query .= " ORDER BY hasil.no ASC";
        
        $statement = $this->pdo->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function create ($data)
    {
        try {
            $alternatif_id = $data['alternatif_id'] ?? null;
            $no = $data['no'] ?? null;
            $nilai = $data['nilai'] ?? null;
    
            if ($alternatif_id !== "" && $no !== "" && $nilai !== "") {
    
                $query = "INSERT INTO hasil VALUES(null, ?, ?, ?)";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $alternatif_id,
                    $no,
                    $nilai
                ]);

                return $execute ? 'success' : 'fail';
            } else {
                return 'validation';
               
            }
        } catch (Exception $e) {
            return 'fail';
        }    
    }

    public function delete()
    {
        try {
            $query = "DELETE FROM hasil";
            
            $statement = $this->pdo->prepare($query);
            
            $execute = $statement->execute();

            return $execute;
        } catch (\Exception $e) {
            return false;
        } 
    }
}