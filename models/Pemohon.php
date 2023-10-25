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
        $query = "SELECT alternatif.id AS id, alternatif.nama AS nama, alternatif.alamat AS alamat FROM alternatif";
        $statement = $this->pdo->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function getAlternatifAndBobot()
    {
        $query = "SELECT alternatif.id AS id, alternatif.nama AS nama, alternatif_bobot.kriteria_id AS kriteria_id, alternatif_bobot.bobot AS nilai " . 
                 "FROM alternatif LEFT JOIN alternatif_bobot ON alternatif.id = alternatif_bobot.alternatif_id ";
        $statement = $this->pdo->prepare($query);
        $statement->execute();

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
            $nama = $data['nama'] ?? "";
            $alamat = $data['alamat'] ?? "";
            $kriteria = $data['kriteria'] ?? "";
    
            if ($nama !== "" && $alamat !== "" && $kriteria !== "") {
    
                $query = "INSERT INTO alternatif VALUES(null, ?, ?)";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $nama,
                    $alamat
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

            $status_sub = $kriteriaItem['status_sub'] == "1";
            $subKriteria = $status_sub ? $kriteriaItem['bobot'] : null;
            $nilai = $kriteriaItem['bobot'];

            if ($status_sub) {
                $subKriteriaSelected = $this->findSubKriteria($subKriteria);
                $nilai = $subKriteriaSelected['bobot'];
            }

            $query = "INSERT INTO alternatif_bobot VALUES(null, ?, ?, ?, ?)";
        
            $statement = $this->pdo->prepare($query);
            
            $statement->execute([
                $id,
                $kriteriaItemId,
                $subKriteria,
                $nilai
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
    
                $query = "SELECT * FROM alternatif_bobot WHERE alternatif_id IN ($anonymous) ";
                
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
            $id = $data['id'] ?? "";
            $nama = $data['nama'] ?? "";
            $alamat = $data['alamat'] ?? "";
            $kriteria = $data['kriteria'] ?? "";

            if ($id !== "" && $nama !== "" && $alamat !== "" && $kriteria !== "") {
    
                $query = "UPDATE alternatif SET nama = ?, alamat = ? WHERE id = ?";
                
                $statement = $this->pdo->prepare($query);
                
                $execute = $statement->execute([
                    $nama,
                    $alamat,
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

    public function findSubKriteria($id)
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
}