<?php
/**
 * @package     Develodesign_CacheLog
 * @copyright   Copyright (c) 2025 Develodesign
 */
namespace Develodesign\CacheLog\Model;

use Develodesign\CacheLog\Api\Data\CacheLogInterface;
use Develodesign\CacheLog\Model\ResourceModel\CacheLog as CacheLogResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CacheLogRepository
{
    /**
     * @var CacheLogResource
     */
    private $cacheLogResource;

    /**
     * @var CacheLogFactory
     */
    private $cacheLogFactory;

    /**
     * @param CacheLogResource $cacheLogResource
     * @param CacheLogFactory $cacheLogFactory
     */
    public function __construct(
        CacheLogResource $cacheLogResource,
        CacheLogFactory $cacheLogFactory
    ) {
        $this->cacheLogResource = $cacheLogResource;
        $this->cacheLogFactory = $cacheLogFactory;
    }

    /**
     * Save cache log
     *
     * @param CacheLogInterface $cacheLog
     * @return CacheLogInterface
     * @throws CouldNotSaveException
     */
    public function save(CacheLogInterface $cacheLog)
    {
        try {
            $this->cacheLogResource->save($cacheLog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $cacheLog;
    }

    /**
     * Get by id
     *
     * @param int $id
     * @return CacheLogInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $cacheLog = $this->cacheLogFactory->create();
        $this->cacheLogResource->load($cacheLog, $id);
        if (!$cacheLog->getId()) {
            throw new NoSuchEntityException(__('Cache log with id "%1" does not exist.', $id));
        }
        return $cacheLog;
    }
}
