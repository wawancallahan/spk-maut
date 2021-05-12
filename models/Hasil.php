<?php

namespace Models;

class Hasil {

    protected $pdo;

    public function __construct ($pdo)
    {
        $this->pdo = $pdo;
    }

    public function index ($kelurahan_id = null)
    {
        $query = "SELECT hasil.alternatif_id AS alternatif_id, alternatif.nama AS nama, hasil.nilai AS nilai, hasil.no AS 'no' FROM hasil LEFT JOIN alternatif ON alternatif.id = hasil.alternatif_id ";
        if ($kelurahan_id !== null) {
            $query .= " WHERE hasil.kelurahan_id = ?";
        }

        $query .= " ORDER BY hasil.no ASC";
        
        $statement = $this->pdo->prepare($query);
        if ($kelurahan_id !== null) {
            $statement->execute([
                $kelurahan_id
            ]);
        } else {
            $statement->execute();
        }

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function create ($data)
    {
        try {
            $alternatif_id = $data['alternatif_id'] ?? null;
            $kelurahan_id = $data['kelurahan_id'] ?? null;
            $no = $data['no'] ?? null;
            $nilai = $data['nilai'] ?? null;
    
            if ($alternatif_id !== "" && $no !== "" && $nilai !== "") {
    
                $query = "INSERT INTO hasil VALUES(null, ?, ?, ?, ?)";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $alternatif_id,
                    $no,
                    $nilai,
                    $kelurahan_id
                ]);

                return $execute ? 'success' : 'fail';
            } else {
                return 'validation';
               
            }
        } catch (Exception $e) {
            return 'fail';
        }    
    }

    public function delete($id)
    {
        try {
            if ($id !== "") {
    
                $query = "DELETE FROM hasil WHERE kelurahan_id = ?";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $id,
                ]);

                return $execute;
            } else {
                return false;
               
            }
        } catch (\Exception $e) {
            return false;
        } 
    }
}