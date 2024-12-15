<?php

namespace app\Services;

use app\Repositories\PoolRepository;

class PoolService
{
    private $repository;

    public function __construct(PoolRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createPool($data)
    {
        if (empty($data['sum_goal'])) {
            throw new \Exception('Sum goal is required');
        }
        return $this->repository->create($data);
    }

    public function getPoolInfo($id)
    {
        if (empty($id)) {
            throw new \Exception('Pool ID is required');
        }
        return $this->repository->getById($id);
    }

    public function getCurrentPoolInfo()
    {
        return $this->repository->getCurrentPool();
    }
}
