<?php

namespace app\Repositories;

use PDO;

class GiftRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function add($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO estatepool_gifts (id_pool, name, general)
            VALUES (:id_pool, :name, :general)
        ");
        $stmt->execute([
            'id_pool' => $data['id_pool'],
            'name' => $data['name'],
            'general' => $data['general']
        ]);
    }

    public function check($poolId)
    {
        // Fetch the current pool's total sum
        $stmt = $this->db->prepare("
            SELECT sum, sum_goal
            FROM estatepool
            WHERE id = :id
        ");
        $stmt->execute(['id' => $poolId]);
        $pool = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pool) {
            throw new \Exception("Pool not found");
        }

        // Fetch all gifts for the pool
        $stmt = $this->db->prepare("
            SELECT id, general, id_winner, sum_required, name
            FROM estatepool_gifts
            WHERE id_pool = :id_pool
            AND (id_winner IS NULL OR id_winner = 0)
        ");
        $stmt->execute(['id_pool' => $poolId]);
        $gifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($gifts as $gift) {
            // Check if the pool sum is enough to process the gift
            if ($pool['sum'] >= $gift['sum_required']) {
                $this->processGift($gift, $poolId);
            }
        }
    }

    private function processGift($gift, $poolId)
    {
        // Find a random ticket for the pool
        $stmt = $this->db->prepare("
            SELECT id, id_user, ticket
            FROM estatepool_usertickets
            WHERE id_pool = :id_pool
            ORDER BY RAND()
            LIMIT 1
        ");
        $stmt->execute(['id_pool' => $poolId]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            throw new \Exception("No tickets found for pool {$poolId}");
        }

        // Update the gift with the winner information
        $stmt = $this->db->prepare("
            UPDATE estatepool_gifts
            SET id_winner = :id_user, date_close = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            'id_user' => $ticket['id_user'],
            'id' => $gift['id']
        ]);

        // Mark the ticket as a winning ticket
        $stmt = $this->db->prepare("
            UPDATE estatepool_usertickets
            SET win = 1
            WHERE id = :id
        ");
        $stmt->execute(['id' => $ticket['id']]);
    }
}
