<?php
/**
 * @package     Develodesign_CacheLog
 * @copyright   Copyright (c) 2025 Develodesign
 */
namespace Develodesign\CacheLog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CacheLog extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('develodesign_cache_log', 'entity_id');
    }
}
