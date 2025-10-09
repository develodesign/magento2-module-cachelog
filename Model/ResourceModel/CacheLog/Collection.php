<?php
/**
 * @package     Develodesign_CacheLog
 * @copyright   Copyright (c) 2025 Develodesign
 */
namespace Develodesign\CacheLog\Model\ResourceModel\CacheLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Develodesign\CacheLog\Model\CacheLog::class,
            \Develodesign\CacheLog\Model\ResourceModel\CacheLog::class
        );
    }
}
