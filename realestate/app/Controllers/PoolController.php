<?php

namespace app\Controllers;

use app\Services\PoolService;
use app\Utils\ResponseHelper;

class PoolController
{
    private $service;

    public function __construct(PoolService $service)
    {
        $this->service = $service;
    }

    public function createPool()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $this->service->createPool($data);
            ResponseHelper::success(['id_pool' => $id], 'Pool created successfully');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function getPoolInfo()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $pool = $this->service->getPoolInfo($data['id_pool']);
            ResponseHelper::success($pool, 'Pool information retrieved successfully');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function getCurrentPoolInfo()
    {
        try {
            $pool = $this->service->getCurrentPoolInfo();
            ResponseHelper::success($pool, 'Current pool information retrieved successfully');
        } catch (\Exception $e) {
            ResponseHelper::error($e->getMessage(), 500);
        }
    }
}
