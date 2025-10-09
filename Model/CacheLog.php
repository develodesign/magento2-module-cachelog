<?php
/**
 * @package     Develodesign_CacheLog
 * @copyright   Copyright (c) 2025 Develodesign
 */
namespace Develodesign\CacheLog\Model;

use Develodesign\CacheLog\Api\Data\CacheLogInterface;
use Magento\Framework\Model\AbstractModel;

class CacheLog extends AbstractModel implements CacheLogInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'develodesign_cache_log';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = 'develodesign_cache_log';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Develodesign\CacheLog\Model\ResourceModel\CacheLog::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Get cleaned type
     *
     * @return string|null
     */
    public function getCleanedType()
    {
        return $this->getData(self::CLEANED_TYPE);
    }

    /**
     * Get cache tags
     *
     * @return string|null
     */
    public function getCacheTags()
    {
        return $this->getData(self::CACHE_TAGS);
    }

    /**
     * Get total records
     *
     * @return int|null
     */
    public function getTotalRecords()
    {
        return $this->getData(self::TOTAL_RECORDS);
    }

    /**
     * Get area
     *
     * @return string|null
     */
    public function getArea()
    {
        return $this->getData(self::AREA);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Set cleaned type
     *
     * @param string $cleanedType
     * @return $this
     */
    public function setCleanedType($cleanedType)
    {
        return $this->setData(self::CLEANED_TYPE, $cleanedType);
    }

    /**
     * Set cache tags
     *
     * @param string $cacheTags
     * @return $this
     */
    public function setCacheTags($cacheTags)
    {
        return $this->setData(self::CACHE_TAGS, $cacheTags);
    }

    /**
     * Set total records
     *
     * @param int $totalRecords
     * @return $this
     */
    public function setTotalRecords($totalRecords)
    {
        return $this->setData(self::TOTAL_RECORDS, $totalRecords);
    }

    /**
     * Set area
     *
     * @param string $area
     * @return $this
     */
    public function setArea($area)
    {
        return $this->setData(self::AREA, $area);
    }

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
