<?php
/**
 * @package     Develodesign_CacheLog
 * @copyright   Copyright (c) 2025 Develodesign
 */
namespace Develodesign\CacheLog\Api\Data;

interface CacheLogInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'entity_id';
    const TYPE = 'type';
    const CLEANED_TYPE = 'cleaned_type';
    const CACHE_TAGS = 'cache_tags';
    const TOTAL_RECORDS = 'total_records';
    const AREA = 'area';
    const CREATED_AT = 'created_at';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Get cleaned type
     *
     * @return string|null
     */
    public function getCleanedType();

    /**
     * Get cache tags
     *
     * @return string|null
     */
    public function getCacheTags();

    /**
     * Get total records
     *
     * @return int|null
     */
    public function getTotalRecords();

    /**
     * Get area
     *
     * @return string|null
     */
    public function getArea();

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Set cleaned type
     *
     * @param string $cleanedType
     * @return $this
     */
    public function setCleanedType($cleanedType);

    /**
     * Set cache tags
     *
     * @param string $cacheTags
     * @return $this
     */
    public function setCacheTags($cacheTags);

    /**
     * Set total records
     *
     * @param int $totalRecords
     * @return $this
     */
    public function setTotalRecords($totalRecords);

    /**
     * Set area
     *
     * @param string $area
     * @return $this
     */
    public function setArea($area);

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
