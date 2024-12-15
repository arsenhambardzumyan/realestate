<?php

namespace app\Repositories;

use PDO;

class PoolRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO estatepool (sum_goal, date_start, status) VALUES (:sum_goal, NOW(), 0)");
        $stmt->execute(['sum_goal' => $data['sum_goal']]);
        return $this->db->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM estatepool WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCurrentPool()
    {
        $stmt = $this->db->prepare("SELECT * FROM estatepool WHERE status = 0 ORDER BY date_start DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
