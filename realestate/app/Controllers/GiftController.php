<?php

namespace app\Controllers;

use app\Services\GiftService;
use app\Utils\ResponseHelper;

class GiftController
{
    private $service;

    public function __construct(GiftService $service)
    {
        $this->service = $service;
    }

    public function addGift()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->service->addGift($data);
            ResponseHelper::success([], 'Gift added successfully');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function checkGifts()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->service->checkGifts($data['id_pool']);
            ResponseHelper::success([], 'Gifts checked successfully');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }
}
