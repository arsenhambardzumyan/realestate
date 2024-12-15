<?php

namespace app\Repositories;

use PDO;

class TicketRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function buy($data)
    {
        // Fetch the ticket price and pool info
        $stmt = $this->db->prepare("
            SELECT sum
            FROM estatepool_tickets
            WHERE id = :id_tickets
        ");
        $stmt->execute(['id_tickets' => $data['id_tickets']]);
        $ticketInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticketInfo) {
            throw new \Exception("Ticket type not found");
        }

        // Check user balance
        $stmt = $this->db->prepare("
            SELECT sum
            FROM users_balances
            WHERE id_user = :id_user
            AND id_balance = 3
        ");
        $stmt->execute(['id_user' => $data['id_user']]);
        $userBalance = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userBalance || $userBalance['sum'] < $ticketInfo['sum']) {
            throw new \Exception("Insufficient balance to purchase tickets");
        }

        // Deduct the ticket price from user balance
        $stmt = $this->db->prepare("
            UPDATE users_balances
            SET sum = sum - :sum
            WHERE id_user = :id_user
            AND id_balance = 3
        ");
        $stmt->execute([
            'sum' => $ticketInfo['sum'],
            'id_user' => $data['id_user']
        ]);

        // Generate the ticket(s)
        $this->generateTickets($data['id_user'], $data['id_pool'], $data['ticket_count']);
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT ticket, id_pool, win
            FROM estatepool_usertickets
            WHERE id_user = :id_user
        ");
        $stmt->execute(['id_user' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserTicketsInfo($userId, $poolId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count_tickets, SUM(win) as winning_tickets
            FROM estatepool_usertickets
            WHERE id_user = :id_user
            AND id_pool = :id_pool
        ");
        $stmt->execute(['id_user' => $userId, 'id_pool' => $poolId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buyFromBalance($userId, $poolId)
    {
        // Fetch user's balance
        $stmt = $this->db->prepare("
            SELECT sum
            FROM users_balances
            WHERE id_user = :id_user
            AND id_balance = 3
        ");
        $stmt->execute(['id_user' => $userId]);
        $balance = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$balance || $balance['sum'] < 50) {
            throw new \Exception("Insufficient balance to buy tickets");
        }

        // Determine the ticket type based on balance
        $stmt = $this->db->prepare("
            SELECT id, sum
            FROM estatepool_tickets
            WHERE sum <= :balance
            ORDER BY sum DESC
            LIMIT 1
        ");
        $stmt->execute(['balance' => $balance['sum']]);
        $ticketType = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticketType) {
            throw new \Exception("No ticket type available for the current balance");
        }

        // Deduct the ticket price from user balance
        $stmt = $this->db->prepare("
            UPDATE users_balances
            SET sum = sum - :sum
            WHERE id_user = :id_user
            AND id_balance = 3
        ");
        $stmt->execute([
            'sum' => $ticketType['sum'],
            'id_user' => $userId
        ]);

        // Generate the ticket(s)
        $this->generateTickets($userId, $poolId, 1); // Assumes 1 ticket per purchase
    }

    private function generateTickets($userId, $poolId, $count)
    {
        $stmt = $this->db->prepare("
            INSERT INTO estatepool_usertickets (id_user, id_pool, ticket)
            VALUES (:id_user, :id_pool, :ticket)
        ");

        for ($i = 0; $i < $count; $i++) {
            $ticketNumber = str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
            $stmt->execute([
                'id_user' => $userId,
                'id_pool' => $poolId,
                'ticket' => $ticketNumber
            ]);
        }
    }
}
