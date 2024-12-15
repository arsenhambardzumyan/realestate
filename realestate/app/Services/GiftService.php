<?php

namespace app\Services;

use app\Repositories\GiftRepository;

class GiftService
{
    private $repository;

    public function __construct(GiftRepository $repository)
    {
        $this->repository = $repository;
    }

    public function addGift($data)
    {
        if (empty($data['id_pool']) || empty($data['name']) || !isset($data['general'])) {
            throw new \Exception('Required fields are missing');
        }
        $this->repository->add($data);
    }

    public function checkGifts($poolId)
    {
        if (empty($poolId)) {
            throw new \Exception('Pool ID is required');
        }
        $this->repository->check($poolId);
    }
}
