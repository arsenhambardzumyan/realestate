<?php

namespace app\Controllers;

use app\Services\TicketService;
use app\Utils\ResponseHelper;

class TicketController
{
    private $service;

    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }

    public function buyTickets()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->service->buyTickets($data);
            ResponseHelper::success($result, 'Tickets purchased successfully');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function getMyTickets()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $tickets = $this->service->getMyTickets($data['id_user']);
            ResponseHelper::success(['tickets' => $tickets], 'User tickets retrieved successfully');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function getMyTicketsInfo()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $info = $this->service->getMyTicketsInfo($data['id_user'], $data['id_pool']);
            ResponseHelper::success($info, 'User ticket info retrieved successfully');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function buyTicketFromBalance()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->service->buyTicketFromBalance($data['id_user'], $data['id_pool']);
            ResponseHelper::success([], 'Ticket purchased using balance');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }
}
