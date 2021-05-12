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

    public function getAlternatifAndBobot($id)
    {
        $query = "SELECT alternatif.id AS id, alternatif.nama AS nama, alternatif_bobot.kriteria_id AS kriteria_id, sub_kriteria.bobot AS nilai " . 
                 "FROM alternatif LEFT JOIN alternatif_bobot ON alternatif.id = alternatif_bobot.alternatif_id " .
                 "LEFT JOIN sub_kriteria ON alternatif_bobot.sub_kriteria_id = sub_kriteria.id WHERE alternatif.kelurahan_id = ?";
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            $id
        ]);

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $resultReduce = [];

        foreach ($result as $resultItem) {
            if ( ! isset($resultReduce[$resultItem['id']])) {
                $resultReduce[$resultItem['id']] = [
                    'id' => $resultItem['id'],
                    'nama' => $resultItem['nama'],
                    'bobot' => []
                ];
            }

            $resultReduce[$resultItem['id']]['bobot'][$resultItem['kriteria_id']] = [
                'kriteria_id' => $resultItem['kriteria_id'],
                'nilai' => $resultItem['nilai']
            ];
        }

        return $resultReduce;
    }

    public function create ($data)
    {
        try {
            $kelurahan_id = $data['kelurahan_id'] ?? null;
            $nama = $data['nama'] ?? null;
            $alamat = $data['alamat'] ?? null;
            $pekerjaan = $data['pekerjaan'] ?? null;
            $kriteria = $data['kriteria'] ?? null;
    
            if ($kelurahan_id !== "" && $nama !== "" && $alamat !== "" && $pekerjaan !== "" && $kriteria !== null) {
    
                $query = "INSERT INTO alternatif VALUES(null, ?, ?, ?, ?)";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $kelurahan_id,
                    $nama,
                    $alamat,
                    $pekerjaan
                ]);

                $id = $this->pdo->lastInsertId();

                $this->createBobot($kriteria, $id);

                return $execute ? 'success' : 'fail';
            } else {
                return 'validation';
               
            }
        } catch (Exception $e) {
            return 'fail';
        }    
    }

    public function createBobot($kriteria, $id)
    {
        foreach ($kriteria as $kriteriaItemId => $kriteriaItem) {
            $query = "INSERT INTO alternatif_bobot VALUES(null, ?, ?, ?)";
        
            $statement = $this->pdo->prepare($query);
            
            $statement->execute([
                $id,
                $kriteriaItemId,
                $kriteriaItem
            ]);
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

    public function getBobot($id)
    {
        try {
            if ($id !== "") {
    
                $query = "SELECT * FROM alternatif_bobot WHERE alternatif_id = ?";
                
                $statement = $this->pdo->prepare($query);
                
                $statement->execute([
                    $id,
                ]);

                if ($statement->rowCount() <= 0) {
                    return [];
                }

                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        } catch (Exception $e) {
            return [];
        }
    }

    public function getBobotIn($id)
    {
        try {
            if (count($id) > 0) {

                $anonymous = implode(',', array_map(function () { return  "?"; }, range(0, count($id) - 1)));
    
                $query = "SELECT * FROM alternatif_bobot LEFT JOIN sub_kriteria ON alternatif_bobot.sub_kriteria_id = sub_kriteria.id WHERE alternatif_id IN ($anonymous) ";
                
                $statement = $this->pdo->prepare($query);
                
                $statement->execute($id);

                if ($statement->rowCount() <= 0) {
                    return [];
                }

                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        } catch (Exception $e) {
            return [];
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
            $kriteria = $data['kriteria'] ?? null;

            if ($id !== "" && $kelurahan_id !== "" && $nama !== "" && $alamat !== "" && $pekerjaan !== "" && $kriteria !== null) {
    
                $query = "UPDATE alternatif SET kelurahan_id = ?, nama = ?, alamat = ?, pekerjaan = ? WHERE id = ?";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $kelurahan_id,
                    $nama,
                    $alamat,
                    $pekerjaan,
                    $id
                ]);

                $this->deleteBobot($id);
                $this->createBobot($kriteria, $id);

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

    public function deleteBobot($id)
    {
        try {
            if ($id !== "") {
    
                $query = "DELETE FROM alternatif_bobot WHERE alternatif_id = ?";
                
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