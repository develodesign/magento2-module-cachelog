<?php
/**
 * @package     Develodesign_CacheLog
 * @copyright   Copyright (c) 2025 Develodesign
 */
namespace Develodesign\CacheLog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Develodesign\CacheLog\Model\ResourceModel\CacheLog\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $deletedCount = 0;

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();

            if ($collectionSize) {
                $ids = $collection->getAllIds();

                if (!empty($ids)) {
                    $connection = $collection->getConnection();
                    $deletedCount = $connection->delete(
                        $collection->getMainTable(),
                        ['entity_id IN (?)' => $ids]
                    );
                }
            }

            if ($deletedCount) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been deleted.', $deletedCount)
                );
            } else {
                $this->messageManager->addWarningMessage(
                    __('No records were deleted.')
                );
            }

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->error('Cache log mass delete error: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while deleting cache log records.')
            );
            $this->logger->critical('Cache log mass delete critical error: ', ['exception' => $e]);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Develodesign_CacheLog::cachelog');
    }
}