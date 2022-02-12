<?php

namespace Models;

class SubKriteria {

    protected $pdo;

    public function __construct ($pdo)
    {
        $this->pdo = $pdo;
    }

    public function index ()
    {
        $query = "SELECT sub_kriteria.id AS id, sub_kriteria.nama AS nama, sub_kriteria.bobot AS bobot, kriteria.nama AS nama_kriteria " . 
                 "FROM sub_kriteria LEFT JOIN kriteria ON sub_kriteria.kriteria_id = kriteria.id";
        $statement = $this->pdo->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function create ($data)
    {
        try {
            $nama = $data['nama'] ?? "";
            $kriteria_id = $data['kriteria_id'] ?? "";
            $bobot = $data['bobot'] ?? "";

            if ($nama !== "" && $bobot !== "" && $kriteria_id !== "") {
    
                $query = "INSERT INTO sub_kriteria VALUES(null, ?, ?, ?)";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $kriteria_id,
                    $nama,
                    $bobot,
                ]);

                return $execute ? 'success' : 'fail';
            } else {
                return 'validation';
               
            }
        } catch (\Exception $e) {
            return 'fail';
        }    
    }

    public function find($id)
    {
        try {
            if ($id !== "") {
    
                $query = "SELECT * FROM sub_kriteria WHERE id = ?";
                
                $statement = $this->pdo->prepare($query);
                
                $statement->execute([
                    $id,
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

    public function update ($data)
    {
        try {
            $id = $data['id'] ?? "";
            $nama = $data['nama'] ?? "";
            $kriteria_id = $data['kriteria_id'] ?? "";
            $bobot = $data['bobot'] ?? "";

            if ($id !== "" && $nama !== "" && $bobot !== "" && $kriteria_id !== "") {
    
                $query = "UPDATE sub_kriteria SET kriteria_id = ?, nama = ?, bobot = ? WHERE id = ?";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $kriteria_id,
                    $nama,
                    $bobot,
                    $id
                ]);

                return $execute ? 'success' : 'fail';
            } else {
                return 'validation';
               
            }
        } catch (\Exception $e) {
            return 'fail';
        }    
    }

    public function delete($id)
    {
        try {
            if ($id !== "") {
    
                $query = "DELETE FROM sub_kriteria WHERE id = ?";
                
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