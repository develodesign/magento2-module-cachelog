<?php
/**
 * @category   Develodesign
 * @package    Develodesign_CacheLog
 * @author     Develodesign Team
 * @copyright  Copyright (c) Develodesign (https://www.develodesign.com)
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Develodesign\CacheLog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\State;
use Magento\Cron\Model\ResourceModel\Schedule\Collection as ScheduleCollection;
use Magento\Cron\Model\Schedule;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Area;

class Data extends AbstractHelper
{
    /**
     * XML Config path constants
     */
    const XML_PATH_CACHE_LOG_ENABLED = 'develodesign_cachelog/general/enabled';
    const XML_PATH_VARNISH_LOGGING_ENABLED = 'develodesign_cachelog/general/enable_varnish_log';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var State
     */
    protected $appState;

    /**
     * @var ScheduleCollection
     */
    protected $scheduleCollection;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param State $appState
     * @param ScheduleCollection $scheduleCollection
     */
    public function __construct(
        Context $context,
        ?State $appState = null,
        ?ScheduleCollection $scheduleCollection = null
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        // Use ObjectManager for backward compatibility if parameters are not provided
        $this->appState = $appState ?: ObjectManager::getInstance()->get(State::class);
        $this->scheduleCollection = $scheduleCollection ?: ObjectManager::getInstance()->get(ScheduleCollection::class);
        parent::__construct($context);
    }

    /**
     * Check if the Cache Log module is enabled
     *
     * @param string $scopeType
     * @param mixed $scopeCode
     * @return bool
     */
    public function isEnabled($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CACHE_LOG_ENABLED,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Check if Varnish logging is enabled
     *
     * @param string $scopeType
     * @param mixed $scopeCode
     * @return bool
     */
    public function isVarnishLoggingEnabled($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): bool
    {
        // First check if the main module is enabled
        if (!$this->isEnabled($scopeType, $scopeCode)) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_VARNISH_LOGGING_ENABLED,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Get the current area code with additional information for cron jobs
     *
     * @return string
     */
    public function getDetailedAreaCode(): string
    {
        try {
            $areaCode = $this->appState->getAreaCode();

            // If we're in crontab area, try to get the job code
            if ($areaCode === Area::AREA_CRONTAB) {
                $jobCode = $this->getCurrentCronJobCode();
                if ($jobCode) {
                    return $areaCode . ' (' . $jobCode . ')';
                }
            }

            return $areaCode;
        } catch (\Exception $e) {
            // If area is not set, return a default value
            return 'unknown';
        }
    }

    /**
     * Get the current running cron job code
     *
     * @return string|null
     */
    public function getCurrentCronJobCode(): ?string
    {
        try {
            // Look for the most recently started running cron job
            /** @var \Magento\Cron\Model\ResourceModel\Schedule\Collection $collection */
            $collection = $this->scheduleCollection;
            $collection->addFieldToFilter('status', Schedule::STATUS_RUNNING);
            $collection->setOrder('executed_at', 'DESC');

            if ($collection->getSize() == 1) {
                $schedule = $collection->getFirstItem();
                return $schedule->getJobCode();
            }

            if ($collection->getSize() > 1) {
                $jobCodes = [];
                foreach ($collection as $schedule) {
                    $jobCodes[] = $schedule->getJobCode();
                }
                // Return a comma-separated list of running jobs
                return 'multiple: ' . implode(', ', $jobCodes);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getDebugIncluded() {
        $trace = debug_backtrace();
        $result = [];

        foreach ($trace as $frame) {
            // Only include calls from classes
            if (isset($frame['class']) && isset($frame['file'])) {
                $file = $frame['file'];
                $call = $file . ' => ' .  $frame['class'] . $frame['type'] . $frame['function'] . '()';
                $result[] = $call;
            }
        }

        return $result;
    }
}
