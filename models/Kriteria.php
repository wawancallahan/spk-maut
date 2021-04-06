<?php

namespace Models;

class Kriteria {

    protected $pdo;

    protected $filter = [];

    public function __construct ($pdo)
    {
        $this->pdo = $pdo;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function index ()
    {
        $query = "SELECT * FROM kriteria";

        if ( ! empty($this->filter)) {
            $query .= " WHERE ";

            $countFilter = count($this->filter);

            $noFilter = 1;
            foreach ($this->filter as $filterKey => $filter) {
                $query .= " $filterKey=$filter ";

                if ($countFilter > $noFilter) {
                    $noFilter++;

                    $query .= " AND ";
                }
            }
        }

        $statement = $this->pdo->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function create ($data)
    {
        try {
            $nama = $data['nama'] ?? null;
            $bobot = $data['bobot'] ?? null;
            $status = $data['status'] ?? null;
            $sub = $data['sub'] ?? null;

    
            if (! empty($nama) && ! empty($bobot) && ! empty($status) && ! empty($sub)) {
    
                $query = "INSERT INTO kriteria VALUES(null, ?, ?, ?, ?)";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $nama,
                    $bobot,
                    $status,
                    $sub
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