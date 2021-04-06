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
            $nama = $data['nama'] ?? null;
            $kriteria_id = $data['kriteria_id'] ?? null;
            $bobot = $data['bobot'] ?? null;

            if (! empty($nama) && ! empty($bobot) && ! empty($kriteria_id)) {
    
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
        } catch (Exception $e) {
            return 'fail';
        }    
    }
}