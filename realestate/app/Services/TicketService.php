<?php

namespace app\Services;

use app\Repositories\TicketRepository;

class TicketService
{
    private $repository;

    public function __construct(TicketRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buyTickets($data)
    {
        if (empty($data['id_user']) || empty($data['id_pool']) || empty($data['ticket_count'])) {
            throw new \Exception('Required fields are missing');
        }
        return $this->repository->buy($data);
    }

    public function getMyTickets($userId)
    {
        return $this->repository->getByUserId($userId);
    }

    public function getMyTicketsInfo($userId, $poolId)
    {
        return $this->repository->getUserTicketsInfo($userId, $poolId);
    }

    public function buyTicketFromBalance($userId, $poolId)
    {
        $this->repository->buyFromBalance($userId, $poolId);
    }
}
