<?php
/**
 * CachePurgeLogger Plugin
 *
 * @category  Develodesign
 * @package   Develodesign_CacheLog
 */

namespace Develodesign\CacheLog\Model\Plugin;

use Develodesign\CacheLog\Model\CacheLogFactory;
use Develodesign\CacheLog\Model\CacheLogRepository;
use Magento\CacheInvalidate\Model\PurgeCache;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Develodesign\CacheLog\Helper\Data as Helper;

class CachePurgeLogger
{
    /**
     * @var CacheLogFactory
     */
    protected $cacheLogFactory;

    /**
     * @var CacheLogRepository
     */
    protected $cacheLogRepository;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var Helper
     */
    protected $coreHelper;

    /**
     * @param CacheLogFactory $cacheLogFactory
     * @param CacheLogRepository $cacheLogRepository
     * @param Helper $coreHelper
     * @param State $state
     */
    public function __construct(
        CacheLogFactory $cacheLogFactory,
        CacheLogRepository $cacheLogRepository,
        Helper $coreHelper,
        State $state
    ) {
        $this->cacheLogFactory = $cacheLogFactory;
        $this->cacheLogRepository = $cacheLogRepository;
        $this->state = $state;
        $this->coreHelper = $coreHelper;
    }

    /**
     * Before send purge request plugin
     *
     * @param PurgeCache $subject
     * @param array $tags
     * @return array
     */
    public function beforeSendPurgeRequest(PurgeCache $subject, $tags)
    {
        // Check if logging is enabled
        if (!$this->coreHelper->isVarnishLoggingEnabled()) {
            return [$tags];
        }
        // Get current area
        try {
            $area = $this->state->getAreaCode();
        } catch (LocalizedException $e) {
            if (PHP_SAPI === 'cli') {
                $area = 'SSH_CLI';
            } else {
                $area = 'unknown';
            }
        }

        if ($area == 'crontab') {
            $jobCode = $this->coreHelper->getCurrentCronJobCode();
            if ($jobCode) {
                $area .= '(' . $jobCode . ')';
            } else {
                $area .= 'NOt found';
            }
        }

        // Limit tags to first 100 if there are too many
        $totalRecords = count($tags);
        $cacheTags = array_slice($tags, 0, 100);

        // Create cache log entry
        $cacheLog = $this->cacheLogFactory->create();
        $cacheLog->setType('cache_purge_varnish');
        $cacheLog->setCacheTags(implode(', ', $cacheTags));
        $cacheLog->setTotalRecords($totalRecords);
        $cacheLog->setArea($area);

        // Save log
        $this->cacheLogRepository->save($cacheLog);

        return [$tags];
    }
}
