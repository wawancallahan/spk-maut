<?php

namespace Models;

class Pemohon {

    protected $pdo;

    public function __construct ($pdo)
    {
        $this->pdo = $pdo;
    }

    public function index ()
    {
        $query = "SELECT alternatif.id AS id, alternatif.nama AS nama, alternatif.alamat AS alamat, alternatif.pekerjaan AS pekerjaan, kelurahan.nama AS nama_kelurahan " . 
                "FROM alternatif LEFT JOIN kelurahan ON alternatif.kelurahan_id = kelurahan.id";
        $statement = $this->pdo->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function create ($data)
    {
        try {
            $kelurahan_id = $data['kelurahan_id'] ?? null;
            $nama = $data['nama'] ?? null;
            $alamat = $data['alamat'] ?? null;
            $pekerjaan = $data['pekerjaan'] ?? null;
    
            if ($kelurahan_id !== "" && $nama !== "" && $alamat !== "" && $pekerjaan !== "") {
    
                $query = "INSERT INTO alternatif VALUES(null, ?, ?, ?, ?)";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $kelurahan_id,
                    $nama,
                    $alamat,
                    $pekerjaan
                ]);

                return $execute ? 'success' : 'fail';
            } else {
                return 'validation';
               
            }
        } catch (Exception $e) {
            return 'fail';
        }    
    }

    public function find($id)
    {
        try {
            if ($id !== "") {
    
                $query = "SELECT * FROM alternatif WHERE id = ?";
                
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
        } catch (Exception $e) {
            return null;
        } 
    }

    public function update ($data)
    {
        try {
            $id = $data['id'] ?? null;
            $kelurahan_id = $data['kelurahan_id'] ?? null;
            $nama = $data['nama'] ?? null;
            $alamat = $data['alamat'] ?? null;
            $pekerjaan = $data['pekerjaan'] ?? null;

            if ($id !== "" && $kelurahan_id !== "" && $nama !== "" && $alamat !== "" && $pekerjaan !== "") {
    
                $query = "UPDATE alternatif SET kelurahan_id = ?, nama = ?, alamat = ?, pekerjaan = ? WHERE id = ?";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $kelurahan_id,
                    $nama,
                    $alamat,
                    $pekerjaan,
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
    
                $query = "DELETE FROM alternatif WHERE id = ?";
                
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