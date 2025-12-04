<?php
/**
 * @package     Develodesign_CacheLog
 * @copyright   Copyright (c) 2025 Develodesign
 */
namespace Develodesign\CacheLog\Observer;

use Develodesign\CacheLog\Model\CacheLogFactory;
use Develodesign\CacheLog\Model\CacheLogRepository;
use Magento\Framework\App\State;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Cache\Tag\Resolver;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use Develodesign\CacheLog\Helper\Data as Helper;

class CacheTagsClean implements ObserverInterface
{
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
     * @var Resolver
     */
    private $tagResolver;

    /**
     * @var Registry
     */
    private $registry;
    
    /**
     * @var Helper
     */
    private $coreHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * set cache tags to debug file paths and methods
     *
     * @var string[]
     */
    protected $debugTags = [
        '((^|,)cat_p(,|$))',
        '((^|,)cat_c(,|$))',
        '((^|,)hyva_nav(,|$))'
    ];

    /**
     * @param CacheLogFactory $cacheLogFactory
     * @param CacheLogRepository $cacheLogRepository
     * @param Resolver $tagResolver
     * @param LoggerInterface $logger
     * @param Registry $registry
     * @param Helper $coreHelper
     * @param State $state
     */
    public function __construct(
        CacheLogFactory $cacheLogFactory,
        CacheLogRepository $cacheLogRepository,
        Resolver $tagResolver,
        LoggerInterface $logger,
        Registry $registry,
        Helper $coreHelper,
        State $state
    ) {
        $this->cacheLogFactory = $cacheLogFactory;
        $this->cacheLogRepository = $cacheLogRepository;
        $this->tagResolver = $tagResolver;
        $this->state = $state;
        $this->logger = $logger;
        $this->registry = $registry;
        $this->coreHelper = $coreHelper;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->coreHelper->isEnabled()) {
            return;
        }
        try {
            $object = $observer->getEvent()->getObject();

            if (!is_object($object)) {
                return;
            }

            $bareTags = $this->tagResolver->getTags($object);

            $tags = [];
            $pattern = '((^|,)%s(,|$))';
            foreach ($bareTags as $tag) {
                $tags[] = sprintf($pattern, $tag);
            }

            $this->logger->debug('clean_cache_by_tags Event fired');

            // Skip if no tags
            if (empty($tags) || !count($tags)) {
                return;
            }

            $this->logger->debug(' - Cache TAGS - ');
            $this->logger->debug(print_r($tags, true));

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

            $filePaths = $this->debugFilePaths($cacheTags);

            $cacheTagsImploded = implode(', ', $cacheTags);
            if ($filePaths && count($filePaths)) {
                $cacheTagsImploded .= "\n" . 'FILES:' . "\n" . implode("\n --- \n", $filePaths);
            }
            // Create cache log entry
            $cacheLog = $this->cacheLogFactory->create();
            $cacheLog->setType('cache_tags');
            $cacheLog->setCacheTags($cacheTagsImploded);
            $cacheLog->setTotalRecords($totalRecords);
            $cacheLog->setArea($area);

            // Save log
            $this->cacheLogRepository->save($cacheLog);
        } catch (CouldNotSaveException $e) {
            // Log error if needed
        }
    }

    /**
     * @param $cacheTags
     * @return array|void
     */
    protected function debugFilePaths($cacheTags)
    {
        foreach ($this->debugTags  as $debugTag) {
            if (in_array($debugTag, $cacheTags)) {
                return $this->coreHelper->getDebugIncluded();
            }
        }
    }
}
