<?php
/**
 * @package     Develodesign_CacheLog
 * @copyright   Copyright (c) 2025 Develodesign
 */
namespace Develodesign\CacheLog\Model\Plugin;

use Develodesign\CacheLog\Model\CacheLogFactory;
use Develodesign\CacheLog\Model\CacheLogRepository;
use Magento\Framework\App\Cache\TypeList;
use Magento\Framework\App\State;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Develodesign\CacheLog\Helper\Data as Helper;

class CacheTypeList
{
    /**
     * Config path
     */
    const XML_PATH_ENABLED = 'develodesign_cachelog/general/enabled';

    /**
     * @var CacheLogFactory
     */
    private $cacheLogFactory;

    /**
     * @var CacheLogRepository
     */
    private $cacheLogRepository;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Helper
     */
    private $coreHelper;

    /**
     * @param CacheLogFactory $cacheLogFactory
     * @param CacheLogRepository $cacheLogRepository
     * @param State $state
     * @param Helper $coreHelper
     */
    public function __construct(
        CacheLogFactory $cacheLogFactory,
        CacheLogRepository $cacheLogRepository,
        State $state,
        Helper $coreHelper
    ) {
        $this->cacheLogFactory = $cacheLogFactory;
        $this->cacheLogRepository = $cacheLogRepository;
        $this->state = $state;
        $this->coreHelper = $coreHelper;
    }

    /**
     * Before clean type
     *
     * @param TypeList $subject
     * @param string $typeCode
     * @return array
     */
    public function beforeCleanType(TypeList $subject, $typeCode)
    {
        // Check if logging is enabled
        if (!$this->coreHelper->isEnabled()) {
            return [$typeCode];
        }

        try {
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

            // Create cache log entry
            $cacheLog = $this->cacheLogFactory->create();
            $cacheLog->setType('cache_type_list');
            $cacheLog->setCleanedType($typeCode);
            $cacheLog->setArea($area);

            // Save log
            $this->cacheLogRepository->save($cacheLog);
        } catch (CouldNotSaveException $e) {
            // Log error if needed
        }

        return [$typeCode];
    }
}
